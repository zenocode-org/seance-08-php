# Corrigé de référence — TP POO + MySQL (usage enseignant)

Ne pas distribuer aux étudiants si tu veux garder uniquement le squelette « à trous ».

## Fichiers concernés

Remplacer le contenu des classes dans `src/Domain/` par les versions ci-dessous (ou s’en inspirer).

---

## `src/Domain/Book.php`

```php
<?php

declare(strict_types=1);

namespace App\Domain;

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

    public function isAvailable(): bool
    {
        return $this->availableCopies > 0;
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['title'],
            (string) $row['author'],
            (string) $row['isbn'],
            (int) $row['publication_year'],
            (int) $row['available_copies'],
        );
    }
}
```

---

## `src/Domain/Borrower.php`

```php
<?php

declare(strict_types=1);

namespace App\Domain;

final class Borrower
{
    public function __construct(
        private int $id,
        private string $name,
        private string $email,
    ) {}

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['name'],
            (string) $row['email'],
        );
    }
}
```

---

## `src/Domain/Loan.php`

```php
<?php

declare(strict_types=1);

namespace App\Domain;

final class Loan
{
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

    public function isReturned(): bool
    {
        return $this->returnedAt !== null;
    }

    public function isLate(\DateTimeImmutable $today): bool
    {
        if ($this->isReturned()) {
            return false;
        }

        $due = $this->borrowedAt->modify('+' . (string) self::MAX_BORROW_DAYS . ' days');

        return $today > $due;
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        $borrower = Borrower::fromRow([
            'id' => (int) $row['borrower_id'],
            'name' => (string) $row['borrower_name'],
            'email' => (string) $row['borrower_email'],
        ]);

        $borrowedRaw = (string) $row['loan_borrowed_at'];
        $borrowedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $borrowedRaw);
        if ($borrowedAt === false) {
            throw new \InvalidArgumentException('Date d\'emprunt invalide : ' . $borrowedRaw);
        }

        $returnedAt = null;
        if (isset($row['loan_returned_at']) && $row['loan_returned_at'] !== null && (string) $row['loan_returned_at'] !== '') {
            $returnedRaw = (string) $row['loan_returned_at'];
            $parsed = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $returnedRaw);
            if ($parsed === false) {
                throw new \InvalidArgumentException('Date de retour invalide : ' . $returnedRaw);
            }
            $returnedAt = $parsed;
        }

        return new self(
            (int) $row['loan_id'],
            (int) $row['loan_book_id'],
            $borrower,
            $borrowedAt,
            $returnedAt,
        );
    }
}
```

---

## Checklist (reprise enseignant)

- [ ] `docker compose up -d --build` puis [http://localhost:8080/](http://localhost:8080/) : liste des 3 livres.
- [ ] Livre `id=1` : au moins un emprunt « rendu », un autre « en cours » ou « en retard » selon la date du jour.
- [ ] Livre `id=2` : badge indisponible (`available_copies = 0`).
- [ ] Aucun `PDO` / SQL dans `src/Domain/`.
- [ ] Casts explicites dans tous les `fromRow()`.
