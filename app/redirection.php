<?php
function redirect($url) {
    ob_start();
    //ob_start() démarre la temporisation de sortie. Tant qu'elle est enclenchée, aucune donnée, hormis les en-têtes, n'est envoyée au navigateur, mais temporairement mise en tampon.
    header('Location: '.$url);
    ob_end_flush();
    die();
}