<?php

$paginationPages = paginationDesign($currentPage, $totalPages);

?>

<div class="title">
    <span>My Posts</span>
    <a href=" <?= url('post', 'add') ?> " class="btn btn--primary btn--add-post">Create Post </a>
</div>

<div class="main-content posts">

    
    <?php if (empty($posts)) : ?>
        
        <div class="alert alert--info">
             <span> No Posts Yet !!</span>
        </div>

    <?php else : ?>

        <div class="post-container">

            <?php foreach ($posts as $post) : ?>

                <?php $tags = explode(',', $post['post_tags']) ?>
                <?php $dateAndTime = new DateTime($post['created_at']) ?>

                <article class="post-card">

                    <!-- Post Content -->
                    <section class="post-card__content">

                        <!-- Post Image -->
                        <div class="post-card__image">
                            <img src="<?= 'assets/uploads/permanent/' . $post['post_image'] ?>" alt="Post image">
                        </div>

                        <!-- Post Details -->
                        <div class="post-card__details">

                            <p><strong>Post ID:</strong> <span> <?= $post['id'] ?> </span></p>
                            <p><strong>Post Title:</strong> <span> <?= $post['post_title'] ?> </span></p>

                            <div class="post-card__tags">
                                <strong>Tags:</strong>

                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag"> <i class='bx bxs-purchase-tag'></i> <span> <?= $tag ?> </span> </span>
                                <?php endforeach ?>


                                <span class="tag"> <i class='bx bxs-calendar'></i> <span> <?= $dateAndTime->format('d-m-Y') ?> </span> </span>
                                <span class="tag"> <i class='bx bx-time'></i> <span> <?= $dateAndTime->format('h:i:s A') ?> </span> </span>

                            </div>

                            <p><strong>Category:</strong> <span> <?= $post['category_name'] ?> </span></p>

                            <p>
                                <strong>Description:</strong>
                                <span> <?= $post['post_description'] ?> </span>
                            </p>

                            <!-- Action Buttons -->
                            <div class="post-card__actions">

                                <a href="<?= url('home', 'preview', ['id' => $post['id']]) ?>" class="btn btn--preview">
                                    View Full Post
                                </a>

                                <a href="<?= url('post', 'edit', ['id' => $post['id']]) ?>" class="btn btn--edit">
                                    Edit
                                </a>

                                <button
                                    type="button"
                                    class="btn btn--delete"
                                    data-modal-target="#del-modal"
                                    data-title="Delete this Post?"
                                    data-message="This Post will be permanently deleted !"
                                    data-form="delete-post-form"
                                    data-form-action="index.php?c=post&a=delete"
                                    data-delete-input = "delete-post"
                                    data-delete-id= "<?= $post['id'] ?>"
                                    data-redirect="<?= '?' . $_SERVER['QUERY_STRING'] ?>"
                                    >

                                    Delete

                                </button>

                            </div>

                        </div>
                    </section>

                </article>

            <?php endforeach ?>


        </div>



        <div class="pagination">

            <div class="pagination__wrapper">

                <div class="dummy__div">Hallo</div>

                <div class="pagination__controls">

                    <ul>


                        <li class="<?= $currentPage === 1 ? 'disabled' : '' ?>">
                            <a href="<?= url('post', 'index', ['page' => max(1, $currentPage - 1)]) ?>">

                                <i class='bx bx-chevron-left'></i>

                            </a>
                        </li>

                        <?php foreach ($paginationPages as $page): ?>

                            <?php if ($page === '...'): ?>

                                <li>
                                    <p> <?= $page ?> </p>
                                </li>

                            <?php else: ?>

                                <li class="<?= $page === $currentPage ? 'active' : '' ?>">
                                    <a href="<?= url('post', 'index', ['page' => $page]) ?>"> <?= $page ?> </a>
                                </li>

                            <?php endif ?>

                        <?php endforeach ?>

                        <li class="<?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                            <a href="<?= url('post', 'index', ['page' => min($totalPages, $currentPage + 1)]) ?>">

                                <i class='bx bx-chevron-right'></i>

                            </a>
                        </li>


                    </ul>

                </div>

                <div class="pagination__summary">

                    <p> Showing <?= $start ?> - <?= $end ?> of <?= $totalRecords ?> </p>

                </div>

            </div>

        </div>

    <?php endif ?>


</div>




