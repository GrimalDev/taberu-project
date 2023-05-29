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

$FORM_ERRORS = '';

//Get the wanted recipe
$titleURL = $_GET['recipe'];

//check if the recipe body is modified by the user that created it
try {
    $currentRecipe = new recipe();
    $currentRecipe->getRecipeByTitle($titleURL);
    $currentRecipeAuthor = $currentRecipe->getCreatorName();
} catch (Exception $e) {
    redirect('/recette-detail?recipe=' . urlencode($titleURL));
}

try {
    $currentUser = new user();
    $currentUser->getUserById($_SESSION['sess_user_id']);
    $currentUserRole = $currentUser->getRole();
    $currentUserUsername = $currentUser->getUsername();
} catch (Exception $e) {
    redirect('/recette-detail?recipe=' . urlencode($titleURL));
}

if ($currentUserUsername !== $currentRecipeAuthor) {
    if ($currentUserRole !== 'admin') {
        redirect('/recette-detail?recipe=' . urlencode($titleURL));
    }
}

// check for validated form, get variables, and execute the validations
function isFormValidated() {

    if (!isset($_POST["submit"])) { return; }

    if (!isset($_POST['country']) && !isset($_POST["title"]) && !isset($_POST["description"]) && !isset($_POST["recipeBody"])) { return "Veuillez renseigner tout les champs"; };

    //define all the required global constances
    define("FORM_TITLE", $_POST["title"]);
    define("FORM_DESCRIPTION", $_POST["description"]);
    define("FORM_RECIPEBODY", $_POST["recipeBody"]);
    define("FORM_COUNTRY", $_POST["country"]);

    return verifyFormData();
}

function verifyFormData(): string
{

    if (strlen(FORM_TITLE) > 50) { return "Le titre ne doit pas excéder 50 charactères"; }
    if (strlen(FORM_DESCRIPTION) > 200) { return "La description ne doit pas excéder 200 charactères"; }

    return updateRecipe();
}

function updateRecipe(): string
{
    global $titleURL;

    $newRecipe = new recipe();
    $newRecipe->getRecipeByTitle($titleURL);

    //replace <br> with \n
    $recipeBody = str_replace("<br>", "\n", FORM_RECIPEBODY);

    try {
        //only update the fields that are different from the original recipe
        $newRecipe->setTitle(FORM_TITLE === $newRecipe->getTitle() ? '' : FORM_TITLE);
        if (FORM_DESCRIPTION !== $newRecipe->getDescription()) {
            $newRecipe->setDescription(FORM_DESCRIPTION);
        }
        if (FORM_RECIPEBODY !== $newRecipe->getRecipeBody()) {
            $newRecipe->setRecipeBody($recipeBody);
        }
        if (FORM_COUNTRY !== $newRecipe->getCountry()) {
            $newRecipe->setCountry(FORM_COUNTRY);
        }
    } catch (Exception $e) {
        return $e->getMessage();
    }

    $message = $newRecipe->updateRecipe();

    if ($message !== true) {
        return $message;
    }

    //create the new url with the encoded title in get
    $newURL = '/modifier-recette?recipe=' . urlencode($newRecipe->getTitle());
    redirect($newURL);
}

$FORM_ERRORS = isFormValidated();

$mainRecipe = new recipe();
$recipeExists = $mainRecipe->getRecipeByTitle($titleURL);

$allCountries = recipe::getAllCountries();

?>
<html lang="fr">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>TaBeRu forum</title>

    <link rel="stylesheet" href="/style/style-single-recipe-modify.css" type="text/css">

    <script defer src="/scripts/script-char-counter.min.js" type="application/javascript"></script>
</head>
<body>
<!--Get header template-->
<?php include realpath(dirname(__FILE__) . '/partials/header.php')?>
<main>
    <div class="section-container">
        <div class="section-title">
            Titre:
            <div class="country-indicator">
                <?php
                $country = $mainRecipe->getCountry();
                //display the flag corresponding to the current recipe country
                foreach ($allCountries as $country) {
                    if ($country['country'] === $mainRecipe->getCountry()) {
                        echo '<img src="/style/media/flags/' . $country['country'] . '-flag.svg" alt="country flag"><span>' . $country['fr'] . '</span>';
                    }
                }
                ?>
            </div>
        </div>
        <p class="section-title-content"><?php echo $mainRecipe->getTitle(true) ?></p>
    </div>
    
    <div class="section-container">
        <div class="section-title">Description:</div>
        <p class="section-title-content"><?php echo $mainRecipe->getDescription(true) ?></p>
    </div>

    <div class="section-container">
        <div class="section-title">Recette:</div>
        <div id="recipes-single-card-container">
            <p class="recipe-single-card-text"><?php echo $mainRecipe->getRecipeBody(true) ?></p>
        </div>
    </div>

    <div class="section-container" id="modifier-container">
        <div class="section-title">MODIFIER LA RECETTE:</div>
        <div class="modify-form-container">
            <form method="post" class="modify-form" action="#modifier-container">
                <div><pre><?php echo $FORM_ERRORS; ?></pre></div>

                <div class="field-container">
                    <label for="contry-select">Veuillez renseigner le pays</label>
                    <select name="country" id="contry-select">
                        <option value="">Origine de la recette</option>
                        <?php
                            foreach ($allCountries as $country) {
                                $active = $mainRecipe->getCountry() === $country['country'] ? 'selected' : '';
                                echo '<option value="' . $country['country'] . '" ' . $active . '>' . $country["fr"] . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="field-container">
                    <label for="title-counter-input">Charactères restants: <span id="title-char-count" class="char-counter">50/50</span></label>
                    <input id="title-counter-input" name="title" type="text" placeholder="<?php echo $mainRecipe->getTitle() ?>" value="<?php echo $mainRecipe->getTitle() ?>">
                </div>

                <div class="field-container">
                    <label for="description-counter-input">Charactères restants: <span id="description-char-count" class="char-counter">200/200</span></label>
                    <input id="description-counter-input" name="description" type="text" placeholder="<?php echo $mainRecipe->getDescription() ?>" value="<?php echo $mainRecipe->getDescription() ?>">
                </div>

                <div class="field-container">
                    <label for="recipe-body">Recette:</label>
                    <textarea id="recipe-body" name="recipeBody" cols="30" rows="40" placeholder="<?php echo $mainRecipe->getRecipeBody() ?>"><?php echo $mainRecipe->getRecipeBody() ?></textarea>
                </div>

                <input name="submit" type="submit" value="Publier">

            </form>
        </div>
        <div class="modify-form-container">
            <form method="post" class="modify-form">
                <input type="submit" value="Supprimer" id="delete-button" name="delete">
            </form>
        </div>
    </div>
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>