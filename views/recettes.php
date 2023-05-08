<?php

require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));

session_start();

function cardTemplate($title, $description, $stars) {
    echo '<a class="generated-card" href=https://taberu.baptistegrimaldi.info/recette-detail?recipes='.urlencode($title).'>
            <h2>'.$title.'</h2>
            <h3>'.$description.'</h3>
            <h4>'.$stars.'</h4>
          </a>';
} // TODO Stars system

function generateCards($rows) {
    foreach ($rows as $row) {
        $description = $row['description'];
        //limit description to 30 characters
        if (strlen($description) > 30) {
            $description = substr($description, 0, 30) . '...';
        }
        cardTemplate($row['title'], $description, $row['stars']);
    }
}

function displayAllRecipes($country) {
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
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>TaBeRu</title>

    <link rel="stylesheet" href="../style/style-recettes.css" type="text/css">
    <link rel="shortcut icon" type="image/jpg" href="../style/media/TaBeRu-solid-fit.png"/>

    <script defer src="../scripts/script-general.js" type="application/javascript"></script>

</head>
<body>
    <!--Get header template-->
    <?php include realpath(dirname(__FILE__) . '/header.php')?>
    <main>
        <div id="relative-circle-animation-container" class="">
            <svg class="pulse" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                <circle id="Oval" cx="512" cy="512" r="512"></circle>
                <circle id="Oval" cx="512" cy="512" r="512"></circle>
                <circle id="Oval" cx="512" cy="512" r="512"></circle>
            </svg>
        </div>
        <div class="all-generated-cards-container">
            <h1 id="japon">Recettes du Japon</h1>
            <div class="all-generated-cards" class="make-disappear">
                <?php displayAllRecipes("japan")?>
            </div>

            <h1 id="chine">Recettes de Chine</h1>
            <div class="all-generated-cards" class="make-disappear">
                <?php displayAllRecipes("china")?>
            </div>

            <h1 id="thailand">Recettes de Thaïland</h1>
            <div class="all-generated-cards" class="make-disappear">
                <?php displayAllRecipes("thailand")?>
            </div>

            <h1 id="inde">Recettes d'Inde</h1>
            <div class="all-generated-cards" class="make-disappear">
                <?php displayAllRecipes("india")?>
            </div>
        </div>
    </main>
    <!--TODO add a big card that displays with the recipes-->
    <!--Get footer template-->
    <?php include realpath(dirname(__FILE__).'/footer.html')?>
</body>
</html>