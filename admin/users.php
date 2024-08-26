<?php
$title = "Users";
include './components/up-all.php' ?>

<?php
include '../actions/connect.php';
$admin_users_path = "/anime";
function timeAgo($dateTime)
{
    // HARBI GELECEGE DONUS OLDU
    $timezone = new DateTimeZone('Europe/Istanbul');
    $now = new DateTime('now', $timezone);
    $date = new DateTime($dateTime, $timezone);
    $interval = $now->diff($date);

    if ($date > $now) {
        return $date->format('d.m.Y H:i:s');
    }

    if ($interval->y > 0) {
        return $interval->y . ' years ago';
    } elseif ($interval->m > 0) {
        return $interval->m . ' months ago';
    } elseif ($interval->d > 0) {
        return $interval->d . ' days ago';
    } elseif ($interval->h > 0) {
        return $interval->h . ' hours ago';
    } elseif ($interval->i > 0) {
        return $interval->i . ' minutes ago';
    } else {
        return 'Now';
    }
}

$whereClauses = [];
if (isset($_GET['uquery']) && !empty($_GET['uquery'])) {
    $sanitizedUsername = filter_var($_GET['uquery'], FILTER_SANITIZE_STRING);
    $whereClauses[] = "username LIKE '%$sanitizedUsername%'";
}

if (isset($_GET['nquery']) && !empty($_GET['nquery'])) {
    $sanitizedName = filter_var($_GET['nquery'], FILTER_SANITIZE_STRING);
    $whereClauses[] = "name LIKE '%$sanitizedName%'";
}

if (isset($_GET['dquery']) && !empty($_GET['dquery'])) {
    $sanitizedDesc = filter_var($_GET['dquery'], FILTER_SANITIZE_STRING);
    $whereClauses[] = "description LIKE '%$sanitizedDesc%'";
}

//* GET THE COUNT
if (!empty($whereClauses)) {
    $whereClause = implode(' AND ', $whereClauses);
    $query = "SELECT COUNT(*) 
            FROM users 
            WHERE $whereClause";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
} else {
    $query = "SELECT COUNT(*) FROM users";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
}

if(isset($userCount)){
    $divide = 1;
    $upage_count = (int)ceil($userCount / $divide);
    $userPage = 1;
    if (isset($_GET['upage']) && $_GET['upage'] > 0 && $upage_count >= $_GET['upage']) {
        $userPage = (int)$_GET['upage'];
    }
    $take = $divide;
    $skipUser = (int)(($userPage - 1) * $take);
}

//* GET ALL
if (!empty($whereClauses)) {
    $whereClause = implode(' AND ', $whereClauses);
    $query = "SELECT id, username, name, image, email, bannerImage, createdAt 
            FROM users 
            WHERE $whereClause
            LIMIT :take OFFSET :skip";
} else {
    $query = "SELECT id, username, name, image, email, bannerImage, createdAt FROM users LIMIT :take OFFSET :skip";
}


try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(":take",$take,PDO::PARAM_INT);
    $stmt->bindParam(":skip",$skipUser,PDO::PARAM_INT);
    $stmt->execute();
    // Fetch all records
    $userss = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($userss as &$usr) {
        $ide = $usr['id'];

        $query = "SELECT until 
        FROM UserBans 
        WHERE userId=:uid
        ORDER BY until DESC
        LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":uid", $ide);
        $stmt->execute();
        $uu = $stmt->fetch();

        if (isset($uu['until'])) {
            $usr['until'] = $uu['until'];
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Users</h3>
                <h6 class="op-7 mb-2">Manage all the Users</h6>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Users Table</div>
                    <a href="./manage/Users.php">
                        <button class="btn btn-primary add-color-btn" data-bs-toggle="modal" data-bs-target="#adduserModal">
                            <i class="fas fa-plus"></i> Add User
                        </button>
                    </a>
                </div>
                <div id="error-message" class="alert alert-danger mx-2 my-2 d-none" role="alert"></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th colspan="3">
                                        <input type="text" value="<?php echo isset($sanitizedUsername) ? $sanitizedUsername : '' ?>" id="usernameSearch" placeholder="Search Username" class="form-control" onkeydown="handleSearch(event, 'u')">
                                    </th>
                                    <th colspan="3">
                                        <input type="text" id="nameSearch" placeholder="Search Name" class="form-control" value="<?php echo isset($sanitizedName) ? $sanitizedName : '' ?>" onkeydown="handleSearch(event, 'n')">
                                    </th>
                                    <th colspan="3">
                                        <input type="text" value="<?php echo isset($sanitizedDesc) ? $sanitizedDesc : '' ?>" id="descriptionSearch" placeholder="Search Description" class="form-control" onkeydown="handleSearch(event, 'd')">
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">User Image</th>
                                    <th scope="col">User Banner</th>
                                    <th scope="col">Joined</th>
                                    <th scope="col">Active Ban</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($userss)) : ?>
                                    <tr>
                                        <td colspan="9" style="text-align: center; font-size: larger; font-weight: bold;">
                                            THERE IS NO User YET
                                        </td>
                                    </tr>
                                <?php else : ?>

                                    <?php foreach ($userss as  $index => $user) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><a style="text-decoration: none;color:gray;" href="<?php echo $admin_users_path."/user/".$user['username'] ?>"><?php echo $user['username'] ?></a></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <?php if (isset($user['image']) && !empty($user['image'])) : ?>
                                                    <a style="color: chocolate;" target="_blank" href="<?php echo $admin_users_path . $user['image'] ?>">
                                                        <?php echo $user['username'] . "'s image" ?>
                                                    </a>
                                                <?php else : ?>
                                                    <span>No Image Set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($user['bannerImage']) && !empty($user['bannerImage'])) : ?>
                                                    <a style="color:burlywood" target="_blank" href="<?php echo $admin_users_path . $user['bannerImage'] ?>">
                                                        <?php echo $user['username'] . "'s banner image" ?>
                                                    </a>
                                                <?php else : ?>
                                                    <span>No Banner Image Set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo timeAgo($user['createdAt']) ?></td>
                                            <td>
                                                <?php
                                                $istanbulTimeZone = new DateTimeZone('Europe/Istanbul');
                                                $now = new DateTime('now', $istanbulTimeZone);
                                                if (isset($user['until']) && !empty($user['until'])) {
                                                    $until = new DateTime($user['until'], $istanbulTimeZone);
                                                    $interval = $now->diff($until);
                                                    if ($until > $now) {
                                                        $days = $interval->days;
                                                        $hours = $interval->h;
                                                        $minutes = $interval->i;
                                                        echo ($days > 0 ? $days . " days " : "");
                                                        echo ($hours > 0 ? $hours . " hours " : "");
                                                        echo ($minutes > 0 ? $minutes . " minutes" : "");

                                                        $check_exist = 0;
                                                    } else {
                                                        $check_exist = 1;
                                                        echo "No";
                                                    }
                                                } else {
                                                    // no active ban
                                                    $check_exist = 1;
                                                    echo "No";
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="icon-container">
                                                    <a href="./manage/edit-user.php?id=<?php echo htmlspecialchars($user['id']); ?>"><i class="fas fa-edit icon-edit" title="Edit"></i></a>
                                                    <a class="text-warning" href="./manage/give-role.php?id=<?php echo htmlspecialchars($user['id']); ?>"><i class="fas fa-user icon-role" title="give role"></i></a>
                                                    <?php if (isset($check_exist) && $check_exist == 1) : ?>
                                                        <a class="text-danger" href="./manage/ban-user.php?id=<?php echo htmlspecialchars($user['id']); ?>"><i class="fas fa-ban icon-ban" title="ban user"></i></a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                if ($userCount != 0) {
                    $max_pages_to_show = 5;
                    $total_pages = $upage_count;
                    $current_page = $userPage;

                    $start = max(1, $current_page - floor($max_pages_to_show / 2));
                    $end = min($total_pages, $current_page + floor($max_pages_to_show / 2));

                    if ($end - $start + 1 < $max_pages_to_show) {
                        if ($start == 1) {
                            $end = min($total_pages, $end + ($max_pages_to_show - ($end - $start + 1)));
                        } else {
                            $start = max(1, $start - ($max_pages_to_show - ($end - $start + 1)));
                        }
                    }
                }
                ?>
                <?php if ($userCount != 0 && floor($userCount / $divide) != 0 && !(floor($userCount / $divide) == 1 && $userCount % $divide == 0)) : ?>
                    <nav aria-label="Page navigation example" class="mx-2">
                        <ul class="pagination justify-content-end">
                            <?php if ($start > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $dynamicUrl . "/admin/users.php?upage=1".(isset($_GET['uquery']) && !empty($_GET['uquery']) ? '&uquery='.$_GET['uquery'] : '').(isset($_GET['nquery']) && !empty($_GET['nquery']) ? '&nquery='.$_GET['nquery'] : '').(isset($_GET['dquery']) && !empty($_GET['dquery']) ? '&dquery='.$_GET['dquery'] : '') ?>">1</a>
                                </li>
                                <?php if ($start > 2) : ?>
                                    <li class="page-item">
                                        <a class="page-link" href="#"><span>...</span></a>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- Middle pages -->
                            <?php for ($i = $start; $i <= $end; $i++) : ?>
                                <li class="page-item">
                                    <a class="page-link <?php echo ($i == $current_page) ? 'active' : '' ?>" style="<?php echo ($i == $current_page) ? 'color:white;' : '' ?>" href="<?php echo $dynamicUrl . "/admin/users.php?upage=" . $i.(isset($_GET['uquery']) && !empty($_GET['uquery']) ? '&uquery='.$_GET['uquery'] : '').(isset($_GET['nquery']) && !empty($_GET['nquery']) ? '&nquery='.$_GET['nquery'] : '').(isset($_GET['dquery']) && !empty($_GET['dquery']) ? '&dquery='.$_GET['dquery'] : '') ?>"><?php echo $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <!-- Last page -->
                            <?php if ($end < $total_pages) : ?>
                                <?php if ($end < $total_pages - 1) : ?>
                                    <li class="page-item">
                                        <a class="page-link" href="#"><span>...</span></a>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $dynamicUrl . "/admin/users.php?upage=" . $total_pages.(isset($_GET['uquery']) && !empty($_GET['uquery']) ? '&uquery='.$_GET['uquery'] : '').(isset($_GET['nquery']) && !empty($_GET['nquery']) ? '&nquery='.$_GET['nquery'] : '').(isset($_GET['dquery']) && !empty($_GET['dquery']) ? '&dquery='.$_GET['dquery'] : '')  ?>"><?php echo $total_pages ?></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function handleSearch(event, queryParam) {
    if (event.key === 'Enter') {
        event.preventDefault();
        var query = '';
        
        switch (queryParam) {
            case 'u':
                query = document.getElementById('usernameSearch').value;
                break;
            case 'n':
                query = document.getElementById('nameSearch').value;
                break;
            case 'd':
                query = document.getElementById('descriptionSearch').value;
                break;
        }

        var url = new URL(window.location.href);
        url.searchParams.set(queryParam + 'query', query);
        window.location.href = url.href;
    }
}
</script>

<?php include './components/down-all.php' ?>