<?php
/**
 * Envoi des messages du formulaire de contact via Brevo SMTP (https://app-smtp.brevo.com)
 * Prérequis : composer require phpmailer/phpmailer (dans le dossier portfolio)
 * Clé SMTP : définir la variable d'environnement BREVO_SMTP_KEY sur le serveur.
 * En local (WAMP) : ajouter dans Apache (httpd.conf ou .env) SetEnv BREVO_SMTP_KEY "ta_cle_smtp"
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$sent = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['nom']) ? trim(htmlspecialchars($_POST['nom'])) : '';
    $email = isset($_POST['email']) ? trim(htmlspecialchars($_POST['email'])) : '';
    $message = isset($_POST['message']) ? trim(htmlspecialchars($_POST['message'])) : '';

    if ($nom === '' || $email === '' || $message === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $autoload = __DIR__ . '/vendor/autoload.php';
        if (!file_exists($autoload)) {
            $error = 'PHPMailer non installé. Exécutez dans le dossier portfolio : composer require phpmailer/phpmailer';
        } else {
            require $autoload;

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp-relay.brevo.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'anas.fekhar92@gmail.com';
                $mail->Password   = getenv('BREVO_SMTP_KEY') ?: '';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom('anas.fekhar92@gmail.com', 'Portfolio Fekhar Anas');
                $mail->addAddress('anas.fekhar92@gmail.com', 'Fekhar Anas');
                $mail->addReplyTo($email, $nom);

                $mail->isHTML(true);
                $mail->Subject = 'Message depuis le portfolio - ' . $nom;
                $mail->Body    = "
                    <h2>Nouveau message depuis votre portfolio</h2>
                    <p><strong>Nom :</strong> " . $nom . "</p>
                    <p><strong>Email :</strong> " . $email . "</p>
                    <p><strong>Message :</strong></p>
                    <p>" . nl2br($message) . "</p>
                ";

                $mail->send();
                $sent = true;
            } catch (Exception $e) {
                $error = 'Erreur d\'envoi : ' . $mail->ErrorInfo;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | Portfolio Fekhar Anas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <?php if ($sent): ?>
                            <div class="text-center">
                                <div class="mb-3 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                    </svg>
                                </div>
                                <h2 class="h4 mb-2">Message envoyé</h2>
                                <p class="text-muted mb-4">Merci, votre message a bien été reçu. Je vous répondrai dès que possible.</p>
                                <a href="index.html#contact" class="btn btn-primary">Retour au portfolio</a>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="text-center">
                                <div class="mb-3 text-danger">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
                                    </svg>
                                </div>
                                <h2 class="h4 mb-2">Erreur</h2>
                                <p class="text-muted mb-4"><?php echo htmlspecialchars($error); ?></p>
                                <a href="index.html#contact" class="btn btn-primary">Retour au formulaire</a>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <p class="text-muted mb-4">Utilisez le formulaire sur la page d'accueil pour m'envoyer un message.</p>
                                <a href="index.html#contact" class="btn btn-primary">Aller au formulaire de contact</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
