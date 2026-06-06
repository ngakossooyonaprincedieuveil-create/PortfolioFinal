<?php
session_start();
// Protection de la page : seul un admin connecté peut ajouter un autre utilisateur
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

$erreur = null;
$succes = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données du formulaire
    $prenom       = trim($_POST['prenom'] ?? '');
    $nom          = trim($_POST['nom'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    // Vérification des champs obligatoires
    if (empty($prenom) || empty($nom) || empty($email) || empty($mot_de_passe)) {
        $erreur = "Veuillez remplir tous les champs obligatoires (*).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } else {
        try {
            // 1. Vérifier si l'adresse email existe déjà dans la table administrateurs
            $verif = $pdo->prepare("SELECT COUNT(*) FROM administrateurs WHERE email = :email");
            $verif->execute(['email' => $email]);
            
            if ($verif->fetchColumn() > 0) {
                $erreur = "Cette adresse email est déjà associée à un compte administrateur.";
            } else {
                // 2. Chiffrement sécurisé du mot de passe (Méthode bcrypt par défaut)
                $mdp_chiffre = password_hash($mot_de_passe, PASSWORD_DEFAULT);

                // 3. Insertion dans ta table administrateurs
                $stmt = $pdo->prepare("INSERT INTO administrateurs (prenom, nom, email, mot_de_passe) VALUES (:prenom, :nom, :email, :mdp)");
                $stmt->execute([
                    'prenom' => $prenom,
                    'nom'    => $nom,
                    'email'  => $email,
                    'mdp'    => $mdp_chiffre
                ]);

                $succes = "L'administrateur <strong>" . htmlspecialchars($prenom . " " . $nom) . "</strong> a été créé avec succès !";
                
                // On vide les variables pour vider le formulaire après succès
                $prenom = $nom = $email = "";
            }
        } catch (PDOException $e) {
            $erreur = "Erreur SQL : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Administrateur | Admin</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        body { background: #0f0f1a; color: white; font-family: 'Segoe UI', sans-serif; padding: 2rem; margin: 0; }
        .container { max-width: 550px; margin: 0 auto; background: #1a1a2e; padding: 2.5rem; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 10px 30px rgba(0,0,0,0.4); }
        
        .btn-retour { display: inline-flex; align-items: center; gap: 8px; color: #cbcbcb; text-decoration: none; background: rgba(255, 255, 255, 0.05); padding: 10px 18px; border-radius: 8px; font-size: 0.9rem; border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease; margin-bottom: 1.5rem; }
        .btn-retour:hover { color: white; background: rgba(0, 119, 255, 0.15); border-color: #0077ff; transform: translateX(-3px); }
        
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.2rem; }
        label { color: #8a8a9a; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        input { padding: 12px; background: #222; border: 1px solid #444; color: white; border-radius: 8px; font-size: 1rem; transition: border 0.2s; }
        input:focus { border-color: #0077ff; outline: none; background: #252535; }
        
        .btn-submit { background: #0077ff; color: white; border: none; padding: 14px; cursor: pointer; border-radius: 8px; font-weight: bold; font-size: 1rem; margin-top: 1rem; transition: background 0.2s; width: 100%; }
        .btn-submit:hover { background: #0055cc; }
        
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; text-align: center; font-size: 0.95rem; }
        .alert-error { background: rgba(255,0,85,0.1); color: #ff0055; border: 1px solid rgba(255,0,85,0.2); }
        .alert-success { background: rgba(0,255,100,0.1); color: #00ff64; border: 1px solid rgba(0,255,100,0.2); }
    </style>
</head>
<body>

    <div class="container">
        <a href="../dashboard.php" class="btn-retour">← Retour au Dashboard</a>
        
        <h2 style="margin-top: 0; margin-bottom: 1.5rem; font-size: 1.6rem; letter-spacing: -0.5px;">Ajouter un nouvel administrateur</h2>

        <?php if ($erreur): ?><div class="alert alert-error"><?php echo $erreur; ?></div><?php endif; ?>
        <?php if ($succes): ?><div class="alert alert-success"><?php echo $succes; ?></div><?php endif; ?>

        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" value="<?php echo isset($prenom) ? htmlspecialchars($prenom) : ''; ?>" placeholder="Ex: Jean" required>
                </div>
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" value="<?php echo isset($nom) ? htmlspecialchars($nom) : ''; ?>" placeholder="Ex: Dupont" required>
                </div>
            </div>

            <div class="form-group">
                <label>Adresse Email *</label>
                <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" placeholder="Ex: admin@monsite.com" required>
            </div>

            <div class="form-group">
                <label>Mot de passe *</label>
                <input type="password" name="mot_de_passe" placeholder="Choisissez un mot de passe robuste" required>
            </div>

            <button type="submit" class="btn-submit">🔒 Créer le compte Admin</button>
        </form>
    </div>

</body>
</html>