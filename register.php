<?php
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Valid email required.';
    } else {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Ensure table exists
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                email VARCHAR(100) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
            
            // Check if user exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = 'Username or email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hash]);
                $success = 'Registration successful! You can now <a href="login.php" style="text-decoration:underline;color:inherit;">login</a>.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>LinguaAI – Register</title>
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
  <h2>Create an Account</h2>
  <?php if($error): ?><div style="color:#dc2626;background:#fee2e2;padding:10px;border-radius:6px;margin-bottom:16px;text-align:center;font-size:0.9rem;"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if($success): ?><div style="color:var(--green);background:var(--green-l);padding:10px;border-radius:6px;margin-bottom:16px;text-align:center;font-size:0.9rem;"><?= $success ?></div><?php endif; ?>
  
  <form method="POST">
    <div class="fg">
      <label>Username</label>
      <input type="text" name="username" required>
    </div>
    <div class="fg">
      <label>Email Address</label>
      <input type="email" name="email" required>
    </div>
    <div class="fg">
      <label>Password</label>
      <input type="password" name="password" required>
    </div>
    <button type="submit" class="btn-primary" style="width:100%; margin-top:10px;">Register</button>
  </form>
  
  <div class="auth-footer">
    Already have an account? <a href="login.php">Login here</a>
  </div>
</div>
</body>
</html>
