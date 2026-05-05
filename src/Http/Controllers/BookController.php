<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Infrastructure\Repositories\BookRepository;
use App\Infrastructure\Repositories\LoanRepository;

/**
 * Orchestration HTTP : paramètres GET, appels repositories, choix de la vue.
 * Aucune règle métier détaillée ici : tout passe par les objets du domaine.
 *
 * ---------------------------------------------------------------------------
 * REF-04 — `require` des vues et transit des données
 * ---------------------------------------------------------------------------
 * `require` (ou `include`) exécute un autre fichier **dans le même scope**
 * lexical que l'appelant : ici, les variables locales de `index()` / `show()`
 * (`$books`, `$book`, `$loans`, `$title`, `$today`, `$content`…) existent
 * déjà quand `home.php` ou `book_show.php` s'exécutent — ce ne sont pas des
 * « paramètres » passés entre parenthèses, mais des variables du bloc courant.
 *
 * Le gabarit `layout.php` reçoit `$title` et surtout `$content`, rempli via
 * `ob_start()` … `ob_get_clean()` : la vue « enfant » produit du HTML dans
 * un tampon, puis le layout l'injecte dans la page complète.
 */
final class BookController
{
    private string $viewDir;

    public function __construct(
        private BookRepository $books,
        private LoanRepository $loans,
    ) {
        $this->viewDir = dirname(__DIR__, 2) . '/View';
    }

    public function index(): void
    {
        $books = $this->books->findAll();
        $title = 'Catalogue — bibliothèque (démo)';
        ob_start();
        require $this->viewDir . '/home.php';
        $content = (string) ob_get_clean();
        require $this->viewDir . '/layout.php';
    }

    public function show(int $id): void
    {
        if ($id <= 0) {
            $this->respondBadRequest('Identifiant de livre invalide.');

            return;
        }

        $book = $this->books->findById($id);
        if ($book === null) {
            $this->respondNotFound('Livre introuvable.');

            return;
        }

        $loans = $this->loans->findByBookId($id);
        $today = new \DateTimeImmutable('today');
        $title = 'Livre — ' . $book->title();
        ob_start();
        require $this->viewDir . '/book_show.php';
        $content = (string) ob_get_clean();
        require $this->viewDir . '/layout.php';
    }

    private function respondBadRequest(string $message): void
    {
        http_response_code(400);
        $title = 'Requête incorrecte';
        ob_start();
        echo '<p class="err">' . \h($message) . '</p>';
        $content = (string) ob_get_clean();
        require $this->viewDir . '/layout.php';
    }

    private function respondNotFound(string $message): void
    {
        http_response_code(404);
        $title = 'Non trouvé';
        ob_start();
        echo '<p class="err">' . \h($message) . '</p>';
        $content = (string) ob_get_clean();
        require $this->viewDir . '/layout.php';
    }
}
