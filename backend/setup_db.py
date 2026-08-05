import os
import psycopg2
from argon2 import PasswordHasher

def setup():
    # 1. Connect to default postgres DB to create placement_pro database if it doesn't exist
    conn = psycopg2.connect(
        dbname="postgres",
        user="postgres",
        password="postgres",
        host="localhost",
        port=5432
    )
    conn.autocommit = True
    cur = conn.cursor()
    
    cur.execute("SELECT 1 FROM pg_database WHERE datname='placement_pro'")
    exists = cur.fetchone()
    if not exists:
        print("Database 'placement_pro' does not exist. Creating it...")
        cur.execute("CREATE DATABASE placement_pro")
    else:
        print("Database 'placement_pro' already exists.")
        
    cur.close()
    conn.close()

    # 2. Connect to placement_pro database
    conn = psycopg2.connect(
        dbname="placement_pro",
        user="postgres",
        password="postgres",
        host="localhost",
        port=5432
    )
    cur = conn.cursor()
    
    # 3. Read and execute schema.sql
    schema_path = os.path.join(os.path.dirname(__file__), "..", "schema.sql")
    print(f"Reading schema from {schema_path}...")
    with open(schema_path, "r", encoding="utf-8") as f:
        schema_sql = f.read()
        
    print("Executing schema.sql...")
    cur.execute(schema_sql)
    conn.commit()
    print("Schema executed successfully.")

    # 4. Check if admin user exists and seed if not
    cur.execute("SELECT 1 FROM users WHERE email='admin@college.edu'")
    admin_exists = cur.fetchone()
    if not admin_exists:
        print("Seeding default admin user...")
        ph = PasswordHasher()
        hashed_password = ph.hash("admin123")
        cur.execute(
            "INSERT INTO users (name, email, password_hash, role) VALUES (%s, %s, %s, %s)",
            ("Coordinator", "admin@college.edu", hashed_password, "admin")
        )
        conn.commit()
        print("Admin user seeded: email='admin@college.edu', password='admin123'")
    else:
        print("Admin user already exists.")
        
    cur.close()
    conn.close()
    print("Database setup complete.")

if __name__ == "__main__":
    setup()
