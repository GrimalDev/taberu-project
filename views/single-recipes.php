<?php
require_once(realpath(dirname(__FILE__) . '/../app/db-config.php'));

session_start();

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
    <div id="recipes-single-card-container">
        <p><?php getRecipesCategory('recipes') ?></p>
    </div>
</main>
<!--Get footer template-->
<?php include realpath(dirname(__FILE__).'/footer.html')?>
</body>
</html>