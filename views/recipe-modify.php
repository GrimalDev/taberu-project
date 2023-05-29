<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require_once(realpath(dirname(__FILE__) . '/../app/redirection.php'));
require_once(realpath(dirname(__FILE__) . '/../app/controllers/recipe.php'));

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    redirect(' /connection');
    exit;
}

$FORM_ERRORS = '';

//Get the wanted recipe
$titleURL = $_GET['recipe'];

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

    return "La recette a bien été modifiée";
}

$FORM_ERRORS = isFormValidated();

$mainRecipe = new recipe();
$recipeExists = $mainRecipe->getRecipeByTitle($titleURL);

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

                if ($country === 'india') {
                    echo '<img src="/style/media/flags/india-flag.svg" alt="india flag" <span>Inde</span>';
                } else if ($country === 'china') {
                    echo '<img src="/style/media/flags/china-flag.svg" alt="china flag" <span>Chine</span>';
                } else if ($country === 'thailand') {
                    echo '<img src="/style/media/flags/thailand-flag.svg" alt="thailand flag" <span>Thailand</span>';
                } else if ($country === 'japan') {
                    echo '<img src="/style/media/flags/japan-flag.svg" alt="japan flag" <span>Japon</span>';
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
        <div id="modify-form-container">
            <form method="post" id="modify-form" action="#modifier-container">
                <div><pre><?php echo $FORM_ERRORS; ?></pre></div>

                <div class="field-container">
                    <label for="contry-select">Veuillez renseigner le pays</label>
                    <select name="country" id="contry-select">
                        <option value="">Origine de la recette</option>
                        <option value="india" <?php echo $mainRecipe->getCountry() === 'india' ? 'selected' : '' ?>>Inde</option>
                        <option value="china" <?php echo $mainRecipe->getCountry() === 'china' ? 'selected' : '' ?>>Chine</option>
                        <option value="thailand" <?php echo $mainRecipe->getCountry() === 'thailand' ? 'selected' : '' ?>>Thaïlande</option>
                        <option value="japan" <?php echo $mainRecipe->getCountry() === 'japan' ? 'selected' : '' ?>>Japon</option>
                        <?php
                        var_dump(recipe::getAllCountries());
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
    </div>
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>