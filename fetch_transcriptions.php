<?php
/**
 * fetch_transcriptions.php
 * Endpoint to load saved transcriptions for the frontend UI
 */
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $search = $_GET['q'] ?? '';
    
    if ($search !== '') {
        $stmt = $pdo->prepare("SELECT * FROM transcriptions WHERE language LIKE ? OR notes LIKE ? OR text_content LIKE ? ORDER BY created_at DESC");
        $term = "%$search%";
        $stmt->execute([$term, $term, $term]);
    } else {
        $stmt = $pdo->query("SELECT * FROM transcriptions ORDER BY created_at DESC");
    }
    
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map database rows to the JSON format the frontend expects
    $result = array_map(function($r) {
        return [
            'id' => $r['txn_id'],
            'timestamp' => $r['created_at'],
            'language' => $r['language'],
            'notes' => $r['notes'],
            'text' => $r['text_content'],
            'chars' => $r['chars'],
            'words' => $r['words']
        ];
    }, $rows);
    
    echo json_encode(['status' => 'success', 'data' => $result]);

} catch(PDOException $e) {
    echo json_encode(['status' => 'error', 'data' => []]);
}
?>
