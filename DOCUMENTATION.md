# Website To App Builder - SaaS Platform Documentation

This document contains a comprehensive guide to installing, configuring, deploying, and integrating the Website To App Builder SaaS platform.

---

## 1. Installation Guide (Local Setup)

### Prerequisites
- **PHP >= 8.2** (with common extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`)
- **Composer** (PHP Package Manager)
- **Node.js & NPM** (For compiling frontend assets if necessary, although Filament loads precompiled assets by default)
- **MySQL / SQLite**

### Step-by-Step Installation

1. **Clone & Navigate:**
   ```bash
   git clone <your-repository-url>
   cd website-to-app-builder
   ```

2. **Install Composer Dependencies:**
   ```bash
   composer install
   ```

3. **Configure Environment File:**
   Copy the example environment file and configure your database settings:
   ```bash
   cp .env.example .env
   ```
   *Edit `.env` and set:*
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=website_to_apps
   DB_USERNAME=root
   DB_PASSWORD=your_password
   
   QUEUE_CONNECTION=database
   ```

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations & Seed Database:**
   This command creates the tables and seeds default admin and demo user accounts, as well as initial settings:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Default Accounts Seeded:*
   - **Admin Panel:** URL: `/admin` | Email: `admin@example.com` | Password: `password`
   - **User Panel:** URL: `/user` | Email: `user@example.com` | Password: `password`

6. **Start Local Servers:**
   Run the dev server:
   ```bash
   php artisan serve
   ```
   Start the queue worker (crucial for executing GitHub API requests asynchronously):
   ```bash
   php artisan queue:work
   ```

---

## 2. GitHub Actions Integration Setup

To allow the SaaS app to build Android APK and AAB files, you must configure a private/public GitHub repository containing the Flutter WebView template:

1. **Generate a GitHub Personal Access Token (PAT):**
   - Go to: **GitHub Settings -> Developer Settings -> Personal Access Tokens (classic)**.
   - Click **Generate new token**.
   - Select the `repo` and `workflow` scopes.
   - Copy the generated token.

2. **Save Token in SaaS Admin Panel:**
   - Log in to the SaaS Admin panel at `/admin` (using `admin@example.com` / `password`).
   - Navigate to the **Settings** page in the sidebar.
   - Go to **GitHub Configuration**.
   - Input your **GitHub Repository** (e.g. `your-username/website-to-app-builder`).
   - Input the **GitHub PAT** you just copied.
   - Copy the **Build Callback Secret Token** shown in the settings (e.g., `5a2b3...`).

3. **Add Callback Secret Token to GitHub Repository Secrets:**
   - Go to your GitHub repository on github.com.
   - Navigate to **Settings -> Secrets and variables -> Actions**.
   - Click **New repository secret**.
   - Name: `API_CALLBACK_TOKEN`
   - Value: (Paste the *Build Callback Secret Token* copied from your SaaS Admin Settings page).
   - Click **Add secret**.

4. **Verify Workflow File:**
   Make sure the workflow file `.github/workflows/build_app.yml` is pushed to the `main` branch of your GitHub repository.

---

## 3. Cloud Storage Setup (Cloudflare R2 / AWS S3)

By default, the platform saves built APK and AAB files on the local public disk. To set up production-ready cloud storage:

### Cloudflare R2
1. Log in to your Cloudflare dashboard and create an R2 Bucket.
2. Generate **R2 API Tokens** with read/write access (Access Key ID and Secret Access Key).
3. In the SaaS Admin Settings under **Cloud Storage**:
   - Set **Active Storage Disk** to `Cloudflare R2`.
   - Fill in R2 Access Key ID, Secret Access Key, Bucket name, and Endpoint URL (found on your R2 bucket dashboard, e.g. `https://<account-id>.r2.cloudflarestorage.com`).
   - Save settings.

### Amazon S3
1. Log in to AWS Console and create an S3 bucket.
2. Create an IAM User with programatic access and assign policies for S3 read/write.
3. In the SaaS Admin Settings under **Cloud Storage**:
   - Set **Active Storage Disk** to `Amazon S3`.
   - Fill in AWS Key, AWS Secret, Bucket Name, and Region (e.g. `us-east-1`).
   - Save settings.

---

## 4. API Documentation (GitHub Runner Callback)

The platform provides a secured callback webhook endpoint for the GitHub runner to report compilation statuses and send compiled binary files.

### Webhook Endpoint
- **URL:** `POST /api/build-callback`
- **Content-Type:** `multipart/form-data` (or `application/json` if reporting failure/started state)

### Form Parameters
| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `build_id` | Integer | Yes | The ID of the build run tracking record. |
| `status` | String | Yes | The current run state: `building`, `completed`, or `failed`. |
| `github_run_id` | String | No | The GitHub Actions run ID (e.g. `98342798`). |
| `token` | String | Yes | Must match the `build_callback_token` configured in settings. |
| `apk_file` | File | No | The compiled APK file (only sent on `completed` status). |
| `aab_file` | File | No | The compiled AAB file (only sent on `completed` status). |
| `build_log` | String | No | Failure logs or error outputs (only sent on `failed` status). |

### Responses

#### 200 OK (Success Callback)
```json
{
  "message": "Build status updated to completed.",
  "apk_url": "http://localhost/storage/apps/com.example.myapp/builds/apk/icon.apk",
  "aab_url": "http://localhost/storage/apps/com.example.myapp/builds/aab/icon.aab"
}
```

#### 401 Unauthorized (Token Mismatch)
```json
{
  "error": "Unauthorized token"
}
```

#### 422 Unprocessable Entity (Validation Error)
```json
{
  "error": {
    "build_id": ["The selected build id is invalid."]
  }
}
```

---

## 5. Deployment Guide (VPS/Production)

When deploying to a live VPS:

1. **Queue Configuration:**
   Configure a process supervisor like **Supervisor** to keep the queue worker running continuously in the background:
   ```ini
   [program:laravel-worker]
   process_name=%(program_name)s_%(process_num)02d
   command=php /var/www/website-to-app-builder/artisan queue:work --sleep=3 --tries=3 --max-time=3600
   autostart=true
   autorestart=true
   stopasgroup=true
   killasgroup=true
   user=www-data
   numprocs=2
   redirect_stderr=true
   stdout_logfile=/var/www/website-to-app-builder/storage/logs/worker.log
   stopwaitsecs=3600
   ```

2. **Cron Scheduler:**
   Add the Laravel Scheduler cron entry to your server's crontab:
   ```cron
   * * * * * cd /var/www/website-to-app-builder && php artisan schedule:run >> /dev/null 2>&1
   ```

3. **Symlink Storage:**
   Ensure the storage folder is linked so built files or icons stored locally can be fetched publicly by GitHub runners or users:
   ```bash
   php artisan storage:link
   ```
