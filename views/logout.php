<?php
session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    redirect(' /connection');
    exit;
}

session_destroy();
header('Location: /');exit;
?>