<?php
session_start();
// Protection : seul un admin connecté accède à la liste des utilisateurs
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

try {
    // Récupération de tous les administrateurs par ordre alphabétique
    $stmt = $pdo->query("SELECT id, prenom, nom, email, date_creation FROM administrateurs ORDER BY nom ASC, prenom ASC");
    $admins = $stmt->fetchAll();
} catch (PDOException $e) {
    die("<div style='background: #ff0055; color: white; padding: 2rem; font-family: sans-serif; border-radius: 10px;'>
            <h3>Erreur SQL :</h3><p>" . htmlspecialchars($e->getMessage()) . "</p>
         </div>");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Administrateurs | Admin</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        body { background: #0f0f1a; color: white; font-family: 'Segoe UI', sans-serif; padding: 2rem; margin: 0; }
        .container { max-width: 1000px; margin: 0 auto; }
        
        /* Bouton Retour cohérent */
        .btn-retour { display: inline-flex; align-items: center; gap: 8px; color: #cbcbcb; text-decoration: none; background: rgba(255, 255, 255, 0.05); padding: 10px 18px; border-radius: 8px; font-size: 0.9rem; border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
        .btn-retour:hover { color: white; background: rgba(0, 119, 255, 0.15); border-color: #0077ff; transform: translateX(-3px); }
        
        /* Bouton d'action Ajouter */
        .btn-ajouter { background: #0077ff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; transition: background 0.2s; }
        .btn-ajouter:hover { background: #0055cc; }

        /* Style du tableau */
        table { width: 100%; border-collapse: collapse; background: #1a1a2e; border-radius: 12px; overflow: hidden; margin-top: 2rem; border: 1px solid rgba(255,255,255,0.05); }
        th, td { padding: 1.2rem; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        th { background: #252540; font-size: 0.9rem; color: #cbcbcb; text-transform: uppercase; letter-spacing: 0.5px; }
        tr:hover td { background: rgba(255,255,255,0.02); }

        /* Badges de rôle ou d'état */
        .badge-current { background: rgba(0, 255, 100, 0.1); color: #00ff64; border: 1px solid rgba(0, 255, 100, 0.2); padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold; margin-left: 8px; }
        .avatar-ui { width: 35px; height: 35px; background: #252540; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #0077ff; border: 1px solid rgba(0, 119, 255, 0.3); }
    </style>
</head>
<body>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <a href="../dashboard.php" class="btn-retour">← Retour au Dashboard</a>
            <a href="ajouter.php" class="btn-ajouter">➕ Ajouter un administrateur</a>
        </div>

        <h2>Équipe d'administration</h2>
        <p style="color: #8a8a9a; margin-top: -10px; margin-bottom: 2rem;">Liste des utilisateurs ayant un accès sécurisé au panneau de contrôle.</p>

        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">Profil</th>
                    <th>Nom Complet</th>
                    <th>Adresse Email</th>
                    <th>Date de création</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td>
                            <div class="avatar-ui">
                                <?php echo strtoupper(substr($admin['prenom'], 0, 1)); ?>
                            </div>
                        </td>
                        
                        <td>
                            <strong><?php echo e($admin['prenom'] . " " . $admin['nom']); ?></strong>
                            <?php if ($_SESSION['admin_email'] === $admin['email']): ?>
                                <span class="badge-current">Moi</span>
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <a href="mailto:<?php echo e($admin['email']); ?>" style="color: #0077ff; text-decoration: none;">
                                <?php echo e($admin['email']); ?>
                            </a>
                        </td>
                        
                        <td style="color: #cbcbcb;">
                            <?php echo date('d/m/Y', strtotime($admin['date_creation'])); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>