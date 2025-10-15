# 🎟️ Event Booking Application (Laravel 10)

A full-featured Laravel application for browsing, creating, and booking events.  
Supports **Guests**, **Attendees**, and **Organisers**, with role-based access, dashboards, bookings, categories, CSV export, and a comprehensive automated test suite.

> ✅ Current status: **All 47 tests passing**.

---

## 📚 Table of Contents
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Requirements](#-requirements)
- [Quick Start (TL;DR)](#-quick-start-tldr)
- [Full Setup Instructions](#-full-setup-instructions)
- [Environment Templates](#-environment-templates)
- [Seeded Test Accounts](#-seeded-test-accounts)
- [Run & Build](#-run--build)
- [Testing](#-testing)
- [Common Tasks](#-common-tasks)
- [Troubleshooting](#-troubleshooting)
- [Git: Add & Push README](#-git-add--push-readme)
- [Author](#-author)
- [License](#-license)

---

## 🚀 Features

### 👥 Roles
- **Guest**: Browse upcoming events.
- **Attendee**: Register/login, book events, view/cancel bookings.
- **Organiser**: Create, edit, delete events; view attendees; export CSV.

### 🎯 Core
- Laravel Breeze auth (register/login/logout, email verification, password reset).
- Event CRUD with capacity, category, date, and location.
- Booking rules (no duplicate, no over-capacity, no past events).
- Filtering, search, pagination, and category views.
- Organiser dashboard with attendee list & CSV export.

### 🧪 Tests
- Authentication & registration
- Event CRUD & filtering
- Booking workflows
- Guest restrictions
- Organiser dashboard
- Profile & password management

---

## 🧰 Tech Stack

| Category | Technology |
|---------:|------------|
| Backend  | Laravel 10 (PHP 8.1+) |
| Database | SQLite (dev/test) |
| Frontend | Blade + Bootstrap 5 |
| Auth     | Laravel Breeze |
| Tests    | PHPUnit + Laravel testing utilities |

---

## ✅ Requirements
- **PHP** 8.1+ (with `pdo_sqlite`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`)
- **Composer** 2.x
- **Node.js** 18+ and **npm** 9+
- (Optional) Mailpit for local mail testing

---

## ⚡ Quick Start (TL;DR)

```bash
# 0) Clone and enter the project
git clone https://github.com/<your-username>/event-booking-app.git
cd event-booking-app

# 1) Install deps
composer install
npm install
npm run build

# 2) Make .env and set SQLite path
cp .env.example .env
php artisan key:generate
# (Ensure DB points to the .sqlite file; see "Environment Templates" below)

# 3) Create SQLite file (if missing)
mkdir -p database
touch database/database.sqlite

# 4) Fresh DB + seed users & events
php artisan migrate:fresh --seed

# 5) Run the server
php artisan serve --host=0.0.0.0 --port=8000

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
