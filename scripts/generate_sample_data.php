<?php
/**
 * generate_sample_data.php
 * LinguaAI 
 * Creates sample transcriptions so the archive doesn't look empty.
 */
$dir = 'transcription_data/';
if (!is_dir($dir)) mkdir($dir, 0755, true);

$samples = [
    [
        'language' => 'Spanish',
        'notes' => 'Test with a common language API.',
        'text' => 'Hola, cómo estás hoy? El clima es muy agradable para un paseo.'
    ],
    [
        'language' => 'Ainu',
        'notes' => 'Rare language - testing proxy transcription workflow via Japanese phonetics. Speaker: Elder from Hokkaido.',
        'text' => 'Irankarapte. Kanto poro no, a-kusu nuye ka e-huye ka siri ne yakka.'
    ],
    [
        'language' => 'Quechua',
        'notes' => 'Andean dialect variant. Recorded during fieldwork.',
        'text' => 'Rimaykullayki. Allillanchu kashanki? Ñuqapas allillanmi kashani.'
    ],
    [
        'language' => 'Navajo',
        'notes' => 'Testing tonal detection limitations.',
        'text' => 'Yáʼátʼééh. Shí éí Ashiihí nishłį́. Haltsooí bashishchiin.'
    ]
];

$recordFlat = "";

foreach ($samples as $i => $s) {
    // Generate dates working backwards
    $time = time() - (($i + 1) * 86400); 
    $ts = date('Y-m-d H:i:s', $time);
    $id = uniqid('txn_') . '_' . $i;
    
    // JSON
    $json = json_encode([
        'id'        => $id,
        'timestamp' => $ts,
        'language'  => $s['language'],
        'notes'     => $s['notes'],
        'text'      => $s['text'],
        'chars'     => strlen($s['text']),
        'words'     => str_word_count($s['text']),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    file_put_contents($dir . $id . '.json', $json);
    
    // Flat text
    $recordFlat .= "=== TRANSCRIPTION [$id] ===\n";
    $recordFlat .= "Timestamp: $ts\n";
    $recordFlat .= "Language: " . $s['language'] . "\n";
    $recordFlat .= "Notes: " . $s['notes'] . "\n";
    $recordFlat .= "Text:\n" . $s['text'] . "\n";
    $recordFlat .= str_repeat('=', 60) . "\n\n";
}

file_put_contents('transcriptions.txt', $recordFlat, FILE_APPEND);
echo "Successfully generated " . count($samples) . " sample transcripts in " . $dir . " and appended to transcriptions.txt.\n";
?>
