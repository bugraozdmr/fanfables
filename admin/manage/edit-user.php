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
            $title = 'Edit User '.$user['username'];
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
        <h2>Edit User Info (<?php echo $user['username'] ?>)</h2>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="user-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter username" value="<?php echo htmlspecialchars($userFound != 0 && $userFound == 1 && isset($user) ? $user['username'] : ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter scategory name" value="<?php echo htmlspecialchars($userFound != 0 && $userFound == 1 && isset($user) ? $user['name'] : ''); ?>">
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" placeholder="Enter Description" value="<?php echo htmlspecialchars($userFound != 0 && $userFound == 1 && isset($user) ? $user['description'] : ''); ?>">
            </div>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($userFound != 0 && $userFound == 1 && isset($user) ? $user['id'] : ''); ?>">

            <div class="mb-2 col-lg-12 text-center row mx-1 mb-3">
                <div class="col-lg-6 mb-1">
                    <button type="submit" name="action" value="remove-image" class="btn btn-warning">Remove User Image <i class="fa-solid fa-image"></i></button>
                </div>
                <div class="col-lg-6">
                <button type="submit" name="action" value="remove-banner" class="btn btn-danger">Remove Banner <i class="fa-solid fa-image"></i></button>
                </div>
            </div>

            <button type="submit" name="action" value="submit"><?php echo $userFound == 1 ? 'Update' : 'Submit' ?></button>
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
        var action = event.submitter.value;

        if (action === 'remove-image') {
            var data = {
                id: userId,
                action:"remove-image"
            };
        } else if (action === 'remove-banner') {
            var data = {
                id: userId,
                action:"remove-banner"
            };
        }
        else{
            var username = document.getElementById('username').value;
            var name = document.getElementById('name').value;
            var description = document.getElementById('description').value;

            var data = {
                id: userId,
                username: username,
                name: name,
                description: description,
                action:"submit"
            };
        }
        //ORTAK
        fetch('../../actions/admin/edit-user-info.php', {
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