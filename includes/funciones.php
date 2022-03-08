<?php

function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function sanitizar($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo(string $actual, string $proximo): bool{
    if($actual !== $proximo){
        return true;
    }

    return false;
}

//Revisa si el usuario esta autenticado
function estaAutenticado() {
    if(!isset($_SESSION['login'])){
        header('Location: /');
    }
}

function isAdmin(){
    if(!isset($_SESSION['admin'])){
        header('Location: /');
    }
}