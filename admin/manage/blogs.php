<?php
$title = "New One";
$blogs_path = "/anime";
include '../components/up-all.php' ?>

<?php
// I NEED THIS
$blogFound = 0;
if (isset($_GET['id'])) {
    include '../../actions/connect.php';

    $id = $_GET['id'];

    try {
        $query = "SELECT * FROM Blog WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the record
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);

        $blogFound = 1;

        if (!$blog) {
            $blogFound = 2;
        }
        else{
            //* blog CATEGORIES
            $query = "SELECT * FROM BlogCategories WHERE blogId=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $blog['id'], PDO::PARAM_STR);
            $stmt->execute();

            $catsFrom = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $catIds = array_column($catsFrom, 'categoryId');


            //! NAMES ARE IMPORTANT --- BETTER COMMENTS

            $jsonFile = __DIR__ . '/../../settings.json';
            $jsonData = file_get_contents($jsonFile);
            $data = json_decode($jsonData, true);
            $dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

//* GETTING CATEGORIES
$query = "SELECT * FROM Categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container" style="width:100% !important;max-width:100%">
        <h2>Blogs Form</h2>
        <?php
        if ($blogFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The Type with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="blog-form">
            <div class="form-group">
                <label for="title">Blog Title</label>
                <input type="text" id="title" name="title" placeholder="Enter blog title" value="<?php echo htmlspecialchars($blogFound != 0 && $blogFound == 1 && isset($blog) ? $blog['title'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="card_desc">Blog Card Description</label>
                <input type="text" id="card_desc" name="card_desc" placeholder="Enter blog card desc" value="<?php echo htmlspecialchars($blogFound != 0 && $blogFound == 1 && isset($blog) ? $blog['card_desc'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Content</label>
                <textarea type="text" id="content" name="content" placeholder="Enter content">
                    <?php echo htmlspecialchars($blogFound != 0 && $blogFound == 1 && isset($blog) ? $blog['content'] : ''); ?>
                </textarea>
            </div>
            <div class="form-group">
                <label for="alt">Alt (deadpool,wolwerine,marvel)</label>
                <input type="text" id="alt" name="alt" placeholder="Enter blog alt categories" value="<?php echo htmlspecialchars($blogFound != 0 && $blogFound == 1 && isset($blog) ? $blog['alt'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Upload Image</label>
                <input type="file" id="image" name="image" accept="image/*">
                <small class="form-text text-muted">Select 1 image (JPG, PNG, JPEG, WEBP, etc.)</small>
            </div>
            <?php if ($blogFound == 1 && !empty($blog['image'])) : ?>
                <div class="form-group">
                    <label for="images">Uploaded Image</label>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="image-container">
                                <img src="<?php echo $dynamicUrl.htmlspecialchars($blog['image']); ?>" alt="Blog Image" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($categories)) : ?>
                <?php foreach ($categories as $category) : ?>
                    <div class="form-check d-flex align-items-center">
                        <div class="col-lg-1 col-md-1 col-sm-1">
                            <?php
                            $isChecked = (isset($catIds) && is_array($catIds) && in_array($category['id'], $catIds)) ? 'checked' : '';
                            ?>
                            <input class="form-check-input" type="checkbox" id="<?php echo $category['name'] ?>" name="cates[]" value="<?php echo $category['id'] ?>" <?php echo $isChecked ?>>
                        </div>
                        <div class="col-md-5 col-sm-5 col-lg-5 mt-3 mx-3">
                            <label class="form-check-label" for="<?php echo $category['name'] ?>"><?php echo $category['name'] ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>There is no category yet.Add some.</p>
            <?php endif; ?>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($blogFound != 0 && $blogFound == 1 && isset($blog) ? $blog['id'] : ''); ?>">
            <button type="submit"><?php echo $blogFound == 1 ? 'Update' : 'Submit' ?></button>
        </form>
    </div>
</div>

<script>
    function getRandomColor() {
        const blogs = [
            ['#ff9a9e', '#fad0c4'],
            ['#a2c2e2', '#d7aef5'],
            ['#d4fc79', '#96e6a1'],
            ['#fbc2eb', '#a6c0fe'],
            ['#f6d365', '#fda085'],
            ['#f8cdda', '#f9b8e1']
        ];
        return blogs[Math.floor(Math.random() * blogs.length)];
    }

    function setRandomGradient() {
        const [a, b] = getRandomColor();
        document.querySelector('.form-wrapper').style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
    }

    setRandomGradient();

    document.getElementById('blog-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var title = document.getElementById('title').value;
        var card_desc = document.getElementById('card_desc').value;
        var alt = document.getElementById('alt').value;
        var id = document.getElementById('id').value;

        var categories = Array.from(document.querySelectorAll('input[name="cates[]"]:checked')).map(checkbox => checkbox.value);

        var imageInput = document.getElementById('image');

        var content = CKEDITOR.instances.content.getData();

        var formData = new FormData();
        formData.append('title', title);
        formData.append('card_desc', card_desc);
        formData.append('alt', alt);
        formData.append('id', id);
        formData.append('content', content);

        formData.append('categories', categories);
        formData.append('image', imageInput.files[0]);

        //TODO BURDA KALDIM SADECE ENDPOINTI YAZ ADAM AKILLI SONRA PAGINATION SONRA BANLI KULLANICILAR SAYFALAMA
        fetch('../../actions/admin/blogs.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorMessageElement = document.getElementById('error-message');

                if (data.status === 'success') {
                    window.location.href = '../Blogs.php';
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

<script src="/anime/admin/assets/js/ckeditor/ckeditor.js"></script>

<script>
    CKEDITOR.replace('content', {
        extraPlugins : 'filebrowser',
        filebrowseUrl : '/anime/up/browser.php',
        filebrowserUploadMethod : 'form',
        filebrowserUploadUrl: '/anime/up/upload_image.php',
    });
</script>


<?php include '../components/down-all.php' ?>