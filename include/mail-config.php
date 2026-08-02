<?php
/**
 * SMTP credentials for the contact form (contact-submit.php).
 *
 * Everything below is blank on purpose. Until smtp_host / smtp_username /
 * smtp_password / to_email are all filled in, contact-submit.php will NOT
 * attempt to send — it logs a note via error_log() and shows the visitor a
 * generic "try again" message instead of pretending it worked.
 *
 * Gmail users: generate an App Password at
 * https://myaccount.google.com/apppasswords (requires 2-Step Verification
 * to be turned on first). Do not use your normal account password here —
 * it will not work over SMTP.
 *
 * Do not commit real credentials from this file to a public repository.
 */

if (basename($_SERVER['SCRIPT_FILENAME'] ?? '') === basename(__FILE__)) {
    http_response_code(403);
    exit;
}

return [
    // Where submissions should land — the inbox you check on your phone.
    'to_email' => '',
    'to_name'  => 'Ecom Circles',

    // SMTP transport.
    'smtp_host'     => '',       // e.g. 'smtp.gmail.com'
    'smtp_port'     => 587,      // 587 = STARTTLS, 465 = implicit TLS
    'smtp_secure'   => 'tls',    // 'tls' for port 587, 'ssl' for port 465
    'smtp_username' => '',       // e.g. 'you@gmail.com'
    'smtp_password' => '',       // an App Password — not your login password

    // Sender identity. Most providers require from_email to match
    // smtp_username (or a verified alias of it), so leave this blank to
    // fall back to smtp_username automatically.
    'from_email' => '',
    'from_name'  => 'Ecom Circles Website',
];
