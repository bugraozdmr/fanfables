<?php
$title = "Forgot Password";
$url_to = "http://localhost/anime/forgot-password.php";
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
                    <h2>Forgot Password</h2>
                    <p>"I'm not gonna run away, I never go back on my word! That's my nindo: my ninja way!"</p>
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
                    <h3>Enter Your Email</h3>
                    <form id="send-forgot-form">
                        <div class="input__item">
                            <input type="email" id="email" placeholder="Email address">
                            <span class="icon_mail"></span>
                        </div>
                        <div class="col-lg-12">
                            <span id="error-message" class="text-danger d-none"></span>
                            <span id="success-message" class="text-success d-none"></span>
                        </div>
                        <button type="submit" class="site-btn">Send Mail</button>
                    </form>
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
    document.getElementById('send-forgot-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var unsafeCharacters = /[<>'"]/;
        var scriptTags = /<script.*?>.*?<\/script>/i;

        var email = document.getElementById('email').value;

        if (unsafeCharacters.test(email) || scriptTags.test(email)) {
            const errorMessageElement = document.getElementById('error-message');
            errorMessageElement.textContent = "Your input has forbidden characters";
            errorMessageElement.classList.remove('d-none');
        } else {
            var data = {
                email: email,
            };

            fetch('./actions/login-register/forgot-password.php', {
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
    });
</script>

<?php include __DIR__ . "/components/down-all.php" ?>