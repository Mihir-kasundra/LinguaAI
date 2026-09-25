<?php
/**
 * admin.php 
 * LinguaAI Proper Admin Dashboard
 */
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ensure users table exists just in case
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS recognition_languages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(20) NOT NULL UNIQUE,
        name VARCHAR(100) NOT NULL,
        is_active TINYINT(1) DEFAULT 1
    )");
    
    // Seed it if empty
    $rec_langs_count = $pdo->query("SELECT COUNT(*) FROM recognition_languages")->fetchColumn();
    if ($rec_langs_count == 0) {
        $pdo->exec("INSERT INTO recognition_languages (code, name, is_active) VALUES 
            ('en-US', 'English', 1),
            ('es-ES', 'Spanish', 1),
            ('pt-BR', 'Portuguese', 1),
            ('hi-IN', 'Hindi', 1),
            ('gu-IN', 'Gujarati', 1),
            ('ja-JP', 'Japanese', 1),
            ('zh-CN', 'Mandarin', 1),
            ('fr-FR', 'French', 1),
            ('de-DE', 'German', 1),
            ('ar-SA', 'Arabic', 1),
            ('ru-RU', 'Russian', 1)
        ");
    }

    // Handle form submissions for recognition languages
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_rec_lang') {
        $code = trim($_POST['code']);
        $name = trim($_POST['name']);
        if ($code && $name) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO recognition_languages (code, name, is_active) VALUES (?, ?, 1)");
            $stmt->execute([$code, $name]);
        }
        header('Location: admin.php?msg=Added+Recognition+Language#rec-languages');
        exit;
    }
    // Handle Deletions
    if (isset($_GET['delete_txn'])) {
        $stmt = $pdo->prepare("DELETE FROM transcriptions WHERE id = ?");
        $stmt->execute([(int)$_GET['delete_txn']]);
        header('Location: admin.php?msg=Deleted+Transcription#transcriptions');
        exit;
    }
    if (isset($_GET['delete_msg'])) {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([(int)$_GET['delete_msg']]);
        header('Location: admin.php?msg=Deleted+Message#messages');
        exit;
    }
    if (isset($_GET['delete_user'])) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([(int)$_GET['delete_user']]);
        header('Location: admin.php?msg=Deleted+User#users');
        exit;
    }
    if (isset($_GET['delete_lang'])) {
        $stmt = $pdo->prepare("DELETE FROM languages WHERE id = ?");
        $stmt->execute([(int)$_GET['delete_lang']]);
        header('Location: admin.php?msg=Deleted+Language+Entry#languages');
        exit;
    }
    if (isset($_GET['delete_rec_lang'])) {
        $stmt = $pdo->prepare("DELETE FROM recognition_languages WHERE id = ?");
        $stmt->execute([(int)$_GET['delete_rec_lang']]);
        header('Location: admin.php?msg=Deleted+Recognition+Language#rec-languages');
        exit;
    }
    if (isset($_GET['toggle_rec_lang'])) {
        $stmt = $pdo->prepare("UPDATE recognition_languages SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([(int)$_GET['toggle_rec_lang']]);
        header('Location: admin.php?msg=Updated+Recognition+Language+Status#rec-languages');
        exit;
    }

    // Fetch Data
    $txns = $pdo->query("SELECT * FROM transcriptions ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $msgs = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    $langs = $pdo->query("SELECT * FROM languages ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $rec_langs = $pdo->query("SELECT * FROM recognition_languages ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

    $total_txns = count($txns);
    $total_msgs = count($msgs);
    $total_users = count($users);
    $total_langs = count($langs);

} catch(PDOException $e) {
    die("Database Error: " . $e->getMessage() . " <br> Ensure setup_db.php was completed.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Admin Dashboard - LinguaAI</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    /* Override / extend for Admin */
    body { display: flex; min-height: 100vh; background: var(--bg); margin: 0; flex-direction: row; }
    
    /* Sidebar */
    .sidebar { width: 260px; background: var(--green); color: var(--white); padding: 20px 0; display: flex; flex-direction: column; position: fixed; height: 100vh; }
    .sidebar h2 { padding: 0 20px; font-family: var(--fh); margin-bottom: 30px; font-size: 1.5rem; color: var(--accent); }
    .sidebar a { color: var(--bg-alt); text-decoration: none; padding: 15px 24px; display: block; border-left: 3px solid transparent; transition: all 0.2s; font-size: 0.95rem; font-weight: 500; font-family: var(--fb); }
    .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.08); color: var(--white); border-left-color: var(--accent); }
    .sidebar .bottom-links { margin-top: auto; }
    
    /* Main Content */
    .main-content { flex: 1; margin-left: 260px; padding: 40px; overflow-y: auto; background: var(--bg); }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header h1 { font-size: 2rem; font-family: var(--fh); color: var(--green); margin: 0; }
    
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; margin-bottom: 40px; }
    .stat-card { background: var(--white); padding: 24px; border-radius: var(--r); box-shadow: var(--shadow); border: 1px solid var(--border); display: flex; flex-direction: column; }
    .stat-card h3 { margin: 0 0 10px; font-size: 0.85rem; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-card p { margin: 0; font-size: 2.5rem; font-weight: 700; color: var(--accent); line-height: 1; }
    
    /* Tab Sections */
    .section-content { display: none; background: var(--white); padding: 30px; border-radius: var(--r); box-shadow: var(--shadow); border: 1px solid var(--border); animation: fadeIn 0.3s; }
    .section-content.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 2px solid var(--border); padding-bottom: 16px; }
    .section-header h2 { margin: 0; font-size: 1.4rem; color: var(--green); font-family: var(--fh); }
    
    /* Tables */
    table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
    th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid var(--border); }
    th { background: var(--bg-alt); font-weight: 600; color: var(--muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; border-radius: 4px 4px 0 0; }
    td { color: var(--text); vertical-align: middle; }
    tr:last-child td { border-bottom: none; }
    tr:hover td { background: var(--bg); }
    
    .action-btn { background: var(--bg-alt); color: var(--muted); padding: 6px 12px; border-radius: 4px; font-size: 0.75rem; text-decoration: none; font-weight: 500; margin-right: 6px; display: inline-block; transition: all 0.2s; border: none; cursor: pointer; }
    .btn-edit { background: var(--green-l); color: var(--green); }
    .btn-edit:hover { opacity: 0.8; }
    .btn-danger { background: #fee2e2; color: #dc2626; }
    .btn-danger:hover { opacity: 0.8; }
  </style>
</head>
<body>

<div class="sidebar">
  <h2>🌿 Admin Panel</h2>
  <a href="#dashboard" onclick="switchTab('dashboard')" class="tab-link active" id="link-dashboard">Dashboard Overview</a>
  <a href="#profile" onclick="switchTab('profile')" class="tab-link" id="link-profile">Admin Profile</a>
  <a href="#users" onclick="switchTab('users')" class="tab-link" id="link-users">Manage Users</a>
  <a href="#languages" onclick="switchTab('languages')" class="tab-link" id="link-languages">Language Database</a>
  <a href="#rec-languages" onclick="switchTab('rec-languages')" class="tab-link" id="link-rec-languages">Recognition Languages</a>
  <a href="#transcriptions" onclick="switchTab('transcriptions')" class="tab-link" id="link-transcriptions">Transcriptions</a>
  <a href="#messages" onclick="switchTab('messages')" class="tab-link" id="link-messages">Contact Messages</a>
  
  <div class="bottom-links">
    <a href="index.php" target="_blank" style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 20px;">Open Website ↗</a>
    <a href="logout.php" style="color: #fca5a5;">Logout Securely</a>
  </div>
</div>

<div class="main-content">
  <div class="header">
    <h1 id="page-title">Dashboard Overview</h1>
  </div>
  
  <?php if(isset($_GET['msg'])): ?>
    <div id="flashMsg" style="background:var(--green-l);color:var(--green);padding:14px 20px;border-radius:8px;margin-bottom:24px;border:1px solid rgba(45,106,79,.2); font-weight: 500; transition: opacity 0.5s ease;">
        ✅ <?= htmlspecialchars($_GET['msg']) ?>
    </div>
    <script>
        setTimeout(() => {
            const el = document.getElementById('flashMsg');
            if(el) {
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
            const url = new URL(window.location);
            url.searchParams.delete('msg');
            window.history.replaceState({}, document.title, url);
        }, 3500);
    </script>
  <?php endif; ?>

  <!-- Dashboard Overview Tab -->
  <div id="tab-dashboard" class="section-content active" style="background:transparent;box-shadow:none;border:none;padding:0;">
    <div class="stats-grid">
      <div class="stat-card"><h3>Total Users</h3><p><?= $total_users ?></p></div>
      <div class="stat-card"><h3>Transcriptions Generated</h3><p><?= $total_txns ?></p></div>
      <div class="stat-card"><h3>Contact Queries</h3><p><?= $total_msgs ?></p></div>
    </div>
    
    <div class="section-content" style="display:block;">
        <div class="section-header"><h2>Welcome to LinguaAI Admin</h2></div>
        <p style="color:var(--muted); font-size: 0.95rem; line-height: 1.6;">
            You have full administrative privileges. Use the left navigation to manage the core website data including secure user accounts, dynamic audio transcriptions, and inbound contact messages. 
            All data edits interact directly with the active production MySQL database.
        </p>
    </div>
  </div>

  <!-- Users Tab -->
  <div id="tab-users" class="section-content">
    <div class="section-header">
      <h2>Registered Accounts</h2>
      <a href="add_user.php" class="btn-primary" style="font-size:0.85rem;padding:8px 16px;">+ Add New User</a>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Joined</th>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email Address</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($users)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--muted);">No users found.</td></tr>
                <?php endif; ?>
                <?php foreach($users as $u): ?>
                <tr>
                    <td style="font-size:0.8rem;color:var(--muted);"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                    <td><span class="tag">#<?= $u['id'] ?></span></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $u['id'] ?>" class="action-btn btn-edit">Edit</a>
                        <a href="admin.php?delete_user=<?= $u['id'] ?>" class="action-btn btn-danger" onclick="return confirm('Permanently delete this user account?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  </div>

  <!-- Admin Profile Tab -->
  <div id="tab-profile" class="section-content">
    <div class="section-header">
      <h2>Admin Profile</h2>
    </div>
    <div style="background:#f8fafc; padding:30px; border-radius:var(--r); border:1px solid var(--border); max-width:500px;">
        <h3 style="margin-top:0; color:var(--green); font-family:var(--fh);">Administrator Account</h3>
        <p><strong>Username:</strong> admin</p>
        <p><strong>Email:</strong> admin@linguaai.local</p>
        <p><strong>Role:</strong> Super Administrator</p>
        <p><strong>Status:</strong> <span class="tag">Active</span></p>
        <div style="margin-top:20px; padding-top:20px; border-top:1px solid var(--border);">
            <p style="color:var(--muted); font-size:0.85rem;">Admin credentials are intentionally locked for this environment to ensure consistent access during project evaluation.</p>
        </div>
    </div>
  </div>

  <!-- Languages Tab -->
  <div id="tab-languages" class="section-content">
    <div class="section-header">
      <h2>Languages Database</h2>
      <a href="add_lang.php" class="btn-primary" style="font-size:0.85rem;padding:8px 16px;">+ Add New Language</a>
    </div>
    
    <div style="display:flex; gap:12px; margin-bottom:20px; flex-wrap:wrap;">
        <input type="text" id="adminLangSearch" placeholder="🔍  Search language, region, or country…" style="flex:1; min-width:250px; padding:12px 16px; border:2px solid var(--border); border-radius:12px; font-family:var(--fb); outline:none; transition:border-color 0.3s; color:var(--text);">
        <select id="adminLangStatus" style="padding:12px 16px; border:2px solid var(--border); border-radius:12px; font-family:var(--fb); outline:none; color:var(--text); cursor:pointer;">
            <option value="">All Statuses</option>
            <option value="Critical">Critical</option>
            <option value="Extinct">Extinct</option>
            <option value="Endangered">Endangered</option>
            <option value="Vulnerable">Vulnerable</option>
            <option value="Revitalized">Revitalized</option>
        </select>
    </div>

    <div style="overflow-x:auto;">
        <table id="adminLangTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Region (Country)</th>
                    <th>Status</th>
                    <th>Speakers</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($langs)): ?>
                    <tr><td colspan="6" style="text-align:center;color:var(--muted);">No languages found.</td></tr>
                <?php endif; ?>
                <?php foreach($langs as $lng): ?>
                <tr class="lang-row" data-name="<?= strtolower(htmlspecialchars($lng['name'] . ' ' . $lng['region'] . ' ' . $lng['country'])) ?>" data-status="<?= strtolower(htmlspecialchars($lng['status'])) ?>">
                    <td><span class="tag">#<?= $lng['id'] ?></span></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($lng['name']) ?></td>
                    <td><?= htmlspecialchars($lng['region']) ?> (<?= htmlspecialchars($lng['country']) ?>)</td>
                    <td><?= htmlspecialchars($lng['status']) ?></td>
                    <td><?= htmlspecialchars($lng['speakers']) ?></td>
                    <td>
                        <a href="edit_lang.php?id=<?= $lng['id'] ?>" class="action-btn btn-edit">Edit</a>
                        <a href="admin.php?delete_lang=<?= $lng['id'] ?>" class="action-btn btn-danger" onclick="return confirm('Permanently delete this language? This changes the live atlas.');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  </div>

  <!-- Recognition Languages Tab -->
  <div id="tab-rec-languages" class="section-content">
    <div class="section-header">
      <h2>Recognition Languages</h2>
      <form method="POST" action="admin.php" style="display:flex;gap:10px;align-items:center;">
        <input type="hidden" name="action" value="add_rec_lang">
        <input type="text" name="name" placeholder="Language Name (e.g. English)" required style="padding:6px;border:1px solid var(--border);border-radius:4px;">
        <input type="text" name="code" placeholder="Code (e.g. en-US)" required pattern="^[a-z]{2,3}-[A-Z]{2}$" title="Must be a valid BCP 47 code (e.g., en-US, ja-JP)" style="padding:6px;border:1px solid var(--border);border-radius:4px;width:120px;">
        <button type="submit" class="btn-primary" style="font-size:0.85rem;padding:6px 12px;">+ Add Language</button>
      </form>
    </div>
    
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Language Name</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($rec_langs)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--muted);">No recognition languages found.</td></tr>
                <?php endif; ?>
                <?php foreach($rec_langs as $rl): ?>
                <tr>
                    <td><span class="tag">#<?= $rl['id'] ?></span></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($rl['code']) ?></td>
                    <td><?= htmlspecialchars($rl['name']) ?></td>
                    <td>
                        <?php if($rl['is_active']): ?>
                            <span class="tag" style="background:var(--green-l);color:var(--green);">Active</span>
                        <?php else: ?>
                            <span class="tag" style="background:#fee2e2;color:#dc2626;">Hidden</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="admin.php?toggle_rec_lang=<?= $rl['id'] ?>" class="action-btn <?= $rl['is_active'] ? 'btn-danger' : 'btn-edit' ?>" style="margin-right:8px;">
                            <?= $rl['is_active'] ? 'Hide' : 'Activate' ?>
                        </a>
                        <a href="admin.php?delete_rec_lang=<?= $rl['id'] ?>" class="action-btn btn-danger" onclick="return confirm('Permanently delete this recognition language?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  </div>

  <!-- Transcriptions Tab -->
  <div id="tab-transcriptions" class="section-content">
    <div class="section-header">
      <h2>Transcriptions Archive</h2>
      <a href="export_csv.php?type=txns" class="btn-primary" style="font-size:0.85rem;padding:8px 16px;">⬇ Export CSV</a>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Language</th>
                    <th>Text Extract</th>
                    <th>Words</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($txns)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--muted);">No transcriptions generated yet.</td></tr>
                <?php endif; ?>
                <?php foreach($txns as $t): ?>
                <tr>
                    <td style="white-space:nowrap;font-size:0.8rem;color:var(--muted);"><?= date('M d, Y', strtotime($t['created_at'])) ?></td>
                    <td><span class="tag vl"><?= htmlspecialchars($t['language']) ?></span></td>
                    <td style="max-width:300px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="<?= htmlspecialchars($t['text_content']) ?>">
                        <?= htmlspecialchars($t['text_content']) ?>
                    </td>
                    <td><?= $t['words'] ?></td>
                    <td>
                        <a href="edit_txn.php?id=<?= $t['id'] ?>" class="action-btn btn-edit">Edit</a>
                        <a href="admin.php?delete_txn=<?= $t['id'] ?>" class="action-btn btn-danger" onclick="return confirm('Delete this transcription?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  </div>

  <!-- Messages Tab -->
  <div id="tab-messages" class="section-content">
    <div class="section-header">
      <h2>Inbound Queries</h2>
      <a href="export_csv.php?type=msgs" class="btn-primary" style="font-size:0.85rem;padding:8px 16px;">⬇ Export CSV</a>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Sender</th>
                    <th>Subject</th>
                    <th>Message Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($msgs)): ?>
                    <tr><td colspan="5" style="text-align:center;color:var(--muted);">Inbox is empty.</td></tr>
                <?php endif; ?>
                <?php foreach($msgs as $m): ?>
                <tr>
                    <td style="white-space:nowrap;font-size:0.8rem;color:var(--muted);"><?= date('M d - H:i', strtotime($m['created_at'])) ?></td>
                    <td>
                        <div style="font-weight:600;"><?= htmlspecialchars($m['name']) ?></div>
                        <a href="mailto:<?= htmlspecialchars($m['email']) ?>" style="font-size:0.8rem;color:var(--accent);text-decoration:none;"><?= htmlspecialchars($m['email']) ?></a>
                    </td>
                    <td><span class="tag"><?= htmlspecialchars($m['subject']) ?></span></td>
                    <td style="max-width:300px;font-size:0.85rem;line-height:1.4;"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
                    <td>
                        <a href="edit_msg.php?id=<?= $m['id'] ?>" class="action-btn btn-edit">Edit</a>
                        <a href="admin.php?delete_msg=<?= $m['id'] ?>" class="action-btn btn-danger" onclick="return confirm('Delete this message?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  </div>

</div>

<script>
    const titles = {
        'dashboard': 'Dashboard Overview',
        'profile': 'Admin Profile',
        'users': 'Manage Users',
        'languages': 'Languages Database',
        'rec-languages': 'Recognition Languages',
        'transcriptions': 'Transcriptions Archive',
        'messages': 'Contact Messages'
    };

    function switchTab(tabId) {
        window.location.hash = tabId;
        document.querySelectorAll('.section-content').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
        
        const targetTab = document.getElementById('tab-' + tabId);
        if (targetTab) { targetTab.classList.add('active'); }
        else { document.getElementById('tab-dashboard').classList.add('active'); }
        
        const targetLink = document.getElementById('link-' + tabId);
        if (targetLink) targetLink.classList.add('active');
        
        document.getElementById('page-title').innerText = titles[tabId] || 'Dashboard Overview';
    }

    window.addEventListener('DOMContentLoaded', () => {
        let hash = window.location.hash.substring(1);
        if(!['dashboard','profile','users','languages','rec-languages','transcriptions','messages'].includes(hash)) {
            hash = 'dashboard';
        }
        switchTab(hash);

        // Language Filtering Logic
        const langSearch = document.getElementById('adminLangSearch');
        const langStatus = document.getElementById('adminLangStatus');
        if (langSearch && langStatus) {
            function filterLangs() {
                const q = langSearch.value.toLowerCase();
                const s = langStatus.value.toLowerCase();
                document.querySelectorAll('.lang-row').forEach(row => {
                    const matchQ = row.getAttribute('data-name').includes(q);
                    const matchS = s === '' || row.getAttribute('data-status') === s;
                    row.style.display = matchQ && matchS ? '' : 'none';
                });
            }
            langSearch.addEventListener('input', filterLangs);
            langStatus.addEventListener('change', filterLangs);
        }
    });
</script>
</body>
</html>
