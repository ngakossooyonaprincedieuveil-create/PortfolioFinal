<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php'; 

// Récupération des données adaptée à ta table messages_contact
try {
    $stmt = $pdo->query("SELECT * FROM messages_contact ORDER BY id DESC");
    $messages = $stmt->fetchAll();
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
    <title>Gestion des Messages de Contact | Admin</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    
    <style>
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

        .grid-messages {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
        }

        /* Cartes de messages */
        .card-message {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-message:hover {
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

        .badge-contact {
            background: rgba(255, 0, 85, 0.15);
            color: #ff0055;
            border: 1px solid rgba(255, 0, 85, 0.3);
            margin-left: 10px;
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
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div style="margin-bottom: 1rem;">
            <a href="../dashboard.php" class="btn-retour">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Retour au Dashboard
            </a>
        </div>

        <h2 class="page-title">Messages de contact reçus</h2>

        <?php if (empty($messages)): ?>
            <p style="color: #8a8a9a; background: rgba(255,255,255,0.01); padding: 2rem; border-radius: 8px; text-align: center; border: 1px dashed rgba(255,255,255,0.1);">
                Aucun message pour le moment.
            </p>
        <?php else: ?>
            <div class="grid-messages">
                <?php foreach ($messages as $m): ?>
                    <div class="card-message" style="border: 1px solid <?php echo $m['lu'] ? 'rgba(255,255,255,0.06)' : 'rgba(255, 0, 85, 0.4)'; ?>; background: <?php echo $m['lu'] ? 'rgba(255,255,255,0.01)' : 'rgba(255, 0, 85, 0.02)'; ?>;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <strong style="font-size: 1.15rem; color: #fff;"><?php echo e($m['nom']); ?></strong>
                                <span style="color: #8a8a9a; font-size: 0.9rem;">(<?php echo e($m['email']); ?>)</span>
                                <span class="badge badge-contact">Contact</span>
                            </div>
                            <p style="margin: 0; color: #a0a0b0; font-size: 0.95rem; line-height: 1.5;">
                                <?php echo e(mb_strimwidth($m['message'], 0, 100, "...")); ?>
                            </p>
                        </div>
                        
                        <div style="margin-left: 1.5rem;">
                            <a href="voir.php?id=<?php echo (int)$m['id']; ?>" class="btn-action" style="background: <?php echo $m['lu'] ? '#e2e8f0' : '#ff0055'; ?>; color: <?php echo $m['lu'] ? 'black' : 'white'; ?>;">
                                <?php echo $m['lu'] ? "Lire" : "Lire (Nouveau)"; ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>