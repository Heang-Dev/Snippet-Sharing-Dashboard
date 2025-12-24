# Snippet Sharing Dashboard - Setup Guide

This guide will help you set up the project on your local machine.

## Prerequisites

Before you begin, make sure you have the following installed:

- **PHP 8.2+** - [Download PHP](https://www.php.net/downloads)
- **Composer** - [Download Composer](https://getcomposer.org/download/)
- **Node.js 18+** - [Download Node.js](https://nodejs.org/)
- **npm** (comes with Node.js)
- **MySQL 8.0+** or **MariaDB 10.4+**
- **Git** - [Download Git](https://git-scm.com/downloads)

### Optional (Recommended)

- **Laravel Valet** (macOS/Linux) - For easy local development
- **Laravel Herd** (macOS/Windows) - Alternative to Valet

---

## Step 1: Accept Repository Invitation

1. Check your email for the GitHub repository invitation
2. Click the invitation link and accept access to the repository
3. You should now have access to: `https://github.com/[organization]/Snippet_Sharing_Dashboard`

---

## Step 2: Clone the Repository

```bash
# Clone the repository
git clone https://github.com/[organization]/Snippet_Sharing_Dashboard.git

# Navigate to the project directory
cd Snippet_Sharing_Dashboard
```

---

## Step 3: Install PHP Dependencies

```bash
composer install
```

This will install all Laravel and PHP dependencies defined in `composer.json`.

---

## Step 4: Install Node.js Dependencies

```bash
npm install
```

This will install all frontend dependencies including React, Tailwind CSS, and Shadcn/ui components.

---

## Step 5: Environment Setup

### Copy the environment file

```bash
cp .env.example .env
```

### Generate application key

```bash
php artisan key:generate
```

### Configure your `.env` file

Open `.env` in your editor and update the following:

#### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=snippet_sharing
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

#### Mail Configuration (for password reset OTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

> **Note:** For Gmail, you need to create an [App Password](https://support.google.com/accounts/answer/185833) if you have 2FA enabled.

#### Social Authentication (Optional)

If you want to enable Google/GitHub login:

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret
GITHUB_REDIRECT_URI="${APP_URL}/auth/github/callback"
```

---

## Step 6: Create the Database

Create a new MySQL database:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE snippet_sharing;
EXIT;
```

Or if using a GUI like phpMyAdmin, TablePlus, or DBeaver, create a database named `snippet_sharing`.

---

## Step 7: Run Database Migrations

```bash
php artisan migrate
```

This will create all the necessary database tables.

### (Optional) Seed the database with sample data

```bash
php artisan db:seed
```

---

## Step 8: Build Frontend Assets

### For development (with hot reload)

```bash
npm run dev
```

### For production

```bash
npm run build
```

---

## Step 9: Start the Development Server

### Option A: Using Laravel's built-in server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

### Option B: Using Laravel Valet (Recommended for macOS/Linux)

```bash
# Link the project (run from project directory)
valet link snippet-dashboard

# Visit: http://snippet-dashboard.test
```

### Option C: Using Laravel Herd (macOS/Windows)

Simply open Herd and add the project directory. It will automatically be available at `http://snippet-sharing-dashboard.test`

---

## Quick Setup (All Commands)

Here's a one-liner to run after cloning:

```bash
composer install && npm install && cp .env.example .env && php artisan key:generate
```

Then manually:
1. Configure `.env` with your database credentials
2. Create the database
3. Run migrations: `php artisan migrate`
4. Start dev server: `npm run dev` (in one terminal) and `php artisan serve` (in another)

---

## Development Workflow

### Running the app locally

You need **two terminal windows**:

**Terminal 1 - Frontend (Vite)**
```bash
npm run dev
```

**Terminal 2 - Backend (Laravel)**
```bash
php artisan serve
```

### Default Login Credentials

After seeding the database, you can login with:
- **Email:** `admin@example.com`
- **Password:** `password`

---

## Common Issues & Solutions

### Issue: "SQLSTATE[HY000] [1049] Unknown database"

**Solution:** Create the database first:
```bash
mysql -u root -p -e "CREATE DATABASE snippet_sharing;"
```

### Issue: "Vite manifest not found"

**Solution:** Build the frontend assets:
```bash
npm run build
```

### Issue: "Class not found" errors

**Solution:** Clear cache and regenerate autoload:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Issue: Permission denied on storage/logs

**Solution:** Fix storage permissions:
```bash
chmod -R 775 storage bootstrap/cache
```

### Issue: npm install fails

**Solution:** Clear npm cache and retry:
```bash
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

---

## Project Structure

```
Snippet_Sharing_Dashboard/
├── app/                    # Laravel application code
│   ├── Http/Controllers/   # Controllers
│   ├── Models/             # Eloquent models
│   └── Mail/               # Mailable classes
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── js/                 # React components & pages
│   │   ├── Components/     # Reusable components
│   │   ├── Layouts/        # Layout components
│   │   └── Pages/          # Inertia pages
│   └── views/              # Blade templates
├── routes/
│   └── web.php             # Web routes
├── public/                 # Public assets
└── .env.example            # Environment template
```

---

## Tech Stack

- **Backend:** Laravel 11, PHP 8.2+
- **Frontend:** React 18, Inertia.js
- **UI Components:** Shadcn/ui, Tailwind CSS 3.4
- **Database:** MySQL/MariaDB
- **Authentication:** Laravel built-in + Socialite (Google, GitHub)
- **Form Validation:** Zod + React Hook Form

---

## Need Help?

If you encounter any issues:

1. Check the [Common Issues](#common-issues--solutions) section above
2. Ask in the team Slack/Discord channel
3. Create an issue in the GitHub repository

---

## Useful Commands

| Command | Description |
|---------|-------------|
| `php artisan serve` | Start Laravel dev server |
| `npm run dev` | Start Vite dev server with HMR |
| `npm run build` | Build for production |
| `php artisan migrate` | Run database migrations |
| `php artisan migrate:fresh --seed` | Reset DB and seed |
| `php artisan tinker` | Laravel REPL |
| `php artisan route:list` | List all routes |
| `php artisan make:model Name -mrc` | Create model with migration, resource controller |
