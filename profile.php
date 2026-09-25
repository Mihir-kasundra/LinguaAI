<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
    header('Location: login.php');
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT id, username, email, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: login.php');
        exit;
    }
} catch (PDOException $e) {
    die("Database Connection Error.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <title>My Profile - LinguaAI</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="style.css"/>
    <style>
        .profile-wrapper {
            max-width: 600px; margin: 80px auto;
            background: var(--white); border-radius: var(--r);
            border: 1px solid var(--border); box-shadow: var(--shadow);
            overflow: hidden; animation: slideUp 0.5s ease-out;
        }
        @keyframes slideUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        
        .profile-header {
            background: linear-gradient(135deg, var(--green) 0%, #1b4332 100%);
            padding: 40px 30px; text-align: center; color: white;
        }
        .profile-avatar {
            width: 90px; height: 90px; margin: 0 auto 15px;
            background: rgba(255,255,255,0.2); backdrop-filter: blur(5px);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; font-family: var(--fh); border: 2px solid rgba(255,255,255,0.5);
        }
        .profile-header h1 { margin: 0; font-family: var(--fh); font-size: 1.8rem; }
        .profile-header p { margin: 5px 0 0; font-size: 0.95rem; opacity: 0.8; }
        
        .profile-body { padding: 40px 30px; }
        .info-group { margin-bottom: 25px; }
        .info-group label {
            display: block; font-size: 0.8rem; font-weight: 700; color: var(--muted);
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;
        }
        .info-value {
            font-size: 1.1rem; color: var(--text); font-weight: 500;
            padding: 14px 18px; background: var(--bg); border-radius: 12px;
            border: 1px solid var(--border);
        }
    </style>
</head>
<body>

<nav id="navbar">
  <div class="nav-inner">
    <a href="index.php" class="logo">🌿 LinguaAI</a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="languages.php">Languages</a></li>
      <li><a href="transcribe.php">Transcribe</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="logout.php" class="nav-cta">Logout</a></li>
    </ul>
    <button class="hamburger" id="menuBtn">☰</button>
  </div>
</nav>

<div class="container" style="min-height: calc(100vh - 200px);">
    <div class="profile-wrapper">
        <div class="profile-header">
            <div class="profile-avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <p>Member strictly since <?= date('F j, Y', strtotime($user['created_at'])) ?></p>
        </div>
        <div class="profile-body">
            <div class="info-group">
                <label>User ID</label>
                <div class="info-value" style="color:var(--muted); font-family:monospace;">#<?= $user['id'] ?></div>
            </div>
            <div class="info-group">
                <label>Email Address</label>
                <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
            </div>
            <div class="info-group">
                <label>Account Status</label>
                <div class="info-value"><span class="badge" style="margin:0; background:#dcfce7; color:#166534; border:1px solid #bbf7d0;">Active Participant</span></div>
            </div>
            <div style="margin-top: 40px; text-align:center;">
                <a href="transcribe.php" class="btn-primary" style="display:inline-block; margin-right:10px;">New Transcription</a>
                <a href="logout.php" class="btn-outline" style="display:inline-block;">Secure Logout</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="shared.js"></script>
</body>
</html>
