<?php

// I NEED THIS
$userFound = 0;
if (isset($_GET['id'])) {
    $jsonFile = __DIR__ . '/../../settings.json';
    $jsonData = file_get_contents($jsonFile);
    $data = json_decode($jsonData, true);
    $dynamicUrl = isset($data['dynamic_url']) ? $data['dynamic_url'] : '';

    include '../../actions/connect.php';

    $id = $_GET['id'];

    try {
        $query = "SELECT u.username as username,u.name as name, u.id as id,u.description as description
        FROM users u
        WHERE u.id = :id";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Fetch the record
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $userFound = 1;

        if (!$user) {
            header('Location: /anime/admin/users.php');
            exit();
        }
        else{
            $title = 'Ban User '.$user['username'];
        }

        include '../components/up-all.php';
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Ban User (<?php echo $user['username'] ?>)</h2>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="user-form">
            <div class="form-group col-lg-1 col-md-1 col-sm-1 col-1">
                <label for="time">Time</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="time" id="oneDay" value="1_day" checked>
                    <label class="form-check-label" for="oneDay">1 Gün</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="time" id="twoDays" value="2_days">
                    <label class="form-check-label" for="twoDays">2 Gün</label>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="time" id="oneWeek" value="1_week">
                    <label class="form-check-label" for="oneWeek">1 Hafta</label>
                </div>
                
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="time" id="unlimited" value="unlimited">
                    <label class="form-check-label" for="unlimited">Süresiz</label>
                </div>
            </div>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($userFound != 0 && $userFound == 1 && isset($user) ? $user['id'] : ''); ?>">
            <button type="submit"><?php echo $userFound == 1 ? 'Update' : 'Submit' ?></button>
        </form>
    </div>
</div>

<script>
    function getRandomColor() {
        const users = [
            ['#ff9a9e', '#fad0c4'],
            ['#a2c2e2', '#d7aef5'],
            ['#d4fc79', '#96e6a1'],
            ['#fbc2eb', '#a6c0fe'],
            ['#f6d365', '#fda085'],
            ['#f8cdda', '#f9b8e1']
        ];
        return users[Math.floor(Math.random() * users.length)];
    }

    function setRandomGradient() {
        const [a, b] = getRandomColor();
        document.querySelector('.form-wrapper').style.background = `linear-gradient(135deg, ${a} 0%, ${b} 100%)`;
    }

    setRandomGradient();

    document.getElementById('user-form').addEventListener('submit', function(event) {
        event.preventDefault();

        var userId = document.getElementById('id').value;
        var selectedTime = document.querySelector('input[name="time"]:checked').value;
        var data = {
            id: userId,
            time: selectedTime,
        };

        fetch('../../actions/admin/ban-user.php', {
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
                window.location.href = '../users.php';
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