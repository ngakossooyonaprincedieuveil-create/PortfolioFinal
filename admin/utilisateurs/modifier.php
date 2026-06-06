<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: index.php'); exit(); }

$erreur = null;
$succes = null;

// 1. Récupération des infos actuelles de l'administrateur
try {
    $stmt = $pdo->prepare("SELECT id, prenom, nom, email FROM administrateurs WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $admin = $stmt->fetch();
    if (!$admin) { header('Location: index.php'); exit(); }
} catch (PDOException $e) { die("Erreur SQL : " . $e->getMessage()); }

// 2. Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom       = trim($_POST['prenom'] ?? '');
    $nom          = trim($_POST['nom'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';

    if (empty($prenom) || empty($nom) || empty($email)) {
        $erreur = "Veuillez remplir tous les champs obligatoires (*).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "L'adresse email n'est pas valide.";
    } else {
        try {
            // Vérifier si l'email n'est pas déjà pris par un AUTRE utilisateur
            $verif = $pdo->prepare("SELECT COUNT(*) FROM administrateurs WHERE email = :email AND id != :id");
            $verif->execute(['email' => $email, 'id' => $id]);
            
            if ($verif->fetchColumn() > 0) {
                $erreur = "Cette adresse email est déjà utilisée par un autre compte.";
            } else {
                // Si un nouveau mot de passe est saisi, on le chiffre, sinon on garde l'actuel
                if (!empty($mot_de_passe)) {
                    $sql = "UPDATE administrateurs SET prenom = :prenom, nom = :nom, email = :email, mot_de_passe = :mdp WHERE id = :id";
                    $params = [
                        'prenom' => $prenom,
                        'nom'    => $nom,
                        'email'  => $email,
                        'mdp'    => password_hash($mot_de_passe, PASSWORD_DEFAULT),
                        'id'     => $id
                    ];
                } else {
                    $sql = "UPDATE administrateurs SET prenom = :prenom, nom = :nom, email = :email WHERE id = :id";
                    $params = [
                        'prenom' => $prenom,
                        'nom'    => $nom,
                        'email'  => $email,
                        'id'     => $id
                    ];
                }

                $update = $pdo->prepare($sql);
                $update->execute($params);
                $succes = "Profil mis à jour avec succès !";

                // Mise à jour des données locales pour ré-affichage
                $admin['prenom'] = $prenom;
                $admin['nom'] = $nom;
                $admin['email'] = $email;
                
                // Si c'est l'utilisateur connecté qui vient de se modifier lui-même, on met à jour sa session
                if ($_SESSION['admin_email'] === $email) {
                    $_SESSION['admin_pseudo'] = $prenom;
                }
            }
        } catch (PDOException $e) { $erreur = "Erreur SQL : " . $e->getMessage(); }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Administrateur | Admin</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        body { background: #0f0f1a; color: white; font-family: 'Segoe UI', sans-serif; padding: 2rem; }
        .container { max-width: 550px; margin: 0 auto; background: #1a1a2e; padding: 2.5rem; border-radius: 12px; border: 1px solid #333; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.2rem; }
        label { color: #8a8a9a; font-size: 0.9rem; font-weight: 600; text-transform: uppercase; }
        input { padding: 12px; background: #222; border: 1px solid #444; color: white; border-radius: 8px; font-size: 1rem; }
        input:focus { border-color: #ffaa00; outline: none; }
        .btn-submit { background: #ffaa00; color: black; border: none; padding: 14px; cursor: pointer; border-radius: 8px; font-weight: bold; font-size: 1rem; margin-top: 1rem; width: 100%; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; }
        .alert-error { background: rgba(255,0,85,0.1); color: #ff0055; border: 1px solid rgba(255,0,85,0.2); }
        .alert-success { background: rgba(0,255,100,0.1); color: #00ff64; border: 1px solid rgba(0,255,100,0.2); }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" style="color: #8a8a9a; text-decoration: none; display: block; margin-bottom: 1.5rem;">← Retour à la liste</a>
        <h2 style="margin-top: 0; margin-bottom: 1.5rem;">Modifier le profil admin</h2>

        <?php if ($erreur): ?><div class="alert alert-error"><?php echo $erreur; ?></div><?php endif; ?>
        <?php if ($succes): ?><div class="alert alert-success"><?php echo $succes; ?></div><?php endif; ?>

        <form method="POST">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" value="<?php echo e($admin['prenom']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" value="<?php echo e($admin['nom']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Adresse Email *</label>
                <input type="email" name="email" value="<?php echo e($admin['email']); ?>" required>
            </div>

            <div class="form-group">
                <label>Nouveau mot de passe (Laisse vide pour ne pas changer)</label>
                <input type="password" name="mot_de_passe" placeholder="Remplir uniquement pour modifier">
            </div>

            <button type="submit" class="btn-submit">✨ Sauvegarder les modifications</button>
        </form>
    </div>
</body>
</html>