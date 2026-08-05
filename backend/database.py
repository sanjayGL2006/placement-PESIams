import os
from flask import g
# pyrefly: ignore [untyped-import]
from psycopg2 import pool
# pyrefly: ignore [untyped-import]
from psycopg2.extras import RealDictCursor

_pool = None


def init_db_pool(app):
    global _pool
    dsn = os.environ.get(
        "DATABASE_URL",
        "dbname=placement_pro user=postgres password=postgres host=localhost port=5432",
    )
    try:
        _pool = pool.SimpleConnectionPool(1, 20, dsn)
        # Automatically create recycle_bin table
        conn = _pool.getconn()
        cur = conn.cursor()
        cur.execute("""
            CREATE TABLE IF NOT EXISTS recycle_bin (
                id SERIAL PRIMARY KEY,
                entity_type VARCHAR(50) NOT NULL,
                original_id INTEGER NOT NULL,
                name VARCHAR(150) NOT NULL,
                data JSONB NOT NULL,
                deleted_at TIMESTAMP DEFAULT NOW()
            );
        """)
        conn.commit()
        cur.close()
        _pool.putconn(conn)
    except Exception as e:
        print(f"Warning: Database pool initialization failed ({e}). App will run in offline mode.")
        _pool = None


def close_db_pool(exc=None):
    conn = g.pop("db_conn", None)
    if conn is not None:
        # pyrefly: ignore [missing-attribute]
        _pool.putconn(conn)


def get_conn():
    if "db_conn" not in g:
        # pyrefly: ignore [missing-attribute]
        g.db_conn = _pool.getconn()
    return g.db_conn


def get_cursor():
    return get_conn().cursor(cursor_factory=RealDictCursor)


def commit():
    get_conn().commit()


def rollback():
    get_conn().rollback()
