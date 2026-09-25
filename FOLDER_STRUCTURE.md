# LinguaAI — Project Folder Structure & Architecture Standard

This document outlines the official folder organization for the **LinguaAI** repository to maintain code cleanliness, modularity, and GitHub repository standards.

---

## 📁 Directory Layout Overview

```
c:\xampp\htdocs\pbl-2\pbl\
│
├── README.md                      # Primary GitHub Documentation & Academic Metadata
├── FOLDER_STRUCTURE.md            # Repository Architecture Guidelines (This File)
├── LICENSE                        # MIT Open Source License
├── .gitignore                     # Git Exclusion Rules (node_modules, logs, tmp)
├── package.json                   # Node.js Dependencies
├── package-lock.json
├── style.css                      # Application Core Glassmorphic Stylesheet
├── shared.js                      # Application Interactivity & AJAX Helper Script
│
├── assets/                        # Static Asset Catalog
│   ├── css/
│   │   └── style.css              # Modular CSS Stylesheet
│   ├── js/
│   │   └── shared.js              # Modular JavaScript Utilities
│   └── images/
│       └── university/            # Institution Logos (Silver Oak University & NAAC Badges)
│
├── docs/                          # Comprehensive Academic Deliverables & Reports
│   ├── papers/
│   │   ├── Review_Paper.pdf       # 7-Page IEEE Conference Research Paper (Typeset)
│   │   ├── LinguaAI_IEEE_Research_Paper.pdf
│   │   └── LinguaAI_IEEE_Research_Paper.md
│   ├── reports/
│   │   ├── Weekly_Review_Report.pdf # 4-Page SOU Weekly Review Form (Item 1-28)
│   │   └── Project_Review_Report.md
│   ├── presentations/
│   │   ├── LinguaAI_Presentation.pdf # 14-Slide Widescreen Presentation PDF
│   │   └── LinguaAI_Review1_SOU_Format.pptx # Original SOU Slide Deck
│   ├── database/
│   │   └── setup_db.php           # Database Initialization & Table Creation Script
│   └── diagrams/
│       └── images/                # Activity, Class, Use Case, & Database Diagrams
│
├── data/                          # Sample Speech Corpus & Record Archives
│   └── transcription_data/
│
├── scripts/                       # Build & Document Generation Scripts
│   ├── build_doc.js
│   ├── generate_sample_data.php
│   └── setup_db.php
│
└── [Core PHP Application Workstation Files]
    ├── index.php                  # Main Landing Page & Transcriber Interface
    ├── transcribe.php             # Speech-to-Text Workstation Module
    ├── languages.php              # Searchable Endangered Language Atlas (49 Locales)
    ├── admin.php                  # Admin Moderation Panel & Dashboard
    ├── profile.php                # User Profile & Saved Transcripts
    ├── login.php                  # User Login Authentication
    ├── register.php               # User Registration / Signup
    ├── logout.php                 # Session Isolation & Logout Handler
    ├── contact.php                # Contact Us Form Interface
    ├── about.php                  # Project Vision & Scope Overview
    ├── backend.php                # AJAX Speech Save & Contact Endpoint
    ├── export_csv.php             # UTF-8 BOM CSV Data Exporter
    └── fetch_transcriptions.php   # Asynchronous Data Fetching API
```

---

## 🛡️ Guidelines to Maintain Structure

1. **Academic Deliverables (`docs/`)**:
   * **Papers**: Store all research papers and manuscript drafts in `docs/papers/`.
   * **Weekly Reports**: Store all weekly review forms and status reports in `docs/reports/`.
   * **Presentations**: Store slide decks, `.pptx`, and presentation PDFs in `docs/presentations/`.
   * **Diagrams**: Store UML, ER, and sequence diagrams in `docs/diagrams/images/`.

2. **Frontend Assets (`assets/`)**:
   * Custom CSS styling belongs in `assets/css/`.
   * JavaScript modules and utility helpers belong in `assets/js/`.
   * University logos, icons, and media files belong in `assets/images/`.

3. **Core PHP Application**:
   * Keep user-facing PHP pages at the root level for clean Apache URL routing (e.g., `localhost/pbl/index.php`).
   * Do not drop loose `.pdf`, `.pptx`, or `.tmp` build artifacts directly into the root folder.

---
*Maintained for Silver Oak University Minor Project Phase I (Project ID: M2_CE_045).*
