<?php

declare(strict_types=1);

/**
 * Point d'entrée HTTP — routage minimal (GET).
 *
 * Détail d'un livre : http://localhost:8080/?id=1
 * Liste du catalogue : http://localhost:8080/
 *
 * ---------------------------------------------------------------------------
 * REF-01 — Chargement des classes (autoload)
 * ---------------------------------------------------------------------------
 * Quand PHP rencontre `new BookRepository`, il doit charger le fichier de la
 * classe. Au lieu d'écrire une longue liste de `require` en tête de fichier,
 * `spl_autoload_register` enregistre une fonction : à chaque classe inconnue
 * du namespace `App\`, on déduit le chemin sous `src/` et on fait un `require`
 * unique du bon fichier. Même idée que Composer (PSR-4), en version minimale.
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

/** Échappement HTML (anti-XSS) — utilisé dans les vues */
function h(string $s): string
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

use App\Http\Controllers\BookController;
use App\Infrastructure\Database;
use App\Infrastructure\Repositories\BookRepository;
use App\Infrastructure\Repositories\LoanRepository;

header('Content-Type: text/html; charset=UTF-8');

/**
 * ---------------------------------------------------------------------------
 * REF-02 — Connexion BDD + câblage des couches
 * ---------------------------------------------------------------------------
 * Ici on construit les « dépendances » : PDO une fois, puis les repositories
 * qui le reçoivent, puis le contrôleur qui reçoit les repositories. Aucune
 * règle métier : uniquement du branchement (voir cours : injection simple).
 */
try {
    $pdo = Database::createPdo();
} catch (Throwable $e) {
    http_response_code(503);
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Erreur</title></head><body>';
    echo '<h1>Base de données indisponible</h1><p>Vérifie que <code>docker compose up -d</code> est lancé et que MySQL a fini de démarrer.</p>';
    echo '</body></html>';
    exit(1);
}

$bookRepository = new BookRepository($pdo);
$loanRepository = new LoanRepository($pdo);
$controller = new BookController($bookRepository, $loanRepository);

/**
 * ---------------------------------------------------------------------------
 * REF-03 — Routage minimal (paramètre GET)
 * ---------------------------------------------------------------------------
 * Pas de `?id=` → page catalogue. Sinon on délègue au contrôleur avec l'id
 * entier (la validation détaillée est dans BookController::show).
 */
try {
    $rawId = $_GET['id'] ?? null;
    if ($rawId === null || $rawId === '') {
        $controller->index();
        exit;
    }

    $id = (int) $rawId;
    $controller->show($id);
} catch (Throwable $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>Erreur</title></head><body>';
    echo '<h1>Erreur PHP</h1><p>En TP : souvent un <code>TODO</code> du domaine à compléter.</p>';
    echo '<pre style="background:#f1f5f9;padding:1rem;overflow:auto;">' . h($e->getMessage()) . '</pre>';
    echo '</body></html>';
}
