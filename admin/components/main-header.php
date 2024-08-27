<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$base_url = '/anime/admin/assets/';
$def_path = "/anime";
include __DIR__."/../../actions/connect.php";

if (isset($_COOKIE['auth_token'])) {
  $token = $_COOKIE['auth_token'];
  $decoded = JWT::decode($token, new Key($key, 'HS256'));
  $username = $decoded->sub;

  $query = "SELECT username,email,image FROM users WHERE username=:username";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':username', $username);
  $stmt->execute();
  $result = $stmt->fetch();
}

?>

<div class="main-header">
  <div class="main-header-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="index.html" class="logo">
        <img
          src="<?php echo $def_path."/img/logo.png" ?>"
          alt="navbar brand"
          class="navbar-brand"
          height="20"
        />
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
    <!-- End Logo Header -->
  </div>

  <!-- Navbar Header -->
  <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
      <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
        <li class="nav-item topbar-user dropdown hidden-caret">
          <a
            class="dropdown-toggle profile-pic"
            data-bs-toggle="dropdown"
            href="#"
            aria-expanded="false"
          >
            <div class="avatar-sm">
              <img
                src="<?php echo $def_path.$result['image'] ?>"
                alt="Profile Image"
                class="avatar-img rounded-circle"
              />
            </div>
            <span class="profile-username">
              <span class="op-7">Hi,</span>
              <span class="fw-bold"><?php echo $result['username'] ?></span>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                    <img
                      src="<?php echo $def_path.$result['image'] ?>"
                      alt="image profile"
                      class="avatar-img rounded"
                    />
                  </div>
                  <div class="u-text">
                    <h4><?php echo $result['username'] ?></h4>
                    <p class="text-muted"><?php echo $result['email'] ?></p>
                    <a href="<?php echo $def_path."/profile.php" ?>" class="btn btn-xs btn-secondary">
                      Edit Profile
                    </a>
                  </div>
                </div>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item" href="<?php echo $def_path."/user/".$result['username'] ?>">
                  <i class="fa fa-user"></i> My Profile
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item" href="<?php echo $def_path ?>/logout.php">
                  <i class="fa fa-power-off"></i> Logout
                </a>
              </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
  <!-- End Navbar Header -->
</div>
