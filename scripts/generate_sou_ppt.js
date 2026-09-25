const fs = require('fs');
const path = require('path');
const JSZip = require('jszip');

const TARGET_FONT = "Cambria";

const slideContents = {
    1: [
        "Advanced Programming with Project-I",
        "1010103494",
        "LinguaAI: Speech-to-Text & AI-Powered Endangered Language Preservation Platform"
    ],
    2: [
        "Index",
        "Introduction",
        "Background and Motivation",
        "Relevance and Importance",
        "Literature review",
        "Objectives",
        "Required Tools and Technology",
        "Method/ Approach",
        "Bibliography",
        "08-08-2026",
        "2"
    ],
    3: [
        "Introduction",
        "Endangered languages face imminent extinction due to declining native speakers, globalization, and a severe lack of digital preservation tools.",
        "LinguaAI is an AI-assisted web platform designed for automated speech-to-text transcription, translation, and digital archiving of endangered and regional languages.",
        "The platform integrates real-time Web Speech API recognition, custom language database management, analytics dashboards, and interactive admin control panel.",
        "08-08-2026",
        "DEPARTMENT OF COMPUTER ENGINEERING",
        "*Proprietary material of SILVER OAK UNIVERSITY",
        "3"
    ],
    4: [
        "Background and Motivation",
        "Over 3,000 endangered languages worldwide lack digital documentation, leading to irreversible loss of cultural heritage and linguistic diversity.",
        "Traditional documentation relies on manual field notes and audio recordings, which are slow, inaccessible, and difficult to search or analyze.",
        "Existing speech-to-text tools primarily target dominant global languages (English, Spanish, Mandarin), completely neglecting minority and regional dialects.",
        "Motivation: Build an accessible, real-time Web-based platform to transcribe, catalog, manage, and preserve endangered languages with administrative oversight and analytics.",
        "08-08-2026",
        "DEPARTMENT OF COMPUTER ENGINEERING",
        "*Proprietary material of SILVER OAK UNIVERSITY",
        "4"
    ],
    5: [
        "Relevance and Importance",
        "New Insight: A unified web architecture combining browser-level Web Speech recognition with MySQL relational indexing for real-time multilingual transcription archiving.",
        "Relevant to: Linguists, cultural researchers, educational institutions, language revival groups, and government cultural departments.",
        "Why it matters: Digital preservation enables real-time acoustic transcription, searchable transcript archives, language status tracking, and secure role-based administrative control.",
        "08-08-2026",
        "DEPARTMENT OF COMPUTER ENGINEERING",
        "*Proprietary material of SILVER OAK UNIVERSITY",
        "5"
    ],
    6: [
        "Literature Review",
        "Traditional Linguistic Archiving (e.g., ELAR, DOBES): Focuses on static audio/video storage; lacks real-time automated transcription and web accessibility [1].",
        "Commercial Automatic Speech Recognition (ASR) (Google Cloud, IBM Watson): High accuracy for major languages, but limited API support for low-resource endangered languages [2].",
        "Web Speech API & Browser-Based ASR: Provides zero-latency, cross-platform voice recognition via BCP-47 language tags directly in browser environments [3][4].",
        "Database & Analytics Architectures for Linguistics: Relational schemas (MySQL) combined with real-time character/word count indexing optimize query speeds for speech corpora [5].",
        "Research gap: Existing platforms either lack web-based real-time speech transcription or lack integrated administrative management for endangered language status tracking.",
        "08-08-2026",
        "DEPARTMENT OF COMPUTER ENGINEERING",
        "*Proprietary material of SILVER OAK UNIVERSITY",
        "6"
    ],
    7: [
        "Objectives",
        "Design and implement a responsive web application for real-time speech-to-text transcription.",
        "Create a centralized MySQL database schema to store users, endangered language metadata, transcriptions, and messages.",
        "Integrate Web Speech API supporting dynamic BCP-47 language selection and active status toggling.",
        "Implement role-based access control with secure authentication and admin dashboard management.",
        "Provide real-time analytics, character/word metrics, and CSV data export capabilities.",
        "Deploy and test the system across multiple browsers for latency, accuracy, and accessibility.",
        "08-08-2026",
        "DEPARTMENT OF COMPUTER ENGINEERING",
        "*Proprietary material of SILVER OAK UNIVERSITY",
        "7"
    ],
    8: [
        "Required Tools and Technology",
        "Programming Language: JavaScript (ES6+), PHP 8.x, HTML5, CSS3",
        "Database: MySQL / MariaDB (Relational schema with language & transcription indexing)",
        "Speech & Web APIs: Web Speech API (SpeechRecognition engine, BCP-47 language codes)",
        "Server & Environment: XAMPP / Apache Web Server, AJAX / Fetch API",
        "Development & Version Control: VS Code, Git & GitHub, Node.js",
        "Deployment & Export: Local XAMPP Web Deployment, CSV Data Exporter, System Analytics Engine",
        "08-08-2026",
        "DEPARTMENT OF COMPUTER ENGINEERING",
        "*Proprietary material of SILVER OAK UNIVERSITY",
        "8"
    ],
    9: [
        "Method/ Approach",
        "System Architecture: Client-server architecture with PHP backend processing AJAX requests and interfacing with MySQL database.",
        "Speech Processing Pipeline: Capture microphone input via Web Speech API, perform real-time speech-to-text tokenization, and compute word/character metrics.",
        "Database Schema Design: Define relational tables for users, languages, recognition_languages, transcriptions, and messages with foreign key constraints.",
        "Admin Panel & Analytics: Implement secure admin login, language status management, transcript review, and system usage analytics.",
        "Security & Validation: Enforce input sanitization, password hashing, session isolation, and structured error logging.",
        "Testing & Deployment: Conduct unit testing (TC-01 to TC-06), AJAX latency testing, and local XAMPP deployment.",
        "08-08-2026",
        "DEPARTMENT OF COMPUTER ENGINEERING",
        "*Proprietary material of SILVER OAK UNIVERSITY",
        "9"
    ],
    10: [
        "Bibliography",
        "[1] P. K. Austin and J. Sallabank, \"The Cambridge Handbook of Endangered Languages,\" Cambridge University Press, 2011.",
        "[2] A. Baevski, Y. Zhou, A. Mohamed, and M. Auli, \"wav2vec 2.0: A framework for self-supervised learning of speech representations,\" Advances in Neural Information Processing Systems, vol. 33, 2020.",
        "[3] W3C, \"Web Speech API Specification,\" W3C Community Group Report, [Online]. Available: https://w3c.github.io/speech-api/",
        "[4] M. C. Bird, \"Local language technology: Infrastructure for low-resource languages,\" Computational Linguistics, vol. 37, no. 2, pp. 325-349, 2011.",
        "[5] L. Besacier, E. Barnard, A. Karpov, and T. Schultz, \"Automatic speech recognition for under-resourced languages: A survey,\" Speech Communication, vol. 56, pp. 85-100, 2014.",
        "[6] MySQL AB, \"MySQL 8.0 Reference Manual,\" Oracle Corporation, [Online]. Available: https://dev.mysql.com/doc/refman/8.0/en/",
        "08-08-2026",
        "DEPARTMENT OF COMPUTER ENGINEERING",
        "*Proprietary material of SILVER OAK UNIVERSITY",
        "10"
    ]
};

function escapeXml(unsafe) {
    return unsafe.replace(/[<>&'"]/g, function (c) {
        switch (c) {
            case '<': return '&lt;';
            case '>': return '&gt;';
            case '&': return '&amp;';
            case '\'': return '&apos;';
            case '"': return '&quot;';
        }
    });
}

function buildTableRowXml(label, defaultValue = "") {
    return `
<a:tr h="550000">
    <a:tc>
        <a:txBody>
            <a:bodyPr lIns="91440" rIns="91440" tIns="45720" bIns="45720" anchor="ctr"/>
            <a:lstStyle/>
            <a:p>
                <a:pPr algn="l"/>
                <a:r>
                    <a:rPr lang="en-US" sz="2600" b="1"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill><a:latin typeface="${TARGET_FONT}"/><a:ea typeface="${TARGET_FONT}"/><a:cs typeface="${TARGET_FONT}"/></a:rPr>
                    <a:t>${escapeXml(label)}</a:t>
                </a:r>
            </a:p>
        </a:txBody>
        <a:tcPr marL="91440" marR="91440" marT="45720" marB="45720">
            <a:lnL w="12700" cmpd="s"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:lnL>
            <a:lnR w="12700" cmpd="s"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:lnR>
            <a:lnT w="12700" cmpd="s"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:lnT>
            <a:lnB w="12700" cmpd="s"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:lnB>
            <a:solidFill><a:srgbClr val="953734"/></a:solidFill>
        </a:tcPr>
    </a:tc>
    <a:tc>
        <a:txBody>
            <a:bodyPr lIns="91440" rIns="91440" tIns="45720" bIns="45720" anchor="ctr"/>
            <a:lstStyle/>
            <a:p>
                <a:pPr algn="l"/>
                <a:r>
                    <a:rPr lang="en-US" sz="2600" b="0"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill><a:latin typeface="${TARGET_FONT}"/><a:ea typeface="${TARGET_FONT}"/><a:cs typeface="${TARGET_FONT}"/></a:rPr>
                    <a:t>${escapeXml(defaultValue)}</a:t>
                </a:r>
            </a:p>
        </a:txBody>
        <a:tcPr marL="91440" marR="91440" marT="45720" marB="45720">
            <a:lnL w="12700" cmpd="s"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:lnL>
            <a:lnR w="12700" cmpd="s"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:lnR>
            <a:lnT w="12700" cmpd="s"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:lnT>
            <a:lnB w="12700" cmpd="s"><a:solidFill><a:srgbClr val="FFFFFF"/></a:solidFill></a:lnB>
            <a:solidFill><a:srgbClr val="953734"/></a:solidFill>
        </a:tcPr>
    </a:tc>
</a:tr>`;
}

async function updatePPT() {
    const templatePath = 'D:\\Downloads\\Phishing_Website_Detection_Review1_SOU_Format.pptx';
    const data = fs.readFileSync(templatePath);
    const zip = await JSZip.loadAsync(data);

    for (let slideNum = 1; slideNum <= 10; slideNum++) {
        const slideFile = `ppt/slides/slide${slideNum}.xml`;
        let xml = await zip.file(slideFile).async('string');

        // On Slide 1: Remove static OLE image graphicFrame and insert native editable table shape
        if (slideNum === 1) {
            xml = xml.replace(/<p:graphicFrame\b[\s\S]*?<\/p:graphicFrame>/, '');
            xml = xml.replace(/<p:sp>\s*<p:nvSpPr>\s*<p:cNvPr id="20"[\s\S]*?<\/p:sp>/, '');

            const editableTableGraphicFrameXml = `
<p:graphicFrame>
    <p:nvGraphicFramePr>
        <p:cNvPr id="20" name="Project Details Table"/>
        <p:cNvGraphicFramePr><a:graphicFrameLocks noGrp="1"/></p:cNvGraphicFramePr>
        <p:nvPr/>
    </p:nvGraphicFramePr>
    <p:xfrm>
        <a:off x="2500000" y="6050000"/>
        <a:ext cx="14685000" cy="3200000"/>
    </p:xfrm>
    <a:graphic>
        <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/table">
            <a:tbl>
                <a:tblPr firstRow="0" bandRow="0">
                    <a:tableStyleId>{59674451-553D-43D6-1609-263776A0D3FE}</a:tableStyleId>
                </a:tblPr>
                <a:tblGrid>
                    <a:gridCol w="4800000"/>
                    <a:gridCol w="9885000"/>
                </a:tblGrid>
                ${buildTableRowXml("Project ID", "")}
                ${buildTableRowXml("Enrollment No", "")}
                ${buildTableRowXml("Name", "")}
                ${buildTableRowXml("Branch", "")}
                ${buildTableRowXml("Guide Name", "")}
            </a:tbl>
        </a:graphicData>
    </a:graphic>
</p:graphicFrame>`;

            xml = xml.replace('</p:spTree>', `${editableTableGraphicFrameXml}</p:spTree>`);
        }

        const contents = slideContents[slideNum];
        let contentIndex = 0;

        // Replace paragraph contents matching <a:p> that contain <a:t>
        xml = xml.replace(/<a:p\b[^>]*>([\s\S]*?)<\/a:p>/g, (fullParagraph, pInner) => {
            if (!/<a:t[^>]*>[\s\S]*?<\/a:t>/.test(pInner)) {
                return fullParagraph;
            }

            const existingText = (pInner.match(/<a:t[^>]*>(.*?)<\/a:t>/g) || [])
                .map(t => t.replace(/<a:t[^>]*>/, '').replace(/<\/a:t>/, ''))
                .join('').trim();

            if (!existingText) {
                return fullParagraph;
            }

            if (["Project ID", "Enrollment No", "Name", "Branch", "Guide Name"].includes(existingText)) {
                return fullParagraph;
            }

            if (contentIndex >= contents.length) {
                return fullParagraph;
            }

            const newText = contents[contentIndex++];
            const escapedNewText = escapeXml(newText);

            const pPrMatch = pInner.match(/<a:pPr\b[^>]*>[\s\S]*?<\/a:pPr>/);
            const pPr = pPrMatch ? pPrMatch[0] : '';

            const rPrMatch = pInner.match(/<a:rPr\b[^>]*>[\s\S]*?<\/a:rPr>/);
            const rPr = rPrMatch ? rPrMatch[0] : `<a:rPr lang="en-US"/>`;

            const newParagraphXml = `<a:p>${pPr}<a:r>${rPr}<a:t>${escapedNewText}</a:t></a:r></a:p>`;
            return newParagraphXml;
        });

        // UNIFY ALL FONT FAMILIES to TARGET_FONT ("Cambria") across all text runs and bullets
        xml = xml.replace(/typeface="[^"]*"/g, `typeface="${TARGET_FONT}"`);

        zip.file(slideFile, xml);
    }

    // Also update slide masters and layouts if present for font consistency
    const otherXmlFiles = Object.keys(zip.files).filter(f => (f.startsWith('ppt/slideMasters/') || f.startsWith('ppt/slideLayouts/') || f.startsWith('ppt/theme/')) && f.endsWith('.xml'));
    for (const file of otherXmlFiles) {
        let xml = await zip.file(file).async('string');
        xml = xml.replace(/typeface="[^"]*"/g, `typeface="${TARGET_FONT}"`);
        zip.file(file, xml);
    }

    const outBuffer = await zip.generateAsync({ type: 'nodebuffer' });

    const targetPath1 = path.join(__dirname, '../docs/presentations/LinguaAI_Review1_SOU_Format.pptx');
    const targetPath2 = path.join(__dirname, '../docs/presentations/LinguaAI_Presentation_Redesigned_v3.pptx');

    fs.writeFileSync(targetPath1, outBuffer);
    fs.writeFileSync(targetPath2, outBuffer);

    console.log(`Successfully unified ALL font families to "${TARGET_FONT}" across all slides:`);
    console.log(' - ' + targetPath1);
    console.log(' - ' + targetPath2);
}

updatePPT().catch(console.error);
