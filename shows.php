<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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

if (isset($_GET['slug'])) {
    //* show path
    $show_path = "/anime";

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
        $query = "SELECT s.id as id,s.name as name,s.slug as slug,s.watchLink as watchLink,s.imdb as imdb, s.director as director,s.image as image,s.status as status,s.studio as studio,s.date_aired as date_aired, s.episode_count as episode_count,s.duration as duration,s.description as description,s.card_desc as card_desc,s.image as image,t.name as type
        FROM Shows s
        JOIN Types t ON t.id=s.typeId
        WHERE slug = :slug";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
        $stmt->execute();

        $show = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($show) {
            //*set title
            $title = $show['name'];
            $pageDescription = $show['card_desc'];

            //? SHOW CATEGORIES
            $query = "SELECT c.name as name 
            FROM ShowCategories sc
            JOIN Categories c ON sc.CategoryId = c.id
            WHERE showId=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $show['id']);
            $stmt->execute();
            $showCategories = $stmt->fetchAll();

            $namesArray = array_column($showCategories, 'name');
            $cnamesString = implode(',', $namesArray);

            //* YOU MIGHT LIKE
            $query = "SELECT s.name as name,s.id as id,s.image as image,s.slug as slug,s.imdb as imdb
            FROM SHOWS s
            ORDER BY RAND()
            LIMIT 5";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $ymlShows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($ymlShows as &$ss) {
                $showwwId = $ss['id'];

                $query = "SELECT COUNT(*) FROM Comments WHERE showId=:showId";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':showId', $showwwId);
                $stmt->execute();
                $commentsCount = $stmt->fetchColumn();

                $ss['commentCount'] = $commentsCount;
            }

            //* COMMENTS
            //? PAGINATION
            $query = "SELECT COUNT(*) FROM Comments WHERE showId=:showId";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':showId', $show['id']);
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
            FROM Comments c
            JOIN users u ON u.id=c.userId
            WHERE showId=:showId
            ORDER BY c.createdAt DESC
            LIMIT :take OFFSET :skip
            ";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':showId', $show['id']);
            $stmt->bindParam(':take', $take, PDO::PARAM_INT);
            $stmt->bindParam(':skip', $skipComment, PDO::PARAM_INT);
            $stmt->execute();
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            //* WATCH LATER CHECK
            $query = "SELECT id FROM WatchLater WHERE userId=:userId AND showId=:sid";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->bindParam(':sid', $show['id']);
            $stmt->execute();
            $is_fav = $stmt->fetchAll(PDO::FETCH_ASSOC);


            //* CHARACTERS
            $query = "SELECT COUNT(*) FROM Characters WHERE showId=:sid";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':sid', $show['id']);
            $stmt->execute();
            $charCohnt = $stmt->fetchColumn();

            $query = "SELECT * FROM Characters WHERE showId=:sid";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':sid', $show['id']);
            $stmt->execute();
            $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

include "./components/up-all.php";
?>

<link rel="stylesheet" href="/anime/css/image-card.css">

<div class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__links">
                    <a href="<?php echo $show_path . "/index.php" ?>"><i class="fa fa-home"></i> Home</a>
                    <a href="<?php echo $show_path . "/shows" ?>">Shows</a>
                    <span><?php echo $show['name'] ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="anime-details spad">
    <div class="container">
        <div class="anime__details__content">
            <div class="row">
                <div class="col-lg-3">
                    <div class="anime__details__pic set-bg" data-setbg="<?php echo $show_path . $show['image'] ?>">
                        <div class="comment"><i class="fa fa-comments"></i> <?php echo $commentsCount ?></div>
                        <div class="view"><i class="fa fa-users"></i> <?php echo $charCohnt ?></div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="anime__details__text">
                        <div class="anime__details__title">
                            <h3><?php echo $show['name'] ?></h3>
                            <?php if ($show['director']) : ?>
                                <span><?php echo $show['director'] ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="anime__details__rating">
                            <div class="rating">
                                <span><img width="60" height="22" src="<?php echo $show_path . "/img/imdb.webp" ?>" alt="imdb logo"></span>
                            </div>
                            <span><?php echo $show['imdb'] ?></span>
                        </div>
                        <p><?php echo $show['description'] ?></p>
                        <div class="anime__details__widget">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <ul>
                                        <li><span>Type:</span> <?php echo $show['type'] ?></li>
                                        <li><span>Rating:</span> <?php echo $show['imdb'] ?></li>
                                        <li><span>Date :</span> <?php echo $show['date_aired'] ?></li>
                                        <?php if (!empty($show['status'])) : ?>
                                            <li><span>Status:</span> <?php echo $show['status'] ?></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                    <ul>
                                        <li><span>Genre:</span> <?php echo $cnamesString ?></li>
                                        <li><span>Studios:</span> <?php echo $show['studio'] ?? "" ?></li>
                                        <li><span>Duration:</span> <?php echo $show['duration'] ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="anime__details__btn">
                            <?php if (isset($token) && !empty($token)) : ?>
                                <?php if(!empty($is_fav)) : ?>
                                    <a href="<?php echo $show_path."/watch-later.php" ?>" class="follow-btn"
                                        data-id="<?php echo $show['slug']; ?>">
                                        <i class="fa fa-heart-o"></i> Watch Later
                                    </a>
                                <?php else : ?>
                                    <a href="#" class="follow-btn favorite-button"
                                        data-id="<?php echo $show['slug']; ?>">
                                        <i class="fa fa-heart-o"></i> Watch Later
                                    </a>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                            <?php if (!empty($show['watchLink'])) : ?>
                                <a target="_blank" href="<?php echo $show['watchLink'] ?>" class="watch-btn"><span>Watch Now</span> <i
                                        class="fa fa-angle-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 col-md-8">
                <div class="row mb-3">
                    <?php foreach($characters as $cssq) : ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="profile-card-4 text-center"><img src="<?php echo $show_path.$cssq['image'] ?>">
                                <div class="profile-content">
                                    <div class="profile-name">
                                        <h6><?php echo $cssq['name'] ?></h6>
                                        <p>@<?php echo $cssq['starring'] ?></p>
                                    </div>
                                    <div class="profile-description"><?php echo $cssq['description'] ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ; ?>
                </div>
                <div class="anime__details__review">
                    <div class="section-title">
                        <h5><?php echo count($comments) != 0 ? "Reviews "."(".$total_comments.")" : 'There is no review yet' ?></h5>
                    </div>
                    <?php foreach ($comments as $comment) : ?>
                        <div class="anime__review__item">
                            <div class="anime__review__item__pic">
                                <img src="<?php echo (isset($comment['userimage']) && !empty($comment['userimage'])) ? $show_path . $comment['userimage']  : $show_path."/img/defuser.png"  ?>" alt="user image">
                            </div>
                            <div class="anime__review__item__text">
                                <h6><a href="<?php echo $show_path."/user/".$comment['username'] ?>" style="text-decoration: none;color:white;"><?php echo $comment['username'] ?></a> - <span><?php echo timeAgo($comment['createdAt']) ?></span></h6>
                                <p><?php echo $comment['comment'] ?></p>
                            </div>
                            <div class="col-lg-12">
                                <?php if (isset($result) && !empty($result) && in_array('ADMIN', $rolee)): ?>
                                    <button type="button" style="justify-content:center; display: block; margin: 0 auto;" class="btn btn-danger button-delete" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo $comment['id'] ?>">Delete The Review</button>
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
                                <a href="<?php echo $dynamicUrl . "/s/" . $show['slug'] . "?cpage=1" ?>">1</a>
                                <?php if ($start > 2) : ?>
                                    <a href="#"><span>...</span></a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <!-- Middle pages -->
                            <?php for ($i = $start; $i <= $end; $i++) : ?>
                                <a class="<?php echo ($i == $current_page) ? 'current-page' : '' ?>" href="<?php echo $dynamicUrl . "/s/" . $show['slug'] . "?cpage=" . $i ?>"><?php echo $i ?></a>
                            <?php endfor; ?>
                            <!-- Last page -->
                            <?php if ($end < $total_pages) : ?>
                                <?php if ($end < $total_pages - 1) : ?>
                                    <a href="#"><span>...</span></a>
                                <?php endif; ?>
                                <a href="<?php echo $dynamicUrl . "/s/" . $show['slug'] . "?cpage=" . $total_pages  ?>"><?php echo $total_pages ?></a>
                            <?php endif; ?>
                            <?php if ($current_page != $total_pages) : ?>
                                <a href="<?php echo $dynamicUrl . "/s/" . $show['slug'] . "?cpage=" . ($current_page + 1) ?>"><i class="fa fa-angle-double-right"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($loggedIn) : ?>
                    <div class="anime__details__form">
                        <div class="section-title">
                            <h5>Your Comment</h5>
                        </div>
                        <form id="comment-form">
                            <textarea placeholder="Your Comment" id="comment"></textarea>
                            <input type="hidden" name="username" id="username" value="<?php echo $userNm ?>">
                            <input type="hidden" name="showSlug" id="showSlug" value="<?php echo $show['slug'] ?>">
                            <div class="col-lg-12">
                                <span id="error-message" class="text-danger mb-2 d-none"></span>
                            </div>
                            <button type="submit"><i class="fa fa-location-arrow"></i> Review</button>
                        </form>
                    </div>
                <?php else : ?>
                    <div class="anime__details__form">
                        <div class="section-title">
                            <h5>If you want to comment first <a style="color:darkred;" href="<?php echo $show_path . "/login.php" ?>">Login</a></h5>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <div class="anime__details__sidebar">
                    <div class="section-title">
                        <h5>you might like...</h5>
                    </div>
                    <?php foreach ($ymlShows as $sshow) : ?>
                        <div class="product__sidebar__view__item set-bg" data-setbg="<?php echo $show_path . $sshow['image'] ?>">
                            <div class="ep"><?php echo $sshow['imdb'] ?></div>
                            <div class="view"><i class="fa fa-comment"></i> <?php echo $sshow['commentCount'] ?></div>
                            <h5><a href="<?php echo $dynamicUrl . "/s/" . $sshow['slug'] ?>"><?php echo $sshow['name'] ?></a></h5>
                        </div>
                    <?php endforeach; ?>
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

            fetch('/anime/actions/show/delete-comment.php', {
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
            var showSlug = document.getElementById('showSlug').value;

            var data = {
                username: username,
                showSlug: showSlug,
                comment: commentTextarea,
            };

            fetch('/anime/actions/show/add-comment.php', {
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

        document.addEventListener("DOMContentLoaded", (function() {
            document.querySelectorAll(".favorite-button").forEach((function(e) {
                e.addEventListener("click", (function(e) {
                    e.preventDefault();
                    var t = {
                        slug: this.getAttribute("data-id")
                    };
                    fetch("/anime/actions/common/add-favs.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(t)
                    }).then((e => e.json())).then((e => {
                        "success" === e.status ? window.location.reload() : console.log(e.message)
                    })).catch((e => {
                        console.error("Error:", e)
                    }))
                }))
            }))
        }));
    </script>
<?php endif; ?>

<?php include "./components/down-all.php" ?>