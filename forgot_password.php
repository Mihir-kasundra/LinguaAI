<?php
session_start();
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if ($email) {
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user) {
                $new_pass = 'pass' . rand(1000, 9999);
                $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                $uStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $uStmt->execute([$hash, $user['id']]);
                
                $to = $email;
                $subject = "LinguaAI Password Reset";
                $headers = "From: noreply@linguaai.local\r\nReply-To: noreply@linguaai.local";
                $body = "Hello " . $user['username'] . ",\n\nYour new temporary password is: $new_pass\n\nPlease login and securely update your password if possible.\n\nThank you,\nLinguaAI";
                @mail($to, $subject, $body, $headers);
                
                $msg = "A password reset link with a temporary password has been sent to your email. (Note for local test: temporary password is $new_pass)";
            } else {
                $err = "No account found with that email address.";
            }
        } catch (PDOException $e) {
            $err = "Database error. Please try again later.";
        }
    } else {
        $err = "Please enter a valid email.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>LinguaAI – Forgot Password</title>
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
  <h2>Reset Password</h2>
  <?php if($err): ?><div style="color:#dc2626;background:#fee2e2;padding:10px;border-radius:6px;margin-bottom:16px;text-align:center;font-size:0.9rem;"><?= htmlspecialchars($err) ?></div><?php endif; ?>
  <?php if($msg): ?><div style="color:#16a34a;background:#dcfce7;padding:10px;border-radius:6px;margin-bottom:16px;text-align:center;font-size:0.9rem;"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
  
  <form method="POST" autocomplete="off">
    <div class="fg">
      <label>Email Address</label>
      <input type="email" name="email" placeholder="Enter your registered email" required autocomplete="email">
    </div>
    <button type="submit" class="btn-primary" style="width:100%; margin-top:10px;">Reset Password</button>
  </form>
  
  <div class="auth-footer">
    Remembered your password? <a href="login.php">Login here</a>
  </div>
</div>
</body>
</html>
