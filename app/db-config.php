<?php
const DB_HOST = 'mariadb';
const DB_USERNAME = 'taberu-com';
const DB_PASSWORD = 'jsm8kD3U8GGWW69G';
const DB_NAME = 'taberu-project';

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