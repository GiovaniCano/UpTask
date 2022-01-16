<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = '7655cc7c118432';
        $phpmailer->Password = 'af7085d52be1c7';

        $phpmailer->setFrom("cuentas@uptask.com");
        $phpmailer->addAddress("cuentas@uptask.com", "uptask.com");
        $phpmailer->Subject = "Confirma tu cuenta";

        $phpmailer->isHTML(TRUE);
        $phpmailer->CharSet = "UTF-8";

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola ".$this->nombre."</strong>, has creado tu cuenta en UpTask, solo debes confirmarla en el siguiente enlace.</p>";
        $contenido .= "<p>Presiona aquí: <a href='http://localhost/confirmar?token=". $this->token ."'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje.</p>";
        $contenido .= "</html>";

        $phpmailer->Body = $contenido;
        $phpmailer->send();
    }
}