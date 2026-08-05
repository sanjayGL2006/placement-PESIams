<?php require_once 'config.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Students Directory — Placement Pro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

  <?php include 'partials/nav.php'; ?>

  <main id="main-wrapper">

    <!-- Header Area & View Toggle -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="h3 font-weight-800 mb-1">Students Directory</h2>
        <p class="text-muted small mb-0">Manage registered candidates and placement track status</p>
      </div>

      <!-- List vs Grid Toggle Switch -->
      <div class="bg-white p-1 rounded-3 border d-flex gap-1 shadow-sm">
        <button class="btn btn-sm px-3 font-weight-600 rounded-2 active" id="btnListView" style="background-color: var(--pp-primary-light); color: var(--pp-primary-dark); border: 1px solid var(--pp-primary);" onclick="toggleView('list')">
          <i class="fa-solid fa-list me-1"></i> List
        </button>
        <button class="btn btn-sm px-3 font-weight-600 text-muted rounded-2 border-0" id="btnGridView" onclick="toggleView('grid')">
          <i class="fa-solid fa-border-all me-1"></i> Grid
        </button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="pp-card mb-4 p-3">
      <div class="row g-3 align-items-center">
        <!-- Search Input -->
        <div class="col-12 col-md-3">
          <div class="position-relative">
            <i class="fa-solid fa-magnifying-glass position-absolute text-muted" style="left: 0.85rem; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
            <input type="text" class="form-control-pp w-100 ps-5" id="search" placeholder="Search name or ID..." onkeyup="loadStudents(1)">
          </div>
        </div>

        <!-- Department Dropdown -->
        <div class="col-6 col-md-2">
          <select class="form-select-pp w-100" id="deptFilter" onchange="loadStudents(1)">
            <option value="">Department</option>
            <option value="Computer Science">Computer Science</option>
            <option value="Electronics & Comm.">Electronics & Comm.</option>
            <option value="Information Tech">Information Tech</option>
          </select>
        </div>

        <!-- Status Dropdown -->
        <div class="col-6 col-md-2">
          <select class="form-select-pp w-100" id="statusFilter" onchange="loadStudents(1)">
            <option value="">All Statuses</option>
            <option value="selected">Placed</option>
            <option value="not_placed">Unplaced</option>
            <option value="applied">In-process</option>
          </select>
        </div>

        <!-- Batch Year Dropdown -->
        <div class="col-6 col-md-2">
          <select class="form-select-pp w-100" id="batchFilter" onchange="loadStudents(1)">
            <option value="">Batch Year</option>
            <option value="2024" selected>2023-2024</option>
            <option value="2025">2024-2025</option>
          </select>
        </div>

        <!-- Showing Count Text -->
        <div class="col-12 col-md-3 text-md-end">
          <span class="text-muted small font-weight-600" id="showingText">Showing 1-10 of 2,450 students</span>
        </div>
      </div>
    </div>

    <!-- Data Table Container -->
    <div class="pp-card p-0 overflow-hidden mb-4" id="listViewContainer">
      <div class="table-responsive">
        <table class="pp-table">
          <thead>
            <tr>
              <th>STUDENT</th>
              <th>DEPARTMENT & SECTION</th>
              <th>COMPANY</th>
              <th>STATUS</th>
              <th class="text-end">ACTIONS</th>
            </tr>
          </thead>
          <tbody id="studentTableBody">
            <!-- Populated dynamically or with fallback -->
          </tbody>
        </table>
      </div>
    </div>

    <!-- Grid View Container (Hidden by default) -->
    <div class="row g-3 d-none mb-4" id="gridViewContainer">
      <!-- Grid items render here -->
    </div>

    <!-- Table Footer Controls -->
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <span class="text-muted small">Rows per page:</span>
        <select class="form-select-pp py-1 px-2 small" style="width: 70px;" id="perPageSelect" onchange="loadStudents(1)">
          <option value="10">10</option>
          <option value="25" selected>25</option>
          <option value="50">50</option>
        </select>
      </div>

      <nav>
        <ul class="pagination pagination-sm mb-0" id="pagination">
          <!-- Rendered dynamically -->
        </ul>
      </nav>
    </div>

  </main>

  <!-- Floating Action Button (FAB) -->
  <button class="fab-btn" title="Add New Student" data-bs-toggle="modal" data-bs-target="#addStudentModal">
    <i class="fa-solid fa-user-plus"></i>
  </button>

  <!-- Add Student Modal -->
  <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
        <div class="modal-header border-0 pb-0 px-4 pt-4">
          <h5 class="modal-title font-weight-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i> Add New Student</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <form id="addStudentForm" onsubmit="event.preventDefault(); showToast('Student added successfully!'); bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();">
            <div class="mb-3">
              <label class="form-label font-weight-600 small text-muted">FULL NAME</label>
              <input type="text" class="form-control form-control-pp" placeholder="e.g. Alex Morgan" required>
            </div>
            <div class="mb-3">
              <label class="form-label font-weight-600 small text-muted">REGISTER / ROLL NUMBER</label>
              <input type="text" class="form-control form-control-pp" placeholder="e.g. 21CS042" required>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label font-weight-600 small text-muted">DEPARTMENT</label>
                <select class="form-select-pp w-100" required>
                  <option value="Computer Science">Computer Science</option>
                  <option value="Information Tech">Information Tech</option>
                  <option value="Electronics & Comm.">Electronics & Comm.</option>
                </select>
              </div>
              <div class="col-6">
                <label class="form-label font-weight-600 small text-muted">SECTION</label>
                <select class="form-select-pp w-100" required>
                  <option>Section A</option>
                  <option>Section B</option>
                  <option>Section C</option>
                </select>
              </div>
            </div>
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
              <button type="button" class="btn btn-pp-outline" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-pp-primary">Add Student</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>window.API_BASE = '<?php echo API_BASE; ?>'; window.API_TOKEN = '<?php echo $_SESSION['token']; ?>';</script>
  <script src="assets/js/api.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const MOCK_STUDENTS = [];

    async function loadStudents(page = 1) {
      const tbody = document.getElementById('studentTableBody');
      const gridContainer = document.getElementById('gridViewContainer');
      
      try {
        const search = document.getElementById('search').value;
        const dept = document.getElementById('deptFilter').value;
        const status = document.getElementById('statusFilter').value;

        const data = await API.get(`/students?page=${page}&per_page=10&search=${search}&department=${dept}&placement_status=${status}`);
        if(data && data.students) {
          renderStudents(data.students, data.total);
          return;
        }
      } catch(err) {
        console.log('Using mock student directory:', err.message);
      }

      renderStudents(MOCK_STUDENTS, 2450);
    }

    function renderStudents(list, total) {
      if (!list || list.length === 0) {
        document.getElementById('showingText').innerText = `Showing 0 of 0 students`;
        document.getElementById('studentTableBody').innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small"><i class="fa-regular fa-user me-1" style="font-size: 1.25rem;"></i> No students found</td></tr>`;
        document.getElementById('gridViewContainer').innerHTML = `<div class="col-12 text-center py-5 text-muted small"><i class="fa-regular fa-user me-1" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i> No students found</div>`;
        document.getElementById('pagination').innerHTML = '';
        return;
      }

      document.getElementById('showingText').innerText = `Showing 1-${list.length} of ${total.toLocaleString()} students`;

      // Table View
      document.getElementById('studentTableBody').innerHTML = list.map(s => {
        const statusBadge = s.statusType === 'success' || s.placement_status === 'selected' || s.placement_status === 'joined'
          ? `<span class="badge-pill-success"><i class="fa-solid fa-circle" style="font-size: 0.4rem;"></i> Placed</span>`
          : (s.statusType === 'warning' || s.placement_status === 'applied'
            ? `<span class="badge-pill-warning"><i class="fa-solid fa-circle" style="font-size: 0.4rem;"></i> In-process</span>`
            : `<span class="badge-pill-danger"><i class="fa-solid fa-circle" style="font-size: 0.4rem;"></i> Unplaced</span>`);

        const companyLogo = s.logo 
          ? `<img src="${s.logo}" onerror="this.src='https://cdn-icons-png.flaticon.com/512/3135/3135715.png'" width="20" height="20" class="rounded me-2">` 
          : `<i class="fa-solid fa-building me-2 text-muted"></i>`;

        return `
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(s.name || 'Student')}&background=4F46E5&color=fff" class="rounded-circle" width="36" height="36">
                <div>
                  <div class="font-weight-700 text-dark">${s.name}</div>
                  <div class="text-muted small">${s.register_number || s.id || '21CS000'}</div>
                </div>
              </div>
            </td>
            <td>
              <div class="font-weight-600 text-dark">${s.department_name || s.dept || 'Computer Science'}</div>
              <div class="text-muted small">${s.section || s.sec || 'Section A'}</div>
            </td>
            <td>
              <div class="d-flex align-items-center font-weight-600 text-dark">
                ${companyLogo} ${s.company_name || s.company || '-'}
              </div>
            </td>
            <td>${statusBadge}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-pp-outline py-1 px-2"><i class="fa-regular fa-eye me-1"></i> View</button>
            </td>
          </tr>
        `;
      }).join('');

      // Grid View
      document.getElementById('gridViewContainer').innerHTML = list.map(s => `
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="pp-card h-100">
            <div class="d-flex align-items-center gap-3 mb-3">
              <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(s.name || 'Student')}&background=4F46E5&color=fff" class="rounded-circle" width="48" height="48">
              <div>
                <div class="font-weight-700 text-dark mb-0">${s.name}</div>
                <div class="text-muted small">${s.register_number || s.id || '21CS000'}</div>
              </div>
            </div>
            <div class="small mb-2"><span class="text-muted">Dept:</span> <span class="font-weight-600">${s.department_name || s.dept || 'CS'}</span></div>
            <div class="small mb-3"><span class="text-muted">Company:</span> <span class="font-weight-600">${s.company_name || s.company || '-'}</span></div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
              <span class="badge-pill-success">Placed</span>
              <button class="btn btn-sm btn-pp-outline py-1"><i class="fa-regular fa-eye me-1"></i> Profile</button>
            </div>
          </div>
        </div>
      `).join('');

      // Pagination
      document.getElementById('pagination').innerHTML = `
        <li class="page-item disabled"><a class="page-link" href="#">Prev</a></li>
        <li class="page-item active"><a class="page-link" href="#" style="background-color: var(--pp-primary); border-color: var(--pp-primary);">1</a></li>
        <li class="page-item"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
        <li class="page-item"><a class="page-link" href="#">245</a></li>
        <li class="page-item"><a class="page-link" href="#">Next</a></li>
      `;
    }

    function toggleView(mode) {
      if(mode === 'list') {
        document.getElementById('listViewContainer').classList.remove('d-none');
        document.getElementById('gridViewContainer').classList.add('d-none');
        document.getElementById('btnListView').style.backgroundColor = 'var(--pp-primary-light)';
        document.getElementById('btnListView').style.color = 'var(--pp-primary-dark)';
        document.getElementById('btnListView').style.border = '1px solid var(--pp-primary)';
        document.getElementById('btnGridView').style.backgroundColor = 'transparent';
        document.getElementById('btnGridView').style.color = '#6B7280';
        document.getElementById('btnGridView').style.border = 'none';
      } else {
        document.getElementById('listViewContainer').classList.add('d-none');
        document.getElementById('gridViewContainer').classList.remove('d-none');
        document.getElementById('btnGridView').style.backgroundColor = 'var(--pp-primary-light)';
        document.getElementById('btnGridView').style.color = 'var(--pp-primary-dark)';
        document.getElementById('btnGridView').style.border = '1px solid var(--pp-primary)';
        document.getElementById('btnListView').style.backgroundColor = 'transparent';
        document.getElementById('btnListView').style.color = '#6B7280';
        document.getElementById('btnListView').style.border = 'none';
      }
    }

    loadStudents(1);
  </script>
</body>
</html>
