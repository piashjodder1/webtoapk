# 📱 Web To App Builder — SaaS Platform

**Web To App Builder** হলো একটি Laravel-based SaaS প্ল্যাটফর্ম যা যেকোনো website URL থেকে Native Android APK ও AAB ফাইল তৈরি করে। GitHub Actions workflow ব্যবহার করে Flutter/Kotlin WebView টেমপ্লেট compile করা হয়।

---

## ✨ Features

- 🌐 **Website to Android App** — যেকোনো URL দিয়ে native WebView app তৈরি
- 🎨 **Custom Branding** — App icon ও splash screen upload
- 🔔 **Push Notifications** — OneSignal integration
- 🔄 **Pull to Refresh** — Native swipe-to-reload
- 📡 **Offline Page** — Custom offline screen
- 📦 **APK + AAB** — Testing ও Play Store উভয়ের জন্য
- ☁️ **Cloud Storage** — Cloudflare R2 ও Amazon S3 সাপোর্ট
- 👥 **Multi-User** — Admin ও User আলাদা panel
- 📊 **Build Tracking** — Real-time build status ও history

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.2+ |
| Admin UI | Filament 5.4 |
| Build System | GitHub Actions |
| Storage | Local / Cloudflare R2 / Amazon S3 |
| Queue | Laravel Queue (database driver) |
| Database | SQLite (dev) / MySQL (prod) |

---

## 🚀 Quick Start

### Prerequisites
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL or SQLite

### Installation

```bash
# 1. Clone repository
git clone <your-repo-url>
cd website-to-app-builder

# 2. Install dependencies
composer install
npm install

# 3. Setup environment
cp .env.example .env
php artisan key:generate

# 4. Database setup
php artisan migrate:fresh --seed

# 5. Start development servers (all-in-one)
composer dev
```

### Default Accounts (after seeding)

| Panel | URL | Email | Password |
|---|---|---|---|
| Admin | `/admin` | `admin@example.com` | `password` |
| User | `/user` | `user@example.com` | `password` |

---

## ⚙️ Configuration

### 1. GitHub Actions Setup

1. **Create a GitHub Repository** with the Flutter WebView template
2. **Generate a PAT** (Personal Access Token) with `repo` and `workflow` scopes
3. In Admin Panel → Settings → GitHub Configuration:
   - Set `GitHub Repository` (e.g., `owner/repo`)
   - Set your `GitHub PAT`
   - Copy the `Build Callback Secret Token`
4. In your GitHub repo → Settings → Secrets → Actions:
   - Add secret: `API_CALLBACK_TOKEN` = (the copied token)

### 2. Cloud Storage (Optional)

By default files are stored locally. For production, configure cloud storage in Admin → Settings → Cloud Storage:

**Cloudflare R2:**
- R2 Access Key ID, Secret, Bucket name, Endpoint URL, Public URL

**Amazon S3:**
- AWS Access Key ID, Secret, Bucket name, Region (e.g., `us-east-1`)

---

## 📡 API Reference

### Build Callback Webhook

The GitHub Actions runner reports build status via this endpoint:

```
POST /api/build-callback
```

| Parameter | Type | Required | Description |
|---|---|---|---|
| `build_id` | integer | ✅ | Build record ID |
| `status` | string | ✅ | `building` / `completed` / `failed` |
| `token` | string | ✅ | Must match `build_callback_token` |
| `github_run_id` | string | - | GitHub Actions run ID |
| `apk_url` | string | - | URL to built APK |
| `aab_url` | string | - | URL to built AAB |
| `build_log` | string | - | Error log (on failure) |

---

## 🏗️ Project Structure

```
app/
├── Filament/
│   ├── Pages/Settings.php          # Admin settings (GitHub, Storage)
│   ├── Resources/                  # Admin CRUD resources
│   ├── User/                       # User panel (apps, builds)
│   └── Widgets/AdminStatsOverview.php
├── Http/Controllers/Api/
│   └── BuildCallbackController.php # GitHub runner webhook
├── Jobs/TriggerAppBuildJob.php     # Dispatches GitHub workflow
├── Models/                         # App, Build, User, Setting
├── Observers/AppObserver.php       # App delete file cleanup
├── Repositories/                   # Repository pattern
└── Services/
    ├── GitHubService.php           # GitHub Actions API
    └── StorageService.php          # Dynamic storage (local/R2/S3)
```

---

## 🖥️ Production Deployment

### Queue Worker (Supervisor)

```ini
[program:laravel-worker]
command=php /var/www/yourapp/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
```

### Cron Scheduler

```cron
* * * * * cd /var/www/yourapp && php artisan schedule:run >> /dev/null 2>&1
```

### Storage Link

```bash
php artisan storage:link
```

---

## 📄 License

MIT License
