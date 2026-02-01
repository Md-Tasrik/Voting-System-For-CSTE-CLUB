<?php
include 'includes/session.php';
include 'includes/mailer.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $voterId = $_POST['voterId'];
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $subject = $_POST['emailsubject'];
    $body = $_POST['emailBody'];

    // Get the voter data based on the provided voter ID
    $sql = "SELECT * FROM voters WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voterId);
    $stmt->execute();
    $result = $stmt->get_result();
    $voter = $result->fetch_assoc();

    // Build voting link with query params for convenience (ID only; don't auto-login)
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
    $baseUrl .= rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\');
    $votingLink = $baseUrl . '/index.php?voter_id=' . urlencode($voter['voters_id']);

    // Replace placeholder
    $body = str_replace('[VOTING_LINK]', $votingLink, $body);

    // Optionally inject credentials placeholders
    if (strpos($body, '[VOTER_ID]') !== false) {
        $body = str_replace('[VOTER_ID]', $voter['voters_id'], $body);
    }
    if (strpos($body, '[PASSWORD]') !== false) {
        $body = str_replace('[PASSWORD]', $voter['vpass'], $body);
    }

    // Send email via configured mailer (e.g., Mailtrap API)
    $to = $email ?: (isset($voter['email']) ? $voter['email'] : '');
    if (empty($to)) {
        echo 'No recipient email provided.';
        exit;
    }

    list($ok, $err) = send_email($to, $subject, $body);
    $mailSent = $ok;

    if ($mailSent) {
        echo 'Email sent successfully!';
    } else {
        echo 'Error sending email! ' . (isset($err) ? $err : '');
    }
}
?>
