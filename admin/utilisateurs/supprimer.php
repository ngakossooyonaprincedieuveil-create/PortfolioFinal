<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        // 1. Récupérer l'email de l'utilisateur ciblé pour la suppression
        $stmt = $pdo->prepare("SELECT email FROM administrateurs WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $target_admin = $stmt->fetch();

        if ($target_admin) {
            // SÉCURITÉ : Interdiction stricte de supprimer le compte actuellement connecté
            if ($_SESSION['admin_email'] === $target_admin['email']) {
                // On annule et on redirige pour éviter le suicide de compte
                header('Location: index.php?erreur=self_delete');
                exit();
            }

            // 2. Si ce n'est pas nous-même, on procède à la suppression
            $delete = $pdo->prepare("DELETE FROM administrateurs WHERE id = :id");
            $delete->execute(['id' => $id]);
        }
    } catch (PDOException $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
}

header('Location: index.php');
exit();