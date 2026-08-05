import io
import pandas as pd
from flask import Blueprint, request, jsonify, send_file
from database import get_cursor
from routes.auth import token_required

reports_bp = Blueprint("reports", __name__)


def _students_df(filters):
    where, params = [], []
    if filters.get("department"):
        where.append("d.name = %s"); params.append(filters["department"])
    if filters.get("academic_year"):
        where.append("s.academic_year = %s"); params.append(filters["academic_year"])
    if filters.get("placement_status"):
        where.append("s.placement_status = %s"); params.append(filters["placement_status"])
    where_clause = f"WHERE {' AND '.join(where)}" if where else ""

    cur = get_cursor()
    cur.execute(
        f"""SELECT s.register_number, s.name, d.name AS department, s.section, s.academic_year,
                   s.cgpa, s.backlogs, s.placement_status, comp.name AS company_name, p.package_amount
            FROM students s
            LEFT JOIN departments d ON d.id = s.department_id
            LEFT JOIN placements p ON p.student_id = s.id AND p.current_stage IN ('selected','joined')
            LEFT JOIN companies comp ON comp.id = p.company_id
            {where_clause}
            ORDER BY s.name""",
        params,
    )
    return pd.DataFrame(cur.fetchall())


def _companies_df():
    cur = get_cursor()
    cur.execute(
        """SELECT c.name, c.industry, c.state, c.visit_date, c.package_amount,
                  (SELECT COUNT(*) FROM placements p WHERE p.company_id = c.id) AS applications,
                  (SELECT COUNT(*) FROM placements p WHERE p.company_id = c.id AND p.current_stage IN ('selected','joined')) AS selected
           FROM companies c ORDER BY c.visit_date DESC NULLS LAST"""
    )
    return pd.DataFrame(cur.fetchall())


def _df_to_response(df, fmt, base_name):
    if fmt == "csv":
        buf = io.StringIO()
        df.to_csv(buf, index=False)
        mem = io.BytesIO(buf.getvalue().encode("utf-8"))
        return send_file(mem, mimetype="text/csv", as_attachment=True, download_name=f"{base_name}.csv")

    if fmt == "excel":
        mem = io.BytesIO()
        # pyrefly: ignore
        with pd.ExcelWriter(mem, engine="openpyxl") as writer:
            # pyrefly: ignore
            df.to_excel(writer, index=False, sheet_name="Report")
        mem.seek(0)
        return send_file(
            mem,
            mimetype="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            as_attachment=True,
            download_name=f"{base_name}.xlsx",
        )

    if fmt == "pdf":
        # pyrefly: ignore [untyped-import]
        from reportlab.lib import colors
        # pyrefly: ignore [untyped-import]
        from reportlab.lib.pagesizes import landscape, A4
        # pyrefly: ignore [untyped-import]
        from reportlab.platypus import SimpleDocTemplate, Table, TableStyle
        mem = io.BytesIO()
        doc = SimpleDocTemplate(mem, pagesize=landscape(A4))
        data = [list(df.columns)] + df.astype(str).values.tolist()
        table = Table(data, repeatRows=1)
        table.setStyle(TableStyle([
            ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1f2937")),
            ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
            ("FONTSIZE", (0, 0), (-1, -1), 7),
            ("GRID", (0, 0), (-1, -1), 0.5, colors.grey),
        ]))
        doc.build([table])
        mem.seek(0)
        return send_file(mem, mimetype="application/pdf", as_attachment=True, download_name=f"{base_name}.pdf")

    return jsonify({"error": "Unsupported format. Use csv, excel, or pdf"}), 400


@reports_bp.route("/students", methods=["GET"])
@token_required()
def student_report():
    fmt = request.args.get("format", "csv")
    filters = {
        "department": request.args.get("department"),
        "academic_year": request.args.get("academic_year"),
        "placement_status": request.args.get("placement_status"),
    }
    df = _students_df(filters)
    return _df_to_response(df, fmt, "student_report")


@reports_bp.route("/companies", methods=["GET"])
@token_required()
def company_report():
    fmt = request.args.get("format", "csv")
    df = _companies_df()
    return _df_to_response(df, fmt, "company_report")


@reports_bp.route("/placement-summary", methods=["GET"])
@token_required()
def placement_summary():
    fmt = request.args.get("format", "csv")
    cur = get_cursor()
    cur.execute(
        """SELECT d.name AS department, COUNT(s.id) AS total_students,
                  COUNT(*) FILTER (WHERE s.placement_status IN ('selected','joined')) AS placed,
                  ROUND(AVG(p.package_amount)::numeric, 2) AS avg_package
           FROM students s
           LEFT JOIN departments d ON d.id = s.department_id
           LEFT JOIN placements p ON p.student_id = s.id AND p.current_stage IN ('selected','joined')
           GROUP BY d.name ORDER BY d.name"""
    )
    df = pd.DataFrame(cur.fetchall())
    return _df_to_response(df, fmt, "placement_summary")
