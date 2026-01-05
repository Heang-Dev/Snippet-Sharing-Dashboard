# Languages, Categories & Tags API Documentation

Base URL: `/api/v1`

All endpoints in this document are **public** and do not require authentication.

---

## Languages API

### 1. Get All Languages

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/languages` | No |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by name or display_name |
| sort_by | string | No | name | Sort field: `name`, `display_name`, `snippet_count`, `popularity_rank`, `created_at` |
| sort_order | string | No | asc | Sort direction: `asc` or `desc` |
| all | boolean | No | false | Include inactive languages |
| per_page | integer | No | - | Enable pagination (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/languages?search=java&sort_by=snippet_count&sort_order=desc
```

**Success Response (Without Pagination):**
```json
{
    "success": true,
    "message": "Languages retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "name": "javascript",
            "slug": "javascript",
            "display_name": "JavaScript",
            "pygments_lexer": "javascript",
            "monaco_language": "javascript",
            "file_extensions": [".js", ".jsx", ".mjs"],
            "icon": "javascript-icon",
            "color": "#f7df1e",
            "snippet_count": 150,
            "popularity_rank": 1,
            "is_active": true,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        }
    ]
}
```

**Success Response (With Pagination):**
```json
{
    "success": true,
    "message": "Languages retrieved successfully.",
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 20,
        "total": 50
    }
}
```

---

### 2. Get Popular Languages

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/languages/popular` | No |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| limit | integer | No | 20 | Number of languages to return (max: 50) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/languages/popular?limit=10
```

**Success Response:**
```json
{
    "success": true,
    "message": "Popular languages retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "name": "javascript",
            "slug": "javascript",
            "display_name": "JavaScript",
            "pygments_lexer": "javascript",
            "monaco_language": "javascript",
            "file_extensions": [".js", ".jsx", ".mjs"],
            "icon": "javascript-icon",
            "color": "#f7df1e",
            "snippet_count": 150,
            "popularity_rank": 1,
            "is_active": true,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        }
    ]
}
```

---

### 3. Get Language by Slug

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/languages/{slug}` | No |

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| slug | string | Yes | Language slug (e.g., `javascript`, `python`) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/languages/javascript
```

**Success Response:**
```json
{
    "success": true,
    "message": "Language retrieved successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "name": "javascript",
        "slug": "javascript",
        "display_name": "JavaScript",
        "pygments_lexer": "javascript",
        "monaco_language": "javascript",
        "file_extensions": [".js", ".jsx", ".mjs"],
        "icon": "javascript-icon",
        "color": "#f7df1e",
        "snippet_count": 150,
        "popularity_rank": 1,
        "is_active": true,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Language not found."
}
```

---

## Categories API

### 4. Get All Categories

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/categories` | No |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by name or description |
| sort_by | string | No | order | Sort field: `name`, `order`, `snippet_count`, `created_at` |
| sort_order | string | No | asc | Sort direction: `asc` or `desc` |
| all | boolean | No | false | Include inactive categories |
| roots | boolean | No | false | Get only root categories (no parent) |
| parent_id | string | No | - | Filter by parent category ID (use `null` for roots) |
| with_children | boolean | No | false | Include children categories |
| per_page | integer | No | - | Enable pagination (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/categories?roots=true&with_children=true
```

**Success Response (Without Pagination):**
```json
{
    "success": true,
    "message": "Categories retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "name": "Web Development",
            "slug": "web-development",
            "description": "Web development related snippets",
            "parent_category_id": null,
            "icon": "web-icon",
            "color": "#3498db",
            "order": 1,
            "snippet_count": 120,
            "is_active": true,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z",
            "children": [
                {
                    "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "name": "Frontend",
                    "slug": "frontend",
                    "description": "Frontend development snippets",
                    "parent_category_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                    "icon": "frontend-icon",
                    "color": "#2ecc71",
                    "order": 1,
                    "snippet_count": 80,
                    "is_active": true,
                    "created_at": "2025-01-01T00:00:00.000000Z",
                    "updated_at": "2025-01-01T00:00:00.000000Z"
                }
            ]
        }
    ]
}
```

**Success Response (With Pagination):**
```json
{
    "success": true,
    "message": "Categories retrieved successfully.",
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 2,
        "per_page": 20,
        "total": 30
    }
}
```

---

### 5. Get Category Tree

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/categories/tree` | No |

**Query Parameters:**
```
None
```

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/categories/tree
```

**Success Response:**
```json
{
    "success": true,
    "message": "Category tree retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "name": "Web Development",
            "slug": "web-development",
            "description": "Web development related snippets",
            "parent_category_id": null,
            "icon": "web-icon",
            "color": "#3498db",
            "order": 1,
            "snippet_count": 120,
            "is_active": true,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z",
            "children": [
                {
                    "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "name": "Frontend",
                    "slug": "frontend",
                    "description": "Frontend development snippets",
                    "parent_category_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                    "icon": "frontend-icon",
                    "color": "#2ecc71",
                    "order": 1,
                    "snippet_count": 80,
                    "is_active": true,
                    "created_at": "2025-01-01T00:00:00.000000Z",
                    "updated_at": "2025-01-01T00:00:00.000000Z"
                },
                {
                    "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                    "name": "Backend",
                    "slug": "backend",
                    "description": "Backend development snippets",
                    "parent_category_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                    "icon": "backend-icon",
                    "color": "#e74c3c",
                    "order": 2,
                    "snippet_count": 40,
                    "is_active": true,
                    "created_at": "2025-01-01T00:00:00.000000Z",
                    "updated_at": "2025-01-01T00:00:00.000000Z"
                }
            ]
        },
        {
            "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
            "name": "Mobile Development",
            "slug": "mobile-development",
            "description": "Mobile app development snippets",
            "parent_category_id": null,
            "icon": "mobile-icon",
            "color": "#9b59b6",
            "order": 2,
            "snippet_count": 60,
            "is_active": true,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z",
            "children": []
        }
    ]
}
```

---

### 6. Get Category by Slug

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/categories/{slug}` | No |

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| slug | string | Yes | Category slug (e.g., `web-development`) |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| with_children | boolean | No | false | Include children categories |
| with_parent | boolean | No | false | Include parent category |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/categories/web-development?with_children=true&with_parent=true
```

**Success Response:**
```json
{
    "success": true,
    "message": "Category retrieved successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "name": "Web Development",
        "slug": "web-development",
        "description": "Web development related snippets",
        "parent_category_id": null,
        "icon": "web-icon",
        "color": "#3498db",
        "order": 1,
        "snippet_count": 120,
        "is_active": true,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z",
        "parent": null,
        "children": [
            {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "name": "Frontend",
                "slug": "frontend",
                "description": "Frontend development snippets",
                "parent_category_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "icon": "frontend-icon",
                "color": "#2ecc71",
                "order": 1,
                "snippet_count": 80,
                "is_active": true,
                "created_at": "2025-01-01T00:00:00.000000Z",
                "updated_at": "2025-01-01T00:00:00.000000Z"
            }
        ]
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Category not found."
}
```

---

## Tags API

### 7. Get All Tags

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/tags` | No |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by tag name |
| sort_by | string | No | name | Sort field: `name`, `usage_count`, `created_at` |
| sort_order | string | No | asc | Sort direction: `asc` or `desc` |
| per_page | integer | No | - | Enable pagination (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/tags?search=react&sort_by=usage_count&sort_order=desc
```

**Success Response (Without Pagination):**
```json
{
    "success": true,
    "message": "Tags retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "name": "react",
            "slug": "react",
            "description": "React.js related code",
            "color": "#61dafb",
            "usage_count": 85,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        },
        {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "name": "react-hooks",
            "slug": "react-hooks",
            "description": "React Hooks examples",
            "color": "#61dafb",
            "usage_count": 42,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        }
    ]
}
```

**Success Response (With Pagination):**
```json
{
    "success": true,
    "message": "Tags retrieved successfully.",
    "data": [...],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 50,
        "total": 230
    }
}
```

---

### 8. Get Popular Tags

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/tags/popular` | No |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| limit | integer | No | 20 | Number of tags to return (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/tags/popular?limit=15
```

**Success Response:**
```json
{
    "success": true,
    "message": "Popular tags retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "name": "javascript",
            "slug": "javascript",
            "description": "JavaScript code snippets",
            "color": "#f7df1e",
            "usage_count": 250,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        },
        {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "name": "python",
            "slug": "python",
            "description": "Python code snippets",
            "color": "#3776ab",
            "usage_count": 200,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        }
    ]
}
```

---

### 9. Search Tags (Autocomplete)

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/tags/search` | No |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| q | string | No | - | Search query (alternative: `search`) |
| search | string | No | - | Search query (alternative: `q`) |
| limit | integer | No | 10 | Number of results to return (max: 50) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/tags/search?q=java&limit=5
```

**Success Response:**
```json
{
    "success": true,
    "message": "Tags retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "name": "javascript",
            "slug": "javascript",
            "usage_count": 250
        },
        {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "name": "java",
            "slug": "java",
            "usage_count": 180
        },
        {
            "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "name": "java-spring",
            "slug": "java-spring",
            "usage_count": 45
        }
    ]
}
```

**Success Response (Empty Search):**
```json
{
    "success": true,
    "message": "Tags retrieved successfully.",
    "data": []
}
```

---

### 10. Get Tag by Slug

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/tags/{slug}` | No |

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| slug | string | Yes | Tag slug (e.g., `javascript`, `react-hooks`) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/tags/javascript
```

**Success Response:**
```json
{
    "success": true,
    "message": "Tag retrieved successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "name": "javascript",
        "slug": "javascript",
        "description": "JavaScript code snippets",
        "color": "#f7df1e",
        "usage_count": 250,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Tag not found."
}
```

---

## Quick Reference

| # | Endpoint | Method | Auth | Description |
|---|----------|--------|------|-------------|
| 1 | `/api/v1/languages` | GET | No | Get all languages |
| 2 | `/api/v1/languages/popular` | GET | No | Get popular languages |
| 3 | `/api/v1/languages/{slug}` | GET | No | Get language by slug |
| 4 | `/api/v1/categories` | GET | No | Get all categories |
| 5 | `/api/v1/categories/tree` | GET | No | Get category tree |
| 6 | `/api/v1/categories/{slug}` | GET | No | Get category by slug |
| 7 | `/api/v1/tags` | GET | No | Get all tags |
| 8 | `/api/v1/tags/popular` | GET | No | Get popular tags |
| 9 | `/api/v1/tags/search` | GET | No | Search/autocomplete tags |
| 10 | `/api/v1/tags/{slug}` | GET | No | Get tag by slug |
