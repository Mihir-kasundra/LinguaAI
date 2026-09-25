<?php
/**
 * setup_db.php 
 * LinguaAI 
 * Run this script ONCE in your browser to create the database and tables automatically.
 */

$host = 'localhost';
$user = 'root'; // default XAMPP user
$pass = '';     // default XAMPP empty password

try {
    // 1. Connect without selecting a database first
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Create the Database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS linguaai_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database 'linguaai_db' verified / created.<br>";
    
    // 3. Connect directly to the new database
    $pdo = new PDO("mysql:host=$host;dbname=linguaai_db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 4. Create Transcriptions Table
    $table1 = "CREATE TABLE IF NOT EXISTS transcriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        txn_id VARCHAR(50) NOT NULL UNIQUE,
        language VARCHAR(100) NOT NULL,
        notes TEXT,
        text_content MEDIUMTEXT NOT NULL,
        chars INT NOT NULL,
        words INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($table1);
    echo "✅ Table 'transcriptions' verified / created.<br>";

    // 5. Create Contact Messages Table
    $table2 = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150) NOT NULL,
        email VARCHAR(150) NOT NULL,
        organization VARCHAR(200),
        subject VARCHAR(100) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($table2);
    echo "✅ Table 'messages' verified / created.<br>";
    
    // 6. Migrate existing flat-file data into the database (if text file exists)
    if (file_exists('transcription_data/')) {
        $files = glob('transcription_data/*.json');
        $migrated = 0;
        
        $stmt = $pdo->prepare("INSERT IGNORE INTO transcriptions (txn_id, language, notes, text_content, chars, words, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        foreach ($files as $file) {
            $data = json_decode(file_get_contents($file), true);
            if ($data) {
                // Ensure date format is MySQL compatible (Y-m-d H:i:s)
                $stmt->execute([
                    $data['id'],
                    $data['language'],
                    $data['notes'],
                    $data['text'],
                    $data['chars'],
                    $data['words'],
                    $data['timestamp']
                ]);
                $migrated++;
            }
        }
        echo "✅ Migrated $migrated existing JSON transcriptions into the MySQL database.<br>";
    }

    echo "<br><b style='color:green;'>🎉 Setup Complete! You can now safely close this page.</b>";

} catch(PDOException $e) {
    die("❌ Error connecting to MySQL: " . $e->getMessage() . "<br><b>Make sure your XAMPP MySQL module is running!</b>");
}
?>
