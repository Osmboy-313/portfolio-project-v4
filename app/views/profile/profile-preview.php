<?php

$paginationPages = paginationDesign($currentPage, $totalPages);

?>

<div class="title"><span>Profile</span></div>

<div class="main-content profile-preview posts ">

    <!---------------- User Details ---------------->

    <section class="profile-details-card">

        <h2 class="profile-details-card__title">Profile Details</h2>

        <div class="profile-details-card__grid">
            <!-- Info rows -->
            <div class="profile-details-card__row">
                <span class="label">ID</span>
                <span class="value"><?= $user['id'] ?></span>
            </div>

            <div class="profile-details-card__row">
                <span class="label">Username</span>
                <span class="value"><?= $_SESSION['user']['username'] === $user['username'] ? $user['username'] . ' <b>(You)</b>' : $user['username']  ?></span>
            </div>

            <div class="profile-details-card__row">
                <span class="label">Email</span>
                <span class="value"><?= $user['email'] ?></span>
            </div>

            <div class="profile-details-card__row">
                <span class="label">Role</span>
                <span class="value"><?= $user['user_type'] ?></span>
            </div>

            <?php if ($_SESSION['user']['user_type'] === 'boss'): ?>
                <div class="profile-details-card__row actions">
                    <span class="label">Actions</span>
                    <div class="value">

                        <button 
                            type="button" 
                            class="btn btn--delete" 
                            data-modal-target="#del-modal"
                            data-title="Delete this User?"
                            data-message="This User and their Posts will be permanently deleted !"
                            data-form="delete-user-form"
                            data-form-action="index.php?c=user&a=delete"
                            data-delete-input="delete-user"
                            data-delete-id="<?= $user['id'] ?>"
                            data-redirect="<?= '?' . $_SERVER['QUERY_STRING'] ?>">

                            Delete

                        </button>

                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>


    <!---------------- User Posts ---------------->





    <h2 class="section-title">Posts</h2>

    <?php if (empty($posts)): ?>


        <div class="alert alert--info">
                <span class="alert__msg">No Posts Yet!</span>
        </div>

    <?php else: ?>

        <!---------------- Post ---------------->

        <section class="posts-section">

            <div class="posts-grid">

                <?php foreach ($posts as $post): ?>
                    
                    <?php $tags = explode(',', $post['post_tags']) ?>
                    <?php $date = new DateTime($post['created_at']) ?>

                    <article class="post-card">

                        <div class="post-card__image">
                            <img src="<?= 'assets/uploads/permanent/' . $post['post_image'] ?>" alt="Post image">
                        </div>

                        <div class="post-card__body">
                            <div class="post-card__info">
                                <p><span class="label">Post ID:</span> <?= $post['id'] ?></p>
                                <p><span class="label">Title:</span> <?= $post['post_title'] ?></p>
                                <p><span class="label">Category:</span> <?= $post['category_name'] ?></p>
                            </div>

                            <div class="post-card__tags">
                                <?php foreach ($tags as $tag): ?>
                                    <span class="tag"><i class="bx bxs-purchase-tag"></i><?= $tag ?></span>
                                <?php endforeach; ?>
                                <span class="tag"><i class="bx bxs-calendar"></i><?= $date->format('d-m-Y') ?></span>
                                <span class="tag"><i class="bx bx-time"></i><?= $date->format('h:i:s A') ?></span>
                            </div>

                            <p class="post-card__desc"><?= $post['post_description'] ?></p>

                            <div class="post-card__actions">

                                <a href="<?= url('home', 'preview', ['id' => $post['id']]) ?>" class="btn btn--preview">View Full Post</a>

                                <?php if ($_SESSION['user']['user_type'] === 'boss' || $_SESSION['user']['id'] === $post['post_user'] ): ?>

                                <button 
                                    type="button" 
                                    class="btn btn--delete" 
                                    data-modal-target="#del-modal"
                                    data-title="Delete this Post?" 
                                    data-message="This Post will be permanently deleted !"
                                    data-form="delete-post-form"
                                    data-form-action="index.php?c=post&a=delete"
                                    data-delete-input="delete-post" 
                                    data-delete-id="<?= $post['id'] ?>"
                                    data-redirect="<?= '?' . $_SERVER['QUERY_STRING'] ?>">

                                    Delete

                                </button>

                                <?php endif; ?>

                            </div>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        </section>


        <!---------------- Pagination ---------------->


        <div class="pagination">

            <div class="pagination__wrapper">

                <div class="dummy__div">Hallo</div>

                <div class="pagination__controls">

                    <ul>


                        <li class="<?= $currentPage === 1 ? 'disabled' : '' ?>">
                            <a
                                href="<?= url('profile', 'preview', ['id' => $user['id'], 'page' => max(1, $currentPage - 1)]) ?>">

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
                                    <a href="<?= url('profile', 'preview', ['id' => $user['id'], 'page' => $page]) ?>">
                                        <?= $page ?>
                                    </a>
                                </li>

                            <?php endif ?>

                        <?php endforeach ?>

                        <li class="<?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                            <a
                                href="<?= url('profile', 'preview', ['id' => $user['id'], 'page' => min($totalPages, $currentPage + 1)]) ?>">

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

    <?php endif; ?>




</div>