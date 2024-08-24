<?php include '../components/up-all.php' ?>

<?php
// I NEED THIS
$bannerFound = 0;
if (isset($_GET['id'])) {
    include '../../actions/connect.php';

    $id = $_GET['id'];

    try {
        $query = "SELECT * FROM Banners WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the record
        $banner = $stmt->fetch(PDO::FETCH_ASSOC);

        $bannerFound = 1;

        if (!$banner) {
            $bannerFound = 2;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Banner Form</h2>
        <?php
        if ($bannerFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The banner with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="banner-form">
            <div class="form-group">
                <label for="title">Banner Title (*: Collection)</label>
                <input type="text" id="title" name="title" placeholder="Enter banner title" value="<?php echo htmlspecialchars($bannerFound != 0 && $bannerFound == 1 && isset($banner) ? $banner['title'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Banner Name (*: The Project Red)</label>
                <input type="text" id="name" name="name" placeholder="Enter banner name" value="<?php echo htmlspecialchars($bannerFound != 0 && $bannerFound == 1 && isset($banner) ? $banner['name'] : ''); ?>" required>
            </div>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($bannerFound != 0 && $bannerFound == 1 && isset($banner) ? $banner['id'] : ''); ?>">
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

    document.getElementById('banner-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var bannerName = document.getElementById('name').value;
        var bannerTitle = document.getElementById('title').value;
        var bannerId = document.getElementById('id').value; // Get the ID

        var data = {
            name: bannerName,
            title: bannerTitle,
            id: bannerId
        };


        fetch('../../actions/admin/banners.php', {
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