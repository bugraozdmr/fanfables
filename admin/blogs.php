<?php 
$title = "Blogs";
include './components/up-all.php' ?>

<?php
include '../actions/connect.php';

try {
    $query = "SELECT * FROM Blogs";
    $stmt = $db->prepare($query);
    $stmt->execute();

    // Fetch all records
    $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<div class="container">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Blogs</h3>
                <h6 class="op-7 mb-2">Manage all the Blogs.<span style="color:darkcyan">Add,delete,update</span></h6>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="card-title">Blogs Table</div>
                    <a href="./manage/blogs.php">
                        <button class="btn btn-primary add-color-btn" data-bs-toggle="modal" data-bs-target="#addblogModal">
                            <i class="fas fa-plus"></i> Add Blogs
                        </button>
                    </a>
                </div>
                <div id="error-message" class="alert alert-danger mx-2 my-2 d-none" role="alert"></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Blog Name</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($blogs)) : ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; font-size: larger; font-weight: bold;">
                                            THERE IS NO Blog YET
                                        </td>
                                    </tr>
                                <?php else : ?>

                                    <?php foreach ($blogs as  $index => $blog) : ?>

                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($blog['name']); ?></td>
                                            <td>
                                                <div class="icon-container">
                                                    <a href="./manage/Blogs.php?id=<?php echo htmlspecialchars($blog['id']); ?>"><i class="fas fa-edit icon-edit" title="Edit"></i></a>
                                                    <!-- DELETE Modal -->
                                                    <i class="fas fa-trash-alt icon-delete" title="Delete" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?php echo htmlspecialchars($blog['id']); ?>"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this item?
                <input type="hidden" id="blog-id" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="delete-button">Delete</button>
            </div>
        </div>
    </div>
</div>



<script>
document.querySelectorAll('.icon-delete').forEach(button => {
    button.addEventListener('click', function() {
        var blogId = this.getAttribute('data-id');
        document.getElementById('blog-id').value = blogId;
    });
});

document.getElementById('delete-button').addEventListener('click', function() {
    var blogId = document.getElementById('blog-id').value;


    fetch('../actions/admin/blogs.php', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: blogId })
        })
        .then(response => response.json())
        .then(data => {
            const errorMessageElement = document.getElementById('error-message');

            if (data.status === 'success') {
                window.location.href = './blogs.php';
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