<?php
$title = "Login";
$login_path = "/anime/";

if (isset($_COOKIE['auth_token'])) {
    $token = $_COOKIE['auth_token'];
}

if (isset($token)) {
    header('Location: index.php');
    exit();
}

include __DIR__ . "/components/up-all.php"
?>

<section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    <h2>Login</h2>
                    <p>Welcome to FanFables: Where Every Character's Story Comes to Life!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="login spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="login__form">
                    <h3>Login</h3>
                    <form id="login-form">
                        <div class="input__item">
                            <input type="text" id="username" placeholder="Email address">
                            <span class="icon_mail"></span>
                        </div>
                        <div class="input__item">
                            <input type="password" id="password" placeholder="Password">
                            <span class="icon_lock"></span>
                        </div>
                        <div class="col-lg-12">
                            <span id="error-message" class="text-danger d-none"></span>
                        </div>
                        <button type="submit" class="site-btn">Login Now</button>
                    </form>
                    <a href="<?php echo $login_path ?>forgot-password.php" class="forget_pass">Forgot Your Password?</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login__register">
                    <h3>Dont’t Have An Account?</h3>
                    <a href="<?php echo $login_path ?>signup.php" class="primary-btn">Register Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('login-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var unsafeCharacters = /[<>'"]/;
        var scriptTags = /<script.*?>.*?<\/script>/i;

        var username = document.getElementById('username').value;
        var password = document.getElementById('password').value;

        if (unsafeCharacters.test(password) || scriptTags.test(password)) {
            const errorMessageElement = document.getElementById('error-message');
            errorMessageElement.textContent = "Your input has forbidden characters";
            errorMessageElement.classList.remove('d-none');
        } else {
            var data = {
                username: username,
                password: password,
            };

            fetch('./actions/login-register/login.php', {
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
                        window.location.href = 'index.php';
                    } else {
                        //* yazi degisirse burasi degiscek
                        if (data.message.includes('First confirm')) {
                            errorMessageElement.innerHTML = data.message;
                        } else {
                            errorMessageElement.textContent = data.message;
                        }
                        errorMessageElement.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    });
</script>

<?php include __DIR__ . "/components/down-all.php" ?>