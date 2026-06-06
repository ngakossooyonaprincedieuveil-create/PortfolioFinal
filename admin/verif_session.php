<?php
// 1. On inclut fonctions.php qui est à la racine (on remonte d'un niveau depuis le dossier admin)
require_once __DIR__ . '/../fonctions.php';

// 2. Si la variable de session n'existe pas ou n'est pas vraie, on redirige
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    
    // On calcule l'adresse de base du site pour que la redirection marche depuis N'IMPORTE QUEL sous-dossier
    // (Que tu sois dans /admin/ ou dans /admin/demandes/, il te renverra proprement au bon endroit)
    header('Location: http://' . $_SERVER['HTTP_HOST'] . '/admin/connexion.php');
    exit();
}