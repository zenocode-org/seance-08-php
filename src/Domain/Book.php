<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Entité « livre » — correspond à la table `books`.
 *
 * TP ex. 1 — hydratation PDO : {@see self::fromRow()}
 * TP ex. 2 — règle métier : {@see self::isAvailable()}
 */
final class Book
{
    public function __construct(
        private int $id,
        private string $title,
        private string $author,
        private string $isbn,
        private int $publicationYear,
        private int $availableCopies,
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function author(): string
    {
        return $this->author;
    }

    public function isbn(): string
    {
        return $this->isbn;
    }

    public function publicationYear(): int
    {
        return $this->publicationYear;
    }

    public function availableCopies(): int
    {
        return $this->availableCopies;
    }

    /**
     * TP ex. 1 — à écrire : mapper les clés de la ligne SQL vers le constructeur.
     *
     * Clés attendues : id, title, author, isbn, publication_year, available_copies
     *
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        throw new \RuntimeException(
            'TP ex.1 : implémenter Book::fromRow() avec casts explicites (voir README §3 et cours).'
        );
    }

    /**
     * TP ex. 2 — à écrire : le livre est disponible à l'emprunt s'il reste au moins un exemplaire libre.
     */
    public function isAvailable(): bool
    {
        throw new \RuntimeException(
            'TP ex.2 : implémenter Book::isAvailable() (voir README §3).'
        );
    }
}
