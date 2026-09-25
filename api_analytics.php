<?php
/**
 * api_analytics.php
 * Endpoint to load live counts of database languages for Chart.js
 */
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Group By query to get totals per language
    $stmt = $pdo->query("SELECT language, COUNT(*) as count FROM transcriptions GROUP BY language ORDER BY count DESC LIMIT 8");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $data]);
} catch(PDOException $e) {
    // Failsafe empty data on MySQL error
    echo json_encode(['status' => 'error', 'data' => []]);
}
?>
