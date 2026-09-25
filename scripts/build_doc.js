const fs = require('fs');
const path = require('path');

function getBase64(file) {
    const data = fs.readFileSync(file);
    return Buffer.from(data).toString('base64');
}

const classDiagram = getBase64(path.join(__dirname, '../docs/diagrams/images/class_diagram.png'));
const usecaseDiagram = getBase64(path.join(__dirname, '../docs/diagrams/images/usecase_diagram.png'));
const userUsecaseDiagram = getBase64(path.join(__dirname, '../docs/diagrams/images/user_usecase_diagram.png'));
const activityDiagram = getBase64(path.join(__dirname, '../docs/diagrams/images/activity_diagram.png'));

const html = `
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <title>LinguaAI Project Diagrams</title>
    <style>
        body { font-family: "Calibri", sans-serif; line-height: 1.5; color: #333; margin: 20px; }
        h1 { color: #2C5E3D; border-bottom: 2px solid #2C5E3D; padding-bottom: 5px; }
        h2 { color: #2C5E3D; margin-top: 30px; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; margin-bottom: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; color: #333; font-weight: bold; }
        img { max-width: 100%; height: auto; margin-top: 15px; border: 1px solid #ccc; box-shadow: 2px 2px 5px rgba(0,0,0,0.1); }
        .page-break { page-break-before: always; }
    </style>
</head>
<body>
    <h1>LinguaAI - System Architecture &amp; Diagrams</h1>
    
    <h2>1. Class Diagram</h2>
    <p>Visualizes the main entities and their attributes in the LinguaAI system.</p>
    <img src="data:image/png;base64,${classDiagram}" alt="Class Diagram">
    
    <div class="page-break"></div>
    
    <h2>2.1 Use-Case Diagram (Admin)</h2>
    <p>Illustrates the interactions between the Administrator and the system.</p>
    <img src="data:image/png;base64,${usecaseDiagram}" alt="Use Case Diagram">
    
    <div class="page-break"></div>
    
    <h2>2.2 Use-Case Diagram (User)</h2>
    <p>Illustrates the interactions between the Standard User and the system.</p>
    <img src="data:image/png;base64,${userUsecaseDiagram}" alt="User Use Case Diagram">
    
    <div class="page-break"></div>
    
    <h2>3. Activity Diagram (Transcription Workflow)</h2>
    <p>Depicts the flow of actions when a user utilizes the speech transcription tool.</p>
    <img src="data:image/png;base64,${activityDiagram}" alt="Activity Diagram">
    
    <div class="page-break"></div>

    <h2>4. Test Cases</h2>
    <p>Test cases for key functionalities across the platform, presented in a structured test matrix.</p>
    <table>
        <tr>
            <th>Test ID</th>
            <th>Module</th>
            <th>Description</th>
            <th>Pre-conditions</th>
            <th>Steps</th>
            <th>Expected Result</th>
        </tr>
        <tr>
            <td><strong>TC-01</strong></td>
            <td>Auth</td>
            <td>User Login Validation</td>
            <td>Valid user account exists in DB</td>
            <td>1. Navigate to login<br>2. Enter correct credentials<br>3. Submit</td>
            <td>User is logged in and redirected to dashboard or home.</td>
        </tr>
        <tr>
            <td><strong>TC-02</strong></td>
            <td>Auth</td>
            <td>Invalid Login Handling</td>
            <td>-</td>
            <td>1. Navigate to login<br>2. Enter wrong password<br>3. Submit</td>
            <td>Error message displayed; access denied.</td>
        </tr>
        <tr>
            <td><strong>TC-03</strong></td>
            <td>Transcribe</td>
            <td>Live Speech to Text</td>
            <td>Mic access granted by browser</td>
            <td>1. Select Language<br>2. Click Mic<br>3. Speak clearly</td>
            <td>Recognized text accurately appears in the live text box.</td>
        </tr>
        <tr>
            <td><strong>TC-04</strong></td>
            <td>Transcribe</td>
            <td>Save Transcription</td>
            <td>Live text box is not empty</td>
            <td>1. Enter spoken language metadata<br>2. Click Save</td>
            <td>Success notification appears; entry added to the saved archive list.</td>
        </tr>
        <tr>
            <td><strong>TC-05</strong></td>
            <td>Admin</td>
            <td>Toggle Rec. Language</td>
            <td>Logged in as Administrator</td>
            <td>1. Go to Admin Panel<br>2. Rec. Languages Tab<br>3. Click Hide/Activate</td>
            <td>Language status updates visually; DB reflects change; dropdown is updated.</td>
        </tr>
        <tr>
            <td><strong>TC-06</strong></td>
            <td>Admin</td>
            <td>Delete Data Entry</td>
            <td>Logged in as Administrator</td>
            <td>1. Go to Admin Panel<br>2. Languages Tab<br>3. Click Delete</td>
            <td>Confirmation prompt appears; upon confirm, entry is permanently removed.</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <h2>5. Data Dictionary</h2>
    <p>Structural metadata defining the schema of the MySQL Database (<code>linguaai_db</code>).</p>

    <h3>Table: <code>users</code></h3>
    <table>
        <tr><th>Field Name</th><th>Data Type</th><th>Constraint</th><th>Description</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary Key, Auto Increment</td><td>Unique identifier for the user.</td></tr>
        <tr><td>username</td><td>VARCHAR(50)</td><td>Unique, Not Null</td><td>User's chosen display name.</td></tr>
        <tr><td>email</td><td>VARCHAR(100)</td><td>Unique, Not Null</td><td>User's registered email address.</td></tr>
        <tr><td>password_hash</td><td>VARCHAR(255)</td><td>Not Null</td><td>Securely hashed password.</td></tr>
        <tr><td>created_at</td><td>TIMESTAMP</td><td>Not Null, Default CURRENT_TIMESTAMP</td><td>Date and time of account creation.</td></tr>
    </table>

    <h3>Table: <code>languages</code></h3>
    <table>
        <tr><th>Field Name</th><th>Data Type</th><th>Constraint</th><th>Description</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary Key, Auto Increment</td><td>Unique identifier for the language.</td></tr>
        <tr><td>name</td><td>VARCHAR(100)</td><td>Not Null</td><td>Name of the endangered language.</td></tr>
        <tr><td>region</td><td>VARCHAR(100)</td><td>Not Null</td><td>Geographic region where spoken.</td></tr>
        <tr><td>country</td><td>VARCHAR(100)</td><td>Not Null</td><td>Country where spoken.</td></tr>
        <tr><td>status</td><td>VARCHAR(50)</td><td>Not Null</td><td>Level of endangerment.</td></tr>
        <tr><td>speakers</td><td>VARCHAR(50)</td><td>Not Null</td><td>Estimated number of speakers remaining.</td></tr>
    </table>

    <h3>Table: <code>recognition_languages</code></h3>
    <table>
        <tr><th>Field Name</th><th>Data Type</th><th>Constraint</th><th>Description</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary Key, Auto Increment</td><td>Unique identifier for the recognition language.</td></tr>
        <tr><td>code</td><td>VARCHAR(20)</td><td>Unique, Not Null</td><td>BCP-47 Language Code for Web Speech API.</td></tr>
        <tr><td>name</td><td>VARCHAR(100)</td><td>Not Null</td><td>Display name of the language.</td></tr>
        <tr><td>is_active</td><td>TINYINT(1)</td><td>Nullable, Default 1</td><td>Boolean indicating if language is active/visible.</td></tr>
    </table>

    <h3>Table: <code>transcriptions</code></h3>
    <table>
        <tr><th>Field Name</th><th>Data Type</th><th>Constraint</th><th>Description</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary Key, Auto Increment</td><td>Unique identifier for the transcription.</td></tr>
        <tr><td>txn_id</td><td>VARCHAR(50)</td><td>Unique, Not Null</td><td>System-generated unique transcription ID.</td></tr>
        <tr><td>language</td><td>VARCHAR(50)</td><td>Not Null</td><td>The language spoken during transcription.</td></tr>
        <tr><td>notes</td><td>TEXT</td><td>Nullable</td><td>Optional context or notes provided by the user.</td></tr>
        <tr><td>text_content</td><td>LONGTEXT</td><td>Not Null</td><td>The transcribed speech text content.</td></tr>
        <tr><td>chars</td><td>INT</td><td>Nullable, Default 0</td><td>Total character count of the transcribed text.</td></tr>
        <tr><td>words</td><td>INT</td><td>Nullable, Default 0</td><td>Total word count of the transcribed text.</td></tr>
        <tr><td>created_at</td><td>TIMESTAMP</td><td>Not Null, Default CURRENT_TIMESTAMP</td><td>Date and time the transcription was saved.</td></tr>
    </table>

    <h3>Table: <code>messages</code></h3>
    <table>
        <tr><th>Field Name</th><th>Data Type</th><th>Constraint</th><th>Description</th></tr>
        <tr><td>id</td><td>INT</td><td>Primary Key, Auto Increment</td><td>Unique identifier for the message.</td></tr>
        <tr><td>name</td><td>VARCHAR(100)</td><td>Not Null</td><td>Sender's full name.</td></tr>
        <tr><td>email</td><td>VARCHAR(100)</td><td>Not Null</td><td>Sender's contact email address.</td></tr>
        <tr><td>subject</td><td>VARCHAR(200)</td><td>Not Null</td><td>Subject line of the message.</td></tr>
        <tr><td>message</td><td>TEXT</td><td>Not Null</td><td>Body content of the contact message.</td></tr>
        <tr><td>created_at</td><td>TIMESTAMP</td><td>Not Null, Default CURRENT_TIMESTAMP</td><td>Date and time the message was sent.</td></tr>
    </table>
</body>
</html>
`;

const outputPath = path.join(__dirname, '../docs/reports/LinguaAI_Diagrams_and_Docs.doc');
fs.writeFileSync(outputPath, html);
console.log('Successfully generated ' + outputPath);
