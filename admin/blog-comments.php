<?php 
$title = "BlogComments";

include './components/up-all.php' ?>

<?php
include '../actions/connect.php';
$comment_path = "/anime";

$jsonFile = __DIR__.'/../settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

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

if (isset($_GET['cquery']) && !empty($_GET['cquery'])) {
    $sanitizedComment = filter_var($_GET['cquery'], FILTER_SANITIZE_STRING);
    $whereClauses[] = "comment LIKE '%$sanitizedComment%'";
}

//* GET THE COUNT
if (!empty($whereClauses)) {
    $whereClause = implode(' AND ', $whereClauses);
    $query = "SELECT COUNT(*)
            FROM BlogComments c
            JOIN users u ON u.id=c.userId
            WHERE $whereClause";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $commentCount = $stmt->fetchColumn();
} else {
    $query = "SELECT COUNT(*) FROM BlogComments";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $commentCount = $stmt->fetchColumn();
}

if(isset($commentCount)){
    $divide = 10;
    $cpage_count = (int)ceil($commentCount / $divide);
    $commentPage = 1;
    if (isset($_GET['cpage']) && $_GET['cpage'] > 0 && $cpage_count >= $_GET['cpage']) {
        $commentPage = (int)$_GET['cpage'];
    }
    $take = $divide;
    $skipUser = (int)(($commentPage - 1) * $take);
}

//* GET ALL
if (!empty($whereClauses)) {
    $whereClause = implode(' AND ', $whereClauses);
    $query = "SELECT u.username as username,c.comment as comment,c.createdAt as createdAt,c.id as id
            FROM BlogComments c
            JOIN users u ON u.id=c.userId
            WHERE $whereClause
            LIMIT :take OFFSET :skip";
} else {
    $query = "SELECT u.username as username,c.comment as comment,c.createdAt as createdAt,c.id as id
    FROM BlogComments c
    JOIN users u ON u.id=c.userId
    LIMIT :take OFFSET :skip";
}

try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(":take",$take,PDO::PARAM_INT);
    $stmt->bindParam(":skip",$skipUser,PDO::PARAM_INT);
    $stmt->execute();

    // Fetch all records
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">BlogComments</h3>
                <h6 class="op-7 mb-2">Manage all the comments.</span></h6>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">BlogComments Table</div>
                </div>
                <div id="error-message" class="alert alert-danger mx-2 my-2 d-none" role="alert"></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th colspan="4">
                                        <input type="text" value="<?php echo isset($sanitizedComment) ? $sanitizedComment : '' ?>" id="commentSearch" placeholder="Search Comment" class="form-control" onkeydown="handleSearch(event, 'c')">
                                    </th>
                                    <th colspan="3">
                                        <input type="text" value="<?php echo isset($sanitizedUsername) ? $sanitizedUsername : '' ?>" id="userSearch" placeholder="Search Username" class="form-control" onkeydown="handleSearch(event, 'u')">
                                    </th>
                                </tr>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Comment Owner</th>
                                    <th scope="col" colspan="3">Comment</th>
                                    <th scope="col">Commented At</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($comments)) : ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; font-size: larger; font-weight: bold;">
                                            THERE IS NO Comment Found
                                        </td>
                                    </tr>
                                <?php else : ?>

                                    <?php foreach ($comments as  $index => $comment) : ?>

                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><a style="color:gray" target="_blank" href="<?php echo $dynamicUrl."/user/".$comment['username'] ?>"><?php echo $comment['username'] ?></a></td>
                                            <td colspan="3"><?php echo htmlspecialchars($comment['comment']); ?></td>
                                            <td><?php echo timeAgo($comment['createdAt']) ?></td>
                                            <td>
                                                <div class="icon-container">
                                                    <!-- DELETE Modal -->
                                                    <i class="fas fa-trash-alt icon-delete" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo htmlspecialchars($comment['id']); ?>"></i>
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
                if ($commentCount != 0) {
                    $max_pages_to_show = 5;
                    $total_pages = $cpage_count;
                    $current_page = $commentPage;

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
                <?php if ($commentCount != 0 && floor($commentCount / $divide) != 0 && !(floor($commentCount / $divide) == 1 && $commentCount % $divide == 0)) : ?>
                    <nav aria-label="Page navigation example" class="mx-2">
                        <ul class="pagination justify-content-end">
                            <?php if ($start > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $dynamicUrl . "/admin/blog-comments.php?cpage=1".(isset($_GET['uquery']) && !empty($_GET['uquery']) ? '&uquery='.$_GET['uquery'] : '').(isset($_GET['cquery']) && !empty($_GET['cquery']) ? '&cquery='.$_GET['cquery'] : '') ?>">1</a>
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
                                    <a class="page-link <?php echo ($i == $current_page) ? 'active' : '' ?>" style="<?php echo ($i == $current_page) ? 'color:white;' : '' ?>" href="<?php echo $dynamicUrl . "/admin/blog-comments.php?cpage=" . $i.(isset($_GET['uquery']) && !empty($_GET['uquery']) ? '&uquery='.$_GET['uquery'] : '').(isset($_GET['cquery']) && !empty($_GET['cquery']) ? '&cquery='.$_GET['cquery'] : '') ?>"><?php echo $i ?></a>
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
                                    <a class="page-link" href="<?php echo $dynamicUrl . "/admin/blog-comments.php?cpage=" . $total_pages.(isset($_GET['uquery']) && !empty($_GET['uquery']) ? '&uquery='.$_GET['uquery'] : '').(isset($_GET['cquery']) && !empty($_GET['cquery']) ? '&cquery='.$_GET['cquery'] : '')  ?>"><?php echo $total_pages ?></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
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
                <input type="hidden" id="scategory-id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="delete-button">Delete</button>
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
                query = document.getElementById('userSearch').value;
                break;
            case 'c':
                query = document.getElementById('commentSearch').value;
                break;
        }

        var url = new URL(window.location.href);
        url.searchParams.set(queryParam + 'query', query);
        window.location.href = url.href;
    }
}


    document.querySelectorAll('.icon-delete').forEach(button => {
        button.addEventListener('click', function() {
            var scategoryId = this.getAttribute('data-id');
            document.getElementById('scategory-id').value = scategoryId;
        });
    });

    document.getElementById('delete-button').addEventListener('click', function() {
        var scategoryId = document.getElementById('scategory-id').value;


        fetch('../actions/blog/delete-comment.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: scategoryId
                })
            })
            .then(response => response.json())
            .then(data => {
                const errorMessageElement = document.getElementById('error-message');

                if (data.status === 'success') {
                    window.location.reload();
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