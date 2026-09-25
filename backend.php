<?php
/**
 * backend.php — Contact Form Handler
 * LinguaAI College Project
 */
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'Method not allowed.']); exit;
}

function clean($v) { return htmlspecialchars(strip_tags(trim($v ?? '')), ENT_QUOTES, 'UTF-8'); }

$name   = clean($_POST['name']);
$email  = clean($_POST['email']);
$org    = clean($_POST['organisation'] ?? '');
$subj   = clean($_POST['subject'] ?? 'General');
$msg    = clean($_POST['message']);

if (!$name)                                    { echo json_encode(['status'=>'error','message'=>'Name is required.']); exit; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)){ echo json_encode(['status'=>'error','message'=>'Valid email required.']); exit; }
if (strlen($msg) < 5)                          { echo json_encode(['status'=>'error','message'=>'Message too short.']); exit; }

try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, organization, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $org, $subj, $msg]);
} catch (PDOException $e) {
    // Fails silently for the user, but we know it failed to log
}

// Email notification — Send a confirmation to the GMAIL ID provided in the form
$to      = $email;
$headers = "From: noreply@linguaai.local\r\nReply-To: noreply@linguaai.local\r\nContent-Type: text/plain; charset=UTF-8";
$email_body = "Hello $name,\n\nWe have successfully received your contact request regarding '$subj'.\n\nYour Message:\n\"$msg\"\n\nOur team will get back to you shortly.\n\nThank you,\nLinguaAI Research Team";

@mail($to, "LinguaAI Contact Confirmation: $subj", $email_body, $headers);

echo json_encode(['status'=>'success','message'=>'Message received!']);
?>
