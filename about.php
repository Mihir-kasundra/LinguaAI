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
  <title>LinguaAI – About the Project</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    .team-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:22px}
    .team-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:28px 22px;text-align:center;opacity:0;transform:translateY(24px)}
    .team-card.visible{opacity:1;transform:translateY(0);transition:opacity .5s,transform .5s}
    .avatar{width:72px;height:72px;border-radius:50%;background:var(--green-l);border:2px solid var(--border);margin:0 auto 16px;display:flex;align-items:center;justify-content:center;font-size:2rem}
    .team-card h3{font-family:var(--fh);font-size:1.05rem;margin-bottom:4px}
    .team-card .role{font-size:.82rem;color:var(--accent);font-weight:500;margin-bottom:8px}
    .team-card p{font-size:.85rem;color:var(--muted)}
    /* Timeline */
    .timeline{position:relative;padding-left:28px}
    .timeline::before{content:'';position:absolute;left:7px;top:0;bottom:0;width:2px;background:var(--border)}
    .tl-item{position:relative;margin-bottom:32px;opacity:0;transform:translateX(-16px)}
    .tl-item.visible{opacity:1;transform:translateX(0);transition:opacity .5s,transform .5s}
    .tl-dot{position:absolute;left:-25px;top:4px;width:14px;height:14px;border-radius:50%;background:var(--accent);border:2px solid var(--white);box-shadow:0 0 0 2px var(--accent)}
    .tl-item h4{font-family:var(--fh);font-size:1.05rem;margin-bottom:4px}
    .tl-item .tl-date{font-size:.78rem;color:var(--accent);font-weight:600;margin-bottom:6px}
    .tl-item p{font-size:.9rem;color:var(--muted)}
    /* Tech Stack */
    .tech-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px}
    .tech-item{background:var(--white);border:1px solid var(--border);border-radius:10px;padding:20px;text-align:center;opacity:0;transform:translateY(16px)}
    .tech-item.visible{opacity:1;transform:translateY(0);transition:opacity .45s,transform .45s}
    .tech-item .ti-icon{font-size:2rem;margin-bottom:10px}
    .tech-item .ti-name{font-size:.88rem;font-weight:600;margin-bottom:4px}
    .tech-item .ti-desc{font-size:.78rem;color:var(--muted)}
    /* Quote block */
    .quote-block{background:var(--green);color:#fff;border-radius:var(--r);padding:40px 36px;text-align:center;margin:20px 0}
    .quote-block blockquote{font-family:var(--fh);font-size:clamp(1.1rem,2.5vw,1.5rem);font-style:italic;line-height:1.5;margin-bottom:14px}
    .quote-block cite{font-size:.85rem;opacity:.8}
    /* Refs */
    .ref-list{list-style:none;display:flex;flex-direction:column;gap:10px}
    .ref-list li{font-size:.88rem;color:var(--muted);padding:12px 16px;background:var(--white);border:1px solid var(--border);border-radius:8px;border-left:3px solid var(--accent)}
    .ref-list li strong{color:var(--text)}
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
      <li><a href="about.php" class="active">About</a></li>
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
  <span class="badge">🔬 About the Project</span>
  <h1>Research, Methods <em>&amp; Team</em></h1>
  <p>Learn about our academic research into AI-driven language preservation — our methodology, the technology we use, and the people behind the project.</p>
</div>

<!-- OVERVIEW -->
<section class="section" id="overview">
  <div class="container">
    <div class="quote-block">
      <blockquote>"When a language dies, a window on the world closes — an entire way of understanding existence disappears with it."</blockquote>
      <cite>— David Harrison, linguist & language preservation advocate</cite>
    </div>
  </div>
</section>

<!-- METHODOLOGY -->
<section class="section section-alt" id="methodology">
  <div class="container">
    <div class="sec-head">
      <span class="sec-label">Methodology</span>
      <h2>Our Research Approach</h2>
      <p>A systematic, community-centered pipeline combining fieldwork with AI-powered analysis.</p>
    </div>
    <div class="steps">
      <div class="step fade-in">
        <div class="step-n">01</div>
        <div><h3>Literature Review</h3><p>We reviewed over 40 academic papers on computational linguistics, low-resource NLP, and endangered language documentation. Key sources include UNESCO's Atlas of World Languages in Danger and work by linguists like Nicholas Evans, David Harrison, and Ken Hale.</p></div>
      </div>
      <div class="step fade-in">
        <div class="step-n">02</div>
        <div><h3>Language Selection Criteria</h3><p>Languages were selected based on UNESCO's endangerment classification, the availability of existing documentation, geographic diversity, and the presence of active community revitalization efforts. We prioritized languages with fewer than 1,000 fluent speakers.</p></div>
      </div>
      <div class="step fade-in">
        <div class="step-n">03</div>
        <div><h3>AI Model Evaluation</h3><p>We evaluated three AI approaches for low-resource languages: (1) transfer learning from related high-resource languages, (2) zero-shot learning using multilingual models like mBERT and XLM-R, and (3) few-shot learning with small curated datasets.</p></div>
      </div>
      <div class="step fade-in">
        <div class="step-n">04</div>
        <div><h3>Tool Development</h3><p>Based on the evaluation, we built practical tools: a speech transcription interface using the Web Speech API, a structured language database, and a PHP backend for community contributions and archive submission.</p></div>
      </div>
      <div class="step fade-in">
        <div class="step-n">05</div>
        <div><h3>Ethical Framework</h3><p>All documentation follows ELDP (Endangered Language Documentation Programme) ethics guidelines. Data sovereignty belongs to the originating community. No recordings or transcriptions are published without explicit informed consent from the speaker community.</p></div>
      </div>
    </div>
  </div>
</section>

<!-- AI MODELS -->
<section class="section" id="models">
  <div class="container">
    <div class="sec-head">
      <span class="sec-label">Technology</span>
      <h2>AI Models &amp; Tools We Use</h2>
      <p>A breakdown of the artificial intelligence and web technologies powering this project.</p>
    </div>
    <div class="tech-grid">
      <div class="tech-item fade-in"><div class="ti-icon">🗣️</div><div class="ti-name">Web Speech API</div><div class="ti-desc">Browser-native speech recognition. Used in our live transcription tool. Supports 60+ languages.</div></div>
      <div class="tech-item fade-in"><div class="ti-icon">🤗</div><div class="ti-name">XLM-RoBERTa</div><div class="ti-desc">Facebook's cross-lingual model pre-trained on 100 languages. Used for multilingual text classification.</div></div>
      <div class="tech-item fade-in"><div class="ti-icon">🎵</div><div class="ti-name">Whisper (OpenAI)</div><div class="ti-desc">Multilingual automatic speech recognition model supporting 99 languages, including low-resource ones.</div></div>
      <div class="tech-item fade-in"><div class="ti-icon">📖</div><div class="ti-name">mBERT</div><div class="ti-desc">Multilingual BERT. Used for token-level analysis, morphological tagging, and grammar rule extraction.</div></div>
      <div class="tech-item fade-in"><div class="ti-icon">🔊</div><div class="ti-name">ELAN (Annotator)</div><div class="ti-desc">Professional linguistic annotation tool used by fieldworkers to align transcription with audio timecodes.</div></div>
      <div class="tech-item fade-in"><div class="ti-icon">🐘</div><div class="ti-name">PHP + MySQL</div><div class="ti-desc">Server-side backend handling form submissions, transcription storage, language database queries, and user contributions.</div></div>
      <div class="tech-item fade-in"><div class="ti-icon">💻</div><div class="ti-name">HTML / CSS / JS</div><div class="ti-desc">Vanilla frontend stack with jQuery for interactivity. No heavy frameworks — keeping it lightweight for rural access.</div></div>
      <div class="tech-item fade-in"><div class="ti-icon">📊</div><div class="ti-name">Python (Analysis)</div><div class="ti-desc">Used offline for corpus processing, phoneme frequency analysis, and training data preparation for ML models.</div></div>
    </div>
  </div>
</section>

<!-- TEAM -->
<section class="section section-alt">
  <div class="container">
    <div class="sec-head">
      <span class="sec-label">Team</span>
      <h2>The Research Team</h2>
      <p>A multidisciplinary college team combining computer science, linguistics, and cultural studies.</p>
    </div>
    <div class="team-grid">
      <div class="team-card fade-in"><div class="avatar">👩‍💻</div><h3>Priya Sharma</h3><div class="role">Lead Developer</div><p>Computer Science, final year. Built the AI transcription tool and PHP backend.</p></div>
      <div class="team-card fade-in"><div class="avatar">👨‍🔬</div><h3>Arjun Mehta</h3><div class="role">AI / NLP Research</div><p>Evaluated multilingual models (XLM-R, Whisper) and designed the ML pipeline for the project.</p></div>
      <div class="team-card fade-in"><div class="avatar">👩‍🎓</div><h3>Sneha Patel</h3><div class="role">Linguistics Lead</div><p>Curated the language database, reviewed documentation methodology, and ensured ethical compliance.</p></div>
      <div class="team-card fade-in"><div class="avatar">👨‍🎨</div><h3>Rohan Iyer</h3><div class="role">UI / UX Design</div><p>Designed the website interface with a focus on accessibility and clean, readable presentation.</p></div>
    </div>
  </div>
</section>

<!-- PROJECT TIMELINE -->
<section class="section" id="timeline">
  <div class="container">
    <div class="sec-head">
      <span class="sec-label">Timeline</span>
      <h2>Project Milestones</h2>
      <p>How this project evolved from a research idea to a working web platform.</p>
    </div>
    <div class="timeline">
      <div class="tl-item fade-in"><div class="tl-dot"></div><div class="tl-date">Month 1 — Jan 2026</div><h4>Topic Selection &amp; Literature Review</h4><p>Selected "AI-Driven Language Preservation" as our project topic. Read 40+ papers on computational linguistics, NLP for endangered languages, and field documentation practices.</p></div>
      <div class="tl-item fade-in"><div class="tl-dot"></div><div class="tl-date">Month 2 — Feb 2026</div><h4>Language Database Research</h4><p>Compiled a database of 25+ endangered languages using UNESCO's Atlas, Ethnologue, and academic papers. Categorized by region, status, and speaker count.</p></div>
      <div class="tl-item fade-in"><div class="tl-dot"></div><div class="tl-date">Month 3 — Mar 2026</div><h4>AI Model Evaluation</h4><p>Evaluated mBERT, XLM-R, and Whisper for low-resource transcription. Documented their capabilities and limitations in detail for our research report.</p></div>
      <div class="tl-item fade-in"><div class="tl-dot"></div><div class="tl-date">Month 4 — Apr 2026</div><h4>Website &amp; Tool Development</h4><p>Built the full multi-page website with HTML, CSS, jQuery, and PHP. Integrated the Web Speech API transcription tool and backend archive system.</p></div>
      <div class="tl-item fade-in"><div class="tl-dot"></div><div class="tl-date">Month 5 — May 2026</div><h4>Testing &amp; Submission</h4><p>User-tested the transcription tool with volunteers using 5 different languages. Finalized the research report, presentation slides, and submitted to faculty.</p></div>
    </div>
  </div>
</section>

<!-- REFERENCES -->
<section class="section section-alt" id="references">
  <div class="container">
    <div class="sec-head">
      <span class="sec-label">Academic Sources</span>
      <h2>References</h2>
      <p>Key academic works and resources that informed this project.</p>
    </div>
    <ul class="ref-list">
      <li><strong>UNESCO (2010).</strong> Atlas of the World's Languages in Danger, 3rd edition. Paris: UNESCO Publishing.</li>
      <li><strong>Harrison, D. K. (2007).</strong> When Languages Die: The Extinction of the World's Languages and the Erosion of Human Knowledge. Oxford University Press.</li>
      <li><strong>Conneau, A. et al. (2020).</strong> Unsupervised Cross-lingual Representation Learning at Scale. ACL 2020.</li>
      <li><strong>Radford, A. et al. (2022).</strong> Robust Speech Recognition via Large-Scale Weak Supervision (Whisper). OpenAI Technical Report.</li>
      <li><strong>Bender, E. M. (2011).</strong> On Achieving and Evaluating Language-Independence in NLP. Linguistic Issues in Language Technology, 6(3).</li>
      <li><strong>Devlin, J. et al. (2019).</strong> BERT: Pre-training of Deep Bidirectional Transformers for Language Understanding. NAACL 2019.</li>
      <li><strong>Ethnologue (2024).</strong> Languages of the World, 27th edition. SIL International. www.ethnologue.com</li>
      <li><strong>ELDP (Endangered Languages Documentation Programme).</strong> Ethics Guidelines for Endangered Language Documentation. SOAS University of London.</li>
    </ul>
  </div>
</section>

<footer>
  <div class="foot-inner">
    <div class="foot-brand"><span class="logo">🌿 LinguaAI</span><p>AI-driven preservation of critically endangered native languages. A college academic research project.</p></div>
    <div class="foot-col"><h4>Pages</h4><a href="index.php">Home</a><a href="languages.php">Languages</a><a href="transcribe.php">Transcribe</a><a href="about.php">About</a><a href="contact.php">Contact</a></div>
    <div class="foot-col"><h4>About</h4><a href="about.php#methodology">Methodology</a><a href="about.php#models">AI Models</a><a href="about.php#timeline">Timeline</a><a href="about.php#references">References</a></div>
    <div class="foot-col"><h4>Tools</h4><a href="transcribe.php">Transcriber</a><a href="languages.php">Language DB</a><a href="contact.php">Contribute</a></div>
  </div>
  <div class="foot-bottom">© 2025 LinguaAI Project — College Academic Research</div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="shared.js"></script>
</body>
</html>
