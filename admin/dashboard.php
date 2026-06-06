<?php
session_start();
if (!isset($_SESSION['admin_connecte']) || $_SESSION['admin_connecte'] !== true) {
    header('Location: connexion.php');
    exit();
}
require_once '../config/connexion.php';
require_once '../fonctions.php';

try {
    $total_projets = $pdo->query("SELECT COUNT(*) FROM projets")->fetchColumn();
    $messages_non_lus = $pdo->query("SELECT COUNT(*) FROM messages_contact WHERE lu = 0")->fetchColumn();
    $demandes_non_lues = $pdo->query("SELECT COUNT(*) FROM demandes_projet WHERE lu = 0")->fetchColumn();
    $dernieres_visites = $pdo->query("SELECT adresse_ip, page, date_visite FROM visites ORDER BY date_visite DESC LIMIT 5")->fetchAll();
    $dernieres_demandes = $pdo->query("SELECT nom, email, date_demande FROM demandes_projet ORDER BY date_demande DESC LIMIT 5")->fetchAll();
} catch (PDOException $e) { die("Erreur : " . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin | Gestion</title>
    <style>
        :root { 
            --bg: #0f0f1a; 
            --card: #1a1a2e; 
            --accent: #0077ff; 
            --text: #ffffff; 
            --warning: #ffaa00;
        }
        body { background: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; margin: 0; padding: 2rem; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; }
        
        /* Transformation des cartes en blocs cliquables propres */
        a.card-link {
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .card { 
            background: var(--card); 
            padding: 1.8rem; 
            border-radius: 12px; 
            text-align: center; 
            border: 1px solid rgba(255, 255, 255, 0.08); 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }
        
        /* Effet au survol des rectangles interactifs */
        a.card-link .card:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
            box-shadow: 0 10px 20px rgba(0, 119, 255, 0.15);
            background: rgba(26, 26, 46, 0.8);
        }

        /* Effet spécifique pour les éléments non lus */
        a.card-link.attention .card:hover {
            border-color: var(--warning);
            box-shadow: 0 10px 20px rgba(255, 170, 0, 0.15);
        }

        .card h3 { color: #8a8a9a; margin: 0 0 8px 0; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .card p { font-size: 2.5rem; font-weight: bold; margin: 0; color: var(--accent); }
        
        /* Style des compteurs d'alerte */
        .card p.alert-count { color: var(--warning); }
        
        .badge-notice {
            display: inline-block;
            font-size: 0.75rem;
            background: rgba(255, 170, 0, 0.1);
            color: var(--warning);
            padding: 4px 8px;
            border-radius: 4px;
            margin-top: 8px;
            font-weight: 600;
        }

        .badge-ok {
            display: inline-block;
            font-size: 0.75rem;
            background: rgba(0, 255, 100, 0.1);
            color: #00ff64;
            padding: 4px 8px;
            border-radius: 4px;
            margin-top: 8px;
            font-weight: 600;
        }

        table { width: 100%; border-collapse: collapse; background: var(--card); border-radius: 12px; overflow: hidden; margin-bottom: 2.5rem; border: 1px solid rgba(255,255,255,0.05); }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        th { background: #252540; font-size: 0.9rem; color: #cbcbcb; }
        tr:hover td { background: rgba(255,255,255,0.01); }

        a.btn { background: #ff0055; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; transition: background 0.2s; }
        a.btn:hover { background: #d00044; }
        
        .section-title { margin-top: 2rem; font-size: 1.3rem; font-weight: 600; color: #fff; display: flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>

    <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
        <div>
            <h1 style="margin: 0; font-size: 2rem;">Tableau de Bord</h1>
            <p style="color: #8a8a9a; margin: 5px 0 0 0;">Bienvenue Monsieur, <?php echo htmlspecialchars($_SESSION['admin_pseudo']); ?> !</p>
        </div>
        <a href="deconnexion.php" class="btn">Se déconnecter</a>
    </header>

    <div class="grid">
        <a href="projets/index.php" class="card-link">
            <div class="card">
                <h3>Projets</h3>
                <p><?php echo (int)$total_projets; ?></p>
                <span class="badge-ok">Gérer le portfolio →</span>
            </div>
        </a>

        <a href="messages/index.php" class="card-link <?php echo $messages_non_lus > 0 ? 'attention' : ''; ?>">
            <div class="card">
                <h3>Messages</h3>
                <p class="<?php echo $messages_non_lus > 0 ? 'alert-count' : ''; ?>">
                    <?php echo (int)$messages_non_lus; ?>
                </p>
                <?php if ($messages_non_lus > 0): ?>
                    <span class="badge-notice">⚡ <?php echo $messages_non_lus; ?> non lu(s) →</span>
                <?php else: ?>
                    <span class="badge-ok">Aucun nouveau message →</span>
                <?php endif; ?>
            </div>
        </a>

        <a href="demandes/index.php" class="card-link <?php echo $demandes_non_lues > 0 ? 'attention' : ''; ?>">
            <div class="card">
                <h3>Demandes de projet</h3>
                <p class="<?php echo $demandes_non_lues > 0 ? 'alert-count' : ''; ?>">
                    <?php echo (int)$demandes_non_lues; ?>
                </p>
                <?php if ($demandes_non_lues > 0): ?>
                    <span class="badge-notice">🔥 <?php echo $demandes_non_lues; ?> nouvelle(s) →</span>
                <?php else: ?>
                    <span class="badge-ok">À jour →</span>
                <?php endif; ?>
            </div>
        </a>
    </div>

    <h3 class="section-title">📊 Dernières visites</h3>
    <table>
        <tr><th>IP</th><th>Page consultée</th><th>Date et Heure</th></tr>
        <?php if (empty($dernieres_visites)): ?>
            <tr><td colspan="3" style="text-align: center; color: #8a8a9a;">Aucune statistique de visite.</td></tr>
        <?php else: ?>
            <?php foreach ($dernieres_visites as $v): ?>
                <tr>
                    <td><code><?php echo htmlspecialchars($v['adresse_ip']); ?></code></td>
                    <td><span style="background: rgba(255,255,255,0.05); padding: 3px 8px; border-radius: 4px; font-size: 0.9rem;"><?php echo htmlspecialchars($v['page']); ?></span></td>
                    <td style="color: #cbcbcb;"><?php echo $v['date_visite']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

    <h3 class="section-title">📬 Dernières demandes reçues</h3>
    <table>
        <tr><th>Nom</th><th>Email</th><th>Date de réception</th></tr>
        <?php if (empty($dernieres_demandes)): ?>
            <tr><td colspan="3" style="text-align: center; color: #8a8a9a;">Aucune demande enregistrée.</td></tr>
        <?php else: ?>
            <?php foreach ($dernieres_demandes as $d): ?>
                <tr>
                    <strong><td><?php echo htmlspecialchars($d['nom']); ?></td></strong>
                    <td><a href="mailto:<?php echo htmlspecialchars($d['email']); ?>" style="color: var(--accent); text-decoration: none;"><?php echo htmlspecialchars($d['email']); ?></a></td>
                    <td style="color: #cbcbcb;"><?php echo $d['date_demande']; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

</body>
</html>