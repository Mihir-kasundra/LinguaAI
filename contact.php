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
  <title>LinguaAI – Contact</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    .contact-layout{display:grid;grid-template-columns:1fr 2fr;gap:36px;align-items:start}
    .info-card{background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:28px;margin-bottom:20px}
    .info-card h3{font-family:var(--fh);font-size:1.1rem;margin-bottom:16px}
    .info-item{display:flex;gap:12px;margin-bottom:14px;align-items:flex-start}
    .info-item .ii-icon{font-size:1.3rem;flex-shrink:0;margin-top:1px}
    .info-item .ii-text{font-size:.88rem;color:var(--muted);line-height:1.5}
    .info-item .ii-text strong{display:block;color:var(--text);font-size:.9rem;margin-bottom:2px}
    .reason-chips{display:flex;flex-direction:column;gap:8px}
    .reason-chip{padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:.88rem;cursor:pointer;transition:all .2s;color:var(--muted);background:var(--white);display:flex;align-items:center;gap:8px}
    .reason-chip:hover,.reason-chip.active{background:var(--green-l);border-color:rgba(45,106,79,.3);color:var(--green)}
    @media(max-width:768px){.contact-layout{grid-template-columns:1fr}}
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
      <li><a href="contact.php" class="active">Contact</a></li>
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
  <span class="badge">✉️ Get In Touch</span>
  <h1>Contact <em>&amp; Collaborate</em></h1>
  <p>Researchers, volunteers, native speakers, and language communities — we'd love to hear from you and work together.</p>
</div>

<section class="section">
  <div class="container">
    <div class="contact-layout">

      <!-- LEFT INFO -->
      <div>
        <div class="info-card">
          <h3>📬 Contact Info</h3>
          <div class="info-item"><div class="ii-icon">📧</div><div class="ii-text"><strong>Email</strong>linguaai@college.edu</div></div>
          <div class="info-item"><div class="ii-icon">🏫</div><div class="ii-text"><strong>Institution</strong>Department of Computer Science &amp; Linguistics, ABC College</div></div>
          <div class="info-item"><div class="ii-icon">📅</div><div class="ii-text"><strong>Response Time</strong>We typically respond within 2–3 business days.</div></div>
        </div>
        <div class="info-card">
          <h3>🤝 How You Can Help</h3>
          <div class="reason-chips">
            <div class="reason-chip" data-subject="volunteer">🙋 Volunteer as a field recorder</div>
            <div class="reason-chip" data-subject="speaker">🗣️ I am a native speaker</div>
            <div class="reason-chip" data-subject="research">🔬 Academic collaboration</div>
            <div class="reason-chip" data-subject="data">📁 Share language data / recordings</div>
            <div class="reason-chip" data-subject="feedback">💬 Feedback on the project</div>
          </div>
        </div>
      </div>

      <!-- RIGHT FORM -->
      <div>
        <div style="background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:36px">
          <h3 style="font-family:var(--fh);font-size:1.4rem;margin-bottom:6px">Send Us a Message</h3>
          <p style="font-size:.9rem;color:var(--muted);margin-bottom:28px">Fill in the form below and we'll get back to you soon.</p>
          <form id="contactForm" action="backend.php" method="POST">
            <div class="form-grid">
              <div class="fg"><label>Full Name *</label><input type="text" name="name" id="fname" placeholder="Your name" required/></div>
              <div class="fg"><label>Email Address *</label><input type="email" name="email" id="femail" placeholder="you@example.com" required/></div>
            </div>
            <div class="fg"><label>Organisation / Institution <span style="color:var(--muted);font-weight:400">(optional)</span></label><input type="text" name="organisation" id="forg" placeholder="University, NGO, community group…"/></div>
            <div class="fg">
              <label>Subject / Reason *</label>
              <select name="subject" id="fsubject" required>
                <option value="">Select a reason…</option>
                <option value="volunteer">Volunteering as field recorder</option>
                <option value="speaker">I'm a native / heritage speaker</option>
                <option value="research">Academic / research collaboration</option>
                <option value="data">Sharing language data or recordings</option>
                <option value="feedback">Feedback on the project</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="fg"><label>Message *</label><textarea name="message" id="fmsg" rows="5" placeholder="Tell us about your interest or query…" required></textarea></div>
            <button type="submit" class="btn-primary" id="submitBtn" style="width:100%">Send Message →</button>
            <div class="form-msg" id="formMsg"></div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<footer>
  <div class="foot-inner">
    <div class="foot-brand"><span class="logo">🌿 LinguaAI</span><p>AI-driven preservation of critically endangered native languages. A college academic research project.</p></div>
    <div class="foot-col"><h4>Pages</h4><a href="index.php">Home</a><a href="languages.php">Languages</a><a href="transcribe.php">Transcribe</a><a href="about.php">About</a><a href="contact.php">Contact</a></div>
    <div class="foot-col"><h4>Tools</h4><a href="transcribe.php">Transcriber</a><a href="languages.php">Language DB</a></div>
    <div class="foot-col"><h4>Resources</h4><a href="about.php#methodology">Methodology</a><a href="about.php#references">References</a></div>
  </div>
  <div class="foot-bottom">© 2025 LinguaAI Project — College Academic Research</div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="shared.js"></script>
<script>
$(function(){
  /* Reason chips auto-fill the select */
  $('.reason-chip').on('click',function(){
    $('.reason-chip').removeClass('active');
    $(this).addClass('active');
    $('#fsubject').val($(this).data('subject'));
  });

  /* Contact form AJAX */
  $('#contactForm').on('submit',function(e){
    e.preventDefault();
    var name=$.trim($('#fname').val()),email=$.trim($('#femail').val()),msg=$.trim($('#fmsg').val());
    var $out=$('#formMsg'),$btn=$('#submitBtn');
    if(!name||!email||!msg)return $out.attr('class','form-msg err').text('⚠ Please fill in all required fields.').show();
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email))return $out.attr('class','form-msg err').text('⚠ Please enter a valid email.').show();
    $btn.text('Sending…').prop('disabled',true);$out.hide();
    $.ajax({
      url:'backend.php',method:'POST',data:$(this).serialize(),
      success:function(res){
        try {
          if (typeof res === 'string') {
              // Extract just the JSON part to avoid any PHP string warnings
              var cleanParts = res.match(/\{[\s\S]*\}/);
              if (cleanParts) { res = JSON.parse(cleanParts[0]); }
              else { res = JSON.parse(res); }
          }
          if(res.status==='success') {
            $out.attr('class','form-msg ok').text('✅ Message sent! We\'ll get back to you within 2–3 days.').show();
            alert('Form successfully submitted!');
            $('#contactForm')[0].reset();
            $('.reason-chip').removeClass('active');
          } else {
            $out.attr('class','form-msg err').text('❌ '+(res.message||'Something went wrong.')).show();
            alert('Error: ' + (res.message || 'Something went wrong.'));
          }
        }catch(e){$out.attr('class','form-msg err').text('❌ Unexpected server response.').show()}
      },
      error:function(){$out.attr('class','form-msg err').text('❌ Could not connect to server.').show()},
      complete:function(){$btn.text('Send Message →').prop('disabled',false)}
    });
  });
});
</script>
</body>
</html>
