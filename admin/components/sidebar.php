<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <div class="logo-header" data-background-color="dark">
      <a href="/anime/admin/index.php" class="logo">
        <img
          src="/anime/img/logo.png"
          alt="navbar brand"
          class="navbar-brand"
          height="30" />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
  </div>
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <li class="nav-item active">
          <a
            href="/anime/admin/index.php"
            class="collapsed">
            <i class="fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a
            href="/anime"
            class="collapsed">
            <i class="fa fa-h-square"></i>
            <p>Return Site</p>
          </a>
        </li>
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Components</h4>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#base">
            <i class="fas fa-layer-group"></i>
            <p>General</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="base">
            <ul class="nav nav-collapse">
              <li>
                <a href="/anime/admin/shows.php">
                  <span class="sub-item">Add Show</span>
                </a>
              </li>
              <li>
                <a href="/anime/admin/types.php">
                  <span class="sub-item">Add Types</span>
                </a>
              </li>
              <li>
                <a href="/anime/admin/categories.php">
                  <span class="sub-item">Add Categories</span>
                </a>
              </li>
              <li>
                <a href="/anime/admin/characters.php">
                  <span class="sub-item">Add Character</span>
                </a>
              </li>
              <li>
                <a href="components/notifications.html">
                  <span class="sub-item">Notifications</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#sidebarLayouts">
            <i class="fas fa-th-list"></i>
            <p>Common Settings</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="sidebarLayouts">
            <ul class="nav nav-collapse">
              <li>
                <a href="/anime/admin/users.php">
                  <span class="sub-item">Edit Users</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>