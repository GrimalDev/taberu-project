<?php

require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));

session_start();

function cardTemplate($title, $description, $stars): void
{
    echo '<a class="generated-card" href= /recette-detail?recipe='.urlencode($title).'>
            <span class="card-title">'.$title.'</span>
            <span class="card-description">'.$description.'</span>
            <span class="card-stars">'.$stars.'</span>
          </a>';
} // TODO Stars system

function generateCards($rows): void
{
    foreach ($rows as $row) {
        $description = $row['description'];
        //limit description to 30 characters
        if (strlen($description) > 30) {
            $description = substr($description, 0, 30) . '...';
        }
        cardTemplate($row['title'], $description, $row['stars']);
    }
}

function displayAllRecipes($country): void
{
    $DBpdo = connectDB();
    $DBtablename = 'recettes';

    try {
        $query = $DBpdo->prepare("SELECT * FROM `$DBtablename` WHERE `country` = :country");
        $query->bindParam(':country', $country);
        $query->execute();
        $rows = $query->fetchAll(PDO::FETCH_ASSOC); // PDO::FETCH_ASSOC: retourne un tableau indexé par le nom de la colonne comme retourné dans le jeu de résultats
        generateCards($rows);
    } catch (PDOException $e) {
        echo $e;
    }
}

?>

<html lang="fr">
<head>
    <?php include realpath(dirname(__FILE__) . '/partials/head.php')?>

    <title>TaBeRu</title>

    <link rel="stylesheet" href="/style/style-recettes.css" type="text/css">

    <script defer src="/scripts/script-pulse-animation.min.js"></script>
</head>
<body>
    <!--Get header template-->
    <?php include realpath(dirname(__FILE__) . '/partials/header.php')?>
    <main>
        <div id="relative-circle-animation-container" class="">
            <svg class="pulse" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <circle id="Oval" cx="512" cy="512" r="512"></circle>
                <circle id="Oval" cx="512" cy="512" r="512"></circle>
                <circle id="Oval" cx="512" cy="512" r="512"></circle>
            </svg>
        </div>
        <div class="all-generated-cards-container make-disappear">
            <h1 id="japon">Recettes du Japon</h1>
            <div class="all-generated-cards">
                <?php displayAllRecipes("japan")?>
            </div>

            <h1 id="chine">Recettes de Chine</h1>
            <div class="all-generated-cards">
                <?php displayAllRecipes("china")?>
            </div>

            <h1 id="thailand">Recettes de Thaïland</h1>
            <div class="all-generated-cards">
                <?php displayAllRecipes("thailand")?>
            </div>

            <h1 id="inde">Recettes d'Inde</h1>
            <div class="all-generated-cards">
                <?php displayAllRecipes("india")?>
            </div>
        </div>
    </main>
    <!--TODO add a big card that displays with the recipe-->
    <!--Get footer template-->
    <?php include realpath(dirname(__FILE__) . '/partials/footer.html')?>
</body>
</html>