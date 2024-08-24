<?php include '../components/up-all.php' ?>

<?php
// I NEED THIS
$sizeFound = 0;
if (isset($_GET['id'])) {
    include '../../actions/connect.php';

    $id = $_GET['id'];

    try {
        $query = "SELECT * FROM Sizes WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the record
        $size = $stmt->fetch(PDO::FETCH_ASSOC);

        $sizeFound = 1;

        if (!$size) {
            $sizeFound = 2;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Size Form</h2>
        <?php
        if ($sizeFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The size with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="size-form">
            <div class="form-group">
                <label for="name">Size Name (*: Medium)</label>
                <input type="text" id="name" name="name" placeholder="Enter size name" value="<?php echo htmlspecialchars($sizeFound != 0 && $sizeFound == 1 && isset($size) ? $size['name'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="shorten">Size Shorten (*: M)</label>
                <input type="text" id="shorten" name="shorten" placeholder="Enter size shorten" value="<?php echo htmlspecialchars($sizeFound != 0 && $sizeFound == 1 && isset($size) ? $size['shorten'] : ''); ?>" required>
            </div>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($sizeFound != 0 && $sizeFound == 1 && isset($size) ? $size['id'] : ''); ?>">
            <button type="submit"><?php echo $sizeFound == 1 ? 'Update' : 'Submit' ?></button>
        </form>
    </div>
</div>

<script>
    function getRandomColor() {
        const sizes = [
            ['#ff9a9e', '#fad0c4'],
            ['#a2c2e2', '#d7aef5'],
            ['#d4fc79', '#96e6a1'],
            ['#fbc2eb', '#a6c0fe'],
            ['#f6d365', '#fda085'],
            ['#f8cdda', '#f9b8e1']
        ];
        return sizes[Math.floor(Math.random() * sizes.length)];
    }

    function setRandomGradient() {
        const [a, b] = getRandomColor();
        document.querySelector('.form-wrapper').style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
    }

    setRandomGradient();

    document.getElementById('size-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var sizeName = document.getElementById('name').value;
        var sizeShorten = document.getElementById('shorten').value;
        var sizeId = document.getElementById('id').value; // Get the ID

        var data = {
            name: sizeName,
            shorten: sizeShorten,
            id: sizeId
        };


        fetch('../../actions/admin/sizes.php', {
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
                    window.location.href = '../sizes.php';
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