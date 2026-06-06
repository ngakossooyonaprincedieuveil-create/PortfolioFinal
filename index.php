<?php
require_once 'config/connexion.php';
// Inclusion du fichier fonctions.php qui est maintenant à la racine
require_once 'fonctions.php';

// 4.1. Journalisation de la visite dès le chargement
journaliser_visite($pdo);

// Récupération des statistiques dynamiques pour la section "À propos"
try {
    $total_projets = $pdo->query("SELECT COUNT(*) FROM projets")->fetchColumn();
} catch (PDOException $e) {
    error_log("Erreur count projets index : " . $e->getMessage());
    $total_projets = 0;
}

// Récupération des 3 projets les plus récents pour la section "Projets Récents"
try {
    $stmt = $pdo->query("SELECT * FROM projets ORDER BY id DESC LIMIT 3");
    $projets_recents = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erreur projets récents index : " . $e->getMessage());
    $projets_recents = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dieuveil NGAKOSSO - Portfolio</title>
    <link rel="stylesheet" href="./CSS/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php require "./Composants/navigation.php"; ?>
    
    <section class="hero">
        <div class="container">
            <div class="hero-grid">
                <div class="hero-content">
                    <h1 class="hero-title">Salut, je suis <span class="highlight">Dieuveil NGAKOSSO</span></h1>
                    <p class="hero-subtitle">Étudiant en Génie logiciel et Administration réseau</p>
                    <p class="hero-description">
                        Passionné par les technologies web et les réseaux, je conçois des solutions modernes tout en développant mes compétences pour devenir un professionnel du numérique.
                    </p>
                    <div class="hero-buttons">
                        <a href="./Pages/projets.php" class="btn btn-primary">Voir mes projets</a>
                        <a href="./Pages/contact.php" class="btn btn-secondary">Me contacter</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="Images/Projets/WhatsApp_Image_2026-03-30_at_20.44.20-removebg-preview.png" alt="Dieuveil NGAKOSSO">
                </div>
            </div>
        </div>
    </section>

    <section class="skills">
        <div class="container">
            <h2 class="section-title">Mes Compétences</h2>
            <div class="skills-grid">
                <div class="skill-card">
                    <h3>Électronique & Embarqué</h3>
                    <p>Arduino, C++, Capteurs, Servomoteurs, Programmation microcontrôleurs</p>
                </div>
                <div class="skill-card">
                    <h3>Bases de Données</h3>
                    <p>C, MySQL, Bibliothèques MySQL, CRUD, Gestion de données</p>
                </div>
                <div class="skill-card">
                    <h3>Réseaux & Infrastructure</h3>
                    <p>Routage, Routes Statiques, Access Lists, Cisco IOS, Simulation Réseau</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about">
        <div class="container">
            <h2 class="section-title">À propos de moi</h2>
            <div class="about-content">
                <div class="about-grid">
                    <div class="about-text">
                        <h3>Qui suis-je ?</h3>
                        <p>
                            Je suis Dieuveil NGAKOSSO, un étudiant passionné par l'informatique et ses multiples domaines d'application. Mon parcours académique m'a permis d'explorer différentes facettes de l'informatique : des mathématiques fondamentales aux réseaux, en passant par le génie logiciel et l'administration système. Je suis motivé par la résolution de problèmes complexes et la création de solutions innovantes qui font la différence.
                        </p>
                    </div>
                    <div class="about-stats">
                        <div class="stat-card">
                            <h4>Projets Réalisés</h4>
                            <p class="stat-number"><?php echo $total_projets; ?></p>
                            <span>Enregistrés en BDD</span>
                        </div>
                        <div class="stat-card">
                            <h4>Domaines</h4>
                            <p class="stat-number">4</p>
                            <span>Électronique, Données, Réseau, Logiciel</span>
                        </div>
                    </div>
                </div>

                <div class="timeline">
                    <h3>Mon Parcours</h3>
                    <div class="timeline-container">
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h4>2021 - 2022</h4>
                                <p class="timeline-title">Baccalauréat Série C</p>
                                <p class="timeline-description">
                                    Obtention du baccalauréat scientifique avec un excellent bagage en mathématiques et sciences physiques. Formation fondamentale solide qui m'a permis d'accéder à l'enseignement supérieur avec les bases nécessaires pour les études informatiques.
                                </p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h4>2022 - 2023</h4>
                                <p class="timeline-title">Licence 1 - Mathématiques</p>
                                <p class="timeline-description">
                                    Première année d'études supérieures axée sur les mathématiques appliquées. Découverte des fondamentaux mathématiques nécessaires pour comprendre les algorithmes et l'informatique théorique. Base essentielle pour toute formation en informatique.
                                </p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h4>2023 - 2025</h4>
                                <p class="timeline-title">Licence 1 & 2 - Réseaux et Télécommunications</p>
                                <p class="timeline-description">
                                    Formation en réseaux informatiques et télécommunications. Maîtrise des protocoles, routage, configuration d'infrastructures réseau, et principes des télécommunications. Réalisation de projets pratiques incluant la simulation de réseaux avec routes statiques et access lists.
                                </p>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <h4>2025 - 2026</h4>
                                <p class="timeline-title">Licence 2 - Génie Logiciel & Administration Réseau</p>
                                <p class="timeline-description">
                                    Poursuite de mes études combinant le génie logiciel et l'administration réseau. Apprentissage du développement d'applications robustes, gestion de bases de données, et administration avancée d'infrastructures réseau. Acquisition de compétences complètes en informatique moderne.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="recent-projects">
        <div class="container">
            <h2 class="section-title">Projets Récents</h2>
            
            <div style="text-align: center; margin-bottom: 2rem;">
                <form action="./Pages/projets.php" method="GET" style="display: inline-flex; gap: 10px; max-width: 500px; width: 100%;">
                    <input type="text" name="recherche" placeholder="Rechercher un projet (ex: Arduino, MySQL...)" style="flex: 1; padding: 10px 15px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">🔍</button>
                </form>
            </div>

            <div class="projects-grid">
    <?php if (empty($projets_recents)): ?>
        <p style="text-align: center; color: #8a8a9a; grid-column: 1 / -1;">Aucun projet publié pour le moment.</p>
    <?php else: ?>
        <?php foreach ($projets_recents as $p): ?>
            <div class="project-card">
                <!-- Chemin corrigé avec Images/Projets/ -->
                <div class="project-image" style="background: url('Images/Projets/<?php echo e($p['image']); ?>') center/contain no-repeat; background-color: #1e293b; height: 200px; border-radius: 8px 8px 0 0;"></div>
                <div style="padding: 20px;">
                    <h3><?php echo e($p['titre']); ?></h3>
                    <p><?php echo e($p['description']); ?></p>
                    <p class="technologies"><strong>Technologies :</strong> <?php echo e($p['technologies']); ?></p>
                    <a href="./Pages/projets.php" class="btn-link">Voir plus →</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2>Tu as un projet en tête ?</h2>
            <p>Contacte-moi pour discuter de tes idées</p>
            <a href="./Pages/contact.php" class="btn btn-primary">Envoyer un message</a>
        </div>
    </section>
    
    <?php require "./Composants/pied-de-page.php"; ?>
</body>
</html>