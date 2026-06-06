<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';

// Récupération de l'ID à supprimer
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        // 1. Récupérer le nom de l'image pour la supprimer physiquement du serveur
        $stmt = $pdo->prepare("SELECT image FROM projets WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $projet = $stmt->fetch();

        if ($projet) {
            // Si le projet a une image associée et qu'elle existe sur le FTP, on la détruit
            if (!empty($projet['image']) && file_exists('../../Images/Projets/' . $projet['image'])) {
                unlink('../../Images/Projets/' . $projet['image']);
            }

            // 2. Supprimer la ligne dans la table projets
            $delete = $pdo->prepare("DELETE FROM projets WHERE id = :id");
            $delete->execute(['id' => $id]);
        }
    } catch (PDOException $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
}

// Redirection immédiate vers la liste des projets
header('Location: index.php');
exit();