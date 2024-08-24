<?php include '../components/up-all.php' ?>

<style>
    .image-container {
        position: relative;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 5px
    }

    .image-container img {
        width: 100%;
        height: auto;
        display: block
    }
</style>

<?php
include '../../actions/connect.php';



//* GETTING CATEGORIES
$query = "SELECT * FROM Categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

//* GETTING TYPES
$query = "SELECT * FROM Types";
$stmt = $db->prepare($query);
$stmt->execute();
$ttypes = $stmt->fetchAll(PDO::FETCH_ASSOC);


//? I NEED THIS
$showFound = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $query = "SELECT * FROM Shows WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the record
        $show = $stmt->fetch(PDO::FETCH_ASSOC);

        $showFound = 1;

        if (!$show) {
            $showFound = 2;
        } else {
            //* show CATEGORIES
            $query = "SELECT * FROM ShowCategories WHERE showId=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $show['id'], PDO::PARAM_STR);
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
?>

<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container mt-5" style="max-width: 800px !important">
        <h2>Show Form</h2>
        <?php
        if ($showFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The show with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="show-form">
            <div class="form-group">
                <label for="name">Show Name (*: Fall Guy)</label>
                <input type="text" id="name" name="name" placeholder="Enter show name" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['name'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="director">Show Director (*: David Leitch)</label>
                <input type="text" id="director" name="director" placeholder="Enter show director" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['director'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="studio">Show Studio (*: Sony)</label>
                <input type="text" id="studio" name="studio" placeholder="Enter show studio" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['studio'] : ''); ?>" >
            </div>
            <div class="form-group">
                <label for="duration">Show Duration (*: 40m / per ep)</label>
                <input type="text" id="duration" name="duration" placeholder="Enter show duration" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['duration'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="episode_count">Show Episode Count (*: 120)</label>
                <input type="text" id="episode_count" name="episode_count" placeholder="Enter show episode count" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['episode_count'] : ''); ?>">
            </div>
            <div class="form-group">
                <label for="date_aired">Show Date (*: 8th March 2024 - ?)</label>
                <input type="text" id="date_aired" name="date_aired" placeholder="Enter show date aired" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['date_aired'] : ''); ?>">
            </div>
            <div class="form-group">
                <label for="status">Status (*: Active / Only Franchies)</label>
                <input type="text" id="status" name="status" placeholder="Enter show status" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['status'] : ''); ?>">
            </div>
            <div class="form-group">
                <label for="imdb">Show IMDB (*: 3.2)</label>
                <input type="text" id="imdb" name="imdb" placeholder="Enter show imdb" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['imdb'] : ''); ?>">
            </div>
            <div class="form-group">
                <label for="link">Show Link (*: Give a link)</label>
                <input type="text" id="link" name="link" placeholder="Enter show url link" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['watchLink'] : ''); ?>">
            </div>
            <div class="form-group">
                <label for="description">Show Description (*: Very nice shoe)</label>
                <textarea class="form-control" type="text" id="description" name="description" placeholder="Enter show description" rows="6" required><?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['description'] : ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="card_desc">Show Card Description (*: For Google)</label>
                <textarea class="form-control" type="text" id="card_desc" name="card_desc" placeholder="Enter show duration" rows="3" required><?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['card_desc'] : ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="typeId">Type</label>
                <select class="form-control custom-select" id="typeId" name="typeId">
                    <option value="" disabled selected>Select a type</option>
                    <?php foreach ($ttypes as $type) : ?>
                        <option <?php echo $showFound != 0 && $showFound == 1 && isset($show) && ($type['id'] == $show['typeId']) ? 'selected' : "" ?> value="<?= htmlspecialchars($type['id']) ?>"><?= htmlspecialchars($type['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group row">
                <div class="col-lg-12">
                    <label for="categoryId">Show Categories</label>
                    <?php if (!empty($categories)) : ?>
                        <?php foreach ($categories as $category) : ?>
                            <div class="form-check d-flex align-items-center">
                                <div class="col-md-1">
                                    <?php
                                    $isChecked = (isset($catIds) && is_array($catIds) && in_array($category['id'], $catIds)) ? 'checked' : '';
                                    ?>
                                    <input class="form-check-input" type="checkbox" id="<?php echo $category['name'] ?>" name="cates[]" value="<?php echo $category['id'] ?>" <?php echo $isChecked ?>>
                                </div>
                                <div class="col-md-5 col-sm-5 mt-3 mx-3">
                                    <label class="form-check-label" for="<?php echo $category['name'] ?>"><?php echo $category['name'] ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>There is no category yet.Add some.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label for="showImage">Upload Image</label>
                <input type="file" id="showImage" name="showImage" accept="image/*">
                <small class="form-text text-muted">Select 1 image (JPG, PNG, JPEG, WEBP, etc.)</small>
            </div>
            <?php if ($showFound == 1 && !empty($show['image'])) : ?>
                <div class="form-group">
                    <label for="images">Uploaded Image</label>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="image-container">
                                <img src="<?php echo $dynamicUrl.htmlspecialchars($show['image']); ?>" alt="Show Image" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($showFound != 0 && $showFound == 1 && isset($show) ? $show['id'] : ''); ?>">

            <button type="submit"><?php echo $showFound == 1 ? 'Update' : 'Submit' ?></button>
        </form>
    </div>
</div>

<script>
    function getRandomshow() {
        const shows = [
            ['#ff9a9e', '#fad0c4'],
            ['#a2c2e2', '#d7aef5'],
            ['#d4fc79', '#96e6a1'],
            ['#fbc2eb', '#a6c0fe'],
            ['#f6d365', '#fda085'],
            ['#f8cdda', '#f9b8e1']
        ];
        return shows[Math.floor(Math.random() * shows.length)];
    }

    function setRandomGradient() {
        const [a, b] = getRandomshow();
        document.querySelector('.form-wrapper').style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
    }

    setRandomGradient();


    document.getElementById('showImage').addEventListener('change', function(event) {
        const files = event.target.files;
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        let allValid = true;

        for (let i = 0; i < files.length; i++) {
            if (!allowedTypes.includes(files[i].type)) {
                allValid = false;
                break;
            }
        }

        if (!allValid) {
            alert('Please select only image files.');
            event.target.value = '';
        }
    });


    document.getElementById('show-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var showName = document.getElementById('name').value;
        var showDirector = document.getElementById('director').value;
        var showStudio = document.getElementById('studio').value;
        var showDuration = document.getElementById('duration').value;
        var showEpCount = document.getElementById('episode_count').value;
        var showDate = document.getElementById('date_aired').value;
        var showStatus = document.getElementById('status').value;
        var showIMDB = document.getElementById('imdb').value;
        var showDescription = document.getElementById('description').value;
        var showCardDesc = document.getElementById('card_desc').value;
        var showLink = document.getElementById('link').value;
        var showId = document.getElementById('id').value; // Get the ID
        var typeId = document.getElementById('typeId').value;

        var categories = Array.from(document.querySelectorAll('input[name="cates[]"]:checked')).map(checkbox => checkbox.value);

        var imageInput = document.getElementById('showImage');

        // Create a FormData object to hold the form data and files
        var formData = new FormData();
        formData.append('name', showName);
        formData.append('director', showDirector);
        formData.append('studio', showStudio);
        formData.append('duration', showDuration);
        formData.append('epCount', showEpCount);
        formData.append('show_date', showDate);
        formData.append('id', showId);
        formData.append('status', showStatus);
        formData.append('imdb', showIMDB);
        formData.append('description', showDescription);
        formData.append('card_desc', showCardDesc);
        formData.append('typeid', typeId);
        formData.append('link', showLink);
        formData.append('categories', categories);
        formData.append('image', imageInput.files[0]);

        // Send the FormData to the server
        fetch('../../actions/admin/shows.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorMessageElement = document.getElementById('error-message');

                if (data.status === 'success') {
                    window.location.href = '../shows.php';
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