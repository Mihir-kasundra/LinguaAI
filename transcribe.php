<?php
session_start();
if (!isset($_SESSION['user_id']) && !isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// POST Handler for saving transcriptions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $text = trim($_POST['text'] ?? '');
    $language = trim($_POST['language'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!$text || !$language) {
        echo json_encode(['status' => 'error', 'message' => 'Missing text or language']);
        exit;
    }

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $txn_id = uniqid('txn_');
        $chars = mb_strlen($text, 'UTF-8');
        $words = str_word_count(strip_tags($text));

        $stmt = $pdo->prepare("INSERT INTO transcriptions (txn_id, language, notes, text_content, chars, words) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$txn_id, $language, $notes, $text, $chars, $words]);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit;
}

// Fetch Recognition Languages
$rec_langs = [];
try {
    $pdo = new PDO("mysql:host=localhost;dbname=linguaai_db;charset=utf8mb4", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $rec_langs = $pdo->query("SELECT * FROM recognition_languages WHERE is_active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Fallback if table doesn't exist yet
    $rec_langs = [
        ['code' => 'en-US', 'name' => 'English'],
        ['code' => 'es-ES', 'name' => 'Spanish']
    ];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>LinguaAI – Transcription Tool</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    .transcribe-layout{display:grid;grid-template-columns:1fr 1fr;gap:28px;align-items:start}
    /* Control Panel */
    .panel{background:var(--white);border:1px solid var(--border);border-radius:var(--r);padding:28px;margin-bottom:24px}
    .panel h3{font-family:var(--fh);font-size:1.2rem;margin-bottom:18px;display:flex;align-items:center;gap:10px}
    /* Mic Button */
    .mic-wrap{text-align:center;padding:20px 0}
    .mic-btn{width:100px;height:100px;border-radius:50%;background:var(--accent);border:none;cursor:pointer;font-size:2.8rem;transition:all .2s;box-shadow:0 4px 20px rgba(196,123,43,.3);display:flex;align-items:center;justify-content:center;margin:0 auto 18px}
    .mic-btn:hover{transform:scale(1.05);box-shadow:0 6px 28px rgba(196,123,43,.4)}
    .mic-btn.recording{background:#dc2626;animation:pulse-ring 1.5s ease-in-out infinite;box-shadow:0 4px 20px rgba(220,38,38,.4)}
    .mic-btn.recording:hover{transform:none}
    @keyframes pulse-ring{0%,100%{box-shadow:0 4px 20px rgba(220,38,38,.4),0 0 0 0 rgba(220,38,38,.3)}50%{box-shadow:0 4px 20px rgba(220,38,38,.4),0 0 0 14px rgba(220,38,38,0)}}
    .mic-status{font-size:.9rem;font-weight:500;color:var(--muted);text-align:center}
    .mic-status.active{color:#dc2626}
    /* Live transcript box */
    #liveBox{min-height:120px;padding:16px;background:var(--bg-alt);border-radius:8px;border:1px solid var(--border);font-size:.95rem;color:var(--text);line-height:1.7;margin-bottom:16px;font-family:var(--fb);white-space:pre-wrap}
    #liveBox .interim{color:var(--muted);font-style:italic}
    /* Controls row */
    .ctrl-row{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}
    /* Language selector */
    .lang-dropdown{width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:var(--r);background:var(--bg-alt);font-size:1rem;color:var(--text);font-family:var(--fb);margin-bottom:16px;outline:none;}
    .lang-dropdown:focus{border-color:var(--accent);}
    /* Saved transcriptions */
    .saved-list{display:flex;flex-direction:column;gap:12px;max-height:480px;overflow-y:auto}
    .saved-item{background:var(--bg-alt);border:1px solid var(--border);border-radius:8px;padding:16px}
    .saved-item .si-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;gap:10px}
    .saved-item .si-lang{font-size:.75rem;font-weight:700;background:var(--green-l);color:var(--green);padding:3px 9px;border-radius:12px}
    .saved-item .si-time{font-size:.74rem;color:var(--muted)}
    .saved-item .si-text{font-size:.88rem;color:var(--text);line-height:1.6}
    .saved-item .si-del{background:none;border:none;cursor:pointer;font-size:.8rem;color:var(--muted);padding:3px 6px;border-radius:4px}
    .saved-item .si-del:hover{color:#dc2626;background:#fee2e2}
    .empty-state{text-align:center;padding:40px 20px;color:var(--muted);font-size:.9rem}
    .empty-state .es-icon{font-size:2.4rem;margin-bottom:12px}
    /* waveform animation */
    .waveform{display:none;align-items:center;justify-content:center;gap:3px;height:28px;margin-bottom:10px}
    .waveform.active{display:flex}
    .wave-bar{width:4px;border-radius:2px;background:var(--accent);animation:wave 1s ease-in-out infinite}
    .wave-bar:nth-child(1){height:6px;animation-delay:0s}
    .wave-bar:nth-child(2){height:14px;animation-delay:.1s}
    .wave-bar:nth-child(3){height:20px;animation-delay:.2s}
    .wave-bar:nth-child(4){height:14px;animation-delay:.3s}
    .wave-bar:nth-child(5){height:6px;animation-delay:.4s}
    .wave-bar:nth-child(6){height:20px;animation-delay:.2s}
    .wave-bar:nth-child(7){height:10px;animation-delay:.1s}
    @keyframes wave{0%,100%{transform:scaleY(1)}50%{transform:scaleY(1.8)}}
    /* Not supported */
    .no-support{background:#fee2e2;border:1px solid rgba(185,28,28,.2);border-radius:8px;padding:16px;color:#b91c1c;font-size:.9rem;display:none}
    /* Save form */
    .save-form{border-top:1px solid var(--border);padding-top:18px;margin-top:4px}
    .save-row{display:flex;gap:10px;align-items:flex-end}
    .save-row .fg{flex:1;margin-bottom:0}
    /* Char counter */
    .char-count{font-size:.75rem;color:var(--muted);text-align:right;margin-top:4px}
    @media(max-width:768px){.transcribe-layout{grid-template-columns:1fr}}
  </style>
</head>
<body>

<nav id="navbar">
  <div class="nav-inner">
    <a href="index.php" class="logo">🌿 LinguaAI</a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="languages.php">Languages</a></li>
      <li><a href="transcribe.php" class="active">Transcribe</a></li>
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
  <span class="badge">🎙️ AI Transcription Tool</span>
  <h1>Live Speech <em>Transcription</em></h1>
  <p>Record and transcribe spoken language directly in your browser using the Web Speech API — then save transcriptions to our archive database.</p>
</div>

<section class="section">
  <div class="container">
    <div class="transcribe-layout">

      <!-- LEFT: Recorder -->
      <div>

        <div class="panel">
          <h3>🌐 Recognition Language</h3>
          <select class="lang-dropdown" id="langSelector">
            <?php foreach($rec_langs as $rl): ?>
                <option value="<?= htmlspecialchars($rl['code']) ?>"><?= htmlspecialchars($rl['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <p style="font-size:.82rem;color:var(--muted)">For undocumented languages, use English/closest relative and manually correct output. Works best in Chrome / Edge.</p>
        </div>

        <div class="panel">
          <h3>🎙️ Record & Transcribe</h3>

          <div class="no-support" id="noSupport">
            ⚠️ Your browser does not support the Web Speech API. Please use <strong>Google Chrome</strong> or <strong>Microsoft Edge</strong> for live transcription.
          </div>

          <div id="recorderUI">
            <div class="mic-wrap">
              <button class="mic-btn" id="micBtn">🎙️</button>
              <div class="waveform" id="waveform">
                <div class="wave-bar"></div><div class="wave-bar"></div>
                <div class="wave-bar"></div><div class="wave-bar"></div>
                <div class="wave-bar"></div><div class="wave-bar"></div>
                <div class="wave-bar"></div>
              </div>
              <div class="mic-status" id="micStatus">Click to start recording</div>
            </div>

            <div id="liveBox"><span style="color:var(--muted);font-style:italic">Transcription will appear here as you speak…</span></div>
            <div class="char-count" id="charCount">0 characters</div>

            <div class="ctrl-row">
              <button class="btn-ghost" id="clearBtn">🗑 Clear</button>
              <button class="btn-ghost" id="copyBtn">📋 Copy Text</button>
              <button class="btn-primary" id="downloadBtn">⬇ Download .txt</button>
            </div>

            <div class="save-form">
              <div class="fg">
                <label>Language Being Spoken (for archive)</label>
                <input type="text" id="spokenLang" placeholder="e.g. Ainu, Quechua, Tzeltal…"/>
              </div>
              <div class="fg">
                <label>Speaker / Location Notes <span style="color:var(--muted);font-weight:400">(optional)</span></label>
                <input type="text" id="speakerNote" placeholder="e.g. Elder speaker, Hokkaido, Japan"/>
              </div>
              <button class="btn-primary" id="saveBtn" style="width:100%">💾 Save to Archive</button>
              <div class="form-msg" id="saveMsg"></div>
            </div>
          </div>
        </div>

      </div>

      <!-- RIGHT: Saved Archive -->
      <div>
        <div class="archive-panel">
          
          <!-- Live Search Box -->
          <div style="margin-bottom: 20px;">
              <input type="text" id="liveSearchInput" placeholder="🔍 Search recordings..." style="width:100%;font-size:0.9rem;padding:10px 14px;border:1px solid var(--border);border-radius:var(--r);background:var(--bg);" autocomplete="off">
          </div>

          <div class="ap-header">
            <h2>📂 Saved Transcriptions <span class="ap-count" id="savedCount"></span></h2>
            <button class="btn-primary" id="exportAllBtn" style="font-size:.75rem;padding:6px 12px">⬇ Export (.txt)</button>
          </div>
          
          <div class="saved-list" id="savedList">
            <div class="empty-state">
              <div class="es-icon">🗂️</div>
              <p>No transcriptions saved yet.<br/>Record and save your first one!</p>
            </div>
          </div>
        </div>

        <div class="panel">
          <h3>ℹ️ How It Works</h3>
          <div class="steps" style="padding-top:0">
            <div class="step visible" style="padding:16px 0">
              <div class="step-n" style="font-size:1.6rem;min-width:44px">01</div>
              <div><h3 style="font-size:1rem">Choose Language</h3><p>Select the closest recognition language. Helps the AI engine align phonemes.</p></div>
            </div>
            <div class="step visible" style="padding:16px 0">
              <div class="step-n" style="font-size:1.6rem;min-width:44px">02</div>
              <div><h3 style="font-size:1rem">Start Recording</h3><p>Click the mic button and speak clearly. Live text appears as you talk.</p></div>
            </div>
            <div class="step visible" style="padding:16px 0">
              <div class="step-n" style="font-size:1.6rem;min-width:44px">03</div>
              <div><h3 style="font-size:1rem">Review & Correct</h3><p>Edit the transcribed text in the box to fix any AI errors manually.</p></div>
            </div>
            <div class="step visible" style="padding:16px 0;border-bottom:none">
              <div class="step-n" style="font-size:1.6rem;min-width:44px">04</div>
              <div><h3 style="font-size:1rem">Save to Archive</h3><p>Save the entry with language tag and speaker notes to our PHP backend.</p></div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<footer>
  <div class="foot-inner">
    <div class="foot-brand"><span class="logo">🌿 LinguaAI</span><p>AI-driven preservation of critically endangered native languages. A college academic research project.</p></div>
    <div class="foot-col"><h4>Pages</h4><a href="index.php">Home</a><a href="languages.php">Languages</a><a href="transcribe.php">Transcribe</a><a href="about.php">About</a><a href="contact.php">Contact</a></div>
    <div class="foot-col"><h4>Tools</h4><a href="transcribe.php">Speech Transcriber</a><a href="languages.php">Language Search</a></div>
    <div class="foot-col"><h4>Resources</h4><a href="about.php#methodology">Methodology</a><a href="about.php#models">AI Models</a></div>
  </div>
  <div class="foot-bottom">© 2025 LinguaAI Project — College Academic Research</div>
</footer>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="shared.js"></script>
<script>
$(function(){
  var recognition = null;
  var isRecording = false;
  var finalText = '';
  var selectedLang = $('#langSelector').val() || 'en-US';

  /* Check browser support */
  var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (!SpeechRecognition) {
    $('#noSupport').show();
    $('#recorderUI').hide();
  } else {
    initRecognition();
  }

  function initRecognition() {
    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = selectedLang;

    recognition.onresult = function(e) {
      var interim = '';
      for (var i = e.resultIndex; i < e.results.length; i++) {
        if (e.results[i].isFinal) {
          finalText += e.results[i][0].transcript + ' ';
        } else {
          interim += e.results[i][0].transcript;
        }
      }
      $('#liveBox').html(finalText + (interim ? '<span class="interim">' + interim + '</span>' : ''));
      updateCharCount();
    };

    recognition.onerror = function(e) {
      stopRecording();
      if (e.error === 'not-allowed') {
        $('#micStatus').text('Microphone access denied. Please allow mic access.');
      } else if (e.error === 'network') {
        $('#micStatus').text('Network error or invalid language code. Try again.');
      } else {
        $('#micStatus').text('Error: ' + e.error + '. Try again.');
      }
    };

    recognition.onend = function() {
      if (isRecording) recognition.start(); /* keep alive */
    };
  }

  function startRecording() {
    if (!recognition) return;
    recognition.lang = selectedLang;
    finalText = '';
    $('#liveBox').html('<span style="color:var(--muted);font-style:italic">Listening…</span>');
    recognition.start();
    isRecording = true;
    $('#micBtn').addClass('recording').text('⏹');
    $('#micStatus').addClass('active').text('Recording… click to stop');
    $('#waveform').addClass('active');
  }

  function stopRecording() {
    if (!recognition) return;
    isRecording = false;
    recognition.stop();
    $('#micBtn').removeClass('recording').text('🎙️');
    $('#micStatus').removeClass('active').text('Click to start recording');
    $('#waveform').removeClass('active');
    if (!finalText.trim()) {
      $('#liveBox').html('<span style="color:var(--muted);font-style:italic">Transcription will appear here as you speak…</span>');
    }
  }

  $('#micBtn').on('click', function() {
    if (isRecording) stopRecording(); else startRecording();
  });

  /* Language Dropdown */
  $('#langSelector').on('change', function() {
    selectedLang = $(this).val();
    if (isRecording) { stopRecording(); startRecording(); }
  });

  /* Clear */
  $('#clearBtn').on('click', function() {
    finalText = '';
    $('#liveBox').html('<span style="color:var(--muted);font-style:italic">Transcription will appear here as you speak…</span>');
    updateCharCount();
  });

  /* Copy */
  $('#copyBtn').on('click', function() {
    var text = getPlainText();
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
      var orig = $('#copyBtn').text();
      $('#copyBtn').text('✅ Copied!');
      setTimeout(function() { $('#copyBtn').text(orig); }, 1800);
    });
  });

  /* Download */
  $('#downloadBtn').on('click', function() {
    var text = getPlainText();
    if (!text) return;
    var blob = new Blob([text], { type: 'text/plain' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url; a.download = 'transcription_' + Date.now() + '.txt';
    a.click(); URL.revokeObjectURL(url);
  });

  /* Save to PHP backend */
  $('#saveBtn').on('click', function() {
    var text = getPlainText();
    var lang = $('#spokenLang').val().trim();
    var note = $('#speakerNote').val().trim();
    if (!text) return showMsg('err', '⚠ Nothing to save. Please transcribe some speech first.');
    if (!lang) return showMsg('err', '⚠ Please enter the language being spoken.');

    $(this).text('Saving…').prop('disabled', true);
    var self = this;

    $.ajax({
      url: 'transcribe.php', method: 'POST',
      data: { text: text, language: lang, notes: note },
      success: function(r) {
        try {
          var res = typeof r === 'string' ? JSON.parse(r) : r;
          if (res.status === 'success') {
            showMsg('ok', '✅ Transcription saved to archive!');
            addToLocalList(lang, note, text);
            $('#spokenLang, #speakerNote').val('');
          } else {
            showMsg('err', '❌ ' + (res.message || 'Save failed.'));
          }
        } catch(e) { showMsg('err', '❌ Server error.'); }
      },
      error: function() {
        /* Save locally if PHP not available */
        addToLocalList(lang, note, text);
        showMsg('ok', '✅ Saved locally (no server detected). Download to preserve.');
      },
      complete: function() { $(self).text('💾 Save to Archive').prop('disabled', false); }
    });
  });

  function getPlainText() {
    return finalText.trim() || $('#liveBox').text().trim();
  }

  function updateCharCount() {
    var c = getPlainText().length;
    $('#charCount').text(c + ' character' + (c !== 1 ? 's' : ''));
  }

  function showMsg(type, msg) {
    $('#saveMsg').attr('class', 'form-msg ' + type).text(msg).show();
    setTimeout(function() { $('#saveMsg').fadeOut(); }, 4000);
  }

  /* Local list management */
  var savedItems = [];
  var searchTimer = null;

  function fetchSavedFromArchive(query = '') {
    var url = 'fetch_transcriptions.php';
    if (query !== '') url += '?q=' + encodeURIComponent(query);
    
    $.ajax({
      url: url,
      method: 'GET',
      success: function(r) {
        if (r && r.status === 'success' && r.data) {
          savedItems = r.data.map(function(item) {
             return {
                lang: item.language,
                note: item.notes,
                text: item.text,
                time: item.timestamp
             };
          });
          renderSavedList(query !== '');
        }
      }
    });
  }

  $('#liveSearchInput').on('input', function() {
    clearTimeout(searchTimer);
    var q = $(this).val();
    searchTimer = setTimeout(function() {
        fetchSavedFromArchive(q);
    }, 300); // 300ms debounce
  });

  function addToLocalList(lang, note, text) {
    var item = { lang: lang, note: note, text: text, time: new Date().toLocaleString() };
    savedItems.unshift(item);
    renderSavedList(false);
  }

  function renderSavedList(isFiltering) {
    var $list = $('#savedList');
    $list.empty();
    if (savedItems.length === 0) {
      if (isFiltering) {
          $list.html('<div class="empty-state"><div class="es-icon">🔍</div><p>No results found.<br/>Try a different search term.</p></div>');
      } else {
          $list.html('<div class="empty-state"><div class="es-icon">🗂️</div><p>No transcriptions saved yet.<br/>Record and save your first one!</p></div>');
      }
      $('#savedCount').text('(0)');
      return;
    }
    $('#savedCount').text('(' + savedItems.length + ')');
    savedItems.forEach(function(item, idx) {
      var preview = item.text.length > 120 ? item.text.substring(0, 120) + '…' : item.text;
      var html = '<div class="saved-item">'
        + '<div class="si-head">'
        + '<span class="si-lang">' + $('<span>').text(item.lang).html() + '</span>'
        + (item.note ? '<span style="font-size:.78rem;color:var(--muted)">' + $('<span>').text(item.note).html() + '</span>' : '')
        + '<span class="si-time">' + item.time + '</span>'
        + '<button class="si-del" data-idx="' + idx + '">✕</button>'
        + '</div>'
        + '<div class="si-text">' + $('<span>').text(preview).html() + '</div>'
        + '</div>';
      $list.append(html);
    });
  }

  $(document).on('click', '.si-del', function() {
    var idx = +$(this).data('idx');
    savedItems.splice(idx, 1);
    renderSavedList();
  });

  /* Fetch on load */
  fetchSavedFromArchive();

  /* Export all */
  $('#exportAllBtn').on('click', function() {
    if (savedItems.length === 0) return;
    var out = savedItems.map(function(item, i) {
      return '=== Entry ' + (i+1) + ' | Language: ' + item.lang + ' | ' + item.time + ' ===\n'
        + (item.note ? 'Notes: ' + item.note + '\n' : '')
        + item.text + '\n';
    }).join('\n');
    var blob = new Blob([out], { type: 'text/plain' });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a'); a.href = url;
    a.download = 'linguaai_archive_' + Date.now() + '.txt';
    a.click(); URL.revokeObjectURL(url);
  });

  /* Make liveBox editable */
  $('#liveBox').attr('contenteditable', true).on('input', function() {
    finalText = $(this).text();
    updateCharCount();
  });
});
</script>
</body>
</html>
