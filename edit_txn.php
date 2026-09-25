<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: admin.php');
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $language = $_POST['language'] ?? '';
        $notes = $_POST['notes'] ?? '';
        $text_content = $_POST['text_content'] ?? '';
        $chars = mb_strlen($text_content);
        $words = str_word_count($text_content);
        
        $stmt = $pdo->prepare("UPDATE transcriptions SET language = ?, notes = ?, text_content = ?, chars = ?, words = ? WHERE id = ?");
        $stmt->execute([$language, $notes, $text_content, $chars, $words, $id]);
        
        header('Location: admin.php?msg=Transcription+Updated#transcriptions');
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM transcriptions WHERE id = ?");
    $stmt->execute([$id]);
    $txn = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$txn) {
        header('Location: admin.php');
        exit;
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>Edit Transcription - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css"/>
    <style>
        body { 
            background: linear-gradient(135deg, var(--bg) 0%, var(--bg-alt) 100%); 
            display: flex; flex-direction: column; min-height: 100vh; margin:0; font-family: var(--fb);
        }
        .nav-bar { 
            background: rgba(45, 106, 79, 0.98); backdrop-filter: blur(10px); padding: 16px 40px; 
            display: flex; align-items: center; justify-content: space-between; 
            border-bottom: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            position: sticky; top: 0; z-index: 100;
        }
        .nav-bar .brand { font-family: var(--fh); color: var(--accent); font-size: 1.4rem; font-weight: 700; letter-spacing: 0.5px; }
        .nav-bar a.back-link { 
            color: var(--white); text-decoration: none; font-size: 0.95rem; font-weight: 500; 
            background: rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 20px; transition: all 0.3s ease;
        }
        .nav-bar a.back-link:hover { background: rgba(255,255,255,0.25); transform: translateX(-4px); }
        
        .edit-container { 
            max-width: 600px; width: 90%; margin: 60px auto; 
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px);
            padding: 50px; border-radius: 24px; border: 1px solid rgba(255, 255, 255, 1); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08), 0 1px 3px rgba(0,0,0,0.05); 
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0; transform: translateY(30px);
        }
        @keyframes slideUp { to { opacity: 1; transform: translateY(0); } }
        
        .edit-container h2 { 
            font-family: var(--fh); text-align: center; margin-top: 0; margin-bottom: 40px; 
            color: var(--green); font-size: 2.2rem; position: relative;
        }
        .edit-container h2::after {
            content: ''; position: absolute; bottom: -12px; left: 50%; transform: translateX(-50%);
            width: 50px; height: 4px; background: var(--accent); border-radius: 2px;
        }
        
        .fg { margin-bottom: 24px; }
        .fg label { 
            display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.85rem; 
            color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px;
        }
        .fg input, .fg textarea { 
            width: 100%; padding: 14px 18px; border: 2px solid #e2e8f0; border-radius: 12px; 
            font-family: var(--fb); font-size: 1rem; background: #fff; transition: all 0.3s ease; color: var(--text); box-sizing: border-box;
        }
        .fg input:focus, .fg textarea:focus {
            outline: none; border-color: var(--accent); box-shadow: 0 0 0 4px rgba(196, 123, 43, 0.12); transform: translateY(-2px);
        }
        
        .btn-submit {
            width: 100%; padding: 16px; background: linear-gradient(135deg, var(--green) 0%, #1b4332 100%);
            color: white; border: none; border-radius: 12px; font-size: 1.05rem; font-weight: 600;
            cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); font-family: var(--fb);
            box-shadow: 0 8px 20px rgba(45, 106, 79, 0.25); margin-top: 15px; position: relative; overflow: hidden;
        }
        .btn-submit::after {
            content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent); transition: 0.5s;
        }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(45, 106, 79, 0.35); }
        .btn-submit:hover::after { left: 100%; }
    </style>
</head>
<body>
<div class="nav-bar">
    <div class="brand">🌿 LinguaAI Admin</div>
    <a href="admin.php#transcriptions" class="back-link">&larr; Back to Dashboard</a>
</div>

<div class="edit-container">
    <h2>Edit Transcription</h2>
    <form method="POST">
        <div class="fg">
            <label>Language</label>
            <input type="text" name="language" value="<?= htmlspecialchars($txn['language']) ?>" required>
        </div>
        <div class="fg">
            <label>Notes</label>
            <input type="text" name="notes" value="<?= htmlspecialchars($txn['notes']) ?>">
        </div>
        <div class="fg">
            <label>Text Content</label>
            <textarea name="text_content" rows="6" required><?= htmlspecialchars($txn['text_content']) ?></textarea>
        </div>
        <div style="margin-top: 35px;">
            <button type="submit" class="btn-submit">Save Changes</button>
        </div>
    </form>
</div>
</body>
</html>
