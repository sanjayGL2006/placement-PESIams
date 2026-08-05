# Placement Pro — Smart College Placement Management System

[![Netlify Status](https://api.netlify.com/api/v1/badges/031678d3-d0d3-40d9-ad1a-938883908178/deploy-status)](https://app.netlify.com/projects/pbpesiams/deploys)

## Stack
- **Frontend:** PHP + Bootstrap 5 + vanilla JS (AJAX to the API)
- **Backend:** Python Flask REST API
- **Database:** PostgreSQL

## Structure
```
placement-pro/
├── schema.sql                  # PostgreSQL schema (run this first)
├── backend/
│   ├── app.py                  # Flask app factory
│   ├── database.py             # connection pool
│   ├── import_utils.py         # file parsing, column-mapping, validation
│   ├── requirements.txt
│   └── routes/
│       ├── auth.py             # login, JWT, role-based decorator
│       ├── students.py         # student directory CRUD
│       ├── companies.py        # company directory CRUD
│       ├── imports.py          # smart import: preview + commit
│       ├── dashboard.py        # live stats
│       └── reports.py          # CSV/Excel/PDF report generation
└── frontend/
    ├── config.php               # API_BASE + session helpers
    ├── login.php / session_store.php / logout.php
    ├── dashboard.php            # auto-refreshing stat cards
    ├── students.php             # filterable directory
    ├── companies.php
    ├── import.php               # drag-drop → preview → confirm import
    ├── reports.php / download.php
    ├── partials/nav.php
    └── assets/{css,js}
```

## Setup

### 1. Database
```bash
createdb placement_pro
psql placement_pro < schema.sql
```

### 2. Backend
```bash
cd backend
python3 -m venv venv && source venv/bin/activate
pip install -r requirements.txt

export DATABASE_URL="dbname=placement_pro user=postgres password=postgres host=localhost"
export SECRET_KEY="a-long-random-string"
export JWT_SECRET="another-long-random-string"
export CORS_ORIGINS="http://localhost:8000"   # your PHP frontend origin

python3 app.py       # dev server on :5000
# or in production: gunicorn -w 4 "app:create_app()"
```

Create your first admin user directly in the DB (registration requires an existing admin token):
```sql
-- password hash generated with argon2-cffi; from Python:
-- from argon2 import PasswordHasher; PasswordHasher().hash("yourpassword")
INSERT INTO users (name, email, password_hash, role)
VALUES ('Coordinator', 'admin@college.edu', '<paste-hash-here>', 'admin');
```

### 3. Frontend
```bash
cd frontend
export PLACEMENT_API_BASE="http://localhost:5000/api"
php -S localhost:8000
```
Visit `http://localhost:8000/login.php`.

## How the Smart Import works
1. Coordinator uploads a file (`.xlsx`, `.xls`, `.csv`, `.docx`, `.pdf`) on the **Import Data** page.
2. `POST /api/imports/students/preview` (or `/companies/preview`) parses the file, fuzzy-matches
   column headers against known field names (see `STUDENT_FIELD_ALIASES` / `COMPANY_FIELD_ALIASES`
   in `import_utils.py`), validates each row, and flags duplicates already in the database.
3. The coordinator reviews the editable preview table in the browser — every row can be switched
   between Insert / Update / Skip, and validation errors are highlighted inline.
4. `POST /api/imports/students/commit` (or `/companies/commit`) writes the approved rows.
   For companies, a `students_selected` column (comma/semicolon/newline separated register numbers)
   automatically creates `placements` + `pipeline_stages` rows and updates each matched student's
   `placement_status` — this is the "automatic student mapping" from the spec.
5. Every import run is logged to `import_history` (inserted/updated/skipped/error counts + an error log).

## What's implemented vs. stubbed
**Implemented:** schema for all entities in the spec, JWT auth with Argon2id hashing and
role-based access (`hr` / `faculty` / `admin`), the full import→preview→commit pipeline for
students and companies, auto student-to-company mapping, placement pipeline timeline, live
dashboard stats, student directory with filters/pagination, company directory, and CSV/Excel/PDF
report downloads.

**Left as follow-up work** (flag if you want these built next):
- TOTP 2FA and audit-log write calls on every mutating action (table exists, not yet wired up)
- Database backup/restore tooling
- Student self-service view / resume upload endpoint
- Company logo upload (currently a `logo_url` text field)
- The Android-ready API is already REST/JSON, so no changes needed there — just point a client at
  the same `/api/*` routes.
