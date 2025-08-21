<?php 

$currentUserId = $_SESSION['user']['id'] ?? 0;

?>
<div class="title">
    
    <span>Global Categories</span>

    <button
        type="button"
        class="btn btn--primary btn--add-category"
        data-modal-target="#add-modal"
        data-title="Create Global Category"
        data-label="Category Name"
        data-placeholder="Enter category name"
        data-form="add-category-form">

        Create Category

    </button>

</div>

<div class="main-content categories">

    <div class="alert de-active"> 
        <span class="alert__msg">No categories found! Create the first category to get started.</span> 
    </div>

    <table class="custom-table styled-table de-active">
        <thead>
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Created By</th>
                <th>Posts</th>
                <th>Created</th>
                <th class="action">Actions</th>
            </tr>
        </thead>

        <tbody>
            <!-- Categories will be loaded here via JavaScript -->
        </tbody>
    </table>

    <div class="pagination de-active">

        <div class="pagination__wrapper">

            <div class="dummy__div">Hallo</div>

            <div class="pagination__controls">

                <!-- Pagination will be loaded here via JavaScript -->

            </div>

            <div class="pagination__summary">

                <p>No categories available</p>

            </div>

        </div>

    </div>

</div>

<script>
// Pass current user ID to JavaScript for ownership checks
window.currentUserId = <?= $currentUserId ?>;
// console.log('Current user ID:', window.currentUserId); // Debug
</script>

