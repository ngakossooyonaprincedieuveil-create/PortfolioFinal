<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

$erreur = null;
$succes = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $lien = trim($_POST['lien'] ?? '');
    
    if (empty($titre) || empty($description) || empty($technologies)) {
        $erreur = "Veuillez remplir tous les champs obligatoires (*).";
    } else {
        $nom_image = null;

        // Gestion de l'upload d'image
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_path'];
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExtension, $extensions_autorisees)) {
                // Renommer proprement le fichier pour éviter les doublons
                $nom_image = time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExtension;
                $uploadFileDir = '../../Images/Projets/';
                
                // Créer le dossier s'il n'existe pas
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }
                
                $dest_path = $uploadFileDir . $nom_image;
                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $erreur = "Erreur lors du déplacement de l'image sur le serveur.";
                    $nom_image = null;
                }
            } else {
                $erreur = "Format d'image non valide (uniquement JPG, JPEG, PNG, GIF, WEBP).";
            }
        }

        if (!$erreur) {
            try {
                $stmt = $pdo->prepare("INSERT INTO projets (titre, description, technologies, image, lien) VALUES (:titre, :description, :technologies, :image, :lien)");
                $stmt->execute([
                    'titre' => $titre,
                    'description' => $description,
                    'technologies' => $technologies,
                    'image' => $nom_image,
                    'lien' => !empty($lien) ? $lien : null
                ]);
                $succes = "Le projet a bien été ajouté !";
                // Réinitialisation des variables pour vider le formulaire
                $titre = $description = $technologies = $lien = "";
            } catch (PDOException $e) {
                $erreur = "Erreur insertion BDD : " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Projet | Admin</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        body { background: #0f0f1a; color: white; font-family: 'Segoe UI', sans-serif; padding: 2rem; }
        .container { max-width: 600px; margin: 0 auto; background: #1a1a2e; padding: 2.5rem; border-radius: 12px; border: 1px solid #333; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.2rem; }
        label { color: #8a8a9a; font-size: 0.9rem; font-weight: 600; }
        input, textarea { padding: 10px; background: #222; border: 1px solid #444; color: white; border-radius: 8px; font-size: 1rem; }
        input:focus, textarea:focus { border-color: #0077ff; outline: none; }
        .btn-submit { background: #0077ff; color: white; border: none; padding: 12px; cursor: pointer; border-radius: 8px; font-weight: bold; font-size: 1rem; margin-top: 1rem; }
        .btn-submit:hover { background: #0055cc; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-weight: 500; text-align: center; }
        .alert-error { background: rgba(255,0,85,0.1); color: #ff0055; border: 1px solid rgba(255,0,85,0.2); }
        .alert-success { background: rgba(0,255,100,0.1); color: #00ff64; border: 1px solid rgba(0,255,100,0.2); }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" style="color: #8a8a9a; text-decoration: none; display: block; margin-bottom: 1.5rem;">← Retour à la liste</a>
        <h2 style="margin-top: 0;">Ajouter un nouveau projet</h2>

        <?php if ($erreur): ?><div class="alert alert-error"><?php echo $erreur; ?></div><?php endif; ?>
        <?php if ($succes): ?><div class="alert alert-success"><?php echo $succes; ?></div><?php endif; ?>

        <!-- IMPORTANT: enctype obligatoirement pour le téléversement d'image -->
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Titre du projet *</label>
                <input type="text" name="titre" value="<?php echo isset($titre) ? htmlspecialchars($titre) : ''; ?>" placeholder="Ex: Poubelle Automatique" required>
            </div>
            <div class="form-group">
                <label>Technologies utilisées *</label>
                <input type="text" name="technologies" value="<?php echo isset($technologies) ? htmlspecialchars($technologies) : ''; ?>" placeholder="Ex: Arduino, C++, Capteurs" required>
            </div>
            <div class="form-group">
                <label>Lien du projet (Optionnel)</label>
                <input type="url" name="lien" value="<?php echo isset($lien) ? htmlspecialchars($lien) : ''; ?>" placeholder="Ex: https://github.com/...">
            </div>
            <div class="form-group">
                <label>Illustration (Image)</label>
                <input type="file" name="image" accept="image/*" style="background: transparent; border: none; padding-left: 0;">
            </div>
            <div class="form-group">
                <label>Description du projet *</label>
                <textarea name="description" rows="5" placeholder="Décris le projet en détail..." required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
            </div>
            <button type="submit" class="btn-submit">💾 Enregistrer le projet</button>
        </form>
    </div>
</body>
</html>