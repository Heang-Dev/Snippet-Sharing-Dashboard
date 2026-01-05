# Snippet Versions API Documentation

Base URL: `/api/v1`

All endpoints require Bearer token in the Authorization header.

---

## Endpoints Overview

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/snippets/{snippetId}/versions` | Get all versions | Yes |
| GET | `/snippets/{snippetId}/versions/stats` | Get version statistics | Yes |
| GET | `/snippets/{snippetId}/versions/latest` | Get latest version | Yes |
| GET | `/snippets/{snippetId}/versions/compare` | Compare two versions | Yes |
| GET | `/snippets/{snippetId}/versions/number/{n}` | Get version by number | Yes |
| GET | `/snippets/{snippetId}/versions/{versionId}` | Get version by ID | Yes |
| POST | `/snippets/{snippetId}/versions/{versionId}/restore` | Restore a version | Yes |

---

## Change Types

| Type | Description |
|------|-------------|
| `create` | Initial version (snippet created) |
| `update` | Normal edit/update |
| `restore` | Restored from a previous version |

---

## Get All Versions

Get the version history for a snippet.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/snippets/{snippetId}/versions` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| change_type | string | No | - | Filter by change type: `create`, `update`, `restore` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Versions retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
      "version_number": 3,
      "title": "My Updated Snippet",
      "description": "A helpful code snippet",
      "code": "console.log('Hello World v3');",
      "language": "javascript",
      "change_summary": "Updated console output",
      "change_type": "update",
      "lines_added": 1,
      "lines_removed": 1,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "created_by": {
        "id": "550e8400-e29b-41d4-a716-446655440002",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      }
    },
    {
      "id": "550e8400-e29b-41d4-a716-446655440003",
      "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
      "version_number": 2,
      "title": "My Snippet",
      "description": "A helpful code snippet",
      "code": "console.log('Hello World v2');",
      "language": "javascript",
      "change_summary": "Fixed typo",
      "change_type": "update",
      "lines_added": 1,
      "lines_removed": 1,
      "created_at": "2024-01-14T15:00:00.000000Z",
      "created_by": {
        "id": "550e8400-e29b-41d4-a716-446655440002",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 3,
    "latest_version": 3
  }
}
```

### Error Response (403 Forbidden)

```json
{
  "success": false,
  "message": "You do not have permission to view this snippet's versions."
}
```

---

## Get Version Statistics

Get statistics about the version history.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/snippets/{snippetId}/versions/stats` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Version statistics retrieved successfully.",
  "data": {
    "total_versions": 15,
    "latest_version_number": 15,
    "first_created_at": "2024-01-01T08:00:00.000000Z",
    "last_updated_at": "2024-01-15T10:30:00.000000Z",
    "total_lines_added": 250,
    "total_lines_removed": 180,
    "change_type_counts": {
      "create": 1,
      "update": 13,
      "restore": 1
    },
    "contributors": [
      {
        "id": "550e8400-e29b-41d4-a716-446655440002",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      },
      {
        "id": "550e8400-e29b-41d4-a716-446655440003",
        "username": "janedoe",
        "full_name": "Jane Doe",
        "avatar_url": "https://example.com/jane.jpg"
      }
    ],
    "contributors_count": 2
  }
}
```

---

## Get Latest Version

Get the most recent version of a snippet.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/snippets/{snippetId}/versions/latest` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Latest version retrieved successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
    "version_number": 15,
    "title": "My Latest Snippet",
    "description": "The most recent version",
    "code": "console.log('Latest code');",
    "language": "javascript",
    "change_summary": "Final polish",
    "change_type": "update",
    "lines_added": 2,
    "lines_removed": 1,
    "created_at": "2024-01-15T10:30:00.000000Z",
    "created_by": {
      "id": "550e8400-e29b-41d4-a716-446655440002",
      "username": "johndoe",
      "full_name": "John Doe",
      "avatar_url": "https://example.com/avatar.jpg"
    }
  }
}
```

---

## Get Version by Number

Get a specific version using its version number.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/snippets/{snippetId}/versions/number/{versionNumber}` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |
| versionNumber | integer | Yes | Version number (1, 2, 3, etc.) |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Version retrieved successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
    "version_number": 5,
    "title": "My Snippet v5",
    "description": "Version 5 description",
    "code": "// Version 5 code",
    "language": "javascript",
    "change_summary": "Added comments",
    "change_type": "update",
    "lines_added": 5,
    "lines_removed": 0,
    "created_at": "2024-01-10T14:00:00.000000Z",
    "created_by": {
      "id": "550e8400-e29b-41d4-a716-446655440002",
      "username": "johndoe",
      "full_name": "John Doe",
      "avatar_url": "https://example.com/avatar.jpg"
    }
  }
}
```

### Error Response (404 Not Found)

```json
{
  "success": false,
  "message": "Version not found."
}
```

---

## Get Version by ID

Get a specific version using its UUID.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/snippets/{snippetId}/versions/{versionId}` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |
| versionId | uuid | Yes | Version ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Version retrieved successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
    "version_number": 5,
    "title": "My Snippet v5",
    "description": "Version 5 description",
    "code": "// Version 5 code",
    "language": "javascript",
    "change_summary": "Added comments",
    "change_type": "update",
    "lines_added": 5,
    "lines_removed": 0,
    "created_at": "2024-01-10T14:00:00.000000Z",
    "created_by": {
      "id": "550e8400-e29b-41d4-a716-446655440002",
      "username": "johndoe",
      "full_name": "John Doe",
      "avatar_url": "https://example.com/avatar.jpg"
    }
  }
}
```

---

## Compare Two Versions

Compare the code between two versions to see differences.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/snippets/{snippetId}/versions/compare` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| from | integer | Yes | Source version number |
| to | integer | Yes | Target version number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Version comparison retrieved successfully.",
  "data": {
    "from": {
      "version_number": 2,
      "title": "My Snippet v2",
      "code": "console.log('Hello');\nconsole.log('World');",
      "language": "javascript",
      "created_at": "2024-01-10T10:00:00.000000Z"
    },
    "to": {
      "version_number": 5,
      "title": "My Snippet v5",
      "code": "console.log('Hello');\nconsole.log('Beautiful');\nconsole.log('World');",
      "language": "javascript",
      "created_at": "2024-01-12T14:00:00.000000Z"
    },
    "diff": {
      "lines_added": 1,
      "lines_removed": 0,
      "total_changes": 2,
      "changes": [
        {
          "type": "modify",
          "line_number": 2,
          "old_content": "console.log('World');",
          "new_content": "console.log('Beautiful');"
        },
        {
          "type": "add",
          "line_number": 3,
          "content": "console.log('World');"
        }
      ]
    }
  }
}
```

### Error Responses

**422 Unprocessable Entity - Missing parameters**
```json
{
  "success": false,
  "message": "Both from and to version numbers are required."
}
```

**404 Not Found - Version not found**
```json
{
  "success": false,
  "message": "One or both versions not found."
}
```

---

## Restore a Version

Restore a snippet to a previous version. This creates a new version with the old content.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/snippets/{snippetId}/versions/{versionId}/restore` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |
| versionId | uuid | Yes | Version ID to restore |

### Request Body

None required.

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Successfully restored to version 5.",
  "data": {
    "snippet": {
      "id": "550e8400-e29b-41d4-a716-446655440001",
      "title": "My Snippet v5",
      "description": "Version 5 description",
      "code": "// Version 5 code",
      "updated_at": "2024-01-15T11:00:00.000000Z"
    },
    "version": {
      "id": "550e8400-e29b-41d4-a716-446655440010",
      "snippet_id": "550e8400-e29b-41d4-a716-446655440001",
      "version_number": 16,
      "title": "My Snippet v5",
      "description": "Version 5 description",
      "code": "// Version 5 code",
      "language": "javascript",
      "change_summary": "Restored from version 5",
      "change_type": "restore",
      "lines_added": 10,
      "lines_removed": 15,
      "created_at": "2024-01-15T11:00:00.000000Z",
      "created_by": {
        "id": "550e8400-e29b-41d4-a716-446655440002",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      }
    },
    "restored_from": 5
  }
}
```

### Error Responses

**403 Forbidden - Not owner**
```json
{
  "success": false,
  "message": "You do not have permission to restore this snippet's version."
}
```

**404 Not Found**
```json
{
  "success": false,
  "message": "Version not found."
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
  "message": "You do not have permission to view this snippet's versions."
}
```

### 404 Not Found

```json
{
  "success": false,
  "message": "Snippet not found."
}
```

---

## Quick Reference

### Version History
```
GET /api/v1/snippets/{snippetId}/versions                    - Get all versions
GET /api/v1/snippets/{snippetId}/versions/stats              - Get statistics
GET /api/v1/snippets/{snippetId}/versions/latest             - Get latest version
```

### Version Access
```
GET /api/v1/snippets/{snippetId}/versions/number/{n}         - Get by version number
GET /api/v1/snippets/{snippetId}/versions/{versionId}        - Get by ID
```

### Version Comparison & Restore
```
GET  /api/v1/snippets/{snippetId}/versions/compare           - Compare two versions
POST /api/v1/snippets/{snippetId}/versions/{versionId}/restore - Restore version
```
