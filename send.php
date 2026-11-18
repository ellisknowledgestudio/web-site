<?php
// Sends the quote form and the business questionnaire to your inbox,
// logging in to your Hostinger mailbox (SMTP) so emails are signed and avoid spam.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';
$cfg = require __DIR__ . '/mail-config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /'); exit; }

// Spam trap: real people never fill this hidden field
if (!empty($_POST['website_url'])) { header('Location: /contact-thanks.html'); exit; }

function clean($v, $max = 5000) {
    $v = trim(strip_tags((string)$v));
    return mb_substr($v, 0, $max);
}
function oneline($v) { return str_replace(["\r", "\n"], ' ', $v); }

$type  = oneline(clean($_POST['form_type'] ?? 'Website enquiry', 60));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$name  = oneline(clean($_POST['Name'] ?? '', 100));
$isQ   = ($type === 'Business questionnaire');
$isC   = ($type === 'Main site contact');

$skip = ['form_type', 'website_url'];
$lines = [];
foreach ($_POST as $key => $value) {
    if (in_array($key, $skip, true)) continue;
    $label = oneline(clean(str_replace('_', ' ', $key), 120));
    if (is_array($value)) {
        $value = implode(', ', array_map(fn($x) => clean($x, 200), array_slice($value, 0, 30)));
    } else {
        $value = clean($value);
    }
    if ($value === '') continue;
    $lines[] = $label . ":\n" . $value . "\n";
    if (count($lines) > 80) break;
}

if (!$lines || !$email) {
    header('Location: ' . ($isC ? '/contact-thanks.html?error=1' : ($isQ ? '/start.html?error=1' : '/?error=1#contact'))); exit;
}

$subject = oneline($type . ($name ? " from $name" : '')) . ($isC ? ' - ellisknowledgestudio.com' : ' - web.ellisknowledgestudio.com');
$body = $type . "\nSent " . date('j M Y H:i') . "\n\n" . implode("\n", $lines);

function mailer($cfg) {
    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->Host       = $cfg['host'];
    $m->SMTPAuth   = true;
    $m->Username   = $cfg['user'];
    $m->Password   = $cfg['pass'];
    $m->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $m->Port       = $cfg['port'];
    $m->CharSet    = 'UTF-8';
    $m->setFrom($cfg['user'], $cfg['from_name']);
    return $m;
}

$ok = false;
try {
    // 1. The enquiry to you
    $m = mailer($cfg);
    $m->addAddress($cfg['to']);
    $m->addReplyTo($email, $name ?: $email);
    $m->Subject = $subject;
    $m->Body    = $body;
    $m->send();
    $ok = true;

    // 2. A short thank-you to the customer
    $a = mailer($cfg);
    $a->addAddress($email, $name);
    $a->addReplyTo($cfg['user'], 'James');
    $a->Subject = 'Thanks, I have your enquiry';
    $a->Body    = "Hi" . ($name ? " $name" : '') . ",\n\nThanks for getting in touch. I've got your details and will reply within one working day.\n\nJames\nEllis Knowledge Studio Web\nhttps://web.ellisknowledgestudio.com";
    $a->send();
} catch (Exception $e) {
    error_log('Form mail error: ' . $e->getMessage());
    if (!$ok) {
        // Fallback so an enquiry is never lost (may land in spam)
        @mail($cfg['to'], $subject, $body, "From: {$cfg['user']}\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n");
    }
}

header('Location: ' . ($isC ? '/contact-thanks.html' : ($isQ ? '/start.html?sent=1' : '/?sent=1#contact')));
exit;
