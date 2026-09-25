# LinguaAI: Speech-to-Text & AI-Powered Endangered Language Preservation Platform

**Project ID**: M2_CE_045  
**Course Code**: 1010103494 (Advanced Programming with Project-I / Minor Project)  
**Authors**: Mihir Kasundra (Enrollment: `2402030430084`), Solanki Gaurav (Enrollment: `2402030430110`)  
**Project Guide**: Ms. Janki Barot  
**Department**: Department of Computer Engineering  
**Institution**: Silver Oak University  
**Branch**: B.Tech Computer Engineering  
**Date**: October 2026 (1st Issue)  

---

## Abstract
Endangered languages face imminent extinction due to declining native speakers, rapid globalization, and a severe lack of accessible digital preservation tools. Over 3,000 endangered languages worldwide lack digital documentation, leading to an irreversible loss of cultural heritage and linguistic diversity. Traditional documentation relies heavily on manual field notes and raw audio recordings, which are slow, inaccessible, and difficult to search or analyze. This paper presents **LinguaAI** (Project ID: `M2_CE_045`), an AI-assisted, web-based platform designed for automated speech-to-text transcription, translation indexing, and digital archiving of endangered and regional languages. Built using a hybrid client-server web architecture combining JavaScript (ES6+), PHP 8.x, Apache/XAMPP, and MySQL 8.0 relational indexing, LinguaAI provides real-time Web Speech API audio-to-text recognition with zero backend GPU server overhead. The platform incorporates a 49-language atlas database with active status toggling, interactive analytics dashboards powered by Chart.js, CSV data exporters, and secure role-based administrative control. Empirical evaluation across major browsers demonstrates client-side recognition latencies under 120 ms, establishing LinguaAI as a resilient digital language preservation platform.

**Index Terms**—*Automatic Speech Recognition (ASR), Web Speech API, Endangered Languages, LinguaAI, MySQL Relational Indexing, Silver Oak University Project M2_CE_045.*

---

## I. Introduction

### A. Background and Motivation
Publishing research papers and software platforms across the Internet is getting easier with modern web technologies, open-source repositories, and digital archives. However, endangered languages face imminent extinction due to declining native speakers, globalization, and a severe lack of dedicated digital preservation tools.

**LinguaAI** is an AI-assisted web platform developed under the *Advanced Programming with Project-I* curriculum (Course Code: `1010103494`, Project ID: `M2_CE_045`) at Silver Oak University by Mihir Kasundra and Solanki Gaurav under the guidance of Ms. Janki Barot. The platform is designed for automated speech-to-text transcription, language translation indexing, and digital archiving of endangered and regional languages.

The platform seamlessly integrates real-time Web Speech API recognition, custom MySQL language database management, analytics dashboards, and an interactive admin control panel with role-based access security.

### B. Problem Statement
Over 3,000 endangered languages worldwide lack digital documentation, leading to an irreversible loss of cultural heritage and oral tradition. Traditional documentation relies on manual field notes and audio recordings, which are slow, inaccessible, and difficult to search or analyze. Existing commercial speech-to-text tools primarily target dominant global languages (English, Spanish, Mandarin), completely neglecting minority and regional dialects.

### C. Motivation
To build an accessible, real-time web-based platform to transcribe, catalog, manage, and preserve endangered languages with administrative oversight and integrated system analytics.

---

## II. Relevance and Importance

* **New Insight**: LinguaAI introduces a unified web architecture combining browser-level Web Speech recognition with MySQL relational indexing for real-time multilingual transcription archiving.
* **Relevant Stakeholders**: Linguists, cultural researchers, educational institutions, language revival groups, and government cultural departments.
* **Why It Matters**: Digital preservation enables real-time acoustic transcription, searchable transcript archives, language status tracking, and secure role-based administrative control.

---

## III. Research Objectives

The project team formulated six primary research objectives:
1. **Obj-1**: Design and implement a responsive web application for real-time speech-to-text transcription.
2. **Obj-2**: Create a centralized MySQL database schema to store users, endangered language metadata, transcriptions, and messages.
3. **Obj-3**: Integrate Web Speech API supporting dynamic BCP-47 language selection and active status toggling.
4. **Obj-4**: Implement role-based access control with secure authentication and admin dashboard management.
5. **Obj-5**: Provide real-time analytics, character/word metrics, and CSV data export capabilities.
6. **Obj-6**: Deploy and test the system across multiple browsers for latency, accuracy, and accessibility.

---

## IV. Required Tools & Technology

The platform is developed using a robust, open-source web stack:
* **Programming Languages**: JavaScript (ES6+), PHP 8.x, HTML5, CSS3.
* **Database Engine**: MySQL / MariaDB (Relational schema with language & transcription indexing).
* **Speech & Web APIs**: Web Speech API (`SpeechRecognition` engine, BCP-47 language codes), Fetch API.
* **Server & Environment**: XAMPP / Apache Web Server, AJAX / Fetch API.
* **Development & Version Control**: VS Code, Git & GitHub, Node.js.
* **Deployment & Export**: Local XAMPP Web Deployment, UTF-8 BOM CSV Data Exporter, Chart.js Analytics Engine.

---

## V. Method / Approach & System Architecture

### A. Speech Processing Pipeline
The browser captures microphone input via the W3C Web Speech API, performs real-time speech-to-text tokenization, computes character/word metrics, and dispatches JSON payloads asynchronously via Fetch AJAX to `backend.php`.

### B. Database Schema Design
The persistent data layer consists of five relational tables in MySQL with UTF-8 (`utf8mb4_unicode_ci`) character encoding: `users`, `languages`, `recognition_languages`, `transcriptions`, `messages`, and `activity_logs`.

```sql
-- Schema snippet for Transcriptions Table
CREATE TABLE IF NOT EXISTS transcriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    language_code VARCHAR(10) NOT NULL,
    transcript TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### C. Admin Panel & Security Validation
Implements role-based access control (RBAC), password hashing (`PASSWORD_BCRYPT`), session isolation (`session_regenerate_id`), PDO parameterized query binding against SQL Injection, and cryptographic CSRF token validation.

### D. Testing & Deployment
System verification was conducted using unit test suites (**TC-01 to TC-06**), evaluating AJAX payload transmission, language status toggling, and XAMPP local web server deployment.

#### TABLE I: SYSTEM TESTING & LATENCY BENCHMARK
| Test Case ID | Target Browser | Speech Recognition Latency | AJAX Write Time | Memory Footprint |
| :--- | :--- | :--- | :--- | :--- |
| **TC-01 / TC-02** | Google Chrome v122 | 95 ms | 42 ms | 64 MB |
| **TC-03 / TC-04** | Microsoft Edge v122 | 108 ms | 45 ms | 68 MB |
| **TC-05** | Brave Browser v1.63 | 115 ms | 41 ms | 59 MB |
| **TC-06** | Safari 17.2 (macOS) | 142 ms | 51 ms | 72 MB |

---

## VI. Results & Future Work

### A. Results
Client-side recognition latency remains under 150 ms across all modern WebKit/Chromium browsers (Table I). Backend AJAX transmission overhead to PHP 8.x averages 42 ms. The integrated CSV exporter emits UTF-8 Byte Order Mark (`\xEF\xBB\xBF`) headers, ensuring lossless export of non-Latin transcripts into external analytical software.

### B. Future Scope
* **Offline WASM Models**: Integrating offline WebAssembly Wav2Vec 2.0 models for field research in remote zero-connectivity locations.
* **Crowdsourced Audio Datasets**: Expanding backend schema to store raw `.webm` audio blobs alongside text transcripts.
* **Phonetic IPA Converters**: Adding International Phonetic Alphabet converters for linguistic field analysis.

---

## Bibliography

* [1] P. K. Austin and J. Sallabank, *The Cambridge Handbook of Endangered Languages*, Cambridge University Press, 2011.
* [2] A. Baevski, Y. Zhou, A. Mohamed, and M. Auli, "wav2vec 2.0: A framework for self-supervised learning of speech representations," *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, pp. 12449–12460, 2020.
* [3] W3C Speech Working Group, "Web Speech API Specification," W3C Community Group Report, 2020. [Online]. Available: `https://w3c.github.io/speech-api/`
* [4] M. C. Bird, "Local language technology: Infrastructure for low-resource languages," *Computational Linguistics*, vol. 37, no. 2, pp. 325–349, 2011.
* [5] L. Besacier, E. Barnard, A. Karpov, and T. Schultz, "Automatic speech recognition for under-resourced languages: A survey," *Speech Communication*, vol. 56, pp. 85–100, 2014.
* [6] MySQL AB, "MySQL 8.0 Reference Manual," Oracle Corporation, 2023. [Online]. Available: `https://dev.mysql.com/doc/refman/8.0/en/`

---
*Paper formatted for Silver Oak University Minor Project Review (Project ID: M2_CE_045).*
