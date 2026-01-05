# Activity Feed API Documentation

Base URL: `/api/v1`

Some endpoints require Bearer token in the Authorization header.

---

## Endpoints Overview

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/feed/public` | Get public activity feed | No |
| GET | `/feed/types` | Get activity types | No |
| GET | `/feed/users/{userId}` | Get user's public activity | No |
| GET | `/feed` | Get personalized feed | Yes |
| GET | `/feed/me` | Get my activity | Yes |
| GET | `/feed/stats` | Get activity statistics | Yes |

---

## Activity Types

| Type | Description |
|------|-------------|
| `snippet_created` | New snippet created |
| `snippet_updated` | Snippet updated |
| `snippet_forked` | Snippet forked |
| `snippet_favorited` | Snippet favorited |
| `snippet_trending` | Trending snippet |
| `comment_added` | Comment added |
| `follow` | New follow |
| `collection_created` | Collection created |
| `team_created` | Team created |
| `team_joined` | Joined a team |

---

## Get Public Activity Feed

Get trending and recent public activities. No authentication required.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/feed/public` | Not Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Public activity feed retrieved successfully.",
  "data": [
    {
      "type": "snippet_trending",
      "user": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      },
      "resource_type": "snippet",
      "resource_id": "550e8400-e29b-41d4-a716-446655440001",
      "resource": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "title": "Awesome React Hook",
        "slug": "awesome-react-hook",
        "description": "A custom hook for...",
        "language": {
          "id": "550e8400-e29b-41d4-a716-446655440002",
          "name": "JavaScript",
          "slug": "javascript"
        },
        "favorites_count": 150,
        "comments_count": 25,
        "forks_count": 10
      },
      "message": "Trending snippet by johndoe",
      "created_at": "2024-01-15T10:30:00.000000Z"
    },
    {
      "type": "snippet_created",
      "user": {
        "id": "550e8400-e29b-41d4-a716-446655440003",
        "username": "janedoe",
        "full_name": "Jane Doe",
        "avatar_url": "https://example.com/jane.jpg"
      },
      "resource_type": "snippet",
      "resource_id": "550e8400-e29b-41d4-a716-446655440004",
      "resource": {
        "id": "550e8400-e29b-41d4-a716-446655440004",
        "title": "Python Data Parser",
        "slug": "python-data-parser",
        "description": "Parse JSON data efficiently",
        "language": {
          "id": "550e8400-e29b-41d4-a716-446655440005",
          "name": "Python",
          "slug": "python"
        }
      },
      "message": "janedoe shared a snippet",
      "created_at": "2024-01-15T09:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

---

## Get Activity Types

Get all available activity types for reference.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/feed/types` | Not Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Activity types retrieved successfully.",
  "data": [
    {
      "type": "snippet_created",
      "description": "New snippet created"
    },
    {
      "type": "snippet_updated",
      "description": "Snippet updated"
    },
    {
      "type": "snippet_forked",
      "description": "Snippet forked"
    },
    {
      "type": "snippet_favorited",
      "description": "Snippet favorited"
    },
    {
      "type": "comment_added",
      "description": "Comment added"
    },
    {
      "type": "follow",
      "description": "New follow"
    },
    {
      "type": "collection_created",
      "description": "Collection created"
    },
    {
      "type": "team_created",
      "description": "Team created"
    },
    {
      "type": "team_joined",
      "description": "Joined a team"
    }
  ]
}
```

---

## Get User's Public Activity

Get public activity for a specific user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/feed/users/{userId}` | Not Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "User activity retrieved successfully.",
  "data": [
    {
      "type": "snippet_created",
      "user": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      },
      "resource_type": "snippet",
      "resource_id": "550e8400-e29b-41d4-a716-446655440001",
      "resource": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "title": "My Latest Snippet",
        "slug": "my-latest-snippet",
        "description": "A cool code snippet",
        "language": {
          "id": "550e8400-e29b-41d4-a716-446655440002",
          "name": "JavaScript",
          "slug": "javascript"
        }
      },
      "message": "Created a new snippet",
      "created_at": "2024-01-15T10:30:00.000000Z"
    },
    {
      "type": "comment_added",
      "user": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "username": "johndoe",
        "full_name": "John Doe",
        "avatar_url": "https://example.com/avatar.jpg"
      },
      "resource_type": "comment",
      "resource_id": "550e8400-e29b-41d4-a716-446655440003",
      "resource": {
        "id": "550e8400-e29b-41d4-a716-446655440003",
        "content": "Great implementation! I would suggest...",
        "snippet": {
          "id": "550e8400-e29b-41d4-a716-446655440004",
          "title": "React useState Pattern",
          "slug": "react-usestate-pattern"
        }
      },
      "message": "Commented on a snippet",
      "created_at": "2024-01-14T15:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 45,
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
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
  "message": "User not found."
}
```

---

## Get Personalized Activity Feed

Get activity feed from users you follow. Requires authentication.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/feed` | Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| type | string | No | - | Filter by single activity type |
| types | string | No | - | Filter by multiple types (comma-separated) |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Activity feed retrieved successfully.",
  "data": [
    {
      "type": "snippet_created",
      "user": {
        "id": "550e8400-e29b-41d4-a716-446655440000",
        "username": "developer1",
        "full_name": "Developer One",
        "avatar_url": "https://example.com/dev1.jpg"
      },
      "resource_type": "snippet",
      "resource_id": "550e8400-e29b-41d4-a716-446655440001",
      "resource": {
        "id": "550e8400-e29b-41d4-a716-446655440001",
        "title": "New API Endpoint",
        "slug": "new-api-endpoint",
        "description": "REST API endpoint implementation",
        "language": {
          "id": "550e8400-e29b-41d4-a716-446655440002",
          "name": "PHP",
          "slug": "php"
        }
      },
      "message": "developer1 created a new snippet",
      "created_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 20,
    "total": 200
  }
}
```

---

## Get My Activity

Get your own activity history. Requires authentication.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/feed/me` | Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

Same format as "Get User's Public Activity" but for the authenticated user.

---

## Get Activity Statistics

Get activity statistics for the authenticated user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/feed/stats` | Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Activity statistics retrieved successfully.",
  "data": {
    "snippets": {
      "total": 50,
      "this_week": 3
    },
    "comments": {
      "total": 120,
      "this_week": 8
    },
    "favorites_received": 350,
    "followers": 75,
    "following": 45
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

### 404 Not Found

```json
{
  "success": false,
  "message": "User not found."
}
```

---

## Activity Object Structure

Each activity in the feed contains:

| Field | Type | Description |
|-------|------|-------------|
| type | string | Activity type (see Activity Types) |
| user | object | User who performed the action |
| resource_type | string | Type of resource (snippet, comment, etc.) |
| resource_id | string | UUID of the resource |
| resource | object | Resource details |
| message | string | Human-readable activity description |
| created_at | datetime | When the activity occurred |

---

## Quick Reference

### Public Endpoints
```
GET /api/v1/feed/public              - Public/trending activity
GET /api/v1/feed/types               - Activity types reference
GET /api/v1/feed/users/{userId}      - User's public activity
```

### Authenticated Endpoints
```
GET /api/v1/feed                     - Personalized feed (from followed users)
GET /api/v1/feed/me                  - My activity
GET /api/v1/feed/stats               - My activity statistics
```
