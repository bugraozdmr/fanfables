<?php
$title = "Reset Password";
$login_path = "/anime/";

if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];
}

if (isset($token)) {
    header('Location: index.php');
    exit();
}

$message = "Token Not Exists";

$tokenFound = false;
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    include './actions/connect.php';

    try {
        $query = "SELECT * FROM forgotTokenUser where token=:token";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!empty($result)) {
            $tokenCreatedAt = $result['createdAt'];

            // (24 hours)
            $tokenCreatedAtTime = new DateTime($tokenCreatedAt);
            $currentTime = new DateTime();
            $interval = $currentTime->diff($tokenCreatedAtTime);
            $hours = ($interval->days * 24) + $interval->h;

            if ($hours > 24) {
                $message = "Token Not Found";
            } else {
                $tokenFound = true;
            }
        } else {
            $message = "Token Not Found";
        }
    } catch (\Exception $e) {
        $message = "Something went wrong !.";
    }
}

include __DIR__ . "/components/up-all.php"
?>

<section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    <h2>Reset Password</h2>
                    <p>"The things you own end up owning you."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($tokenFound): ?>
    <section class="login spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Reset Password</h3>
                        <form id="login-form">
                            <div class="input__item">
                                <input type="text" id="password" placeholder="New Password">
                                <span class="icon_mail"></span>
                            </div>
                            <input id="token" type="hidden" value="<?php echo $tokenFound ? $token : '' ?>">
                            <div class="input__item">
                                <input type="password" id="confirm_password" placeholder="New Password Confirm">
                                <span class="icon_lock"></span>
                            </div>
                            <div class="col-lg-12">
                                <span id="error-message" class="text-danger d-none"></span>
                                <span id="success-message" class="text-success d-none"></span>
                            </div>
                            <button type="submit" class="site-btn">Send</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login__register">
                        <h3>Next time be more carefull ok?</h3>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php else: ?>
    <div style="height: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto;">
        <p class="text-center text-danger" style="font-size: 1.5rem;"><?php echo $message; ?></p>
    </div>
<?php endif; ?>

<script>
    document.getElementById('send-reset-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var password = document.getElementById('password').value;
        var token = document.getElementById('token').value;

        var confirm_password = document.getElementById('confirm_password').value;


        if (confirm_password !== password) {
            const errorMessageElement = document.getElementById('error-message');

            errorMessageElement.textContent = "Password and Confirm Passwors Not Matching";
            errorMessageElement.classList.remove('d-none');
        } else {
            var unsafeCharacters = /[<>'"]/;
            var scriptTags = /<script.*?>.*?<\/script>/i;


            if (unsafeCharacters.test(password) || scriptTags.test(password)) {
                const errorMessageElement = document.getElementById('error-message');
                errorMessageElement.textContent = "Your input has forbidden characters";
                errorMessageElement.classList.remove('d-none');
            } else {
                var data = {
                    password: password,
                    token: token
                };

                fetch('./actions/login-register/reset-password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        const errorMessageElement = document.getElementById('error-message');
                        const successMessageElement = document.getElementById('success-message');

                        if (data.status === 'success') {
                            successMessageElement.textContent = data.message;
                            successMessageElement.classList.remove('d-none');
                            errorMessageElement.classList.add('d-none');
                        } else {
                            errorMessageElement.textContent = data.message;
                            errorMessageElement.classList.remove('d-none');
                            successMessageElement.classList.add('d-none');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        }
    });
</script>

<?php include __DIR__ . "/components/down-all.php" ?>