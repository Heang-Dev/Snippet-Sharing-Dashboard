# Dashboard Implementation Plan

> **Project:** Code Snippet Sharing Platform - Dashboard
> **Tech Stack:** Laravel 11 + Inertia.js + React 18 + Shadcn/ui + Tailwind CSS
> **Created:** December 23, 2025

---

## Table of Contents

1. [Implementation Overview](#1-implementation-overview)
2. [Project Setup](#2-project-setup)
3. [Database Schema](#3-database-schema)
4. [Backend Implementation](#4-backend-implementation)
5. [Frontend Implementation](#5-frontend-implementation)
6. [API Implementation](#6-api-implementation)
7. [Feature Implementation Order](#7-feature-implementation-order)
8. [Testing Strategy](#8-testing-strategy)
9. [Deployment](#9-deployment)

---

## 1. Implementation Overview

### 1.1 What We're Building

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         DASHBOARD APPLICATION                            │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│   ┌─────────────────────────────────────────────────────────────────┐  │
│   │                     WEB INTERFACE (Inertia)                      │  │
│   │   ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │  │
│   │   │    Auth     │ │  Dashboard  │ │   Admin     │              │  │
│   │   │   Pages     │ │    Pages    │ │   Pages     │              │  │
│   │   └─────────────┘ └─────────────┘ └─────────────┘              │  │
│   │           React + Shadcn/ui + Tailwind CSS                       │  │
│   └─────────────────────────────────────────────────────────────────┘  │
│                                    │                                    │
│                                    ▼                                    │
│   ┌─────────────────────────────────────────────────────────────────┐  │
│   │                      LARAVEL BACKEND                             │  │
│   │   ┌─────────────┐ ┌─────────────┐ ┌─────────────┐              │  │
│   │   │ Controllers │ │  Services   │ │   Models    │              │  │
│   │   └─────────────┘ └─────────────┘ └─────────────┘              │  │
│   │                                                                  │  │
│   │   ┌─────────────────────────────────────────────────────────┐   │  │
│   │   │                    REST API (Sanctum)                    │   │  │
│   │   │              For Mobile App Consumption                  │   │  │
│   │   └─────────────────────────────────────────────────────────┘   │  │
│   └─────────────────────────────────────────────────────────────────┘  │
│                                    │                                    │
│                                    ▼                                    │
│   ┌──────────────────┐  ┌──────────────────┐  ┌──────────────────┐   │
│   │   PostgreSQL     │  │  Google Cloud    │  │    Pygments      │   │
│   │    Database      │  │    Storage       │  │    Service       │   │
│   └──────────────────┘  └──────────────────┘  └──────────────────┘   │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Key Features

| Feature | Web Dashboard | REST API |
|---------|--------------|----------|
| User Authentication | ✅ Session-based | ✅ Token-based (Sanctum) |
| Snippet CRUD | ✅ Full UI | ✅ Complete endpoints |
| Team Management | ✅ Full UI | ✅ Complete endpoints |
| Syntax Highlighting | ✅ Real-time preview | ✅ Pygments integration |
| Version History | ✅ Diff viewer | ✅ Version endpoints |
| Search | ✅ Full-text search | ✅ Search API |
| Reports | ✅ Charts & analytics | ✅ Report endpoints |
| Admin Panel | ✅ User/Content mgmt | ✅ Admin endpoints |
| Audit Logs | ✅ Activity viewer | ✅ Log endpoints |

### 1.3 Directory Structure

```
Snippet_Sharing_Dashboard/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                    # API Controllers (for mobile)
│   │   │   │   ├── V1/
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   ├── SnippetController.php
│   │   │   │   │   ├── TeamController.php
│   │   │   │   │   ├── TagController.php
│   │   │   │   │   ├── CategoryController.php
│   │   │   │   │   ├── SearchController.php
│   │   │   │   │   ├── UserController.php
│   │   │   │   │   ├── NotificationController.php
│   │   │   │   │   ├── ReportController.php
│   │   │   │   │   └── Admin/
│   │   │   │   │       ├── AuditLogController.php
│   │   │   │   │       └── UserManagementController.php
│   │   │   │   └── HighlightController.php
│   │   │   ├── Web/                    # Web Controllers (Inertia)
│   │   │   │   ├── Auth/
│   │   │   │   │   ├── LoginController.php
│   │   │   │   │   ├── RegisterController.php
│   │   │   │   │   └── PasswordResetController.php
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── SnippetController.php
│   │   │   │   ├── TeamController.php
│   │   │   │   ├── ProfileController.php
│   │   │   │   ├── SettingsController.php
│   │   │   │   ├── CollectionController.php
│   │   │   │   ├── SearchController.php
│   │   │   │   └── Admin/
│   │   │   │       ├── AdminDashboardController.php
│   │   │   │       ├── UserController.php
│   │   │   │       └── AuditLogController.php
│   │   │   └── Controller.php
│   │   ├── Middleware/
│   │   │   ├── HandleInertiaRequests.php
│   │   │   ├── EnsureEmailIsVerified.php
│   │   │   └── IsAdmin.php
│   │   ├── Requests/                   # Form Requests (Validation)
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Snippet/
│   │   │   │   ├── StoreSnippetRequest.php
│   │   │   │   └── UpdateSnippetRequest.php
│   │   │   └── Team/
│   │   │       ├── StoreTeamRequest.php
│   │   │       └── InviteMemberRequest.php
│   │   └── Resources/                  # API Resources (Transformers)
│   │       ├── UserResource.php
│   │       ├── SnippetResource.php
│   │       ├── SnippetCollection.php
│   │       ├── TeamResource.php
│   │       └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── Snippet.php
│   │   ├── SnippetVersion.php
│   │   ├── SnippetFile.php
│   │   ├── Team.php
│   │   ├── TeamMember.php
│   │   ├── TeamInvitation.php
│   │   ├── Tag.php
│   │   ├── Category.php
│   │   ├── Language.php
│   │   ├── Favorite.php
│   │   ├── Comment.php
│   │   ├── Follow.php
│   │   ├── Share.php
│   │   ├── Collection.php
│   │   ├── CollectionSnippet.php
│   │   ├── SnippetView.php
│   │   ├── Notification.php
│   │   ├── AuditLog.php
│   │   └── ApiKey.php
│   ├── Services/
│   │   ├── PygmentsService.php         # Syntax highlighting
│   │   ├── SnippetService.php          # Snippet business logic
│   │   ├── TeamService.php             # Team business logic
│   │   ├── SearchService.php           # Search functionality
│   │   ├── VersioningService.php       # Version management
│   │   ├── NotificationService.php     # Notifications
│   │   ├── AuditLogService.php         # Audit logging
│   │   └── StorageService.php          # File uploads
│   ├── Observers/
│   │   ├── SnippetObserver.php         # Auto-versioning, audit
│   │   ├── UserObserver.php            # Audit on user changes
│   │   └── TeamObserver.php            # Audit on team changes
│   ├── Events/
│   │   ├── SnippetCreated.php
│   │   ├── SnippetViewed.php
│   │   └── TeamMemberInvited.php
│   ├── Listeners/
│   │   ├── SendSnippetNotification.php
│   │   ├── RecordSnippetView.php
│   │   └── SendTeamInvitationEmail.php
│   ├── Policies/
│   │   ├── SnippetPolicy.php           # Authorization
│   │   ├── TeamPolicy.php
│   │   └── CommentPolicy.php
│   └── Traits/
│       ├── HasAuditLog.php
│       └── HasSlug.php
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_01_01_000001_create_languages_table.php
│   │   ├── 2024_01_01_000002_create_categories_table.php
│   │   ├── 2024_01_01_000003_create_teams_table.php
│   │   ├── 2024_01_01_000004_create_team_members_table.php
│   │   ├── 2024_01_01_000005_create_team_invitations_table.php
│   │   ├── 2024_01_01_000006_create_snippets_table.php
│   │   ├── 2024_01_01_000007_create_snippet_versions_table.php
│   │   ├── 2024_01_01_000008_create_snippet_files_table.php
│   │   ├── 2024_01_01_000009_create_tags_table.php
│   │   ├── 2024_01_01_000010_create_snippet_tag_table.php
│   │   ├── 2024_01_01_000011_create_favorites_table.php
│   │   ├── 2024_01_01_000012_create_comments_table.php
│   │   ├── 2024_01_01_000013_create_follows_table.php
│   │   ├── 2024_01_01_000014_create_shares_table.php
│   │   ├── 2024_01_01_000015_create_collections_table.php
│   │   ├── 2024_01_01_000016_create_collection_snippet_table.php
│   │   ├── 2024_01_01_000017_create_snippet_views_table.php
│   │   ├── 2024_01_01_000018_create_notifications_table.php
│   │   ├── 2024_01_01_000019_create_audit_logs_table.php
│   │   └── 2024_01_01_000020_create_api_keys_table.php
│   ├── seeders/
│   │   ├── DatabaseSeeder.php
│   │   ├── LanguageSeeder.php          # 100+ languages
│   │   ├── CategorySeeder.php
│   │   ├── UserSeeder.php
│   │   ├── SnippetSeeder.php
│   │   └── TagSeeder.php
│   └── factories/
│       ├── UserFactory.php
│       ├── SnippetFactory.php
│       ├── TeamFactory.php
│       └── ...
├── resources/
│   ├── js/
│   │   ├── app.jsx                     # React entry point
│   │   ├── bootstrap.js
│   │   ├── Components/                 # Reusable components
│   │   │   ├── ui/                     # Shadcn components
│   │   │   │   ├── button.jsx
│   │   │   │   ├── input.jsx
│   │   │   │   ├── dialog.jsx
│   │   │   │   ├── dropdown-menu.jsx
│   │   │   │   ├── card.jsx
│   │   │   │   ├── avatar.jsx
│   │   │   │   ├── badge.jsx
│   │   │   │   ├── tabs.jsx
│   │   │   │   ├── toast.jsx
│   │   │   │   └── ...
│   │   │   ├── CodeEditor.jsx          # Monaco/CodeMirror wrapper
│   │   │   ├── CodePreview.jsx         # Syntax highlighted display
│   │   │   ├── SnippetCard.jsx
│   │   │   ├── TeamCard.jsx
│   │   │   ├── UserAvatar.jsx
│   │   │   ├── LanguageSelector.jsx
│   │   │   ├── TagInput.jsx
│   │   │   ├── PrivacySelector.jsx
│   │   │   ├── SearchBar.jsx
│   │   │   ├── Pagination.jsx
│   │   │   ├── EmptyState.jsx
│   │   │   ├── LoadingSpinner.jsx
│   │   │   └── ConfirmDialog.jsx
│   │   ├── Layouts/
│   │   │   ├── AuthLayout.jsx          # Login/Register layout
│   │   │   ├── AppLayout.jsx           # Main app layout with sidebar
│   │   │   ├── AdminLayout.jsx         # Admin panel layout
│   │   │   └── GuestLayout.jsx         # Public pages layout
│   │   ├── Pages/
│   │   │   ├── Auth/
│   │   │   │   ├── Login.jsx
│   │   │   │   ├── Register.jsx
│   │   │   │   ├── ForgotPassword.jsx
│   │   │   │   ├── ResetPassword.jsx
│   │   │   │   └── VerifyEmail.jsx
│   │   │   ├── Dashboard/
│   │   │   │   ├── Index.jsx           # Main dashboard
│   │   │   │   └── MySnippets.jsx
│   │   │   ├── Snippets/
│   │   │   │   ├── Index.jsx           # Browse snippets
│   │   │   │   ├── Show.jsx            # View snippet
│   │   │   │   ├── Create.jsx          # Create snippet
│   │   │   │   ├── Edit.jsx            # Edit snippet
│   │   │   │   ├── History.jsx         # Version history
│   │   │   │   └── Compare.jsx         # Diff view
│   │   │   ├── Teams/
│   │   │   │   ├── Index.jsx           # Teams list
│   │   │   │   ├── Show.jsx            # Team dashboard
│   │   │   │   ├── Create.jsx          # Create team
│   │   │   │   ├── Settings.jsx        # Team settings
│   │   │   │   ├── Members.jsx         # Manage members
│   │   │   │   └── Snippets.jsx        # Team snippets
│   │   │   ├── Profile/
│   │   │   │   ├── Show.jsx            # Public profile
│   │   │   │   └── Edit.jsx            # Edit profile
│   │   │   ├── Settings/
│   │   │   │   ├── Profile.jsx
│   │   │   │   ├── Account.jsx
│   │   │   │   ├── Notifications.jsx
│   │   │   │   ├── Privacy.jsx
│   │   │   │   └── ApiKeys.jsx
│   │   │   ├── Search/
│   │   │   │   └── Index.jsx
│   │   │   ├── Browse/
│   │   │   │   ├── Languages.jsx
│   │   │   │   ├── Categories.jsx
│   │   │   │   ├── Tags.jsx
│   │   │   │   └── Trending.jsx
│   │   │   ├── Collections/
│   │   │   │   ├── Index.jsx
│   │   │   │   ├── Show.jsx
│   │   │   │   └── Create.jsx
│   │   │   ├── Favorites/
│   │   │   │   └── Index.jsx
│   │   │   ├── Notifications/
│   │   │   │   └── Index.jsx
│   │   │   ├── Admin/
│   │   │   │   ├── Dashboard.jsx
│   │   │   │   ├── Users/
│   │   │   │   │   ├── Index.jsx
│   │   │   │   │   └── Show.jsx
│   │   │   │   ├── AuditLogs.jsx
│   │   │   │   ├── Reports.jsx
│   │   │   │   └── Moderation.jsx
│   │   │   └── Errors/
│   │   │       ├── 404.jsx
│   │   │       └── 500.jsx
│   │   ├── hooks/                      # Custom React hooks
│   │   │   ├── useDebounce.js
│   │   │   ├── useLocalStorage.js
│   │   │   └── useTheme.js
│   │   └── lib/
│   │       ├── utils.js                # Shadcn utilities
│   │       └── axios.js                # API client
│   ├── css/
│   │   └── app.css                     # Tailwind + custom styles
│   └── views/
│       └── app.blade.php               # Single Blade template for Inertia
├── routes/
│   ├── web.php                         # Inertia routes
│   ├── api.php                         # API routes (v1)
│   └── auth.php                        # Authentication routes
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── inertia.php
│   ├── sanctum.php
│   ├── pygments.php                    # Custom: Pygments config
│   └── snippets.php                    # Custom: App settings
├── tests/
│   ├── Feature/
│   │   ├── Api/
│   │   │   ├── AuthTest.php
│   │   │   ├── SnippetTest.php
│   │   │   └── TeamTest.php
│   │   └── Web/
│   │       ├── DashboardTest.php
│   │       └── SnippetTest.php
│   └── Unit/
│       ├── Services/
│       │   ├── PygmentsServiceTest.php
│       │   └── SnippetServiceTest.php
│       └── Models/
│           ├── SnippetTest.php
│           └── TeamTest.php
├── .env.example
├── composer.json
├── package.json
├── tailwind.config.js
├── vite.config.js
├── jsconfig.json
└── components.json                      # Shadcn config
```

---

## 2. Project Setup

### 2.1 Step-by-Step Setup Commands

```bash
# ============================================================
# STEP 1: Create Laravel Project
# ============================================================

cd /home/mengheang/Desktop/Project/Snippet_Sharing_Platform__

# Remove empty dashboard folder
rm -rf Snippet_Sharing_Dashboard

# Create new Laravel project
composer create-project laravel/laravel Snippet_Sharing_Dashboard

cd Snippet_Sharing_Dashboard

# ============================================================
# STEP 2: Install Backend Dependencies
# ============================================================

# Sanctum for API authentication
composer require laravel/sanctum

# Inertia.js server-side adapter
composer require inertiajs/inertia-laravel

# Spatie packages for common features
composer require spatie/laravel-sluggable        # Auto slug generation
composer require spatie/laravel-activitylog     # Audit logging (optional)
composer require spatie/laravel-permission      # Roles & permissions

# Image handling
composer require intervention/image

# UUID support
composer require ramsey/uuid

# IDE Helper (development)
composer require --dev barryvdh/laravel-ide-helper

# ============================================================
# STEP 3: Install Frontend Dependencies
# ============================================================

# Core React + Inertia
npm install @inertiajs/react react react-dom

# TypeScript support (optional but recommended)
npm install -D typescript @types/react @types/react-dom

# Tailwind CSS
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p

# Shadcn/ui dependencies
npm install tailwindcss-animate class-variance-authority clsx tailwind-merge
npm install lucide-react
npm install @radix-ui/react-avatar
npm install @radix-ui/react-dialog
npm install @radix-ui/react-dropdown-menu
npm install @radix-ui/react-label
npm install @radix-ui/react-popover
npm install @radix-ui/react-select
npm install @radix-ui/react-separator
npm install @radix-ui/react-slot
npm install @radix-ui/react-tabs
npm install @radix-ui/react-toast
npm install @radix-ui/react-tooltip
npm install @radix-ui/react-switch
npm install @radix-ui/react-checkbox
npm install @radix-ui/react-radio-group
npm install @radix-ui/react-scroll-area
npm install @radix-ui/react-alert-dialog
npm install @radix-ui/react-progress
npm install @radix-ui/react-collapsible

# Code editor
npm install @monaco-editor/react
# OR
npm install @uiw/react-codemirror

# Syntax highlighting for display
npm install react-syntax-highlighter

# Form handling
npm install react-hook-form @hookform/resolvers zod

# Date handling
npm install date-fns

# Charts (for analytics)
npm install recharts

# Diff viewer (for version comparison)
npm install react-diff-viewer-continued

# ============================================================
# STEP 4: Publish Configs & Setup
# ============================================================

# Publish Sanctum config
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Publish Inertia middleware
php artisan inertia:middleware

# Create storage link
php artisan storage:link

# Generate app key
php artisan key:generate

# ============================================================
# STEP 5: Initialize Shadcn/ui
# ============================================================

# Initialize shadcn
npx shadcn@latest init

# Add components (select the ones we need)
npx shadcn@latest add button
npx shadcn@latest add input
npx shadcn@latest add label
npx shadcn@latest add card
npx shadcn@latest add dialog
npx shadcn@latest add dropdown-menu
npx shadcn@latest add avatar
npx shadcn@latest add badge
npx shadcn@latest add tabs
npx shadcn@latest add toast
npx shadcn@latest add tooltip
npx shadcn@latest add select
npx shadcn@latest add textarea
npx shadcn@latest add checkbox
npx shadcn@latest add switch
npx shadcn@latest add separator
npx shadcn@latest add scroll-area
npx shadcn@latest add alert-dialog
npx shadcn@latest add popover
npx shadcn@latest add command
npx shadcn@latest add table
npx shadcn@latest add skeleton
npx shadcn@latest add progress
npx shadcn@latest add sheet
npx shadcn@latest add form
npx shadcn@latest add sonner
```

### 2.2 Configuration Files

#### tailwind.config.js
```javascript
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ["class"],
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.{js,jsx,ts,tsx}",
    ],
    theme: {
        container: {
            center: true,
            padding: "2rem",
            screens: {
                "2xl": "1400px",
            },
        },
        extend: {
            colors: {
                border: "hsl(var(--border))",
                input: "hsl(var(--input))",
                ring: "hsl(var(--ring))",
                background: "hsl(var(--background))",
                foreground: "hsl(var(--foreground))",
                primary: {
                    DEFAULT: "hsl(var(--primary))",
                    foreground: "hsl(var(--primary-foreground))",
                },
                secondary: {
                    DEFAULT: "hsl(var(--secondary))",
                    foreground: "hsl(var(--secondary-foreground))",
                },
                destructive: {
                    DEFAULT: "hsl(var(--destructive))",
                    foreground: "hsl(var(--destructive-foreground))",
                },
                muted: {
                    DEFAULT: "hsl(var(--muted))",
                    foreground: "hsl(var(--muted-foreground))",
                },
                accent: {
                    DEFAULT: "hsl(var(--accent))",
                    foreground: "hsl(var(--accent-foreground))",
                },
                popover: {
                    DEFAULT: "hsl(var(--popover))",
                    foreground: "hsl(var(--popover-foreground))",
                },
                card: {
                    DEFAULT: "hsl(var(--card))",
                    foreground: "hsl(var(--card-foreground))",
                },
            },
            borderRadius: {
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },
            fontFamily: {
                sans: ["Inter", "sans-serif"],
                mono: ["JetBrains Mono", "Fira Code", "monospace"],
            },
            keyframes: {
                "accordion-down": {
                    from: { height: 0 },
                    to: { height: "var(--radix-accordion-content-height)" },
                },
                "accordion-up": {
                    from: { height: "var(--radix-accordion-content-height)" },
                    to: { height: 0 },
                },
            },
            animation: {
                "accordion-down": "accordion-down 0.2s ease-out",
                "accordion-up": "accordion-up 0.2s ease-out",
            },
        },
    },
    plugins: [require("tailwindcss-animate")],
}
```

#### vite.config.js
```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
});
```

#### jsconfig.json
```json
{
    "compilerOptions": {
        "baseUrl": ".",
        "paths": {
            "@/*": ["resources/js/*"]
        }
    },
    "exclude": ["node_modules", "public"]
}
```

#### components.json (Shadcn config)
```json
{
    "$schema": "https://ui.shadcn.com/schema.json",
    "style": "default",
    "rsc": false,
    "tsx": false,
    "tailwind": {
        "config": "tailwind.config.js",
        "css": "resources/css/app.css",
        "baseColor": "slate",
        "cssVariables": true,
        "prefix": ""
    },
    "aliases": {
        "components": "@/Components",
        "utils": "@/lib/utils",
        "ui": "@/Components/ui",
        "lib": "@/lib",
        "hooks": "@/hooks"
    }
}
```

### 2.3 Environment Variables (.env)

```env
APP_NAME="Snippet Sharing Platform"
APP_ENV=local
APP_KEY=base64:your-key-here
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Database (PostgreSQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=snippet_sharing
DB_USERNAME=postgres
DB_PASSWORD=your-password

# For development with SQLite (simpler)
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Queue (for background jobs)
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=database

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@snippetsharing.com"
MAIL_FROM_NAME="${APP_NAME}"

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173,127.0.0.1,127.0.0.1:8000

# Pygments Service
PYGMENTS_SERVICE_URL=http://localhost:5000
PYGMENTS_TIMEOUT=5

# Google Cloud Storage (optional, for production)
GOOGLE_CLOUD_PROJECT_ID=your-project-id
GOOGLE_CLOUD_STORAGE_BUCKET=snippet-sharing-assets
GOOGLE_APPLICATION_CREDENTIALS=/path/to/service-account.json

# File Storage (local for development)
FILESYSTEM_DISK=local
```

---

## 3. Database Schema

### 3.1 Migration Order

```
1.  users                  (Laravel default + customizations)
2.  password_reset_tokens  (Laravel default)
3.  sessions              (Laravel default)
4.  cache                 (Laravel default)
5.  jobs                  (Laravel default)
6.  languages             (Programming languages list)
7.  categories            (Snippet categories)
8.  teams                 (Team organizations)
9.  team_members          (User-Team relationship)
10. team_invitations      (Pending invitations)
11. snippets              (Core snippet data)
12. snippet_versions      (Version history)
13. snippet_files         (Multi-file snippets)
14. tags                  (Tag definitions)
15. snippet_tag           (Snippet-Tag pivot)
16. favorites             (User favorites)
17. comments              (Snippet comments)
18. follows               (User following)
19. shares                (Share tracking)
20. collections           (User collections)
21. collection_snippet    (Collection-Snippet pivot)
22. snippet_views         (View analytics)
23. notifications         (User notifications)
24. audit_logs            (System audit trail)
25. api_keys              (API access tokens)
26. personal_access_tokens (Sanctum tokens)
```

### 3.2 Key Migrations

#### users (modify default)
```php
Schema::create('users', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('username', 50)->unique();
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password')->nullable(); // Nullable for OAuth
    $table->string('full_name')->nullable();
    $table->text('bio')->nullable();
    $table->string('avatar_url', 500)->nullable();
    $table->string('location', 100)->nullable();
    $table->string('company', 100)->nullable();
    $table->string('github_url', 255)->nullable();
    $table->string('twitter_url', 255)->nullable();
    $table->string('website_url', 255)->nullable();
    $table->boolean('is_admin')->default(false);
    $table->boolean('is_active')->default(true);
    $table->enum('profile_visibility', ['public', 'private'])->default('public');
    $table->boolean('show_email')->default(false);
    $table->boolean('show_activity')->default(true);
    $table->enum('default_snippet_privacy', ['public', 'private', 'team'])->default('public');
    $table->enum('theme_preference', ['light', 'dark', 'system'])->default('system');
    $table->integer('snippets_count')->default(0);
    $table->integer('followers_count')->default(0);
    $table->integer('following_count')->default(0);
    $table->timestamp('last_login_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();

    $table->index('username');
    $table->index('email');
    $table->index('is_active');
});
```

#### languages
```php
Schema::create('languages', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name', 50)->unique();
    $table->string('slug', 60)->unique();
    $table->string('display_name', 100);
    $table->json('file_extensions'); // [".py", ".pyw"]
    $table->string('pygments_lexer', 100); // python3
    $table->string('monaco_language', 50)->nullable();
    $table->string('icon', 50)->nullable();
    $table->string('color', 7)->nullable(); // #3776AB
    $table->integer('snippet_count')->default(0);
    $table->integer('popularity_rank')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('slug');
    $table->index('snippet_count');
});
```

#### teams
```php
Schema::create('teams', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name', 100);
    $table->string('slug', 120)->unique();
    $table->text('description')->nullable();
    $table->string('avatar_url', 500)->nullable();
    $table->foreignUuid('owner_id')->constrained('users')->onDelete('cascade');
    $table->enum('privacy', ['public', 'private', 'invite_only'])->default('private');
    $table->integer('member_count')->default(1);
    $table->integer('snippet_count')->default(0);
    $table->boolean('allow_member_invite')->default(true);
    $table->enum('default_snippet_privacy', ['public', 'private', 'team'])->default('team');
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();

    $table->index('slug');
    $table->index('owner_id');
});
```

#### team_members
```php
Schema::create('team_members', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('team_id')->constrained()->onDelete('cascade');
    $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
    $table->enum('role', ['owner', 'admin', 'member', 'viewer'])->default('member');
    $table->boolean('can_create_snippets')->default(true);
    $table->boolean('can_edit_snippets')->default(false);
    $table->boolean('can_delete_snippets')->default(false);
    $table->boolean('can_manage_members')->default(false);
    $table->boolean('can_invite_members')->default(true);
    $table->foreignUuid('invited_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamp('joined_at')->useCurrent();
    $table->timestamps();

    $table->unique(['team_id', 'user_id']);
    $table->index('team_id');
    $table->index('user_id');
});
```

#### snippets
```php
Schema::create('snippets', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
    $table->foreignUuid('team_id')->nullable()->constrained()->nullOnDelete();
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->text('code');
    $table->text('highlighted_html')->nullable(); // Cached Pygments output
    $table->string('language', 50);
    $table->foreignUuid('category_id')->nullable()->constrained()->nullOnDelete();
    $table->enum('privacy', ['public', 'private', 'team', 'unlisted'])->default('public');
    $table->string('slug', 300)->unique();
    $table->integer('version_number')->default(1);
    $table->foreignUuid('parent_snippet_id')->nullable()->constrained('snippets')->nullOnDelete();
    $table->boolean('is_fork')->default(false);
    $table->boolean('is_featured')->default(false);
    $table->boolean('allow_comments')->default(true);
    $table->boolean('allow_forks')->default(true);
    $table->string('license', 50)->nullable();
    $table->integer('view_count')->default(0);
    $table->integer('unique_view_count')->default(0);
    $table->integer('fork_count')->default(0);
    $table->integer('favorite_count')->default(0);
    $table->integer('comment_count')->default(0);
    $table->integer('share_count')->default(0);
    $table->float('trending_score')->default(0);
    $table->timestamp('published_at')->nullable();
    $table->timestamps();
    $table->softDeletes();

    $table->index('user_id');
    $table->index('team_id');
    $table->index('language');
    $table->index('privacy');
    $table->index('slug');
    $table->index('created_at');
    $table->index('trending_score');
    $table->fullText(['title', 'description', 'code']); // PostgreSQL full-text
});
```

#### snippet_versions
```php
Schema::create('snippet_versions', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('snippet_id')->constrained()->onDelete('cascade');
    $table->integer('version_number');
    $table->string('title', 255);
    $table->text('description')->nullable();
    $table->text('code');
    $table->string('language', 50);
    $table->text('change_summary')->nullable();
    $table->enum('change_type', ['create', 'update', 'restore'])->default('update');
    $table->integer('lines_added')->default(0);
    $table->integer('lines_removed')->default(0);
    $table->foreignUuid('created_by')->constrained('users')->onDelete('cascade');
    $table->timestamps();

    $table->unique(['snippet_id', 'version_number']);
    $table->index('snippet_id');
});
```

#### tags
```php
Schema::create('tags', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('name', 50)->unique();
    $table->string('slug', 60)->unique();
    $table->text('description')->nullable();
    $table->string('color', 7)->nullable();
    $table->integer('usage_count')->default(0);
    $table->boolean('is_official')->default(false);
    $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
    $table->timestamps();

    $table->index('slug');
    $table->index('usage_count');
});
```

#### snippet_tag (pivot)
```php
Schema::create('snippet_tag', function (Blueprint $table) {
    $table->foreignUuid('snippet_id')->constrained()->onDelete('cascade');
    $table->foreignUuid('tag_id')->constrained()->onDelete('cascade');
    $table->timestamps();

    $table->primary(['snippet_id', 'tag_id']);
});
```

#### favorites
```php
Schema::create('favorites', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
    $table->foreignUuid('snippet_id')->constrained()->onDelete('cascade');
    $table->text('note')->nullable();
    $table->timestamps();

    $table->unique(['user_id', 'snippet_id']);
    $table->index('user_id');
    $table->index('snippet_id');
});
```

#### comments
```php
Schema::create('comments', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('snippet_id')->constrained()->onDelete('cascade');
    $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
    $table->foreignUuid('parent_comment_id')->nullable()->constrained('comments')->onDelete('cascade');
    $table->text('content');
    $table->integer('line_number')->nullable();
    $table->boolean('is_edited')->default(false);
    $table->timestamp('edited_at')->nullable();
    $table->integer('upvote_count')->default(0);
    $table->integer('reply_count')->default(0);
    $table->boolean('is_pinned')->default(false);
    $table->boolean('is_resolved')->default(false);
    $table->timestamps();
    $table->softDeletes();

    $table->index('snippet_id');
    $table->index('user_id');
    $table->index('parent_comment_id');
});
```

#### audit_logs
```php
Schema::create('audit_logs', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('action', 50); // create, update, delete, login, etc.
    $table->string('resource_type', 50); // snippet, user, team, etc.
    $table->uuid('resource_id')->nullable();
    $table->json('old_values')->nullable();
    $table->json('new_values')->nullable();
    $table->ipAddress('ip_address')->nullable();
    $table->text('user_agent')->nullable();
    $table->string('method', 10)->nullable(); // GET, POST, etc.
    $table->string('endpoint', 255)->nullable();
    $table->integer('status_code')->nullable();
    $table->text('error_message')->nullable();
    $table->json('metadata')->nullable();
    $table->timestamps();

    $table->index('user_id');
    $table->index('action');
    $table->index('resource_type');
    $table->index('created_at');
});
```

---

## 4. Backend Implementation

### 4.1 Models

#### User Model (app/Models/User.php)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'bio',
        'avatar_url',
        'location',
        'company',
        'github_url',
        'twitter_url',
        'website_url',
        'profile_visibility',
        'show_email',
        'show_activity',
        'default_snippet_privacy',
        'theme_preference',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'show_email' => 'boolean',
            'show_activity' => 'boolean',
        ];
    }

    // Relationships
    public function snippets()
    {
        return $this->hasMany(Snippet::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_members')
            ->withPivot('role', 'can_create_snippets', 'can_edit_snippets')
            ->withTimestamps();
    }

    public function ownedTeams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function favorites()
    {
        return $this->belongsToMany(Snippet::class, 'favorites')
            ->withTimestamps();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function isFollowing(User $user): bool
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function hasFavorited(Snippet $snippet): bool
    {
        return $this->favorites()->where('snippet_id', $snippet->id)->exists();
    }
}
```

#### Snippet Model (app/Models/Snippet.php)
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Snippet extends Model
{
    use HasFactory, HasUuids, SoftDeletes, HasSlug;

    protected $fillable = [
        'user_id',
        'team_id',
        'title',
        'description',
        'code',
        'highlighted_html',
        'language',
        'category_id',
        'privacy',
        'parent_snippet_id',
        'is_fork',
        'allow_comments',
        'allow_forks',
        'license',
    ];

    protected function casts(): array
    {
        return [
            'is_fork' => 'boolean',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'allow_forks' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(300);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function languageModel()
    {
        return $this->belongsTo(Language::class, 'language', 'slug');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function versions()
    {
        return $this->hasMany(SnippetVersion::class)->orderBy('version_number', 'desc');
    }

    public function latestVersion()
    {
        return $this->hasOne(SnippetVersion::class)->latestOfMany('version_number');
    }

    public function files()
    {
        return $this->hasMany(SnippetFile::class)->orderBy('order');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->whereNull('parent_comment_id');
    }

    public function allComments()
    {
        return $this->hasMany(Comment::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function views()
    {
        return $this->hasMany(SnippetView::class);
    }

    public function shares()
    {
        return $this->hasMany(Share::class);
    }

    public function parent()
    {
        return $this->belongsTo(Snippet::class, 'parent_snippet_id');
    }

    public function forks()
    {
        return $this->hasMany(Snippet::class, 'parent_snippet_id');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('privacy', 'public');
    }

    public function scopeVisibleTo($query, ?User $user)
    {
        if (!$user) {
            return $query->where('privacy', 'public');
        }

        return $query->where(function ($q) use ($user) {
            $q->where('privacy', 'public')
              ->orWhere('privacy', 'unlisted')
              ->orWhere('user_id', $user->id)
              ->orWhere(function ($q2) use ($user) {
                  $q2->where('privacy', 'team')
                     ->whereIn('team_id', $user->teams->pluck('id'));
              });
        });
    }

    public function scopeByLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    public function scopeTrending($query)
    {
        return $query->orderBy('trending_score', 'desc');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopePopular($query)
    {
        return $query->orderBy('view_count', 'desc');
    }

    // Helpers
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeViewedBy(?User $user): bool
    {
        if ($this->privacy === 'public' || $this->privacy === 'unlisted') {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($this->user_id === $user->id) {
            return true;
        }

        if ($this->privacy === 'team' && $this->team_id) {
            return $this->team->members->contains($user);
        }

        return false;
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        if ($this->team_id) {
            $member = $this->team->members()->where('user_id', $user->id)->first();
            return $member && ($member->pivot->can_edit_snippets || $member->pivot->role === 'owner');
        }

        return false;
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
```

### 4.2 Services

#### PygmentsService (app/Services/PygmentsService.php)
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PygmentsService
{
    protected string $serviceUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->serviceUrl = config('pygments.service_url', 'http://localhost:5000');
        $this->timeout = config('pygments.timeout', 5);
    }

    /**
     * Highlight code using Pygments service
     */
    public function highlight(string $code, string $language, string $theme = 'monokai'): array
    {
        $cacheKey = $this->getCacheKey($code, $language, $theme);

        return Cache::remember($cacheKey, now()->addDay(), function () use ($code, $language, $theme) {
            try {
                $response = Http::timeout($this->timeout)
                    ->post("{$this->serviceUrl}/highlight", [
                        'code' => $code,
                        'language' => $language,
                        'theme' => $theme,
                    ]);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'html' => $response->json('html'),
                        'css' => $response->json('css'),
                    ];
                }

                Log::warning('Pygments service error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->fallbackHighlight($code);

            } catch (\Exception $e) {
                Log::error('Pygments service exception', [
                    'error' => $e->getMessage(),
                ]);

                return $this->fallbackHighlight($code);
            }
        });
    }

    /**
     * Get list of supported languages
     */
    public function getLanguages(): array
    {
        return Cache::remember('pygments_languages', now()->addWeek(), function () {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->serviceUrl}/languages");

                if ($response->successful()) {
                    return $response->json('languages', []);
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch Pygments languages', ['error' => $e->getMessage()]);
            }

            return $this->getDefaultLanguages();
        });
    }

    /**
     * Fallback when Pygments service is unavailable
     */
    protected function fallbackHighlight(string $code): array
    {
        return [
            'success' => false,
            'html' => '<pre><code>' . htmlspecialchars($code) . '</code></pre>',
            'css' => '',
        ];
    }

    protected function getCacheKey(string $code, string $language, string $theme): string
    {
        return 'highlight_' . md5($code . $language . $theme);
    }

    protected function getDefaultLanguages(): array
    {
        return [
            'python', 'javascript', 'typescript', 'java', 'csharp',
            'php', 'ruby', 'go', 'rust', 'swift', 'kotlin', 'dart',
            'html', 'css', 'sql', 'bash', 'json', 'yaml', 'xml',
        ];
    }
}
```

#### SnippetService (app/Services/SnippetService.php)
```php
<?php

namespace App\Services;

use App\Models\Snippet;
use App\Models\SnippetVersion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SnippetService
{
    protected PygmentsService $pygmentsService;

    public function __construct(PygmentsService $pygmentsService)
    {
        $this->pygmentsService = $pygmentsService;
    }

    /**
     * Create a new snippet
     */
    public function create(array $data, User $user): Snippet
    {
        return DB::transaction(function () use ($data, $user) {
            // Highlight code
            $highlighted = $this->pygmentsService->highlight(
                $data['code'],
                $data['language']
            );

            // Create snippet
            $snippet = $user->snippets()->create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'code' => $data['code'],
                'highlighted_html' => $highlighted['html'] ?? null,
                'language' => $data['language'],
                'category_id' => $data['category_id'] ?? null,
                'team_id' => $data['team_id'] ?? null,
                'privacy' => $data['privacy'] ?? $user->default_snippet_privacy,
                'allow_comments' => $data['allow_comments'] ?? true,
                'allow_forks' => $data['allow_forks'] ?? true,
                'license' => $data['license'] ?? null,
                'published_at' => now(),
            ]);

            // Create initial version
            $this->createVersion($snippet, $user, 'create');

            // Attach tags
            if (!empty($data['tags'])) {
                $snippet->tags()->sync($data['tags']);
            }

            // Update counters
            $user->increment('snippets_count');
            if ($snippet->team_id) {
                $snippet->team->increment('snippet_count');
            }

            return $snippet->fresh(['user', 'tags', 'category']);
        });
    }

    /**
     * Update an existing snippet
     */
    public function update(Snippet $snippet, array $data, User $user): Snippet
    {
        return DB::transaction(function () use ($snippet, $data, $user) {
            // Check if code changed
            $codeChanged = isset($data['code']) && $data['code'] !== $snippet->code;

            // Highlight new code if changed
            if ($codeChanged) {
                $highlighted = $this->pygmentsService->highlight(
                    $data['code'],
                    $data['language'] ?? $snippet->language
                );
                $data['highlighted_html'] = $highlighted['html'] ?? null;
            }

            // Store old values for versioning
            if ($codeChanged || (isset($data['title']) && $data['title'] !== $snippet->title)) {
                $this->createVersion($snippet, $user, 'update');
                $data['version_number'] = $snippet->version_number + 1;
            }

            $snippet->update($data);

            // Update tags
            if (isset($data['tags'])) {
                $snippet->tags()->sync($data['tags']);
            }

            return $snippet->fresh(['user', 'tags', 'category']);
        });
    }

    /**
     * Fork a snippet
     */
    public function fork(Snippet $snippet, User $user, ?string $newTitle = null): Snippet
    {
        return DB::transaction(function () use ($snippet, $user, $newTitle) {
            $forked = $user->snippets()->create([
                'title' => $newTitle ?? $snippet->title . ' (Fork)',
                'description' => $snippet->description,
                'code' => $snippet->code,
                'highlighted_html' => $snippet->highlighted_html,
                'language' => $snippet->language,
                'category_id' => $snippet->category_id,
                'privacy' => 'private', // Forks start as private
                'parent_snippet_id' => $snippet->id,
                'is_fork' => true,
                'published_at' => now(),
            ]);

            // Copy tags
            $forked->tags()->sync($snippet->tags->pluck('id'));

            // Create initial version
            $this->createVersion($forked, $user, 'create');

            // Update counters
            $snippet->increment('fork_count');
            $user->increment('snippets_count');

            return $forked->fresh(['user', 'tags']);
        });
    }

    /**
     * Create a version snapshot
     */
    protected function createVersion(Snippet $snippet, User $user, string $changeType): SnippetVersion
    {
        return $snippet->versions()->create([
            'version_number' => $snippet->version_number,
            'title' => $snippet->title,
            'description' => $snippet->description,
            'code' => $snippet->code,
            'language' => $snippet->language,
            'change_type' => $changeType,
            'created_by' => $user->id,
        ]);
    }

    /**
     * Restore a previous version
     */
    public function restoreVersion(Snippet $snippet, SnippetVersion $version, User $user): Snippet
    {
        return DB::transaction(function () use ($snippet, $version, $user) {
            // Re-highlight the old code
            $highlighted = $this->pygmentsService->highlight(
                $version->code,
                $version->language
            );

            // Create new version before restoring
            $this->createVersion($snippet, $user, 'restore');

            // Update snippet with old values
            $snippet->update([
                'title' => $version->title,
                'description' => $version->description,
                'code' => $version->code,
                'highlighted_html' => $highlighted['html'] ?? null,
                'language' => $version->language,
                'version_number' => $snippet->version_number + 1,
            ]);

            return $snippet->fresh();
        });
    }

    /**
     * Record a view
     */
    public function recordView(Snippet $snippet, ?User $user, ?string $ipAddress = null): void
    {
        // Don't count owner's views
        if ($user && $snippet->user_id === $user->id) {
            return;
        }

        $snippet->views()->create([
            'user_id' => $user?->id,
            'ip_address' => $ipAddress,
            'viewed_at' => now(),
        ]);

        $snippet->increment('view_count');
    }
}
```

### 4.3 Controllers

#### API SnippetController (app/Http/Controllers/Api/V1/SnippetController.php)
```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Snippet\StoreSnippetRequest;
use App\Http\Requests\Snippet\UpdateSnippetRequest;
use App\Http\Resources\SnippetResource;
use App\Http\Resources\SnippetCollection;
use App\Models\Snippet;
use App\Services\SnippetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SnippetController extends Controller
{
    public function __construct(
        protected SnippetService $snippetService
    ) {}

    /**
     * List snippets with filters
     */
    public function index(Request $request): SnippetCollection
    {
        $query = Snippet::query()
            ->visibleTo($request->user())
            ->with(['user', 'tags', 'category']);

        // Apply filters
        if ($language = $request->get('language')) {
            $query->byLanguage($language);
        }

        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        if ($tags = $request->get('tags')) {
            $tagIds = explode(',', $tags);
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $tagIds));
        }

        if ($user = $request->get('user')) {
            $query->whereHas('user', fn($q) => $q->where('username', $user));
        }

        // Apply sorting
        $sort = $request->get('sort', 'recent');
        match ($sort) {
            'popular' => $query->popular(),
            'trending' => $query->trending(),
            default => $query->recent(),
        };

        $snippets = $query->paginate($request->get('per_page', 20));

        return new SnippetCollection($snippets);
    }

    /**
     * Create a new snippet
     */
    public function store(StoreSnippetRequest $request): JsonResponse
    {
        $snippet = $this->snippetService->create(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Snippet created successfully',
            'data' => new SnippetResource($snippet),
        ], 201);
    }

    /**
     * Get a single snippet
     */
    public function show(Request $request, Snippet $snippet): JsonResponse
    {
        if (!$snippet->canBeViewedBy($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet',
            ], 403);
        }

        // Record view
        $this->snippetService->recordView(
            $snippet,
            $request->user(),
            $request->ip()
        );

        $snippet->load(['user', 'tags', 'category', 'comments.user']);

        return response()->json([
            'success' => true,
            'data' => new SnippetResource($snippet),
        ]);
    }

    /**
     * Update a snippet
     */
    public function update(UpdateSnippetRequest $request, Snippet $snippet): JsonResponse
    {
        if (!$snippet->canBeEditedBy($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit this snippet',
            ], 403);
        }

        $snippet = $this->snippetService->update(
            $snippet,
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Snippet updated successfully',
            'data' => new SnippetResource($snippet),
        ]);
    }

    /**
     * Delete a snippet
     */
    public function destroy(Request $request, Snippet $snippet): JsonResponse
    {
        if (!$snippet->isOwnedBy($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this snippet',
            ], 403);
        }

        $snippet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Snippet deleted successfully',
        ]);
    }

    /**
     * Fork a snippet
     */
    public function fork(Request $request, Snippet $snippet): JsonResponse
    {
        if (!$snippet->allow_forks && !$snippet->isOwnedBy($request->user())) {
            return response()->json([
                'success' => false,
                'message' => 'This snippet does not allow forking',
            ], 403);
        }

        $forked = $this->snippetService->fork(
            $snippet,
            $request->user(),
            $request->get('title')
        );

        return response()->json([
            'success' => true,
            'message' => 'Snippet forked successfully',
            'data' => new SnippetResource($forked),
        ], 201);
    }

    /**
     * Toggle favorite
     */
    public function favorite(Request $request, Snippet $snippet): JsonResponse
    {
        $user = $request->user();

        if ($user->hasFavorited($snippet)) {
            $user->favorites()->detach($snippet->id);
            $snippet->decrement('favorite_count');
            $message = 'Snippet removed from favorites';
            $favorited = false;
        } else {
            $user->favorites()->attach($snippet->id);
            $snippet->increment('favorite_count');
            $message = 'Snippet added to favorites';
            $favorited = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => ['favorited' => $favorited],
        ]);
    }

    /**
     * Get version history
     */
    public function versions(Snippet $snippet): JsonResponse
    {
        $versions = $snippet->versions()
            ->with('creator:id,username,avatar_url')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $versions,
        ]);
    }
}
```

---

## 5. Frontend Implementation

### 5.1 App Entry Point (resources/js/app.jsx)
```jsx
import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Toaster } from '@/Components/ui/sonner';

const appName = import.meta.env.VITE_APP_NAME || 'Snippet Sharing';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx')
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <>
                <App {...props} />
                <Toaster position="top-right" />
            </>
        );
    },
    progress: {
        color: '#6366F1',
    },
});
```

### 5.2 Main Layout (resources/js/Layouts/AppLayout.jsx)
```jsx
import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Home,
    Code,
    Users,
    Search,
    Settings,
    Bell,
    Menu,
    X,
    Plus,
    User,
    LogOut,
    Moon,
    Sun,
} from 'lucide-react';
import { Button } from '@/Components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { cn } from '@/lib/utils';

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: Home },
    { name: 'My Snippets', href: '/snippets', icon: Code },
    { name: 'Teams', href: '/teams', icon: Users },
    { name: 'Search', href: '/search', icon: Search },
];

export default function AppLayout({ children, title }) {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [darkMode, setDarkMode] = useState(false);

    const toggleDarkMode = () => {
        setDarkMode(!darkMode);
        document.documentElement.classList.toggle('dark');
    };

    return (
        <div className="min-h-screen bg-background">
            {/* Mobile sidebar overlay */}
            {sidebarOpen && (
                <div
                    className="fixed inset-0 z-40 bg-black/50 lg:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            {/* Sidebar */}
            <aside
                className={cn(
                    'fixed inset-y-0 left-0 z-50 w-64 bg-card border-r transform transition-transform lg:translate-x-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                )}
            >
                <div className="flex h-16 items-center gap-2 px-6 border-b">
                    <Code className="h-8 w-8 text-primary" />
                    <span className="font-bold text-xl">SnippetShare</span>
                </div>

                <nav className="flex-1 px-4 py-6 space-y-2">
                    {navigation.map((item) => (
                        <Link
                            key={item.name}
                            href={item.href}
                            className={cn(
                                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                'hover:bg-accent hover:text-accent-foreground',
                                route().current(item.href.slice(1) + '*')
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-muted-foreground'
                            )}
                        >
                            <item.icon className="h-5 w-5" />
                            {item.name}
                        </Link>
                    ))}
                </nav>

                {/* Create button */}
                <div className="p-4 border-t">
                    <Button asChild className="w-full">
                        <Link href="/snippets/create">
                            <Plus className="h-4 w-4 mr-2" />
                            New Snippet
                        </Link>
                    </Button>
                </div>
            </aside>

            {/* Main content */}
            <div className="lg:pl-64">
                {/* Top header */}
                <header className="sticky top-0 z-30 flex h-16 items-center gap-4 border-b bg-background/95 backdrop-blur px-6">
                    <button
                        onClick={() => setSidebarOpen(true)}
                        className="lg:hidden"
                    >
                        <Menu className="h-6 w-6" />
                    </button>

                    <div className="flex-1">
                        {title && (
                            <h1 className="text-lg font-semibold">{title}</h1>
                        )}
                    </div>

                    <div className="flex items-center gap-4">
                        {/* Theme toggle */}
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={toggleDarkMode}
                        >
                            {darkMode ? (
                                <Sun className="h-5 w-5" />
                            ) : (
                                <Moon className="h-5 w-5" />
                            )}
                        </Button>

                        {/* Notifications */}
                        <Button variant="ghost" size="icon" asChild>
                            <Link href="/notifications">
                                <Bell className="h-5 w-5" />
                            </Link>
                        </Button>

                        {/* User menu */}
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button
                                    variant="ghost"
                                    className="relative h-10 w-10 rounded-full"
                                >
                                    <Avatar>
                                        <AvatarImage src={auth.user.avatar_url} />
                                        <AvatarFallback>
                                            {auth.user.username.charAt(0).toUpperCase()}
                                        </AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <div className="px-2 py-1.5">
                                    <p className="text-sm font-medium">
                                        {auth.user.full_name || auth.user.username}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {auth.user.email}
                                    </p>
                                </div>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem asChild>
                                    <Link href="/profile">
                                        <User className="mr-2 h-4 w-4" />
                                        Profile
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem asChild>
                                    <Link href="/settings">
                                        <Settings className="mr-2 h-4 w-4" />
                                        Settings
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem asChild>
                                    <Link href="/logout" method="post" as="button">
                                        <LogOut className="mr-2 h-4 w-4" />
                                        Log out
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </header>

                {/* Page content */}
                <main className="p-6">{children}</main>
            </div>
        </div>
    );
}
```

### 5.3 Code Editor Component (resources/js/Components/CodeEditor.jsx)
```jsx
import { useState, useCallback } from 'react';
import Editor from '@monaco-editor/react';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Label } from '@/Components/ui/label';

const themes = [
    { value: 'vs-dark', label: 'Dark' },
    { value: 'light', label: 'Light' },
    { value: 'hc-black', label: 'High Contrast' },
];

export default function CodeEditor({
    value,
    onChange,
    language = 'javascript',
    onLanguageChange,
    languages = [],
    height = '400px',
    readOnly = false,
}) {
    const [theme, setTheme] = useState('vs-dark');

    const handleEditorChange = useCallback(
        (value) => {
            onChange?.(value);
        },
        [onChange]
    );

    return (
        <div className="space-y-3">
            <div className="flex gap-4">
                <div className="space-y-1.5">
                    <Label>Language</Label>
                    <Select value={language} onValueChange={onLanguageChange}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Select language" />
                        </SelectTrigger>
                        <SelectContent>
                            {languages.map((lang) => (
                                <SelectItem key={lang.slug} value={lang.slug}>
                                    {lang.display_name}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>

                <div className="space-y-1.5">
                    <Label>Theme</Label>
                    <Select value={theme} onValueChange={setTheme}>
                        <SelectTrigger className="w-[140px]">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            {themes.map((t) => (
                                <SelectItem key={t.value} value={t.value}>
                                    {t.label}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <div className="border rounded-lg overflow-hidden">
                <Editor
                    height={height}
                    language={language}
                    theme={theme}
                    value={value}
                    onChange={handleEditorChange}
                    options={{
                        readOnly,
                        minimap: { enabled: false },
                        fontSize: 14,
                        fontFamily: "'JetBrains Mono', 'Fira Code', monospace",
                        lineNumbers: 'on',
                        scrollBeyondLastLine: false,
                        automaticLayout: true,
                        tabSize: 4,
                        wordWrap: 'on',
                    }}
                />
            </div>
        </div>
    );
}
```

### 5.4 Snippet Card Component (resources/js/Components/SnippetCard.jsx)
```jsx
import { Link } from '@inertiajs/react';
import { formatDistanceToNow } from 'date-fns';
import { Eye, GitFork, Heart, MessageSquare, Lock, Users, Globe } from 'lucide-react';
import { Card, CardContent, CardFooter, CardHeader } from '@/Components/ui/card';
import { Badge } from '@/Components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';

const privacyIcons = {
    public: Globe,
    private: Lock,
    team: Users,
    unlisted: Globe,
};

export default function SnippetCard({ snippet }) {
    const PrivacyIcon = privacyIcons[snippet.privacy];

    return (
        <Card className="hover:shadow-md transition-shadow">
            <CardHeader className="pb-3">
                <div className="flex items-start justify-between">
                    <div className="flex items-center gap-2">
                        <Badge variant="secondary" className="font-mono text-xs">
                            {snippet.language}
                        </Badge>
                        <PrivacyIcon className="h-4 w-4 text-muted-foreground" />
                    </div>
                    {snippet.is_featured && (
                        <Badge variant="default">Featured</Badge>
                    )}
                </div>

                <Link
                    href={`/snippets/${snippet.slug}`}
                    className="text-lg font-semibold hover:text-primary transition-colors line-clamp-1"
                >
                    {snippet.title}
                </Link>

                {snippet.description && (
                    <p className="text-sm text-muted-foreground line-clamp-2">
                        {snippet.description}
                    </p>
                )}
            </CardHeader>

            <CardContent className="pb-3">
                {/* Code preview */}
                <div className="bg-muted rounded-md p-3 font-mono text-xs overflow-hidden">
                    <pre className="line-clamp-4 text-muted-foreground">
                        {snippet.code}
                    </pre>
                </div>

                {/* Tags */}
                {snippet.tags?.length > 0 && (
                    <div className="flex flex-wrap gap-1.5 mt-3">
                        {snippet.tags.slice(0, 3).map((tag) => (
                            <Badge
                                key={tag.id}
                                variant="outline"
                                className="text-xs"
                            >
                                #{tag.name}
                            </Badge>
                        ))}
                        {snippet.tags.length > 3 && (
                            <Badge variant="outline" className="text-xs">
                                +{snippet.tags.length - 3}
                            </Badge>
                        )}
                    </div>
                )}
            </CardContent>

            <CardFooter className="pt-0">
                <div className="flex items-center justify-between w-full text-sm text-muted-foreground">
                    <Link
                        href={`/users/${snippet.user.username}`}
                        className="flex items-center gap-2 hover:text-foreground"
                    >
                        <Avatar className="h-6 w-6">
                            <AvatarImage src={snippet.user.avatar_url} />
                            <AvatarFallback className="text-xs">
                                {snippet.user.username.charAt(0).toUpperCase()}
                            </AvatarFallback>
                        </Avatar>
                        <span>@{snippet.user.username}</span>
                    </Link>

                    <div className="flex items-center gap-3">
                        <span className="flex items-center gap-1">
                            <Eye className="h-4 w-4" />
                            {snippet.view_count}
                        </span>
                        <span className="flex items-center gap-1">
                            <Heart className="h-4 w-4" />
                            {snippet.favorite_count}
                        </span>
                        <span className="flex items-center gap-1">
                            <GitFork className="h-4 w-4" />
                            {snippet.fork_count}
                        </span>
                    </div>
                </div>
            </CardFooter>
        </Card>
    );
}
```

### 5.5 Create Snippet Page (resources/js/Pages/Snippets/Create.jsx)
```jsx
import { useForm } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import CodeEditor from '@/Components/CodeEditor';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Switch } from '@/Components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import TagInput from '@/Components/TagInput';
import { toast } from 'sonner';

export default function Create({ languages, categories, teams, tags: availableTags }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        code: '',
        language: 'javascript',
        category_id: '',
        team_id: '',
        privacy: 'public',
        tags: [],
        allow_comments: true,
        allow_forks: true,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/snippets', {
            onSuccess: () => {
                toast.success('Snippet created successfully!');
            },
            onError: () => {
                toast.error('Failed to create snippet. Please check the form.');
            },
        });
    };

    return (
        <AppLayout title="Create Snippet">
            <form onSubmit={handleSubmit} className="max-w-4xl mx-auto space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Create New Snippet</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-6">
                        {/* Title */}
                        <div className="space-y-2">
                            <Label htmlFor="title">Title *</Label>
                            <Input
                                id="title"
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                placeholder="Enter snippet title"
                                className={errors.title ? 'border-destructive' : ''}
                            />
                            {errors.title && (
                                <p className="text-sm text-destructive">{errors.title}</p>
                            )}
                        </div>

                        {/* Description */}
                        <div className="space-y-2">
                            <Label htmlFor="description">Description</Label>
                            <Textarea
                                id="description"
                                value={data.description}
                                onChange={(e) => setData('description', e.target.value)}
                                placeholder="Describe what this snippet does..."
                                rows={3}
                            />
                        </div>

                        {/* Code Editor */}
                        <div className="space-y-2">
                            <Label>Code *</Label>
                            <CodeEditor
                                value={data.code}
                                onChange={(value) => setData('code', value)}
                                language={data.language}
                                onLanguageChange={(lang) => setData('language', lang)}
                                languages={languages}
                                height="350px"
                            />
                            {errors.code && (
                                <p className="text-sm text-destructive">{errors.code}</p>
                            )}
                        </div>

                        {/* Category & Team */}
                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-2">
                                <Label>Category</Label>
                                <Select
                                    value={data.category_id}
                                    onValueChange={(value) => setData('category_id', value)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select category" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {categories.map((cat) => (
                                            <SelectItem key={cat.id} value={cat.id}>
                                                {cat.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>

                            <div className="space-y-2">
                                <Label>Team (optional)</Label>
                                <Select
                                    value={data.team_id}
                                    onValueChange={(value) => setData('team_id', value)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Personal snippet" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">Personal</SelectItem>
                                        {teams.map((team) => (
                                            <SelectItem key={team.id} value={team.id}>
                                                {team.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        {/* Tags */}
                        <div className="space-y-2">
                            <Label>Tags</Label>
                            <TagInput
                                value={data.tags}
                                onChange={(tags) => setData('tags', tags)}
                                suggestions={availableTags}
                                placeholder="Add tags..."
                            />
                        </div>

                        {/* Privacy */}
                        <div className="space-y-2">
                            <Label>Privacy</Label>
                            <Select
                                value={data.privacy}
                                onValueChange={(value) => setData('privacy', value)}
                            >
                                <SelectTrigger className="w-[200px]">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="public">Public</SelectItem>
                                    <SelectItem value="private">Private</SelectItem>
                                    <SelectItem value="unlisted">Unlisted</SelectItem>
                                    {data.team_id && (
                                        <SelectItem value="team">Team Only</SelectItem>
                                    )}
                                </SelectContent>
                            </Select>
                        </div>

                        {/* Toggles */}
                        <div className="flex gap-8">
                            <div className="flex items-center gap-2">
                                <Switch
                                    id="allow_comments"
                                    checked={data.allow_comments}
                                    onCheckedChange={(checked) =>
                                        setData('allow_comments', checked)
                                    }
                                />
                                <Label htmlFor="allow_comments">Allow comments</Label>
                            </div>

                            <div className="flex items-center gap-2">
                                <Switch
                                    id="allow_forks"
                                    checked={data.allow_forks}
                                    onCheckedChange={(checked) =>
                                        setData('allow_forks', checked)
                                    }
                                />
                                <Label htmlFor="allow_forks">Allow forks</Label>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Submit */}
                <div className="flex justify-end gap-4">
                    <Button type="button" variant="outline">
                        Save as Draft
                    </Button>
                    <Button type="submit" disabled={processing}>
                        {processing ? 'Creating...' : 'Create Snippet'}
                    </Button>
                </div>
            </form>
        </AppLayout>
    );
}
```

---

## 6. API Implementation

### 6.1 Routes (routes/api.php)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public routes
    Route::post('/auth/register', [V1\AuthController::class, 'register']);
    Route::post('/auth/login', [V1\AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [V1\AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [V1\AuthController::class, 'resetPassword']);

    // Public snippet viewing
    Route::get('/public/snippets/{snippet:slug}', [V1\SnippetController::class, 'showPublic']);
    Route::get('/embed/{shareToken}', [V1\SnippetController::class, 'embed']);

    // Languages, categories, tags (public)
    Route::get('/languages', [V1\LanguageController::class, 'index']);
    Route::get('/categories', [V1\CategoryController::class, 'index']);
    Route::get('/tags', [V1\TagController::class, 'index']);

    // Reports (public)
    Route::prefix('reports')->group(function () {
        Route::get('/popular-snippets', [V1\ReportController::class, 'popularSnippets']);
        Route::get('/trending', [V1\ReportController::class, 'trending']);
        Route::get('/top-users', [V1\ReportController::class, 'topUsers']);
    });

    // Search (public, limited results)
    Route::get('/search', [V1\SearchController::class, 'index']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/auth/logout', [V1\AuthController::class, 'logout']);
        Route::post('/auth/refresh', [V1\AuthController::class, 'refresh']);
        Route::get('/auth/user', [V1\AuthController::class, 'user']);
        Route::post('/auth/verify-email', [V1\AuthController::class, 'verifyEmail']);

        // User profile
        Route::get('/user', [V1\UserController::class, 'show']);
        Route::put('/user/profile', [V1\UserController::class, 'updateProfile']);
        Route::put('/user/password', [V1\UserController::class, 'updatePassword']);
        Route::put('/user/settings', [V1\UserController::class, 'updateSettings']);
        Route::get('/user/analytics', [V1\UserController::class, 'analytics']);

        // Other users
        Route::get('/users/{user:username}', [V1\UserController::class, 'showByUsername']);
        Route::get('/users/{user:username}/snippets', [V1\UserController::class, 'snippets']);
        Route::post('/users/{user}/follow', [V1\UserController::class, 'follow']);
        Route::delete('/users/{user}/follow', [V1\UserController::class, 'unfollow']);

        // Snippets
        Route::apiResource('snippets', V1\SnippetController::class);
        Route::post('/snippets/{snippet}/fork', [V1\SnippetController::class, 'fork']);
        Route::post('/snippets/{snippet}/favorite', [V1\SnippetController::class, 'favorite']);
        Route::get('/snippets/{snippet}/versions', [V1\SnippetController::class, 'versions']);
        Route::post('/snippets/{snippet}/restore/{version}', [V1\SnippetController::class, 'restore']);
        Route::get('/snippets/{snippet}/share', [V1\SnippetController::class, 'shareInfo']);
        Route::post('/snippets/{snippet}/share', [V1\SnippetController::class, 'createShare']);

        // Comments
        Route::get('/snippets/{snippet}/comments', [V1\CommentController::class, 'index']);
        Route::post('/snippets/{snippet}/comments', [V1\CommentController::class, 'store']);
        Route::put('/comments/{comment}', [V1\CommentController::class, 'update']);
        Route::delete('/comments/{comment}', [V1\CommentController::class, 'destroy']);

        // Teams
        Route::apiResource('teams', V1\TeamController::class);
        Route::get('/teams/{team}/snippets', [V1\TeamController::class, 'snippets']);
        Route::get('/teams/{team}/members', [V1\TeamController::class, 'members']);
        Route::post('/teams/{team}/invite', [V1\TeamController::class, 'invite']);
        Route::put('/teams/{team}/members/{user}', [V1\TeamController::class, 'updateMember']);
        Route::delete('/teams/{team}/members/{user}', [V1\TeamController::class, 'removeMember']);
        Route::post('/teams/{team}/leave', [V1\TeamController::class, 'leave']);

        // Invitations
        Route::post('/invitations/{invitation}/accept', [V1\InvitationController::class, 'accept']);
        Route::post('/invitations/{invitation}/decline', [V1\InvitationController::class, 'decline']);

        // Favorites & Collections
        Route::get('/favorites', [V1\FavoriteController::class, 'index']);
        Route::apiResource('collections', V1\CollectionController::class);
        Route::post('/collections/{collection}/snippets', [V1\CollectionController::class, 'addSnippet']);
        Route::delete('/collections/{collection}/snippets/{snippet}', [V1\CollectionController::class, 'removeSnippet']);

        // Notifications
        Route::get('/notifications', [V1\NotificationController::class, 'index']);
        Route::put('/notifications/{notification}/read', [V1\NotificationController::class, 'markAsRead']);
        Route::put('/notifications/read-all', [V1\NotificationController::class, 'markAllAsRead']);

        // Syntax highlighting
        Route::post('/highlight', [V1\HighlightController::class, 'highlight']);

        // Admin routes
        Route::middleware('admin')->prefix('admin')->group(function () {
            Route::get('/audit-logs', [V1\Admin\AuditLogController::class, 'index']);
            Route::get('/audit-logs/export', [V1\Admin\AuditLogController::class, 'export']);
            Route::get('/users', [V1\Admin\UserManagementController::class, 'index']);
            Route::put('/users/{user}/suspend', [V1\Admin\UserManagementController::class, 'suspend']);
            Route::put('/users/{user}/activate', [V1\Admin\UserManagementController::class, 'activate']);
        });
    });
});
```

---

## 7. Feature Implementation Order

### Phase 1: Foundation (Days 1-3)
```
□ 1.1 Create Laravel project with all dependencies
□ 1.2 Configure database connection
□ 1.3 Create all migrations
□ 1.4 Create all models with relationships
□ 1.5 Setup Inertia + React + Shadcn
□ 1.6 Create base layouts (Auth, App, Admin)
□ 1.7 Implement authentication (register, login, logout)
□ 1.8 Create API authentication with Sanctum
□ 1.9 Seed languages and categories
```

### Phase 2: Core Snippets (Days 4-6)
```
□ 2.1 Create PygmentsService
□ 2.2 Create SnippetService
□ 2.3 Implement Snippet CRUD API
□ 2.4 Create CodeEditor component
□ 2.5 Create SnippetCard component
□ 2.6 Build Create Snippet page
□ 2.7 Build Edit Snippet page
□ 2.8 Build View Snippet page
□ 2.9 Build My Snippets page
□ 2.10 Implement tags system
```

### Phase 3: Teams & Collaboration (Days 7-9)
```
□ 3.1 Create TeamService
□ 3.2 Implement Team CRUD API
□ 3.3 Build Teams List page
□ 3.4 Build Team Dashboard page
□ 3.5 Build Team Settings page
□ 3.6 Implement team invitations
□ 3.7 Implement team permissions
□ 3.8 Team snippets view
```

### Phase 4: Social Features (Days 10-11)
```
□ 4.1 Implement favorites system
□ 4.2 Implement comments system
□ 4.3 Implement follow system
□ 4.4 Build user profiles
□ 4.5 Implement notifications
□ 4.6 Build collections feature
```

### Phase 5: Advanced Features (Days 12-14)
```
□ 5.1 Implement version history
□ 5.2 Build diff viewer component
□ 5.3 Implement full-text search
□ 5.4 Build search page with filters
□ 5.5 Implement sharing & embedding
□ 5.6 Build trending/popular pages
```

### Phase 6: Admin & Reports (Days 15-16)
```
□ 6.1 Build admin dashboard
□ 6.2 Implement audit logging
□ 6.3 Build audit logs viewer
□ 6.4 Create reports pages
□ 6.5 User management admin
```

### Phase 7: Polish & Testing (Days 17-18)
```
□ 7.1 Write feature tests for API
□ 7.2 Write unit tests for services
□ 7.3 UI/UX polish
□ 7.4 Performance optimization
□ 7.5 Security review
□ 7.6 Documentation
```

---

## 8. Testing Strategy

### 8.1 Test Structure
```
tests/
├── Feature/
│   ├── Api/
│   │   ├── AuthenticationTest.php
│   │   ├── SnippetTest.php
│   │   ├── TeamTest.php
│   │   ├── CommentTest.php
│   │   └── SearchTest.php
│   └── Web/
│       ├── DashboardTest.php
│       └── SnippetManagementTest.php
└── Unit/
    ├── Models/
    │   ├── SnippetTest.php
    │   └── TeamTest.php
    └── Services/
        ├── PygmentsServiceTest.php
        └── SnippetServiceTest.php
```

### 8.2 Example Test
```php
<?php

namespace Tests\Feature\Api;

use App\Models\Snippet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnippetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_snippet(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/snippets', [
                'title' => 'Test Snippet',
                'code' => 'console.log("Hello");',
                'language' => 'javascript',
                'privacy' => 'public',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Test Snippet');

        $this->assertDatabaseHas('snippets', [
            'title' => 'Test Snippet',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_view_private_snippet(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $snippet = Snippet::factory()->create([
            'user_id' => $owner->id,
            'privacy' => 'private',
        ]);

        $response = $this->actingAs($other, 'sanctum')
            ->getJson("/api/v1/snippets/{$snippet->id}");

        $response->assertStatus(403);
    }
}
```

---

## 9. Deployment

### 9.1 Production Checklist
```
□ Set APP_ENV=production
□ Set APP_DEBUG=false
□ Configure Cloud SQL connection
□ Configure Cloud Storage
□ Set up Redis for caching (optional)
□ Configure queue worker
□ Set up SSL certificate
□ Configure CORS for API
□ Set rate limiting
□ Enable OPcache
□ Run migrations
□ Seed production data (languages, categories)
```

### 9.2 Dockerfile
```dockerfile
FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node and build assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm ci \
    && npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
```

---

## Summary

This implementation plan provides a comprehensive roadmap for building the Dashboard application. Key points:

1. **Solid Foundation** - Laravel 11 with modern tooling
2. **Type-Safe Frontend** - React with Shadcn/ui components
3. **Clean Architecture** - Services, Resources, Policies
4. **Dual Purpose** - Serves both Web UI and Mobile API
5. **Scalable** - Ready for production deployment

**Estimated Timeline:** 2-3 weeks for core features

**Next Steps:**
1. Review and approve this plan
2. Set up development environment
3. Create Laravel project
4. Begin Phase 1 implementation

---

*Document Version: 1.0*
*Last Updated: December 23, 2025*