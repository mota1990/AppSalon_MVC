<?php
    namespace Classes;
    use PHPMailer\PHPMailer\PHPMailer;


    class Email{

        public $email;
        public $nombre;
        public $token;


        public function __construct($email, $nombre, $token)
        {
            $this->email = $email;
            $this->nombre = $nombre;
            $this->token = $token;
        }

        public function enviarConfirmacion(){

            //Crear el objeto de email
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = '03d188bf1e9537';
            $mail->Password = '038e4a890341d3';

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress('cuentas@appsalon.net');
            $mail->Subject = 'Confirma Tu Cuenta';

            //Set HTML
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido =  '<html>';
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong Has Creado Tu Cuenta en Appsalon, Solo debes 
            confirmarla presionando en el siguiente enlace</p>";
            $contenido .= "<p>Presione Aqui: <a href='http://localhost:3000/confirmar-cuenta?token=" 
            . $this->token . "'>Confirmar Cuenta</a> </p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
            $contenido .= '</html>';
            $mail->Body = $contenido;
        
            //Enviar el Email
            $mail->send();

        }

        public function enviarIntruccion(){
            //Crear el objeto de email
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Port = 2525;
            $mail->Username = '03d188bf1e9537';
            $mail->Password = '038e4a890341d3';

            $mail->setFrom('cuentas@appsalon.com');
            $mail->addAddress('cuentas@appsalon.net');
            $mail->Subject = 'Reestablece tu password';

            //Set HTML
            $mail->isHTML(TRUE);
            $mail->CharSet = 'UTF-8';

            $contenido =  '<html>';
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer tu password
            , sigue el siguiente enlace para hacerlo.</p>";
            $contenido .= "<p>Presione Aqui: <a href='http://localhost:3000/recuperar?token=" 
            . $this->token . "'>Restablecer Password</a> </p>";
            $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
            $contenido .= '</html>';
            $mail->Body = $contenido;
        
            //Enviar el Email
            $mail->send();

        }
    }

?>