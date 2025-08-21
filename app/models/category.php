<?php 

require_once __DIR__ . '/../core/db.php';

// 🔍 NEW: Create global category (check for duplicates)
function createGlobalCategory($name, $userId) {
    $conn = db();
    
    // Check if category name already exists globally
    $checkSql = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
    $checkSql->bind_param('s', $name);
    $checkSql->execute();
    $result = $checkSql->get_result();
    
    if ($result->num_rows > 0) {
        return false; // Category already exists
    }
    
    // Create new global category
    $sql = $conn->prepare("INSERT INTO categories (category_name, created_by_user_id) VALUES (?, ?)");
    $sql->bind_param('si', $name, $userId);
    
    return $sql->execute();
}

// 🔍 NEW: Get total count of active categories
function getTotalActiveCategories() {
    $conn = db();
    
    $sql = $conn->prepare("
        SELECT COUNT(*) as total
        FROM categories 
        WHERE is_active = 1
    ");
    
    $sql->execute();
    $result = $sql->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

// 🔍 NEW: Get all global categories with post counts (with pagination support)
function getAllGlobalCategories($page = 1, $limit = 10) {
    $conn = db();
    
    // Calculate offset
    $offset = ($page - 1) * $limit;
    
    $sql = $conn->prepare("
        SELECT 
            c.id,
            c.category_name,
            c.created_by_user_id,
            c.created_at,
            c.is_active,
            u.username as creator_name,
            COUNT(p.id) as post_count
        FROM categories c
        LEFT JOIN users u ON c.created_by_user_id = u.id
        LEFT JOIN posts p ON c.id = p.post_category
        WHERE c.is_active = 1
        GROUP BY c.id, c.category_name, c.created_by_user_id, c.created_at, c.is_active, u.username
        ORDER BY post_count DESC, c.category_name ASC
        LIMIT ? OFFSET ?
    ");
    
    $sql->bind_param('ii', $limit, $offset);
    $sql->execute();
    $result = $sql->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// 🔍 NEW: Get categories for navigation (with post counts)
function getCategoriesWithPostCounts($limit = 10) {
    $conn = db();
    
    $sql = $conn->prepare("
        SELECT 
            c.id,
            c.category_name,
            COUNT(p.id) as post_count
        FROM categories c
        LEFT JOIN posts p ON c.id = p.post_category
        WHERE c.is_active = 1
        GROUP BY c.id, c.category_name
        HAVING post_count > 0
        ORDER BY post_count DESC, c.category_name ASC
        LIMIT ?
    ");
    
    $sql->bind_param('i', $limit);
    $sql->execute();
    $result = $sql->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// 🔍 NEW: Get single category with creator info
function getSingleCategory($id) {
    $conn = db();
    
    $sql = $conn->prepare("
        SELECT 
            c.*,
            u.username as creator_name
        FROM categories c
        LEFT JOIN users u ON c.created_by_user_id = u.id
        WHERE c.id = ?
    ");
    
    $sql->bind_param('i', $id);
    $sql->execute();
    $result = $sql->get_result();
    
    return $result->fetch_assoc();
}

// 🔍 NEW: Check if user owns category

function doesUserOwnCategory($categoryId, $userId) {
    $conn = db();
    
    // First check if this is a system category (non-deletable)
    if (isSystemCategory($categoryId)) {
        return false; // System categories cannot be deleted by any user
    }
    
    // Then check if user owns the category (and user is not system)
    $sql = $conn->prepare("SELECT id FROM categories WHERE id = ? AND created_by_user_id = ? AND created_by_user_id != 0");
    $sql->bind_param('ii', $categoryId, $userId);
    $sql->execute();
    $result = $sql->get_result();
    
    return $result->num_rows > 0;
}

// 🔍 NEW: Check category name existence globally
function categoryExistenceCheck($name, $idToExclude = 0) {
    $conn = db();
    
    $sql = $conn->prepare("SELECT id FROM categories WHERE category_name = ? AND id != ? AND is_active = 1");
    $sql->bind_param('si', $name, $idToExclude);
    $sql->execute();
    $result = $sql->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// 🔍 NEW: Edit global category (only by owner)
function editGlobalCategory($id, $name, $userId) {
    $conn = db();
    
    // Check if user owns this category
    if (!doesUserOwnCategory($id, $userId)) {
        return false;
    }
    
    $sql = $conn->prepare("UPDATE categories SET category_name = ? WHERE id = ? AND created_by_user_id = ?");
    $sql->bind_param('sii', $name, $id, $userId);
    
    return $sql->execute();
}

// 🔍 NEW: Delete global category (only by owner) - SMART SOFT DELETE
function deleteGlobalCategory($id, $userId) {
    $conn = db();
    $conn->begin_transaction();
    
    try {
        // 1. Verify ownership
        $check = $conn->prepare("SELECT id FROM categories 
                                WHERE id = ? AND created_by_user_id = ?");
        $check->bind_param('ii', $id, $userId);
        $check->execute();
        
        if ($check->get_result()->num_rows === 0) {
            throw new Exception("Ownership verification failed");
        }

        // 2. Get/Create Uncategorized
        $uncategorizedId = getOrCreateUncategorizedCategory();
        if (!$uncategorizedId) {
            throw new Exception("Could not setup Uncategorized category");
        }

        // 3. Move posts
        $conn->query("UPDATE posts 
                     SET post_category = $uncategorizedId 
                     WHERE post_category = $id");

        // 4. Soft delete with unique name
        $newName = 'deleted_' . time() . '_' . bin2hex(random_bytes(4));
        $conn->query("UPDATE categories 
                     SET is_active = 0, 
                         category_name = '$newName',
                         deleted_at = NOW() 
                     WHERE id = $id");

        $conn->commit();
        return ['success' => true];
        
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function getUncategorizedId() {
    $conn = db();
    $result = $conn->query("SELECT id FROM categories 
                           WHERE category_name = 'Uncategorized' 
                           AND is_active = 1 LIMIT 1");
    return $result->num_rows > 0 ? $result->fetch_assoc()['id'] : null;
}

function ensureSystemUserExists() {
    $conn = db();
    $systemUserId = -1;
    
    $check = $conn->query("SELECT id FROM users WHERE id = $systemUserId");
    if ($check->num_rows > 0) {
        return $systemUserId;
    }
    
    $randomPassword = bin2hex(random_bytes(16));
    $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);
    
    $conn->query("INSERT INTO users 
                 (id, username, email, password, user_type) 
                 VALUES 
                 ($systemUserId, 'system', 'system@example.com', '$passwordHash', 'system')");
    
    return $systemUserId;
}


// 🔍 NEW: Get or create "Uncategorized" category (System-owned, non-deletable)

function getOrCreateUncategorizedCategory() {
    $conn = db();
    $systemUserId = ensureSystemUserExists(); // Get the ID
    
    if ($uncategorizedId = getUncategorizedId()) {
        return $uncategorizedId;
    }
    
    $conn->query("INSERT INTO categories 
                 (category_name, created_by_user_id, is_active) 
                 VALUES ('Uncategorized', $systemUserId, 1)"); // ← Use $systemUserId (which is -1)
    return $conn->insert_id;
}

// 🔍 NEW: Check if category is system-owned (non-deletable)

function isSystemCategory($categoryId) {
    $conn = db();
    $systemUserId = ensureSystemUserExists(); // Get the actual ID
    
    $sql = $conn->prepare("SELECT category_name, created_by_user_id FROM categories WHERE id = ?");
    $sql->bind_param('i', $categoryId);
    $sql->execute();
    $result = $sql->get_result();
    
    if ($result->num_rows === 0) {
        return false;
    }
    
    $category = $result->fetch_assoc();
    
    // Use the actual system user ID (-1)
    return ($category['category_name'] === 'Uncategorized' && 
            $category['created_by_user_id'] == $systemUserId);
}

// 🔍 NEW: Get user's owned categories
function getUserOwnedCategories($userId) {
    $conn = db();
    
    $sql = $conn->prepare("
        SELECT 
            c.*,
            COUNT(p.id) as post_count
        FROM categories c
        LEFT JOIN posts p ON c.id = p.post_category
        WHERE c.created_by_user_id = ? AND c.is_active = 1
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    
    $sql->bind_param('i', $userId);
    $sql->execute();
    $result = $sql->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// 🔍 NEW: Get category by name
function getCategoryByName($name) {
    $conn = db();
    
    $sql = $conn->prepare("SELECT * FROM categories WHERE category_name = ? AND is_active = 1");
    $sql->bind_param('s', $name);
    $sql->execute();
    $result = $sql->get_result();
    
    return $result->fetch_assoc();
}

?>