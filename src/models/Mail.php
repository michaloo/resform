<?php

namespace Resform\Model;


class Mail {

    function __construct($db, $Mailer) {

        $this->db     = $db;
        $this->Mailer = $Mailer;

        $this->Mailer->isSMTP();
        $this->Mailer->Host = getenv("SMTP_HOST");
        $this->Mailer->Port = getenv("SMTP_PORT");
        $this->Mailer->CharSet = 'UTF-8';

        if (getenv("SMTP_USER")) {
            $this->Mailer->SMTPAuth = true;
            $this->Mailer->Username = getenv("SMTP_USER");
            $this->Mailer->Password = getenv("SMTP_PASS");
            $this->Mailer->SMTPSecure = 'tls';
        }

        return $this;
    }

    public function send($email, $name, $message) {
        $mail = $this->Mailer;

        $mail->From = 'zapisy@fundacjamalak.pl';
        $mail->FromName = 'Zapisy';
        $mail->addAddress($email, $name);
        $mail->addBCC('zapisy@fundacjamalak.pl');

        $mail->WordWrap = 50;
        //$mail->isHTML(true);

        $mail->Subject = 'Zapisy';
        $mail->Body    = $message;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            // echo 'Message could not be sent.';
            // echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            //echo 'Message has been sent';
        }
    }
}
