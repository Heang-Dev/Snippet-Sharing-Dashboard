# Database Schema Documentation

> **Last Updated:** January 2026
> **Project:** Snippet Sharing Platform
> **Database:** SQLite (configurable to MySQL/PostgreSQL)

---

## Table of Contents

1. [Overview](#overview)
2. [Entity Relationship Diagram](#entity-relationship-diagram)
3. [Models & Fields](#models--fields)
4. [Relationships Summary](#relationships-summary)
5. [Enums & Constants](#enums--constants)

---

## Overview

The Snippet Sharing Platform uses **18 models** with **UUID primary keys** throughout. Most models support **soft deletes** for data recovery.

| Feature | Technology |
|---------|------------|
| Primary Keys | UUID (36 characters) |
| Timestamps | `created_at`, `updated_at` |
| Soft Deletes | `deleted_at` (where applicable) |
| Slugs | Auto-generated via Spatie Sluggable |

---

## Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────────┐
│                           SNIPPET SHARING PLATFORM - ER DIAGRAM                      │
└─────────────────────────────────────────────────────────────────────────────────────┘

                                    ┌──────────────┐
                                    │    USERS     │
                                    │──────────────│
                                    │ id (PK)      │
                                    │ username     │
                                    │ email        │
                                    └──────┬───────┘
                                           │
           ┌───────────────┬───────────────┼───────────────┬───────────────┐
           │               │               │               │               │
           ▼               ▼               ▼               ▼               ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │   SNIPPETS   │ │    TEAMS     │ │ COLLECTIONS  │ │   COMMENTS   │ │   FOLLOWS    │
    │──────────────│ │──────────────│ │──────────────│ │──────────────│ │──────────────│
    │ id (PK)      │ │ id (PK)      │ │ id (PK)      │ │ id (PK)      │ │ id (PK)      │
    │ user_id (FK) │ │ owner_id(FK) │ │ user_id (FK) │ │ user_id (FK) │ │ follower_id  │
    │ team_id (FK) │ │              │ │              │ │ snippet_id   │ │ following_id │
    └──────┬───────┘ └──────┬───────┘ └──────┬───────┘ └──────────────┘ └──────────────┘
           │                │                │
           │                │                │
           ▼                ▼                ▼
    ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
    │ SNIPPET_TAG  │ │ TEAM_MEMBERS │ │ COLLECTION_  │
    │   (Pivot)    │ │   (Pivot)    │ │   SNIPPET    │
    │──────────────│ │──────────────│ │──────────────│
    │ snippet_id   │ │ team_id (FK) │ │ collection_id│
    │ tag_id (FK)  │ │ user_id (FK) │ │ snippet_id   │
    └──────┬───────┘ └──────────────┘ └──────────────┘
           │
           ▼
    ┌──────────────┐
    │     TAGS     │
    │──────────────│
    │ id (PK)      │
    │ name         │
    └──────────────┘

Additional Tables:
┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│  LANGUAGES   │ │  CATEGORIES  │ │   SHARES     │ │NOTIFICATIONS │
│──────────────│ │──────────────│ │──────────────│ │──────────────│
│ id (PK)      │ │ id (PK)      │ │ id (PK)      │ │ id (PK)      │
│ name         │ │ name         │ │ snippet_id   │ │ user_id (FK) │
│              │ │ parent_id    │ │ shared_by    │ │ actor_id     │
└──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘

┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐
│  FAVORITES   │ │SNIPPET_VIEWS │ │SNIPPET_VERS. │ │ AUDIT_LOGS   │
│──────────────│ │──────────────│ │──────────────│ │──────────────│
│ id (PK)      │ │ id (PK)      │ │ id (PK)      │ │ id (PK)      │
│ user_id (FK) │ │ snippet_id   │ │ snippet_id   │ │ user_id (FK) │
│ snippet_id   │ │ user_id      │ │ version_num  │ │ action       │
└──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘

┌──────────────┐
│TEAM_INVITES  │
│──────────────│
│ id (PK)      │
│ team_id (FK) │
│ email        │
└──────────────┘
```

---

## Models & Fields

### 1. USERS

> Core user authentication and profile model

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `username` | VARCHAR(50) | UNIQUE, NOT NULL | Unique username |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL | User email |
| `email_verified_at` | TIMESTAMP | NULLABLE | Email verification date |
| `password` | VARCHAR(255) | NULLABLE | Hashed password (nullable for OAuth) |
| `full_name` | VARCHAR(255) | NULLABLE | Display name |
| `bio` | TEXT | NULLABLE | User biography |
| `avatar_url` | VARCHAR(500) | NULLABLE | Avatar URL |
| `avatar` | VARCHAR(255) | NULLABLE | Avatar file path |
| `location` | VARCHAR(100) | NULLABLE | User location |
| `company` | VARCHAR(100) | NULLABLE | Company name |
| `github_url` | VARCHAR(255) | NULLABLE | GitHub profile URL |
| `twitter_url` | VARCHAR(255) | NULLABLE | Twitter profile URL |
| `website_url` | VARCHAR(255) | NULLABLE | Personal website URL |
| `is_admin` | BOOLEAN | DEFAULT false | Admin flag |
| `is_active` | BOOLEAN | DEFAULT true | Account active flag |
| `profile_visibility` | ENUM | DEFAULT 'public' | 'public', 'private' |
| `show_email` | BOOLEAN | DEFAULT false | Show email publicly |
| `show_activity` | BOOLEAN | DEFAULT true | Show activity publicly |
| `default_snippet_privacy` | ENUM | DEFAULT 'public' | 'public', 'private', 'team' |
| `theme_preference` | ENUM | DEFAULT 'system' | 'light', 'dark', 'system' |
| `snippets_count` | INTEGER | DEFAULT 0 | Counter cache |
| `followers_count` | INTEGER | DEFAULT 0 | Counter cache |
| `following_count` | INTEGER | DEFAULT 0 | Counter cache |
| `last_login_at` | TIMESTAMP | NULLABLE | Last login time |
| `social_provider` | VARCHAR(255) | NULLABLE | OAuth provider |
| `social_id` | VARCHAR(255) | NULLABLE | OAuth provider ID |
| `remember_token` | VARCHAR(100) | NULLABLE | Remember me token |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete |

**Indexes:** `username`, `is_active`, `[social_provider, social_id]`

---

### 2. SNIPPETS

> Code snippet storage with versioning and social features

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `user_id` | UUID | FK → users, CASCADE | Owner |
| `team_id` | UUID | FK → teams, NULLABLE | Team (optional) |
| `category_id` | UUID | FK → categories, NULLABLE | Category |
| `title` | VARCHAR(255) | NOT NULL | Snippet title |
| `description` | TEXT | NULLABLE | Description |
| `code` | LONGTEXT | NOT NULL | The actual code |
| `highlighted_html` | LONGTEXT | NULLABLE | Syntax highlighted HTML |
| `language` | VARCHAR(50) | NOT NULL | Programming language |
| `privacy` | ENUM | DEFAULT 'public' | 'public', 'private', 'team', 'unlisted' |
| `slug` | VARCHAR(300) | UNIQUE | URL-friendly slug |
| `version_number` | INTEGER | DEFAULT 1 | Current version |
| `parent_snippet_id` | UUID | FK → snippets, NULLABLE | Forked from |
| `is_fork` | BOOLEAN | DEFAULT false | Is this a fork |
| `is_featured` | BOOLEAN | DEFAULT false | Featured flag |
| `allow_comments` | BOOLEAN | DEFAULT true | Comments enabled |
| `allow_forks` | BOOLEAN | DEFAULT true | Forks enabled |
| `license` | VARCHAR(50) | NULLABLE | License type |
| `view_count` | INTEGER | DEFAULT 0 | Total views |
| `unique_view_count` | INTEGER | DEFAULT 0 | Unique views |
| `fork_count` | INTEGER | DEFAULT 0 | Fork count |
| `favorite_count` | INTEGER | DEFAULT 0 | Favorite count |
| `comment_count` | INTEGER | DEFAULT 0 | Comment count |
| `share_count` | INTEGER | DEFAULT 0 | Share count |
| `trending_score` | FLOAT | DEFAULT 0 | Trending score |
| `published_at` | TIMESTAMP | NULLABLE | Publish date |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete |

**Indexes:** `user_id`, `team_id`, `language`, `privacy`, `slug`, `created_at`, `trending_score`, `is_featured`, `is_fork`, `parent_snippet_id`

---

### 3. TEAMS

> Team/organization management

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `name` | VARCHAR(100) | NOT NULL | Team name |
| `slug` | VARCHAR(120) | UNIQUE | URL slug |
| `description` | TEXT | NULLABLE | Description |
| `avatar_url` | VARCHAR(500) | NULLABLE | Team avatar |
| `owner_id` | UUID | FK → users, CASCADE | Team owner |
| `privacy` | ENUM | DEFAULT 'private' | 'public', 'private', 'invite_only' |
| `member_count` | INTEGER | DEFAULT 1 | Member count |
| `snippet_count` | INTEGER | DEFAULT 0 | Snippet count |
| `allow_member_invite` | BOOLEAN | DEFAULT true | Members can invite |
| `default_snippet_privacy` | ENUM | DEFAULT 'team' | Default snippet privacy |
| `is_active` | BOOLEAN | DEFAULT true | Team active |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete |

**Indexes:** `slug`, `owner_id`

---

### 4. TEAM_MEMBERS (Pivot)

> Team membership with role-based permissions

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `team_id` | UUID | FK → teams, CASCADE | Team |
| `user_id` | UUID | FK → users, CASCADE | User |
| `role` | ENUM | DEFAULT 'member' | 'owner', 'admin', 'member', 'viewer' |
| `can_create_snippets` | BOOLEAN | DEFAULT true | Permission |
| `can_edit_snippets` | BOOLEAN | DEFAULT false | Permission |
| `can_delete_snippets` | BOOLEAN | DEFAULT false | Permission |
| `can_manage_members` | BOOLEAN | DEFAULT false | Permission |
| `can_invite_members` | BOOLEAN | DEFAULT true | Permission |
| `invited_by` | UUID | FK → users, NULLABLE | Inviter |
| `joined_at` | TIMESTAMP | DEFAULT CURRENT | Join date |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Constraints:** UNIQUE(`team_id`, `user_id`)

---

### 5. TEAM_INVITATIONS

> Team invitation tokens

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `team_id` | UUID | FK → teams, CASCADE | Team |
| `invited_by` | UUID | FK → users, CASCADE | Inviter |
| `email` | VARCHAR(255) | NOT NULL | Invitee email |
| `user_id` | UUID | FK → users, NULLABLE | Invitee (if registered) |
| `role` | ENUM | DEFAULT 'member' | 'admin', 'member', 'viewer' |
| `token` | VARCHAR(255) | UNIQUE | Invitation token |
| `message` | TEXT | NULLABLE | Personal message |
| `status` | ENUM | DEFAULT 'pending' | 'pending', 'accepted', 'declined', 'expired' |
| `accepted_at` | TIMESTAMP | NULLABLE | Acceptance date |
| `declined_at` | TIMESTAMP | NULLABLE | Decline date |
| `expires_at` | TIMESTAMP | NOT NULL | Expiration date |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Indexes:** `team_id`, `email`, `token`, `status`

---

### 6. LANGUAGES

> Programming language definitions

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `name` | VARCHAR(50) | UNIQUE | Language name |
| `slug` | VARCHAR(60) | UNIQUE | URL slug |
| `display_name` | VARCHAR(100) | NOT NULL | Display name |
| `file_extensions` | JSON | NOT NULL | Array [".py", ".pyw"] |
| `pygments_lexer` | VARCHAR(100) | NOT NULL | Pygments lexer name |
| `monaco_language` | VARCHAR(50) | NULLABLE | Monaco editor language |
| `icon` | VARCHAR(50) | NULLABLE | Icon name |
| `color` | VARCHAR(7) | NULLABLE | Hex color (#3776AB) |
| `snippet_count` | INTEGER | DEFAULT 0 | Counter cache |
| `popularity_rank` | INTEGER | NULLABLE | Popularity rank |
| `is_active` | BOOLEAN | DEFAULT true | Active flag |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Indexes:** `slug`, `snippet_count`

---

### 7. CATEGORIES

> Hierarchical code categories

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `name` | VARCHAR(100) | UNIQUE | Category name |
| `slug` | VARCHAR(120) | UNIQUE | URL slug |
| `description` | TEXT | NULLABLE | Description |
| `icon` | VARCHAR(50) | NULLABLE | Icon name |
| `color` | VARCHAR(7) | NULLABLE | Hex color |
| `parent_category_id` | UUID | FK → categories, NULLABLE | Parent (self-ref) |
| `snippet_count` | INTEGER | DEFAULT 0 | Counter cache |
| `order` | INTEGER | DEFAULT 0 | Display order |
| `is_active` | BOOLEAN | DEFAULT true | Active flag |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Indexes:** `slug`, `order`

---

### 8. TAGS

> Snippet categorization tags

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `name` | VARCHAR(50) | UNIQUE | Tag name |
| `slug` | VARCHAR(60) | UNIQUE | URL slug |
| `description` | TEXT | NULLABLE | Description |
| `color` | VARCHAR(7) | NULLABLE | Hex color |
| `usage_count` | INTEGER | DEFAULT 0 | Usage counter |
| `is_official` | BOOLEAN | DEFAULT false | Official tag |
| `created_by` | UUID | FK → users, NULLABLE | Creator |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Indexes:** `slug`, `usage_count`

---

### 9. SNIPPET_TAG (Pivot - No Model)

> Many-to-many relationship between snippets and tags

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `snippet_id` | UUID | FK → snippets, PK | Snippet |
| `tag_id` | UUID | FK → tags, PK | Tag |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Constraints:** PRIMARY KEY(`snippet_id`, `tag_id`)

---

### 10. COLLECTIONS

> User-organized snippet collections

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `user_id` | UUID | FK → users, CASCADE | Owner |
| `name` | VARCHAR(255) | NOT NULL | Collection name |
| `slug` | VARCHAR(300) | NOT NULL | URL slug |
| `description` | TEXT | NULLABLE | Description |
| `cover_image_url` | VARCHAR(500) | NULLABLE | Cover image |
| `privacy` | ENUM | DEFAULT 'public' | 'public', 'private', 'unlisted' |
| `snippet_count` | INTEGER | DEFAULT 0 | Counter cache |
| `view_count` | INTEGER | DEFAULT 0 | View counter |
| `is_featured` | BOOLEAN | DEFAULT false | Featured flag |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete |

**Constraints:** UNIQUE(`user_id`, `slug`)

---

### 11. COLLECTION_SNIPPET (Pivot)

> Collection membership with ordering

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `collection_id` | UUID | FK → collections, CASCADE | Collection |
| `snippet_id` | UUID | FK → snippets, CASCADE | Snippet |
| `position` | INTEGER | DEFAULT 0 | Sort position |
| `note` | TEXT | NULLABLE | Personal note |
| `added_at` | TIMESTAMP | DEFAULT CURRENT | When added |

**Constraints:** UNIQUE(`collection_id`, `snippet_id`)

---

### 12. COMMENTS

> Nested comment system for snippets

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `snippet_id` | UUID | FK → snippets, CASCADE | Snippet |
| `user_id` | UUID | FK → users, CASCADE | Author |
| `parent_comment_id` | UUID | FK → comments, NULLABLE | Parent (for replies) |
| `content` | TEXT | NOT NULL | Comment content |
| `line_number` | INTEGER | NULLABLE | Code line reference |
| `is_edited` | BOOLEAN | DEFAULT false | Edited flag |
| `edited_at` | TIMESTAMP | NULLABLE | Edit date |
| `upvote_count` | INTEGER | DEFAULT 0 | Upvote counter |
| `reply_count` | INTEGER | DEFAULT 0 | Reply counter |
| `is_pinned` | BOOLEAN | DEFAULT false | Pinned flag |
| `is_resolved` | BOOLEAN | DEFAULT false | Resolved flag |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |
| `deleted_at` | TIMESTAMP | NULLABLE | Soft delete |

**Indexes:** `snippet_id`, `user_id`, `parent_comment_id`, `created_at`

---

### 13. FAVORITES (Pivot)

> User favorite snippets

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `user_id` | UUID | FK → users, CASCADE | User |
| `snippet_id` | UUID | FK → snippets, CASCADE | Snippet |
| `note` | TEXT | NULLABLE | Personal note |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Constraints:** UNIQUE(`user_id`, `snippet_id`)

---

### 14. FOLLOWS (Pivot)

> User follow relationships

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `follower_id` | UUID | FK → users, CASCADE | Follower |
| `following_id` | UUID | FK → users, CASCADE | Being followed |
| `notification_enabled` | BOOLEAN | DEFAULT true | Notify on activity |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Constraints:** UNIQUE(`follower_id`, `following_id`)

---

### 15. SNIPPET_VERSIONS

> Version history for snippets

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `snippet_id` | UUID | FK → snippets, CASCADE | Snippet |
| `version_number` | INTEGER | NOT NULL | Version number |
| `title` | VARCHAR(255) | NOT NULL | Title at version |
| `description` | TEXT | NULLABLE | Description at version |
| `code` | LONGTEXT | NOT NULL | Code at version |
| `language` | VARCHAR(50) | NOT NULL | Language at version |
| `change_summary` | TEXT | NULLABLE | Change summary |
| `change_type` | ENUM | DEFAULT 'update' | 'create', 'update', 'restore' |
| `lines_added` | INTEGER | DEFAULT 0 | Lines added |
| `lines_removed` | INTEGER | DEFAULT 0 | Lines removed |
| `created_by` | UUID | FK → users, CASCADE | Who made this |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Constraints:** UNIQUE(`snippet_id`, `version_number`)

---

### 16. SNIPPET_VIEWS

> View tracking for analytics

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `snippet_id` | UUID | FK → snippets, CASCADE | Snippet |
| `user_id` | UUID | FK → users, NULLABLE | Viewer (if logged in) |
| `session_id` | VARCHAR(255) | NULLABLE | Session ID |
| `ip_address` | VARCHAR(45) | NULLABLE | IP address |
| `user_agent` | TEXT | NULLABLE | Browser user agent |
| `referrer` | VARCHAR(500) | NULLABLE | Referrer URL |
| `country` | VARCHAR(2) | NULLABLE | Country code |
| `city` | VARCHAR(100) | NULLABLE | City name |
| `viewed_at` | TIMESTAMP | DEFAULT CURRENT | View timestamp |

**Note:** No `created_at` or `updated_at` columns

---

### 17. SHARES

> Snippet sharing with tokens

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `snippet_id` | UUID | FK → snippets, CASCADE | Snippet |
| `shared_by` | UUID | FK → users, CASCADE | Sharer |
| `shared_with` | UUID | FK → users, NULLABLE | Recipient user |
| `team_id` | UUID | FK → teams, NULLABLE | Recipient team |
| `share_type` | VARCHAR(20) | DEFAULT 'link' | 'link', 'user', 'team', 'email' |
| `share_token` | VARCHAR(64) | UNIQUE, NULLABLE | Share token |
| `permission` | VARCHAR(20) | DEFAULT 'view' | 'view', 'edit' |
| `email` | VARCHAR(255) | NULLABLE | Email (for email shares) |
| `expires_at` | TIMESTAMP | NULLABLE | Expiration date |
| `access_count` | INTEGER | DEFAULT 0 | Access counter |
| `last_accessed_at` | TIMESTAMP | NULLABLE | Last access |
| `is_active` | BOOLEAN | DEFAULT true | Active flag |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Indexes:** `share_token`, `shared_by`, `shared_with`, `share_type`

---

### 18. NOTIFICATIONS

> User notification system

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `user_id` | UUID | FK → users, CASCADE | Recipient |
| `type` | VARCHAR(50) | NOT NULL | Notification type |
| `title` | VARCHAR(255) | NOT NULL | Title |
| `message` | TEXT | NULLABLE | Message body |
| `link` | VARCHAR(500) | NULLABLE | Action link |
| `icon` | VARCHAR(50) | NULLABLE | Icon name |
| `actor_id` | UUID | FK → users, NULLABLE | Who triggered it |
| `related_resource_type` | VARCHAR(50) | NULLABLE | Resource type |
| `related_resource_id` | UUID | NULLABLE | Resource ID |
| `is_read` | BOOLEAN | DEFAULT false | Read flag |
| `read_at` | TIMESTAMP | NULLABLE | When read |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Indexes:** `user_id`, `is_read`, `created_at`

---

### 19. AUDIT_LOGS

> Admin action logging

| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| `id` | UUID | PK | Primary key |
| `user_id` | UUID | FK → users, NULLABLE | Actor |
| `action` | VARCHAR(50) | NOT NULL | Action performed |
| `resource_type` | VARCHAR(50) | NOT NULL | Resource type |
| `resource_id` | UUID | NULLABLE | Resource ID |
| `old_values` | JSON | NULLABLE | Previous values |
| `new_values` | JSON | NULLABLE | New values |
| `ip_address` | VARCHAR(45) | NULLABLE | IP address |
| `user_agent` | TEXT | NULLABLE | Browser user agent |
| `method` | VARCHAR(10) | NULLABLE | HTTP method |
| `endpoint` | VARCHAR(255) | NULLABLE | API endpoint |
| `status_code` | INTEGER | NULLABLE | Response status |
| `error_message` | TEXT | NULLABLE | Error message |
| `metadata` | JSON | NULLABLE | Additional data |
| `created_at` | TIMESTAMP | | Created date |
| `updated_at` | TIMESTAMP | | Updated date |

**Indexes:** `user_id`, `action`, `resource_type`, `created_at`

---

## Relationships Summary

### One-to-Many (1:N)

| Parent | Child | Foreign Key |
|--------|-------|-------------|
| User | Snippets | `snippets.user_id` |
| User | Teams (owned) | `teams.owner_id` |
| User | Collections | `collections.user_id` |
| User | Comments | `comments.user_id` |
| User | Notifications | `notifications.user_id` |
| User | Audit Logs | `audit_logs.user_id` |
| User | Shares (sent) | `shares.shared_by` |
| User | Tags (created) | `tags.created_by` |
| Team | Snippets | `snippets.team_id` |
| Team | Invitations | `team_invitations.team_id` |
| Snippet | Comments | `comments.snippet_id` |
| Snippet | Versions | `snippet_versions.snippet_id` |
| Snippet | Views | `snippet_views.snippet_id` |
| Snippet | Shares | `shares.snippet_id` |
| Snippet | Forks | `snippets.parent_snippet_id` |
| Category | Snippets | `snippets.category_id` |
| Category | Children | `categories.parent_category_id` |
| Comment | Replies | `comments.parent_comment_id` |

### Many-to-Many (N:M)

| Table A | Table B | Pivot Table |
|---------|---------|-------------|
| Users | Teams | `team_members` |
| Users | Snippets (favorites) | `favorites` |
| Users | Users (follows) | `follows` |
| Snippets | Tags | `snippet_tag` |
| Snippets | Collections | `collection_snippet` |

---

## Enums & Constants

### Privacy Levels

**Snippets:**
- `public` - Visible to everyone
- `private` - Only visible to owner
- `team` - Visible to team members
- `unlisted` - Accessible via direct link

**Collections:**
- `public` - Visible to everyone
- `private` - Only visible to owner
- `unlisted` - Accessible via direct link

**Teams:**
- `public` - Anyone can view
- `private` - Members only
- `invite_only` - By invitation only

### User Roles (Team Members)

- `owner` - Full control, can delete team
- `admin` - Manage members, edit settings
- `member` - Create snippets, participate
- `viewer` - Read-only access

### Share Types

- `link` - Public shareable link
- `user` - Shared with specific user
- `team` - Shared with team
- `email` - Shared via email

### Share Permissions

- `view` - Read-only access
- `edit` - Can modify snippet

### Invitation Status

- `pending` - Awaiting response
- `accepted` - Invitation accepted
- `declined` - Invitation declined
- `expired` - Invitation expired

### Version Change Types

- `create` - Initial creation
- `update` - Content update
- `restore` - Restored from previous version

---

## Notes for Your Team

1. **All primary keys are UUIDs** - Not auto-incrementing integers
2. **Soft deletes** are enabled on: Users, Snippets, Teams, Collections, Comments
3. **Slugs** are auto-generated from names/titles using Spatie Sluggable
4. **Counter caches** (like `snippet_count`, `view_count`) should be updated via model events or observers
5. **Timestamps** are automatically managed by Laravel
