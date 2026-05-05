<?php

declare(strict_types=1);

use App\Domain\Book;
use App\Domain\Loan;

/**
 * Même principe que {@see home.php} (REF-05) : fichier `require` depuis
 * {@see \App\Http\Controllers\BookController::show()} — variables du scope du contrôleur.
 *
 * @var string $title
 * @var Book $book
 * @var list<Loan> $loans
 * @var \DateTimeImmutable $today
 */
?>
<h1><?= \h($book->title()) ?></h1>
<p class="sub">Par <?= \h($book->author()) ?> · ISBN <?= \h($book->isbn()) ?> · <?= (string) $book->publicationYear() ?></p>
<p>
  Exemplaires disponibles : <strong><?= (string) $book->availableCopies() ?></strong>
  <?php if ($book->isAvailable()) { ?>
    <span class="ok">empruntable</span>
  <?php } else { ?>
    <span class="no">non empruntable (0 exemplaire libre)</span>
  <?php } ?>
</p>

<h2>Emprunts enregistrés</h2>
<?php if ($loans === []) { ?>
  <p class="muted">Aucun emprunt pour ce livre.</p>
<?php } else { ?>
  <table>
    <thead>
      <tr>
        <th>Emprunteur</th>
        <th>Emprunt</th>
        <th>Retour</th>
        <th>Statut</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($loans as $loan) { ?>
        <tr>
          <td><?= \h($loan->borrower()->name()) ?><br><span class="muted"><?= \h($loan->borrower()->email()) ?></span></td>
          <td><?= \h($loan->borrowedAt()->format('d/m/Y H:i')) ?></td>
          <td><?= $loan->returnedAt() === null ? '—' : \h($loan->returnedAt()->format('d/m/Y H:i')) ?></td>
          <td>
            <?php if ($loan->isReturned()) { ?>
              <span class="ok">rendu</span>
            <?php } elseif ($loan->isLate($today)) { ?>
              <span class="no">en retard</span>
            <?php } else { ?>
              <span class="ok">en cours</span>
            <?php } ?>
          </td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
<?php } ?>
