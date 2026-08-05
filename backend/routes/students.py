from flask import Blueprint, request, jsonify
from database import get_cursor, commit
from routes.auth import token_required

students_bp = Blueprint("students", __name__)


@students_bp.route("", methods=["GET"])
@token_required()
def list_students():
    dept = request.args.get("department")
    section = request.args.get("section")
    year = request.args.get("academic_year")
    status = request.args.get("placement_status")
    search = request.args.get("search")
    page = max(int(request.args.get("page", 1)), 1)
    per_page = min(int(request.args.get("per_page", 25)), 200)

    where, params = [], []
    if dept:
        where.append("d.name = %s"); params.append(dept)
    if section:
        sec_letter = section[-1] if section else "A"
        where.append("(s.section ILIKE %s OR RIGHT(TRIM(s.section), 1) ILIKE %s)")
        params.extend([section, sec_letter])
    if year:
        where.append("s.academic_year = %s"); params.append(year)
    if status:
        where.append("s.placement_status = %s"); params.append(status)
    if search:
        where.append("(s.name ILIKE %s OR s.register_number ILIKE %s)")
        params.extend([f"%{search}%", f"%{search}%"])

    where_clause = f"WHERE {' AND '.join(where)}" if where else ""
    cur = get_cursor()
    cur.execute(f"SELECT COUNT(*) AS total FROM students s LEFT JOIN departments d ON d.id = s.department_id {where_clause}", params)
    total = cur.fetchone()["total"]

    cur.execute(
        f"""SELECT s.*, d.name AS department_name, c.name AS course_name,
                   comp.name AS company_name
            FROM students s
            LEFT JOIN departments d ON d.id = s.department_id
            LEFT JOIN courses c ON c.id = s.course_id
            LEFT JOIN placements p ON p.student_id = s.id AND p.current_stage IN ('selected','joined')
            LEFT JOIN companies comp ON comp.id = p.company_id
            {where_clause}
            ORDER BY s.name
            LIMIT %s OFFSET %s""",
        params + [per_page, (page - 1) * per_page],
    )
    rows = cur.fetchall()
    return jsonify({"total": total, "page": page, "per_page": per_page, "students": rows})


@students_bp.route("/<int:student_id>", methods=["GET"])
@token_required()
def get_student(student_id):
    cur = get_cursor()
    cur.execute(
        """SELECT s.*, d.name AS department_name, c.name AS course_name
           FROM students s
           LEFT JOIN departments d ON d.id = s.department_id
           LEFT JOIN courses c ON c.id = s.course_id
           WHERE s.id = %s""",
        (student_id,),
    )
    student = cur.fetchone()
    if not student:
        return jsonify({"error": "Not found"}), 404

    cur.execute(
        """SELECT p.*, comp.name AS company_name FROM placements p
           JOIN companies comp ON comp.id = p.company_id
           WHERE p.student_id = %s ORDER BY p.created_at DESC""",
        (student_id,),
    )
    placements = cur.fetchall()

    for p in placements:
        cur.execute("SELECT * FROM pipeline_stages WHERE placement_id = %s ORDER BY created_at", (p["id"],))
        p["timeline"] = cur.fetchall()

    student["placements"] = placements
    return jsonify(student)


@students_bp.route("/<int:student_id>", methods=["PUT"])
@token_required(roles=["hr", "faculty", "admin"])
def update_student(student_id):
    data = request.get_json(force=True) or {}
    allowed = {
        "name", "section", "academic_year", "gender", "mobile_number", "email", "address",
        "cgpa", "percentage", "backlogs", "skills", "resume_link", "placement_status", "eligible_status",
    }
    fields = {k: v for k, v in data.items() if k in allowed}
    if not fields:
        return jsonify({"error": "No valid fields to update"}), 400

    set_clause = ", ".join(f"{k} = %s" for k in fields)
    cur = get_cursor()
    cur.execute(
        f"UPDATE students SET {set_clause}, updated_at = NOW() WHERE id = %s RETURNING id",
        list(fields.values()) + [student_id],
    )
    if not cur.fetchone():
        return jsonify({"error": "Not found"}), 404
    commit()
    return jsonify({"updated": True})


@students_bp.route("/<int:student_id>", methods=["DELETE"])
@token_required(roles=["admin"])
def delete_student(student_id):
    cur = get_cursor()
    cur.execute("DELETE FROM students WHERE id = %s RETURNING id", (student_id,))
    if not cur.fetchone():
        return jsonify({"error": "Not found"}), 404
    commit()
    return jsonify({"deleted": True})


@students_bp.route("/<int:student_id>/pipeline", methods=["POST"])
@token_required(roles=["hr", "faculty", "admin"])
def add_pipeline_stage(student_id):
    """Manually add/update a pipeline stage for a student's active placement."""
    data = request.get_json(force=True) or {}
    company_id = data.get("company_id")
    stage = data.get("stage")
    status = data.get("status", "completed")
    remarks = data.get("remarks")
    if not company_id or not stage:
        return jsonify({"error": "company_id and stage required"}), 400

    cur = get_cursor()
    cur.execute(
        "SELECT id FROM placements WHERE student_id = %s AND company_id = %s",
        (student_id, company_id),
    )
    row = cur.fetchone()
    if not row:
        cur.execute(
            "INSERT INTO placements (student_id, company_id, current_stage) VALUES (%s,%s,%s) RETURNING id",
            (student_id, company_id, stage),
        )
        row = cur.fetchone()
    placement_id = row["id"]

    cur.execute(
        "INSERT INTO pipeline_stages (placement_id, stage, status, stage_date, remarks) "
        "VALUES (%s,%s,%s, CURRENT_DATE, %s)",
        (placement_id, stage, status, remarks),
    )
    cur.execute(
        "UPDATE placements SET current_stage = %s, updated_at = NOW() WHERE id = %s",
        (stage, placement_id),
    )
    commit()
    return jsonify({"added": True, "placement_id": placement_id})
