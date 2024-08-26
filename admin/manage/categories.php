<?php include '../components/up-all.php' ?>

<?php

include '../../actions/connect.php';


// I NEED THIS
$categoryFound = 0;


if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $query = "SELECT * FROM Categories WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the record
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        $categoryFound = 1;

        if (!$category) {
            $categoryFound = 2;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Category Form</h2>
        <?php
        if ($categoryFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The Category with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="category-form" >
            <div class="form-group">
                <label for="name">Category Name (*: Action)</label>
                <input type="text" id="name" name="name" placeholder="Enter category name" value="<?php echo htmlspecialchars($categoryFound != 0 && $categoryFound == 1 && isset($category) ? $category['name'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Slug (*: action)</label>
                <input type="text" id="slug" name="name" placeholder="Enter category name" value="<?php echo htmlspecialchars($categoryFound != 0 && $categoryFound == 1 && isset($category) ? $category['slug'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="categoryImage">Upload Image</label>
                <input type="file" id="categoryImage" name="categoryImage" accept="image/*">
                <small class="form-text text-muted">Select 1 image (JPG, PNG, JPEG, WEBP, etc.)</small>
            </div>
            <?php if ($categoryFound == 1 && !empty($category['categoryImage'])) : ?>
                <div class="form-group">
                    <label for="images">Uploaded Image</label>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="image-container">
                                <img src="<?php echo $dynamicUrl.htmlspecialchars($category['categoryImage']); ?>" alt="Category Image" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($categoryFound != 0 && $categoryFound == 1 && isset($category) ? $category['id'] : ''); ?>">
            <button type="submit"><?php echo $categoryFound == 1 ? 'Update' : 'Submit' ?></button>
        </form>
    </div>
</div>

<script>
    function getRandomColor() {
        const categorys = [
            ['#ff9a9e', '#fad0c4'],
            ['#a2c2e2', '#d7aef5'],
            ['#d4fc79', '#96e6a1'],
            ['#fbc2eb', '#a6c0fe'],
            ['#f6d365', '#fda085'],
            ['#f8cdda', '#f9b8e1']
        ];
        return categorys[Math.floor(Math.random() * categorys.length)];
    }

    function setRandomGradient() {
        const [a, b] = getRandomColor();
        document.querySelector('.form-wrapper').style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
    }

    setRandomGradient();

    document.getElementById('categoryImage').addEventListener('change', function() {
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

    document.getElementById('category-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var categoryName = document.getElementById('name').value;
        var categorySlug = document.getElementById('slug').value;
        var categoryId = document.getElementById('id').value;

        var selectedCategories = Array.from(document.querySelectorAll('input[name="scategories[]"]:checked')).map(checkbox => checkbox.value);
        // 1 tane resim aliyor
        var imageInput = document.getElementById('categoryImage');

        var formData = new FormData();
        formData.append('categoryName', categoryName);
        formData.append('categorySlug', categorySlug);
        formData.append('categoryId', categoryId);
        formData.append('categoryImage', imageInput.files[0]);

        fetch('../../actions/admin/categories.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorMessageElement = document.getElementById('error-message');

                if (data.status === 'success') {
                    window.location.href = '../categories.php';
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