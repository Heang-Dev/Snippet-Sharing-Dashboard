# Admin Dashboard API Documentation

Base URL: `/api/v1/admin`

**All endpoints require admin privileges.** Non-admin users will receive a 403 Forbidden response.

---

## Endpoints Overview

### Dashboard & Analytics
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/dashboard` | Get dashboard statistics |
| GET | `/admin/analytics` | Get analytics data |
| GET | `/admin/audit-logs` | Get audit logs |

### User Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/users` | List all users |
| GET | `/admin/users/{id}` | Get user details |
| PUT | `/admin/users/{id}` | Update user |
| DELETE | `/admin/users/{id}` | Delete user |

### Snippet Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/admin/snippets` | List all snippets |
| PUT | `/admin/snippets/{id}` | Update snippet |
| DELETE | `/admin/snippets/{id}` | Delete snippet |

### Comment Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| DELETE | `/admin/comments/{id}` | Delete comment |

### Language Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/admin/languages` | Create language |
| PUT | `/admin/languages/{id}` | Update language |
| DELETE | `/admin/languages/{id}` | Delete language |

### Category Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/admin/categories` | Create category |
| PUT | `/admin/categories/{id}` | Update category |
| DELETE | `/admin/categories/{id}` | Delete category |

---

## Get Dashboard Statistics

Get an overview of all system statistics.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/admin/dashboard` | Admin Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Dashboard statistics retrieved successfully.",
  "data": {
    "users": {
      "total": 1500,
      "active": 1450,
      "admins": 5,
      "new_this_week": 25,
      "new_this_month": 100
    },
    "snippets": {
      "total": 5000,
      "public": 3500,
      "private": 1500,
      "new_this_week": 150,
      "new_this_month": 500
    },
    "collections": {
      "total": 800,
      "public": 500
    },
    "comments": {
      "total": 3000,
      "new_this_week": 200
    },
    "teams": {
      "total": 50,
      "active": 48
    },
    "languages": 25,
    "categories": 15,
    "tags": 500
  }
}
```

---

## Get Analytics

Get analytics data over a time period.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/admin/analytics` | Admin Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| days | integer | No | 30 | Number of days (max: 365) |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Analytics retrieved successfully.",
  "data": {
    "period_days": 30,
    "users_growth": [
      {"date": "2024-01-01", "count": 5},
      {"date": "2024-01-02", "count": 3}
    ],
    "snippets_growth": [
      {"date": "2024-01-01", "count": 25},
      {"date": "2024-01-02", "count": 18}
    ],
    "top_languages": [
      {
        "language_id": "550e8400-e29b-41d4-a716-446655440000",
        "count": 1500,
        "language": {
          "id": "550e8400-e29b-41d4-a716-446655440000",
          "name": "JavaScript",
          "slug": "javascript"
        }
      }
    ],
    "most_active_users": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "username": "topuser",
        "full_name": "Top User",
        "avatar_url": "https://example.com/avatar.jpg",
        "snippets_count": 100,
        "comments_count": 250
      }
    ]
  }
}
```

---

## Get Audit Logs

Get system audit logs for monitoring actions.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/admin/audit-logs` | Admin Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| user_id | uuid | No | - | Filter by user |
| action | string | No | - | Filter by action type |
| resource_type | string | No | - | Filter by resource type |
| resource_id | uuid | No | - | Filter by resource ID |
| from | datetime | No | - | Start date filter |
| to | datetime | No | - | End date filter |
| per_page | integer | No | 50 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Audit logs retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "user_id": "550e8400-e29b-41d4-a716-446655440001",
      "action": "user_updated",
      "resource_type": "user",
      "resource_id": "550e8400-e29b-41d4-a716-446655440002",
      "old_values": {"is_active": true},
      "new_values": {"is_active": false},
      "ip_address": "192.168.1.1",
      "user_agent": "Mozilla/5.0...",
      "method": "PUT",
      "endpoint": "api/v1/admin/users/...",
      "metadata": {"admin_id": "550e8400-e29b-41d4-a716-446655440001"},
      "created_at": "2024-01-15T10:30:00.000000Z",
      "user": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "username": "admin",
        "full_name": "Admin User",
        "avatar_url": "https://example.com/admin.jpg"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 50,
    "total": 500
  }
}
```

---

## List All Users

Get all users with filtering and sorting.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/admin/users` | Admin Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by username, email, or name |
| is_active | boolean | No | - | Filter by active status |
| is_admin | boolean | No | - | Filter by admin status |
| email_verified | boolean | No | - | Filter by email verification |
| sort_by | string | No | created_at | Sort: `created_at`, `username`, `email`, `last_login_at`, `snippets_count` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Users retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "username": "johndoe",
      "email": "john@example.com",
      "full_name": "John Doe",
      "avatar_url": "https://example.com/avatar.jpg",
      "bio": "Developer",
      "is_admin": false,
      "is_active": true,
      "email_verified_at": "2024-01-01T10:00:00.000000Z",
      "last_login_at": "2024-01-15T08:00:00.000000Z",
      "created_at": "2024-01-01T10:00:00.000000Z",
      "snippets_count": 50,
      "collections_count": 5,
      "comments_count": 120,
      "followers_count": 75,
      "following_count": 45
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 75,
    "per_page": 20,
    "total": 1500
  }
}
```

---

## Get User Details

Get detailed information about a specific user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/admin/users/{id}` | Admin Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | User ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "User details retrieved successfully.",
  "data": {
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "username": "johndoe",
      "email": "john@example.com",
      "full_name": "John Doe",
      "is_admin": false,
      "is_active": true,
      "snippets_count": 50,
      "collections_count": 5,
      "comments_count": 120,
      "followers_count": 75,
      "following_count": 45,
      "teams_count": 3
    },
    "recent_snippets": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "title": "Recent Snippet",
        "slug": "recent-snippet",
        "visibility": "public",
        "created_at": "2024-01-15T10:00:00.000000Z"
      }
    ],
    "recent_comments": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440002",
        "snippet_id": "550e8400-e29b-41d4-a716-446655440003",
        "content": "Great snippet!",
        "created_at": "2024-01-14T15:00:00.000000Z",
        "snippet": {
          "id": "550e8400-e29b-41d4-a716-446655440003",
          "title": "Some Snippet",
          "slug": "some-snippet"
        }
      }
    ]
  }
}
```

---

## Update User

Update a user's admin-controlled fields.

| Method | URL | Auth |
|--------|-----|------|
| PUT | `/api/v1/admin/users/{id}` | Admin Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | User ID |

### Request Body

```json
{
  "is_active": false,
  "is_admin": true,
  "email_verified_at": "2024-01-15T10:00:00Z"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| is_active | boolean | No | User active status |
| is_admin | boolean | No | Admin privileges |
| email_verified_at | datetime | No | Email verification timestamp (null to unverify) |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "User updated successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "username": "johndoe",
    "is_active": false,
    "is_admin": true
  }
}
```

### Error Response (422 - Self demotion)

```json
{
  "success": false,
  "message": "You cannot remove your own admin privileges."
}
```

---

## Delete User

Permanently delete a user account.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/admin/users/{id}` | Admin Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | User ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "User deleted successfully."
}
```

### Error Response (422 - Self deletion)

```json
{
  "success": false,
  "message": "You cannot delete your own account."
}
```

---

## List All Snippets

Get all snippets with admin filtering.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/admin/snippets` | Admin Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search in title/description |
| visibility | string | No | - | Filter: `public`, `private`, `unlisted` |
| user_id | uuid | No | - | Filter by user |
| is_featured | boolean | No | - | Filter by featured |
| language_id | uuid | No | - | Filter by language |
| sort_by | string | No | created_at | Sort: `created_at`, `title`, `views_count`, `favorites_count` |
| sort_order | string | No | desc | Sort order |
| per_page | integer | No | 20 | Items per page |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Snippets retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "title": "Example Snippet",
      "slug": "example-snippet",
      "visibility": "public",
      "is_featured": false,
      "views_count": 100,
      "created_at": "2024-01-15T10:00:00.000000Z",
      "user": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      },
      "language": {
        "id": "550e8400-e29b-41d4-a716-446655440002",
        "name": "JavaScript",
        "slug": "javascript"
      },
      "favorites_count": 25,
      "comments_count": 10,
      "forks_count": 5
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 250,
    "per_page": 20,
    "total": 5000
  }
}
```

---

## Update Snippet (Admin)

Update snippet visibility or featured status.

| Method | URL | Auth |
|--------|-----|------|
| PUT | `/api/v1/admin/snippets/{id}` | Admin Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Snippet ID |

### Request Body

```json
{
  "is_featured": true,
  "visibility": "public"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| is_featured | boolean | No | Featured status |
| visibility | string | No | Visibility: `public`, `private`, `unlisted` |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Snippet updated successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "title": "Example Snippet",
    "is_featured": true,
    "visibility": "public"
  }
}
```

---

## Delete Snippet (Admin)

Permanently delete a snippet.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/admin/snippets/{id}` | Admin Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Snippet deleted successfully."
}
```

---

## Delete Comment (Admin)

Delete any comment.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/admin/comments/{id}` | Admin Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Comment deleted successfully."
}
```

---

## Create Language

Add a new programming language.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/admin/languages` | Admin Required |

### Request Body

```json
{
  "name": "Rust",
  "slug": "rust",
  "file_extension": ".rs",
  "mime_type": "text/x-rust",
  "icon": "rust-icon",
  "color": "#dea584",
  "is_active": true
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Language name (max: 50) |
| slug | string | Yes | URL slug (max: 50) |
| file_extension | string | No | File extension |
| mime_type | string | No | MIME type |
| icon | string | No | Icon identifier |
| color | string | No | Color code |
| is_active | boolean | No | Active status (default: true) |

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Language created successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Rust",
    "slug": "rust",
    "file_extension": ".rs",
    "mime_type": "text/x-rust",
    "icon": "rust-icon",
    "color": "#dea584",
    "is_active": true
  }
}
```

---

## Update Language

Update an existing language.

| Method | URL | Auth |
|--------|-----|------|
| PUT | `/api/v1/admin/languages/{id}` | Admin Required |

Same fields as create, all optional.

---

## Delete Language

Delete a language (only if not in use).

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/admin/languages/{id}` | Admin Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Language deleted successfully."
}
```

### Error Response (422 - In use)

```json
{
  "success": false,
  "message": "Cannot delete language. It is used by 150 snippets."
}
```

---

## Create Category

Add a new category.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/admin/categories` | Admin Required |

### Request Body

```json
{
  "name": "Web Development",
  "slug": "web-development",
  "description": "Web development snippets",
  "icon": "globe",
  "parent_id": null,
  "sort_order": 1,
  "is_active": true
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Category name (max: 100) |
| slug | string | Yes | URL slug (max: 100) |
| description | string | No | Description (max: 500) |
| icon | string | No | Icon identifier |
| parent_id | uuid | No | Parent category ID |
| sort_order | integer | No | Display order |
| is_active | boolean | No | Active status |

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Category created successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "name": "Web Development",
    "slug": "web-development"
  }
}
```

---

## Update Category

Update an existing category.

| Method | URL | Auth |
|--------|-----|------|
| PUT | `/api/v1/admin/categories/{id}` | Admin Required |

Same fields as create, all optional.

### Error Response (422 - Self reference)

```json
{
  "success": false,
  "message": "Category cannot be its own parent."
}
```

---

## Delete Category

Delete a category (only if empty).

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/admin/categories/{id}` | Admin Required |

### Error Responses

**Has children:**
```json
{
  "success": false,
  "message": "Cannot delete category. It has 5 child categories."
}
```

**In use:**
```json
{
  "success": false,
  "message": "Cannot delete category. It is used by 50 snippets."
}
```

---

## Error Responses

### 403 Forbidden (Not Admin)

```json
{
  "success": false,
  "message": "Access denied. Admin privileges required."
}
```

### 401 Unauthorized

```json
{
  "message": "Unauthenticated."
}
```

### 404 Not Found

```json
{
  "success": false,
  "message": "Resource not found."
}
```

---

## Quick Reference

### Dashboard
```
GET /api/v1/admin/dashboard     - Overview statistics
GET /api/v1/admin/analytics     - Analytics data
GET /api/v1/admin/audit-logs    - Audit logs
```

### Users
```
GET    /api/v1/admin/users           - List users
GET    /api/v1/admin/users/{id}      - User details
PUT    /api/v1/admin/users/{id}      - Update user
DELETE /api/v1/admin/users/{id}      - Delete user
```

### Snippets
```
GET    /api/v1/admin/snippets           - List snippets
PUT    /api/v1/admin/snippets/{id}      - Update snippet
DELETE /api/v1/admin/snippets/{id}      - Delete snippet
```

### Comments
```
DELETE /api/v1/admin/comments/{id}      - Delete comment
```

### Languages
```
POST   /api/v1/admin/languages          - Create language
PUT    /api/v1/admin/languages/{id}     - Update language
DELETE /api/v1/admin/languages/{id}     - Delete language
```

### Categories
```
POST   /api/v1/admin/categories         - Create category
PUT    /api/v1/admin/categories/{id}    - Update category
DELETE /api/v1/admin/categories/{id}    - Delete category
```
