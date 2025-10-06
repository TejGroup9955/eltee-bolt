<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Ensure Composer is configured correctly

function sendMail($toEmails, $ccEmails = [], $subject, $bodyHtml, $attachmentPath = '') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'pallavi1004php@gmail.com';
        $mail->Password   = 'yuejsazoynojpkfc';
        $mail->SMTPSecure = 'ssl';
        $mail->Port       = 465;

        // Sender info
        $mail->setFrom('pallavi1004php@gmail.com', 'ELTEE DMCC');

        // Handle single or multiple To recipients
        if (is_array($toEmails)) {
            foreach ($toEmails as $email) {
                $mail->addAddress($email);
            }
        } else {
            $mail->addAddress($toEmails);
        }

        // Handle single or multiple CC recipients
        if (!empty($ccEmails)) {
            if (is_array($ccEmails)) {
                foreach ($ccEmails as $email) {
                    $mail->addCC($email);
                }
            } else {
                $mail->addCC($ccEmails);
            }
        }

        // Add attachment if provided
        if (!empty($attachmentPath)) {
            $mail->addAttachment($attachmentPath);
        }

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $bodyHtml;

        $mail->send();
        return "Mail Send";

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>