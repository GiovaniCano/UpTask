<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Función que revisa que el usuario este autenticado
function isAuth() : void {
    if(!isset($_SESSION['login'])) {
        exit(header('Location: /'));
    }
}
function isAuthAPI() {    
    if(!isset($_SESSION['login'])) {
        exit;
    }
}

/** Unset keys if they are not expected */
function cleanAssocArray(array $assocArray, array $expectedKeys):array {
    foreach($assocArray as $key => $value) {
        if( !in_array($key, $expectedKeys) ) {
            unset( $assocArray[$key] );
        }
    }
    return $assocArray;
}