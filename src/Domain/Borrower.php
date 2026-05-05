<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Entité « emprunteur » — correspond à la table `borrowers`.
 *
 * TP ex. 3 — à écrire entièrement : propriétés, constructeur, accesseurs, {@see self::fromRow()}.
 *
 * {@see self::fromRow()} peut recevoir un tableau avec les clés :
 *   - id (int)
 *   - name (string)
 *   - email (string)
 *
 * Astuce : {@see Loan::fromRow()} appellera {@see Borrower::fromRow()} avec ces trois clés.
 */
final class Borrower
{
    // --- TP ex. 3 : ajoute des propriétés privées + constructeur + getters id(), name(), email() ---

    public function __construct()
    {
        throw new \RuntimeException(
            'TP ex.3 : remplacer ce constructeur par une version avec propriétés (voir README §3).'
        );
    }

    public function id(): int
    {
        throw new \RuntimeException('TP ex.3 : implémenter Borrower::id().');
    }

    public function name(): string
    {
        throw new \RuntimeException('TP ex.3 : implémenter Borrower::name().');
    }

    public function email(): string
    {
        throw new \RuntimeException('TP ex.3 : implémenter Borrower::email().');
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        throw new \RuntimeException(
            'TP ex.3 : implémenter Borrower::fromRow() (casts explicites id, name, email).'
        );
    }
}
