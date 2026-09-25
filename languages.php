<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $langs = $pdo->query("SELECT * FROM languages ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    $totalCount = count($langs);
    
    $criticalCount = 0; $endangeredCount = 0; $vulnerableCount = 0; $revitalizedCount = 0;
    foreach($langs as $l) {
        $s = strtolower($l['status']);
        if ($s === 'critical' || $s === 'extinct') $criticalCount++;
        else if ($s === 'endangered') $endangeredCount++;
        else if ($s === 'vulnerable') $vulnerableCount++;
        else if ($s === 'revitalized') $revitalizedCount++;
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
  <title>LinguaAI – Languages Database</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    .stats-row{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:16px;margin-bottom:40px}
    .stat-box{background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:22px 20px;text-align:center}
    .stat-box .n{font-family:var(--fh);font-size:2rem;font-weight:700;color:var(--accent);line-height:1}
    .stat-box .l{font-size:.8rem;color:var(--muted);margin-top:4px}
    .lang-count{font-size:.85rem;color:var(--muted);margin-top:10px}
    /* Modal */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;align-items:center;justify-content:center;padding:20px}
    .modal-overlay.open{display:flex}
    .modal{background:var(--white);border-radius:var(--r);max-width:560px;width:100%;padding:36px;position:relative;max-height:90vh;overflow-y:auto}
    .modal-close{position:absolute;top:16px;right:20px;background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--muted)}
    .modal-close:hover{color:var(--text)}
    .modal h2{font-family:var(--fh);font-size:1.6rem;margin-bottom:6px}
    .modal .modal-sub{font-size:.88rem;color:var(--muted);margin-bottom:20px}
    .modal-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px}
    .mf{display:flex;flex-direction:column;gap:3px}
    .mf .ml{font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--muted)}
    .mf .mv{font-size:.92rem;color:var(--text);font-weight:500}
    .modal-desc{font-size:.9rem;color:var(--muted);line-height:1.7;border-top:1px solid var(--border);padding-top:18px}
  </style>
</head>
<body>

<nav id="navbar">
  <div class="nav-inner">
    <a href="index.php" class="logo">🌿 LinguaAI</a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="languages.php" class="active">Languages</a></li>
      <li><a href="transcribe.php">Transcribe</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="logout.php" class="nav-cta">Logout</a></li>
    </ul>
    <button class="hamburger" id="menuBtn">☰</button>
  </div>
  <ul class="mobile-menu" id="mobileMenu">
    <li><a href="index.php">Home</a></li>
    <li><a href="languages.php">Languages</a></li>
    <li><a href="transcribe.php">Transcribe</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="contact.php">Contact</a></li>
    <li><a href="logout.php" style="color:var(--accent)">Logout</a></li>
  </ul>
</nav>

<div class="page-hero">
  <span class="badge">📖 Language Database</span>
  <h1>Endangered Languages <em>Atlas</em></h1>
  <p>A curated database of critically endangered, endangered, and vulnerable native languages — with AI preservation status and regional data.</p>
</div>

<section class="section">
  <div class="container">

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-box"><div class="n" id="totalCount"><?= $totalCount ?></div><div class="l">Languages Tracked</div></div>
      <div class="stat-box"><div class="n" style="color:#b91c1c"><?= $criticalCount ?></div><div class="l">Critical / Extinct</div></div>
      <div class="stat-box"><div class="n" style="color:#c2410c"><?= $endangeredCount ?></div><div class="l">Endangered</div></div>
      <div class="stat-box"><div class="n" style="color:#a16207"><?= $vulnerableCount ?></div><div class="l">Vulnerable</div></div>
      <div class="stat-box"><div class="n" style="color:#166534"><?= $revitalizedCount ?></div><div class="l">Revitalized</div></div>
    </div>

    <!-- Analytics Chart -->
    <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:30px;margin-bottom:40px;text-align:center;">
        <h3 style="font-family:var(--fh);font-size:1.3rem;margin-bottom:6px;">Live Transcription Analytics</h3>
        <p style="font-size:0.9rem;color:var(--muted);margin-bottom:20px;">Real-time breakdown of community recordings by language from our backend database.</p>
        <div style="max-width:500px;margin:0 auto;height:300px;display:flex;align-items:center;justify-content:center;">
           <canvas id="analyticsChart"></canvas>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-bar">
      <input type="text" id="langSearch" placeholder="🔍  Search language, region, or country…"/>
      <select id="statusFilter">
        <option value="">All Statuses</option>
        <option value="Critical">Critical</option>
        <option value="Extinct">Extinct</option>
        <option value="Endangered">Endangered</option>
        <option value="Vulnerable">Vulnerable</option>
        <option value="Revitalized">Revitalized</option>
      </select>
      <select id="regionFilter">
        <option value="">All Regions</option>
        <option value="Asia">Asia</option>
        <option value="Africa">Africa</option>
        <option value="Americas">Americas</option>
        <option value="Europe">Europe</option>
        <option value="Oceania">Oceania</option>
        <option value="Middle East">Middle East</option>
      </select>
    </div>
    <div class="lang-count" id="langCount">Showing 49 languages. Click a row for details.</div>

    <div class="table-wrap">
      <table id="langTable">
        <thead>
          <tr>
            <th>#</th><th>Language</th><th>Country / Region</th>
            <th>Speakers</th><th>Status</th><th>AI Tool</th><th>Region</th>
          </tr>
        <tbody>
          <?php foreach($langs as $i => $l): 
            $s = strtolower($l['status']);
            $cls = 'dn';
            if($s === 'critical' || $s === 'extinct') $cls = 'cr';
            if($s === 'vulnerable') $cls = 'wn';
            if($s === 'revitalized') $cls = 'vl';
          ?>
          <tr data-status="<?= htmlspecialchars($l['status']) ?>" data-region="<?= htmlspecialchars($l['region']) ?>" data-desc="<?= htmlspecialchars($l['description']) ?>">
            <td><?= $i+1 ?></td>
            <td><strong><?= htmlspecialchars($l['name']) ?></strong></td>
            <td><?= htmlspecialchars($l['country']) ?></td>
            <td><?= htmlspecialchars($l['speakers']) ?></td>
            <td><span class="tag <?= $cls ?>"><?= htmlspecialchars($l['status']) ?></span></td>
            <td><?= htmlspecialchars($l['ai_tool']) ?></td>
            <td><?= htmlspecialchars($l['region']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>
</section>

<!-- Language Detail Modal -->
<div class="modal-overlay" id="langModal">
  <div class="modal">
    <button class="modal-close" id="modalClose">✕</button>
    <h2 id="modalName">–</h2>
    <div class="modal-sub" id="modalSub">–</div>
    <div class="modal-grid">
      <div class="mf"><span class="ml">Region</span><span class="mv" id="modalRegion">–</span></div>
      <div class="mf"><span class="ml">Speakers</span><span class="mv" id="modalSpeakers">–</span></div>
      <div class="mf"><span class="ml">Status</span><span class="mv" id="modalStatus">–</span></div>
      <div class="mf"><span class="ml">AI Tool</span><span class="mv" id="modalTool">–</span></div>
    </div>
    <div class="modal-desc" id="modalDesc">–</div>
  </div>
</div>

<footer>
  <div class="foot-inner">
    <div class="foot-brand"><span class="logo">🌿 LinguaAI</span><p>AI-driven preservation of critically endangered native languages. A college academic research project.</p></div>
    <div class="foot-col"><h4>Pages</h4><a href="index.php">Home</a><a href="languages.php">Languages</a><a href="transcribe.php">Transcribe</a><a href="about.php">About</a><a href="contact.php">Contact</a></div>
    <div class="foot-col"><h4>Tools</h4><a href="transcribe.php">Speech Transcriber</a><a href="languages.php">Language Search</a></div>
    <div class="foot-col"><h4>Resources</h4><a href="about.php#methodology">Methodology</a><a href="about.php#models">AI Models</a><a href="about.php#references">References</a></div>
  </div>
  <div class="foot-bottom">© 2025 LinguaAI Project — College Academic Research</div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="shared.js"></script>
<script>
$(function(){
  /* Analytics Chart */
  $.ajax({
    url: 'api_analytics.php',
    method: 'GET',
    success: function(r) {
      if (r && r.status === 'success' && r.data && r.data.length > 0) {
        var labels = r.data.map(function(d){ return d.language; });
        var counts = r.data.map(function(d){ return d.count; });
        var ctx = document.getElementById('analyticsChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: counts,
                    backgroundColor: [
                        '#2d6a4f', '#40916c', '#52b788', '#74c69d', '#95d5b2', '#b7e4c7', '#d8f3dc', '#f1f8f5'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { font: { family: "'DM Sans', sans-serif" } } }
                },
                cutout: '65%'
            }
        });
      } else {
        $('#analyticsChart').parent().html('<p style="color:var(--muted);font-style:italic;">No transcription data recorded yet to display analytics.</p>');
      }
    }
  });

  /* Search + Filter */
  function applyFilters(){
    var q=$('#langSearch').val().toLowerCase();
    var status=$('#statusFilter').val().toLowerCase();
    var region=$('#regionFilter').val().toLowerCase();
    var count=0;
    $('#langTable tbody tr').each(function(){
      var text=$(this).text().toLowerCase();
      var rowStatus=$(this).data('status')||'';
      var rowRegion=$(this).data('region')||'';
      var show=text.includes(q)
        &&(status===''||rowStatus.toLowerCase()===status)
        &&(region===''||rowRegion.toLowerCase()===region);
      $(this).toggle(show);
      if(show)count++;
    });
    $('#langCount').text('Showing '+count+' language'+(count!==1?'s':'')+'. Click a row for details.');
  }
  $('#langSearch,#statusFilter,#regionFilter').on('input change',applyFilters);

  /* Row click → modal */
  $('#langTable tbody tr').on('click',function(){
    var $r=$(this);
    var cells=$r.find('td');
    $('#modalName').text(cells.eq(1).text());
    $('#modalSub').text('Language Entry #'+cells.eq(0).text());
    $('#modalRegion').text(cells.eq(2).text());
    $('#modalSpeakers').text(cells.eq(3).text());
    $('#modalStatus').html(cells.eq(4).html());
    $('#modalTool').text(cells.eq(5).text());
    $('#modalDesc').text($r.data('desc')||'No additional information available.');
    $('#langModal').addClass('open');
  });
  $('#modalClose, #langModal').on('click',function(e){
    if(e.target===this)$('#langModal').removeClass('open');
  });
});
</script>
</body>
</html>
