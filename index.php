<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>LinguaAI – Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    /* HERO */
    .hero{min-height:100vh;padding:110px 28px 80px;display:flex;align-items:center;justify-content:center;gap:60px;flex-wrap:wrap;position:relative;overflow:hidden}
    .hero::before{content:'';position:absolute;inset:0;z-index:0;background:radial-gradient(ellipse 60% 60% at 72% 38%,rgba(196,123,43,.10) 0%,transparent 70%),radial-gradient(ellipse 50% 50% at 18% 72%,rgba(45,106,79,.09) 0%,transparent 70%)}
    .hero-content{max-width:560px;position:relative;z-index:1}
    .badge{display:inline-block;padding:5px 14px;margin-bottom:20px;background:var(--green-l);color:var(--green);border-radius:20px;font-size:.78rem;font-weight:600;border:1px solid rgba(45,106,79,.2)}
    .hero-content h1{font-family:var(--fh);font-size:clamp(2.2rem,5vw,3.5rem);line-height:1.15;margin-bottom:20px}
    .hero-content h1 em{color:var(--accent);font-style:italic}
    .hero-content > p{color:var(--muted);font-size:1.05rem;margin-bottom:34px}
    .hero-btns{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:52px}
    .hero-stats{display:flex;gap:36px;flex-wrap:wrap}
    .stat{display:flex;flex-direction:column}
    .stat .num{font-family:var(--fh);font-size:1.9rem;font-weight:700;color:var(--accent);line-height:1.1}
    .stat .lbl{font-size:.78rem;color:var(--muted);margin-top:2px}
    /* VISUAL */
    .hero-visual{position:relative;width:300px;height:300px;display:flex;align-items:center;justify-content:center;flex-shrink:0;z-index:1}
    .ring{position:absolute;inset:0;border-radius:50%;border:2px dashed var(--border);animation:spin 20s linear infinite}
    .ring::before{content:'';position:absolute;top:-6px;left:50%;transform:translateX(-50%);width:12px;height:12px;border-radius:50%;background:var(--accent)}
    .globe{font-size:5rem;animation:bob 4s ease-in-out infinite}
    .ftag{position:absolute;background:var(--white);border:1px solid var(--border);border-radius:20px;padding:5px 13px;font-size:.76rem;font-weight:500;color:var(--muted);box-shadow:var(--shadow)}
    .ftag:nth-child(3){top:8%;left:-10%;animation:bob 3.2s ease-in-out infinite}
    .ftag:nth-child(4){top:8%;right:-10%;animation:bob 3.2s ease-in-out .7s infinite}
    .ftag:nth-child(5){bottom:12%;left:-6%;animation:bob 3.2s ease-in-out 1.4s infinite}
    .ftag:nth-child(6){bottom:12%;right:-6%;animation:bob 3.2s ease-in-out 2.1s infinite}
    /* HIGHLIGHT STRIP */
    .highlight-strip{background:var(--green);color:#fff;padding:18px 28px;text-align:center;font-size:.9rem;font-weight:500;letter-spacing:.3px}
    .highlight-strip a{color:#a8d5c2;text-decoration:underline;margin-left:8px}
    /* QUICK LINKS */
    .quick-links{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px}
    .ql-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:28px 24px;transition:all .2s;display:block}
    .ql-card:hover{transform:translateY(-4px);box-shadow:var(--shadow);border-color:var(--accent)}
    .ql-card .ql-icon{font-size:2.4rem;margin-bottom:14px}
    .ql-card h3{font-family:var(--fh);font-size:1.1rem;margin-bottom:8px;color:var(--text)}
    .ql-card p{font-size:.87rem;color:var(--muted)}
    .ql-card .ql-arrow{display:inline-block;margin-top:12px;font-size:.84rem;color:var(--accent);font-weight:500}
    @media(max-width:768px){.hero{flex-direction:column;text-align:center;padding-top:96px;gap:40px}.hero-btns,.hero-stats{justify-content:center}}
    @media(max-width:500px){.hero-visual{width:220px;height:220px}.globe{font-size:3.5rem}}
  </style>
</head>
<body>

<nav id="navbar">
  <div class="nav-inner">
    <a href="index.php" class="logo">🌿 LinguaAI</a>
    <ul class="nav-links">
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="languages.php">Languages</a></li>
      <li><a href="transcribe.php">Transcribe</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="profile.php" style="color:var(--green); font-weight:600;">Profile</a></li>
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
    <li><a href="profile.php" style="color:var(--green); font-weight:600;">Profile</a></li>
    <li><a href="logout.php" style="color:var(--accent)">Logout</a></li>
  </ul>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-content">
    <span class="badge">🎓 College Research Project</span>
    <h1>Preserving Voices<br/><em>Before They Fade</em></h1>
    <p>Harnessing Artificial Intelligence to document, revitalize, and protect critically endangered native languages around the world — one voice at a time.</p>
    <div class="hero-btns">
      <a href="languages.php" class="btn-primary">Explore Languages</a>
      <a href="transcribe.php" class="btn-outline">Try Transcription</a>
    </div>
    <div class="hero-stats">
      <div class="stat"><span class="num" data-target="7000">0</span><span class="lbl">Languages Exist</span></div>
      <div class="stat"><span class="num" data-target="3000">0</span><span class="lbl">Endangered</span></div>
      <div class="stat"><span class="num" data-target="1">0</span><span class="lbl">Dies Every 2 Weeks</span></div>
    </div>
  </div>
  <div class="hero-visual">
    <div class="ring"></div>
    <div class="globe">🌍</div>
    <div class="ftag">Quechua</div>
    <div class="ftag">Ainu</div>
    <div class="ftag">Tzeltal</div>
    <div class="ftag">Yawuru</div>
  </div>
</section>

<div class="highlight-strip">
  A language dies every 14 days. AI can help change that.
  <a href="about.php">Learn how →</a>
</div>

<!-- WHAT WE DO -->
<section class="section" id="about">
  <div class="container">
    <div class="sec-head">
      <span class="sec-label">What We Do</span>
      <h2>AI Meets Cultural Heritage</h2>
      <p>Our project explores how artificial intelligence can become a powerful tool for linguistic preservation — making the work faster, more accurate, and more accessible.</p>
    </div>
    <div class="cards">
      <div class="card fade-in"><div class="card-icon">🧠</div><h3>Speech AI</h3><p>Deep learning models trained to transcribe and interpret speech from low-resource languages with high accuracy — even from noisy field recordings.</p></div>
      <div class="card fade-in"><div class="card-icon">📚</div><h3>Digital Archives</h3><p>Structured, searchable repositories of grammar, vocabulary, oral traditions, and cultural stories — accessible to researchers and communities alike.</p></div>
      <div class="card fade-in"><div class="card-icon">🤝</div><h3>Community First</h3><p>Working directly with native speakers and local communities to ensure documentation is authentic, respectful, and community-owned.</p></div>
      <div class="card fade-in"><div class="card-icon">🌐</div><h3>NLP Tools</h3><p>Natural language processing pipelines that extract grammar rules, build bilingual dictionaries, and power language-learning apps for new generations.</p></div>
    </div>
  </div>
</section>

<!-- QUICK NAVIGATE -->
<section class="section section-alt">
  <div class="container">
    <div class="sec-head">
      <span class="sec-label">Explore</span>
      <h2>Browse the Project</h2>
      <p>Dive into any section of our research platform.</p>
    </div>
    <div class="quick-links">
      <a href="languages.php" class="ql-card fade-in">
        <div class="ql-icon">🗺️</div>
        <h3>Language Database</h3>
        <p>Browse 25+ endangered languages with status, region, and AI tool information.</p>
        <span class="ql-arrow">View All Languages →</span>
      </a>
      <a href="transcribe.php" class="ql-card fade-in">
        <div class="ql-icon">🎙️</div>
        <h3>Live Transcription</h3>
        <p>Use AI-powered speech recognition to transcribe and save audio in any language.</p>
        <span class="ql-arrow">Open Transcriber →</span>
      </a>
      <a href="about.php" class="ql-card fade-in">
        <div class="ql-icon">🔬</div>
        <h3>Research & Methods</h3>
        <p>Learn about our methodology, the AI models we use, and the team behind this project.</p>
        <span class="ql-arrow">Read About →</span>
      </a>
      <a href="contact.php" class="ql-card fade-in">
        <div class="ql-icon">✉️</div>
        <h3>Collaborate</h3>
        <p>Researchers, volunteers, and language communities — reach out to work with us.</p>
        <span class="ql-arrow">Get In Touch →</span>
      </a>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="foot-inner">
    <div class="foot-brand">
      <span class="logo">🌿 LinguaAI</span>
      <p>AI-driven preservation of critically endangered native languages. A college academic research project.</p>
    </div>
    <div class="foot-col">
      <h4>Pages</h4>
      <a href="index.php">Home</a>
      <a href="languages.php">Languages</a>
      <a href="transcribe.php">Transcribe</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
    </div>
    <div class="foot-col">
      <h4>Tools</h4>
      <a href="transcribe.php">Speech Transcriber</a>
      <a href="languages.php">Language Search</a>
      <a href="contact.php">Submit Language</a>
    </div>
    <div class="foot-col">
      <h4>Resources</h4>
      <a href="about.php#methodology">Methodology</a>
      <a href="about.php#models">AI Models</a>
      <a href="about.php#references">References</a>
    </div>
  </div>
  <div class="foot-bottom">© 2025 LinguaAI Project — College Academic Research &nbsp;|&nbsp; Built with HTML, CSS, JS & PHP</div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="shared.js"></script>
<script>
$(function(){
  /* Animated counters */
  var counted=false;
  var cobs=new IntersectionObserver(function(entries){
    if(entries[0].isIntersecting&&!counted){
      counted=true;
      $('.num[data-target]').each(function(){
        var $el=$(this),target=+$el.data('target'),step=Math.ceil(target/60),cur=0;
        var t=setInterval(function(){cur=Math.min(cur+step,target);$el.text(target>=1000?cur.toLocaleString()+'+':cur);if(cur>=target)clearInterval(t)},28);
      });
    }
  },{threshold:0.5});
  var hs=document.querySelector('.hero-stats');if(hs)cobs.observe(hs);
});
</script>
</body>
</html>
