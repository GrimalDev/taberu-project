<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require(realpath(dirname(__FILE__) . '/../app/redirection.php'));
require(realpath(dirname(__FILE__) . '/../app/controllers/recipe.php'));
require(realpath(dirname(__FILE__) . '/../app/controllers/user.php'));

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    redirect(' /connection');
    exit;
}

$connectedUser = new user();
$userProtector = $connectedUser->getUserById($_SESSION['sess_user_id']);

if ($userProtector === null) {
    redirect(' /connection');
    exit;
}

$FORM_ERRORS = '';
$DBpdo = connectDB();

function isFormValidated() { // check for validated form, get variables, and execute the validations

    if (!isset($_POST["submit"])) { return; }

    if (!isset($_POST['country']) && !isset($_POST["title"]) && !isset($_POST["description"]) && !isset($_POST["recipeBody"])) { return "Veuillez renseigner tout les champs"; };

    //define all the required global constances
    define("FORM_TITLE", $_POST["title"]);
    define("FORM_DESCRIPTION", $_POST["description"]);
    define("FORM_RECIPEBODY", $_POST["recipeBody"]);
    define("FORM_COUNTRY", $_POST["country"]);

    return verifyFormData();
}

function verifyFormData() {

    if (strlen(FORM_TITLE) > 30) { return "Le titre ne doit pas excéder 30 charactères"; }
    if (strlen(FORM_DESCRIPTION) > 50) { return "La description ne doit pas excéder 50 charactères"; }

    return sendDataDB(); // do stuff with the data
}

function sendDataDB() { // process the data coming from the form
    global $connectedUser;

    $newRecipe = new recipe();

    $newRecipe->setTitle(FORM_TITLE);
    $newRecipe->setDescription(FORM_DESCRIPTION);
    $newRecipe->setRecipeBody(FORM_RECIPEBODY);
    $newRecipe->setCountry(FORM_COUNTRY);
    $newRecipe->setCreatorName($connectedUser->getUsername());

    $message = $newRecipe->addRecipe();

    if ($message !== true) {
        return $message;
    }

    return "La recette a bien été ajoutée";
}

$FORM_ERRORS = isFormValidated();

function cardTemplate($title, $description, $stars) {
    echo '<a class="generated-card" href= /modifier-recette?recipe='.urlencode($title).'>
            <span class="card-title">'.$title.'</span>
            <span class="card-description">'.$description.'</span>
            <span class="card-stars">'.$stars.'</span>
          </a>';
} // TODO Stars system

function generateCards($rows) {
    foreach ($rows as $row) {
        cardTemplate($row['title'], $row['description'], $row['stars']);
    }
}

function displayAllRecipes() {
    global $connectedUser;

    generateCards(recipe::getRecipesByUser($connectedUser->getUsername()));
}
?>

<html lang="fr">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>TaBeRu compte</title>

    <link rel="stylesheet" href="../style/style-user-space.css" type="text/css">

    <script defer src="../scripts/script-general.js" type="application/javascript"></script>
    <script defer src="../scripts/script-user-space.js" type="application/javascript"></script>

</head>
<body>
<!--Get header template-->
<?php include realpath(dirname(__FILE__) . '/partials/header.php')?>
<main>
    <h1>Ajouter ma recette</h1>
    <form action="/compte" method="post">

        <div><pre><?php echo $FORM_ERRORS; ?></pre></div>

        <select name="country" id="contry-select">
            <option value="">Origine de la recette</option>
            <option value="india">Inde</option>
            <option value="china">Chine</option>
            <option value="thailand">Thaïland</option>
            <option value="japan">Japon</option>
        </select>

        <label for="title-counter-input">Charactères restants: <span id="title-char-count" class="char-counter">30/30</span></span></label>
        <input id="title-counter-input" name="title" type="text" placeholder="Titre de ma recette">

        <label for="description-counter-input">Charactères restants: <span id="description-char-count" class="char-counter">50/50</span></span></label>
        <input id="description-counter-input" name="description" type="text" placeholder="Description de ma recette">

        <textarea name="recipeBody" cols="30" rows="40" placeholder="Ma recette points par points"></textarea>

        <input name="submit" type="submit" value="Publier">

    </form>
    <h1>Mes recettes ajoutées</h1>
    <div id="all-generated-cards">
        <?php displayAllRecipes()?>
    </div>
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>
