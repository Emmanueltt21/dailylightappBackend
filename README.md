# dailylightappBackend

Backend API and Admin Dashboard for the **DailyLight Mobile App**, built with CodeIgniter 3.

---

## 📋 Table of Contents
1. [Overview](#overview)
2. [Tech Stack](#tech-stack)
3. [Prerequisites](#prerequisites)
4. [Getting Started](#getting-started)
5. [Key API Endpoints](#key-api-endpoints)
6. [Directory Structure](#directory-structure)
7. [Contributing & Version Control](#contributing--version-control)

---

## 📖 Overview

`dailylightappBackend` is the centralized backend service for the DailyLight application. It provides:

- **Admin Dashboard** — Content management for devotionals, news, media, audio sermons, books, videos, and hymns.
- **RESTful JSON API** — Endpoints serving content, user authentication, social feed, comments, chats, and subscriptions to mobile clients.
- **Multilingual Support** — Auto-translation of devotionals and news into 8 languages (French, German, Italian, Spanish, Hindi, Russian, Portuguese, Mandarin) using DeepL with Google Translate fallback.
- **Push Notifications** — Firebase Cloud Messaging (FCM) for iOS and Android.
- **Payment & Subscriptions** — Integration with Stripe and PayPal.

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Framework | CodeIgniter 3.1.x |
| Language | PHP 7.4 – 8.2 |
| Database | MySQL / MariaDB (UTF8MB4) |
| Web Server | Apache 2.4+ (mod_rewrite) |
| Push | Firebase Cloud Messaging (FCM) |
| Payments | Stripe, PayPal |
| Translation | DeepL API + Google Translate fallback |

---

## ⚙️ Prerequisites

- PHP 7.4 – 8.2 with extensions: `pdo`, `mysqli`, `curl`, `openssl`, `mbstring`
- MySQL / MariaDB
- Apache with `mod_rewrite` enabled
- Composer

---

## 🚀 Getting Started

### 1. Clone the repository
```bash
git clone https://github.com/Emmanueltt21/dailylightappBackend.git
cd dailylightappBackend
```

### 2. Install dependencies
```bash
composer install
```

### 3. Configure the application
Copy the example config files and fill in your environment values:
```bash
cp application/config/config.example.php application/config/config.php
cp application/config/database.example.php application/config/database.php
cp .htaccess.example .htaccess
cp application/config/firebase_credentials.json.example application/config/firebase_credentials.json
```

Edit each file with your server's base URL, database credentials, and API keys.

### 4. Set permissions
```bash
chmod -R 755 uploads/
chmod -R 777 application/cache/ application/logs/
```

### 5. Import the database
Import your SQL schema into your MySQL database, then update `application/config/database.php` with your credentials.

---

## 🔌 Key API Endpoints

All mobile requests are handled via `application/controllers/Api.php`:

| Method | Endpoint | Description |
|---|---|---|
| `GET/POST` | `/api/discover` | Home discover feeds, live streams, sliders |
| `POST` | `/api/fetch_media` | Paginated media listings |
| `POST` | `/api/devotionals` | Daily devotional by date (with all language translations) |
| `POST` | `/api/get_news` | News / announcements (with all language translations) |
| `POST` | `/api/search` | Search across content types |
| `GET` | `/bible/getBibleVersions` | Bible translations list |
| `POST` | `/api/authenticate` | User authentication |
| `POST` | `/chat/send_message` | Messaging |

---

## 📁 Directory Structure

```
dailylightapp_backend/
├── application/
│   ├── config/          # App, DB, routes, and API key configuration
│   ├── controllers/     # Web & API controllers
│   ├── models/          # Database models (includes translation logic)
│   ├── views/           # Admin panel views
│   └── libraries/       # BaseController and external SDKs
├── assets/              # CSS, JS, and Admin theme assets
├── system/              # CodeIgniter core framework
├── uploads/             # Media storage (excluded from git)
├── .gitignore
├── index.php            # Front controller
└── README.md
```

---

## 🔒 Contributing & Version Control

- `vendor/`, `uploads/` (media binaries), and environment-specific config files (`.htaccess`, `config.php`, `database.php`) are excluded from git.
- Each deployment environment manages its own config files independently.
- Bible translation JSON schemas in `uploads/` are tracked as essential application assets.
- Run `composer install` when setting up in a fresh environment.
