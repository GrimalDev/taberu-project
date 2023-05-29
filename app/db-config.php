<?php

require __DIR__ . '/../vendor/autoload.php';

//load environnement variable with .env file at project root
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$dotenv->required(['DB_HOST_PRODUCTION', 'DB_HOST_DEVELOPMENT', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD']);

require_once realpath(dirname(__FILE__) . '/../app/redirection.php');

//Access environnement variable for host. If environnement is in production use the "mariadb" host
define("DB_HOST", getenv('ENVIRONMENT') === 'production' ? $_ENV['DB_HOST_PRODUCTION'] : $_ENV['DB_HOST_DEVELOPMENT']);
define("DB_USERNAME", $_ENV['DB_USERNAME']);
define("DB_PASSWORD", $_ENV['DB_PASSWORD']);
define("DB_NAME", $_ENV['DB_NAME']);

function connectDB() {
    /*--------------------------
    A config page to be able to communicate with the backend database
    ----------------------------*/

    /* Attempt to connect to MySQL database */
    try {
        //PDO = Php Data Objects : Php extension
        $DBpdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
        // Set the PDO error mode to exception
        $DBpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $DBpdo;
    } catch (PDOException $e) {
        //display error
        die("ERROR: Could not connect. " . $e->getMessage());
    }
}