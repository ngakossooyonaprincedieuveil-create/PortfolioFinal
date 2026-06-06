<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si un champ n'est pas vide après nettoyage des espaces.
 */
function champ_requis(string $valeur): bool {
    return !empty(trim($valeur));
}

/**
 * Nettoie une chaîne de caractères contre les failles XSS.
 */
function nettoyer(string $valeur): string {
    return htmlspecialchars(trim($valeur), ENT_QUOTES, 'UTF-8');
}

/**
 * Alias de la fonction nettoyer pour l'affichage HTML.
 */
function e($valeur): string {
    return nettoyer((string)$valeur);
}

/**
 * Génère un jeton CSRF unique et sécurisé.
 */
function generer_csrf(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie la validité du jeton CSRF soumis en évitant les attaques temporelles.
 */
function verifier_csrf($token_soumis): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token_soumis);
}

/**
 * 4.1. Journalisation la visite de l'utilisateur de manière sécurisée.
 */
function journaliser_visite($pdo): void {
    // [i] Exigence 4.1 du sujet : Vérification obligatoire de l'IP derrière un proxy
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $liste_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($liste_ips[0]);
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    // Validation basique de l'IP pour s'assurer que le format est correct (IPv4 ou IPv6)
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $ip = '0.0.0.0'; 
    }

    // On extrait juste le nom du fichier (ex: index.php) au lieu du chemin complet du script
    $page_courante = basename($_SERVER['PHP_SELF']) ?? 'Inconnu';

    try {
        // Ajustement des colonnes pour correspondre à ton sujet et ton tracker (adresse_ip, page_visitee)
        $stmt = $pdo->prepare("INSERT INTO visites (adresse_ip, page_visitee, date_visite) VALUES (:ip, :page, NOW())");
        $stmt->execute([
            'ip'   => $ip,
            'page' => $page_courante
        ]);
    } catch (PDOException $e) {
        error_log("Erreur tracker visites : " . $e->getMessage());
    }
}