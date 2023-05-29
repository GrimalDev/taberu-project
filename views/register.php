<?php // PHP Part with form process

require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require_once(realpath(dirname(__FILE__) . '/../app/redirection.php'));
require_once(realpath(dirname(__FILE__) . '/../app/controllers/user.php'));

//verify if user is logged in
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    redirect('/recettes');
    exit;
}

$FORM_ERRORS = '';
$DBpdo = connectDB();

function isFormValidated() { // check for validated form, get variables, and execute the validations

    if (!isset($_POST["submit"])) { return ""; };

    if (!isset($_POST["username"]) && !isset($_POST["email"]) && !isset($_POST["password"])) { return "Veuillez renseigner tout les champs"; };

    define("FORM_USERNAME", $_POST["username"]);
    define("FORM_EMAIL", $_POST["email"]);
    define("FORM_PASSWORD", $_POST["password"]);
    define("FORM_PASSWORD_CONFIRM", $_POST["password-confirm"]);

    return verifyFormData(); // pass data to get validate and get return in global variable FORM_ERROR
}

function verifyFormData() {

    if (strlen(FORM_USERNAME) > 20 || strlen(FORM_USERNAME) < 5) { return "Le nom d'utilisateur doit comprendre entre 5 et 15 caractères compris."; } // check username if between 5 and 15 characters

    if (!filter_var(FORM_EMAIL, FILTER_VALIDATE_EMAIL)) { return "Email incorrect."; } //Check email validity

    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,32}$/", FORM_PASSWORD)) {
        return "Mot de passe incorrect,<br />&nbsp- Minimum un caractère spécial (@!%*?&),<br />&nbsp- Minimum une Majuscule ainsi que une minuscule,<br />&nbsp- Entre 8 et 32 caractères.";
    }; //Password has been verified

    //Minimum eight and maximum 10 characters, at least one uppercase letter, one lowercase letter, one number and one special character
    if (FORM_PASSWORD != FORM_PASSWORD_CONFIRM) { return "Les mots de passe ne correspondent pas."; }; // verify password match

    return sendDataDB(); // do stuff with the data
}

function sendDataDB() { // process the data coming from the form

    try {
        $user = new user();
        $user->setUsername(FORM_USERNAME);
        $user->setEmail(FORM_EMAIL);
        $user->setRole('regular');
        $user->setPassHashFromPassword(FORM_PASSWORD);
        $user->createUser();
    } catch (PDOException $e) {
        return $e;
    }
    redirect(' /connection');
}

$FORM_ERRORS = isFormValidated();

?>

<html lang="fr">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>TaBeRu Register</title>

    <link type="text/css" rel="stylesheet" href="/style/style-register-connection.css">

    
</head>
<body>
    <!--Get header template-->
    <?php include realpath(dirname(__FILE__) . '/partials/header.php')?>

    <div class="authentication-nav">
        <h1>S'inscrire</h1>
        <a href=" /connection"><h1>Se connecter</h1></a>
    </div>
    <main>
        <form action="/enregistrement" method="post">  <!--TODO implement form animation-->
            <div><pre><?php echo $FORM_ERRORS; ?></pre></div>
            <input type="username" name="username" placeholder="Entrez votre nom d'utilisateur" <?php echo isset($_POST["username"]) ? 'value="' .$_POST["username"] . '"' : ''  ?> required>
            <input type="email" name="email" placeholder="Entrez votre e-mail" <?php echo isset($_POST["email"]) ? 'value="' . $_POST["email"] . '"' : ''  ?> required>
            <input type="password" name="password" placeholder="Entrez votre mot de passe" required>
            <input type="password" name="password-confirm" placeholder="Confirmer votre mot de passe" required> <!--TODO add view password button-->
            <input type="submit" name="submit" value="S'inscrire">
        </form>
    </main>
    <!--Get footer template-->
    <?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>

