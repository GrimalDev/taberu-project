<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require_once(realpath(dirname(__FILE__) . '/../app/redirection.php'));
require_once(realpath(dirname(__FILE__) . '/../app/controllers/recipe.php'));
require_once(realpath(dirname(__FILE__) . '/../app/controllers/user.php'));

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

    if (strlen(FORM_TITLE) > 50) { return "Le titre ne doit pas excéder 50 charactères"; }
    if (strlen(FORM_DESCRIPTION) > 200) { return "La description ne doit pas excéder 200 charactères"; }

    return sendDataDB(); // do stuff with the data
}

function sendDataDB() { // process the data coming from the form
    global $connectedUser;

    $newRecipe = new recipe();

    try {
        $newRecipe->setTitle(FORM_TITLE);
        $newRecipe->setDescription(FORM_DESCRIPTION);
        $newRecipe->setRecipeBody(FORM_RECIPEBODY);
        $newRecipe->setCountry(FORM_COUNTRY);
        $newRecipe->setCreatorName($connectedUser->getUsername());
    } catch (Exception $e) {
        return $e->getMessage();
    }

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

$allCountries = recipe::getAllCountries();

?>

<html lang="fr">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>TaBeRu compte</title>

    <link rel="stylesheet" href="/style/style-user-space.css" type="text/css">

    
    <script defer src="/scripts/script-char-counter.min.js" type="application/javascript"></script>

    <script defer src="/scripts/user-space-menus.min.js" type="application/javascript"></script>

</head>
<body>
<!--Get header template-->
<?php include realpath(dirname(__FILE__) . '/partials/header.php')?>
<main>
    <h1>Mon compte <?php echo $connectedUser->getUsername() ?></h1>
    <div class="section-container dynamic">
        <div class="section-title">Ajouter une recette</div>
        <form action="/compte" method="post">

            <div><pre><?php echo $FORM_ERRORS; ?></pre></div>

            <select name="country" id="contry-select">
                <option value="">Origine de la recette</option>
                <?php
                    foreach ($allCountries as $country) {
                        echo '<option value="'.$country['country'].'">'.$country['fr'].'</option>';
                    }
                ?>
            </select>

            <label for="title-counter-input">Charactères restants: <span id="title-char-count" class="char-counter">50/50</span></span></label>
            <input id="title-counter-input" name="title" type="text" placeholder="Titre de ma recette">

            <label for="description-counter-input">Charactères restants: <span id="description-char-count" class="char-counter">200/200</span></span></label>
            <input id="description-counter-input" name="description" type="text" placeholder="Description de ma recette">

            <textarea name="recipeBody" cols="30" rows="40" placeholder="Ma recette points par points"></textarea>

            <input name="submit" type="submit" value="Publier">

        </form>
    </div>
    <?php // display section only if there are recipes
    if (recipe::getRecipesByUser($connectedUser->getUsername())) { ?>
        <div class="section-container dynamic">
            <div class="section-title">Mes recettes ajoutées</div>
            <div id="all-generated-cards">
                <?php displayAllRecipes() ?>
            </div>
        </div>
    <?php } ?>
    <?php if (isset($_SESSION['sess_user_id']) && $connectedUser->getRole() === "admin") {
        $allUsers = user::getAllUsers();
        //remove password and id from array
        foreach ($allUsers as $key => $user) {
            unset($allUsers[$key]['password']);
            unset($allUsers[$key]['id']);
        }
        ?>
        <div class="section-container dynamic">
            <div class="section-title">Gestion des utilisateurs</div>
            <div class="section-content">
                <table class="table-view">
                    <tr>
                        <?php foreach ($allUsers[0] as $key => $_) {
                            echo '<th>'.$key.'</th>';
                        } ?>
                    </tr>
                    <?php foreach ($allUsers as $user) {
                        echo '<tr>';
                        foreach ($user as $value) {
                            echo '<td>'.$value.'</td>';
                        }
                        echo '</tr>';
                    } ?>
                </table>
            </div>
        </div>
    <?php } ?>
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>
