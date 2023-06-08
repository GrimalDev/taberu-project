<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require_once(realpath(dirname(__FILE__) . '/../app/redirection.php'));
require_once(realpath(dirname(__FILE__) . '/../app/controllers/user.php'));

session_start(); //initialize session cookie

//verify if user is logged in

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    redirect('/recettes');
    exit;
}

// PHP Part with form process

$FORM_ERRORS = '';
$DBpdo = connectDB();

function isFormValidated() { // check for validated form, get variables, and execute the validations

    if (!isset($_POST["submit"])) { return ""; };

    if (!isset($_POST["auth"]) && !isset($_POST["password"])) { return "Veuillez renseigner tout les champs"; };

    define("FORM_EMAIL", $_POST["auth"]);
    define("FORM_PASSWORD", $_POST["password"]);

    return verifyFormData(); // pass data to get validate and get return in global variable FORM_ERROR
}

function verifyFormData() {
    $tempBool = false;

    if (!filter_var(FORM_EMAIL, FILTER_VALIDATE_EMAIL)) { $tempBool = true; } //Check email validity

    return sendDataDB($tempBool); // do stuff with the data
}

function sendDataDB($connectWithUsername) { // process the data coming from the form
    $auth = FORM_EMAIL;

    $user = new user();

    try {
        $user->getUserByEmail($auth);
    } catch (PDOException $e) {
        return "Aucun compte n'existe à cette addresse mail ou nom d'utilisateur";
    }

    if(!$user->verifyPassword(FORM_PASSWORD)) {
        return "Mauvais mot de passe";
    }

    //defnie properties for the session cookies
    $_SESSION["loggedin"] = true;
    $_SESSION['sess_user_id'] = $user->getId();
    $_SESSION['sess_user_name'] = $user->getUsername();

    //log connection in database
    user::logConnection($user->getId());

    redirect(' /compte');
}

$FORM_ERRORS = isFormValidated();

?>

<html lang="fr">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>TaBeRu Login</title>

    <link type="text/css" rel="stylesheet" href="/style/style-register-connection.css">
    <link type="text/css" rel="stylesheet" href="/style/style-common.css">

    
</head>
<body>
    <!--Get header template-->
    <?php include realpath(dirname(__FILE__) . '/partials/header.php')?>

    <div class="authentication-nav">
        <h1>Se connecter</h1>
        <a href=" /enregistrement"><h1>S'inscrire</h1></a>
    </div>
    <main>
        <!--TODO Custom field required note html-->
        <form action="/connection" method="post">  <!--TODO implement form animation-->
            <div><pre><?php echo $FORM_ERRORS; ?></pre></div>
            <input type="text" name="auth" placeholder="Entrez votre e-mail" required>
            <input type="password" name="password" placeholder="Entrez votre mot de passe" required>
            <input type="submit" name="submit" value="Se connecter">
        </form>
    </main>
    <!--Get footer template-->
    <?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>

</body>
</html>