# Snippet Sharing Dashboard - Setup Guide

Complete guide to set up the project on your local machine from scratch.

---

## Table of Contents

1. [Install Required Tools](#part-1-install-required-tools)
2. [Clone & Setup Project](#part-2-clone--setup-project)
3. [Running the Application](#part-3-running-the-application)
4. [Troubleshooting](#part-4-troubleshooting)

---
abcde

# Part 1: Install Required Tools

## 1.1 Install Git

Git is required to clone the repository.

### Windows

1. Download Git from: https://git-scm.com/download/win
2. Run the installer
3. Use default settings, click "Next" through all steps
4. Verify installation:
   ```bash
   git --version
   ```

### macOS

```bash
# Option 1: Using Homebrew (recommended)
brew install git

# Option 2: Using Xcode Command Line Tools
xcode-select --install
```

### Ubuntu/Debian Linux

```bash
sudo apt update
sudo apt install git -y
```

---

## 1.2 Install PHP 8.2+

### Windows

1. Download PHP from: https://windows.php.net/download/
   - Choose "VS16 x64 Thread Safe" zip file
2. Extract to `C:\php`
3. Add `C:\php` to your System PATH:
   - Search "Environment Variables" in Windows
   - Edit "Path" under System Variables
   - Add `C:\php`
4. Copy `php.ini-development` to `php.ini`
5. Edit `php.ini` and enable these extensions (remove the `;` at the start):
   ```ini
   extension=curl
   extension=fileinfo
   extension=mbstring
   extension=openssl
   extension=pdo_mysql
   extension=zip
   ```
6. Verify:
   ```bash
   php --version
   ```

### macOS

```bash
# Using Homebrew
brew install php@8.2

# Add to PATH (add this to ~/.zshrc or ~/.bash_profile)
echo 'export PATH="/opt/homebrew/opt/php@8.2/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# Verify
php --version
```

### Ubuntu/Debian Linux

```bash
# Add PHP repository
sudo apt update
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and required extensions
sudo apt install php8.2 php8.2-cli php8.2-common php8.2-curl php8.2-mbstring php8.2-mysql php8.2-xml php8.2-zip php8.2-bcmath -y

# Verify
php --version
```

---

## 1.3 Install Composer

Composer is PHP's package manager.

### Windows

1. Download Composer-Setup.exe from: https://getcomposer.org/download/
2. Run the installer
3. It will auto-detect your PHP installation
4. Verify:
   ```bash
   composer --version
   ```

### macOS

```bash
# Download and install
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Move to global location
sudo mv composer.phar /usr/local/bin/composer

# Verify
composer --version
```

### Ubuntu/Debian Linux

```bash
# Download and install
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Move to global location
sudo mv composer.phar /usr/local/bin/composer

# Verify
composer --version
```

---

## 1.4 Install Node.js & npm

Node.js is required for frontend build tools. npm comes bundled with Node.js.

### Windows

1. Download from: https://nodejs.org/ (Choose LTS version, e.g., 20.x)
2. Run the installer
3. Check "Automatically install necessary tools" if prompted
4. Verify:
   ```bash
   node --version
   npm --version
   ```

### macOS

```bash
# Using Homebrew (recommended)
brew install node@20

# Or download from https://nodejs.org/

# Verify
node --version
npm --version
```

### Ubuntu/Debian Linux

```bash
# Install using NodeSource
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y

# Verify
node --version
npm --version
```

---

## 1.5 Install MySQL

### Windows

1. Download MySQL Installer from: https://dev.mysql.com/downloads/installer/
2. Choose "MySQL Server" and "MySQL Workbench" (optional GUI)
3. Set root password during installation (remember this!)
4. Verify:
   ```bash
   mysql --version
   ```

### macOS

```bash
# Using Homebrew
brew install mysql

# Start MySQL service
brew services start mysql

# Secure installation (set root password)
mysql_secure_installation

# Verify
mysql --version
```

### Ubuntu/Debian Linux

```bash
# Install MySQL
sudo apt install mysql-server -y

# Start service
sudo systemctl start mysql
sudo systemctl enable mysql

# Secure installation (set root password)
sudo mysql_secure_installation

# Verify
mysql --version
```

---

## 1.6 (Optional) Install Laravel Valet

Valet provides a fast local development environment. **Recommended for macOS/Linux.**

### macOS

```bash
# Install Valet via Composer
composer global require laravel/valet

# Add Composer global bin to PATH (add to ~/.zshrc)
echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# Install and start Valet
valet install

# Verify
valet --version
```

### Linux (Ubuntu/Debian)

```bash
# Install required packages
sudo apt install network-manager libnss3-tools jq xsel -y

# Install Valet Linux via Composer
composer global require cpriego/valet-linux

# Add Composer global bin to PATH (add to ~/.bashrc)
echo 'export PATH="$HOME/.config/composer/vendor/bin:$PATH"' >> ~/.bashrc
source ~/.bashrc

# Install Valet
valet install

# Verify
valet --version
```

---

## 1.7 Verify All Installations

Run these commands to verify everything is installed:

```bash
git --version      # Should show: git version 2.x.x
php --version      # Should show: PHP 8.2.x or higher
composer --version # Should show: Composer version 2.x.x
node --version     # Should show: v20.x.x or higher
npm --version      # Should show: 10.x.x or higher
mysql --version    # Should show: mysql Ver 8.x.x
```

---

# Part 2: Clone & Setup Project

## Step 1: Accept Repository Invitation

1. Check your email for the GitHub repository invitation
2. Click the invitation link and accept access
3. You should now have access to the repository

---

## Step 2: Clone the Repository

```bash
# Navigate to where you want the project
cd ~/Projects  # or any folder you prefer

# Clone the repository
git clone https://github.com/[organization]/Snippet_Sharing_Dashboard.git

# Navigate into the project
cd Snippet_Sharing_Dashboard
```

---

## Step 3: Install PHP Dependencies

```bash
composer install
```

This installs all Laravel packages defined in `composer.json`.

---

## Step 4: Install Node.js Dependencies

```bash
npm install
```

This installs React, Tailwind CSS, and all frontend packages.

---

## Step 5: Environment Configuration

### Copy environment file

```bash
cp .env.example .env
```

### Generate application key

```bash
php artisan key:generate
```

### Edit `.env` file

Open `.env` in your code editor and configure:

#### Application Settings

```env
APP_NAME="Snippet Sharing"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

> If using Valet, change `APP_URL` to your Valet domain, e.g., `http://snippet-dashboard.test`

#### Database Configuration

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=snippet_sharing
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

#### Mail Configuration (Required for Password Reset)

**Using Gmail:**

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

> **Gmail App Password:** If you have 2FA enabled, create an App Password:
> 1. Go to Google Account > Security > 2-Step Verification > App passwords
> 2. Generate a new app password for "Mail"
> 3. Use this 16-character password in `MAIL_PASSWORD`

**Using Mailtrap (for testing):**

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
```

#### Social Authentication (Optional)

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"

GITHUB_CLIENT_ID=your_github_client_id
GITHUB_CLIENT_SECRET=your_github_client_secret
GITHUB_REDIRECT_URI="${APP_URL}/auth/github/callback"
```

---

## Step 6: Create Database

### Using MySQL Command Line

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE snippet_sharing;

# Exit
EXIT;
```

### Using MySQL Workbench or phpMyAdmin

1. Open your MySQL GUI tool
2. Create a new database named `snippet_sharing`
3. Use default charset `utf8mb4`

---

## Step 7: Run Database Migrations

```bash
php artisan migrate
```

This creates all necessary database tables.

### (Optional) Seed with Sample Data

```bash
php artisan db:seed
```

---

## Step 8: Build Frontend Assets

```bash
npm run build
```

---

# Part 3: Running the Application

## Option A: Using PHP Built-in Server (Simple)

Open **two terminal windows**:

**Terminal 1 - Start Vite (Frontend)**
```bash
npm run dev
```

**Terminal 2 - Start Laravel (Backend)**
```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## Option B: Using Laravel Valet (Recommended)

```bash
# Link the project (run from project directory)
valet link snippet-dashboard

# Start Vite in watch mode
npm run dev
```

Visit: **http://snippet-dashboard.test**

---

## Default Login Credentials

After running seeders:

| Field    | Value               |
|----------|---------------------|
| Email    | admin@example.com   |
| Password | password            |

---

# Part 4: Troubleshooting

## Common Issues

### "SQLSTATE[HY000] [1049] Unknown database"

**Solution:** Create the database first

```bash
mysql -u root -p -e "CREATE DATABASE snippet_sharing;"
```

---

### "Vite manifest not found"

**Solution:** Build frontend assets

```bash
npm run build
```

---

### "Class not found" errors

**Solution:** Regenerate autoload files

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

### Permission denied on storage/logs

**Solution:** Fix permissions

```bash
# Linux/macOS
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

---

### npm install fails

**Solution:** Clear cache and retry

```bash
rm -rf node_modules package-lock.json
npm cache clean --force
npm install
```

---

### MySQL "Access denied for user 'root'@'localhost'"

**Solution:** Reset MySQL password or check credentials

```bash
# Linux - Login without password first
sudo mysql

# Then set password
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'new_password';
FLUSH PRIVILEGES;
EXIT;
```

---

### Port 8000 already in use

**Solution:** Use a different port

```bash
php artisan serve --port=8080
```

---

## Useful Commands Reference

| Command | Description |
|---------|-------------|
| `composer install` | Install PHP dependencies |
| `npm install` | Install Node.js dependencies |
| `npm run dev` | Start Vite dev server (hot reload) |
| `npm run build` | Build for production |
| `php artisan serve` | Start Laravel dev server |
| `php artisan migrate` | Run database migrations |
| `php artisan migrate:fresh` | Drop all tables and re-migrate |
| `php artisan migrate:fresh --seed` | Fresh migrate + seed data |
| `php artisan db:seed` | Run database seeders |
| `php artisan tinker` | Laravel REPL for testing |
| `php artisan route:list` | List all routes |
| `php artisan config:clear` | Clear config cache |
| `php artisan cache:clear` | Clear application cache |

---

## Project Structure

```
Snippet_Sharing_Dashboard/
├── app/
│   ├── Http/Controllers/    # Controllers
│   ├── Models/              # Eloquent models
│   └── Mail/                # Mail classes
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Database seeders
├── resources/
│   ├── js/
│   │   ├── Components/      # React components
│   │   ├── Layouts/         # Layout components
│   │   └── Pages/           # Inertia pages
│   └── css/                 # Stylesheets
├── routes/
│   └── web.php              # Web routes
├── public/                  # Public assets
├── .env.example             # Environment template
└── SETUP.md                 # This file
```

---

## Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| Laravel | 11.x | Backend framework |
| PHP | 8.2+ | Server-side language |
| React | 18.x | Frontend library |
| Inertia.js | 1.x | SPA bridge |
| Tailwind CSS | 3.4.x | Styling |
| Shadcn/ui | - | UI components |
| MySQL | 8.x | Database |
| Vite | 5.x | Build tool |

---

## Need Help?

1. Check the [Troubleshooting](#part-4-troubleshooting) section
2. Ask in the team Slack/Discord channel
3. Create an issue in the GitHub repository
