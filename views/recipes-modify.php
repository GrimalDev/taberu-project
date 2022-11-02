<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require(realpath(dirname(__FILE__) . '/../app/redirection.php'));

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    redirect('https://taberu.baptistegrimaldi.info/connection');
    exit;
}

$FORM_ERRORS = '';
$DBpdo = connectDB();

function isFormValidated() { // check for validated form, get variables, and execute the validations

    if (!isset($_POST["submit"])) { return; }

    if (!isset($_POST['country']) && !isset($_POST["title"]) && !isset($_POST["description"]) && !isset($_POST["recipes"])) { return "Veuillez renseigner tout les champs"; };

    //define all the required global constances
    define("FORM_TITLE", $_POST["title"]);
    define("FORM_DESCRIPTION", $_POST["description"]);
    define("FORM_RECIPES", $_POST["recipes"]);
    define("FORM_COUNTRY", $_POST["country"]);

    return verifyFormData();
}

function verifyFormData() {

    if (strlen(FORM_TITLE) > 30) { return "Le titre ne doit pas excéder 30 charactères"; }
    if (strlen(FORM_DESCRIPTION) > 50) { return "La description ne doit pas excéder 50 charactères"; }

    return sendDataDB(); // do stuff with the data
}

function sendDataDB() { // process the data coming from the form
    $DBpdo = connectDB();
    $DBtablename = 'recettes';
    $title = FORM_TITLE;
    $description = FORM_DESCRIPTION;
    $recipes = FORM_RECIPES;
    $country = FORM_COUNTRY;

    try {
        $query = $DBpdo->prepare("UPDATE `$DBtablename` SET (`title`, `description`, `recipes`, `country`) = (:password, :description, :recipes, :country) WHERE `username` = :username");
        $query->bindParam(':title', $title); // default PDO::PARAM_STR
        $query->bindParam(':description', $description); // default PDO::PARAM_STR
        $query->bindParam(':recipes', $recipes); // default PDO::PARAM_STR
        $query->bindParam(':country', $country); // default PDO::PARAM_STR

        $query->execute();

        return "Modification recette Enregistrée!";
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            return "Ce titre éxiste déja!";
        }
    }
}

$FORM_ERRORS = isFormValidated();

function getRecipesCategory($category) {
    $DBpdo = connectDB();
    $DBtablename = 'recettes';
    $titleURL = $_GET['recipes'];

    try {
        $query = $DBpdo->prepare("SELECT * FROM `$DBtablename` WHERE `title` = :titleURL");
        $query->bindParam(':titleURL', $titleURL);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC: retourne un tableau indexé par le nom de la colonne comme retourné dans le jeu de résultats
        echo $row[$category];
    } catch (PDOException $e) {
        echo $e;
    }
}

function getRecipesInfos($info) {
    $DBpdo = connectDB();
    $DBtablename = 'recettes';
    $titleURL = $_GET['recipes'];

    try {
        $query = $DBpdo->prepare("SELECT * FROM `$DBtablename` WHERE `title` = :titleURL");
        $query->bindParam(':titleURL', $titleURL);
        $query->execute();
        $row = $query->fetch(PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC: retourne un tableau indexé par le nom de la colonne comme retourné dans le jeu de résultats
        echo $row[$info];
        return;
    } catch (PDOException $e) {
        echo $e;
    }
}

?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>TaBeRu forum</title>

    <link rel="stylesheet" href="../style/style-single-recipes.css" type="text/css">
    <link rel="shortcut icon" type="image/jpg" href="../style/media/TaBeRu-solid-fit.png"/>

    <script defer src="../scripts/script-general.js" type="application/javascript"></script>

</head>
<body>
<!--Get header template-->
<?php include realpath(dirname(__FILE__).'/header.php')?>
<main>
    <h1>Titre:</h1>
    <h2><?php getRecipesCategory('title') ?></h2>

    <h1>Description:</h1>
    <h2><?php getRecipesCategory('description') ?></h2>

    <h1>Recette:</h1>
    <p><?php getRecipesCategory('recipes') ?></p>

    <br /><br />
    <center><h1>MODIFIER SA RECETTE</h1></center>

    <center>
        <form action="/compte" method="post">

            <div><pre><?php echo $FORM_ERRORS; ?></pre></div>

            <label for="contry-select">Veuillez renseigner le pays</label>
            <select name="country" id="contry-select">
                <option value="">Origine de la recette</option>
                <option value="india">Inde</option>
                <option value="china">Chine</option>
                <option value="thailand">Thaïland</option>
                <option value="japan">Japon</option>
            </select>

            <label for="title-counter-input">Charactères restants: <span id="title-char-count" class="char-counter">30/30</span></span></label>
            <input id="title-counter-input" name="title" type="text" placeholder="<?php getRecipesInfos('title'); ?>" value="<?php getRecipesInfos('title'); ?>">

            <label for="description-counter-input">Charactères restants: <span id="description-char-count" class="char-counter">50/50</span></span></label>
            <input id="description-counter-input" name="description" type="text" placeholder="<?php getRecipesInfos('description'); ?>" value="<?php getRecipesInfos('description'); ?>">

            <textarea name="recipes" cols="30" rows="40" placeholder="<?php getRecipesInfos('recipes'); ?>"><?php getRecipesInfos('recipes'); ?></textarea>

            <input name="submit" type="submit" value="Publier">

        </form>
    </center>
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__).'/footer.html')?>
</body>
</html>