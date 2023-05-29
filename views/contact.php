<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$dotenv->required(['SENDGRID_API_KEY']);

function sendMail($contactInfo): void
{
    $email = new \SendGrid\Mail\Mail();
    try {
        $email->setFrom("no-reply@baptistegrimaldi.info", "No-Reply");
        $email->setSubject("Sending with SendGrid is Fun");
        $email->addTo("contact@baptistegrimaldi.info", "Contact",
            [
                "email" => $contactInfo['email'],
                "message" => $contactInfo['message']
            ]);
        $email->setTemplateId('d-aba77fe1691b4e73bf3e1017d10065a9');

        $sendgrid = new \SendGrid($_ENV['SENDGRID_API_KEY']);

        $response = $sendgrid->send($email);

    } catch (Exception $e) {
        echo <<<HTML
            <style>
                .error-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 50vh;
                }
                .error-container p {
                    font-size: 2rem;
                    font-weight: bold;
                }
                main {
                    display: none !important;
                }
            </style>
            <div class="error-container">
                <p>Une erreur est survenue, veuillez contacter l'administrateur</p>
            </div>
        HTML;
    }
}

function verifyFormData(): string
{
    //if form is not submitted
    if (!isset($_POST['submit'])) {
        return " ";
    }

    //verify all field are set
    if (!isset($_POST['email']) || !isset($_POST['message'])) {
        return "Veuillez remplir tous les champs";
    }

    //verify all fields are not empty
    if (strlen($_POST['email']) <= 1 || strlen($_POST['message']) <= 0) {
        return "Veuillez remplir tous les champs";
    }
    //verify the mail is valid
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        return "Veuillez entrer un mail valide";
    }

    $email = $_POST['email'];
    $message = $_POST['message'];

    //sendmail even if the database is not available
    sendMail([
        "email" => $_POST['email'],
        "message" => $_POST['message']
    ]);

    //save mail in database
    require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
    try {
        $DBpdo = connectDB();

        //handle if no connection to database is possible from the server
        if ($DBpdo !== null) {
            $query = $DBpdo->prepare("INSERT INTO contacts (email, message) VALUES (:email, :message)");
            $query->bindParam(':email', $email);
            $query->bindParam(':message', $message);
            $query->execute();
        }
    } catch (PDOException $e) {
    }

    return "Votre message a bien été envoyé";
}

$returnMessage = verifyFormData();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>Taberu Contact</title>

    <link type="text/css" rel="stylesheet" href="/style/style-contact.css">
</head>
<body>
    <!--Get header template-->
    <?php include realpath(dirname(__FILE__) . '/partials/header.php')?>

    <main>
        <form method="post" id="contact-form">
            <p class="return-message"><?php echo $returnMessage ?></p>
            <span id="enter_mail">
              Entrez votre mail :
              <br>
                <input type="email" placeholder="Email" name="email">
            </span>

            <span id="message_mail">
              Votre message :
              <br>
                <textarea name="message" id="" cols="40" rows="10"></textarea>
            </span>

            <div id="form-buttons-container">
                <span class="submit_button">
                    <input type="submit" class="submit_button form__action-button" value="Envoyer" name="submit">
                </span>

                <span class="clear_button">
                    <input type="reset" class="clear_button form__action-button" value="Vider">
                </span>
            </div>
        </form>
    </main>

    <!--Get footer template-->
    <?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>
