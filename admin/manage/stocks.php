<?php include '../components/up-all.php' ?>

<?php
include '../../actions/connect.php';


// I NEED THIS
$stockFound = 0;

//* getting only what needed FROM PRODUCTS
$query = "SELECT id,name,product_code FROM Products";
$stmt = $db->prepare($query);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);


//* SIZES
$query = "SELECT * FROM Sizes";
$stmt = $db->prepare($query);
$stmt->execute();
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);


//* COLORS
$query = "SELECT * FROM Colors";
$stmt = $db->prepare($query);
$stmt->execute();
$colors = $stmt->fetchAll(PDO::FETCH_ASSOC);



if (isset($_GET['id'])) {

    $id = $_GET['id'];

    try {
        $query = "SELECT * FROM Stocks WHERE id=:id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the record
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);

        $stockFound = 1;

        if (!$stock) {
            $stockFound = 2;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Stocks Form</h2>
        <span><span class="text-warning">IMPORTANT</span> : If you have sold all products update it to 0</span>
        <?php
        if ($stockFound == 2) {
            echo '<div id="error-message" class="alert alert-warning" role="alert">The stocks with the specified id could not be found. Maybe create a new one.</div>';
        }
        ?>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="size-form">
            <!-- ID FIELD -->
            <div class="form-group">
                <!-- GETTING THE ID BY ASKING THE PRODUCT CODE -->
                <label for="productId">Select The Product</label>
                <select class="form-control custom-select" id="productId" name="productId">
                    <option value="" disabled selected>Select a product</option>
                    <?php if (!empty($products)) : ?>
                    <?php foreach ($products as $product) : ?>
                        <option 
                        <?php echo $stockFound != 0 && $stockFound == 1 && isset($stock) && ($product['id'] == $stock['productId']) ? 'selected' : "" ?> 
                        value="<?= htmlspecialchars($product['id']) ?>">
                        <?= htmlspecialchars($product['name']).' - '.htmlspecialchars($product['product_code']) ?>
                        </option>
                    <?php endforeach; ?>
                    <?php else : ?>
                        <p>There is no products yet</p>
                    <?php endif; ?>
                </select>
            </div>
            <!-- SIZE FIELD -->
            <div class="form-group">
                <!-- GETTING THE ID BY ASKING THE Size -->
                <label for="sizeId">Select The Size</label>
                <select class="form-control custom-select" id="sizeId" name="sizeId">
                    <option value="" disabled selected>Select a Size</option>
                    <?php if (!empty($sizes)) : ?>
                    <?php foreach ($sizes as $size) : ?>
                        <option 
                        <?php echo $stockFound != 0 && $stockFound == 1 && isset($stock) && ($size['id'] == $stock['sizeId']) ? 'selected' : "" ?> 
                        value="<?= htmlspecialchars($size['id']) ?>">
                        <?= htmlspecialchars($size['name']).' - '.htmlspecialchars($size['shorten']) ?>
                        </option>
                    <?php endforeach; ?>
                    <?php else : ?>
                        <p>There is no sizes yet</p>
                    <?php endif; ?>
                </select>
            </div>
            <!-- COLOR FIELD -->
            <div class="form-group">
                <!-- GETTING THE ID BY ASKING THE Color -->
                <label for="colorId">Select The Color</label>
                <select class="form-control custom-select" id="colorId" name="colorId">
                    <option value="" disabled selected>Select a color</option>
                    <?php if (!empty($colors)) : ?>
                    <?php foreach ($colors as $color) : ?>
                        <option 
                        <?php echo $stockFound != 0 && $stockFound == 1 && isset($stock) && ($color['id'] == $stock['colorId']) ? 'selected' : "" ?> 
                        value="<?= htmlspecialchars($color['id']) ?>">
                        <?= htmlspecialchars($color['name']) ?>
                        </option>
                    <?php endforeach; ?>
                    <?php else : ?>
                        <p>There is no colors yet</p>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount (*: 20)</label>
                <input type="number" id="amount" name="amount" placeholder="Enter product amount" value="<?php echo htmlspecialchars($stockFound != 0 && $stockFound == 1 && isset($stock) ? $stock['amount'] : ''); ?>" min="0" step="1" required>
            </div>
            <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($stockFound != 0 && $stockFound == 1 && isset($stock) ? $stock['id'] : ''); ?>">
            <button type="submit"><?php echo $stockFound == 1 ? 'Update' : 'Submit' ?></button>
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

        var productId = document.getElementById('productId').value;
        var colorId = document.getElementById('colorId').value;
        var sizeId = document.getElementById('sizeId').value;
        var stockId = document.getElementById('id').value; // Get the ID
        var amount = document.getElementById('amount').value;

        var data = {
            productId: productId,
            colorId: colorId,
            sizeId: sizeId,
            amount: amount,
            id : stockId
        };


        fetch('../../actions/admin/stocks.php', {
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
                    window.location.href = '../stocks.php';
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