<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    
    // Hardcoded credentials for college demo
    if ($user === 'admin' && $pass === 'LinguaAI2025') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    }
    
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Ensure table exists dynamically just in case they login first
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$user, $user]);
        $row = $stmt->fetch();
        
        if ($row && password_verify($pass, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid credentials or user not found.';
        }
    } catch (PDOException $e) {
         $error = 'Database connection error. Please try again later.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>LinguaAI – Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    body { background: var(--bg); display: flex; flex-direction: column; min-height: 100vh; }
    .auth-container { max-width: 450px; margin: 120px auto 60px; background: var(--white); padding: 40px; border-radius: var(--r); border: 1px solid var(--border); box-shadow: var(--shadow); }
    .auth-container h2 { font-family: var(--fh); text-align: center; margin-bottom: 24px; color: var(--text); }
    .auth-footer { text-align: center; margin-top: 20px; font-size: 0.9rem; color: var(--muted); }
    .auth-footer a { color: var(--accent); font-weight: 500; }
    .auth-footer a:hover { text-decoration: underline; }
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
      <li><a href="login.php" class="nav-cta">Login</a></li>
    </ul>
  </div>
</nav>

<div class="auth-container">
  <h2>Welcome Back</h2>
  <?php if($error): ?><div style="color:#dc2626;background:#fee2e2;padding:10px;border-radius:6px;margin-bottom:16px;text-align:center;font-size:0.9rem;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  
  <form method="POST" autocomplete="off">
    <div class="fg">
      <label>Username or Email</label>
      <input type="text" name="username" placeholder="e.g., student.name or user@example.com" required autocomplete="off">
    </div>
    <div class="fg">
      <label>Password</label>
      <input type="password" name="password" placeholder="Enter your secret password" required autocomplete="new-password">
    </div>
    <div style="text-align:right; margin-top:-10px; margin-bottom:15px; font-size: 0.85rem;">
        <a href="forgot_password.php" style="color:var(--accent); text-decoration:none;">Forgot Password?</a>
    </div>
    <button type="submit" class="btn-primary" style="width:100%; margin-top:10px;">Login</button>
  </form>
  
  <div class="auth-footer">
    Don't have an account? <a href="register.php">Register here</a>
  </div>
</div>
</body>
</html>
