<?php

include VIEWPATH . '/layouts/head.php';

$id = (int) $_SESSION['user']['id'] ?? 0;
$username = $_SESSION['user']['username'];
$user_type = $_SESSION['user']['user_type'];

// Get global categories for sidebar
require_once __DIR__ . '/../../models/category.php';
$sidebarCategories = getAllGlobalCategories();

switch ($user_type) {

    case 'user':
        $menuItems = [
            ['controller' => 'post', 'action' => 'add', 'label' => 'Create Post'],
            ['controller' => 'post', 'action' => 'index', 'label' => 'My Posts'],
            ['controller' => 'category', 'action' => 'index', 'label' => 'Global Categories'],
        ];
        break;

    case 'admin':
        $menuItems = [
            ['controller' => 'user', 'action' => 'index', 'label' => 'Users'],
            ['controller' => 'post', 'action' => 'all', 'label' => 'All Posts'],
            ['controller' => 'post', 'action' => 'add', 'label' => 'Create Post'],
            ['controller' => 'post', 'action' => 'index', 'label' => 'My Posts'],
            ['controller' => 'category', 'action' => 'index', 'label' => 'Global Categories'],
        ];
        break;

    case 'boss':
        $menuItems = [
            ['controller' => 'user', 'action' => 'index', 'label' => 'Users'],
            ['controller' => 'post', 'action' => 'all', 'label' => 'All Posts'],
            ['controller' => 'post', 'action' => 'add', 'label' => 'Create Post'],
            ['controller' => 'post', 'action' => 'index', 'label' => 'My Posts'],
            ['controller' => 'category', 'action' => 'index', 'label' => 'Global Categories'],
            ['controller' => 'code', 'action' => 'index', 'label' => 'Codes'],
        ];
        break;
}

?>

<body>

    <div class="app app--private">

        <div class="sidebar" id="sidebar">
            <div class="sidebar__title"><a href="index.php"><span>Home</span></a></div>

            <ul class="sidebar__menu">

                <?php foreach ($menuItems as $items): ?>
                    <li class=" sidebar__item <?= active($items['controller'], $items['action']) ?>">
                        <a href="<?= url($items['controller'], $items['action']) ?>" class="sidebar__link" >
                            <!-- <i class='bx bx-book'></i> --> <i class='bx bx-chevron-right' ></i>  <?= $items['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>

            </ul>

        </div>

        <div class="main-layout">

            <div class="header">

                <div id="toggle-sidebar" class="header__toggle-sidebar">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div class="header__dropdown" id="user-dropdown">

                    <!-- <img src="assets/images/wallpaperflare.com_wallpaper(1).jpg" alt=""> -->
                    <span class="header__username"><?= $username ?></span>
                    <i class='bx bx-chevron-down '></i>
                    
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
                                <a href="<?= url('auth', 'logout', ['id' => $id]) ?>" class="header__link"> 
                                    <i class='bx bx-power-off'></i> <span>logout</span>
                                </a>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>


            <div class="content">

                <div class="content-card">

                    <?php echo $content ?? ""; ?>

                </div>

            </div>

            <?php echo $modals ?? ""; ?>

            <div class="footer">&copy; 2025 The Commons. A full-stack forum project by Osmboy.</div>

        </div>

    </div>

    <script type="module" src="assets/javascript/script.js"></script>

</body>

</html>