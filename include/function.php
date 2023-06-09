<?php

function debug($variable){
    echo '<pre>' . print_r($variable, true) . '</pre>';
}

function logged_only(){

    if(session_status() == PHP_SESSION_NONE){
        session_start();
    }
    if(!isset($_SESSION['auth']) || !isset($_SESSION['admin'])){
        $_SESSION['flash']['danger'] = "Vous n'avez pas le droit d'accéder à cette page";
        header('Location: ../connexion.php');
        exit();
    }
}

