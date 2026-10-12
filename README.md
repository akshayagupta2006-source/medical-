# Jajal Medical — Medical Case Management System

A robust, beginner-friendly Medical Case Management web application built with **Laravel 12**, **MySQL 8.0**, **Laravel Blade**, and **Laravel Sanctum**.

Developed as part of the Software Developer Intern Practical Assignment for **Jajal Medical**.

---

## 1. Project Overview

The **Medical Case Management System** is designed for surgical and orthopedic device teams to manage implant planning, patient cases, and review workflows. It enforces strict Role-Based Access Control (RBAC):
- **Admin**: Full authority to create, view, edit, update clinical status, and delete cases.
- **Viewer**: Read-only clinical access to inspect case details and case list. Restricted actions are blocked at both web and API levels with **HTTP 403 Forbidden**.

Patient confidentiality is maintained through anonymous **dummy patient references** (e.g., `PAT-8492`), without storing identifiable patient names.

---

## 2. Technology Stack

- **Backend Framework:** Laravel 12 (PHP 8.2+)
- **Database Engine:** MySQL 8.0+ (with SQLite in-memory testing capability)
- **Frontend / Templating:** Laravel Blade with custom responsive CSS
- **API Token Authentication:** Laravel Sanctum (Bearer Token)
- **Testing Framework:** PHPUnit 11 / Laravel Test Runner
- **Version Control:** Git

---

## 3. Dummy Login Credentials

As required by the assignment, test credentials for seeded accounts are documented here:

| Role | Name | Email | Password | Permissions |
| :--- | :--- | :--- | :--- | :--- |
| **Admin** | System Administrator | `admin@jajalmedical.com` | `Password123!` | Create, View, Edit, Status Change, Delete cases |
| **Viewer** | Clinical Viewer | `viewer@jajalmedical.com` | `Password123!` | View cases and details only (Delete/Edit blocked with 403) |

*Tip: On the web login screen, one-click autofill buttons are provided for both roles to simplify practical evaluation.*

---

## 4. Local Installation & Setup

### Prerequisites
1. **PHP 8.2+** with extensions enabled: `pdo_mysql`, `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `curl`, `fileinfo`, `bcmath`.
2. **Composer 2.x**
3. **MySQL Server 8.0+**
4. **Git**

### Step-by-Step Setup Instructions

```bash
# 1. Clone the repository (or navigate to project folder)
cd medical

# 2. Copy the environment configuration file
cp .env.example .env

# 3. Configure your database credentials in .env:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=medical_cases
# DB_USERNAME=root
# DB_PASSWORD=your_mysql_password

# 4. Install PHP dependencies
composer install

# 5. Generate application encryption key
php artisan key:generate

# 6. Create the MySQL database if not exists
# mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS medical_cases;"

# 7. Run database migrations and seeders (Creates Admin, Viewer, and 26 dummy cases)
php artisan migrate:fresh --seed

# 8. Start the local development web server
php artisan serve
```

The application will be live at: **`http://127.0.0.1:8000`**

---

## 5. Running Automated Feature Tests

The application includes an extensive feature and security test suite testing authentication, authorization policies, validation rules, duplicate prevention, and role restrictions.

Run tests using either:

```bash
php artisan test
```

Or directly via PHPUnit:

```bash
./vendor/bin/phpunit
```

### Verified Test Suite (17 Tests, 68 Assertions — 100% Passing):
1. `valid web login succeeds`
2. `valid api login succeeds and returns sanctum token`
3. `invalid web login is rejected`
4. `invalid api login is rejected with 401`
5. `web logout succeeds`
6. `api logout succeeds`
7. `guests cannot access protected case web pages`
8. `guests cannot access protected case apis`
9. `admin can create a case via web`
10. `admin can create a case via api`
11. `duplicate case number is rejected with 422`
12. `viewer cannot delete a case via web and receives 403`
13. `viewer cannot delete a case via api and receives 403`
14. `viewer cannot create a case via api and receives 403`
15. `case update ignores current case id for unique case number`
16. `application root redirects to dashboard`
17. `unit test baseline assertion`

---

## 6. REST API Endpoints Specification

All protected endpoints require an `Authorization: Bearer <token>` header obtained from `/api/login`.
API responses adhere to a consistent JSON structure: `{ "success": boolean, "message": string, "data": mixed }`.

| Method | Endpoint | Access Level | Description | Status Codes |
| :--- | :--- | :--- | :--- | :--- |
| `POST` | `/api/login` | Public | Authenticate user and receive Sanctum Bearer token | `200`, `401`, `422` |
| `POST` | `/api/logout` | Protected | Invalidate current bearer token | `200`, `401` |
| `GET` | `/api/cases` | Admin & Viewer | List paginated cases (query params: `page`, `search`, `status`) | `200`, `401`, `403` |
| `POST` | `/api/cases` | Admin Only | Create a new medical case (auto-assigns `created_by`) | `201`, `401`, `403`, `422` |
| `GET` | `/api/cases/{id}` | Admin & Viewer | Retrieve details for a specific case by ID | `200`, `401`, `403`, `404` |
| `PUT` | `/api/cases/{id}` | Admin Only | Update an existing medical case | `200`, `401`, `403`, `404`, `422` |
| `DELETE`| `/api/cases/{id}` | Admin Only | Delete an existing medical case (Viewer blocked with 403) | `200`, `401`, `403`, `404` |

### Sample JSON Request Body (`POST /api/cases`):
```json
{
  "case_number": "CAS-30041",
  "patient_reference": "PAT-7721",
  "surgeon_name": "Dr. Eleanor Vance",
  "implant_type": "Titanium Hip Stem 12mm",
  "status": "Planning",
  "priority": "High",
  "surgery_date": "2026-11-20"
}
```

### Sample JSON Response (`201 Created`):
```json
{
  "success": true,
  "message": "Medical case created successfully.",
  "data": {
    "id": 27,
    "case_number": "CAS-30041",
    "patient_reference": "PAT-7721",
    "surgeon_name": "Dr. Eleanor Vance",
    "implant_type": "Titanium Hip Stem 12mm",
    "status": "Planning",
    "priority": "High",
    "surgery_date": "2026-11-20T00:00:00.000000Z",
    "created_by": 1,
    "created_at": "2026-10-12T17:35:00.000000Z",
    "updated_at": "2026-10-12T17:35:00.000000Z"
  }
}
```

A complete Postman collection is exported and available at: [`docs/postman_collection.json`](docs/postman_collection.json).

---

## 7. Assumptions

1. **PHP Model Naming (`MedicalCase`):** In PHP, `case` is a reserved keyword (used in `switch` and `match` expressions). Consequently, defining `class Case` causes a `PHP Parse error`. Following standard Laravel architecture, the model is named `MedicalCase`, explicitly mapped to the `cases` table (`protected $table = 'cases';`).
2. **Patient Data Privacy:** Per instructions, only synthetic dummy identifiers (e.g. `PAT-XXXX`) are accepted and stored to prevent exposure of identifiable patient information.
3. **Session vs Token Authentication:** Web browser interactions use traditional cookie/session CSRF-protected authentication, while external API clients use stateless Laravel Sanctum Bearer tokens.

---

## 8. Known Limitations

1. **Email Verification:** Email verification is not required for local intern evaluation; user accounts are activated immediately upon seeding.
2. **File Attachments:** Medical image uploads (such as DICOM or CT scans) are not in the primary assignment scope and can be added as an extension.
3. **Audit Log:** Case histories track `created_at`, `updated_at`, and `created_by`; granular audit diffs per field change can be added via an event listener if desired.

---

## 9. Architectural Documentation

- **Database Structure:** See [`docs/database.md`](docs/database.md)
- **Requirements Traceability:** See [`docs/traceability.md`](docs/traceability.md)
- **Postman API Collection:** See [`docs/postman_collection.json`](docs/postman_collection.json)
