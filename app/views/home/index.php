<?php

$searchQuery = $searchQuery ?? '';
$categoryFilter = $categoryFilter ?? null;
$categoryName = $categoryName ?? null;
$hasSearchResults = !empty($searchQuery);
$hasCategoryFilter = !empty($categoryFilter);

?>

<div class="posts-page posts-page--list">

    <div class="posts-search">

        <div class="posts-search__title">Search Posts</div>

        <div class="posts-search__field">
            <form action="index.php" method="GET">
                <input type="hidden" name="c" value="home">
                <input type="hidden" name="a" value="index">
                
                <div class="posts-search__input-box">
                    <input type="text" name="search" class="search-input" placeholder="Search Posts" value="<?= htmlspecialchars($searchQuery) ?>">
                    <i class='bx bx-search'></i>

                    <!-- <button type="submit"> <i class='bx bx-search'></i> </button> -->

                    <?php if (!empty($searchQuery)): ?>
                       <a href="index.php?c=home&a=index"> <i class='bx bx-x right-icon'> </i></a>
                    <?php endif; ?>
                </div>

            </form>
        </div>

        <?php if ($hasSearchResults): ?>
            <div class="search-results-info">
                <p>Search results for: <strong>"<?= htmlspecialchars($searchQuery) ?>"</strong></p>
                <p>Found <?= count($posts) ?> post(s)</p>
            </div>
        <?php endif; ?>

        <?php if ($hasCategoryFilter): ?>
            <div class="category-filter-info">
                <p>Showing posts in category: <strong>"<?= htmlspecialchars($categoryName) ?>"</strong></p>
                <p>Found <?= count($posts) ?> post(s)</p>
                <a href="index.php?c=home&a=index" class="clear-category-filter">View All Posts</a>
            </div>
        <?php endif; ?>

    </div>

    <div class="posts-page__list">

        <!-- <div class="alert alert--info">
             <span> No records found !! </span>
        </div> -->

        <?php if (empty($posts)): ?>

            <div class="no-posts-message">
                <?php if ($hasSearchResults): ?>
                    <p>No posts found matching your search: <strong>"<?= htmlspecialchars($searchQuery) ?>"</strong></p>
                    <p>Try different keywords or <a href="index.php?c=home&a=index">browse all posts</a></p>
                <?php elseif ($hasCategoryFilter): ?>
                    <p>No posts found in category: <strong>"<?= htmlspecialchars($categoryName) ?>"</strong></p>
                    <p><a href="index.php?c=home&a=index">Browse all posts</a> or try another category</p>
                <?php else: ?>
                    <p>No posts available at the moment.</p>
                <?php endif; ?>
            </div>

        <?php else: ?>

            <?php foreach ($posts as $post): ?>

                <?php $tags = explode(',', $post['post_tags']) ?>
                <?php $dateAndTime = new DateTime($post['created_at']) ?>

                <div class="post">

                    <div class="post__image"><img src=" <?= 'assets/uploads/permanent/' . $post['post_image'] ?> " alt=""></div>

                    <div class="post__details">

                        <div class="post__title"> <?= $post['post_title'] ?> </div>

                        <div class="post__tags">

                            <?php foreach ($tags as $tag): ?>
                                <span> <i class='bx bxs-purchase-tag'></i> <span><?= $tag ?></span> </span>
                            <?php endforeach ?>

                            <span> <i class='bx bxs-calendar'></i> <span> <?= $dateAndTime->format('d-m-Y') ?> </span> </span>
                            <span> <i class='bx bx-time'></i> <span> <?= $dateAndTime->format('h:i:s A') ?> </span> </span>

                        </div>

                        <div class="post__description"> <?= $post['post_description'] ?> </div>

                        <div class="post__buttons">

                            <a href="<?= url('home', 'preview', ['id' => $post['id']]) ?>">
                                <span>Read More</span>
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach ?>
        <?php endif; ?>

    </div>

</div>