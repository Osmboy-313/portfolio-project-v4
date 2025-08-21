
<?php
// public/cron.php
$secretKey = 'a_big_super_secret';

if (!isset($_GET['key']) || $_GET['key'] !== $secretKey) {
    http_response_code(403);
    die('Invalid cron key');
}

echo "Cron job started...<br>";

$conn = new mysqli('sql100.infinityfree.com', 'if0_39697025', 'yHsU1pdODr1', 'if0_39697025_project');

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// 1. Delete categories deleted MORE THAN 1 DAY AGO (for testing)
echo "Checking for categories deleted more than 1 day ago...<br>";
$result = $conn->query("SELECT id, category_name, deleted_at FROM categories WHERE is_active = 0 AND deleted_at < NOW() - INTERVAL 1 DAY");
$rows = $result->fetch_all(MYSQLI_ASSOC);

echo "Found " . count($rows) . " old categories:<br>";
foreach ($rows as $row) {
    echo "- ID: {$row['id']}, Name: {$row['category_name']}, Deleted: {$row['deleted_at']}<br>";
}

$deleteResult = $conn->query("DELETE FROM categories WHERE is_active = 0 AND deleted_at < NOW() - INTERVAL 1 DAY");
echo "Deleted {$conn->affected_rows} categories<br>";

// 2. Clean temp files OLDER THAN 1 HOUR (for testing)
$tempDir = __DIR__ . '/assets/uploads/temp/';
echo "Checking temp directory: $tempDir<br>";

if (!is_dir($tempDir)) {
    echo "Temp directory doesn't exist!<br>";
} else {
    $files = glob($tempDir . '*');
    echo "Found " . count($files) . " files in temp directory<br>";
    
    $deleted = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileAge = (time() - filemtime($file)) / 3600; // hours
            if ($fileAge > 0.5) { // DELETE FILES OLDER THAN 1 HOUR (for testing)
                if (unlink($file)) {
                    $deleted++;
                    echo "Deleted: $file (age: {$fileAge}h)<br>";
                } else {
                    echo "Failed to delete: $file<br>";
                }
            } else {
                echo "Skipped (too new): $file (age: {$fileAge}h)<br>";
            }
        }
    }
    echo "Total temp files deleted: $deleted<br>";
}

echo "Cleanup done! 🧹";
?>