<?php
$title = "Edit Profile";
$pageDescription = "Edit your profile info.";

$profile_path = "/anime";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require __DIR__ . '/vendor/autoload.php';

$jsonFile = __DIR__ . '/settings.json';
$jsonData = file_get_contents($jsonFile);
$data = json_decode($jsonData, true);
$dynamicUrl = isset($data['dynamicUrl']) ? $data['dynamicUrl'] : '';
$key = isset($data['key']) ? $data['key'] : '';

function maskEmail($email)
{
    $email_parts = explode("@", $email);
    $email_name = substr($email_parts[0], 0, 3);
    return $email_name . '***@' . $email_parts[1];
}

if (isset($_COOKIE['auth_token'])) {
    try {
        include './actions/connect.php';

        // TOKEN BOS GELEBILIR
        $token = $_COOKIE['auth_token'];
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $TokenUsername = $decoded->sub;

        $query = "SELECT id,username,name,email,image,bannerImage FROM users WHERE username=:username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $TokenUsername);
        $stmt->execute();
        // result altta kullanilmis galiba ondan hata aliyor onu görüyor yukardan assa
        $usrRes = $stmt->fetch();

        $maskedEmail = isset($usrRes['email']) ? maskEmail($usrRes['email']) : '';

        if (!isset($usrRes['username']) && empty($usrRes['username'])) {
            header('Location: /anime/404.php');
            exit();
        }
    } catch (\Exception $e) {
        header('Location: /anime/404.php');
        exit();
    }
} else {
    header('Location: /anime/404.php');
    exit();
}



include __DIR__ . "/components/up-all.php"
?>

<section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="normal__breadcrumb__text">
                    <h2>Edit Profile</h2>
                    <p>"I’m not leaving. I’m not leaving. I’m not fucking leaving!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .image-preview,.image-upload-label{background-color:#f8f8f8;width:100%}.image-preview{height:200px;display:flex;justify-content:center;align-items:center;margin-bottom:15px;border:1px solid #ddd;border-radius:4px}.image-upload-label,.site-btn{display:inline-block;cursor:pointer}.image-preview img{max-width:100%;max-height:100%;border-radius:4px}.input__item{position:relative;margin-bottom:15px}.input__item input[type=text]{width:100%;padding:10px;border:1px solid #ddd;border-radius:4px}.input__item input[type=file]{display:none}.image-upload-label{padding:10px;border:1px solid #ddd;border-radius:4px;text-align:center;margin-top:10px}.site-btn{padding:10px 20px;border:none;border-radius:4px;background-color:#007bff;color:#fff;font-size:16px}.site-btn:hover{background-color:#0056b3}
</style>

<section class="login spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="login__form">
                    <h3>Profile</h3>
                    <form id="profile-form">
                        <?php if (isset($usrRes['image']) && !empty($usrRes['image'])) : ?>
                            <div class="image-preview">
                                <img src="<?php echo $profile_path.htmlspecialchars($usrRes['image']); ?>" alt="Profile Image">
                            </div>
                        <?php else : ?>
                            <div class="image-preview">
                                <p>No profile image uploaded</p>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($usrRes['bannerImage']) && !empty($usrRes['bannerImage'])) : ?>
                            <div class="image-preview">
                                <img src="<?php echo $profile_path.htmlspecialchars($usrRes['bannerImage']); ?>" alt="Profile Image">
                            </div>
                        <?php else : ?>
                            <div class="image-preview">
                                <p>No profile banner image uploaded</p>
                            </div>
                        <?php endif; ?>
                        <div class="input__item" style="width: 100% !important;">
                            <input type="file" id="profile-image" accept="image/*">
                            <span class="icon_camera_alt"></span>
                            <label for="profile-image" class="image-upload-label">Upload User Profile Image</label>
                        </div>
                        <div class="input__item" style="width: 100% !important;">
                            <input type="file" id="banner-image" accept="image/*">
                            <span class="icon_camera"></span>
                            <label for="banner-image" class="image-upload-label">Upload Banner Image</label>
                        </div>
                        <input type="hidden" id="id" value="<?php echo $usrRes['id'] ?>">
                        <div class="input__item" style="width: 100% !important;">
                            <input type="text" id="username" placeholder="Username" value="<?php echo $usrRes['username'] ?? '' ?>">
                            <span class="icon_plus_alt2"></span>
                        </div>
                        <div class="input__item" style="width: 100% !important;">
                            <input type="text" id="name" placeholder="Name" value="<?php echo $usrRes['name'] ?? '' ?>">
                            <span class="icon_cloud"></span>
                        </div>
                        <div class="col-lg-12">
                            <label for="description" style="color:white">About You</label>
                            <textarea style="width: 100%" rows="6" type="text" id="description" placeholder="Description">
                            <?php echo $usrRes['description'] ?? '' ?>
                            </textarea>
                        </div>
                        <div class="col-lg-12">
                            <span id="error-message" class="text-danger d-none"></span>
                        </div>
                        <div class="col-lg-12">
                            <span id="error-message" class="text-danger d-none"></span>
                        </div>
                        <button type="submit" class="site-btn">Update</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="login__register">
                    <h3>Make your profile unique<br /><?php echo $maskedEmail; ?></h3>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('profile-image').addEventListener('change', function() {
        var imageInput = this;
        var fileCount = imageInput.files.length;

        if (fileCount > 1) {
            alert("You can only upload 1 image.");
            imageInput.value = "";
        }

        var file = imageInput.files[0];
        var maxFileSize = 1 * 1024 * 1024;
        var allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        var allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        var fileExtension = file.name.split('.').pop().toLowerCase();
        var fileType = file.type;

        if (!allowedExtensions.includes(fileExtension) || !allowedMimeTypes.includes(fileType)) {
            alert("Invalid file type or extension.");
            imageInput.value = "";
        }

        if (file.size > maxFileSize) {
            alert("File size must be less than 1 MB.");
            imageInput.value = "";
        }
    });

    document.getElementById('banner-image').addEventListener('change', function() {
        var imageInput = this;
        var fileCount = imageInput.files.length;

        if (fileCount > 1) {
            alert("You can only upload 1 image.");
            imageInput.value = "";
        }

        var file = imageInput.files[0];
        var maxFileSize = 1 * 1024 * 1024;
        var allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        var allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        var fileExtension = file.name.split('.').pop().toLowerCase();
        var fileType = file.type;

        if (!allowedExtensions.includes(fileExtension) || !allowedMimeTypes.includes(fileType)) {
            alert("Invalid file type or extension.");
            imageInput.value = "";
        }

        if (file.size > maxFileSize) {
            alert("File size must be less than 1 MB.");
            imageInput.value = "";
        }
    });

    document.getElementById('profile-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var name = document.getElementById('name').value;
        var username = document.getElementById('username').value;
        var id = document.getElementById('id').value;
        var description = document.getElementById('description').value;
        var imageInput = document.getElementById('profile-image');
        var bannerImageInput = document.getElementById('banner-image');

        var formData = new FormData();
        formData.append('name', name);
        formData.append('username', username);
        formData.append('id', id);
        formData.append('description', description);
        formData.append('image', imageInput.files[0]);
        formData.append('bannerImage', bannerImageInput.files[0]);

        fetch('/anime/actions/profile/edit-profile.php', {
                method: 'POST',
                body: formData
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

<?php include __DIR__ . "/components/down-all.php" ?>