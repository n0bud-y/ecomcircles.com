<?php
/**
 * Handles contact.php's form POST. Validates input, sends it by SMTP using
 * the settings in include/mail-config.php, then redirects back to
 * contact.php?status=... (POST/redirect/GET, so refreshing the result page
 * never resubmits the form).
 *
 * include/mail-config.php ships with blank credentials — until it's filled
 * in, submissions are rejected with status=notready instead of being
 * silently dropped or falsely reported as sent.
 */

declare(strict_types=1);

function ec_redirect(string $status): never
{
    header('Location: contact.php?status=' . urlencode($status));
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    ec_redirect('');
}

// Honeypot: a real visitor never sees or fills this field in (see contact.php).
// Report success without sending, so bots don't learn the field is checked.
if (trim((string) ($_POST['website'] ?? '')) !== '') {
    ec_redirect('sent');
}

$name = trim((string) ($_POST['name'] ?? ''));
$email = trim((string) ($_POST['email'] ?? ''));
$service = trim((string) ($_POST['service'] ?? ''));
$message = trim((string) ($_POST['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    ec_redirect('invalid');
}
if (mb_strlen($name) > 200 || mb_strlen($email) > 200 || mb_strlen($message) > 8000) {
    ec_redirect('invalid');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ec_redirect('invalid');
}

$serviceLabels = [
    'amazon-management'          => 'Amazon Account Management',
    'walmart-management'         => 'Walmart Account Management',
    'account-buying'             => 'Account Buying',
    'suspension-reinstatement'   => 'Suspension Reinstatement',
    'warehouse-fulfillment'      => 'Warehouse & Fulfillment',
];
$serviceLabel = $serviceLabels[$service] ?? ($service !== '' ? $service : 'Not specified');

$config = require __DIR__ . '/include/mail-config.php';

if ($config['smtp_host'] === '' || $config['smtp_username'] === '' || $config['smtp_password'] === '' || $config['to_email'] === '') {
    error_log('[contact-submit] SMTP not configured yet (include/mail-config.php) — dropped submission from ' . $email);
    ec_redirect('notready');
}

require __DIR__ . '/include/mailer.php';

$submittedAt = date('Y-m-d H:i:s T');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

$body = "New contact form submission from ecomcircles.com\n\n"
    . "Name:    $name\n"
    . "Email:   $email\n"
    . "Service: $serviceLabel\n"
    . "\nMessage:\n$message\n"
    . "\n---\n"
    . "Submitted:  $submittedAt\n"
    . "IP:         $ip\n"
    . "User-Agent: $userAgent\n";

$mailer = new Ec_SmtpMailer(
    $config['smtp_host'],
    (int) $config['smtp_port'],
    $config['smtp_secure'],
    $config['smtp_username'],
    $config['smtp_password']
);

[$ok, $info] = $mailer->send(
    $config['from_email'] !== '' ? $config['from_email'] : $config['smtp_username'],
    $config['from_name'],
    $config['to_email'],
    $config['to_name'],
    'New contact form submission — ' . $name,
    $body,
    $email,
    $name
);

if (!$ok) {
    error_log('[contact-submit] send failed: ' . $info);
    ec_redirect('error');
}

ec_redirect('sent');
