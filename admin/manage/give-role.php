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
        //* ALL ROLES
        $query = "SELECT * FROM roles";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $roles = $stmt->fetchAll();

        //*---

        $query = "SELECT u.username as username, u.id as id,
                GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ', ') as roles,
                GROUP_CONCAT(r.normalized_name ORDER BY r.normalized_name SEPARATOR ', ') as normalized_roles
        FROM UserRoles ur
        JOIN users u ON u.id = ur.userId
        JOIN roles r ON r.id = ur.roleId
        WHERE ur.userId = :id
        GROUP BY u.username";

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
            $normalizedRoles = explode(',', $user['normalized_roles']);
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
        <h2>Give A Role</h2>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="user-form">
            <div class="form-group">
                <label for="role">Roles <a style="text-decoration: none;color:gray;" target="_blank" href="<?php echo $dynamicUrl . "/user/" . $user['username'] ?>"><?php echo $user['username'] ?></a>
                </label>
                <?php if (!empty($roles)) : ?>
                    <?php foreach ($roles as $role) : ?>
                        <div class="form-check d-flex align-items-center">
                            <div class="col-md-1">
                                <?php
                                $isChecked = (isset($normalizedRoles) && is_array($normalizedRoles) && in_array(trim(strtolower($role['normalized_name'])), array_map('strtolower', array_map('trim', $normalizedRoles)))) ? 'checked' : '';
                                ?>
                                <input class="form-check-input" type="checkbox" id="<?php echo $role['name'] ?>" name="roles[]" value="<?php echo $role['normalized_name'] ?>" <?php echo $isChecked ?>>
                            </div>
                            <div class="col-md-5 col-sm-5 mt-3 mx-3">
                                <label class="form-check-label" for="<?php echo $role['name'] ?>"><?php echo $role['name'] ?></label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p>There is no role yet.Add some.</p>
                <?php endif; ?>
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

        var roles = Array.from(document.querySelectorAll('input[name="roles[]"]:checked')).map(checkbox => checkbox.value);
        var userId = document.getElementById('id').value;



        var data = {
            roles: roles,
            id: userId
        };



        fetch('../../actions/admin/give-role.php', {
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