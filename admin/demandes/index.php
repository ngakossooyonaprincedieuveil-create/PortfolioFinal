<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php'; 

// Récupération des données adaptée à ta table demandes_projet
try {
    $stmt = $pdo->query("SELECT * FROM demandes_projet ORDER BY id DESC");
    $demandes = $stmt->fetchAll();
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
    <title>Gestion des Demandes de Projet | Admin</title>
    <!-- On garde ton fichier CSS s'il existe -->
    <link rel="stylesheet" href="../../CSS/style.css">
    
    <style>
        /* Styles personnalisés pour moderniser l'interface admin */
        body {
            background: #0f0f1a;
            color: white;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            padding: 2rem;
            margin: 0;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Style moderne pour le bouton de retour */
        .btn-retour {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #cbcbcb;
            text-decoration: none;
            background: rgba(255, 255, 255, 0.05);
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .btn-retour:hover {
            color: white;
            background: rgba(0, 119, 255, 0.15);
            border-color: #0077ff;
            transform: translateX(-3px);
        }

        .page-title {
            margin: 2.5rem 0 1.5rem 0;
            font-size: 1.8rem;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .grid-demandes {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        /* Cartes de demandes améliorées */
        .card-demande {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-demande:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-type {
            background: rgba(0, 119, 255, 0.15);
            color: #0088ff;
            border: 1px solid rgba(0, 119, 255, 0.3);
        }

        .badge-budget {
            background: rgba(0, 255, 100, 0.1);
            color: #00ff64;
            border: 1px solid rgba(0, 255, 100, 0.2);
            margin-left: 6px;
        }

        .btn-action {
            color: black;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-action:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(255, 170, 0, 0.3);
        }
    </style>
</head>
<body>

    <div class="container">
        
        <!-- Bouton Retour au Dashboard plus présentable -->
        <div style="margin-bottom: 1rem;">
            <a href="../dashboard.php" class="btn-retour">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Retour au Dashboard
            </a>
        </div>

        <h2 class="page-title">Demandes de projets reçues</h2>

        <?php if (empty($demandes)): ?>
            <p style="color: #8a8a9a; background: rgba(255,255,255,0.01); padding: 2rem; border-radius: 8px; text-align: center; border: 1px dashed rgba(255,255,255,0.1);">
                Aucune demande de projet pour le moment.
            </p>
        <?php else: ?>
            <div class="grid-demandes">
                <?php foreach ($demandes as $d): ?>
                    <div class="card-demande" style="border: 1px solid <?php echo $d['lu'] ? 'rgba(255,255,255,0.06)' : 'rgba(255, 170, 0, 0.4)'; ?>; background: <?php echo $d['lu'] ? 'rgba(255,255,255,0.01)' : 'rgba(255, 170, 0, 0.02)'; ?>;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <strong style="font-size: 1.15rem; color: #fff;"><?php echo e($d['nom']); ?></strong>
                                <span class="badge badge-type"><?php echo e($d['type_projet']); ?></span>
                                
                                <?php if (!empty($d['budget'])): ?>
                                    <span class="badge badge-budget">💰 <?php echo e($d['budget']); ?></span>
                                <?php endif; ?>
                            </div>
                            <p style="margin: 0; color: #a0a0b0; font-size: 0.95rem; line-height: 1.5;">
                                <?php echo e(mb_strimwidth($d['description'], 0, 100, "...")); ?>
                            </p>
                        </div>
                        
                        <div style="margin-left: 1.5rem;">
                            <a href="voir.php?id=<?php echo (int)$d['id']; ?>" class="btn-action" style="background: <?php echo $d['lu'] ? '#e2e8f0' : '#ffaa00'; ?>;">
                                <?php echo $d['lu'] ? "Consulter" : "Consulter (Nouveau)"; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>