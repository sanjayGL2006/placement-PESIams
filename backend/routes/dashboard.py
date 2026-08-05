from flask import Blueprint, jsonify, request
from database import get_cursor
from routes.auth import token_required

dashboard_bp = Blueprint("dashboard", __name__)


@dashboard_bp.route("/stats", methods=["GET"])
@token_required()
def stats():
    cur = get_cursor()

    cur.execute("SELECT COUNT(*) AS n FROM students")
    total_students = cur.fetchone()["n"]

    cur.execute("SELECT COUNT(*) AS n FROM companies")
    total_companies = cur.fetchone()["n"]

    cur.execute("SELECT COUNT(*) AS n FROM students WHERE eligible_status = TRUE")
    eligible_students = cur.fetchone()["n"]

    cur.execute("SELECT COUNT(DISTINCT student_id) AS n FROM placements")
    applied_students = cur.fetchone()["n"]

    cur.execute(
        "SELECT COUNT(DISTINCT student_id) AS n FROM placements WHERE current_stage NOT IN ('registered')"
    )
    students_attended = cur.fetchone()["n"]

    cur.execute(
        "SELECT COUNT(*) AS n FROM students WHERE placement_status IN ('selected','joined')"
    )
    students_selected = cur.fetchone()["n"]

    cur.execute(
        "SELECT COUNT(*) AS n FROM placements WHERE offer_status = 'offered' OR offer_letter_date IS NOT NULL"
    )
    total_offer_letters = cur.fetchone()["n"]

    cur.execute("SELECT COUNT(*) AS n FROM students WHERE placement_status = 'joined'")
    students_joined = cur.fetchone()["n"]

    cur.execute(
        "SELECT MAX(package_amount) AS hi, MIN(package_amount) AS lo, AVG(package_amount) AS avg "
        "FROM placements WHERE package_amount IS NOT NULL"
    )
    pkg = cur.fetchone()

    placement_pct = round((students_selected / total_students) * 100, 2) if total_students else 0.0

    return jsonify({
        "total_students": total_students,
        "total_companies": total_companies,
        "eligible_students": eligible_students,
        "applied_students": applied_students,
        "students_attended": students_attended,
        "students_selected": students_selected,
        "placement_percentage": placement_pct,
        "highest_package": float(pkg["hi"]) if pkg["hi"] else 0,
        "average_package": round(float(pkg["avg"]), 2) if pkg["avg"] else 0,
        "minimum_package": float(pkg["lo"]) if pkg["lo"] else 0,
        "total_offer_letters": total_offer_letters,
        "students_joined": students_joined,
    })


@dashboard_bp.route("/filters", methods=["GET"])
@token_required()
def filter_options():
    cur = get_cursor()
    cur.execute("SELECT name FROM departments ORDER BY name")
    departments = [r["name"] for r in cur.fetchall()]
    cur.execute("SELECT DISTINCT section FROM students WHERE section IS NOT NULL ORDER BY section")
    sections = [r["section"] for r in cur.fetchall()]
    cur.execute("SELECT DISTINCT academic_year FROM students WHERE academic_year IS NOT NULL ORDER BY academic_year")
    years = [r["academic_year"] for r in cur.fetchall()]
    return jsonify({"departments": departments, "sections": sections, "academic_years": years})
