<?php
$userName = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'Admin User';
$userRole = isset($_SESSION['user']['role']) ? ucfirst($_SESSION['user']['role']) : 'Administrator';
?>
<header id="top-header">
  <!-- Search Bar -->
  <div class="header-search-container">
    <i class="fa-solid fa-magnifying-glass search-icon"></i>
    <input type="text" placeholder="Search students, companies, records..." id="globalSearchInput">
  </div>

  <!-- Header Right Actions -->
  <div class="header-actions">
    <!-- Help / Question Mark -->
    <button class="header-icon-btn" title="Help & Documentation">
      <i class="fa-regular fa-circle-question"></i>
    </button>

    <!-- Notification Bell -->
    <button class="header-icon-btn" title="Notifications">
      <i class="fa-regular fa-bell"></i>
      <span class="notification-dot"></span>
    </button>

    <!-- User Profile Component -->
    <div class="dropdown">
      <div class="user-profile-badge cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120" alt="Avatar" class="user-avatar">
        <div class="user-info">
          <div class="user-name"><?php echo htmlspecialchars($userName); ?></div>
          <div class="user-role"><?php echo htmlspecialchars($userRole); ?></div>
        </div>
        <i class="fa-solid fa-chevron-down ms-1 text-muted" style="font-size: 0.75rem;"></i>
      </div>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
        <li><a class="dropdown-item py-2" href="settings.php"><i class="fa-solid fa-user me-2 text-muted"></i> Edit Profile</a></li>
        <li><a class="dropdown-item py-2" href="settings.php"><i class="fa-solid fa-gear me-2 text-muted"></i> Account Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item py-2 text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i> Sign Out</a></li>
      </ul>
    </div>
  </div>
</header>
