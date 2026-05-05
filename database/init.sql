-- Schéma + données de démo — TP « Catalogue bibliothèque »
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE books (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    isbn VARCHAR(32) NOT NULL,
    publication_year SMALLINT UNSIGNED NOT NULL,
    available_copies INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_books_isbn (isbn)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE borrowers (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_borrowers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE loans (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    book_id INT UNSIGNED NOT NULL,
    borrower_id INT UNSIGNED NOT NULL,
    borrowed_at DATETIME NOT NULL,
    returned_at DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_loans_book (book_id),
    KEY idx_loans_borrower (borrower_id),
    CONSTRAINT fk_loans_book FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_loans_borrower FOREIGN KEY (borrower_id) REFERENCES borrowers (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO books (id, title, author, isbn, publication_year, available_copies) VALUES
(1, 'L''Étranger', 'Albert Camus', '978-2070360024', 1942, 3),
(2, 'Petit traité de résistance à l''efficacité', 'Isabelle Stengers', '978-2707174323', 2019, 0),
(3, 'Apprendre PHP 8', 'Démo Pédago', '978-0000000001', 2024, 5);

INSERT INTO borrowers (id, name, email) VALUES
(1, 'Alice Martin', 'alice@exemple.test'),
(2, 'Bob Durand', 'bob@exemple.test');

INSERT INTO loans (id, book_id, borrower_id, borrowed_at, returned_at) VALUES
(1, 1, 1, '2025-01-10 14:00:00', '2025-01-25 10:00:00'),
(2, 1, 2, '2025-02-01 09:30:00', NULL),
(3, 3, 1, '2025-03-05 11:00:00', NULL);
