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
          </select>
        </div>

        <!-- Section Dropdown -->
        <div class="col-6 col-md-2">
          <select class="form-select-pp w-100" id="sectionFilter" onchange="loadStudents(1)">
            <option value="">Section</option>
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
        <div class="col-6 col-md-1">
          <select class="form-select-pp w-100" id="batchFilter" onchange="loadStudents(1)">
            <option value="">Batch Year</option>
          </select>
        </div>

        <!-- Showing Count Text -->
        <div class="col-12 col-md-2 text-md-end">
          <span class="text-muted small font-weight-600" id="showingText">Showing 0 of 0 students</span>
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

  <!-- Edit Student Modal -->
  <div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
        <div class="modal-header border-0 pb-0 px-4 pt-4">
          <h5 class="modal-title font-weight-bold"><i class="fa-solid fa-user-pen text-primary me-2"></i> Edit Student Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-4">
          <form id="editStudentForm" onsubmit="saveStudentChanges(event)">
            <input type="hidden" id="editStudentId">
            <div class="mb-3">
              <label class="form-label font-weight-600 small text-muted">FULL NAME</label>
              <input type="text" id="editName" class="form-control form-control-pp" required>
            </div>
            <div class="mb-3">
              <label class="form-label font-weight-600 small text-muted">REGISTER / ROLL NUMBER</label>
              <input type="text" id="editRegister" class="form-control form-control-pp" readonly disabled>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label font-weight-600 small text-muted">DEPARTMENT</label>
                <select id="editDept" class="form-select-pp w-100" required>
                  <!-- Loaded dynamically -->
                </select>
              </div>
              <div class="col-6">
                <label class="form-label font-weight-600 small text-muted">SECTION</label>
                <input type="text" id="editSection" class="form-control form-control-pp" placeholder="e.g. Section A" required>
              </div>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label font-weight-600 small text-muted">ACADEMIC YEAR</label>
                <input type="text" id="editYear" class="form-control form-control-pp" placeholder="e.g. 2023-2024" required>
              </div>
              <div class="col-6">
                <label class="form-label font-weight-600 small text-muted">PLACEMENT STATUS</label>
                <select id="editPlacementStatus" class="form-select-pp w-100" required>
                  <option value="unplaced">Unplaced</option>
                  <option value="applied">Applied</option>
                  <option value="selected">Selected</option>
                  <option value="joined">Joined</option>
                </select>
              </div>
            </div>
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label font-weight-600 small text-muted">CGPA</label>
                <input type="number" step="0.01" min="0" max="10" id="editCgpa" class="form-control form-control-pp" placeholder="e.g. 8.5">
              </div>
              <div class="col-6">
                <label class="form-label font-weight-600 small text-muted">BACKLOGS</label>
                <input type="number" min="0" id="editBacklogs" class="form-control form-control-pp" placeholder="e.g. 0">
              </div>
            </div>
            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
              <button type="button" class="btn btn-pp-outline" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-pp-primary">Save Changes</button>
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

    // Helper to format section nicely (e.g., "section C" -> "Section C")
    function formatSection(sec) {
      if (!sec) return 'Section A';
      sec = sec.trim();
      const match = sec.match(/^section\s+([a-zA-Z])$/i);
      if (match) {
        return 'Section ' + match[1].toUpperCase();
      }
      if (sec.length === 1 && /[a-zA-Z]/.test(sec)) {
        return 'Section ' + sec.toUpperCase();
      }
      return sec;
    }

    // Helper to format department nicely (e.g., "bca" -> "BCA")
    function formatDept(dept) {
      if (!dept) return 'Computer Science';
      dept = dept.trim();
      const lower = dept.toLowerCase();
      if (lower === 'bca') return 'BCA';
      if (lower === 'bba') return 'BBA';
      if (lower === 'b.com') return 'B.Com';
      if (lower === 'b.sc') return 'B.Sc';
      // Capitalize first letters of words
      return dept.replace(/\b\w/g, c => c.toUpperCase());
    }

    // Dynamic Filter Loader
    async function initFilters() {
      try {
        const filters = await API.get('/dashboard/filters');
        
        // 1. Populate Department filter
        const deptSelect = document.getElementById('deptFilter');
        deptSelect.innerHTML = '<option value="">Department</option>';
        filters.departments.forEach(d => {
          deptSelect.innerHTML += `<option value="${d}">${formatDept(d)}</option>`;
        });

        // 2. Populate editDept dropdown
        const editDeptSelect = document.getElementById('editDept');
        editDeptSelect.innerHTML = '';
        filters.departments.forEach(d => {
          editDeptSelect.innerHTML += `<option value="${d}">${formatDept(d)}</option>`;
        });
        
        // 3. Populate Section filter
        const sectionSelect = document.getElementById('sectionFilter');
        sectionSelect.innerHTML = '<option value="">Section</option>';
        filters.sections.forEach(s => {
          sectionSelect.innerHTML += `<option value="${s}">${formatSection(s)}</option>`;
        });

        // 4. Populate Batch Year filter
        const batchSelect = document.getElementById('batchFilter');
        batchSelect.innerHTML = '<option value="">Batch Year</option>';
        filters.academic_years.forEach(y => {
          batchSelect.innerHTML += `<option value="${y}">${y}</option>`;
        });
      } catch (err) {
        console.error('Failed to load filter options:', err);
      }
    }

    async function loadStudents(page = 1) {
      const tbody = document.getElementById('studentTableBody');
      const gridContainer = document.getElementById('gridViewContainer');
      
      try {
        const search = document.getElementById('search').value;
        const dept = document.getElementById('deptFilter').value;
        const section = document.getElementById('sectionFilter').value;
        const status = document.getElementById('statusFilter').value;
        const batch = document.getElementById('batchFilter').value;
        const perPage = document.getElementById('perPageSelect').value || 25;

        const data = await API.get(`/students?page=${page}&per_page=${perPage}&search=${search}&department=${dept}&section=${section}&placement_status=${status}&academic_year=${batch}`);
        if(data && data.students) {
          renderStudents(data.students, data.total, page, perPage);
          return;
        }
      } catch(err) {
        console.log('Using mock student directory:', err.message);
      }

      renderStudents(MOCK_STUDENTS, 0, page, 25);
    }

    function renderStudents(list, total, page, perPage) {
      if (!list || list.length === 0) {
        document.getElementById('showingText').innerText = `Showing 0 of 0 students`;
        document.getElementById('studentTableBody').innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted small"><i class="fa-regular fa-user me-1" style="font-size: 1.25rem;"></i> No students found</td></tr>`;
        document.getElementById('gridViewContainer').innerHTML = `<div class="col-12 text-center py-5 text-muted small"><i class="fa-regular fa-user me-1" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i> No students found</div>`;
        document.getElementById('pagination').innerHTML = '';
        return;
      }

      const start = (page - 1) * perPage + 1;
      const end = Math.min(page * perPage, total);
      document.getElementById('showingText').innerText = `Showing ${start}-${end} of ${total.toLocaleString()} students`;

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
              <div class="font-weight-600 text-dark">${formatDept(s.department_name || s.dept)}</div>
              <div class="text-muted small">${formatSection(s.section || s.sec)}</div>
            </td>
            <td>
              <div class="d-flex align-items-center font-weight-600 text-dark">
                ${companyLogo} ${s.company_name || s.company || '-'}
              </div>
            </td>
            <td>${statusBadge}</td>
            <td class="text-end">
              <button class="btn btn-sm btn-pp-outline py-1 px-2" onclick="showToast('Loading profile for ${s.name}...')"><i class="fa-regular fa-eye me-1"></i> View</button>
              <button class="btn btn-sm btn-pp-primary py-1 px-2 ms-1" onclick="openEditModal(${s.id})"><i class="fa-solid fa-user-pen"></i> Edit</button>
            </td>
          </tr>
        `;
      }).join('');

      // Grid View
      document.getElementById('gridViewContainer').innerHTML = list.map(s => {
        const isPlaced = s.placement_status === 'selected' || s.placement_status === 'joined';
        const badge = isPlaced 
          ? `<span class="badge-pill-success">Placed</span>`
          : (s.placement_status === 'applied' ? `<span class="badge-pill-warning">In-process</span>` : `<span class="badge-pill-danger">Unplaced</span>`);
          
        return `
          <div class="col-12 col-sm-6 col-lg-3">
            <div class="pp-card h-100">
              <div class="d-flex align-items-center gap-3 mb-3">
                <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(s.name || 'Student')}&background=4F46E5&color=fff" class="rounded-circle" width="48" height="48">
                <div>
                  <div class="font-weight-700 text-dark mb-0">${s.name}</div>
                  <div class="text-muted small">${s.register_number || s.id || '21CS000'}</div>
                </div>
              </div>
              <div class="small mb-2"><span class="text-muted">Dept & Sec:</span> <span class="font-weight-600">${formatDept(s.department_name || s.dept)} (${formatSection(s.section || s.sec)})</span></div>
              <div class="small mb-3"><span class="text-muted">Company:</span> <span class="font-weight-600">${s.company_name || s.company || '-'}</span></div>
              <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                ${badge}
                <div class="d-flex gap-1">
                  <button class="btn btn-sm btn-pp-outline py-1 px-2" onclick="showToast('Loading profile for ${s.name}...')"><i class="fa-regular fa-eye"></i> View</button>
                  <button class="btn btn-sm btn-pp-primary py-1 px-2" onclick="openEditModal(${s.id})"><i class="fa-solid fa-user-pen"></i> Edit</button>
                </div>
              </div>
            </div>
          </div>
        `;
      }).join('');

      // Render Pagination Links
      const totalPages = Math.ceil(total / perPage);
      let paginationHtml = '';
      
      // Prev Button
      paginationHtml += `<li class="page-item ${page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="event.preventDefault(); ${page > 1 ? `loadStudents(${page - 1})` : ''}">Prev</a>
      </li>`;
      
      // Page numbers (simplified display)
      for (let i = 1; i <= Math.min(totalPages, 5); i++) {
        paginationHtml += `<li class="page-item ${page === i ? 'active' : ''}">
          <a class="page-link" href="#" onclick="event.preventDefault(); loadStudents(${i})" ${page === i ? 'style="background-color: var(--pp-primary); border-color: var(--pp-primary);"' : ''}>${i}</a>
        </li>`;
      }
      if (totalPages > 5) {
        paginationHtml += `<li class="page-item disabled"><a class="page-link" href="#">...</a></li>`;
        paginationHtml += `<li class="page-item ${page === totalPages ? 'active' : ''}">
          <a class="page-link" href="#" onclick="event.preventDefault(); loadStudents(${totalPages})" ${page === totalPages ? 'style="background-color: var(--pp-primary); border-color: var(--pp-primary);"' : ''}>${totalPages}</a>
        </li>`;
      }
      
      // Next Button
      paginationHtml += `<li class="page-item ${page === totalPages || totalPages === 0 ? 'disabled' : ''}">
        <a class="page-link" href="#" onclick="event.preventDefault(); ${page < totalPages ? `loadStudents(${page + 1})` : ''}">Next</a>
      </li>`;
      
      document.getElementById('pagination').innerHTML = paginationHtml;
    }

    // Modal Operations
    async function openEditModal(studentId) {
      try {
        const student = await API.get(`/students/${studentId}`);
        if (student) {
          document.getElementById('editStudentId').value = student.id;
          document.getElementById('editName').value = student.name || '';
          document.getElementById('editRegister').value = student.register_number || '';
          document.getElementById('editDept').value = student.department_name || '';
          document.getElementById('editSection').value = formatSection(student.section || '');
          document.getElementById('editYear').value = student.academic_year || '2023-2024';
          document.getElementById('editPlacementStatus').value = student.placement_status || 'unplaced';
          document.getElementById('editCgpa').value = student.cgpa || '';
          document.getElementById('editBacklogs').value = student.backlogs || 0;
          
          const modalEl = document.getElementById('editStudentModal');
          const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
          modalInstance.show();
        }
      } catch (err) {
        showToast('Error loading student details: ' + err.message, 'danger');
      }
    }

    async function saveStudentChanges(e) {
      e.preventDefault();
      const studentId = document.getElementById('editStudentId').value;
      const payload = {
        name: document.getElementById('editName').value,
        section: document.getElementById('editSection').value,
        academic_year: document.getElementById('editYear').value,
        placement_status: document.getElementById('editPlacementStatus').value,
        cgpa: document.getElementById('editCgpa').value ? parseFloat(document.getElementById('editCgpa').value) : null,
        backlogs: document.getElementById('editBacklogs').value ? parseInt(document.getElementById('editBacklogs').value) : 0
      };
      
      try {
        await API.put(`/students/${studentId}`, payload);
        showToast('Student profile updated successfully!');
        
        const modalEl = document.getElementById('editStudentModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if(modalInstance) modalInstance.hide();
        
        loadStudents(1);
      } catch (err) {
        showToast('Failed to save changes: ' + err.message, 'danger');
      }
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

    // Initialize dropdowns and load first page on load
    document.addEventListener('DOMContentLoaded', async () => {
      await initFilters();
      loadStudents(1);
    });
  </script>
</body>
</html>
