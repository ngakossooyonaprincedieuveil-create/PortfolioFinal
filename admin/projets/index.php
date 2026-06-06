<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

try {
    // Récupération de tous les projets du plus récent au plus ancien
    $stmt = $pdo->query("SELECT * FROM projets ORDER BY id DESC");
    $projets = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Projets | Admin</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        body { background: #0f0f1a; color: white; font-family: 'Segoe UI', sans-serif; padding: 2rem; margin: 0; }
        .container { max-width: 1100px; margin: 0 auto; }
        
        .btn-retour { display: inline-flex; align-items: center; gap: 8px; color: #cbcbcb; text-decoration: none; background: rgba(255, 255, 255, 0.05); padding: 10px 18px; border-radius: 8px; font-size: 0.9rem; border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; }
        .btn-retour:hover { color: white; background: rgba(0, 119, 255, 0.15); border-color: #0077ff; transform: translateX(-3px); }
        
        .btn-ajouter { background: #0077ff; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; display: inline-flex; align-items: center; gap: 5px; transition: background 0.2s; }
        .btn-ajouter:hover { background: #0055cc; }

        table { width: 100%; border-collapse: collapse; background: #1a1a2e; border-radius: 12px; overflow: hidden; margin-top: 2rem; border: 1px solid rgba(255,255,255,0.05); }
        th, td { padding: 1.2rem; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        th { background: #252540; font-size: 0.9rem; color: #cbcbcb; text-transform: uppercase; }
        tr:hover td { background: rgba(255,255,255,0.02); }

        .btn-modifier { background: #ffaa00; color: black; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; margin-right: 5px; }
        .btn-supprimer { background: #ff0055; color: white; text-decoration: none; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; }
        .img-preview { width: 60px; height: 45px; object-fit: contain; background: #0f0f1a; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <a href="../dashboard.php" class="btn-retour">← Retour au Dashboard</a>
            <a href="ajouter.php" class="btn-ajouter">➕ Ajouter un projet</a>
        </div>

        <h2>Liste de tes projets publiés</h2>

        <?php if (empty($projets)): ?>
            <p style="color: #8a8a9a; background: rgba(255,255,255,0.01); padding: 2rem; border-radius: 8px; text-align: center; border: 1px dashed rgba(255,255,255,0.1);">Aucun projet pour le moment. Commence par en ajouter un !</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Technologies</th>
                        <th>Lien</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projets as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['image'])): ?>
                                    <img src="../../Images/Projets/<?php echo e($p['image']); ?>" class="img-preview" alt="Projet">
                                <?php else: ?>
                                    <span style="color: #555;">Aucune</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo e($p['titre']); ?></strong></td>
                            <td><span style="background: rgba(0,119,255,0.1); color: #0077ff; padding: 3px 8px; border-radius: 4px; font-size: 0.85rem;"><?php echo e($p['technologies']); ?></span></td>
                            <td>
                                <?php if (!empty($p['lien'])): ?>
                                    <a href="<?php echo e($p['lien']); ?>" target="_blank" style="color: #00ff64; text-decoration: none;">Visiter ↗</a>
                                <?php else: ?>
                                    <span style="color: #666;">Aucun</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="modifier.php?id=<?php echo (int)$p['id']; ?>" class="btn-modifier">✏️ Modifier</a>
                                <a href="supprimer.php?id=<?php echo (int)$p['id']; ?>" class="btn-supprimer" onclick="return confirm('Es-tu sûr de vouloir supprimer ce projet ? Cette action est irréversible.');">🗑️ Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>