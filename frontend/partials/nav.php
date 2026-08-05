<?php
require_once __DIR__ . '/sidebar.php';
require_once __DIR__ . '/header.php';
?>

<!-- Global Post New Job Modal -->
<div class="modal fade" id="postJobModal" tabindex="-1" aria-labelledby="postJobModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
      <div class="modal-header border-0 pb-0 px-4 pt-4">
        <h5 class="modal-title font-weight-bold" id="postJobModalLabel">
          <i class="fa-solid fa-briefcase text-primary me-2" style="color: var(--pp-primary) !important;"></i> Post New Job Drive
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form id="postJobForm" onsubmit="event.preventDefault(); showToast('Job post drive published successfully!'); bootstrap.Modal.getInstance(document.getElementById('postJobModal')).hide();">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label font-weight-600 text-muted small">COMPANY NAME</label>
              <input type="text" class="form-control form-control-pp" placeholder="e.g. Goldman Sachs" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-600 text-muted small">ROLE / POSITION</label>
              <input type="text" class="form-control form-control-pp" placeholder="e.g. Software Development Engineer" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-600 text-muted small">PACKAGE (LPA)</label>
              <input type="text" class="form-control form-control-pp" placeholder="e.g. 14.5" required>
            </div>
            <div class="col-md-6">
              <label class="form-label font-weight-600 text-muted small">DRIVE DATE</label>
              <input type="date" class="form-control form-control-pp" required>
            </div>
            <div class="col-md-12">
              <label class="form-label font-weight-600 text-muted small">ELIGIBLE DEPARTMENTS</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" checked id="deptCS">
                  <label class="form-check-label" for="deptCS">Computer Science</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" checked id="deptIT">
                  <label class="form-check-label" for="deptIT">Information Tech</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="deptECE">
                  <label class="form-check-label" for="deptECE">Electronics & Comm</label>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <label class="form-label font-weight-600 text-muted small">JOB DESCRIPTION & REQUIREMENTS</label>
              <textarea class="form-control form-control-pp" rows="3" placeholder="Enter drive details..."></textarea>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-2 mt-4 pt-2 border-top">
            <button type="button" class="btn btn-pp-outline" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-pp-primary"><i class="fa-solid fa-paper-plane me-1"></i> Publish Drive</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  window.API_BASE = '<?php echo API_BASE; ?>';
  window.API_TOKEN = '<?php echo $_SESSION['token'] ?? ""; ?>';
</script>
<script src="assets/js/api.js"></script>
