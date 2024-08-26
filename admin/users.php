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

try {

    $query = "SELECT id,username,name,image,bannerImage,createdAt FROM users";
    $stmt = $db->prepare($query);
    $stmt->execute();

    // Fetch all records
    $userss = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                                    <th scope="col">#</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">User Image</th>
                                    <th scope="col">User Banner</th>
                                    <th scope="col">Joined</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($userss)) : ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; font-size: larger; font-weight: bold;">
                                            THERE IS NO User YET
                                        </td>
                                    </tr>
                                <?php else : ?>

                                    <?php foreach ($userss as  $index => $user) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td>
                                                <?php if(isset($user['image']) && !empty($user['image'])) : ?>
                                                    <a target="_blank" href="<?php echo $admin_users_path.$user['image'] ?>">
                                                        <?php echo $user['username']."'s image" ?>
                                                    </a>
                                                <?php else : ?>
                                                    <span>No Image Set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(isset($user['bannerImage']) && !empty($user['bannerImage'])) : ?>
                                                    <a target="_blank" href="<?php echo $admin_users_path.$user['bannerImage'] ?>">
                                                        <?php echo $user['username']."'s banner image" ?>
                                                    </a>
                                                <?php else : ?>
                                                    <span>No Banner Image Set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo timeAgo($user['createdAt']) ?></td>
                                            <td>
                                                <div class="icon-container">
                                                    <a href="./manage/edit-user.php?id=<?php echo htmlspecialchars($user['id']); ?>"><i class="fas fa-edit icon-edit" title="Edit"></i></a>
                                                    <a href="./manage/give-role.php?id=<?php echo htmlspecialchars($user['id']); ?>"><i class="fas fa-user icon-role" title="give role"></i></a>
                                                    <!-- BLOCK Modal -->
                                                    <i class="fas fa-ban icon-block" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo htmlspecialchars($user['id']); ?>"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this item?
                <input type="hidden" id="user-id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="delete-button">Delete</button>
            </div>
        </div>
    </div>
</div>



<script>
document.querySelectorAll('.icon-block').forEach(button => {
    button.addEventListener('click', function() {
        var userId = this.getAttribute('data-id');
        document.getElementById('user-id').value = userId;
    });
});

document.getElementById('delete-button').addEventListener('click', function() {
    var userId = document.getElementById('user-id').value;


    fetch('../actions/admin/Users.php', {
            method: 'DELETE',
            headers: {
                'Content-User': 'application/json'
            },
            body: JSON.stringify({ id: userId })
        })
        .then(response => response.json())
        .then(data => {
            const errorMessageElement = document.getElementById('error-message');

            if (data.status === 'success') {
                window.location.href = './Users.php';
            } else {
                errorMessageElement.textContent = data.message;
                errorMessageElement.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
});
</script>

<?php include './components/down-all.php' ?>