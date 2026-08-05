from flask import Blueprint, request, jsonify
from database import get_cursor, commit

companies_bp = Blueprint("companies", __name__)
from routes.auth import token_required


@companies_bp.route("", methods=["GET"])
@token_required()
def list_companies():
    cur = get_cursor()
    cur.execute(
        """SELECT c.*,
                  (SELECT COUNT(*) FROM placements p WHERE p.company_id = c.id) AS application_count,
                  (SELECT COUNT(*) FROM placements p WHERE p.company_id = c.id AND p.current_stage IN ('selected','joined')) AS selected_count
           FROM companies c ORDER BY c.visit_date DESC NULLS LAST"""
    )
    return jsonify(cur.fetchall())


@companies_bp.route("/<int:company_id>", methods=["GET"])
@token_required()
def get_company(company_id):
    cur = get_cursor()
    cur.execute("SELECT * FROM companies WHERE id = %s", (company_id,))
    company = cur.fetchone()
    if not company:
        return jsonify({"error": "Not found"}), 404

    cur.execute(
        """SELECT s.id, s.register_number, s.name, s.email, p.package_amount, p.offer_status, p.current_stage
           FROM placements p JOIN students s ON s.id = p.student_id
           WHERE p.company_id = %s ORDER BY s.name""",
        (company_id,),
    )
    company["selected_students"] = cur.fetchall()
    return jsonify(company)


@companies_bp.route("", methods=["POST"])
@token_required(roles=["hr", "faculty", "admin"])
def create_company():
    data = request.get_json(force=True) or {}
    if not data.get("name"):
        return jsonify({"error": "name required"}), 400
    fields = {
        "name": data.get("name"), "industry": data.get("industry"), "state": data.get("state"),
        "location": data.get("location"), "hr_name": data.get("hr_name"), "hr_email": data.get("hr_email"),
        "hr_contact_number": data.get("hr_contact_number"), "visit_date": data.get("visit_date"),
        "package_amount": data.get("package_amount"), "min_package": data.get("min_package"),
        "max_package": data.get("max_package"), "avg_package": data.get("avg_package"),
        "eligible_departments": data.get("eligible_departments"), "min_cgpa": data.get("min_cgpa"),
        "allowed_backlogs": data.get("allowed_backlogs", 0), "hiring_count": data.get("hiring_count", 0),
        "logo_url": data.get("logo_url"),
    }
    cols = ", ".join(fields.keys())
    placeholders = ", ".join(["%s"] * len(fields))
    cur = get_cursor()
    cur.execute(f"INSERT INTO companies ({cols}) VALUES ({placeholders}) RETURNING id", list(fields.values()))
    new_id = cur.fetchone()["id"]
    commit()
    return jsonify({"id": new_id}), 201


@companies_bp.route("/<int:company_id>", methods=["PUT"])
@token_required(roles=["hr", "faculty", "admin"])
def update_company(company_id):
    data = request.get_json(force=True) or {}
    allowed = {
        "name", "industry", "state", "location", "hr_name", "hr_email", "hr_contact_number",
        "visit_date", "package_amount", "min_package", "max_package", "avg_package",
        "eligible_departments", "min_cgpa", "allowed_backlogs", "hiring_count", "logo_url",
    }
    fields = {k: v for k, v in data.items() if k in allowed}
    if not fields:
        return jsonify({"error": "No valid fields"}), 400
    set_clause = ", ".join(f"{k} = %s" for k in fields)
    cur = get_cursor()
    cur.execute(
        f"UPDATE companies SET {set_clause}, updated_at = NOW() WHERE id = %s RETURNING id",
        list(fields.values()) + [company_id],
    )
    if not cur.fetchone():
        return jsonify({"error": "Not found"}), 404
    commit()
    return jsonify({"updated": True})


@companies_bp.route("/<int:company_id>", methods=["DELETE"])
@token_required(roles=["admin"])
def delete_company(company_id):
    cur = get_cursor()
    cur.execute("DELETE FROM companies WHERE id = %s RETURNING id", (company_id,))
    if not cur.fetchone():
        return jsonify({"error": "Not found"}), 404
    commit()
    return jsonify({"deleted": True})
