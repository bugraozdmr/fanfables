<?php
$title = "Sign Up";
$login_path = "/anime/";
include __DIR__ . "/components/up-all.php"
?>

<section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    <h2>Sign Up</h2>
                    <p>"Do not go gentle into that good night. Old age should burn and rave at close of day; Rage, rage against the dying of the light."</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="signup spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="login__form">
                    <h3>Sign Up</h3>
                    <form id="register-form">
                        <div class="input__item">
                            <input type="email" id="email" placeholder="Email address">
                            <span class="icon_mail"></span>
                        </div>
                        <div class="input__item">
                            <input type="text" id="username" placeholder="Your Username">
                            <span class="icon_profile"></span>
                        </div>
                        <div class="input__item">
                            <input type="password" id="password" placeholder="Password">
                            <span class="icon_lock"></span>
                        </div>
                        <div class="input__item">
                            <input type="password" id="confirm_password" placeholder="Confirm Password">
                            <span class="icon_lock"></span>
                        </div>
                        <div class="col-lg-12">
                            <span id="error-message" class="text-danger d-none"></span>
                        </div>
                        <button type="submit" class="site-btn">Sign Up Now</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="login__register">
                    <h3>Already have an account?</h3>
                    <a href="<?php echo $login_path ?>login.php" class="primary-btn">Log In!</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('register-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var username = document.getElementById('username').value;
        var email = document.getElementById('email').value;
        var password = document.getElementById('password').value;

        var confirm_password = document.getElementById('confirm_password').value;

        if (confirm_password !== password) {
            const errorMessageElement = document.getElementById('error-message');

            errorMessageElement.textContent = "Password and Confirm Password Not Matching";
            errorMessageElement.classList.remove('d-none');
        } else {
            var data = {
                username: username,
                password: password,
                email: email,
            };


            fetch('./actions/login-register/register.php', {
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
                        window.location.href = './login.php';
                    } else {
                        errorMessageElement.textContent = data.message;
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