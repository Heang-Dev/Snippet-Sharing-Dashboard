# Entity Relationship Diagram

> **Snippet Sharing Platform - Database Relationships**

---

## Visual ER Diagram

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                    SNIPPET SHARING PLATFORM - ER DIAGRAM                                │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘


                                         ┌─────────────────────┐
                                         │       USERS         │
                                         │─────────────────────│
                                         │ PK: id (UUID)       │
                                         │ username (UNIQUE)   │
                                         │ email (UNIQUE)      │
                                         │ password            │
                                         │ full_name           │
                                         │ bio                 │
                                         │ avatar_url          │
                                         │ is_admin            │
                                         │ is_active           │
                                         │ profile_visibility  │
                                         │ theme_preference    │
                                         │ social_provider     │
                                         │ social_id           │
                                         │ ...counters         │
                                         └──────────┬──────────┘
                                                    │
                    ┌──────────────────┬────────────┼────────────┬──────────────────┐
                    │                  │            │            │                  │
                    │ 1:N              │ 1:N        │ N:M        │ 1:N              │ 1:N
                    ▼                  ▼            ▼            ▼                  ▼
         ┌─────────────────┐  ┌─────────────────┐  │   ┌─────────────────┐  ┌─────────────────┐
         │    SNIPPETS     │  │     TEAMS       │  │   │   COLLECTIONS   │  │   COMMENTS      │
         │─────────────────│  │─────────────────│  │   │─────────────────│  │─────────────────│
         │ PK: id          │  │ PK: id          │  │   │ PK: id          │  │ PK: id          │
         │ FK: user_id ────┼──│ FK: owner_id ───┼──┘   │ FK: user_id ────┼──│ FK: user_id ────│
         │ FK: team_id ────┼──│ name            │      │ name            │  │ FK: snippet_id  │
         │ FK: category_id │  │ slug            │      │ slug            │  │ FK: parent_id   │
         │ title           │  │ description     │      │ description     │  │ content         │
         │ code            │  │ privacy         │      │ privacy         │  │ line_number     │
         │ language        │  │ member_count    │      │ snippet_count   │  │ is_edited       │
         │ privacy         │  │ is_active       │      │ view_count      │  │ upvote_count    │
         │ slug            │  │                 │      │ is_featured     │  │ is_pinned       │
         │ view_count      │  │                 │      │                 │  │ is_resolved     │
         │ favorite_count  │  │                 │      │                 │  │                 │
         │ is_featured     │  │                 │      │                 │  │                 │
         └────────┬────────┘  └────────┬────────┘      └────────┬────────┘  └─────────────────┘
                  │                    │                        │
                  │                    │                        │
    ┌─────────────┼─────────────┐      │               ┌────────┴────────┐
    │             │             │      │               │                 │
    ▼             ▼             ▼      ▼               ▼                 │
┌───────────┐ ┌───────────┐ ┌───────────────┐  ┌─────────────────┐      │
│SNIPPET_TAG│ │ FAVORITES │ │SNIPPET_VERSIONS│  │ TEAM_MEMBERS    │      │
│  (Pivot)  │ │  (Pivot)  │ │───────────────│  │     (Pivot)     │      │
│───────────│ │───────────│ │ PK: id        │  │─────────────────│      │
│ snippet_id│ │ PK: id    │ │ FK: snippet_id│  │ PK: id          │      │
│ tag_id    │ │ user_id   │ │ version_number│  │ FK: team_id     │      │
│           │ │ snippet_id│ │ title         │  │ FK: user_id     │      │
│           │ │ note      │ │ code          │  │ role            │      │
└─────┬─────┘ └───────────┘ │ change_type   │  │ can_create      │      │
      │                     │ lines_added   │  │ can_edit        │      │
      ▼                     │ FK: created_by│  │ can_delete      │      │
┌───────────┐               └───────────────┘  │ joined_at       │      │
│   TAGS    │                                  └─────────────────┘      │
│───────────│                                                           │
│ PK: id    │                                  ┌─────────────────┐      │
│ name      │                                  │TEAM_INVITATIONS │      │
│ slug      │                                  │─────────────────│      │
│ color     │                                  │ PK: id          │      │
│usage_count│                                  │ FK: team_id     │      │
│is_official│                                  │ FK: invited_by  │      │
│FK:created │                                  │ email           │      │
└───────────┘                                  │ token           │      │
                                               │ status          │      │
                                               │ expires_at      │      │
                                               └─────────────────┘      │
                                                                        │
                                               ┌────────────────────────┘
                                               │
                                               ▼
                                        ┌─────────────────┐
                                        │COLLECTION_SNIPPET│
                                        │     (Pivot)     │
                                        │─────────────────│
                                        │ PK: id          │
                                        │ FK: collection_id│
                                        │ FK: snippet_id  │
                                        │ position        │
                                        │ note            │
                                        │ added_at        │
                                        └─────────────────┘


┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│                                        ADDITIONAL TABLES                                                 │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   CATEGORIES    │     │   LANGUAGES     │     │    FOLLOWS      │     │ NOTIFICATIONS   │
│─────────────────│     │─────────────────│     │    (Pivot)      │     │─────────────────│
│ PK: id          │     │ PK: id          │     │─────────────────│     │ PK: id          │
│ name            │     │ name            │     │ PK: id          │     │ FK: user_id     │
│ slug            │     │ slug            │     │ FK: follower_id │     │ FK: actor_id    │
│ description     │     │ display_name    │     │ FK: following_id│     │ type            │
│ icon            │     │ file_extensions │     │ notification_on │     │ title           │
│ color           │     │ pygments_lexer  │     │                 │     │ message         │
│ FK: parent_id   │◄────│ monaco_language │     │                 │     │ is_read         │
│ snippet_count   │     │ icon            │     │                 │     │ read_at         │
│ order           │     │ color           │     │                 │     │                 │
│ is_active       │     │ snippet_count   │     └─────────────────┘     └─────────────────┘
└─────────────────┘     │ is_active       │
                        └─────────────────┘

┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│    SHARES       │     │ SNIPPET_VIEWS   │     │  AUDIT_LOGS     │
│─────────────────│     │─────────────────│     │─────────────────│
│ PK: id          │     │ PK: id          │     │ PK: id          │
│ FK: snippet_id  │     │ FK: snippet_id  │     │ FK: user_id     │
│ FK: shared_by   │     │ FK: user_id     │     │ action          │
│ FK: shared_with │     │ session_id      │     │ resource_type   │
│ FK: team_id     │     │ ip_address      │     │ resource_id     │
│ share_type      │     │ user_agent      │     │ old_values      │
│ share_token     │     │ referrer        │     │ new_values      │
│ permission      │     │ country         │     │ ip_address      │
│ email           │     │ city            │     │ user_agent      │
│ expires_at      │     │ viewed_at       │     │ method          │
│ access_count    │     │                 │     │ endpoint        │
│ is_active       │     │                 │     │ status_code     │
└─────────────────┘     └─────────────────┘     └─────────────────┘
```

---

## Relationship Lines Explained

| Symbol | Meaning |
|--------|---------|
| `──────` | One-to-One (1:1) |
| `──────<` | One-to-Many (1:N) |
| `>──────<` | Many-to-Many (N:M) via pivot |
| `- - - -` | Optional relationship |

---

## Cardinality Summary

### One-to-Many (1:N)

```
User ─────────< Snippets         (A user has many snippets)
User ─────────< Teams            (A user owns many teams)
User ─────────< Collections      (A user has many collections)
User ─────────< Comments         (A user has many comments)
User ─────────< Notifications    (A user has many notifications)
User ─────────< AuditLogs        (A user has many audit logs)
User ─────────< Shares (sent)    (A user sends many shares)
User ─────────< Tags             (A user creates many tags)

Team ─────────< Snippets         (A team has many snippets)
Team ─────────< Invitations      (A team has many invitations)
Team ─────────< Shares           (A team receives many shares)

Snippet ──────< Comments         (A snippet has many comments)
Snippet ──────< Versions         (A snippet has many versions)
Snippet ──────< Views            (A snippet has many views)
Snippet ──────< Shares           (A snippet has many shares)
Snippet ──────< Forks            (A snippet has many forks)

Category ─────< Snippets         (A category has many snippets)
Category ─────< Children         (A category has many sub-categories)

Comment ──────< Replies          (A comment has many replies)
```

### Many-to-Many (N:M)

```
Users >────────< Teams           via team_members
Users >────────< Snippets        via favorites
Users >────────< Users           via follows (self-referencing)
Snippets >─────< Tags            via snippet_tag
Snippets >─────< Collections     via collection_snippet
```

---

## Self-Referencing Relationships

### Categories (Hierarchical)
```
categories.parent_category_id → categories.id
```

### Comments (Nested/Threaded)
```
comments.parent_comment_id → comments.id
```

### Snippets (Forks)
```
snippets.parent_snippet_id → snippets.id
```

### Follows (User-to-User)
```
follows.follower_id → users.id
follows.following_id → users.id
```

---

## Foreign Key Cascade Rules

| Table | Foreign Key | On Delete |
|-------|-------------|-----------|
| snippets | user_id | CASCADE |
| snippets | team_id | SET NULL |
| snippets | category_id | SET NULL |
| snippets | parent_snippet_id | SET NULL |
| teams | owner_id | CASCADE |
| team_members | team_id | CASCADE |
| team_members | user_id | CASCADE |
| team_invitations | team_id | CASCADE |
| team_invitations | invited_by | CASCADE |
| collections | user_id | CASCADE |
| collection_snippet | collection_id | CASCADE |
| collection_snippet | snippet_id | CASCADE |
| comments | snippet_id | CASCADE |
| comments | user_id | CASCADE |
| comments | parent_comment_id | CASCADE |
| favorites | user_id | CASCADE |
| favorites | snippet_id | CASCADE |
| follows | follower_id | CASCADE |
| follows | following_id | CASCADE |
| snippet_versions | snippet_id | CASCADE |
| snippet_versions | created_by | CASCADE |
| snippet_views | snippet_id | CASCADE |
| snippet_views | user_id | SET NULL |
| shares | snippet_id | CASCADE |
| shares | shared_by | CASCADE |
| shares | shared_with | CASCADE |
| shares | team_id | CASCADE |
| notifications | user_id | CASCADE |
| notifications | actor_id | SET NULL |
| audit_logs | user_id | SET NULL |
| tags | created_by | SET NULL |
| categories | parent_category_id | SET NULL |

---

## Quick Reference Table

| # | Model | Table | Primary Key | Soft Delete | Slug |
|---|-------|-------|-------------|-------------|------|
| 1 | User | users | UUID | Yes | No |
| 2 | Snippet | snippets | UUID | Yes | Yes |
| 3 | Team | teams | UUID | Yes | Yes |
| 4 | TeamMember | team_members | UUID | No | No |
| 5 | TeamInvitation | team_invitations | UUID | No | No |
| 6 | Language | languages | UUID | No | Yes |
| 7 | Category | categories | UUID | No | Yes |
| 8 | Tag | tags | UUID | No | Yes |
| 9 | Collection | collections | UUID | Yes | Yes |
| 10 | CollectionSnippet | collection_snippet | UUID | No | No |
| 11 | Comment | comments | UUID | Yes | No |
| 12 | Favorite | favorites | UUID | No | No |
| 13 | Follow | follows | UUID | No | No |
| 14 | SnippetVersion | snippet_versions | UUID | No | No |
| 15 | SnippetView | snippet_views | UUID | No | No |
| 16 | Share | shares | UUID | No | No |
| 17 | Notification | notifications | UUID | No | No |
| 18 | AuditLog | audit_logs | UUID | No | No |

---

## Tools for Visualizing

You can use these tools to create a more graphical ER diagram:

1. **[dbdiagram.io](https://dbdiagram.io)** - Free online tool
2. **[draw.io](https://draw.io)** - Free diagramming tool
3. **[Lucidchart](https://lucidchart.com)** - Professional diagrams
4. **[MySQL Workbench](https://www.mysql.com/products/workbench/)** - Database design tool
5. **[Laravel ER Diagram Generator](https://github.com/beyondcode/laravel-er-diagram-generator)** - Auto-generate from models

### dbdiagram.io Code

Copy this code to dbdiagram.io for a visual diagram:

```dbml
Table users {
  id uuid [pk]
  username varchar(50) [unique]
  email varchar(255) [unique]
  password varchar(255)
  full_name varchar(255)
  is_admin boolean
  is_active boolean
  created_at timestamp
  deleted_at timestamp
}

Table snippets {
  id uuid [pk]
  user_id uuid [ref: > users.id]
  team_id uuid [ref: > teams.id]
  category_id uuid [ref: > categories.id]
  parent_snippet_id uuid [ref: > snippets.id]
  title varchar(255)
  code longtext
  language varchar(50)
  privacy enum
  slug varchar(300) [unique]
  view_count integer
  created_at timestamp
  deleted_at timestamp
}

Table teams {
  id uuid [pk]
  owner_id uuid [ref: > users.id]
  name varchar(100)
  slug varchar(120) [unique]
  privacy enum
  is_active boolean
  created_at timestamp
  deleted_at timestamp
}

Table team_members {
  id uuid [pk]
  team_id uuid [ref: > teams.id]
  user_id uuid [ref: > users.id]
  role enum
  created_at timestamp
}

Table collections {
  id uuid [pk]
  user_id uuid [ref: > users.id]
  name varchar(255)
  slug varchar(300)
  privacy enum
  created_at timestamp
  deleted_at timestamp
}

Table collection_snippet {
  id uuid [pk]
  collection_id uuid [ref: > collections.id]
  snippet_id uuid [ref: > snippets.id]
  position integer
}

Table tags {
  id uuid [pk]
  name varchar(50) [unique]
  slug varchar(60) [unique]
  usage_count integer
  created_by uuid [ref: > users.id]
}

Table snippet_tag {
  snippet_id uuid [ref: > snippets.id]
  tag_id uuid [ref: > tags.id]
}

Table comments {
  id uuid [pk]
  snippet_id uuid [ref: > snippets.id]
  user_id uuid [ref: > users.id]
  parent_comment_id uuid [ref: > comments.id]
  content text
  created_at timestamp
  deleted_at timestamp
}

Table favorites {
  id uuid [pk]
  user_id uuid [ref: > users.id]
  snippet_id uuid [ref: > snippets.id]
}

Table follows {
  id uuid [pk]
  follower_id uuid [ref: > users.id]
  following_id uuid [ref: > users.id]
}

Table categories {
  id uuid [pk]
  name varchar(100) [unique]
  slug varchar(120) [unique]
  parent_category_id uuid [ref: > categories.id]
}

Table languages {
  id uuid [pk]
  name varchar(50) [unique]
  slug varchar(60) [unique]
  display_name varchar(100)
}

Table notifications {
  id uuid [pk]
  user_id uuid [ref: > users.id]
  actor_id uuid [ref: > users.id]
  type varchar(50)
  is_read boolean
}

Table shares {
  id uuid [pk]
  snippet_id uuid [ref: > snippets.id]
  shared_by uuid [ref: > users.id]
  shared_with uuid [ref: > users.id]
  team_id uuid [ref: > teams.id]
  share_token varchar(64) [unique]
}

Table snippet_versions {
  id uuid [pk]
  snippet_id uuid [ref: > snippets.id]
  created_by uuid [ref: > users.id]
  version_number integer
  code longtext
}

Table snippet_views {
  id uuid [pk]
  snippet_id uuid [ref: > snippets.id]
  user_id uuid [ref: > users.id]
  viewed_at timestamp
}

Table audit_logs {
  id uuid [pk]
  user_id uuid [ref: > users.id]
  action varchar(50)
  resource_type varchar(50)
}

Table team_invitations {
  id uuid [pk]
  team_id uuid [ref: > teams.id]
  invited_by uuid [ref: > users.id]
  email varchar(255)
  token varchar(255) [unique]
  status enum
}
```

---

## Notes

- All tables use **UUID** primary keys
- **Soft deletes** enabled on: users, snippets, teams, collections, comments
- **Timestamps** are managed automatically by Laravel
- **Slugs** are auto-generated using Spatie Sluggable package
