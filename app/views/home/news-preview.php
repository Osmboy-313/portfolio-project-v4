<?php

// echo '<pre>';
// print_r($latestPosts);
// echo '</pre>';

$tags = explode(',', $post['post_tags']);
$dateAndTime = new DateTime($post['created_at']);

?>

<div class="posts-page posts-page--preview">


    <div class="posts-search">

        <div class="posts-search__title">Search Posts</div>

        <div class="posts-search__field">
            <form action="">

                <div class="posts-search__input-box">
                    <input type="text" name="search" class="search-input" placeholder="Search Posts">
                    <i class='bx bx-search'></i>
                    <i class='bx bx-x right-icon'></i>
                </div>

            </form>
        </div>

    </div>


    <div class="post-preview">

        <div class="post-preview__title"> <span> <?= $post['post_title'] ?> </span> </div>

        <div class="post-preview__tags">

            <?php foreach ($tags as $tag): ?>
                <span class="post-preview__tag"> <i class='bx bxs-purchase-tag'></i> <span> <?= $tag ?> </span> </span>
            <?php endforeach ?>

            <span class="post-preview__tag">
                <i class='bx bxs-calendar'></i>
                <span> <?= $dateAndTime->format('d-m-Y') ?> </span>
            </span>

            <span class="post-preview__tag">
                <i class='bx bx-time'></i>
                <span> <?= $dateAndTime->format('h:i:s A') ?> </span>
            </span>

        </div>

        <div class="post-preview__image"><img src="<?= 'assets/uploads/permanent/' . $post['post_image'] ?>" alt=""></div>

        <div class="post-preview__description"> <?= $post['post_description'] ?> </div>

    </div>


    <div class="posts-page__list">

        <div class="posts-page__heading--list">Recent Posts</div>

        <?php if(empty($latestPosts)) : ?>

            <div class="alert alert--info">
                <span class="alert__msg">No other Posts to show !</span>
            </div>
            
        <?php endif ?>


            <?php foreach ($latestPosts as $latestPost): ?>

                <?php $tags = explode(',', $latestPost['post_tags']) ?>
                <?php $dateAndTime = new DateTime($latestPost['created_at']) ?>

                <div class="post">

                    <div class="post__image"><img src=" <?= 'assets/uploads/permanent/' . $latestPost['post_image'] ?> " alt=""></div>

                    <div class="post__details">

                        <div class="post__title"> <?= $latestPost['post_title'] ?> </div>

                        <div class="post__tags">

                            <?php foreach ($tags as $tag): ?>
                                <span> <i class='bx bxs-purchase-tag'></i> <span><?= $tag ?></span> </span>
                            <?php endforeach ?>

                            <span> <i class='bx bxs-calendar'></i> <span> <?= $dateAndTime->format('d-m-Y') ?> </span> </span>
                            <span> <i class='bx bx-time'></i> <span> <?= $dateAndTime->format('h:i:s A') ?> </span> </span>

                        </div>

                        <div class="post__description"> <?= $latestPost['post_description'] ?> </div>

                        <div class="post__buttons">

                            <a href="<?= url('home', 'preview', ['id' => $latestPost['id']]) ?>">
                                <span>Read More</span>
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach ?>




    </div>


</div>