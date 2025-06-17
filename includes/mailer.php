<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php'; 

function enviarCorreoRecuperacion($correoDestino, $token) {
    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP para Outlook
        $mail->isSMTP();
        $mail->Host       = 'smtp.office365.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'asemblantes1588@uta.edu.ec';   
        $mail->Password   = 'XpdKnyeMqD';            
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('asemblantes1588@uta.edu.ec', 'UTA - Recuperación');
        $mail->addAddress($correoDestino); // destinatario

        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de contraseña - Plataforma UTA';

        $link = "http://localhost/Proyecto2P_Event/views/reset.php?token=$token";

        $mail->Body = "
            <h2>Solicitud de recuperación de contraseña</h2>
            <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:</p>
            <p><a href='$link'>Restablecer contraseña</a></p>
            <p>Este enlace expirará en 15 minutos. Si no hiciste esta solicitud, ignora este mensaje.</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return "Error al enviar el correo: {$mail->ErrorInfo}";
    }
}
