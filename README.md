# LinguaAI: Speech-to-Text & AI-Powered Endangered Language Preservation Platform

[![Project Status](https://img.shields.io/badge/Project_Phase-Minor_Project_Phase_I_(55%25)-blue.svg)](#academic-metadata)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4.svg?logo=php&logoColor=white)](#technology-stack)
[![MySQL](https://img.shields.io/badge/MySQL-8.0_utf8mb4-4479A1.svg?logo=mysql&logoColor=white)](#technology-stack)
[![Institution](https://img.shields.io/badge/Institution-Silver_Oak_University-953734.svg)](#academic-metadata)

> **LinguaAI** is an AI-assisted, browser-native web platform designed for real-time speech-to-text transcription, phonetic recording, and digital archiving of endangered and regional languages. Built using lightweight client-side W3C Web Speech APIs and a normalized MySQL relational data layer, LinguaAI provides zero-latency audio conversion across **49 worldwide language locales** with zero server GPU overhead.

---

## 🎓 Academic & Project Metadata

| Attribute | Details |
| :--- | :--- |
| **Institution** | Silver Oak University, Ahmedabad |
| **Department** | Department of Computer Engineering |
| **Course** | Minor Project (Phase I) — Course Code: `1010103494` |
| **Project ID** | `M2_CE_045` |
| **Student Authors** | **Mihir Kasundra** (`2402030430084`) & **Solanki Gaurav** (`2402030430110`) |
| **Project Guide** | **Ms. Janki Barot** |
| **Academic Session** | Academic Year 2026 (Phase I Review - 55% Milestone) |

---

## 🌟 Key Features

* 🎙️ **Real-Time Speech-to-Text Workstation (`index.php` / `transcribe.php`)**:
  * Integrates client-side W3C `SpeechRecognition` interface with sub-120 ms latency.
  * Supports dynamic BCP-47 locale switching across 49 language dialects.
  * Features Text-to-Speech (TTS) audio feedback via `SpeechSynthesis` for instant transcript verification.
* 🗺️ **Searchable Endangered Language Atlas (`languages.php`)**:
  * Indexes 49 global language locales with speaker count estimates, regions, and international endangerment status badges (*Safe*, *Vulnerable*, *Severely Endangered*, *Critically Endangered*, *Extinct*).
  * Real-time JavaScript search filtering across language names and endangerment tiers.
* 📊 **Analytics Dashboard (`dashboard.php` / `api_analytics.php`)**:
  * Visualizes transcription volume, character counts, and active language distributions using Chart.js bar and doughnut widgets.
* 🛡️ **Defense-in-Depth Security**:
  * **SQL Injection Mitigation**: All DB interactions execute via PHP Data Objects (PDO) with parameterized query bindings.
  * **XSS Defense**: DOM outputs sanitized using `htmlspecialchars()`.
  * **Session Protection**: `session_regenerate_id(true)` and `HttpOnly` / `SameSite=Strict` cookie protection.
  * **CSRF Token Validation**: Cryptographic anti-CSRF token verification (`bin2hex(random_bytes(32))`).
* 📁 **Lossless Data Portability (`export_csv.php`)**:
  * Prepends UTF-8 Byte Order Mark (`\xEF\xBB\xBF`) headers to exported CSV files for seamless ingestion into Microsoft Excel, R, or SPSS without character corruption.

---

## 🛠️ Technology Stack

* **Frontend**: HTML5, Vanilla CSS3 (Glassmorphism Dark UI Design System), JavaScript (ES6+), Chart.js v4.4.1.
* **APIs**: W3C Web Speech API (`SpeechRecognition` & `SpeechSynthesisUtterance`), Fetch API AJAX.
* **Backend**: PHP 8.x (PDO MySQL drivers, Session Management).
* **Database**: MySQL 8.0 / MariaDB (`utf8mb4_unicode_ci` character collation).
* **Web Server**: Apache / XAMPP Local Web Server.

---

## 📂 Project Directory Structure

```
linguaai/
├── index.php                      # Main Landing & Webstation Page
├── transcribe.php                 # Speech-to-Text Recording Engine
├── languages.php                  # Searchable Endangered Language Atlas
├── admin.php                      # Admin Control Panel & Content Moderation
├── profile.php                    # User Profile & Saved Transcripts
├── login.php                      # User Authentication (Login)
├── register.php                   # User Registration (Signup)
├── logout.php                     # Session Logout Handler
├── contact.php                    # Contact Us Form Interface
├── about.php                      # About LinguaAI Project
├── backend.php                    # AJAX Speech Save & Contact Endpoint
├── export_csv.php                 # UTF-8 BOM CSV Exporter
├── fetch_transcriptions.php       # Asynchronous Data Retrieval
│
├── assets/                        # Frontend Static Assets
│   ├── css/
│   │   └── style.css              # Custom Glassmorphism Stylesheet
│   ├── js/
│   │   └── shared.js              # Client UI Interactivity & AJAX Helpers
│   └── images/
│       └── university/            # Institution Logos & Badges
│
├── docs/                          # Academic Deliverables & Reports
│   ├── papers/
│   │   ├── Review_Paper.pdf       # 7-Page Typeset IEEE Research Paper
│   │   └── LinguaAI_IEEE_Research_Paper.pdf
│   ├── reports/
│   │   ├── Weekly_Review_Report.pdf # 4-Page SOU Weekly Review Form
│   │   └── Project_Review_Report.md
│   └── presentations/
│       ├── LinguaAI_Presentation.pdf # 14 Widescreen Presentation Slides
│       └── LinguaAI_Review1_SOU_Format.pptx
│
├── data/                          # Sample Corpus Data Archives
├── scripts/                       # Database Setup & Presentation Generator Scripts
├── .gitignore                     # Git Exclusion Rules
├── LICENSE                        # MIT Open Source License
└── README.md                      # Project GitHub Documentation
```

---

## ⚡ Quick Start & Installation Guide

### Prerequisites
1. **XAMPP / WAMP Server** with Apache and MySQL enabled.
2. **PHP 8.0+** with PDO extension enabled.
3. **Web Browser** supporting W3C Web Speech API (Google Chrome, Microsoft Edge, or Brave).

### Setup Instructions
1. **Clone the Repository**:
   ```bash
   git clone https://github.com/your-username/linguaai.git
   cd linguaai
   ```
2. **Move to XAMPP htdocs Directory**:
   Move the project folder into `c:/xampp/htdocs/pbl/` or your web server directory.
3. **Import Database**:
   * Open **phpMyAdmin** (`http://localhost/phpmyadmin/`).
   * Create a database named `linguaai_db`.
   * Run the setup script by visiting `http://localhost/pbl/scripts/setup_db.php` in your browser.
4. **Launch Application**:
   Open your browser and navigate to:
   ```
   http://localhost/pbl/index.php
   ```

---

## 📄 Academic Deliverables & Documents

* 📑 **7-Page IEEE Research Paper**: [`docs/papers/Review_Paper.pdf`](docs/papers/Review_Paper.pdf)
* 📋 **4-Page Weekly Review Report**: [`docs/reports/Weekly_Review_Report.pdf`](docs/reports/Weekly_Review_Report.pdf)
* 🖥️ **14-Slide Presentation Deck**: [`docs/presentations/LinguaAI_Presentation.pdf`](docs/presentations/LinguaAI_Presentation.pdf)

---

## 📜 License

This project is open-source software licensed under the **[MIT License](LICENSE)**.

---
*Developed for Silver Oak University Minor Project Phase I (Project ID: M2_CE_045).*
