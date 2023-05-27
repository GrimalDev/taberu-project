<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require realpath(dirname(__FILE__) . '/../app/controllers/recipe.php');

session_start();

$mainRecipe = new recipe();
$recipeExists = $mainRecipe->getRecipeByTitle($_GET['recipe']);

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
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>