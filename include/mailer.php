<?php
/**
 * Minimal dependency-free SMTP client.
 *
 * This repo has no composer/vendor directory (see CLAUDE.md — no build
 * step), so rather than pull in PHPMailer this talks SMTP directly over a
 * socket: EHLO, optional STARTTLS, AUTH LOGIN, MAIL FROM/RCPT TO/DATA.
 * Good enough for the one thing it needs to do — send the contact form to
 * a single recipient — without adding a dependency manager to the repo.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit;
}

class Ec_SmtpMailer
{
    private string $host;
    private int $port;
    private string $secure; // 'tls', 'ssl', or ''
    private string $username;
    private string $password;
    private int $timeout;

    /** @var resource|null */
    private $socket;

    private array $transcript = [];

    public function __construct(string $host, int $port, string $secure, string $username, string $password, int $timeout = 12)
    {
        $this->host = $host;
        $this->port = $port;
        $this->secure = $secure;
        $this->username = $username;
        $this->password = $password;
        $this->timeout = $timeout;
    }

    /**
     * @return array{0: bool, 1: string} [success, human-readable result/error]
     */
    public function send(
        string $fromEmail,
        string $fromName,
        string $toEmail,
        string $toName,
        string $subject,
        string $body,
        string $replyToEmail = '',
        string $replyToName = ''
    ): array {
        try {
            $this->connect();
            $this->expect('', '220');

            $ehloHost = preg_replace('/[^a-zA-Z0-9.-]/', '', $_SERVER['SERVER_NAME'] ?? 'localhost') ?: 'localhost';
            $this->expect("EHLO $ehloHost", '250');

            if ($this->secure === 'tls') {
                $this->expect('STARTTLS', '220');
                if (!stream_socket_enable_crypto($this->socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new RuntimeException('STARTTLS negotiation failed');
                }
                $this->expect("EHLO $ehloHost", '250');
            }

            if ($this->username !== '') {
                $this->expect('AUTH LOGIN', '334');
                $this->expect(base64_encode($this->username), '334', true);
                $this->expect(base64_encode($this->password), '235', true);
            }

            $this->expect('MAIL FROM:<' . $this->cleanAddress($fromEmail) . '>', '250');
            $this->expect('RCPT TO:<' . $this->cleanAddress($toEmail) . '>', '250');
            $this->expect('DATA', '354');

            $headers = [];
            $headers[] = 'From: ' . $this->encodeHeader($fromName) . ' <' . $this->cleanAddress($fromEmail) . '>';
            $headers[] = 'To: ' . $this->encodeHeader($toName) . ' <' . $this->cleanAddress($toEmail) . '>';
            if ($replyToEmail !== '') {
                $headers[] = 'Reply-To: ' . $this->encodeHeader($replyToName) . ' <' . $this->cleanAddress($replyToEmail) . '>';
            }
            $headers[] = 'Subject: ' . $this->encodeHeader($subject);
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/plain; charset=UTF-8';
            $headers[] = 'Content-Transfer-Encoding: 8bit';
            $headers[] = 'Date: ' . date('r');
            $headers[] = 'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $ehloHost . '>';

            // RFC 5321 dot-stuffing: a line starting with '.' must be escaped
            // to '..' or the SMTP server reads it as the end-of-DATA marker.
            $lines = preg_split('/\r\n|\r|\n/', $body);
            foreach ($lines as &$line) {
                if (isset($line[0]) && $line[0] === '.') {
                    $line = '.' . $line;
                }
            }
            unset($line);

            $data = implode("\r\n", $headers) . "\r\n\r\n" . implode("\r\n", $lines) . "\r\n.";
            $this->write($data);
            $resp = $this->read();
            if (strpos($resp, '250') !== 0) {
                throw new RuntimeException('Message not accepted: ' . trim($resp));
            }

            $this->write('QUIT');
            $this->close();

            return [true, 'sent'];
        } catch (Throwable $e) {
            $this->close();
            return [false, $e->getMessage() . ' — transcript: ' . implode(' | ', $this->transcript)];
        }
    }

    private function connect(): void
    {
        $transport = $this->secure === 'ssl' ? 'ssl://' : '';
        $errno = 0;
        $errstr = '';
        $this->socket = @stream_socket_client(
            $transport . $this->host . ':' . $this->port,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT
        );
        if (!$this->socket) {
            throw new RuntimeException("Could not connect to $this->host:$this->port — $errstr ($errno)");
        }
        stream_set_timeout($this->socket, $this->timeout);
    }

    private function close(): void
    {
        if (is_resource($this->socket)) {
            @fclose($this->socket);
        }
        $this->socket = null;
    }

    private function read(): string
    {
        $data = '';
        while (($line = fgets($this->socket, 515)) !== false) {
            $data .= $line;
            // Multiline SMTP responses use "250-" on every line but the
            // last, which uses "250 " (a space in the 4th column).
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        if ($data === '') {
            throw new RuntimeException('No response from SMTP server (timed out or connection closed)');
        }
        $this->transcript[] = 'S: ' . trim($data);
        return $data;
    }

    private function write(string $cmd, bool $sensitive = false): void
    {
        fwrite($this->socket, $cmd . "\r\n");
        $this->transcript[] = 'C: ' . ($sensitive ? '[hidden]' : trim($cmd));
    }

    private function expect(string $cmd, string $code, bool $sensitive = false): string
    {
        if ($cmd !== '') {
            $this->write($cmd, $sensitive);
        }
        $resp = $this->read();
        if (strpos($resp, $code) !== 0) {
            throw new RuntimeException("Expected $code, got: " . trim($resp));
        }
        return $resp;
    }

    private function cleanAddress(string $addr): string
    {
        // Strip CR/LF and angle brackets so a crafted "name" field can't
        // inject extra SMTP envelope commands or message headers.
        return preg_replace('/[\r\n<>]/', '', trim($addr));
    }

    private function encodeHeader(string $value): string
    {
        $value = preg_replace('/[\r\n]/', ' ', $value);
        if (preg_match('/[^\x20-\x7E]/', $value)) {
            return '=?UTF-8?B?' . base64_encode($value) . '?=';
        }
        return $value;
    }
}
