<?php
require __DIR__.'/../core/bootstrap.php';

$log = [];
$cleanupTime = date('[Y-m-d H:i:s]');

// 1. CATEGORY CLEANUP (30+ days old deleted categories)
try {
    $conn = db();
    
    // Count before deletion for logging
    $countResult = $conn->query("
        SELECT COUNT(*) as count 
        FROM categories 
        WHERE is_active = 0 
        AND deleted_at < NOW() - INTERVAL 30 DAY
    ");
    $count = $countResult->fetch_assoc()['count'];
    
    // Actual deletion
    $conn->query("
        DELETE FROM categories 
        WHERE is_active = 0 
        AND deleted_at < NOW() - INTERVAL 30 DAY
    ");
    
    $deletedCount = $conn->affected_rows;
    $log[] = "Categories: Purged $deletedCount/$count old entries";
    
} catch (Exception $e) {
    $log[] = "Categories: FAILED - " . $e->getMessage();
}

// 2. TEMP FILE CLEANUP (24+ hours old)
$tempDir = __DIR__ . '/../../public/assets/uploads/temp';
$tempFilesDeleted = 0;
$tempErrors = 0;

if (is_dir($tempDir)) {
    $files = glob($tempDir . '*'); // Simple glob instead of RecursiveIterator
    
    foreach ($files as $file) {
        if (is_file($file)) {
            $fileAge = (time() - filemtime($file)) / 3600; // hours
            
            if ($fileAge > 24) {
                try {
                    unlink($file);
                    $tempFilesDeleted++;
                } catch (Exception $e) {
                    $tempErrors++;
                }
            }
        }
    }
    $log[] = "Temp Files: Deleted $tempFilesDeleted, Errors: $tempErrors";
} else {
    $log[] = "Temp Files: Directory not found";
}

// 3. LOG RESULTS
$logMessage = $cleanupTime . " " . implode(" | ", $log) . PHP_EOL;
file_put_contents(__DIR__ . '/../../storage/logs/cron.log', $logMessage, FILE_APPEND);

echo "Cleanup completed: " . implode(", ", $log);

