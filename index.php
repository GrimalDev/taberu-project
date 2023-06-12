<?php

$urlpath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($urlpath) {
    case '':
    case '/' :
        require __DIR__ . '/views/home.php';
        break;
    case '/enregistrement' :
        require __DIR__ . '/views/register.php';
        break;
    case '/connection' :
        require __DIR__ . '/views/connection.php';
        break;
    case '/compte' :
        require __DIR__ . '/views/user-space.php';
        break;
    case '/modifications' :
        require __DIR__ . '/views/modifications.php';
        break;
    case '/recettes' :
        require __DIR__ . '/views/recipes.php';
        break;
    case '/recette-detail' :
        require __DIR__ . '/views/single-recipe.php';
        break;
    case '/modifier-recette' :
        require __DIR__ . '/views/recipe-modify.php';
        break;
    case '/support' :
        require __DIR__ . '/views/support.php';
        break;
    case '/contact' :
        require __DIR__ . '/views/contact.php';
        break;
//    case '/forum' :
//        require __DIR__ . '/views/forum.php';
//        break;
    case '/legal' :
        require __DIR__ . '/views/legal.php';
        break;
    case '/logout' :
        require __DIR__ . '/views/logout.php';
        break;

    default:
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}