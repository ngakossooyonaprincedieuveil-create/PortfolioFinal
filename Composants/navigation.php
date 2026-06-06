<!-- Navigation -->
    <?php
// On détecte le nom du fichier actuel
$page_courante = basename($_SERVER['PHP_SELF']);

// On détermine le préfixe du chemin
// Si on est dans l'index (racine), on doit entrer dans Pages/
// Si on n'est pas dans l'index, on est déjà dans Pages/, donc pas de préfixe pour les fichiers voisins
$chemin_pages = ($page_courante === 'index.php') ? './Pages/' : './';
$chemin_index = ($page_courante === 'index.php') ? './' : '../';
?>

<nav class="navbar">
    <div class="container">
        <div class="nav-brand">DN</div>
        <ul class="nav-links">
            <li>
                <a href="<?= $chemin_index ?>index.php" class="<?= ($page_courante === 'index.php') ? 'active' : '' ?>">
                    Accueil
                </a>
            </li>
            <li>
                <a href="<?= $chemin_pages ?>projets.php" class="<?= ($page_courante === 'projets.php') ? 'active' : '' ?>">
                    Projets
                </a>
            </li>
            <li>
                <a href="<?= $chemin_pages ?>contact.php" class="<?= ($page_courante === 'contact.php') ? 'active' : '' ?>">
                    Contact
                </a>
            </li>
        </ul>
    </div>
</nav>