<?php
require_once __DIR__ . '/../core/database_config.php';
require_once __DIR__ . '/../models/ReadingSession.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



class ReminderController
{
    public function sendReminders($dias_inatividade = 3)
    {
        header("Content-Type: application/json");

        // Busca usuários inativos
        $usuarios = ReadingSession::getInactiveUsers($dias_inatividade);

        if (isset($usuarios['error'])) {
            http_response_code(500);
            echo json_encode(["error" => $usuarios['error']]);
            return;
        }

        if (empty($usuarios)) {
            echo json_encode(["message" => "Nenhum usuário inativo há $dias_inatividade dias."]);
            return;
        }

        $resultados = [];

        foreach ($usuarios as $u) {
            $email = $u['email'];
            $nome = $u['nome'];
            $dias = $u['dias_inativo'];

            // Conteúdo do e-mail
            $assunto = "Hora de continuar sua leitura ";
            $corpo = "
                <p>Olá <strong>{$nome}</strong>,</p>
                <p>Você está há <strong>{$dias}</strong> dias sem ler nada.</p>
                <p>Que tal retomar sua leitura hoje? </p>
                <hr>
                <p><em>TimerBook - Seu progresso, sua história.</em></p>
            ";

            $mail = new PHPMailer(true);
            ob_start(); // captura debug

            try {
                // Configurações SMTP
                $mail->isSMTP();
                $mail->Host = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['MAIL_USERNAME'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Remetente e destinatário
                $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
                $mail->addAddress($email, $nome);

                // Conteúdo
                $mail->isHTML(true);
                $mail->Subject = $assunto;
                $mail->Body = $corpo;

                // Debug
                $mail->SMTPDebug = 0; // 2 se quiser saída detalhada
                $mail->Debugoutput = function ($str, $level) {
                    echo "Debug: $str\n";
                };

                $enviado = $mail->send();
                $debugOutput = ob_get_clean();

                $resultados[] = [
                    'user_id' => $u['user_id'],
                    'email' => $email,
                    'status' => $enviado ? 'enviado' : 'falhou',
                    'debug' => $debugOutput,
                    'error' => $enviado ? null : $mail->ErrorInfo
                ];
            } catch (Exception $e) {
                $debugOutput = ob_get_clean();
                $resultados[] = [
                    'user_id' => $u['user_id'],
                    'email' => $email,
                    'status' => 'erro',
                    'error' => $e->getMessage(),
                    'mail_error' => $mail->ErrorInfo ?? '',
                    'debug' => $debugOutput
                ];
            }
        }

        echo json_encode([
            'status' => 'processado',
            'total' => count($resultados),
            'usuarios' => $resultados
        ]);
    }
}
