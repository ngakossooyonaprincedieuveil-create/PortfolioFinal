<?php 
require_once '../config/connexion.php';
require_once '../fonctions.php';

journaliser_visite($pdo);

$search = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';

$all_projets = [
    [
        'titre' => 'Poubelle Automatique',
        'description' => 'Conception et programmation d\'une poubelle automatique utilisant Arduino. Le projet inclut un capteur de distance pour détecter la présence d\'un objet, ce qui déclenche l\'ouverture automatique du couvercle via un servomoteur.',
        'technologies' => 'Arduino, C++, Capteurs, Servomoteur',
        'image' => '../Images/Projets/kit-arduino-diy-poubelle-automatique-snar46.jpg'
    ],
    [
        'titre' => 'Gestion Base de Données',
        'description' => 'Développement d\'une application en langage C permettant de gérer une base de données MySQL incluant les opérations CRUD avec une interface en ligne de commande.',
        'technologies' => 'C, MySQL, CRUD',
        'image' => '../Images/Projets/16108543285909_image6.png'
    ],
    [
        'titre' => 'Simulation d\'un Réseau',
        'description' => 'Conception et simulation d\'une infrastructure réseau complète incluant la configuration des routeurs, les routes statiques et l\'implémentation d\'access lists (ACL).',
        'technologies' => 'Routage, Cisco IOS, ACL',
        'image' => '../Images/Projets/image-25.png'
    ]
];

$resultats = [];
foreach ($all_projets as $p) {
    if (empty($search) || str_contains(strtolower($p['titre'] . $p['technologies']), $search)) {
        $resultats[] = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Projets - Dieuveil NGAKOSSO</title>
    <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
   <?php require "../Composants/navigation.php"; ?>
   
    <section class="page-header">
        <div class="container">
            <h1>Mes Projets</h1>
            <p>Découvre les projets sur lesquels j'ai travaillé</p>
        </div>
    </section>

    <section class="search-section">
        <div class="container">
            <form method="GET" action="" style="display: flex; gap: 10px; margin-bottom: 20px;">
                <input type="text" name="q" class="search-bar" 
                       placeholder="Recherche par technologies (Arduino, C, Réseau, MySQL...)" 
                       value="<?php echo e($search); ?>" 
                       style="flex-grow: 1;">
                <button type="submit" class="btn-link" style="border: none; cursor: pointer; padding: 10px 20px;">Rechercher</button>
                <?php if(!empty($search)): ?>
                    <a href="projets.php" class="btn-link" style="background-color: #64748b;">Effacer</a>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <section class="projects-section">
        <div class="container">
            <div class="projects-grid-full">
                <?php if (empty($resultats)): ?>
                    <p style="text-align: center; width: 100%;">Aucun projet ne correspond à ta recherche.</p>
                <?php else: ?>
                    <?php foreach($resultats as $projet): ?> 
                        <div class="project-card-full">
                            <div class="project-image-full" style="background-image: url('<?php echo e($projet['image']); ?>'); background-position: center; background-size: contain; background-repeat: no-repeat; background-color: #1e293b;"></div>
                            <div class="project-info">
                                <h3><?php echo e($projet['titre']); ?></h3>
                                <p><?php echo e($projet['description']); ?></p>
                                <div class="project-tech">
                                    <?php foreach(explode(',', $projet['technologies']) as $tech): ?>
                                        <span class="tech-tag"><?php echo e(trim($tech)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <div class="project-links">
                                    <a href="projets.php" class="btn-link">Voir le projet →</a>
                                    <a href="https://github.com" class="btn-link" target="_blank">Code GitHub →</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php require "../Composants/pied-de-page.php"; ?>
</body>
</html>