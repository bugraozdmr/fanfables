<?php include '../components/up-all.php' ?>

<?php
// I NEED THIS
$scategoryFound = 0;
if (isset($_GET['id'])) {
    include '../../actions/connect.php';

    $id = $_GET['id'];

    try {
        $query = "SELECT * FROM Types WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the record
        $scategory = $stmt->fetch(PDO::FETCH_ASSOC);

        $scategoryFound = 1;

        if (!$scategory) {
            $scategoryFound = 2;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Types Form</h2>
        <?php
        if ($scategoryFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The Type with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="scategory-form">
            <div class="form-group">
                <label for="name">Type Name (*: Film)</label>
                <input type="text" id="name" name="name" placeholder="Enter scategory name" value="<?php echo htmlspecialchars($scategoryFound != 0 && $scategoryFound == 1 && isset($scategory) ? $scategory['name'] : ''); ?>" required>
            </div>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($scategoryFound != 0 && $scategoryFound == 1 && isset($scategory) ? $scategory['id'] : ''); ?>">
            <button type="submit"><?php echo $scategoryFound == 1 ? 'Update' : 'Submit' ?></button>
        </form>
    </div>
</div>

<script>
    function getRandomColor() {
        const scategorys = [
            ['#ff9a9e', '#fad0c4'],
            ['#a2c2e2', '#d7aef5'],
            ['#d4fc79', '#96e6a1'],
            ['#fbc2eb', '#a6c0fe'],
            ['#f6d365', '#fda085'],
            ['#f8cdda', '#f9b8e1']
        ];
        return scategorys[Math.floor(Math.random() * scategorys.length)];
    }

    function setRandomGradient() {
        const [a, b] = getRandomColor();
        document.querySelector('.form-wrapper').style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
    }

    setRandomGradient();

    document.getElementById('scategory-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var scategoryName = document.getElementById('name').value;
        var scategoryId = document.getElementById('id').value; // Get the ID

        var data = {
            name: scategoryName,
            id: scategoryId
        };


        fetch('../../actions/admin/types.php', {
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
                    window.location.href = '../types.php';
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