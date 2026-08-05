-- ============================================================
-- Placement Pro — PostgreSQL Schema
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    id              SERIAL PRIMARY KEY,
    name            VARCHAR(120) NOT NULL,
    email           VARCHAR(150) UNIQUE NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    role            VARCHAR(20) NOT NULL DEFAULT 'faculty', -- 'hr' | 'faculty' | 'admin'
    is_active       BOOLEAN NOT NULL DEFAULT TRUE,
    created_at      TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS departments (
    id              SERIAL PRIMARY KEY,
    name            VARCHAR(150) UNIQUE NOT NULL   -- e.g. 'BCA', 'BBA', 'BBA - Hospitality & Hotel Management'
);

CREATE TABLE IF NOT EXISTS courses (
    id              SERIAL PRIMARY KEY,
    department_id   INTEGER REFERENCES departments(id) ON DELETE CASCADE,
    name            VARCHAR(150) NOT NULL,          -- e.g. 'B.Sc - Computer Science'
    stream          VARCHAR(100)                    -- e.g. 'Computer Science', 'Physics', 'Chemistry'
);

CREATE TABLE IF NOT EXISTS students (
    id                  SERIAL PRIMARY KEY,
    register_number     VARCHAR(50) UNIQUE NOT NULL,
    name                VARCHAR(150) NOT NULL,
    department_id       INTEGER REFERENCES departments(id),
    course_id           INTEGER REFERENCES courses(id),
    section             VARCHAR(10),
    academic_year       VARCHAR(20),
    gender              VARCHAR(20),
    date_of_birth       DATE,
    mobile_number       VARCHAR(20),
    email               VARCHAR(150),
    address             TEXT,
    cgpa                NUMERIC(4,2),
    percentage          NUMERIC(5,2),
    backlogs            INTEGER DEFAULT 0,
    skills              TEXT,                       -- comma-separated
    resume_link          TEXT,
    placement_status    VARCHAR(30) DEFAULT 'not_placed', -- not_placed | applied | selected | joined
    eligible_status      BOOLEAN DEFAULT TRUE,
    created_at          TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS companies (
    id                    SERIAL PRIMARY KEY,
    name                  VARCHAR(150) NOT NULL,
    industry              VARCHAR(100),
    state                 VARCHAR(100),
    location              VARCHAR(150),
    hr_name               VARCHAR(150),
    hr_email              VARCHAR(150),
    hr_contact_number     VARCHAR(20),
    visit_date            DATE,
    package_amount        NUMERIC(12,2),
    min_package           NUMERIC(12,2),
    max_package           NUMERIC(12,2),
    avg_package           NUMERIC(12,2),
    eligible_departments  TEXT,                     -- comma-separated department names
    min_cgpa              NUMERIC(4,2),
    allowed_backlogs      INTEGER DEFAULT 0,
    hiring_count          INTEGER DEFAULT 0,
    logo_url              TEXT,
    created_at            TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at            TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE (name, visit_date)
);

-- Links a student to a company they were selected by / applied to
CREATE TABLE IF NOT EXISTS placements (
    id                  SERIAL PRIMARY KEY,
    student_id          INTEGER REFERENCES students(id) ON DELETE CASCADE,
    company_id          INTEGER REFERENCES companies(id) ON DELETE CASCADE,
    package_amount      NUMERIC(12,2),
    selection_date       DATE,
    offer_status         VARCHAR(30) DEFAULT 'pending', -- pending | offered | accepted | declined
    offer_letter_date    DATE,
    joining_date         DATE,
    current_stage        VARCHAR(40) DEFAULT 'registered',
    created_at          TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at          TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE (student_id, company_id)
);

-- Placement pipeline timeline events per student/company
CREATE TABLE IF NOT EXISTS pipeline_stages (
    id              SERIAL PRIMARY KEY,
    placement_id    INTEGER REFERENCES placements(id) ON DELETE CASCADE,
    stage           VARCHAR(40) NOT NULL,   -- registration|eligibility_verification|applied|aptitude_test|
                                             -- technical_test|group_discussion|hr_interview|selected|
                                             -- offer_letter_received|joined_company
    status          VARCHAR(20) DEFAULT 'pending', -- pending | in_progress | completed | failed
    stage_date      DATE,
    remarks         TEXT,
    created_at      TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS import_history (
    id              SERIAL PRIMARY KEY,
    imported_by     INTEGER REFERENCES users(id),
    import_type     VARCHAR(20) NOT NULL,   -- 'student' | 'company'
    file_name       VARCHAR(255),
    total_rows      INTEGER DEFAULT 0,
    inserted_count  INTEGER DEFAULT 0,
    updated_count   INTEGER DEFAULT 0,
    skipped_count   INTEGER DEFAULT 0,
    error_count     INTEGER DEFAULT 0,
    error_log       JSONB,
    created_at      TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id              SERIAL PRIMARY KEY,
    user_id         INTEGER REFERENCES users(id),
    action          VARCHAR(100) NOT NULL,
    entity_type     VARCHAR(50),
    entity_id       INTEGER,
    details         JSONB,
    ip_address      VARCHAR(50),
    created_at      TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_students_register_number ON students(register_number);
CREATE INDEX IF NOT EXISTS idx_students_department ON students(department_id);
CREATE INDEX IF NOT EXISTS idx_students_placement_status ON students(placement_status);
CREATE INDEX IF NOT EXISTS idx_companies_name ON companies(name);
CREATE INDEX IF NOT EXISTS idx_placements_student ON placements(student_id);
CREATE INDEX IF NOT EXISTS idx_placements_company ON placements(company_id);

-- Seed departments/courses
INSERT INTO departments (name) VALUES
    ('BCA'), ('BBA'), ('BBA - Hospitality & Hotel Management'), ('B.Com'), ('B.Sc')
ON CONFLICT (name) DO NOTHING;

INSERT INTO courses (department_id, name, stream)
SELECT d.id, 'B.Sc - Computer Science', 'Computer Science' FROM departments d WHERE d.name = 'B.Sc'
ON CONFLICT DO NOTHING;
INSERT INTO courses (department_id, name, stream)
SELECT d.id, 'B.Sc - Physics', 'Physics' FROM departments d WHERE d.name = 'B.Sc'
ON CONFLICT DO NOTHING;
INSERT INTO courses (department_id, name, stream)
SELECT d.id, 'B.Sc - Chemistry', 'Chemistry' FROM departments d WHERE d.name = 'B.Sc'
ON CONFLICT DO NOTHING;
