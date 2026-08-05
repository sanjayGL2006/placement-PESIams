<?php require_once 'config.php'; require_login(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sections Overview — Placement Pro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

  <!-- Layout Framing -->
  <?php include 'partials/nav.php'; ?>

  <main id="main-wrapper">

    <!-- Header Area & Segmented Tabs -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2 class="h3 font-weight-800 mb-1">Sections Overview</h2>
        <p class="text-muted small mb-0">Cohort performance analysis across section divisions</p>
      </div>

      <!-- Segmented Control Tabs -->
      <div class="bg-white p-1 rounded-3 border d-flex gap-1 shadow-sm">
        <button class="btn btn-sm px-3 font-weight-600 rounded-2" style="background-color: var(--pp-primary-light); color: var(--pp-primary-dark); border: 1px solid var(--pp-primary);" onclick="switchSection(this, 'Section A')">Section A</button>
        <button class="btn btn-sm px-3 font-weight-600 text-muted rounded-2 border-0" onclick="switchSection(this, 'Section B')">Section B</button>
        <button class="btn btn-sm px-3 font-weight-600 text-muted rounded-2 border-0" onclick="switchSection(this, 'Section C')">Section C</button>
      </div>
    </div>

    <!-- Section KPI Cards -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-sm-6 col-lg-3">
        <div class="pp-card kpi-card">
          <div class="kpi-header">
            <span class="kpi-title">Total Students</span>
            <i class="fa-solid fa-users text-primary"></i>
          </div>
          <div class="kpi-value" id="secTotal">800</div>
          <div class="kpi-subtext">Section cohort count</div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="pp-card kpi-card">
          <div class="kpi-header">
            <span class="kpi-title">Selected Students</span>
            <span class="text-muted small font-weight-600">84%</span>
          </div>
          <div class="kpi-value" id="secSelected">672</div>
          <div class="progress mt-2" style="height: 6px; border-radius: 999px;">
            <div class="progress-bar bg-success" style="width: 84%;"></div>
          </div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="pp-card kpi-card">
          <div class="kpi-header">
            <span class="kpi-title">Placement %</span>
            <span class="badge-pill-success">Target 80% Exceeded</span>
          </div>
          <div class="kpi-value" id="secPct">84.0%</div>
          <div class="kpi-subtext">Conversion rate</div>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-lg-3">
        <div class="pp-card kpi-card">
          <div class="kpi-header">
            <span class="kpi-title">Avg. Package</span>
            <span class="badge-pill-info">Top 5% Batch</span>
          </div>
          <div class="kpi-value" id="secAvg">$9.2 LPA</div>
          <div class="kpi-subtext">Highest $42 LPA</div>
        </div>
      </div>
    </div>

    <!-- Widgets Row -->
    <div class="row g-4 mb-4">
      <!-- Company Distribution Donut Chart -->
      <div class="col-12 col-lg-6">
        <div class="pp-card h-100">
          <h5 class="h6 font-weight-700 mb-3">Company Distribution</h5>
          <div style="height: 220px;" class="position-relative d-flex justify-content-center">
            <canvas id="companyDistChart"></canvas>
          </div>
          <div class="row mt-3 pt-3 border-top text-center">
            <div class="col-3">
              <div class="small text-muted mb-1"><i class="fa-solid fa-circle me-1" style="color: #4F46E5;"></i> Product</div>
              <div class="font-weight-700">42%</div>
            </div>
            <div class="col-3">
              <div class="small text-muted mb-1"><i class="fa-solid fa-circle me-1" style="color: #10B981;"></i> Service</div>
              <div class="font-weight-700">30%</div>
            </div>
            <div class="col-3">
              <div class="small text-muted mb-1"><i class="fa-solid fa-circle me-1" style="color: #F59E0B;"></i> Fintech</div>
              <div class="font-weight-700">18%</div>
            </div>
            <div class="col-3">
              <div class="small text-muted mb-1"><i class="fa-solid fa-circle me-1" style="color: #64748B;"></i> Others</div>
              <div class="font-weight-700">10%</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Department Analytics Horizontal Progress Bars -->
      <div class="col-12 col-lg-6">
        <div class="pp-card h-100">
          <h5 class="h6 font-weight-700 mb-3">Department Analytics</h5>
          <div class="d-flex flex-column gap-3">
            <div>
              <div class="d-flex justify-content-between mb-1">
                <span class="font-weight-600 text-dark small">Computer Science</span>
                <span class="font-weight-700 text-primary small" style="color: var(--pp-primary) !important;">92% Placed (368 / 400)</span>
              </div>
              <div class="progress" style="height: 8px; border-radius: 999px;">
                <div class="progress-bar" style="width: 92%; background-color: var(--pp-primary);"></div>
              </div>
            </div>

            <div>
              <div class="d-flex justify-content-between mb-1">
                <span class="font-weight-600 text-dark small">Electronics & Comm.</span>
                <span class="font-weight-700 text-success small">78% Placed (195 / 250)</span>
              </div>
              <div class="progress" style="height: 8px; border-radius: 999px;">
                <div class="progress-bar bg-success" style="width: 78%;"></div>
              </div>
            </div>

            <div>
              <div class="d-flex justify-content-between mb-1">
                <span class="font-weight-600 text-dark small">Information Tech</span>
                <span class="font-weight-700 text-warning small">72% Placed (108 / 150)</span>
              </div>
              <div class="progress" style="height: 8px; border-radius: 999px;">
                <div class="progress-bar bg-warning" style="width: 72%;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Placement Pipeline Step Tracker (Bottom) -->
    <div class="pp-card">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h5 class="h6 font-weight-700 mb-0">Placement Pipeline Funnel</h5>
          <p class="text-muted small mb-0">Student progression & drop-off metrics through selection rounds</p>
        </div>
        <span class="badge-pill-success">Final Stage Complete</span>
      </div>

      <div class="pipeline-tracker my-3">
        <!-- Connecting line -->
        <div class="pipeline-connector">
          <div class="pipeline-connector-progress"></div>
        </div>

        <!-- Step 1: Eligible -->
        <div class="pipeline-step active">
          <div class="icon-circle">
            <i class="fa-solid fa-user-check"></i>
          </div>
          <div class="font-weight-700 text-dark">Eligible</div>
          <div class="text-muted small">800 Students</div>
          <div class="badge-pill-info mt-1" style="font-size: 0.7rem;">100% Start</div>
        </div>

        <!-- Step 2: Aptitude -->
        <div class="pipeline-step active">
          <div class="icon-circle">
            <i class="fa-solid fa-laptop-code"></i>
          </div>
          <div class="font-weight-700 text-dark">Aptitude</div>
          <div class="text-muted small">740 Passed</div>
          <div class="badge-pill-warning mt-1" style="font-size: 0.7rem;">-60 Drop-off</div>
        </div>

        <!-- Step 3: Technical -->
        <div class="pipeline-step active">
          <div class="icon-circle">
            <i class="fa-solid fa-code-branch"></i>
          </div>
          <div class="font-weight-700 text-dark">Technical</div>
          <div class="text-muted small">700 Cleared</div>
          <div class="badge-pill-warning mt-1" style="font-size: 0.7rem;">-40 Drop-off</div>
        </div>

        <!-- Step 4: Selected -->
        <div class="pipeline-step active">
          <div class="icon-circle" style="background: #10B981;">
            <i class="fa-solid fa-trophy"></i>
          </div>
          <div class="font-weight-700 text-dark">Selected</div>
          <div class="text-muted small">672 Offers</div>
          <div class="badge-pill-success mt-1" style="font-size: 0.7rem;">84% Overall</div>
        </div>
      </div>
    </div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Company distribution chart
    const ctxDist = document.getElementById('companyDistChart').getContext('2d');
    new Chart(ctxDist, {
      type: 'doughnut',
      data: {
        labels: ['Product', 'Service', 'Fintech', 'Others'],
        datasets: [{
          data: [42, 30, 18, 10],
          backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#64748B'],
          borderWidth: 0,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: { legend: { display: false } }
      }
    });

    function switchSection(btn, secName) {
      document.querySelectorAll('.bg-white.p-1.rounded-3 button').forEach(b => {
        b.style.backgroundColor = 'transparent';
        b.style.color = '#6B7280';
        b.style.border = 'none';
        b.classList.remove('font-weight-700');
      });
      btn.style.backgroundColor = 'var(--pp-primary-light)';
      btn.style.color = 'var(--pp-primary-dark)';
      btn.style.border = '1px solid var(--pp-primary)';
      btn.classList.add('font-weight-700');
    }
  </script>
</body>
</html>
