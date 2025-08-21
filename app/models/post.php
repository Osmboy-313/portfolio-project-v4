<?php

require_once __DIR__ . '/../core/db.php';

function addPost($title, $tags, $description, $category, $img_O_name, $img_S_name)
{

    $conn = db();

    $id = $_SESSION['user']['id'];
    $sql = $conn->prepare('INSERT INTO `posts`(`post_title`, `post_tags`, `post_description`, `post_category`, `post_image`, `post_img_original_name`, `post_user`) VALUES ( ? , ? , ? , ? , ? , ? , ? )');
    $sql->bind_param('sssissi', $title, $tags, $description, $category, $img_S_name, $img_O_name, $id);

    return $sql->execute();
}

function updatePost($id, $title, $tags, $description, $category, $img_O_name, $img_S_name)
{

    $conn = db();

    $sql = $conn->prepare('UPDATE `posts` SET `post_title`= ? ,`post_tags`= ? ,`post_description`= ? ,`post_category`= ? ,`post_image`= ? ,`post_img_original_name`= ?  WHERE `id` = ? ');
    $sql->bind_param('sssissi', $title, $tags, $description, $category, $img_S_name, $img_O_name, $id);

    return $sql->execute();
}

function deletePost($id){
    $conn = db();

    $sql = $conn->prepare('DELETE FROM `posts` WHERE id = ?');
    $sql->bind_param('i', $id);

    return $sql->execute();
}

function getUserPaginatedPosts($recordsPerPage, $offset)
{
    $conn = db();

    $id = $_SESSION['user']['id'];

    $sql = $conn->prepare("SELECT posts.*, categories.category_name FROM `posts` JOIN categories ON posts.post_category = categories.id WHERE posts.post_user = ? LIMIT ? OFFSET ? ");
    $sql->bind_param('iii', $id, $recordsPerPage,  $offset);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function countUserPosts() {
    $conn = db();
    $id = $_SESSION['user']['id'];
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM posts WHERE post_user = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return (int)$res['cnt'];
}

function getPostById($id)
{
    $conn = db();

    $sql = $conn->prepare("SELECT posts.*, categories.category_name FROM `posts` JOIN categories ON posts.post_category = categories.id WHERE posts.id = $id");
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_assoc();
}

function getAllPosts()
{

    $conn = db();

    $sql = $conn->prepare("SELECT 
        posts.*,
        users.id AS user_id,
        users.username AS username,
        categories.id AS category_id,
        categories.category_name AS category_name
    FROM posts
    JOIN users ON posts.post_user = users.id
    JOIN categories ON posts.post_category = categories.id
    ORDER BY posts.created_at DESC");

    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllPaginatedPosts($recordsPerPage, $offset){

    $conn = db();

    $sql = $conn->prepare("SELECT 
        posts.*,
        users.id AS user_id,
        users.username AS username,
        categories.id AS category_id,
        categories.category_name AS category_name
    FROM posts
    JOIN users ON posts.post_user = users.id
    JOIN categories ON posts.post_category = categories.id
    ORDER BY posts.created_at DESC LIMIT ? OFFSET ?");

    $sql->bind_param('ii',$recordsPerPage, $offset);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function countAllPaginatedPosts($id){
    $conn = db();

    $sql = $conn->prepare("SELECT COUNT(*) AS total FROM posts WHERE post_user = ?");
    $sql->bind_param('i', $id);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_assoc()['total'];
}

function countAllPosts() {
    $conn = db();

    $sql = $conn->prepare("SELECT COUNT(*) AS total FROM posts");
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_assoc()['total'];
}

function getPostsByCategoryId($id)
{

    $conn = db();

    $sql = $conn->prepare('SELECT `post_image` FROM `posts` WHERE `post_category` = ?');
    $sql->bind_param('i', $id);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function deletePostsByCategory($id): bool
{
    $conn = db();
    $sql = $conn->prepare("DELETE FROM posts WHERE post_category = ?");
    $sql->bind_param('i', $id);

    return $sql->execute();
}

function getLatestPosts($limit, $excludeId)
{
    $conn = db();

    $sql = $conn->prepare("SELECT posts.*, categories.category_name FROM posts JOIN categories ON posts.post_category = categories.id WHERE posts.id != ? ORDER BY posts.created_at DESC LIMIT ?");
    $sql->bind_param('ii', $excludeId, $limit);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getPostsbyUserId($id) {

    $conn = db();

    $sql = $conn->prepare("SELECT posts.*, categories.category_name FROM posts JOIN categories ON posts.post_category = categories.id WHERE posts.post_user = ?");
    $sql->bind_param('i', $id,);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);

}

// 🔍 NEW: Search posts function
function searchPosts($searchQuery) {
    $conn = db();
    
    // Clean the search query
    $searchQuery = '%' . trim($searchQuery) . '%';
    
    $sql = $conn->prepare("SELECT 
        posts.*,
        users.id AS user_id,
        users.username AS username,
        categories.id AS category_id,
        categories.category_name AS category_name
    FROM posts
    JOIN users ON posts.post_user = users.id
    JOIN categories ON posts.post_category = categories.id
    WHERE posts.post_title LIKE ? 
       OR posts.post_tags LIKE ? 
       OR posts.post_description LIKE ? 
       OR categories.category_name LIKE ?
    ORDER BY posts.created_at DESC");

    $sql->bind_param('ssss', $searchQuery, $searchQuery, $searchQuery, $searchQuery);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

// 🔍 NEW: Get posts by category
function getPostsByCategory($categoryId) {
    $conn = db();
    
    $sql = $conn->prepare("SELECT 
        posts.*,
        users.id AS user_id,
        users.username AS username,
        categories.id AS category_id,
        categories.category_name AS category_name
    FROM posts
    JOIN users ON posts.post_user = users.id
    JOIN categories ON posts.post_category = categories.id
    WHERE posts.post_category = ? AND categories.is_active = 1
    ORDER BY posts.created_at DESC");

    $sql->bind_param('i', $categoryId);
    $sql->execute();
    $result = $sql->get_result();

    return $result->fetch_all(MYSQLI_ASSOC);
}

// 🔍 NEW: Get category name by ID
function getCategoryName($categoryId) {
    $conn = db();
    
    $sql = $conn->prepare("SELECT category_name FROM categories WHERE id = ? AND is_active = 1");
    $sql->bind_param('i', $categoryId);
    $sql->execute();
    $result = $sql->get_result();
    
    $category = $result->fetch_assoc();
    return $category ? $category['category_name'] : null;
}

// 🔍 NEW: Get posts in "Uncategorized" category
function getUncategorizedPosts() {
    $conn = db();
    
    // First get the "Uncategorized" category ID
    $categorySql = $conn->prepare("SELECT id FROM categories WHERE category_name = 'Uncategorized' AND is_active = 1");
    $categorySql->execute();
    $categoryResult = $categorySql->get_result();
    
    if ($categoryResult->num_rows === 0) {
        return []; // No uncategorized category exists
    }
    
    $uncategorizedCategory = $categoryResult->fetch_assoc();
    $categoryId = $uncategorizedCategory['id'];
    
    // Now get all posts in this category
    $sql = $conn->prepare("
        SELECT 
            p.*,
            u.username as author_name,
            c.category_name
        FROM posts p
        LEFT JOIN users u ON p.post_user = u.id
        LEFT JOIN categories c ON p.post_category = c.id
        WHERE p.post_category = ? AND c.is_active = 1
        ORDER BY p.created_at DESC
    ");
    
    $sql->bind_param('i', $categoryId);
    $sql->execute();
    $result = $sql->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}
