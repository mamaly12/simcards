<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Class ServerService
 *
 * @category EmailApi
 *
 * @package App\Service
 * @author  MohammadAghaAbbasloo <a.mohammad85@gmail.com>
 * @license Copyright (c) 2019, CKSource - All rights reserved.
 * @link    localhost
 */
class EmailApiService
{
    /**
     * CustomerApiService constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param $apiInfo
     * @param $text
     */
    public function sendEmail($apiInfo, $text)
    {
        $mail = new PHPMailer(false);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = $apiInfo['smtp_host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $apiInfo['smtp_username'];
            $mail->Password   = $apiInfo['smtp_password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $apiInfo['smtp_port'];

            //Recipients
            $mail->setFrom($apiInfo['smtp_from'], 'Mailer');

            $recipientEmails = explode(',', $apiInfo['smtp_support_emails']);
            foreach ($recipientEmails as $recipient) {
                $mail->addAddress($recipient);
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Error calling api';
            $mail->Body    = $text;
            $mail->AltBody = $text;
            $mail->Debugoutput = 'error_log';
            @$mail->send();
        } catch (phpmailerException $e) {
            echo $e->errorMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
