<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));
require_once(realpath(dirname(__FILE__) . '/../app/controllers/recipe.php'));

session_start();

$mainRecipe = new recipe();
$recipeExists = $mainRecipe->getRecipeByTitle($_GET['recipe']);

$allCountries = recipe::getAllCountries();

?>
<html lang="fr">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>TaBeRu forum</title>

    <link rel="stylesheet" href="/style/style-single-recipe-modify.css" type="text/css">

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
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>