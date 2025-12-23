# Code Snippet Sharing Platform - Project Documentation

> **Last Updated:** December 23, 2025
> **Version:** 1.0
> **Status:** Planning Phase

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack](#2-tech-stack)
3. [System Architecture](#3-system-architecture)
4. [Required Modules](#4-required-modules)
5. [Data Models](#5-data-models)
6. [Page Structure](#6-page-structure)
7. [API Design](#7-api-design)
8. [Development Phases](#8-development-phases)
9. [UX/UI Guidelines](#9-uxui-guidelines)
10. [Configuration & Setup](#10-configuration--setup)
11. [Decision Log](#11-decision-log)
12. [References](#12-references)

---

## 1. Project Overview

### 1.1 Description

A utility for developers and teams to store, categorize, and share code snippets with syntax highlighting and revision control.

### 1.2 Target Users

-   Individual developers
-   Development teams
-   Organizations managing code knowledge bases

### 1.3 Key Value Propositions

-   Centralized code snippet storage
-   Syntax highlighting for 100+ languages
-   Version control and revision history
-   Team collaboration features
-   Easy sharing and embedding

### 1.4 Project Components

| Component               | Description                      | Technology                            |
| ----------------------- | -------------------------------- | ------------------------------------- |
| **Android App**         | Mobile client for end users      | Java, XML                             |
| **Dashboard**           | Web admin panel & user interface | Laravel, React, Inertia.js, Shadcn/ui |
| **API Backend**         | REST API serving both clients    | Laravel (within Dashboard)            |
| **Database**            | Data persistence                 | Google Cloud SQL (PostgreSQL)         |
| **Storage**             | File storage (avatars, assets)   | Google Cloud Storage                  |
| **Syntax Highlighting** | Code formatting                  | Pygments (Python)                     |

---

## 2. Tech Stack

### 2.1 Android Application

| Category       | Technology           | Version     | Purpose                      |
| -------------- | -------------------- | ----------- | ---------------------------- |
| Language       | Java                 | 11          | Primary development language |
| UI Framework   | XML Layouts          | -           | User interface               |
| Min SDK        | API 24               | Android 7.0 | Minimum supported version    |
| Target SDK     | API 36               | Android 15  | Target version               |
| Build System   | Gradle               | 8.13        | Build automation             |
| Architecture   | MVVM                 | -           | App architecture pattern     |
| Networking     | Retrofit + OkHttp    | Latest      | API communication            |
| Local Database | Room                 | Latest      | Local data caching           |
| Image Loading  | Glide                | Latest      | Image loading & caching      |
| Navigation     | Navigation Component | Latest      | Screen navigation            |
| DI             | Hilt                 | Latest      | Dependency injection         |

### 2.2 Dashboard (Web Application)

| Category           | Technology      | Version | Purpose                   |
| ------------------ | --------------- | ------- | ------------------------- |
| Backend Framework  | Laravel         | 11.x    | API & Server-side logic   |
| Frontend Framework | React           | 18.x    | User interface            |
| Bridge             | Inertia.js      | 2.x     | Laravel-React integration |
| UI Components      | Shadcn/ui       | Latest  | Pre-built components      |
| Styling            | Tailwind CSS    | 3.x     | Utility-first CSS         |
| Authentication     | Laravel Sanctum | Latest  | API token authentication  |
| Database ORM       | Eloquent        | -       | Database abstraction      |

### 2.3 Infrastructure (Google Cloud)

| Service        | Purpose                             |
| -------------- | ----------------------------------- |
| Cloud SQL      | PostgreSQL database hosting         |
| Cloud Storage  | File storage (avatars, attachments) |
| Cloud Run      | Container hosting for Laravel app   |
| Cloud CDN      | Static asset caching                |
| Secret Manager | Secure credential storage           |
| Cloud Build    | CI/CD pipeline                      |

### 2.4 Syntax Highlighting

| Technology         | Purpose                                |
| ------------------ | -------------------------------------- |
| Pygments (Python)  | Server-side syntax highlighting        |
| Integration Method | Python microservice or shell execution |

---

## 3. System Architecture

### 3.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      CODE SNIPPET SHARING PLATFORM                       │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│   ┌─────────────────────┐              ┌─────────────────────────────┐  │
│   │    Android App      │              │     Dashboard (Web)         │  │
│   │    ───────────      │              │     ─────────────           │  │
│   │    Java + XML       │   REST API   │     Laravel + React         │  │
│   │    MVVM Pattern     │◄────────────►│     Inertia.js + Shadcn     │  │
│   │    Retrofit + Room  │   (JSON)     │                             │  │
│   └─────────────────────┘              └─────────────────────────────┘  │
│              │                                      │                    │
│              │         HTTPS / REST API             │                    │
│              └──────────────────┬───────────────────┘                    │
│                                 │                                        │
│                                 ▼                                        │
│   ┌─────────────────────────────────────────────────────────────────┐   │
│   │                      Laravel API Backend                         │   │
│   │                      ──────────────────                          │   │
│   │    • RESTful API Endpoints                                       │   │
│   │    • Sanctum Authentication (Token-based)                        │   │
│   │    • Request Validation & Rate Limiting                          │   │
│   │    • Business Logic & Services                                   │   │
│   └─────────────────────────────────────────────────────────────────┘   │
│              │                    │                    │                 │
│              ▼                    ▼                    ▼                 │
│   ┌──────────────────┐ ┌──────────────────┐ ┌──────────────────────┐   │
│   │  Google Cloud    │ │    Pygments      │ │   Google Cloud       │   │
│   │    Cloud SQL     │ │    Service       │ │     Storage          │   │
│   │  ──────────────  │ │  ──────────────  │ │  ────────────────    │   │
│   │   PostgreSQL     │ │  Python-based    │ │  User Avatars        │   │
│   │   Main Database  │ │  Syntax Highlight│ │  File Attachments    │   │
│   └──────────────────┘ └──────────────────┘ └──────────────────────┘   │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

### 3.2 Pygments Integration Architecture

```
┌─────────────────────────────────────────────────────────────────────────┐
│                       PYGMENTS INTEGRATION OPTIONS                       │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  OPTION A: Shell Execution (Simple)                                     │
│  ─────────────────────────────────                                      │
│                                                                         │
│   ┌─────────────┐    shell_exec()    ┌─────────────┐                   │
│   │   Laravel   │──────────────────►│  pygmentize │                    │
│   │   Backend   │◄──────────────────│    CLI      │                    │
│   └─────────────┘    HTML output     └─────────────┘                   │
│                                                                         │
│   Pros: Simple setup, no extra services                                 │
│   Cons: Slower, process overhead per request                            │
│                                                                         │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  OPTION B: Python Microservice (Recommended)                            │
│  ───────────────────────────────────────────                            │
│                                                                         │
│   ┌─────────────┐    HTTP Request    ┌─────────────┐                   │
│   │   Laravel   │──────────────────►│   FastAPI   │                    │
│   │   Backend   │◄──────────────────│  + Pygments │                    │
│   └─────────────┘    JSON Response   └─────────────┘                   │
│                                                                         │
│   Pros: Better performance, async processing, scalable                  │
│   Cons: Additional service to maintain                                  │
│                                                                         │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  OPTION C: Pre-render on Save (Hybrid)                                  │
│  ─────────────────────────────────────                                  │
│                                                                         │
│   ┌─────────────┐    Queue Job       ┌─────────────┐                   │
│   │   Laravel   │──────────────────►│   Worker    │                    │
│   │   Backend   │                    │  + Pygments │                    │
│   └─────────────┘                    └──────┬──────┘                   │
│          │                                   │                          │
│          │         Store Both                │                          │
│          ▼              ▼                    ▼                          │
│   ┌─────────────────────────────────────────────────┐                  │
│   │              Database                            │                  │
│   │   • raw_code (original)                          │                  │
│   │   • highlighted_html (cached)                    │                  │
│   └─────────────────────────────────────────────────┘                  │
│                                                                         │
│   Pros: Fast reads, highlighted on demand                               │
│   Cons: Storage overhead, re-highlight on theme change                  │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

### 3.3 Authentication Flow

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         AUTHENTICATION FLOW                              │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  Mobile App (Sanctum Token)                                             │
│  ──────────────────────────                                             │
│                                                                         │
│   ┌──────────┐    POST /api/v1/auth/login    ┌──────────────┐          │
│   │  Android │─────────────────────────────►│    Laravel   │           │
│   │   App    │   {email, password}           │    Backend   │           │
│   └──────────┘                               └──────────────┘           │
│        │                                            │                   │
│        │◄───────────────────────────────────────────┘                   │
│        │   {token: "xxx", user: {...}}                                  │
│        │                                                                │
│        │    GET /api/v1/snippets                                        │
│        │    Authorization: Bearer {token}                               │
│        │──────────────────────────────────────►                         │
│                                                                         │
│  Web Dashboard (Session + CSRF)                                         │
│  ──────────────────────────────                                         │
│                                                                         │
│   ┌──────────┐    POST /login (Inertia)      ┌──────────────┐          │
│   │  React   │─────────────────────────────►│    Laravel   │           │
│   │  Frontend│   {email, password, _token}   │    Backend   │           │
│   └──────────┘                               └──────────────┘           │
│        │                                            │                   │
│        │◄───────────────────────────────────────────┘                   │
│        │   Session Cookie + CSRF Token                                  │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Required Modules

### 4.1 Module Overview

| #   | Module Name                    | Description                               | Priority |
| --- | ------------------------------ | ----------------------------------------- | -------- |
| 1   | User Authentication & Teams    | User registration, login, team management | Critical |
| 2   | Snippet Creation & Editing     | CRUD operations for snippets              | Critical |
| 3   | Syntax Highlighting (Pygments) | Code formatting with colors               | Critical |
| 4   | Versioning & Revision History  | Track changes, restore versions           | High     |
| 5   | Tagging & Categorization       | Organize snippets with tags/categories    | High     |
| 6   | Search Functionality           | Full-text search, filters                 | High     |
| 7   | Privacy Settings               | Public, Private, Team visibility          | Critical |
| 8   | Embedded/Sharing Links         | Share snippets externally                 | Medium   |
| 9   | Reports                        | Popular snippets, top users               | Medium   |
| 10  | Audit Logs                     | Track all system activities               | Medium   |

### 4.2 Module Details

#### Module 1: User Authentication & Teams

**Features:**

-   Email/Password registration and login
-   OAuth integration (Google, GitHub) - Phase 2
-   Password reset via email
-   Email verification
-   Team creation and management
-   Team roles (Owner, Admin, Member, Viewer)
-   Team invitations via email
-   Session management

**Models:** User, UserSession, OAuthProvider, PasswordReset, EmailVerification, Team, TeamMember, TeamInvitation, TeamRole

**API Endpoints:**

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout
POST   /api/v1/auth/forgot-password
POST   /api/v1/auth/reset-password
POST   /api/v1/auth/verify-email
GET    /api/v1/auth/user

GET    /api/v1/teams
POST   /api/v1/teams
GET    /api/v1/teams/{id}
PUT    /api/v1/teams/{id}
DELETE /api/v1/teams/{id}
POST   /api/v1/teams/{id}/invite
POST   /api/v1/teams/{id}/leave
GET    /api/v1/teams/{id}/members
PUT    /api/v1/teams/{id}/members/{userId}
DELETE /api/v1/teams/{id}/members/{userId}
```

---

#### Module 2: Snippet Creation & Editing

**Features:**

-   Create new snippets with title, description, code
-   Edit existing snippets
-   Delete snippets (soft delete)
-   Multi-file snippets support
-   Auto-save drafts
-   Duplicate/Fork snippets

**Models:** Snippet, SnippetFile

**API Endpoints:**

```
GET    /api/v1/snippets
POST   /api/v1/snippets
GET    /api/v1/snippets/{id}
PUT    /api/v1/snippets/{id}
DELETE /api/v1/snippets/{id}
POST   /api/v1/snippets/{id}/fork
POST   /api/v1/snippets/{id}/duplicate
```

---

#### Module 3: Syntax Highlighting (Pygments)

**Features:**

-   Support for 100+ programming languages
-   Multiple color themes (light/dark)
-   Line numbers display
-   Line highlighting
-   Code formatting preservation

**Models:** Language, SnippetMetadata (cached HTML)

**Supported Languages (Sample):**

```
Python, JavaScript, TypeScript, Java, Kotlin, C, C++, C#,
Go, Rust, Ruby, PHP, Swift, Dart, SQL, HTML, CSS, SCSS,
JSON, YAML, XML, Markdown, Bash, PowerShell, Docker, etc.
```

**API Endpoints:**

```
GET    /api/v1/languages
POST   /api/v1/highlight
        Body: {code: "...", language: "python", theme: "monokai"}
        Response: {html: "<pre>...</pre>"}
```

---

#### Module 4: Versioning & Revision History

**Features:**

-   Automatic version creation on edit
-   View all versions of a snippet
-   Compare two versions (diff view)
-   Restore previous versions
-   Version metadata (who, when, what changed)

**Models:** SnippetVersion

**API Endpoints:**

```
GET    /api/v1/snippets/{id}/versions
GET    /api/v1/snippets/{id}/versions/{versionId}
GET    /api/v1/snippets/{id}/compare?v1={id1}&v2={id2}
POST   /api/v1/snippets/{id}/restore/{versionId}
```

---

#### Module 5: Tagging & Categorization

**Features:**

-   Create and manage tags
-   Assign multiple tags to snippets
-   Browse by category
-   Tag suggestions (autocomplete)
-   Popular tags display

**Models:** Tag, SnippetTag, Category

**API Endpoints:**

```
GET    /api/v1/tags
POST   /api/v1/tags
GET    /api/v1/tags/{id}
GET    /api/v1/tags/{id}/snippets
GET    /api/v1/categories
GET    /api/v1/categories/{id}/snippets
```

---

#### Module 6: Search Functionality

**Features:**

-   Full-text search across snippets
-   Filter by language, tags, category
-   Filter by author, team
-   Filter by date range
-   Sort by relevance, date, popularity

**API Endpoints:**

```
GET    /api/v1/search?q={query}&language={lang}&tags={tags}&sort={sort}
GET    /api/v1/search/suggestions?q={query}
```

---

#### Module 7: Privacy Settings

**Features:**

-   Public snippets (visible to everyone)
-   Private snippets (only owner)
-   Team snippets (visible to team members)
-   Unlisted snippets (accessible via direct link)
-   Default privacy preference per user

**Privacy Levels:**
| Level | Visibility |
|-------|------------|
| Public | Anyone can view |
| Private | Only owner |
| Team | Team members only |
| Unlisted | Anyone with link |

---

#### Module 8: Embedded/Sharing Links

**Features:**

-   Generate shareable links
-   Embed code for websites
-   Customizable embed (theme, size, line numbers)
-   QR code generation
-   Social sharing (Twitter, LinkedIn)
-   Track share statistics

**Models:** Share

**API Endpoints:**

```
GET    /api/v1/snippets/{id}/share
POST   /api/v1/snippets/{id}/share
GET    /api/v1/snippets/{id}/embed
GET    /api/v1/embed/{shareToken}  (Public endpoint)
```

---

#### Module 9: Reports

**Features:**

-   Popular snippets (most viewed, forked, favorited)
-   Top users (most snippets, followers)
-   Trending snippets (recent popularity)
-   Language statistics
-   Personal analytics (views on your snippets)

**Models:** SnippetView, SnippetStatistics, UserActivity

**API Endpoints:**

```
GET    /api/v1/reports/popular-snippets
GET    /api/v1/reports/top-users
GET    /api/v1/reports/trending
GET    /api/v1/reports/languages
GET    /api/v1/user/analytics
```

---

#### Module 10: Audit Logs

**Features:**

-   Log all user actions
-   Log system events
-   Filter by user, action, date
-   Export logs (admin only)
-   Retention policy

**Models:** AuditLog

**Tracked Actions:**

```
USER_REGISTERED, USER_LOGIN, USER_LOGOUT
SNIPPET_CREATED, SNIPPET_UPDATED, SNIPPET_DELETED
TEAM_CREATED, TEAM_MEMBER_ADDED, TEAM_MEMBER_REMOVED
SETTINGS_CHANGED, PASSWORD_CHANGED
```

**API Endpoints:**

```
GET    /api/v1/admin/audit-logs
GET    /api/v1/admin/audit-logs/export
```

---

## 5. Data Models

### 5.1 Core Models Summary

| Category        | Models                                                             | Count  |
| --------------- | ------------------------------------------------------------------ | ------ |
| User Management | User, UserSession, OAuthProvider, PasswordReset, EmailVerification | 5      |
| Snippets        | Snippet, SnippetVersion, SnippetFile, SnippetMetadata              | 4      |
| Teams           | Team, TeamMember, TeamInvitation, TeamRole                         | 4      |
| Organization    | Tag, SnippetTag, Category, Language                                | 4      |
| Social          | Favorite, Comment, Follow, Fork, Share                             | 5      |
| Analytics       | SnippetView, SnippetStatistics, UserActivity                       | 3      |
| System          | AuditLog, Notification, APIKey                                     | 3      |
| **Total**       |                                                                    | **28** |

### 5.2 Key Model Definitions

#### User Model

```
User
├── id: UUID (PK)
├── username: VARCHAR(50) UNIQUE
├── email: VARCHAR(255) UNIQUE
├── email_verified_at: TIMESTAMP NULL
├── password: VARCHAR(255)
├── full_name: VARCHAR(255) NULL
├── bio: TEXT NULL
├── avatar_url: VARCHAR(500) NULL
├── is_admin: BOOLEAN DEFAULT FALSE
├── is_active: BOOLEAN DEFAULT TRUE
├── default_snippet_privacy: ENUM('public', 'private', 'team')
├── theme_preference: ENUM('light', 'dark', 'auto')
├── created_at: TIMESTAMP
├── updated_at: TIMESTAMP
└── deleted_at: TIMESTAMP NULL
```

#### Snippet Model

```
Snippet
├── id: UUID (PK)
├── user_id: UUID (FK -> users)
├── team_id: UUID NULL (FK -> teams)
├── title: VARCHAR(255)
├── description: TEXT NULL
├── code: TEXT
├── language: VARCHAR(50)
├── privacy: ENUM('public', 'private', 'team', 'unlisted')
├── slug: VARCHAR(300) UNIQUE
├── version_number: INTEGER DEFAULT 1
├── parent_snippet_id: UUID NULL (FK -> snippets, for forks)
├── is_fork: BOOLEAN DEFAULT FALSE
├── view_count: INTEGER DEFAULT 0
├── fork_count: INTEGER DEFAULT 0
├── favorite_count: INTEGER DEFAULT 0
├── created_at: TIMESTAMP
├── updated_at: TIMESTAMP
└── deleted_at: TIMESTAMP NULL
```

#### Team Model

```
Team
├── id: UUID (PK)
├── name: VARCHAR(100) UNIQUE
├── slug: VARCHAR(120) UNIQUE
├── description: TEXT NULL
├── avatar_url: VARCHAR(500) NULL
├── owner_id: UUID (FK -> users)
├── privacy: ENUM('public', 'private', 'invite_only')
├── member_count: INTEGER DEFAULT 1
├── snippet_count: INTEGER DEFAULT 0
├── created_at: TIMESTAMP
├── updated_at: TIMESTAMP
└── deleted_at: TIMESTAMP NULL
```

#### SnippetVersion Model

```
SnippetVersion
├── id: UUID (PK)
├── snippet_id: UUID (FK -> snippets)
├── version_number: INTEGER
├── title: VARCHAR(255)
├── description: TEXT NULL
├── code: TEXT
├── language: VARCHAR(50)
├── change_summary: TEXT NULL
├── created_by: UUID (FK -> users)
└── created_at: TIMESTAMP
```

### 5.3 Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    ENTITY RELATIONSHIP DIAGRAM                           │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│   ┌──────────┐         ┌──────────┐         ┌──────────┐               │
│   │   User   │────────<│  Snippet │>────────│   Tag    │               │
│   └──────────┘   1:N   └──────────┘   N:M   └──────────┘               │
│        │                     │                                          │
│        │ 1:N                 │ 1:N                                      │
│        ▼                     ▼                                          │
│   ┌──────────┐         ┌──────────────┐                                │
│   │   Team   │         │ SnippetVersion│                               │
│   └──────────┘         └──────────────┘                                │
│        │                                                                │
│        │ N:M                                                            │
│        ▼                                                                │
│   ┌──────────────┐                                                     │
│   │  TeamMember  │                                                     │
│   └──────────────┘                                                     │
│                                                                         │
│   Legend:                                                               │
│   ────── : One-to-Many (1:N)                                           │
│   ══════ : Many-to-Many (N:M)                                          │
│   ─ ─ ─  : Optional relationship                                       │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 6. Page Structure

### 6.1 Android App Pages (18 Essential + 8 Secondary)

#### Essential Pages (Phase 1) - 18 Pages

| #                  | Page            | Route                    | Description                  |
| ------------------ | --------------- | ------------------------ | ---------------------------- |
| **Authentication** |                 |                          |                              |
| 1                  | Splash Screen   | `/splash`                | App loading, auth check      |
| 2                  | Onboarding      | `/onboarding`            | First-time user introduction |
| 3                  | Login           | `/login`                 | Email/password login         |
| 4                  | Register        | `/register`              | New user registration        |
| 5                  | Forgot Password | `/forgot-password`       | Password reset request       |
| **Main**           |                 |                          |                              |
| 6                  | Home/Feed       | `/home`                  | Recent & popular snippets    |
| 7                  | My Snippets     | `/my-snippets`           | User's own snippets          |
| 8                  | Search          | `/search`                | Search with filters          |
| **Snippet**        |                 |                          |                              |
| 9                  | View Snippet    | `/snippets/{id}`         | View snippet details         |
| 10                 | Create Snippet  | `/snippets/new`          | Create new snippet           |
| 11                 | Edit Snippet    | `/snippets/{id}/edit`    | Edit existing snippet        |
| 12                 | Snippet History | `/snippets/{id}/history` | Version history              |
| **Teams**          |                 |                          |                              |
| 13                 | Teams List      | `/teams`                 | List of user's teams         |
| 14                 | Team Detail     | `/teams/{id}`            | Team dashboard               |
| 15                 | Team Settings   | `/teams/{id}/settings`   | Manage team                  |
| **Profile**        |                 |                          |                              |
| 16                 | My Profile      | `/profile`               | View own profile             |
| 17                 | Edit Profile    | `/profile/edit`          | Edit profile info            |
| 18                 | Settings        | `/settings`              | App settings                 |

#### Secondary Pages (Phase 2) - 8 Pages

| #   | Page              | Route                  | Description            |
| --- | ----------------- | ---------------------- | ---------------------- |
| 19  | Favorites         | `/favorites`           | Favorited snippets     |
| 20  | Collections       | `/collections`         | User collections       |
| 21  | Collection Detail | `/collections/{id}`    | View collection        |
| 22  | Browse Languages  | `/browse/languages`    | Filter by language     |
| 23  | Trending          | `/trending`            | Trending snippets      |
| 24  | User Profile      | `/users/{username}`    | View other users       |
| 25  | Notifications     | `/notifications`       | Activity notifications |
| 26  | Share Snippet     | `/snippets/{id}/share` | Share modal            |

### 6.2 Dashboard Pages (55 Total)

See `complete_pages_doc.md` for full page structure.

**Summary by Category:**
| Category | Count |
|----------|-------|
| Authentication | 5 |
| Dashboard | 2 |
| Snippet Management | 5 |
| Search & Discovery | 6 |
| Team Management | 6 |
| User Profile | 5 |
| Favorites & Collections | 4 |
| Sharing & Embedding | 2 |
| Reports & Analytics | 5 |
| Audit & Administration | 4 |
| Utility Pages | 11 |
| **Total** | **55** |

### 6.3 Mobile Navigation Structure

```
┌─────────────────────────────────────────────────────────────────────────┐
│                      MOBILE NAVIGATION STRUCTURE                         │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│   Bottom Navigation Bar (5 tabs)                                        │
│   ┌─────────────────────────────────────────────────────────────────┐  │
│   │   🏠        🔍        ➕        👥        👤                    │  │
│   │  Home    Search    Create    Teams    Profile                   │  │
│   └─────────────────────────────────────────────────────────────────┘  │
│                                                                         │
│   Tab 1: Home                                                           │
│   ├── Feed (Recent Snippets)                                           │
│   ├── My Snippets                                                       │
│   └── Favorites                                                         │
│                                                                         │
│   Tab 2: Search                                                         │
│   ├── Search Bar                                                        │
│   ├── Filters (Language, Tags, Category)                               │
│   ├── Results List                                                      │
│   └── Browse Languages                                                  │
│                                                                         │
│   Tab 3: Create (FAB - Floating Action Button)                         │
│   └── Opens Create Snippet Screen                                       │
│                                                                         │
│   Tab 4: Teams                                                          │
│   ├── My Teams List                                                     │
│   ├── Team Detail                                                       │
│   ├── Team Snippets                                                     │
│   └── Team Settings                                                     │
│                                                                         │
│   Tab 5: Profile                                                        │
│   ├── My Profile                                                        │
│   ├── Edit Profile                                                      │
│   ├── Settings                                                          │
│   └── Notifications                                                     │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 7. API Design

### 7.1 API Conventions

| Aspect         | Convention                        |
| -------------- | --------------------------------- |
| Base URL       | `/api/v1`                         |
| Format         | JSON                              |
| Authentication | Bearer Token (Sanctum)            |
| Pagination     | `?page=1&per_page=20`             |
| Sorting        | `?sort=created_at&order=desc`     |
| Filtering      | `?language=python&privacy=public` |
| Errors         | Standard HTTP codes + JSON body   |

### 7.2 Response Format

**Success Response:**

```json
{
  "success": true,
  "data": { ... },
  "message": "Operation successful"
}
```

**Paginated Response:**

```json
{
  "success": true,
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 150,
    "last_page": 8
  }
}
```

**Error Response:**

```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 8 characters."]
    }
}
```

### 7.3 Complete API Endpoints

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           API ENDPOINTS                                  │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  AUTHENTICATION                                                         │
│  ───────────────                                                        │
│  POST   /api/v1/auth/register          Create new account               │
│  POST   /api/v1/auth/login             Login, get token                 │
│  POST   /api/v1/auth/logout            Invalidate token                 │
│  POST   /api/v1/auth/refresh           Refresh token                    │
│  POST   /api/v1/auth/forgot-password   Request password reset           │
│  POST   /api/v1/auth/reset-password    Reset password with token        │
│  POST   /api/v1/auth/verify-email      Verify email address             │
│  GET    /api/v1/auth/user              Get authenticated user           │
│                                                                         │
│  USERS                                                                  │
│  ─────                                                                  │
│  GET    /api/v1/users/{username}       Get user profile                 │
│  GET    /api/v1/users/{username}/snippets  User's public snippets       │
│  PUT    /api/v1/user/profile           Update own profile               │
│  PUT    /api/v1/user/password          Change password                  │
│  PUT    /api/v1/user/settings          Update settings                  │
│  POST   /api/v1/users/{id}/follow      Follow user                      │
│  DELETE /api/v1/users/{id}/follow      Unfollow user                    │
│                                                                         │
│  SNIPPETS                                                               │
│  ────────                                                               │
│  GET    /api/v1/snippets               List snippets (with filters)     │
│  POST   /api/v1/snippets               Create snippet                   │
│  GET    /api/v1/snippets/{id}          Get snippet details              │
│  PUT    /api/v1/snippets/{id}          Update snippet                   │
│  DELETE /api/v1/snippets/{id}          Delete snippet                   │
│  POST   /api/v1/snippets/{id}/fork     Fork snippet                     │
│  POST   /api/v1/snippets/{id}/favorite Toggle favorite                  │
│  GET    /api/v1/snippets/{id}/versions Get version history              │
│  POST   /api/v1/snippets/{id}/restore/{versionId}  Restore version      │
│  GET    /api/v1/snippets/{id}/share    Get share info                   │
│  POST   /api/v1/snippets/{id}/share    Create share link                │
│                                                                         │
│  TEAMS                                                                  │
│  ─────                                                                  │
│  GET    /api/v1/teams                  List user's teams                │
│  POST   /api/v1/teams                  Create team                      │
│  GET    /api/v1/teams/{id}             Get team details                 │
│  PUT    /api/v1/teams/{id}             Update team                      │
│  DELETE /api/v1/teams/{id}             Delete team                      │
│  GET    /api/v1/teams/{id}/snippets    Team's snippets                  │
│  GET    /api/v1/teams/{id}/members     Team members                     │
│  POST   /api/v1/teams/{id}/invite      Invite member                    │
│  PUT    /api/v1/teams/{id}/members/{userId}  Update member role         │
│  DELETE /api/v1/teams/{id}/members/{userId}  Remove member              │
│  POST   /api/v1/teams/{id}/leave       Leave team                       │
│                                                                         │
│  TAGS & CATEGORIES                                                      │
│  ─────────────────                                                      │
│  GET    /api/v1/tags                   List all tags                    │
│  GET    /api/v1/tags/{id}/snippets     Snippets with tag                │
│  GET    /api/v1/categories             List categories                  │
│  GET    /api/v1/categories/{id}/snippets  Snippets in category          │
│  GET    /api/v1/languages              List supported languages         │
│                                                                         │
│  SEARCH                                                                 │
│  ──────                                                                 │
│  GET    /api/v1/search                 Search snippets                  │
│         ?q={query}                                                      │
│         &language={lang}                                                │
│         &tags={tag1,tag2}                                               │
│         &category={cat}                                                 │
│         &author={username}                                              │
│         &team={teamId}                                                  │
│         &privacy={public|private|team}                                  │
│         &sort={relevance|recent|popular}                                │
│                                                                         │
│  FAVORITES & COLLECTIONS                                                │
│  ───────────────────────                                                │
│  GET    /api/v1/favorites              List favorites                   │
│  GET    /api/v1/collections            List collections                 │
│  POST   /api/v1/collections            Create collection                │
│  GET    /api/v1/collections/{id}       Get collection                   │
│  PUT    /api/v1/collections/{id}       Update collection                │
│  DELETE /api/v1/collections/{id}       Delete collection                │
│  POST   /api/v1/collections/{id}/snippets  Add snippet                  │
│  DELETE /api/v1/collections/{id}/snippets/{snippetId}  Remove snippet   │
│                                                                         │
│  NOTIFICATIONS                                                          │
│  ─────────────                                                          │
│  GET    /api/v1/notifications          List notifications               │
│  PUT    /api/v1/notifications/{id}/read  Mark as read                   │
│  PUT    /api/v1/notifications/read-all   Mark all as read               │
│                                                                         │
│  REPORTS (Public)                                                       │
│  ───────────────                                                        │
│  GET    /api/v1/reports/popular-snippets   Most popular                 │
│  GET    /api/v1/reports/trending           Trending now                 │
│  GET    /api/v1/reports/top-users          Top contributors             │
│                                                                         │
│  SYNTAX HIGHLIGHTING                                                    │
│  ───────────────────                                                    │
│  POST   /api/v1/highlight              Highlight code                   │
│         Body: {code, language, theme}                                   │
│                                                                         │
│  ADMIN ONLY                                                             │
│  ──────────                                                             │
│  GET    /api/v1/admin/audit-logs       Get audit logs                   │
│  GET    /api/v1/admin/users            Manage users                     │
│  GET    /api/v1/admin/reports          Content reports                  │
│                                                                         │
│  PUBLIC (No Auth)                                                       │
│  ────────────────                                                       │
│  GET    /api/v1/public/snippets/{slug} View public snippet              │
│  GET    /embed/{shareToken}            Embedded snippet view            │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

---

## 8. Development Phases

### 8.1 Phase Overview

```
┌─────────────────────────────────────────────────────────────────────────┐
│                       DEVELOPMENT PHASES                                 │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  Phase 1: Foundation                                                    │
│  ───────────────────                                                    │
│  • Set up Laravel project                                               │
│  • Set up Android project structure                                     │
│  • Configure Google Cloud (SQL, Storage)                                │
│  • Create database migrations                                           │
│  • Implement authentication API                                         │
│  • Android: Login/Register screens                                      │
│                                                                         │
│  Phase 2: Core Snippets                                                 │
│  ─────────────────────                                                  │
│  • Snippet CRUD API                                                     │
│  • Pygments integration                                                 │
│  • Basic search                                                         │
│  • Android: View/Create/Edit snippets                                   │
│  • Dashboard: Snippet management UI                                     │
│                                                                         │
│  Phase 3: Teams & Social                                                │
│  ───────────────────────                                                │
│  • Team management API                                                  │
│  • Privacy settings                                                     │
│  • Favorites & Comments                                                 │
│  • Android: Teams feature                                               │
│  • Dashboard: Team management UI                                        │
│                                                                         │
│  Phase 4: Advanced Features                                             │
│  ──────────────────────────                                             │
│  • Version history                                                      │
│  • Sharing & Embedding                                                  │
│  • Reports & Analytics                                                  │
│  • Audit logs                                                           │
│  • Dashboard: Admin panel                                               │
│                                                                         │
│  Phase 5: Polish & Launch                                               │
│  ────────────────────────                                               │
│  • Performance optimization                                             │
│  • Security audit                                                       │
│  • UI/UX refinements                                                    │
│  • Testing & Bug fixes                                                  │
│  • Documentation                                                        │
│  • Deployment                                                           │
│                                                                         │
└─────────────────────────────────────────────────────────────────────────┘
```

### 8.2 Phase 1: Foundation (Detailed)

**Dashboard Setup:**

```bash
# Create Laravel project
composer create-project laravel/laravel Snippet_Sharing_Dashboard

# Install dependencies
composer require laravel/sanctum
composer require inertiajs/inertia-laravel
npm install @inertiajs/react react react-dom
npm install -D @types/react @types/react-dom
npm install tailwindcss postcss autoprefixer
npm install @radix-ui/react-* (shadcn components)
```

**Database Migrations (Priority Order):**

1. `create_users_table`
2. `create_teams_table`
3. `create_team_members_table`
4. `create_snippets_table`
5. `create_languages_table`
6. `create_tags_table`
7. `create_snippet_tags_table`
8. `create_categories_table`

**Android Setup:**

```gradle
// Add to build.gradle (app)
dependencies {
    // Networking
    implementation 'com.squareup.retrofit2:retrofit:2.9.0'
    implementation 'com.squareup.retrofit2:converter-gson:2.9.0'
    implementation 'com.squareup.okhttp3:logging-interceptor:4.12.0'

    // Architecture
    implementation 'androidx.lifecycle:lifecycle-viewmodel:2.7.0'
    implementation 'androidx.lifecycle:lifecycle-livedata:2.7.0'

    // Room Database
    implementation 'androidx.room:room-runtime:2.6.1'
    annotationProcessor 'androidx.room:room-compiler:2.6.1'

    // Navigation
    implementation 'androidx.navigation:navigation-fragment:2.7.7'
    implementation 'androidx.navigation:navigation-ui:2.7.7'

    // Image Loading
    implementation 'com.github.bumptech.glide:glide:4.16.0'

    // Dependency Injection
    implementation 'com.google.dagger:hilt-android:2.50'
    annotationProcessor 'com.google.dagger:hilt-compiler:2.50'
}
```

### 8.3 Deliverables by Phase

| Phase | Dashboard Deliverables    | Android Deliverables       |
| ----- | ------------------------- | -------------------------- |
| 1     | Auth API, User management | Splash, Login, Register    |
| 2     | Snippet CRUD, Pygments    | View, Create, Edit snippet |
| 3     | Teams API, Privacy        | Teams list, Team detail    |
| 4     | Versions, Reports, Audit  | History, Settings          |
| 5     | Admin panel, Polish       | Offline cache, Polish      |

---

## 9. UX/UI Guidelines

### 9.1 Design Principles

| Principle             | Description                                               |
| --------------------- | --------------------------------------------------------- |
| **Simplicity**        | Clean, uncluttered interface focused on code              |
| **Speed**             | Fast loading, instant search, smooth animations           |
| **Accessibility**     | Screen reader support, high contrast, keyboard navigation |
| **Consistency**       | Same patterns across mobile and web                       |
| **Developer-Focused** | Dark mode default, monospace fonts, familiar patterns     |

### 9.2 Color Palette

```
Primary Colors:
├── Primary:      #6366F1 (Indigo 500)
├── Primary Dark: #4F46E5 (Indigo 600)
├── Secondary:    #8B5CF6 (Violet 500)
└── Accent:       #10B981 (Emerald 500)

Neutral Colors:
├── Background Light: #FFFFFF
├── Background Dark:  #0F172A (Slate 900)
├── Surface Light:    #F8FAFC (Slate 50)
├── Surface Dark:     #1E293B (Slate 800)
├── Text Primary:     #0F172A / #F8FAFC
└── Text Secondary:   #64748B (Slate 500)

Semantic Colors:
├── Success: #10B981 (Green)
├── Warning: #F59E0B (Amber)
├── Error:   #EF4444 (Red)
└── Info:    #3B82F6 (Blue)
```

### 9.3 Typography

```
Headings:    Inter (sans-serif)
Body:        Inter (sans-serif)
Code:        JetBrains Mono / Fira Code (monospace)

Scale:
├── H1: 32px / 2rem
├── H2: 24px / 1.5rem
├── H3: 20px / 1.25rem
├── Body: 16px / 1rem
├── Small: 14px / 0.875rem
└── Code: 14px / 0.875rem
```

### 9.4 Component Guidelines

**Snippet Card:**

```
┌─────────────────────────────────────────┐
│ [Language Badge]           [Privacy] ⭐ │
│                                         │
│ Snippet Title                           │
│ Short description text here...          │
│                                         │
│ ┌─────────────────────────────────────┐ │
│ │ 1 │ function hello() {              │ │
│ │ 2 │   console.log("Hello");         │ │
│ │ 3 │ }                               │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ #javascript #tutorial                   │
│                                         │
│ @username · 2 hours ago · 👁 123        │
└─────────────────────────────────────────┘
```

**Code Editor:**

```
┌─────────────────────────────────────────┐
│ Language: [Python ▼]  Theme: [Dark ▼]   │
├─────────────────────────────────────────┤
│   1 │ def fibonacci(n):                 │
│   2 │     if n <= 1:                    │
│   3 │         return n                  │
│   4 │     return fibonacci(n-1) +       │
│   5 │            fibonacci(n-2)         │
│   6 │                                   │
│   7 │ print(fibonacci(10))              │
│     │ █                                 │
├─────────────────────────────────────────┤
│ Lines: 7  |  Chars: 142  |  Python 3.x  │
└─────────────────────────────────────────┘
```

### 9.5 Mobile-Specific Guidelines

| Guideline         | Specification             |
| ----------------- | ------------------------- |
| Touch targets     | Minimum 48x48dp           |
| Bottom navigation | 5 items max               |
| FAB position      | Bottom right, 16dp margin |
| Pull to refresh   | All list screens          |
| Swipe actions     | Delete, favorite, share   |
| Code font size    | Adjustable 12-20sp        |

---

## 10. Configuration & Setup

### 10.1 Environment Variables

**Dashboard (.env):**

```env
APP_NAME="Snippet Sharing Platform"
APP_ENV=local
APP_KEY=base64:xxx
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (Google Cloud SQL)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=snippet_sharing
DB_USERNAME=postgres
DB_PASSWORD=secret

# Google Cloud Storage
GOOGLE_CLOUD_PROJECT_ID=your-project-id
GOOGLE_CLOUD_STORAGE_BUCKET=snippet-sharing-assets
GOOGLE_APPLICATION_CREDENTIALS=/path/to/service-account.json

# Pygments Service
PYGMENTS_SERVICE_URL=http://localhost:5000

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000
```

**Android (local.properties):**

```properties
sdk.dir=/path/to/Android/Sdk
api.base.url=http://10.0.2.2:8000/api/v1/
api.base.url.production=https://api.snippet-sharing.com/api/v1/
```

### 10.2 Google Cloud Setup

```bash
# 1. Create project
gcloud projects create snippet-sharing-platform

# 2. Enable APIs
gcloud services enable sqladmin.googleapis.com
gcloud services enable storage.googleapis.com
gcloud services enable run.googleapis.com

# 3. Create Cloud SQL instance
gcloud sql instances create snippet-db \
  --database-version=POSTGRES_15 \
  --tier=db-f1-micro \
  --region=asia-southeast1

# 4. Create database
gcloud sql databases create snippet_sharing --instance=snippet-db

# 5. Create storage bucket
gsutil mb -l asia-southeast1 gs://snippet-sharing-assets
```

### 10.3 Pygments Service Setup

**requirements.txt:**

```
fastapi==0.109.0
uvicorn==0.27.0
pygments==2.17.2
```

**main.py:**

```python
from fastapi import FastAPI
from pygments import highlight
from pygments.lexers import get_lexer_by_name
from pygments.formatters import HtmlFormatter

app = FastAPI()

@app.post("/highlight")
async def highlight_code(code: str, language: str, theme: str = "monokai"):
    lexer = get_lexer_by_name(language)
    formatter = HtmlFormatter(style=theme, linenos=True)
    result = highlight(code, lexer, formatter)
    css = formatter.get_style_defs('.highlight')
    return {"html": result, "css": css}
```

---

## 11. Decision Log

### 11.1 Technical Decisions

| Date       | Decision                         | Rationale                   | Alternatives Considered |
| ---------- | -------------------------------- | --------------------------- | ----------------------- |
| 2024-12-23 | Use Java for Android             | Project requirement         | Kotlin                  |
| 2024-12-23 | Use Pygments for highlighting    | Project requirement         | Prism.js, Shiki         |
| 2024-12-23 | Use Google Cloud                 | Project requirement         | AWS, Azure              |
| 2024-12-23 | Use Laravel + React              | Modern, productive          | Django, Express         |
| 2024-12-23 | Use PostgreSQL                   | Better for full-text search | MySQL                   |
| 2024-12-23 | Python microservice for Pygments | Performance                 | Shell execution         |
| 2024-12-23 | 28 core models                   | Reduced from 43 for MVP     | Full 43 models          |
| 2024-12-23 | 18 mobile pages for Phase 1      | Focused MVP                 | Full 55 pages           |

### 11.2 Open Questions

| Question                          | Status  | Notes                  |
| --------------------------------- | ------- | ---------------------- |
| OAuth providers (Google, GitHub)? | Pending | Start with email only? |
| Real-time collaboration?          | Pending | WebSockets or polling? |
| Offline mode priority?            | Pending | Phase 1 or 2?          |
| Code execution sandbox?           | Pending | Out of scope?          |

---

## 12. References

### 12.1 Related Documents

| Document           | Location                    | Description                   |
| ------------------ | --------------------------- | ----------------------------- |
| Complete Pages Doc | `./complete_pages_doc.md`   | Full 55 page specifications   |
| Data Models Doc    | `./complete_data_models.md` | All 43 data model definitions |

### 12.2 External Resources

| Resource           | URL                           | Purpose              |
| ------------------ | ----------------------------- | -------------------- |
| Laravel Docs       | https://laravel.com/docs      | Backend framework    |
| Inertia.js Docs    | https://inertiajs.com         | Laravel-React bridge |
| Shadcn/ui          | https://ui.shadcn.com         | UI components        |
| Android Developers | https://developer.android.com | Android development  |
| Pygments           | https://pygments.org          | Syntax highlighting  |
| Google Cloud       | https://cloud.google.com/docs | Cloud infrastructure |

### 12.3 Design Inspiration

| App/Website | What to Learn              |
| ----------- | -------------------------- |
| GitHub Gist | Snippet management UX      |
| Pastebin    | Simple sharing flow        |
| CodePen     | Code editor experience     |
| Notion      | Team collaboration         |
| VS Code     | Syntax highlighting themes |

---

## Appendix A: Folder Structure

### Dashboard (Laravel + React)

```
Snippet_Sharing_Dashboard/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── SnippetController.php
│   │   │   │   ├── TeamController.php
│   │   │   │   └── ...
│   │   │   └── Web/
│   │   │       ├── DashboardController.php
│   │   │       └── ...
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── Snippet.php
│   │   ├── Team.php
│   │   └── ...
│   └── Services/
│       ├── PygmentsService.php
│       ├── SnippetService.php
│       └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── js/
│   │   ├── Components/
│   │   ├── Layouts/
│   │   ├── Pages/
│   │   └── app.jsx
│   └── css/
├── routes/
│   ├── api.php
│   └── web.php
└── ...
```

### Android App

```
Snippet_Sharing_App/
├── app/
│   └── src/
│       └── main/
│           ├── java/group/eleven/snippet_sharing_app/
│           │   ├── data/
│           │   │   ├── api/
│           │   │   │   ├── ApiService.java
│           │   │   │   └── RetrofitClient.java
│           │   │   ├── local/
│           │   │   │   ├── AppDatabase.java
│           │   │   │   └── dao/
│           │   │   ├── models/
│           │   │   │   ├── User.java
│           │   │   │   ├── Snippet.java
│           │   │   │   └── Team.java
│           │   │   └── repository/
│           │   │       ├── AuthRepository.java
│           │   │       └── SnippetRepository.java
│           │   ├── di/
│           │   │   └── AppModule.java
│           │   ├── ui/
│           │   │   ├── auth/
│           │   │   │   ├── LoginActivity.java
│           │   │   │   └── LoginViewModel.java
│           │   │   ├── home/
│           │   │   ├── snippet/
│           │   │   ├── team/
│           │   │   └── profile/
│           │   ├── utils/
│           │   └── MainActivity.java
│           ├── res/
│           │   ├── drawable/
│           │   ├── layout/
│           │   ├── navigation/
│           │   └── values/
│           └── AndroidManifest.xml
└── ...
```

---

## Appendix B: Checklist

### Pre-Development Checklist

-   [ ] Google Cloud project created
-   [ ] Cloud SQL instance created
-   [ ] Cloud Storage bucket created
-   [ ] Service account created with proper permissions
-   [ ] Laravel project initialized
-   [ ] Android project configured with dependencies
-   [ ] Pygments service created and tested
-   [ ] Database migrations written
-   [ ] API authentication tested

### Phase 1 Completion Checklist

-   [ ] User registration API
-   [ ] User login API
-   [ ] Password reset API
-   [ ] Email verification API
-   [ ] Android: Splash screen
-   [ ] Android: Login screen
-   [ ] Android: Register screen
-   [ ] Android: Forgot password screen
-   [ ] Dashboard: Login page
-   [ ] Dashboard: Register page
-   [ ] Database seeded with test data

---

**End of Document**

_This document should be updated as the project progresses._
