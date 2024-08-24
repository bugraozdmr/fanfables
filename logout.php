<?php
if (isset($_COOKIE['auth_token'])) {
    setcookie('auth_token', '', time() - 3600, '/');
    
    unset($_COOKIE['auth_token']);

    header("Location: /anime/index.php");
    exit();
} else {
    header("Location: /anime/index.php");
    exit();
}
