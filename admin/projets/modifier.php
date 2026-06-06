<?php
session_start();
require_once '../verif_session.php';
require_once '../../config/connexion.php';
require_once '../../fonctions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header('Location: index.php'); exit(); }

$erreur = null;
$succes = null;

try {
    $stmt = $pdo->prepare("SELECT * FROM projets WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $projet = $stmt->fetch();
    if (!$projet) { header('Location: index.php'); exit(); }
} catch (PDOException $e) { die("Erreur SQL : " . $e->getMessage()); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $lien = trim($_POST['lien'] ?? '');
    
    if (empty($titre) || empty($description) || empty($technologies)) {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    } else {
        $nom_image = $projet['image']; // On garde l'ancienne image par défaut

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['image']['tmp_path'];
            $fileName = $_FILES['image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $extensions_autorisees = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($fileExtension, $extensions_autorisees)) {
                // Supprimer l'ancienne image physiquement du serveur s'il y en avait une
                if (!empty($projet['image']) && file_exists('../../Images/Projets/' . $projet['image'])) {
                    unlink('../../Images/Projets/' . $projet['image']);
                }
                
                $nom_image = time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExtension;
                move_uploaded_file($fileTmpPath, '../../Images/Projets/' . $nom_image);
            } else {
                $erreur = "Format d'image invalide.";
            }
        }

        if (!$erreur) {
            try {
                $update = $pdo->prepare("UPDATE projets SET titre = :titre, description = :description, technologies = :technologies, image = :image, lien = :lien WHERE id = :id");
                $update->execute([
                    'titre' => $titre,
                    'description' => $description,
                    'technologies' => $technologies,
                    'image' => $nom_image,
                    'lien' => !empty($lien) ? $lien : null,
                    'id' => $id
                ]);
                $succes = "Projet mis à jour avec succès !";
                
                // Rafraîchir les valeurs locales pour l'affichage du formulaire
                $projet['titre'] = $titre;
                $projet['description'] = $description;
                $projet['technologies'] = $technologies;
                $projet['image'] = $nom_image;
                $projet['lien'] = $lien;
            } catch (PDOException $e) { $erreur = "Erreur SQL : " . $e->getMessage(); }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Projet | Admin</title>
    <link rel="stylesheet" href="../../CSS/style.css">
    <style>
        body { background: #0f0f1a; color: white; font-family: 'Segoe UI', sans-serif; padding: 2rem; }
        .container { max-width: 600px; margin: 0 auto; background: #1a1a2e; padding: 2.5rem; border-radius: 12px; border: 1px solid #333; }
        .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 1.2rem; }
        label { color: #8a8a9a; font-size: 0.9rem; font-weight: 600; }
        input, textarea { padding: 10px; background: #222; border: 1px solid #444; color: white; border-radius: 8px; font-size: 1rem; }
        .btn-submit { background: #ffaa00; color: black; border: none; padding: 12px; cursor: pointer; border-radius: 8px; font-weight: bold; font-size: 1rem; margin-top: 1rem; }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: center; }
        .alert-error { background: rgba(255,0,85,0.1); color: #ff0055; border: 1px solid rgba(255,0,85,0.2); }
        .alert-success { background: rgba(0,255,100,0.1); color: #00ff64; border: 1px solid rgba(0,255,100,0.2); }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" style="color: #8a8a9a; text-decoration: none; display: block; margin-bottom: 1.5rem;">← Retour à la liste</a>
        <h2 style="margin-top: 0;">Modifier le projet</h2>

        <?php if ($erreur): ?><div class="alert alert-error"><?php echo $erreur; ?></div><?php endif; ?>
        <?php if ($succes): ?><div class="alert alert-success"><?php echo $succes; ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Titre du projet *</label>
                <input type="text" name="titre" value="<?php echo e($projet['titre']); ?>" required>
            </div>
            <div class="form-group">
                <label>Technologies utilisées *</label>
                <input type="text" name="technologies" value="<?php echo e($projet['technologies']); ?>" required>
            </div>
            <div class="form-group">
                <label>Lien du projet</label>
                <input type="url" name="lien" value="<?php echo e($projet['lien']); ?>">
            </div>
            <div class="form-group">
                <label>Remplacer l'image (Laisse vide pour conserver l'actuelle)</label>
                <?php if (!empty($projet['image'])): ?>
                    <p style="margin: 0; color: #8a8a9a; font-size: 0.85rem;">Image actuelle : <code><?php echo e($projet['image']); ?></code></p>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" style="background: transparent; border: none; padding-left: 0;">
            </div>
            <div class="form-group">
                <label>Description du projet *</label>
                <textarea name="description" rows="5" required><?php echo e($projet['description']); ?></textarea>
            </div>
            <button type="submit" class="btn-submit">✨ Appliquer les modifications</button>
        </form>
    </div>
</body>
</html>