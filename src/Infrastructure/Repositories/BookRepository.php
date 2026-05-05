<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Book;
use PDO;

/**
 * Accès SQL aux livres — hydratation via {@see Book::fromRow()}.
 *
 * Après `fetch` / `fetchAll`, on a des **tableaux** : la « traduction » en
 * objet métier se fait ici à la lisière (cours), en appelant `Book::fromRow`
 * — le repository ne réécrit pas les champs à la main une fois le domaine en place.
 */
final class BookRepository
{
    public function __construct(private PDO $pdo) {}

    /**
     * @return list<Book>
     */
    public function findAll(): array
    {
        $sql = <<<'SQL'
            SELECT id, title, author, isbn, publication_year, available_copies
            FROM books
            ORDER BY title ASC
            SQL;
        $stmt = $this->pdo->query($sql);
        if ($stmt === false) {
            return [];
        }
        /** @var list<array<string, mixed>> $rows */
        $rows = $stmt->fetchAll();
        $books = [];
        foreach ($rows as $row) {
            $books[] = Book::fromRow($row);
        }

        return $books;
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare(
            <<<'SQL'
                SELECT id, title, author, isbn, publication_year, available_copies
                FROM books
                WHERE id = :id
                LIMIT 1
                SQL
        );
        $stmt->execute(['id' => $id]);
        /** @var array<string, mixed>|false $row */
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        return Book::fromRow($row);
    }
}
