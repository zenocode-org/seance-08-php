<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Loan;
use PDO;

/**
 * Emprunts liés à un livre — jointure vers les emprunteurs.
 */
final class LoanRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * @return list<Loan>
     */
    public function findByBookId(int $bookId): array
    {
        $stmt = $this->pdo->prepare(
            <<<'SQL'
                SELECT
                    l.id AS loan_id,
                    l.book_id AS loan_book_id,
                    l.borrowed_at AS loan_borrowed_at,
                    l.returned_at AS loan_returned_at,
                    b.id AS borrower_id,
                    b.name AS borrower_name,
                    b.email AS borrower_email
                FROM loans l
                INNER JOIN borrowers b ON b.id = l.borrower_id
                WHERE l.book_id = :book_id
                ORDER BY l.borrowed_at DESC
                SQL
        );
        $stmt->execute(['book_id' => $bookId]);
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll();
        $loans = [];
        foreach ($rows as $row) {
            $loans[] = Loan::fromRow($row);
        }

        return $loans;
    }
}
