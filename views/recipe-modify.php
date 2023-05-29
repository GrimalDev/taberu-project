<?php
require_once realpath(dirname(__FILE__) . '/../app/db-config.php');
require realpath(dirname(__FILE__) . '/../app/redirection.php');
require realpath(dirname(__FILE__) . '/../app/controllers/recipe.php');

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

    if (strlen(FORM_TITLE) > 30) { return "Le titre ne doit pas excéder 30 charactères"; }
    if (strlen(FORM_DESCRIPTION) > 100) { return "La description ne doit pas excéder 50 charactères"; }

    return updateRecipe();
}

function updateRecipe(): string
{
    $newRecipe = new recipe();
    $newRecipe->setTitle(FORM_TITLE);
    $newRecipe->setDescription(FORM_DESCRIPTION);
    $newRecipe->setRecipeBody(FORM_RECIPEBODY);
    $newRecipe->setCountry(FORM_COUNTRY);

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

    <link rel="stylesheet" href="../style/style-single-recipe-modify.css" type="text/css">

    

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
                    echo '<img src="../style/media/flags/india-flag.svg" alt="india flag" <span>Inde</span>';
                } else if ($country === 'china') {
                    echo '<img src="../style/media/flags/china-flag.svg" alt="china flag" <span>Chine</span>';
                } else if ($country === 'thailand') {
                    echo '<img src="../style/media/flags/thailand-flag.svg" alt="thailand flag" <span>Thailand</span>';
                } else if ($country === 'japan') {
                    echo '<img src="../style/media/flags/japan-flag.svg" alt="japan flag" <span>Japon</span>';
                }
                ?>
            </div>
        </div>
        <p class="section-title-content"><?php echo $mainRecipe->getTitle() ?></p>
    </div>
    
    <div class="section-container">
        <div class="section-title">Description:</div>
        <p class="section-title-content"><?php echo $mainRecipe->getDescription() ?></p>
    </div>

    <div class="section-container">
        <div class="section-title">Recette:</div>
        <div id="recipes-single-card-container">
            <p class="recipe-single-card-text"><?php
                //convert html codes to special characters
                $rawText = html_entity_decode($mainRecipe->getRecipeBody());
                //text uses \n for new lines, replace with <br>
                $text = str_replace("\n", "<br>", $rawText);
                echo $text;
                ?></p>
        </div>
    </div>

    <div class="section-container">
        <div class="section-title">MODIFIER LA RECETTE:</div>
        <div id="modify-form-container">
            <form method="post" id="modify-form">
                <div><pre><?php echo $FORM_ERRORS; ?></pre></div>

                <div class="field-container">
                    <label for="contry-select">Veuillez renseigner le pays</label>
                    <select name="country" id="contry-select">
                        <option value="">Origine de la recette</option>
                        <option value="india" <?php echo $mainRecipe->getCountry() === 'india' ? 'selected' : '' ?>>Inde</option>
                        <option value="china" <?php echo $mainRecipe->getCountry() === 'china' ? 'selected' : '' ?>>Chine</option>
                        <option value="thailand" <?php echo $mainRecipe->getCountry() === 'thailand' ? 'selected' : '' ?>>Thaïlande</option>
                        <option value="japan" <?php echo $mainRecipe->getCountry() === 'japan' ? 'selected' : '' ?>>Japon</option>
                    </select>
                </div>

                <div class="field-container">
                    <label for="title-counter-input">Charactères restants: <span id="title-char-count" class="char-counter">30/30</span></label>
                    <input id="title-counter-input" name="title" type="text" placeholder="<?php echo $mainRecipe->getTitle() ?>" value="<?php echo $mainRecipe->getTitle() ?>">
                </div>

                <div class="field-container">
                    <label for="description-counter-input">Charactères restants: <span id="description-char-count" class="char-counter">50/50</span></label>
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