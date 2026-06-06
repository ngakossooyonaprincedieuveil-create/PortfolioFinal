<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

// 1. Récupération et sécurisation de l'ID passé dans l'URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    // Si pas d'ID valide, redirection immédiate vers la liste
    header('Location: index.php');
    exit();
}

try {
    // 2. Récupération de la demande spécifique
    $stmt = $pdo->prepare("SELECT * FROM demandes_projet WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $demande = $stmt->fetch();

    // Si la demande n'existe pas en BDD
    if (!$demande) {
        header('Location: index.php');
        exit();
    }

    // 3. ADAPTATION AUTOMATIQUE : Si la demande est "Non lue", on la passe en "Lue"
    if ($demande['lu'] == 0) {
        $update = $pdo->prepare("UPDATE demandes_projet SET lu = 1 WHERE id = :id");
        $update->execute(['id' => $id]);
    }

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
    <title>Détail de la demande de <?php echo e($demande['nom']); ?> | Admin</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    
    <style>
        body {
            background: #0f0f1a;
            color: white;
            font-family: 'Segoe UI', Roboto, sans-serif;
            padding: 2rem;
            margin: 0;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Bouton Retour identique à demandes/index.php */
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
            margin-bottom: 2rem;
        }

        .btn-retour:hover {
            color: white;
            background: rgba(255, 170, 0, 0.1);
            border-color: #ffaa00;
            transform: translateX(-3px);
        }

        /* Fiche de visualisation */
        .fiche-demande {
            background: #1a1a2e;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .header-fiche {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .meta-item {
            background: rgba(255,255,255,0.02);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.04);
        }

        .meta-item label {
            display: block;
            color: #8a8a9a;
            font-size: 0.85rem;
            text-transform: uppercase;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .meta-item span, .meta-item a {
            font-size: 1.05rem;
            color: #fff;
            text-decoration: none;
        }

        .meta-item a {
            color: #0077ff;
            font-weight: 500;
        }

        .meta-item a:hover {
            text-decoration: underline;
        }

        .message-box {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 1.5rem;
            border-left: 4px solid #ffaa00;
            line-height: 1.6;
            color: #cbcbcb;
            white-space: pre-wrap; /* Conserve les retours à la ligne du client */
        }
    </style>
</head>
<body>

    <div class="container">
        
        <a href="index.php" class="btn-retour">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Retour à la liste
        </a>

        <div class="fiche-demande">
            <div class="header-fiche">
                <span style="color: #ffaa00; font-size: 0.85rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Détails du projet soumis</span>
                <h1 style="margin: 5px 0 0 0; font-size: 1.8rem;"><?php echo e($demande['type_projet']); ?></h1>
                <small style="color: #8a8a9a;">Reçu le : <?php echo date('d/m/Y à H:i', strtotime($demande['date_demande'])); ?></small>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <label>Nom du Client</label>
                    <span><?php echo e($demande['nom']); ?></span>
                </div>
                
                <div class="meta-item">
                    <label>Adresse Email</label>
                    <a href="mailto:<?php echo e($demande['email']); ?>"><?php echo e($demande['email']); ?></a>
                </div>

                <div class="meta-item">
                    <label>Budget Estimé</label>
                    <span style="color: #00ff64; font-weight: bold;">
                        <?php echo !empty($demande['budget']) ? e($demande['budget']) : 'Non spécifié'; ?>
                    </span>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <label style="color: #8a8a9a; display:block; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 8px; font-weight:600;">Description des besoins :</label>
                <div class="message-box">
                    <?php echo e($demande['description']); ?>
                </div>
            </div>

            <div style="margin-top: 2.5rem; text-align: right;">
                <a href="mailto:<?php echo e($demande['email']); ?>?subject=Réponse à votre demande de projet : <?php echo rawurlencode($demande['type_projet']); ?>" 
                   style="background: #0077ff; color: white; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; display: inline-block; transition: background 0.2s;">
                    ✉️ Répondre par Email
                </a>
            </div>
        </div>

    </div>

</body>
</html>