<?php
$title = "Sitemap";
include __DIR__.'/components/up-all.php' ?>


<!-- Classnames added to css -->
<div class="form-wrapper">
    <div class="form-container">
        <h2>Upload Sitemap</h2>
        <div id="error-message" class="alert alert-danger d-none" role="alert"></div>
        <form id="sitemap-form">
            <div class="form-group">
                <label for="sitemap">Upload Sitemap</label>
                <input type="file" id="sitemap" name="sitemap" accept=".xml">
                <small class="form-text text-muted">Select Sitemap (XML)</small>
            </div>
            <button type="submit">DO IT !</button>
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

    document.getElementById('sitemap-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('../actions/sitemap/upload-sitemap.php', {
                method: 'POST',
                body: formData
        })
        .then(response => response.json())
        .then(data => {
            const errorMessageElement = document.getElementById('error-message');

            if (data.status === 'success') {
                window.location.href = './index.php';
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

<?php include './components/down-all.php' ?>