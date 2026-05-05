<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Emprunt — table `loans`, enrichi avec les infos emprunteur (JOIN côté repository).
 *
 * TP ex. 4.1 — règles : {@see self::isReturned()}, {@see self::isLate()}
 * TP ex. 4.2 — hydratation : {@see self::fromRow()}
 */
final class Loan
{
    /** Durée d'emprunt maximale (jours) avant considération « en retard » (règle métier pédago.) */
    public const MAX_BORROW_DAYS = 21;

    public function __construct(
        private int $id,
        private int $bookId,
        private Borrower $borrower,
        private \DateTimeImmutable $borrowedAt,
        private ?\DateTimeImmutable $returnedAt,
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function bookId(): int
    {
        return $this->bookId;
    }

    public function borrower(): Borrower
    {
        return $this->borrower;
    }

    public function borrowedAt(): \DateTimeImmutable
    {
        return $this->borrowedAt;
    }

    public function returnedAt(): ?\DateTimeImmutable
    {
        return $this->returnedAt;
    }

    /**
     * TP ex. 4.1 — à écrire : true si une date de retour est enregistrée.
     */
    public function isReturned(): bool
    {
        throw new \RuntimeException(
            'TP ex.4.1 : implémenter Loan::isReturned() (voir README §3).'
        );
    }

    /**
     * TP ex. 4.1 — à écrire : en retard si non rendu ET aujourd'hui dépasse {@see self::MAX_BORROW_DAYS}
     * après la date d'emprunt (comparaison sur dates complètes ou sur minuit : reste cohérent avec ton choix, documente-le en commentaire).
     *
     * Astuce : {@see \DateTimeImmutable::modify()} avec `+N days`.
     */
    public function isLate(\DateTimeImmutable $today): bool
    {
        throw new \RuntimeException(
            'TP ex.4.1 : implémenter Loan::isLate() avec la constante MAX_BORROW_DAYS (voir README §3).'
        );
    }

    /**
     * TP ex. 4.2 — à écrire : construire un {@see Borrower} via {@see Borrower::fromRow()} puis instancier ce prêt.
     *
     * Clés attendues (alias SQL du repository) :
     *   loan_id, loan_book_id, loan_borrowed_at, loan_returned_at,
     *   borrower_id, borrower_name, borrower_email
     *
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        throw new \RuntimeException(
            'TP ex.4.2 : implémenter Loan::fromRow() (dates en DateTimeImmutable, voir README §3).'
        );
    }
}
