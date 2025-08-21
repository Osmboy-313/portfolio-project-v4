<?php

include VIEWPATH . '/layouts/head.php';

if (isset($_SESSION['user'])) {
    $id = (int) $_SESSION['user']['id'] ?? 0;
    $username = $_SESSION['user']['username'];
    $user_type = $_SESSION['user']['user_type'];
}

// Get categories with post counts for navigation
require_once __DIR__ . '/../../models/category.php';
$navigationCategories = getCategoriesWithPostCounts();

?>

<body>

    <div class="app app--public">

        <!-------------------- Header -------------------->

        <div class="header">

            <div class="header__wrapper">

                <div class="header__logo">
                    <a href="<?= url('home', 'index') ?>" class="header__logo-link">The Commons</a>
                </div>

                <div class="header__nav">
                    <ul class="header__menu">
                        <?php if (!empty($navigationCategories)): ?>
                            <?php foreach ($navigationCategories as $category): ?>
                                <li class="header__item">
                                    <a href="<?= url('home', 'index', ['category' => $category['id']]) ?>" 
                                       class="header__link <?= (isset($_GET['category']) && $_GET['category'] == $category['id']) ? 'active' : '' ?>">
                                        <?= htmlspecialchars($category['category_name']) ?>
                                        <span class="post-count"><?= $category['post_count'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li class="header__item">
                                <a href="<?= url('home', 'index') ?>" 
                                   class="header__link <?= (!isset($_GET['category'])) ? 'active' : '' ?>">
                                    All Posts
                                    <span class="post-count"> <?= array_sum(array_column($navigationCategories, 'post_count')) ?> </span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="header__item"><a href="#" class="header__link">No Categories Yet</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="header__auth">

                    <?php if (isset($_SESSION['user'])) : ?>

                        <div class="header__dropdown" id="user-dropdown">

                            <span class="header__username"><?= $_SESSION['user']['username'] ?></span>
                            <i class='bx bx-chevron-down'></i>

                            <div class="header__main-dropdown" id="main-dropdown">

                                <ul class="header__menu">

                                    <li class="header__item">
                                        <a href="<?= url('profile', 'myProfile', ['id' => $id]) ?>" class="header__link">
                                            <i class='bx bx-user'></i> <span>Profile</span>
                                        </a>
                                    </li>

                                    <li class="header__item">
                                        <a href="<?= url('dashboard', 'index') ?>" class="header__link">
                                            <i class='bx bxs-dashboard'></i> <span>Dashboard</span>
                                        </a>
                                    </li>

                                    <li class="header__item">
                                        <a href="<?= url('auth', 'logout') ?>" class="header__link">
                                            <i class='bx bx-power-off'></i> <span>Logout</span>
                                        </a>
                                    </li>

                                </ul>

                            </div>

                        </div>

                    <?php else : ?>

                        <a href="<?= url('auth', 'index') ?>" class="header__login-btn">
                            <span>Login / Register</span>
                        </a>

                    <?php endif ?>

                </div>

            </div>

        </div>

        <!-------------------- Content -------------------->

        <div class="content">

            <?php echo $content ?? ''; ?>

        </div>

        <!-------------------- Footer -------------------->

        <div class="footer">

            <div class="footer__wrapper">

                <span>&copy; 2025 The Commons. A full-stack forum project by Osmboy.</span>

            </div>

        </div>
        

    </div>

    <script type="module" src="assets/javascript/script.js"></script>

</body>

</html>