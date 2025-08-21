<?php 

require_once __DIR__ .  '/../models/post.php';
require_once __DIR__ .  '/../models/category.php';
require_once __DIR__ .  '/../core/view.php';
require_once __DIR__ .  '/../core/auth.php';

function home_index(){
  // Get search query and category filter from URL
  $searchQuery = $_GET['search'] ?? '';
  $categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : null;
  $posts = [];
  $categoryName = null;
  
  if (!empty($searchQuery)) {
    // Search posts if query exists
    $posts = searchPosts($searchQuery);
  } elseif ($categoryFilter) {
    // Filter posts by category if category is selected
    $posts = getPostsByCategory($categoryFilter);
    $categoryName = getCategoryName($categoryFilter);
  } else {
    // Get all posts if no search or category filter
    $posts = getAllPosts();
  }
  
  echo view('/home/index', [
    'title' => 'Home', 
    'posts' => $posts, 
    'searchQuery' => $searchQuery,
    'categoryFilter' => $categoryFilter,
    'categoryName' => $categoryName
  ], 'public');
}

function home_preview(){

  $postId = $_GET['id'] ?? 0;
  $post = getPostById($postId);
  $latestPosts = getLatestPosts(10, $postId);

  echo view('/home/news-preview' ,['title' => 'Preview', 'post' => $post, 'latestPosts' => $latestPosts], 'public');
}

?>