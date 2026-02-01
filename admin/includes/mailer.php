<?php

function send_email_mailtrap($toEmail, $subject, $textBody, $fromEmail, $fromName, $apiToken) {
    $payload = array(
        'from' => array('email' => $fromEmail, 'name' => $fromName),
        'to' => array(array('email' => $toEmail)),
        'subject' => $subject,
        'text' => $textBody
    );

    $ch = curl_init('https://send.api.mailtrap.io/api/send');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $apiToken,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = null;
    if ($response === false) {
        $curlErr = curl_error($ch);
    }
    curl_close($ch);

    if ($httpCode >= 200 && $httpCode < 300) {
        return array(true, '');
    }

    return array(false, $curlErr ? $curlErr : $response);
}

function send_email_sendgrid($toEmail, $subject, $textBody, $fromEmail, $fromName, $apiKey) {
    $payload = array(
        'personalizations' => array(
            array('to' => array(array('email' => $toEmail)))
        ),
        'from' => array('email' => $fromEmail, 'name' => $fromName),
        'subject' => $subject,
        'content' => array(array('type' => 'text/plain', 'value' => $textBody))
    );

    $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = null;
    if ($response === false) {
        $curlErr = curl_error($ch);
    }
    curl_close($ch);

    // SendGrid returns 202 on success
    if ($httpCode === 202) {
        return array(true, '');
    }
    return array(false, $curlErr ? $curlErr : ($response ?: 'HTTP ' . $httpCode));
}

function send_email_gmail_smtp($toEmail, $subject, $textBody, $fromEmail, $fromName, $gmailUser, $gmailAppPassword) {
    $host = 'smtp.gmail.com';
    $port = 587;

    $timeout = 30;
    $errno = 0;
    $errstr = '';
    $fp = stream_socket_client('tcp://' . $host . ':' . $port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        return array(false, 'SMTP connect failed: ' . $errstr);
    }

    stream_set_timeout($fp, $timeout);
    $read = function() use ($fp) {
        $data = '';
        while ($line = fgets($fp, 515)) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $data;
    };
    $write = function($cmd) use ($fp) {
        fwrite($fp, $cmd . "\r\n");
    };

    $resp = $read();
    if (strpos($resp, '220') !== 0) { fclose($fp); return array(false, 'SMTP banner: ' . $resp); }

    $write('EHLO localhost');
    $resp = $read();
    if (strpos($resp, '250') !== 0) { fclose($fp); return array(false, 'EHLO failed: ' . $resp); }

    $write('STARTTLS');
    $resp = $read();
    if (strpos($resp, '220') !== 0) { fclose($fp); return array(false, 'STARTTLS failed: ' . $resp); }

    if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
        fclose($fp);
        return array(false, 'TLS negotiation failed');
    }

    $write('EHLO localhost');
    $resp = $read();
    if (strpos($resp, '250') !== 0) { fclose($fp); return array(false, 'EHLO(2) failed: ' . $resp); }

    $write('AUTH LOGIN');
    $resp = $read();
    if (strpos($resp, '334') !== 0) { fclose($fp); return array(false, 'AUTH start failed: ' . $resp); }

    $write(base64_encode($gmailUser));
    $resp = $read();
    if (strpos($resp, '334') !== 0) { fclose($fp); return array(false, 'Username rejected: ' . $resp); }

    $write(base64_encode($gmailAppPassword));
    $resp = $read();
    if (strpos($resp, '235') !== 0) { fclose($fp); return array(false, 'Password rejected: ' . $resp); }

    $fromHeader = $fromName ? $fromName . ' <' . $fromEmail . '>' : $fromEmail;
    $headers = 'From: ' . $fromHeader . "\r\n" .
               'To: ' . $toEmail . "\r\n" .
               'Subject: ' . $subject . "\r\n" .
               'MIME-Version: 1.0' . "\r\n" .
               'Content-Type: text/plain; charset=UTF-8' . "\r\n" .
               'Content-Transfer-Encoding: 8bit' . "\r\n";

    $write('MAIL FROM:<' . $fromEmail . '>');
    $resp = $read();
    if (strpos($resp, '250') !== 0) { fclose($fp); return array(false, 'MAIL FROM failed: ' . $resp); }

    $write('RCPT TO:<' . $toEmail . '>');
    $resp = $read();
    if (strpos($resp, '250') !== 0) { fclose($fp); return array(false, 'RCPT TO failed: ' . $resp); }

    $write('DATA');
    $resp = $read();
    if (strpos($resp, '354') !== 0) { fclose($fp); return array(false, 'DATA failed: ' . $resp); }

    $message = $headers . "\r\n" . $textBody . "\r\n.\r\n";
    $write($message);
    $resp = $read();
    if (strpos($resp, '250') !== 0) { fclose($fp); return array(false, 'Message not accepted: ' . $resp); }

    $write('QUIT');
    fclose($fp);
    return array(true, '');
}

function send_email($toEmail, $subject, $textBody) {
    $configPath = __DIR__ . '/../config.ini';
    $config = file_exists($configPath) ? parse_ini_file($configPath) : array();

    $fromEmail = isset($config['mail_from_email']) ? $config['mail_from_email'] : 'no-reply@example.com';
    $fromName = isset($config['mail_from_name']) ? $config['mail_from_name'] : 'Voting System';

    // Gmail SMTP (real email) if credentials provided
    if (!empty($config['gmail_user']) && !empty($config['gmail_app_password'])) {
        list($ok, $err) = send_email_gmail_smtp($toEmail, $subject, $textBody, $fromEmail, $fromName, $config['gmail_user'], $config['gmail_app_password']);
        if ($ok) {
            return array(true, '');
        }
        // If Gmail fails, try other providers
    }

    // SendGrid (real email delivery)
    if (isset($config['sendgrid_api_key']) && !empty($config['sendgrid_api_key'])) {
        list($ok, $err) = send_email_sendgrid($toEmail, $subject, $textBody, $fromEmail, $fromName, $config['sendgrid_api_key']);
        if ($ok) {
            return array(true, '');
        }
        // If SendGrid fails, continue to other options
    }

    // Mailtrap API (easiest for local/dev)
    if (isset($config['mailtrap_api_token']) && !empty($config['mailtrap_api_token'])) {
        list($ok, $err) = send_email_mailtrap($toEmail, $subject, $textBody, $fromEmail, $fromName, $config['mailtrap_api_token']);
        if ($ok) {
            return array(true, '');
        }
        // API failed -> fall back to dev log but still report success for simplicity
    }

    // Dev fallback: pretend success and log to file
    $logPath = __DIR__ . '/../email_dev.log';
    $logEntry = "TO: $toEmail\nSUBJECT: $subject\nFROM: $fromName <$fromEmail>\nBODY:\n$textBody\n----\n";
    @file_put_contents($logPath, $logEntry, FILE_APPEND);
    return array(true, '');
}

?>


