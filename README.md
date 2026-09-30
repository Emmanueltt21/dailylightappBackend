# dailylightappBackend

Backend API and Admin Dashboard for the DailyLight Mobile App built with CodeIgniter.

---

## 📋 Table of Contents
1. [Overview](#overview)
2. [Prerequisites & Requirements](#prerequisites--requirements)
3. [Local Development Setup (XAMPP macOS)](#local-development-setup-xampp-macos)
4. [Mobile App Integration & Testing URLs](#mobile-app-integration--testing-urls)
5. [Key API Endpoints](#key-api-endpoints)
6. [PHP Compatibility & CodeIgniter 4 Upgrade Evaluation](#php-compatibility--codeigniter-4-upgrade-evaluation)
7. [Directory Structure](#directory-structure)
8. [Git & Version Control Policy](#git--version-control-policy)

---

## 📖 Overview
`dailylightappBackend` serves as the centralized backend service for the DailyLight application. It provides:
- **Admin Dashboard**: Content management for devotionals, media, audio sermons, books, videos, hymns, and bible versions.
- **RESTful JSON API**: Endpoints serving content, user authentication, social feed, comments, chats, and subscriptions to mobile clients.
- **Payment & Subscriptions**: Integration with Stripe and PayPal.
- **Push Notifications**: Firebase Cloud Messaging (FCM) integration.

---

## ⚙️ Prerequisites & Requirements
- **Web Server**: Apache 2.4+ (with `mod_rewrite` enabled)
- **PHP**: PHP 7.4 - PHP 8.2 (configured with PDO, MySQLi, cURL, OpenSSL, mbstring)
- **Database**: MySQL / MariaDB (UTF-8 / UTF8MB4 charset)
- **Composer**: PHP dependency manager

---

## 🚀 Local Development Setup (XAMPP macOS)

### 1. Directory Location
Clone or place the project inside your XAMPP web root:
```bash
/Applications/XAMPP/xamppfiles/htdocs/dailylightapp_backend
```

### 2. Permissions (Crucial for macOS Apache)
Ensure Apache (`daemon` / `_www`) has read and execute permissions:
```bash
chmod 755 /Applications/XAMPP/xamppfiles/htdocs/dailylightapp_backend
chmod -R 755 /Applications/XAMPP/xamppfiles/htdocs/dailylightapp_backend/uploads
chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/dailylightapp_backend/application/cache
chmod -R 777 /Applications/XAMPP/xamppfiles/htdocs/dailylightapp_backend/application/logs
```

### 3. Database Configuration
Edit [application/config/database.php](file:///Applications/XAMPP/xamppfiles/htdocs/dailylightapp_backend/application/config/database.php):
```php
$db['default'] = array(
    'dsn'      => 'mysql:host=localhost;dbname=dailylightapp;unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock',
    'hostname' => 'localhost',
    'username' => 'rootuser',  // Your MySQL user
    'password' => 'rootuser',  // Your MySQL password
    'database' => 'dailylightapp',
    'dbdriver' => 'pdo',
    ...
);
```

### 4. Apache `.htaccess` Configuration
Ensure [`.htaccess`](file:///Applications/XAMPP/xamppfiles/htdocs/dailylightapp_backend/.htaccess) contains the correct `RewriteBase`:
```apache
DirectoryIndex index.php

RewriteEngine on
RewriteBase /dailylightapp_backend/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond $1 !^(index\.php|robots\.txt)

RewriteRule ^(.*)$ index.php?/$1 [L]
```

---

## 📱 Mobile App Integration & Testing URLs

When connecting your Flutter / React Native / Android / iOS app locally, choose the appropriate base URL based on your testing environment:

### 1. Physical Device (Phone connected to same Wi-Fi)
Use your Mac's Local LAN IP address:
```
http://192.168.1.122/dailylightapp_backend/
```
> **Note**: Verify your Mac's current Wi-Fi IP anytime using `ipconfig getifaddr en0`.

### 2. Android Emulator (Standard Android Studio AVD)
Android emulators run on a virtual network where `10.0.2.2` aliases the host machine's loopback (`localhost`):
```
http://10.0.2.2/dailylightapp_backend/
```

### 3. iOS Simulator / Web Browser
iOS Simulator shares the host network stack directly:
```
http://localhost/dailylightapp_backend/
```

### Quick Test Command:
```bash
curl -i http://192.168.1.122/dailylightapp_backend/api/discover
```

---

## 🔌 Key API Endpoints

All mobile requests are handled primarily via `application/controllers/Api.php`:

| Method | Endpoint | Description |
|---|---|---|
| `GET/POST` | `/api/discover` | Home discover feeds, live streams, sliders, and featured media |
| `POST` | `/api/fetch_media` | Paginated media listings (audio, video, sermons) |
| `POST` | `/api/get_devotionals` | Daily devotional readings by date |
| `POST` | `/api/get_news` | Church / organization announcements |
| `POST` | `/api/search` | Search devotionals, books, audio, and videos |
| `GET` | `/bible/getBibleVersions` | List of installed Bible translations |
| `POST` | `/api/authenticate` | User authentication & profile retrieval |
| `POST` | `/chat/send_message` | Real-time / asynchronous messaging |

---

## 🔄 PHP Compatibility & CodeIgniter 4 Upgrade Evaluation

### Current State
- **Framework**: CodeIgniter `3.1.6`
- **Environment Tested**: PHP `8.2.4` (Apache 2.4.56 on macOS)

### Compatibility Adjustments Applied:
1. **Dynamic Base URL Resolution**: Fixed `application/config/config.php` to dynamically detect `HTTP_HOST` and project root path without hardcoding.
2. **Prevented Double Domain Concatenations**: Cleaned `base_url()` in `Media_model`, `Devotionals_model`, `News_model`, `Books_model`, and `Books_cat_model` so media URLs format correctly across environments.
3. **Deprecation Handling**: Tailored error reporting in `index.php` to suppress deprecated PHP 8.1+ notices (e.g. `FILTER_SANITIZE_STRING`, dynamic properties) from leaking into JSON API responses.

### Is it possible to upgrade to CodeIgniter 4?
**Yes, but it is an architectural rewrite, not an in-place upgrade.**

#### Key Differences Between CI3 and CI4:
| Feature | CodeIgniter 3 (Current) | CodeIgniter 4 |
|---|---|---|
| Architecture | Procedural / Singleton Loaders (`$this->load->...`) | Namespaced / PSR-4 OOP (`App\Controllers\...`) |
| Routing | Config array / URI segment mapping | Advanced Router with HTTP verbs & filters |
| Models | Custom DB query helper classes | Models with Entities, validation, & auto-timestamps |
| Dependency Management | Optional Composer support | Composer-first framework architecture |
| PHP Requirement | PHP 5.6 - 7.4 (PHP 8 with warnings) | PHP 7.4 - 8.3+ native |

#### Recommended Roadmap:
- **Phase 1 (Immediate / Low Risk)**: Upgrade from CI **3.1.6** to CI **3.1.13** (latest CI3 release). This is a drop-in replacement of the `system/` directory that natively resolves remaining PHP 8.2 deprecations without touching any controller or model code.
- **Phase 2 (Long Term Migration to CI4)**:
  1. Scaffold a fresh CI4 project (`composer create-project codeigniter4/appstarter`).
  2. Implement API controllers using CI4's `ResourcePresenter` / `API\ResponseTrait`.
  3. Migrate models to CI4 Entity Models.
  4. Transition mobile API routes to versioned endpoints (`/api/v1/...`).

---

## 📁 Directory Structure
```
dailylightapp_backend/
├── application/             # Application source code
│   ├── config/              # App, DB, and Route configurations
│   ├── controllers/         # Web & API controllers (Api.php, Chat.php, etc.)
│   ├── models/              # Database models
│   ├── views/               # Admin panel views
│   └── libraries/           # BaseController and external SDKs
├── assets/                  # CSS, JS, Bootstrap, and Admin theme assets
├── system/                  # CodeIgniter core framework
├── uploads/                 # Storage for media, audio, PDFs, and Bible JSONs
├── .gitignore               # Clean VCS filter (ignores vendor & media binaries)
├── .htaccess                # Apache rewrite rules
├── index.php                # Front controller
└── README.md                # Project documentation
```

---

## 🔒 Git & Version Control Policy
- Dependencies (`/vendor/`) are excluded from Git. Run `composer install` when setting up in fresh environments.
- High-volume user media files (`uploads/audios/`, `uploads/videos/`, `uploads/pdf/`, `uploads/thumbnails/`) are ignored by `.gitignore` to keep repository size lean.
- Bible translation schemas (`uploads/*.json`) are tracked as essential application assets.
