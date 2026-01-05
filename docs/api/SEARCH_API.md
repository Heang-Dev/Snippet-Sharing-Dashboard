# Search API Documentation

Base URL: `/api/v1`

All search endpoints are **public** and do not require authentication.

---

## Endpoints

### 1. Global Search

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/search` | No |

Search across all resources (snippets, users, collections, tags, languages, categories).

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| q | string | Yes | - | Search query (min: 2 characters). Alternative: `query` |
| type | string | No | all | Resource type: `all`, `snippets`, `users`, `collections`, `tags`, `languages`, `categories` |
| limit | integer | No | 10 | Results per type (max: 50) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/search?q=react&type=all&limit=5
```

**Success Response:**
```json
{
    "success": true,
    "message": "Search results retrieved successfully.",
    "data": {
        "snippets": [
            {
                "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "title": "React useLocalStorage Hook",
                "slug": "react-uselocalstorage-hook",
                "description": "Custom hook for localStorage",
                "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "language_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                "view_count": 250,
                "created_at": "2025-01-01T00:00:00.000000Z",
                "user": {
                    "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "username": "johndoe",
                    "full_name": "John Doe",
                    "avatar_url": "https://example.com/avatar.jpg"
                },
                "language": {
                    "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                    "name": "javascript",
                    "slug": "javascript",
                    "display_name": "JavaScript",
                    "color": "#f7df1e"
                }
            }
        ],
        "users": [
            {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "username": "reactdev",
                "full_name": "React Developer",
                "avatar_url": "https://example.com/avatar.jpg"
            }
        ],
        "collections": [
            {
                "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
                "name": "React Hooks Collection",
                "slug": "react-hooks-collection",
                "description": "Useful React hooks",
                "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "snippets_count": 15,
                "created_at": "2025-01-01T00:00:00.000000Z",
                "user": {
                    "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "username": "johndoe",
                    "full_name": "John Doe",
                    "avatar_url": "https://example.com/avatar.jpg"
                }
            }
        ],
        "tags": [
            {
                "id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                "name": "react",
                "slug": "react",
                "color": "#61dafb",
                "usage_count": 150
            },
            {
                "id": "4a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p",
                "name": "react-hooks",
                "slug": "react-hooks",
                "color": "#61dafb",
                "usage_count": 85
            }
        ],
        "languages": [
            {
                "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                "name": "javascript",
                "slug": "javascript",
                "display_name": "JavaScript",
                "color": "#f7df1e",
                "snippet_count": 500
            }
        ],
        "categories": [
            {
                "id": "3a1b2c3d-4e5f-6g7h-8i9j-0k1l2m3n4o5p",
                "name": "Frontend",
                "slug": "frontend",
                "description": "Frontend development",
                "color": "#3498db",
                "snippet_count": 200
            }
        ]
    },
    "meta": {
        "query": "react",
        "type": "all"
    }
}
```

**Example URL (Single Type):**
```
GET /api/v1/search?q=react&type=snippets&limit=10
```

**Success Response (Single Type):**
```json
{
    "success": true,
    "message": "Search results retrieved successfully.",
    "data": {
        "snippets": [...]
    },
    "meta": {
        "query": "react",
        "type": "snippets"
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Search query must be at least 2 characters."
}
```

---

### 2. Advanced Snippet Search

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/search/snippets` | No |

Search snippets with advanced filtering options.

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| q | string | No | - | Search query (searches title, description, code). Alternative: `query` |
| language_id | uuid | No | - | Filter by language ID |
| language | string | No | - | Filter by language slug (e.g., `javascript`) |
| category_id | uuid | No | - | Filter by category ID |
| category | string | No | - | Filter by category slug (e.g., `frontend`) |
| tags | string/array | No | - | Filter by tag slugs (comma-separated or array) |
| user_id | uuid | No | - | Filter by user ID |
| username | string | No | - | Filter by username |
| from_date | date | No | - | Filter from date (YYYY-MM-DD) |
| to_date | date | No | - | Filter to date (YYYY-MM-DD) |
| sort_by | string | No | relevance | Sort: `relevance`, `created_at`, `updated_at`, `view_count`, `favorite_count` |
| sort_order | string | No | desc | Sort direction: `asc` or `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/search/snippets?q=hook&language=javascript&tags=react,hooks&sort_by=view_count&per_page=10
```

**Example URL (Multiple Filters):**
```
GET /api/v1/search/snippets?q=api&category=backend&from_date=2025-01-01&to_date=2025-12-31&sort_by=created_at&sort_order=desc
```

**Success Response:**
```json
{
    "success": true,
    "message": "Search results retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "title": "React useLocalStorage Hook",
            "slug": "react-uselocalstorage-hook",
            "description": "Custom hook for localStorage with TypeScript support",
            "code": "const useLocalStorage = (key, initialValue) => {...}",
            "visibility": "public",
            "view_count": 250,
            "favorite_count": 45,
            "fork_count": 12,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-15T00:00:00.000000Z",
            "user": {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": "https://example.com/avatar.jpg"
            },
            "language": {
                "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                "name": "javascript",
                "slug": "javascript",
                "display_name": "JavaScript",
                "color": "#f7df1e"
            },
            "tags": [
                {
                    "id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                    "name": "react",
                    "slug": "react",
                    "color": "#61dafb"
                },
                {
                    "id": "4a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p",
                    "name": "hooks",
                    "slug": "hooks",
                    "color": "#2ecc71"
                }
            ]
        }
    ],
    "meta": {
        "query": "hook",
        "current_page": 1,
        "last_page": 5,
        "per_page": 10,
        "total": 45
    }
}
```

---

### 3. Search Users

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/search/users` | No |

Search for users by username, full name, or bio.

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| q | string | Yes | - | Search query (min: 2 characters). Alternative: `query` |
| sort_by | string | No | relevance | Sort: `relevance`, `created_at`, `snippets_count` |
| sort_order | string | No | desc | Sort direction: `asc` or `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/search/users?q=john&sort_by=snippets_count&sort_order=desc&per_page=10
```

**Success Response:**
```json
{
    "success": true,
    "message": "Search results retrieved successfully.",
    "data": [
        {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "username": "johndoe",
            "full_name": "John Doe",
            "bio": "Full-stack developer passionate about React and Laravel",
            "avatar_url": "https://example.com/avatar.jpg",
            "created_at": "2024-06-01T00:00:00.000000Z",
            "snippets_count": 45
        },
        {
            "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "username": "johnny_dev",
            "full_name": "Johnny Developer",
            "bio": "Backend engineer",
            "avatar_url": "https://example.com/avatar2.jpg",
            "created_at": "2024-08-15T00:00:00.000000Z",
            "snippets_count": 23
        }
    ],
    "meta": {
        "query": "john",
        "current_page": 1,
        "last_page": 1,
        "per_page": 20,
        "total": 2
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Search query must be at least 2 characters."
}
```

---

### 4. Autocomplete / Suggestions

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/search/autocomplete` | No |

Get search suggestions for autocomplete functionality.

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| q | string | No | - | Search query. Alternative: `query` |
| limit | integer | No | 5 | Suggestions per type (max: 20) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/search/autocomplete?q=react&limit=5
```

**Success Response:**
```json
{
    "success": true,
    "message": "Suggestions retrieved successfully.",
    "data": [
        {
            "type": "snippet",
            "value": "React useLocalStorage Hook"
        },
        {
            "type": "snippet",
            "value": "React Context API Example"
        },
        {
            "type": "tag",
            "value": "react"
        },
        {
            "type": "tag",
            "value": "react-hooks"
        },
        {
            "type": "language",
            "value": "JavaScript"
        },
        {
            "type": "user",
            "value": "reactdev",
            "display": "React Developer"
        }
    ]
}
```

**Success Response (Empty Query):**
```json
{
    "success": true,
    "message": "Suggestions retrieved successfully.",
    "data": []
}
```

---

## Quick Reference

| # | Endpoint | Method | Auth | Description |
|---|----------|--------|------|-------------|
| 1 | `/api/v1/search` | GET | No | Global search across all resources |
| 2 | `/api/v1/search/snippets` | GET | No | Advanced snippet search with filters |
| 3 | `/api/v1/search/users` | GET | No | Search users |
| 4 | `/api/v1/search/autocomplete` | GET | No | Autocomplete suggestions |

---

## Search Features

### Relevance Scoring
- Title matches are prioritized over description matches
- Exact prefix matches rank higher than substring matches
- Results are sorted by view count as secondary sort

### Supported Filters (Snippet Search)
| Filter | Description |
|--------|-------------|
| Language | Filter by programming language (slug or ID) |
| Category | Filter by category (slug or ID) |
| Tags | Filter by multiple tags (comma-separated) |
| User | Filter by author (username or ID) |
| Date Range | Filter by creation date range |

### Sort Options
| Resource | Available Sort Fields |
|----------|----------------------|
| Snippets | `relevance`, `created_at`, `updated_at`, `view_count`, `favorite_count` |
| Users | `relevance`, `created_at`, `snippets_count` |

### Performance Tips
- Use specific resource type (`type=snippets`) for faster results
- Use autocomplete endpoint for instant search suggestions
- Combine filters to narrow down results
- Use pagination to handle large result sets
