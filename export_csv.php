<?php
/**
 * export_csv.php
 * LinguaAI
 */
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

if (!isset($_GET['type']) || !in_array($_GET['type'], ['txns', 'msgs'])) {
    die("Invalid export type.");
}
$type = $_GET['type'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($type === 'txns') {
        $filename = "LinguaAI_Transcriptions_" . date('Ymd_Hi') . ".csv";
        $stmt = $pdo->query("SELECT id, txn_id, language, notes, text_content as text, chars, words, created_at FROM transcriptions ORDER BY created_at DESC");
    } else {
        $filename = "LinguaAI_Messages_" . date('Ymd_Hi') . ".csv";
        $stmt = $pdo->query("SELECT id, name, email, organization, subject, message, created_at FROM messages ORDER BY created_at DESC");
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Database Error");
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Add UTF-8 BOM for proper Excel rendering
fputs($output, $bom =(chr(0xEF) . chr(0xBB) . chr(0xBF)));

if (count($rows) > 0) {
    // Add headers
    fputcsv($output, array_keys($rows[0]));
    // Add data
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
} else {
    fputcsv($output, ['No data available']);
}
fclose($output);
exit;
?>
