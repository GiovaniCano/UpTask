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

        $phpmailer->setFrom("cuentas@uptask.com", "UpTask");
        $phpmailer->addAddress($this->email, $this->nombre);
        $phpmailer->Subject = "Confirma tu cuenta";

        $phpmailer->isHTML(TRUE);
        $phpmailer->CharSet = "UTF-8";

        $dominio = "http://localhost";

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola ".$this->nombre."</strong>, has creado tu cuenta en UpTask, solo debes confirmarla en el siguiente enlace.</p>";
        $contenido .= "<p>Presiona aquí: <a href='{$dominio}/confirmar?token=". $this->token ."'>Confirmar Cuenta</a></p>";
        $contenido .= "<p>Si tu no creaste esta cuenta, puedes ignorar este mensaje.</p>";
        $contenido .= "</html>";

        $phpmailer->Body = $contenido;
        $phpmailer->send();
    }

    public function enviarInstrucciones() {
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = '7655cc7c118432';
        $phpmailer->Password = 'af7085d52be1c7';

        $phpmailer->setFrom("cuentas@uptask.com", "UpTask");
        $phpmailer->addAddress($this->email, $this->nombre);
        $phpmailer->Subject = "Restablece tu Contraseña";

        $phpmailer->isHTML(TRUE);
        $phpmailer->CharSet = "UTF-8";

        $dominio = "http://localhost";

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola ".$this->nombre."</strong>, parece que has olvidado tu contraseña, sigue el siguiente enlace para recuperarla.</p>";
        $contenido .= "<p>Presiona aquí: <a href='{$dominio}/restablecer?token=". $this->token ."'>Restablecer Contraseña</a></p>";
        $contenido .= "</html>";

        $phpmailer->Body = $contenido;
        $phpmailer->send();
    }
}