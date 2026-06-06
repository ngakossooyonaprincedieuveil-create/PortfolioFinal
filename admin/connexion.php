<?php
session_start();
require_once '../config/connexion.php';
require_once '../fonctions.php';

// 1. Si déjà connecté, redirection vers le dashboard
if (isset($_SESSION['admin_connecte']) && $_SESSION['admin_connecte'] === true) {
    header('Location: dashboard.php');
    exit();
}

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 2. Protection CSRF
    if (!isset($_POST['csrf_token']) || !verifier_csrf($_POST['csrf_token'])) {
        $erreur = "Identifiants ou jeton invalides.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        // 3. Authentification (On récupère tout l'admin par son email)
        $stmt = $pdo->prepare("SELECT * FROM administrateurs WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $admin = $stmt->fetch();

        // 4. Vérification password et message générique
        if ($admin && password_verify($mot_de_passe, $admin['mot_de_passe'])) {
            // 5. Régénération ID de session
            session_regenerate_id(true);
            
            // 6. Stockage uniquement ID et Prénom
            $_SESSION['admin_connecte'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_pseudo'] = $admin['prenom'];
            
            header('Location: dashboard.php');
            exit();
        } else {
            // Message générique identique pour les deux cas (email faux ou mdp faux)
            $erreur = "Identifiants incorrects.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Connexion Administration</title></head>
<body style="background: #0f0f1a; color: white; display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: sans-serif;">
    <div style="background: rgba(255,255,255,0.03); padding: 2.5rem; border-radius: 16px; width: 350px;">
        <h2 style="text-align: center;">Connexion</h2>
        
        <?php if ($erreur): ?>
            <div style="color: #FF0055; text-align: center; margin-bottom: 1rem;"><?php echo htmlspecialchars($erreur); ?></div>
        <?php endif; ?>

        <form method="POST" action="connexion.php" style="display: flex; flex-direction: column; gap: 1rem;">
            <input type="hidden" name="csrf_token" value="<?php echo generer_csrf(); ?>">
            
            <input type="email" name="email" placeholder="Email" required style="padding: 10px; background: #222; border: 1px solid #444; color: white; border-radius: 8px;">
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required style="padding: 10px; background: #222; border: 1px solid #444; color: white; border-radius: 8px;">
            
            <button type="submit" style="padding: 10px; background: #0077ff; color: white; border: none; cursor: pointer; border-radius: 8px;">Se connecter</button>
        </form>
    </div>
</body>
</html>