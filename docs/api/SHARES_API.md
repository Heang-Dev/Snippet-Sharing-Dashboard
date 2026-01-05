# Shares API Documentation

Base URL: `/api/v1`

All endpoints except token access require Bearer token in the Authorization header.

---

## Endpoints Overview

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/shares/token/{token}` | Access shared snippet by token | No |
| GET | `/shares/with-me` | Get snippets shared with me | Yes |
| GET | `/shares/by-me` | Get snippets I've shared | Yes |
| GET | `/shares/{id}` | Get share details | Yes |
| PUT | `/shares/{id}` | Update share | Yes |
| DELETE | `/shares/{id}` | Delete share | Yes |
| POST | `/shares/{id}/regenerate-token` | Regenerate share token | Yes |
| GET | `/snippets/{snippetId}/shares` | Get shares for snippet | Yes |
| POST | `/snippets/{snippetId}/shares` | Create new share | Yes |
| POST | `/snippets/{snippetId}/shares/revoke-all` | Revoke all shares | Yes |

---

## Share Types

| Type | Description |
|------|-------------|
| `link` | Shareable link with token |
| `user` | Share with specific user |
| `team` | Share with team |
| `email` | Share via email (generates token) |

## Permissions

| Permission | Description |
|------------|-------------|
| `view` | Can view the snippet |
| `edit` | Can view and edit the snippet |

---

## Access Shared Snippet by Token

Access a snippet that was shared via link or email. No authentication required.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/shares/token/{token}` | Not Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| token | string | Yes | The share token (64 characters) |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Shared snippet retrieved successfully.",
  "data": {
    "snippet": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "title": "My Code Snippet",
      "description": "A helpful snippet",
      "code": "console.log('Hello World');",
      "language": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "name": "JavaScript",
        "slug": "javascript"
      },
      "tags": [],
      "user": {
        "id": "550e8400-e29b-41d4-a716-446655440002",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      }
    },
    "permission": "view",
    "shared_by": {
      "id": "550e8400-e29b-41d4-a716-446655440002",
      "username": "johndoe",
      "full_name": "John Doe"
    },
    "expires_at": "2024-02-15T10:30:00.000000Z"
  }
}
```

### Error Responses

**404 Not Found - Invalid token**
```json
{
  "success": false,
  "message": "Invalid share link."
}
```

**403 Forbidden - Expired or inactive**
```json
{
  "success": false,
  "message": "This share link has expired."
}
```

---

## Get Snippets Shared With Me

Get all snippets that have been shared with the authenticated user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/shares/with-me` | Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| permission | string | No | - | Filter by permission: `view`, `edit` |
| sort_by | string | No | created_at | Sort field: `created_at`, `last_accessed_at` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Shared snippets retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
      "share_type": "user",
      "permission": "view",
      "expires_at": null,
      "access_count": 5,
      "last_accessed_at": "2024-01-15T10:30:00.000000Z",
      "created_at": "2024-01-10T08:00:00.000000Z",
      "snippet": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "title": "Shared Snippet",
        "language": {
          "id": "550e8400-e29b-41d4-a716-446655440002",
          "name": "Python",
          "slug": "python"
        },
        "user": {
          "id": "550e8400-e29b-41d4-a716-446655440003",
          "username": "codemaster",
          "full_name": "Code Master",
          "avatar_url": "https://example.com/avatar2.jpg"
        }
      },
      "shared_by": {
        "id": "550e8400-e29b-41d4-a716-446655440003",
        "username": "codemaster",
        "full_name": "Code Master",
        "avatar_url": "https://example.com/avatar2.jpg"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 20,
    "total": 25
  }
}
```

---

## Get Snippets I've Shared

Get all shares created by the authenticated user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/shares/by-me` | Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| type | string | No | - | Filter by share type: `link`, `user`, `team`, `email` |
| is_active | boolean | No | - | Filter by active status |
| sort_by | string | No | created_at | Sort field: `created_at`, `last_accessed_at`, `access_count` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Your shared snippets retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
      "share_type": "link",
      "share_token": "abc123...",
      "permission": "view",
      "expires_at": "2024-02-15T10:30:00.000000Z",
      "access_count": 10,
      "last_accessed_at": "2024-01-15T10:30:00.000000Z",
      "is_active": true,
      "created_at": "2024-01-10T08:00:00.000000Z",
      "snippet": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "title": "My Shared Snippet",
        "slug": "my-shared-snippet"
      },
      "shared_with": null,
      "team": null
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 5
  }
}
```

---

## Get Shares for a Snippet

Get all shares for a specific snippet (owner only).

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/snippets/{snippetId}/shares` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| type | string | No | - | Filter by share type |
| is_active | boolean | No | - | Filter by active status |
| sort_by | string | No | created_at | Sort field: `created_at`, `last_accessed_at`, `access_count` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Shares retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "share_type": "user",
      "permission": "view",
      "expires_at": null,
      "access_count": 3,
      "is_active": true,
      "created_at": "2024-01-10T08:00:00.000000Z",
      "shared_with": {
        "id": "550e8400-e29b-41d4-a716-446655440002",
        "username": "janedoe",
        "full_name": "Jane Doe",
        "avatar_url": "https://example.com/jane.jpg"
      },
      "team": null
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 3
  }
}
```

### Error Response (403 Forbidden)

```json
{
  "success": false,
  "message": "You do not have permission to view shares for this snippet."
}
```

---

## Create Share

Create a new share for a snippet.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/snippets/{snippetId}/shares` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |

### Request Body

**Link Share:**
```json
{
  "share_type": "link",
  "permission": "view",
  "expires_at": "2024-02-15T10:30:00Z"
}
```

**User Share:**
```json
{
  "share_type": "user",
  "user_id": "550e8400-e29b-41d4-a716-446655440002",
  "permission": "edit"
}
```

**Team Share:**
```json
{
  "share_type": "team",
  "team_id": "550e8400-e29b-41d4-a716-446655440003",
  "permission": "view"
}
```

**Email Share:**
```json
{
  "share_type": "email",
  "email": "recipient@example.com",
  "permission": "view",
  "expires_at": "2024-02-15T10:30:00Z"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| share_type | string | Yes | Share type: `link`, `user`, `team`, `email` |
| permission | string | No | Permission level: `view` (default), `edit` |
| expires_at | datetime | No | Expiration date (ISO 8601 format) |
| user_id | uuid | Conditional | Required for `user` type |
| team_id | uuid | Conditional | Required for `team` type |
| email | string | Conditional | Required for `email` type |

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Snippet shared successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
    "share_type": "link",
    "share_token": "abc123def456ghi789jkl012mno345pqr678stu901vwx234yz567abc890def123",
    "permission": "view",
    "expires_at": "2024-02-15T10:30:00.000000Z",
    "access_count": 0,
    "last_accessed_at": null,
    "is_active": true,
    "created_at": "2024-01-15T08:00:00.000000Z"
  }
}
```

### Error Responses

**403 Forbidden - Not owner**
```json
{
  "success": false,
  "message": "You do not have permission to share this snippet."
}
```

**422 Unprocessable Entity - Already shared**
```json
{
  "success": false,
  "message": "This snippet is already shared with this recipient.",
  "data": {
    "id": "existing-share-id",
    "share_type": "user"
  }
}
```

**422 Unprocessable Entity - Self share**
```json
{
  "success": false,
  "message": "You cannot share a snippet with yourself."
}
```

---

## Get Share Details

Get details of a specific share.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/shares/{id}` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Share ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Share retrieved successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
    "share_type": "user",
    "share_token": null,
    "permission": "view",
    "email": null,
    "expires_at": null,
    "access_count": 5,
    "last_accessed_at": "2024-01-15T10:30:00.000000Z",
    "is_active": true,
    "created_at": "2024-01-10T08:00:00.000000Z",
    "snippet": {
      "id": "550e8400-e29b-41d4-a716-446655440001",
      "title": "My Snippet",
      "slug": "my-snippet",
      "user_id": "550e8400-e29b-41d4-a716-446655440002"
    },
    "shared_with": {
      "id": "550e8400-e29b-41d4-a716-446655440003",
      "username": "janedoe",
      "full_name": "Jane Doe",
      "avatar_url": "https://example.com/jane.jpg"
    },
    "team": null
  }
}
```

---

## Update Share

Update a share's permission or expiration.

| Method | URL | Auth |
|--------|-----|------|
| PUT | `/api/v1/shares/{id}` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Share ID |

### Request Body

```json
{
  "permission": "edit",
  "expires_at": "2024-03-15T10:30:00Z",
  "is_active": true
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| permission | string | No | Permission level: `view`, `edit` |
| expires_at | datetime | No | New expiration date (null to remove) |
| is_active | boolean | No | Active status |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Share updated successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "permission": "edit",
    "expires_at": "2024-03-15T10:30:00.000000Z",
    "is_active": true
  }
}
```

---

## Delete Share

Delete a share permanently.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/shares/{id}` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Share ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Share deleted successfully."
}
```

---

## Regenerate Share Token

Regenerate the token for a link or email share. This invalidates the old token.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/shares/{id}/regenerate-token` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Share ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Share token regenerated successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "share_token": "new123token456here789updated012abc345def678ghi901jkl234mno567pqr890",
    "access_count": 0,
    "last_accessed_at": null
  }
}
```

### Error Response (422 - No token type)

```json
{
  "success": false,
  "message": "This share type does not have a token."
}
```

---

## Revoke All Shares

Deactivate all shares for a snippet.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/snippets/{snippetId}/shares/revoke-all` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Revoked 5 shares.",
  "data": {
    "revoked_count": 5
  }
}
```

---

## Error Responses

### 401 Unauthorized

```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden

```json
{
  "success": false,
  "message": "You do not have permission to perform this action."
}
```

### 404 Not Found

```json
{
  "success": false,
  "message": "Share not found."
}
```

### 422 Validation Error

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "share_type": ["The share type field is required."],
    "user_id": ["The user id field is required when share type is user."]
  }
}
```

---

## Quick Reference

### Public Access
```
GET /api/v1/shares/token/{token}           - Access via share link
```

### My Shares
```
GET /api/v1/shares/with-me                 - Snippets shared with me
GET /api/v1/shares/by-me                   - Snippets I've shared
```

### Share Management
```
GET    /api/v1/shares/{id}                 - Get share details
PUT    /api/v1/shares/{id}                 - Update share
DELETE /api/v1/shares/{id}                 - Delete share
POST   /api/v1/shares/{id}/regenerate-token - Regenerate token
```

### Snippet Shares
```
GET  /api/v1/snippets/{snippetId}/shares           - List shares
POST /api/v1/snippets/{snippetId}/shares           - Create share
POST /api/v1/snippets/{snippetId}/shares/revoke-all - Revoke all
```
