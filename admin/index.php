<?php 
$title = 'Dashboard';
include __DIR__."/../actions/connect.php" ;

$admin_path = "/anime";

$jsonFile = __DIR__.'/../settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

//* GETTING USERS
$query = "SELECT username,name,image,createdAt,email FROM users LIMIT 6";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll();

//* GETTING MOST COMMENTED SHOWS
$query = "SELECT s.name, s.image, s.slug,s.imdb, COUNT(c.id) AS comment_count
FROM Shows s
JOIN Comments c ON s.id = c.showId
GROUP BY s.id, s.name, s.image, s.slug, s.imdb
ORDER BY comment_count DESC
LIMIT 8;
";
$stmt = $db->prepare($query);
$stmt->execute();
$mcShows = $stmt->fetchAll();

//* GETTING COMMENTS COUNT
$query = "SELECT COUNT(*) FROM Comments";
$stmt = $db->prepare($query);
$stmt->execute();
$comCount = $stmt->fetchColumn();

//* GETTING BLOGS COUNT
$query = "SELECT COUNT(*) FROM Blog";
$stmt = $db->prepare($query);
$stmt->execute();
$bCount = $stmt->fetchColumn();

//* GETTING USERS COUNT
$query = "SELECT COUNT(*) FROM users";
$stmt = $db->prepare($query);
$stmt->execute();
$usCount = $stmt->fetchColumn();

//* GETTING SHOWS COUNT
$query = "SELECT COUNT(*) FROM Shows";
$stmt = $db->prepare($query);
$stmt->execute();
$ssCount = $stmt->fetchColumn();


include './components/up-all.php' ?>

<div class="container">
  <div class="page-inner">
    <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
      <div>
        <h3 class="fw-bold mb-3">Dashboard</h3>
        <h6 class="op-7 mb-2">Overview of site and more</h6>
      </div>
      <div class="ms-md-auto py-2 py-md-0">
        <a href="<?php echo $admin_path."/admin/users.php" ?>" class="btn btn-label-info btn-round me-2">Manage Users</a>
        <a href="<?php echo $admin_path."/admin/shows.php" ?>" class="btn btn-primary btn-round">Add Show</a>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-primary bubble-shadow-small">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Users</p>
                  <h4 class="card-title"><?php echo $usCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-info bubble-shadow-small">
                  <i class="fa-solid fa-video"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Shows</p>
                  <h4 class="card-title"><?php echo $ssCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-success bubble-shadow-small">
                  <i class="fa-solid fa-blog"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Blogs</p>
                  <h4 class="card-title"><?php echo $bCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-sm-6 col-md-3">
        <div class="card card-stats card-round">
          <div class="card-body">
            <div class="row align-items-center">
              <div class="col-icon">
                <div class="icon-big text-center icon-secondary bubble-shadow-small">
                  <i class="fa-solid fa-comment"></i>
                </div>
              </div>
              <div class="col col-stats ms-3 ms-sm-0">
                <div class="numbers">
                  <p class="card-category">Comments</p>
                  <h4 class="card-title"><?php echo $comCount ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="row">
      <div class="col-md-4">
        <div class="card card-round">
          <div class="card-body">
            <div class="card-head-row card-tools-still-right">
              <div class="card-title">New Users</div>
            </div>
            <div class="card-list py-4">
              <?php foreach($users as $user) : ?>
                <div class="item-list">
                  <div class="avatar">
                    <img src="<?php echo $admin_path.($user['image'] ?? "/img/defuser.png") ?>" alt="user image" class="avatar-img rounded-circle" />
                  </div>
                  <div class="info-user ms-3">
                    <div class="username"><a target="_blank" href="<?php echo $admin_path."/user/".$user['username'] ?>" style="text-decoration: none;color:gray;"><?php echo $user['username'] ?></a></div>
                    <div class="status"><?php echo $user['name'] ?? "no name given" ?></div>
                  </div>
                  <button class="btn btn-icon btn-link op-8 me-1">
                    <i class="fa fa-search"></i>
                  </button>
                </div>
              <?php endforeach ; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <div class="card-title">Top Commented Shows</div>
          </div>
          <div class="card-body pb-0">
            <?php foreach($mcShows as $mc) :?>
              <div class="d-flex">
                <div class="avatar">
                  <img src="<?php echo $admin_path.$mc['image'] ?>" alt="show image" class="avatar-img rounded-circle" />
                </div>
                <div class="flex-1 pt-1 ms-2">
                  <h6 class="fw-bold mb-1"><a href="<?php echo $dynamicUrl."/s/".$mc['slug'] ?>" target="_blank" style="text-decoration: none;color:gray;" >
                    <?php echo $mc['name'] ?>
                  </a></h6>
                  <small class="text-muted"><?php echo $mc['imdb'] ?></small>
                </div>
                <div class="d-flex ms-auto align-items-center">
                  <h4 class="text-info fw-bold"><?php echo $mc['comment_count'] ?></h4>
                </div>
              </div>
              <div class="separator-dashed"></div>
            <?php endforeach; ?>
            
            <div class="pull-in">
              <canvas id="topProductsChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include './components/down-all.php' ?>