<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


if (isset($_GET['slug'])) {
    //* blog path
    $blog_path = "/anime";

    $slug = $_GET['slug'];

    $jsonFile = __DIR__ . '/settings.json';
    $jsonData = file_get_contents($jsonFile);
    $data = json_decode($jsonData, true);
    $dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

    $key = isset($data['key']) ? $data['key'] : '';

    require __DIR__ . '/vendor/autoload.php';

    //* CONNECT
    include __DIR__ . "/actions/connect.php";

    $loggedIn = true;
    if (isset($_COOKIE['auth_token'])) {
        $token = $_COOKIE['auth_token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $username = $decoded->sub;

        $query = "SELECT id,username FROM users WHERE username=:username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch();

        //* USERID
        $userId = $result['id'];
        $userNm = $result['username'] ?? null;

        $query = "SELECT r.normalized_name AS role
              FROM UserRoles ur
              INNER JOIN roles r ON ur.RoleId = r.id
              WHERE ur.UserId = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $rolee = [];
        foreach ($result as $row) {
            $rolee[] = $row['role'];
        }
    } else {
        $loggedIn = false;
    }


    try {
        $query = "SELECT * FROM Blog where slug=:slug
        ";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        $blog = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($blog) {
            //*set title
            $title = $blog['title'];
            $pageDescription = $blog['card_desc'];

            //? blog CATEGORIES
            $query = "SELECT c.name as name 
            FROM BlogCategories bc
            JOIN Categories c ON bc.CategoryId = c.id
            WHERE blogId=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $blog['id']);
            $stmt->execute();
            $blogCategories = $stmt->fetchAll();

            $namesArray = array_column($blogCategories, 'name');
            $cnamesString = implode(',', $namesArray);
        } else {
            // TODO CHANGE 404
            header('Location: /anime/404.php');
            exit();
        }
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
        exit();
    }
} else {
    header('Location: /anime/404.php');
    exit();
}

//* COMMENTS
//? PAGINATION
$query = "SELECT COUNT(*) FROM BlogComments WHERE blogId=:blogId";
$stmt = $db->prepare($query);
$stmt->bindParam(':blogId', $blog['id']);
$stmt->execute();
$commentsCount = $stmt->fetchColumn();

$total_comments = $commentsCount;

$cpage_count = (int)ceil($total_comments / 6);
$commentPage = 1;
if (isset($_GET['cpage']) && $_GET['cpage'] > 0 && $cpage_count >= $_GET['cpage']) {
    $commentPage = (int)$_GET['cpage'];
}
$take = 6;
$skipComment = (int)(($commentPage - 1) * $take);


$query = "SELECT c.id as id,c.comment as comment,u.username as username,u.image as userimage,c.createdAt as createdAt
FROM BlogComments c
JOIN users u ON u.id=c.userId
WHERE blogId=:blogId
ORDER BY c.createdAt DESC
LIMIT :take OFFSET :skip
";
$stmt = $db->prepare($query);
$stmt->bindParam(':blogId', $blog['id']);
$stmt->bindParam(':take', $take, PDO::PARAM_INT);
$stmt->bindParam(':skip', $skipComment, PDO::PARAM_INT);
$stmt->execute();
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($comments as &$comment23){
    $usrr = $comment23['username'];
    $query = "SELECT
        r.normalized_name AS role
        FROM UserRoles ur
        JOIN users u ON u.id=ur.UserId
        JOIN roles r ON r.id=ur.RoleId
        WHERE u.username=:username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $usrr);
    $stmt->execute();
    $result11 = $stmt->fetchAll();

    $rolee = [];
    foreach ($result11 as $row) {
        $rolee[] = $row['role'];
    }

    $comment23['roles']= $rolee;
}

//RANDOM BLOGS
$sql = "SELECT image, title, createdAt, slug FROM Blog ORDER BY RAND() LIMIT 6";
$stmt = $db->prepare($sql);
$stmt->execute();
$randomBlogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . "/components/up-all.php";

?>

<section class="blog-details spad">
    <div class="container">
        <div class="row d-flex justify-content-center">
            <div class="col-lg-8">
                <div class="blog__details__title">
                    <h6><?php echo $cnamesString ?> <span>- <?php
                        $date = new DateTime($blog['createdAt']);
                        $formattedDate = $date->format('F d, Y');
                        echo $formattedDate;
                        ?>
                        </span></h6>
                    <h2><?php echo $blog['title'] ?></h2>
                    <div class="blog__details__social">
                    <a href="#" class="facebook" onclick="shareOnFacebook()" target="_blank">
                        <i class="fa fa-facebook-square"></i> Facebook
                    </a>
                    
                    <a href="#" class="pinterest" onclick="shareOnPinterest()" target="_blank">
                        <i class="fa fa-pinterest"></i> Pinterest
                    </a>
                    
                    <a href="#" class="linkedin" onclick="shareOnLinkedIn()" target="_blank">
                        <i class="fa fa-linkedin-square"></i> Linkedin
                    </a>
                    
                    <a href="#" class="twitter" onclick="shareOnTwitter()" target="_blank">
                        <i class="fa fa-twitter-square"></i> Twitter
                    </a>
                </div>

                </div>
            </div>
            <div class="col-lg-12">
                <div class="blog__details__pic">
                    <img width="1170" height="600" src="<?php echo isset($blog['image']) ? $blog_path . "/" . $blog['image'] : '' ?>" alt="blog banner image">
                </div>
            </div>
            <div class="col-lg-8">
                <div class="blog__details__content">
                    <div class="blog__details__text">
                        <?php echo $blog['content'] ?>
                    </div>
                    <div class="blog__details__tags">
                        <?php 
                        // Dizeyi al
                        $altText = $blog['alt'];
                        $items = explode(',', $altText);
                        foreach ($items as $item) {
                            $uppercaseItem = strtoupper($item);
                            echo "<a href='#' title='$uppercaseItem'>$uppercaseItem</a> ";
                        }
                        ?>
                    </div>
                    <div class="blog__details__btns">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="blog__details__btns__item">
                                    <h5><a href="<?php echo $blog_path."/b/".$randomBlogs[0]['slug'] ?>"><span class="arrow_left"></span> <?php echo $randomBlogs[0]['title'] ?></a>
                                    </h5>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="blog__details__btns__item next__btn">
                                <h5><a href="<?php echo $blog_path."/b/".$randomBlogs[1]['slug'] ?>"><span class="arrow_right"></span> <?php echo $randomBlogs[1]['title'] ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="random__blogs">
                        <div class="row">
                            <?php for($i=2;$i<6;$i++) : ?>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <div class="product__sidebar__view__item set-bg mix month week"
                                        data-setbg="<?php echo $blog_path.$randomBlogs[$i]['image'] ?>">
                                        <div class="ep">
                                        <h6><i class="fa fa-calendar" aria-hidden="true"></i>
                                        <?php
                                        $date = new DateTime($randomBlogs[$i]['createdAt']);
                                        $formattedDate = $date->format('F d, Y');
                                        echo $formattedDate;
                                        ?>
                                        </span></h6>
                                        </div>
                                        <h5><a href="<?php echo $blog_path."/b/".$randomBlogs[$i]['slug'] ?>"><?php echo $randomBlogs[$i]['title'] ?></a></h5>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="blog__details__comment">
                        <h4><?php echo $total_comments ?> Comments</h4>
                        <?php foreach ($comments as $comment) : ?>
                            <div class="blog__details__comment__item">
                                <div class="blog__details__comment__item__pic">
                                    <img width="70" height="70" src="<?php echo (isset($comment['userimage']) && !empty($comment['userimage'])) ? $blog_path . $comment['userimage']  : $blog_path."/img/defuser.png" ?>" alt="user image" style=" border-radius: 50%;object-fit: cover;">
                                </div>
                                <div class="blog__details__comment__item__text">
                                    <span>
                                    <?php
                                        $date = new DateTime($comment['createdAt']);
                                        $formattedDate = $date->format('F d, Y');
                                        echo $formattedDate;
                                    ?>
                                    </span>
                                    <h5>
                                    <a href="<?php echo $blog_path."/user/".$comment['username'] ?>" style="text-decoration: none; color: white; <?php echo (isset($comment['roles']) && !empty($comment['roles'] && in_array('ADMIN',$comment['roles']) ) ? 'background: linear-gradient(45deg, rgba(255, 0, 0, 0.5), rgba(0, 255, 0, 0.5), rgba(0, 0, 255, 0.5), rgba(255, 255, 0, 0.5)); padding: 10px; border-radius: 5px; display: inline-block;' : '') ?>">
                                        <?php echo $comment['username'] ?>
                                    </a>
                                    </h5>
                                    <p>
                                        <?php echo $comment['comment'] ?>
                                    </p>
                                    <?php if (isset($result) && !empty($result) && in_array('ADMIN', $rolee)): ?>
                                        <a href="#"
                                        type="button" style="justify-content:center; display: block; margin: 0 auto;" class="btn btn-danger button-delete" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $comment['id'] ?>"
                                        >Remove <i class="fa fa-trash"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php
                        if ($total_comments != 0) {
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
                        <?php if ($total_comments != 0 && floor($total_comments / 6) != 0 && !(floor($total_comments / 6) == 1 && $total_comments % 6 == 0)) : ?>
                            <div class="product__pagination">
                                <?php if ($start > 1) : ?>
                                    <a href="<?php echo $dynamicUrl . "/b/" . $blog['slug'] . "?cpage=1" ?>">1</a>
                                    <?php if ($start > 2) : ?>
                                        <a href="#"><span>...</span></a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <!-- Middle pages -->
                                <?php for ($i = $start; $i <= $end; $i++) : ?>
                                    <a class="<?php echo ($i == $current_page) ? 'current-page' : '' ?>" href="<?php echo $dynamicUrl . "/b/" . $blog['slug'] . "?cpage=" . $i ?>"><?php echo $i ?></a>
                                <?php endfor; ?>
                                <!-- Last page -->
                                <?php if ($end < $total_pages) : ?>
                                    <?php if ($end < $total_pages - 1) : ?>
                                        <a href="#"><span>...</span></a>
                                    <?php endif; ?>
                                    <a href="<?php echo $dynamicUrl . "/b/" . $blog['slug'] . "?cpage=" . $total_pages  ?>"><?php echo $total_pages ?></a>
                                <?php endif; ?>
                                <?php if ($current_page != $total_pages) : ?>
                                    <a href="<?php echo $dynamicUrl . "/b/" . $blog['slug'] . "?cpage=" . ($current_page + 1) ?>"><i class="fa fa-angle-double-right"></i></a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($loggedIn) : ?>
                        <div class="blog__details__form">
                        <h4>Leave A Commnet</h4>
                            <form id="comment-form">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <textarea placeholder="Your Comment" id="comment"></textarea>
                                        <input type="hidden" name="username" id="username" value="<?php echo $userNm ?>">
                                        <input type="hidden" name="blogSlug" id="blogSlug" value="<?php echo $blog['slug'] ?>">
                                        <div class="col-lg-12">
                                            <span id="error-message" class="text-danger mb-2 d-none"></span>
                                        </div>
                                        <button type="submit" class="site-btn">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else : ?>
                        <div class="blog__details__form">
                            <h4><a style="color:darkgoldenrod" href="<?php echo $blog_path."/login.php" ?>">Login</a> to Drop A Comment</h4>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<?php if (isset($result) && !empty($result)  && in_array('ADMIN', $rolee)): ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this item?
                    <input type="hidden" id="comment-id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="delete-button">Delete</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<?php if (isset($result) && !empty($result)  && in_array('ADMIN', $rolee)): ?>
    <script>
        // HIDDEN SCRIPT REVIEW
        document.querySelectorAll('.button-delete').forEach(button => {
            button.addEventListener('click', function() {
                var reviewId = this.getAttribute('data-id');
                console.log(reviewId);
                document.getElementById('comment-id').value = reviewId;
            });
        });

        document.getElementById('delete-button').addEventListener('click', function() {
            var reviewId = document.getElementById('comment-id').value;

            fetch('/anime/actions/blog/delete-comment.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: reviewId
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
<?php endif; ?>

<?php if ($loggedIn) : ?>
<script>
    document.getElementById('comment-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var commentTextarea = document.getElementById('comment').value;
        var username = document.getElementById('username').value;
        var blogSlug = document.getElementById('blogSlug').value;

        var data = {
            username: username,
            blogSlug: blogSlug,
            comment: commentTextarea,
        };

        fetch('/anime/actions/blog/add-comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                const errorMessageElement = document.getElementById('error-message');

                if (data.status === 'success') {
                    setTimeout(() => {
                        window.location.reload();
                    }, 0);
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
<?php endif; ?>

<script>
    var shareUrl = "<?php echo $dynamicUrl . '/b/' . $blog['slug']; ?>";
    var shareText = "Check out this blog!";

    function shareOnFacebook() {
        var url = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(shareUrl);
        window.open(url, '_blank');
    }

    function shareOnPinterest() {
        var url = "https://pinterest.com/pin/create/button/?url=" + encodeURIComponent(shareUrl) + "&description=" + encodeURIComponent(shareText);
        window.open(url, '_blank');
    }

    function shareOnLinkedIn() {
        var url = "https://www.linkedin.com/sharing/share-offsite/?url=" + encodeURIComponent(shareUrl);
        window.open(url, '_blank');
    }

    function shareOnTwitter() {
        var url = "https://twitter.com/intent/tweet?url=" + encodeURIComponent(shareUrl) + "&text=" + encodeURIComponent(shareText);
        window.open(url, '_blank');
    }
</script>

<?php include __DIR__ . "/components/down-all.php" ?>