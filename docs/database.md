# Database Schema Documentation

This document describes the database architecture, schema design, table definitions, foreign keys, and indexes for the **Jajal Medical Case Management** application.

---

## Database Configuration

- **Database Engine:** MySQL 8.0+ (InnoDB)
- **Character Set:** `utf8mb4`
- **Collation:** `utf8mb4_unicode_ci`
- **Default Database Name:** `medical_cases`

---

## Entity Relationship Overview

```
+------------------+         1 : N         +-------------------+
|      users       | --------------------> |       cases       |
+------------------+                       +-------------------+
| id (PK)          |                       | id (PK)           |
| email (Unique)   |                       | case_number (UQ)  |
| role             |                       | created_by (FK)   |
+------------------+                       +-------------------+
```

- Each user has a `role` (`admin` or `viewer`).
- Each medical case belongs to a user through `created_by` foreign key referencing `users.id`.
- If a user is deleted, associated cases are handled according to `ON DELETE CASCADE`.

---

## Tables Specification

### 1. `users` Table

Stores authenticated users of the system, their credentials, and their system roles.

| Column Name | Data Type | Modifiers / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `AUTO_INCREMENT`, Primary Key | Unique user record identifier |
| `name` | `VARCHAR(255)` | `NOT NULL` | Full name of the user |
| `email` | `VARCHAR(255)` | `NOT NULL`, `UNIQUE` index | Unique login email address |
| `email_verified_at` | `TIMESTAMP` | `NULLABLE` | Timestamp when email was verified |
| `password` | `VARCHAR(255)` | `NOT NULL` | Bcrypt hashed password |
| `role` | `VARCHAR(255)` | `NOT NULL`, `DEFAULT 'viewer'` | Access control role (`admin` or `viewer`) |
| `remember_token` | `VARCHAR(100)` | `NULLABLE` | Web session remember token |
| `created_at` | `TIMESTAMP` | `NULLABLE` | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | Record last update timestamp |

**Indexes:**
- `PRIMARY KEY (id)`
- `UNIQUE INDEX users_email_unique (email)`

---

### 2. `cases` Table

Stores surgical and orthopedic case planning records, patient dummy references, surgeon assignments, implant requisitions, and clinical statuses.

| Column Name | Data Type | Modifiers / Constraints | Description |
| :--- | :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | `AUTO_INCREMENT`, Primary Key | Unique case identifier |
| `case_number` | `VARCHAR(50)` | `NOT NULL`, `UNIQUE` index | Unique business case code (e.g. `CAS-10001`) |
| `patient_reference` | `VARCHAR(100)` | `NOT NULL` | Dummy patient reference code (e.g. `PAT-8492`) |
| `surgeon_name` | `VARCHAR(100)` | `NOT NULL` | Name of the operating surgeon |
| `implant_type` | `VARCHAR(150)` | `NOT NULL` | Type and specification of the surgical implant |
| `status` | `ENUM` | `NOT NULL`, `DEFAULT 'Draft'`, `INDEX` | Workflow status: `Draft`, `Planning`, `In Review`, `Approved`, `Completed`, `Cancelled` |
| `priority` | `ENUM` | `NOT NULL`, `DEFAULT 'Medium'` | Case priority: `Low`, `Medium`, `High` |
| `surgery_date` | `DATE` | `NULLABLE` | Scheduled date of the surgery |
| `created_by` | `BIGINT UNSIGNED` | `NOT NULL`, Foreign Key | User ID who registered the case |
| `created_at` | `TIMESTAMP` | `NULLABLE` | Record creation timestamp |
| `updated_at` | `TIMESTAMP` | `NULLABLE` | Record last update timestamp |

**Indexes & Constraints:**
- `PRIMARY KEY (id)`
- `UNIQUE INDEX cases_case_number_unique (case_number)`: Guarantees case number uniqueness at the database level.
- `INDEX cases_status_index (status)`: Optimizes dashboard status counting and list filtering.
- `FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE`: Enforces referential integrity.

---

### 3. `personal_access_tokens` Table (Laravel Sanctum)

Manages API Bearer tokens issued to users upon successful authentication via `POST /api/login`.

| Column Name | Data Type | Description |
| :--- | :--- | :--- |
| `id` | `BIGINT UNSIGNED` | Primary Key |
| `tokenable_type` | `VARCHAR(255)` | Polymorphic entity class (`App\Models\User`) |
| `tokenable_id` | `BIGINT UNSIGNED` | Polymorphic entity ID (references `users.id`) |
| `name` | `VARCHAR(255)` | Friendly token descriptor (`api_token`) |
| `token` | `VARCHAR(64)` | SHA-256 hashed token string |
| `abilities` | `TEXT` | Token capabilities |
| `last_used_at` | `TIMESTAMP` | Last timestamp token was presented |
| `expires_at` | `TIMESTAMP` | Token expiration time |
| `created_at` | `TIMESTAMP` | Token creation time |
| `updated_at` | `TIMESTAMP` | Token update time |

---

### 4. Supporting System Tables

- `sessions`: Stores secure server-side HTTP session data.
- `cache` / `cache_locks`: Application caching table.
- `jobs` / `job_batches` / `failed_jobs`: Database queue handling.

---

## Model Relationships in Eloquent

### User Model (`App\Models\User`)
```php
public function cases(): HasMany
{
    return $this->hasMany(MedicalCase::class, 'created_by');
}
```

### MedicalCase Model (`App\Models\MedicalCase`)
```php
public function creator(): BelongsTo
{
    return $this->belongsTo(User::class, 'created_by');
}
```
*Note:* The model class is named `MedicalCase` because `case` is a reserved keyword in PHP syntax (`match`/`switch`). The model explicitly maps to the `cases` table via `protected $table = 'cases';`.
