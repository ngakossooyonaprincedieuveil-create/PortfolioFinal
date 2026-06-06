<?php 
require_once "../fonctions.php"; 
require_once "../config/connexion.php";

journaliser_visite($pdo);

$erreurs = [];
$success = false;

$Nom = $Email = $Sujet = $Message = '';
$demande = ['Nom' => '', 'Email' => '', 'Type' => '', 'Description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifier_csrf($_POST['csrf_token'])) {
        $erreurs[] = "Erreur de sécurité : Jeton CSRF invalide.";
    } else {
        // Traitement Formulaire 1
        if (isset($_POST['Sujet'])) {
            $Nom = $_POST['Nom'] ?? ''; $Email = $_POST['Email'] ?? ''; 
            $Sujet = $_POST['Sujet'] ?? ''; $Message = $_POST['Message'] ?? '';
            if (champ_requis($Nom) && champ_requis($Email) && champ_requis($Sujet) && champ_requis($Message)) {
                $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, message, lu) VALUES (:nom, :email, :message, 0)");
                $stmt->execute(['nom' => nettoyer($Nom), 'email' => nettoyer($Email), 'message' => nettoyer($Message)]);
                $success = "Merci pour ton message ! Je te répondrai bientôt.";
                $Nom = $Email = $Sujet = $Message = '';
            } else { $erreurs[] = "Tous les champs du message sont obligatoires."; }
        }
        // Traitement Formulaire 2
        if (isset($_POST['ProjType'])) {
            $demande = ['Nom' => $_POST['ProjNom'] ?? '', 'Email' => $_POST['ProjEmail'] ?? '', 'Type' => $_POST['ProjType'] ?? '', 'Description' => $_POST['ProjDesc'] ?? ''];
            if (champ_requis($demande['Nom']) && champ_requis($demande['Email']) && champ_requis($demande['Type']) && champ_requis($demande['Description'])) {
                $stmt = $pdo->prepare("INSERT INTO demandes_projet (nom, email, type_projet, description, lu) VALUES (:nom, :email, :type, :description, 0)");
                $stmt->execute(['nom' => nettoyer($demande['Nom']), 'email' => nettoyer($demande['Email']), 'type' => nettoyer($demande['Type']), 'description' => nettoyer($demande['Description'])]);
                $success = "Merci pour ta demande de projet ! Je vais l'examiner rapidement.";
                $demande = ['Nom' => '', 'Email' => '', 'Type' => '', 'Description' => ''];
            } else { $erreurs[] = "Tous les champs de la demande sont obligatoires."; }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contact - Dieuveil NGAKOSSO</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
    <?php require_once "../Composants/navigation.php"; ?>

    <section class="page-header">
        <div class="container">
            <h1>Me Contacter</h1>
            <p>Des questions ? Tu as un projet ? Contacte-moi !</p>
        </div>
    </section>

    <div class="container" style="margin-top: 20px; text-align: center;">
        <?php foreach ($erreurs as $e): ?><div style="background:#ff4d4d; color:white; padding:10px; border-radius:8px; margin-bottom:10px;"><?php echo e($e); ?></div><?php endforeach; ?>
        <?php if ($success): ?><div style="background:#2ed573; color:white; padding:10px; border-radius:8px; margin-bottom:10px;"><?php echo e($success); ?></div><?php endif; ?>
    </div>

    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-form">
                    <h2>Envoie-moi un message</h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generer_csrf(); ?>">
                        <div class="form-group"><label>Nom *</label><input type="text" name="Nom" value="<?php echo e($Nom); ?>" required></div>
                        <div class="form-group"><label>Email *</label><input type="email" name="Email" value="<?php echo e($Email); ?>" required></div>
                        <div class="form-group"><label>Sujet *</label><input type="text" name="Sujet" value="<?php echo e($Sujet); ?>" required></div>
                        <div class="form-group"><label>Message *</label><textarea name="Message" rows="6" required><?php echo e($Message); ?></textarea></div>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>

                <div class="project-request-form">
                    <h2>Demande de Projet</h2>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generer_csrf(); ?>">
                        <div class="form-group"><label>Nom de l'entreprise / Client *</label><input type="text" name="ProjNom" value="<?php echo e($demande['Nom']); ?>" required></div>
                        <div class="form-group"><label>Email *</label><input type="email" name="ProjEmail" value="<?php echo e($demande['Email']); ?>" required></div>
                        <div class="form-group"><label>Type de projet *</label>
                            <select name="ProjType" required>
                                <option value="">-- Sélectionne --</option>
                                <option value="site-vitrine">Site Vitrine</option>
                                <option value="web-app">Application Web</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Description de ton projet *</label><textarea name="ProjDesc" rows="6" required><?php echo e($demande['Description']); ?></textarea></div>
                        <button type="submit" class="btn btn-primary">Envoyer ma demande</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <?php require_once "../Composants/pied-de-page.php"; ?>
</body>
</html>