<?php

require_once __DIR__ . '/../models/category.php';
require_once __DIR__ . '/../models/post.php';
require_once __DIR__ .  '/../core/view.php';
require_once __DIR__ .  '/../core/auth.php';

function category_index()
{
    auth_require_login();

    $modals = view('/components/modals');

    echo view('/categories/index', ['title' => 'Categories', 'modals' => $modals], 'private');
}

function category_add()
{
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $response = [];

        $name = trim($data['name']);
        $userId = $_SESSION['user']['id'];

        $nameAlreadyExists = categoryExistenceCheck($name) ? true : false;

        if (empty($name)) $response['errors']['name'] = "Enter category name";
        if (!empty($name) && $nameAlreadyExists) $response['errors']['name'] = "Category name already exists globally";

        if (!isset($response['errors'])) {
            $result = createGlobalCategory($name, $userId);

            if ($result) {
                $response['success'] = "Category has been created globally!";
            } else {
                $response['failure'] = "Failed to create category";
            }
        }

        echo json_encode($response);
    }

    exit;
}

function category_fetchAll()
{
    header('Content-Type: application/json');
    
    // Accept both GET and POST requests
    if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        // Ensure valid values
        $page = max(1, $page);
        $limit = max(1, min(100, $limit));
        
        $categories = getAllGlobalCategories($page, $limit);
        $totalCategories = getTotalActiveCategories(); // Get total count using new function
        
        $totalPages = ceil($totalCategories / $limit);
        
        echo json_encode([
            'categories' => $categories,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalCategories' => $totalCategories,
            'itemsPerPage' => $limit
        ]);
    }
}

function category_populate()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = json_decode(file_get_contents('php://input'), true);

        $category = getSingleCategory($id);

        echo json_encode($category);
    }
}

function category_existenceCheck()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        $id = $data['id'] ?? 0;
        $name = trim($data['name']);
        if (empty($name)) return;

        $check = categoryExistenceCheck($name, $id);
        $exists = false;

        if (!empty($check)) {
            $exists = true;
        }

        echo json_encode(['exists' => $exists, 'id' => $id, 'name' => $name]);
    }
}

function category_edit()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $response = [];
        
        $id = trim($data['id']);
        $name = trim($data['name']);
        $userId = $_SESSION['user']['id'];

        // Check if user owns this category
        if (!doesUserOwnCategory($id, $userId)) {
            $response['failure'] = "You don't have permission to edit this category";
            echo json_encode($response);
            return;
        }

        $nameExistenceCheck = categoryExistenceCheck($name, $id);

        if (empty($name)) $response['errors']['name'] = "Enter Category Name";
        if (!empty($name) && !empty($nameExistenceCheck)) $response['errors']['name'] = "Category name already exists globally";

        if (!isset($response['errors'])) {
            if (editGlobalCategory($id, $name, $userId)) {
                $response['success'] = "Successfully edited category";
            } else {
                $response['failure'] = "Failed to edit category";
            }
        }
        
        echo json_encode($response);
    }
}

function category_delete() {
    header('Content-Type: application/json');
    $response = ['success' => false];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $userId = $_SESSION['user']['id'];
        $categoryId = (int)($data['id'] ?? 0);

        // Ownership check
        if (!doesUserOwnCategory($categoryId, $userId)) {
            $response['error'] = "You don't own this category";
            echo json_encode($response);
            return;
        }

        // Prevent deleting Uncategorized
        if ($categoryId == getUncategorizedId()) {
            $response['error'] = "Cannot delete system category";
            echo json_encode($response);
            return;
        }

        $result = deleteGlobalCategory($categoryId, $userId);
        
        if ($result['success']) {
            $response = [
                'success' => true,
                'message' => "Category archived. Posts moved to 'Uncategorized'"
            ];
        } else {
            $response['error'] = $result['error'] ?? "Deletion failed";
        }
    }
    
    echo json_encode($response);
}

?>
