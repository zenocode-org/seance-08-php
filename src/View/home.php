<?php

declare(strict_types=1);

use App\Domain\Book;

/**
 * ---------------------------------------------------------------------------
 * REF-05 — Vue incluse par le contrôleur
 * ---------------------------------------------------------------------------
 * Ce fichier n'est pas appelé directement par Apache : il est `require` depuis
 * {@see \App\Http\Controllers\BookController::index()} (REF-04). Les variables
 * `$title` et `$books` viennent du scope de cette méthode — pas de magie.
 *
 * @var string $title
 * @var list<Book> $books
 */
?>
<h1>Catalogue</h1>
<p class="sub">Clique sur un titre pour voir la fiche et l’historique d’emprunts (données MySQL).</p>

<ul class="books">
  <?php foreach ($books as $book) { ?>
    <li>
      <a href="/?id=<?= (string) $book->id() ?>"><strong><?= \h($book->title()) ?></strong></a>
      <span class="muted"> — <?= \h($book->author()) ?></span>
      <?php if ($book->isAvailable()) { ?>
        <span class="ok">disponible</span>
      <?php } else { ?>
        <span class="no">indisponible</span>
      <?php } ?>
    </li>
  <?php } ?>
</ul>
