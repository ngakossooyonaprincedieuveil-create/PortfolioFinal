<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

// 1. Récupération et sécurisation de l'ID passé dans l'URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    // Si pas d'ID valide, redirection immédiate vers la liste des messages
    header('Location: index.php');
    exit();
}

try {
    // 2. Récupération du message spécifique dans la table messages_contact
    $stmt = $pdo->prepare("SELECT * FROM messages_contact WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $message = $stmt->fetch();

    // Si le message n'existe pas en base de données
    if (!$message) {
        header('Location: index.php');
        exit();
    }

    // 3. Mise à jour automatique du statut : Si le message est "Non lu", on le passe en "Lu"
    if ($message['lu'] == 0) {
        $update = $pdo->prepare("UPDATE messages_contact SET lu = 1 WHERE id = :id");
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
    <title>Lecture du message de <?php echo e($message['nom']); ?> | Admin</title>
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

        /* Bouton Retour cohérent avec le reste de l'administration */
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
            background: rgba(255, 0, 85, 0.1);
            border-color: #ff0055;
            transform: translateX(-3px);
        }

        /* Fiche de lecture */
        .fiche-message {
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
            color: #ff0055;
            font-weight: 500;
        }

        .meta-item a:hover {
            text-decoration: underline;
        }

        .message-box {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 8px;
            padding: 1.5rem;
            border-left: 4px solid #ff0055;
            line-height: 1.6;
            color: #cbcbcb;
            white-space: pre-wrap; /* Garde la mise en page et les sauts de ligne d'origine */
        }
    </style>
</head>
<body>

    <div class="container">
        
        <a href="index.php" class="btn-retour">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Retour à la liste
        </a>

        <div class="fiche-message">
            <div class="header-fiche">
                <span style="color: #ff0055; font-size: 0.85rem; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Message Général de Contact</span>
                <h1 style="margin: 5px 0 0 0; font-size: 1.8rem;">De : <?php echo e($message['nom']); ?></h1>
                <!-- Utilisation de la colonne date_envoi de ta table messages_contact -->
                <small style="color: #8a8a9a;">Envoyé le : <?php echo date('d/m/Y à H:i', strtotime($message['date_envoi'])); ?></small>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <label>Expéditeur</label>
                    <span><?php echo e($message['nom']); ?></span>
                </div>
                
                <div class="meta-item">
                    <label>Adresse de contact</label>
                    <a href="mailto:<?php echo e($message['email']); ?>"><?php echo e($message['email']); ?></a>
                </div>
            </div>

            <div style="margin-top: 1.5rem;">
                <label style="color: #8a8a9a; display:block; font-size: 0.85rem; text-transform: uppercase; margin-bottom: 8px; font-weight:600;">Contenu du message :</label>
                <div class="message-box">
                    <!-- Utilisation de la colonne message de ta table messages_contact -->
                    <?php echo e($message['message']); ?>
                </div>
            </div>

            <!-- Action rapide pour répondre directement au visiteur -->
            <div style="margin-top: 2.5rem; text-align: right;">
                <a href="mailto:<?php echo e($message['email']); ?>?subject=Réponse à votre message de contact - Portfolio" 
                   style="background: #ff0055; color: white; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: bold; display: inline-block; transition: background 0.2s;">
                    ✉️ Répondre par Email
                </a>
            </div>
        </div>

    </div>

</body>
</html>