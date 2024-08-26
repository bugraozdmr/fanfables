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


//* GETTING SHOWS
$query = "SELECT name,id FROM Shows";
$stmt = $db->prepare($query);
$stmt->execute();
$showss = $stmt->fetchAll(PDO::FETCH_ASSOC);


//? I NEED THIS
$characterFound = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $query = "SELECT * FROM Characters WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the record
        $character = $stmt->fetch(PDO::FETCH_ASSOC);

        $characterFound = 1;

        if (!$character) {
            $characterFound = 2;
        } else {
            //* character Show
            $query = "SELECT id FROM Shows WHERE id=:id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $character['showId'], PDO::PARAM_STR);
            $stmt->execute();

            $showIddd = $stmt->fetch(PDO::FETCH_ASSOC);


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
        <h2>Character Form</h2>
        <?php
        if ($characterFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The character with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="character-form">
            <div class="form-group">
                <label for="name">Character Name (*: Gumball)</label>
                <input type="text" id="name" name="name" placeholder="Enter character name" value="<?php echo htmlspecialchars($characterFound != 0 && $characterFound == 1 && isset($character) ? $character['name'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="starring">Starring (*: Tom Cruise)</label>
                <input type="text" id="starring" name="starring" placeholder="Enter starring actor/actress" value="<?php echo htmlspecialchars($characterFound != 0 && $characterFound == 1 && isset($character) ? $character['starring'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">character Description (*: Funny little guy)</label>
                <textarea class="form-control" type="text" id="description" name="description" placeholder="Enter character description" rows="6" required><?php echo htmlspecialchars($characterFound != 0 && $characterFound == 1 && isset($character) ? $character['description'] : ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="showId">Show</label>
                <select class="form-control custom-select" id="showId" name="showId">
                    <option value="" disabled selected>Select a show</option>
                    <?php foreach ($showss as $show) : ?>
                        <option <?php echo $characterFound != 0 && $characterFound == 1 && isset($character) && ($show['id'] == $character['showId']) ? 'selected' : "" ?> value="<?= htmlspecialchars($show['id']) ?>"><?= htmlspecialchars($show['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <input type="hidden" id="id" value="<?php echo htmlspecialchars($characterFound != 0 && $characterFound == 1 && isset($character) ? $character['id'] : '') ?>">
            <div class="form-group">
                <label for="characterImage">Upload Image</label>
                <input type="file" id="characterImage" name="characterImage" accept="image/*">
                <small class="form-text text-muted">Select 1 image (JPG, PNG, JPEG, WEBP, etc.)</small>
            </div>
            <?php if ($characterFound == 1 && !empty($character['image'])) : ?>
                <div class="form-group">
                    <label for="images">Uploaded Image</label>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="image-container">
                                <img src="<?php echo $dynamicUrl.htmlspecialchars($character['image']); ?>" alt="character Image" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($characterFound != 0 && $characterFound == 1 && isset($character) ? $character['id'] : ''); ?>">

            <button type="submit"><?php echo $characterFound == 1 ? 'Update' : 'Submit' ?></button>
        </form>
    </div>
</div>

<script>
    function getRandomcharacter() {
        const characters = [
            ['#ff9a9e', '#fad0c4'],
            ['#a2c2e2', '#d7aef5'],
            ['#d4fc79', '#96e6a1'],
            ['#fbc2eb', '#a6c0fe'],
            ['#f6d365', '#fda085'],
            ['#f8cdda', '#f9b8e1']
        ];
        return characters[Math.floor(Math.random() * characters.length)];
    }

    function setRandomGradient() {
        const [a, b] = getRandomcharacter();
        document.querySelector('.form-wrapper').style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
    }

    setRandomGradient();


    document.getElementById('characterImage').addEventListener('change', function(event) {
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


    document.getElementById('character-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var characterName = document.getElementById('name').value;
        var starring = document.getElementById('starring').value;
        var characterDescription = document.getElementById('description').value;
        var characterId = document.getElementById('id').value; // Get the ID
        var showId = document.getElementById('showId').value;

        var imageInput = document.getElementById('characterImage');

        // Create a FormData object to hold the form data and files
        var formData = new FormData();
        formData.append('name', characterName);
        formData.append('id', characterId);
        formData.append('description', characterDescription);
        formData.append('showId', showId);
        formData.append('starring', starring);
        formData.append('image', imageInput.files[0]);

        // Send the FormData to the server
        fetch('../../actions/admin/characters.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const errorMessageElement = document.getElementById('error-message');

                if (data.status === 'success') {
                    window.location.href = '../characters.php';
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