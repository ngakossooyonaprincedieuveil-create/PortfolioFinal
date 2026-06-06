<?php
// Configuration de la base de données
$host     = 'sql101.infinityfree.com';
$dbname   = 'if0_41904243_if0_41904243_base_portfolio';
$username = 'if0_41904243';
$password = 'Srfwc1syhU';

try {
    // Connexion à la base de données
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    // Si tu veux tester, décommente la ligne ci-dessous :
    // echo "Connexion réussie !";

} catch (PDOException $e) {
    // Affichage de l'erreur réelle en cas de problème
    die("Erreur de connexion : " . $e->getMessage());
}
?>