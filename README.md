# TP — POO PHP + base de données (1 h 30)

**Thème :** *Modélisation objet* — classes du **domaine**, **hydratation** `fromRow()`, **repositories** (infrastructure), sans toucher au routage HTTP ni aux vues.

**Intitulé pédago. :** « Catalogue bibliothèque » — affichage de livres et d’emprunts depuis MySQL.

> **Enseignant·e :** corrigé de référence (ne pas distribuer si tu veux un TP « à trous » seulement) : [ENSEIGNANT-corrige.md](ENSEIGNANT-corrige.md).

## Prérequis

- Docker et Docker Compose.
- Séance 6–7 : PHP de base, `$_GET`, Docker ; cours 7 : classes, `fromRow`, repositories, séparation domaine / infrastructure.

## Arborescence (rappel cours)

```text
projet/
├── public/index.php           ← point d’entrée HTTP (déjà fait)
├── src/
│   ├── Http/Controllers/      ← orchestration (déjà fait)
│   ├── Infrastructure/      ← PDO + repositories (déjà fait)
│   ├── Domain/                ← **TU TRAVAILLES ICI**
│   └── View/                  ← HTML (déjà fait)
└── database/init.sql          ← schéma + données de démo
```

---

## 1 — Mise en route (Docker)

1. À la racine du dossier : `docker compose up -d --build` (la **première** fois, MySQL importe `database/init.sql`).
2. Attends ~10–20 s que MySQL soit prêt, puis ouvre :
   - **Catalogue :** [http://localhost:8080/](http://localhost:8080/)
   - **Fiche livre :** [http://localhost:8080/?id=1](http://localhost:8080/?id=1)
   - **phpMyAdmin :** [http://localhost:8081/](http://localhost:8081/) (utilisateur `root` / mot de passe `root`, base `library`).

Tant que les méthodes du domaine ne sont pas complétées, tu verras une page d’erreur PHP avec le message du `TODO` — **c’est normal**.

### 1.1 — En cas de souci

- Port `8080` déjà pris (autre TP) : modifie le mapping dans `docker-compose.yml`.
- Base vide / tables manquantes : supprime le dossier local `db-data/` (données Docker) puis `docker compose up -d` pour réimporter `init.sql` (**efface les données locales**).

---

## 2 — Comprendre le code fourni (lecture guidée)

Avant de coder, parcours le flux **dans l’ordre** ci-dessous. Dans les fichiers PHP, cherche les blocs de commentaire **`REF-xx`** : ils reprennent les mêmes idées que cette section.

### 2.1 — D’où part la requête ?

1. **[public/index.php](public/index.php)** — **`REF-01`** (autoload) et **`REF-02`** (connexion PDO, instanciation des repositories et du contrôleur).
2. **`REF-03`** — routage minimal : pas de `?id=` → catalogue ; sinon → fiche livre.

### 2.2 — Autoload : pas de liste interminable de `require`

**`REF-01`** : quand le code fait `new BookRepository(...)`, PHP doit connaître la classe. Ici, `spl_autoload_register` charge **automatiquement** le fichier dont le chemin correspond au namespace `App\…` (équivalent propre à une série de `require`).

### 2.3 — Comment les données arrivent jusqu’au HTML ?

Chaîne type **fiche livre** :

```text
navigateur (GET ?id=1)
  → index.php crée PDO + repositories + BookController
  → BookController::show($id)
  → BookRepository::findById → PDO → une ligne SQL (tableau associatif)
  → Book::fromRow($row)   ← hydratation : tableau → objet métier
  → LoanRepository::findByBookId → plusieurs Loan via Loan::fromRow
  → require de la vue : les variables ($book, $loans, $title…) sont celles du scope courant
  → layout.php affiche $content (HTML produit par la vue)
```

**`REF-04`** (dans `BookController`) : le rôle de **`require`** sur les fichiers de vue — ce n’est pas un « appel de fonction » qui reçoit des arguments : le fichier inclus **s’exécute dans le même scope** que `index()` ou `show()`. D’où les commentaires `@var` en tête de `home.php` / `book_show.php` : ils documentent **quelles variables existent déjà** quand la vue est incluse.

**`REF-05`** (dans `home.php`) : rappel concret côté vue.

### 2.4 — Où s’arrête « l’infrastructure » ?

3. **[src/Infrastructure/Repositories/BookRepository.php](src/Infrastructure/Repositories/BookRepository.php)** — SQL + `fetch` ; la dernière étape est **`Book::fromRow($row)`** (le repository ne fabrique pas le livre à la main champ par champ : il délègue au domaine).
4. **[src/Domain/Book.php](src/Domain/Book.php)** — **aucun** `PDO` ni SQL ici : uniquement des objets et des règles métier (une fois tes exercices faits).

### 2.5 — Fichiers de lecture conseillée (ordre)

| Ordre | Fichier | Repère dans le code |
| ----- | ------- | ------------------- |
| 1 | [public/index.php](public/index.php) | `REF-01`, `REF-02`, `REF-03` |
| 2 | [src/Http/Controllers/BookController.php](src/Http/Controllers/BookController.php) | `REF-04` |
| 3 | [src/View/home.php](src/View/home.php) | `REF-05` (vue catalogue) |
| 4 | [src/View/book_show.php](src/View/book_show.php) | même idée : vue fiche, incluse par `show()` |
| 5 | [src/Infrastructure/Repositories/BookRepository.php](src/Infrastructure/Repositories/BookRepository.php) | commentaire d’en-tête (SQL → `fromRow`) |
| 6 | [src/Domain/Book.php](src/Domain/Book.php) | exercices **1** et **2** |

**Critère :** tu peux expliquer à l’oral pourquoi le domaine ne doit pas contenir de `PDO` ni de SQL, et ce que fait un `require` de vue dans le contrôleur.

---

## 3 — Exercices (ordre à respecter)

Les numéros **Ex. 1 à 4** correspondent aux messages d’erreur `TP ex.…` dans le squelette.

### Ex. 1 — `Book::fromRow()` (~20 min)

**Fichier :** [src/Domain/Book.php](src/Domain/Book.php)

**Objectif :** transformer une **ligne PDO** (`array` associatif) en instance de `Book`.

**Clés attendues** (noms de colonnes SQL) : `id`, `title`, `author`, `isbn`, `publication_year`, `available_copies`.

**Contraintes :**

- Casts **explicites** `(int)` / `(string)` (MySQL renvoie souvent des chaînes).
- Pas de `PDO` dans cette classe.

**Pourquoi en premier ?** Tant que `fromRow()` n’existe pas, le repository ne peut pas construire de `Book` : ni la liste du catalogue ni la fiche ne s’affichent.

**Test :** la page d’accueil charge (tu peux encore avoir une erreur sur **Ex. 2** tant que `isAvailable()` n’est pas fait).

---

### Ex. 2 — `Book::isAvailable()` (~10 min)

**Fichier :** [src/Domain/Book.php](src/Domain/Book.php)

**Règle métier :** un livre est disponible à l’emprunt s’il reste **au moins un exemplaire** libre (`available_copies > 0`).

**Tests :**

- Livre `id=1` → disponible.
- Livre `id=2` → **non** disponible (0 exemplaire dans la base de démo).

---

### Ex. 3 — Classe `Borrower` (~25 min)

**Fichier :** [src/Domain/Borrower.php](src/Domain/Borrower.php)

**Objectif :** modéliser la table `borrowers` : `id`, `name`, `email`.

**À fournir :**

- Propriétés privées + constructeur.
- Accesseurs `id()`, `name()`, `email()`.
- `Borrower::fromRow(array $row)` avec les clés **`id`**, **`name`**, **`email`** (tu pourras réutiliser ce tableau depuis `Loan::fromRow()` à l’**Ex. 4**).

---

### Ex. 4 — `Loan` : règles métier puis hydratation (~25 min)

**Fichier :** [src/Domain/Loan.php](src/Domain/Loan.php)

#### 4.1 — Règles métier

- `isReturned()` : `true` si `returned_at` est renseigné (non `null`).
- `isLate(DateTimeImmutable $today)` : **uniquement si non rendu**, en retard si `today` est **strictement après** la date d’échéance = emprunt + **`Loan::MAX_BORROW_DAYS`** jours.  
  Utilise `DateTimeImmutable::modify()` (ex. `'+21 days'`).

#### 4.2 — `Loan::fromRow()`

**Clés du tableau** (alias SQL du repository) :

| Clé                 | Rôle                         |
| ------------------- | ---------------------------- |
| `loan_id`           | id du prêt                   |
| `loan_book_id`      | id du livre                  |
| `loan_borrowed_at`  | datetime emprunt             |
| `loan_returned_at`  | datetime retour ou absent    |
| `borrower_id`       | id emprunteur                |
| `borrower_name`     | nom                          |
| `borrower_email`    | email                        |

**Dates :** `DateTimeImmutable::createFromFormat('Y-m-d H:i:s', ...)` ; pour `loan_returned_at` vide / `null`, garde `returnedAt` à `null`.

**Emprunteur :** construis un `Borrower` via `Borrower::fromRow([...])` avec les trois clés `id`, `name`, `email`.

**Tests (données de démo) :**

- Livre `1` : un prêt **rendu**, un prêt **en cours** (non rendu) — selon la date « aujourd’hui », le second peut apparaître **en retard** si > 21 jours après `2025-02-01` (c’est voulu pour tester `isLate`).

---

## 4 — Comportement attendu (récap)

| Zone            | État dans le squelette              |
| --------------- | ----------------------------------- |
| Docker / SQL    | Fourni                              |
| `public/`       | Fourni                              |
| Contrôleur      | Fourni                              |
| Repositories    | Fourni (SQL + appels `fromRow`)     |
| Vues            | Fournies                            |
| `src/Domain/*`  | **À compléter** (sujet du TP)       |

---

## 5 — Index des liens utiles

- [PDO](https://www.php.net/manual/fr/book.pdo.php) · [`prepare` / `execute`](https://www.php.net/manual/fr/pdo.prepare.php) · [`FETCH_ASSOC`](https://www.php.net/manual/fr/pdostatement.fetch.php)
- [`DateTimeImmutable`](https://www.php.net/manual/fr/class.datetimeimmutable.php)
- [Docker Compose — logs](https://docs.docker.com/reference/cli/docker/compose/logs/)

Bonne pratique : garde le **domaine** sans dépendance à la couche HTTP ou SQL — comme dans le cours 7.
