<?php include '../components/up-all.php' ?>

<?php
// I NEED THIS
$bannerFound = 0;
include '../../actions/connect.php';

try {
    $query = "SELECT * FROM BannerImage WHERE id=:id";
    $stmt = $db->prepare($query);
    $id = 1;
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the record
    $bannerImage = $stmt->fetch(PDO::FETCH_ASSOC);

    $bannerFound = 1;

    if (!$bannerImage) {
        $bannerFound = 2;
    }
    else{
        $jsonFile = __DIR__ . '/../../settings.json';
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
        $dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Banner Image</h2>
        <?php
        if ($bannerFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The banner with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="banner-form">
            <div class="form-group">
                <label for="bannerImage">Upload Image</label>
                <input type="file" id="bannerImage" name="bannerImage" accept="image/*">
                <small class="form-text text-muted">Select 1 images (JPG, PNG, JPEG, WEBP, etc.)</small>
            </div>
            <?php if ($bannerFound == 1 && !empty($bannerImage['image_url'])) : ?>
                <div class="form-group">
                    <label for="images">Uploaded Image</label>
                    <div class="row">
                        
                    <div class="col-md-12 mb-3">
                        <div class="image-container">
                            <!-- TODO : DUZENLE BURAYI BURDA LOCALHOST'a gerek olmıcak zaten donen deger yeterli olacak buraya domain linki verilir -->
                            <img src="<?php echo $dynamicUrl.htmlspecialchars($bannerImage['image_url']); ?>" alt="Banner Image" class="img-fluid">
                        </div>
                    </div>
                    </div>
                </div>
            <?php endif; ?>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($bannerFound != 0 && $bannerFound == 1 && isset($bannerImage) ? $bannerImage['id'] : ''); ?>">
            <button type="submit"><?php echo $bannerFound == 1 ? 'Update' : 'Submit' ?></button>
        </form>
    </div>
</div>

<script>
    function getRandomColor() {
        const banners = [
            ['#ff9a9e', '#fad0c4'],
            ['#a2c2e2', '#d7aef5'],
            ['#d4fc79', '#96e6a1'],
            ['#fbc2eb', '#a6c0fe'],
            ['#f6d365', '#fda085'],
            ['#f8cdda', '#f9b8e1']
        ];
        return banners[Math.floor(Math.random() * banners.length)];
    }

    function setRandomGradient() {
        const [a, b] = getRandomColor();
        document.querySelector('.form-wrapper').style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
    }

    setRandomGradient();

    document.getElementById('bannerImage').addEventListener('change', function() {
        var imageInput = this;
        var fileCount = imageInput.files.length;

        if (fileCount > 1) {
            alert("You can only upload 1 image.");
            imageInput.value = "";
        }

        var file = imageInput.files[0];
        var maxFileSize = 3 * 1024 * 1024; // 3 MB
        var allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        var allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
        var fileExtension = file.name.split('.').pop().toLowerCase();
        var fileType = file.type;

        if (!allowedExtensions.includes(fileExtension) || !allowedMimeTypes.includes(fileType)) {
            alert("Invalid file type or extension.");
            imageInput.value = "";
        }

        if (file.size > maxFileSize) {
            alert("File size must be less than 3 MB.");
            imageInput.value = "";
        }
    });


    document.getElementById('banner-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var bannerId = document.getElementById('id').value;

        var imageInput = document.getElementById('bannerImage');

        var formData = new FormData();
        formData.append('bannerId', bannerId);
        formData.append('image', imageInput.files[0]);


        fetch('../../actions/admin/banner-image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorMessageElement = document.getElementById('error-message');

                if (data.status === 'success') {
                    window.location.href = '../banners.php';
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

<?php include '../components/down-all.php' ?>