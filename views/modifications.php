<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require_once(realpath(dirname(__FILE__) . '/../app/redirection.php'));
require_once(realpath(dirname(__FILE__) . '/../app/controllers/user.php'));

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    redirect(' /connection');
    exit;
}

$FORM_ERRORS = '';
$DBpdo = connectDB();

$user = new user();
$user->getUserById($_SESSION['sess_user_id']);

function isFormValidated() { // check for validated form, get variables, and execute the validations

    if (!isset($_POST["submit"])) { return; };

    if (!isset($_POST["password-initial"]) && !isset($_POST["password"]) && !isset($_POST["password-confirm"])) { return; };

    define("FORM_PASSWORD_INITIAL", $_POST["password-initial"]);
    define("FORM_PASSWORD", $_POST["password"]);
    define("FORM_PASSWORD_CONFIRM", $_POST["password-confirm"]);

    return verifyFormData(); // pass data to get validate and get return in global variable FORM_ERROR
}

function verifyFormData() {
    global $user;

    $password_initial = FORM_PASSWORD_INITIAL;

    if(!$user->verifyPassword($password_initial)) { return "Mauvais mot de passe"; }

    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,32}$/", FORM_PASSWORD)) {
        return "Mot de passe incorrect,<br />&nbsp- Minimum un caractère spécial (@!%*?&),<br />&nbsp- Minimum une Majuscule ainsi que une minuscule,<br />&nbsp- Entre 8 et 32 caractères.";
    }; //Password has been verified

    //Minimum eight and maximum 10 characters, at least one uppercase letter, one lowercase letter, one number and one special character
    if (FORM_PASSWORD != FORM_PASSWORD_CONFIRM) { return "Les mots de passe ne correspondent pas."; }; // verify password match

    return sendDataDB(); // do stuff with the data
}

/**
 * @throws Exception
 */
function sendDataDB() { // process the data coming from the form
    $updateUser = new user();
    $updateUser->setId($_SESSION['sess_user_id']);
    $updateUser->setPassHashFromPassword(FORM_PASSWORD);
    try {
        $updateUser->updateUser();
    } catch (PDOException $e) {
        return "Une erreur est survenue lors de la modification du mot de passe";
    }

    return "Mot de passe modifié avec succès";
}

$FORM_ERRORS = isFormValidated();
?>

<html lang="fr">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>TaBeRu Modifications</title>

    <link rel="stylesheet" href="/style/style-modifications.css" type="text/css">

    

</head>
<body>
<!--Get header template-->
<?php include realpath(dirname(__FILE__) . '/partials/header.php')?>
<main>
    <h1>Modifier son mot de passe</h1>
    <form action="/modifications" method="post">  <!--TODO implement form animation-->
        <div><pre><?php echo $FORM_ERRORS; ?></pre></div>
        <input type="password" name="password-initial" placeholder="Entrez votre mot de passe actuel" required>
        <input type="password" name="password" placeholder="Entrez votre nouveau mot de passe" required>
        <input type="password" name="password-confirm" placeholder="Confirmer votre nouveau mot de passe" required> <!--TODO add view password button-->
        <input type="submit" name="submit" value="S'inscrire">
    </form>
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>
