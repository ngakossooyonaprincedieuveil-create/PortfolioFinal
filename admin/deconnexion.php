<?php
// 1. Démarrer la session pour pouvoir la détruire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Correction du chemin (utilisation d'un seul ../ pour remonter de admin à la racine)
// Vérifie si ton fichier fonctions.php est bien à la racine
require_once '../fonctions.php';

// 3. Vider le tableau de session
$_SESSION = array();

// 4. Supprimer le cookie de session côté navigateur
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 5. Détruire la session côté serveur
session_destroy();

// 6. Redirection propre vers la page de connexion
header('Location: connexion.php');
exit();
?>