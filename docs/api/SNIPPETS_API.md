# Snippets API Documentation

**Base URL:** `http://snippet-g11.test/api/v1`

---

## Headers (All Requests)

```
Accept: application/json
Content-Type: application/json
Authorization: Bearer {{token}}    // Only for protected endpoints
```

---

# PUBLIC ENDPOINTS (No Auth Required)

---

## 1. Browse Public Snippets

| | |
|---|---|
| **Method** | `GET` |
| **URL** | `/snippets/public` |
| **Auth** | No |

### Query Parameters (Optional)

| Parameter | Type | Example | Description |
|-----------|------|---------|-------------|
| `language` | string | `javascript` | Filter by language slug |
| `language_id` | UUID | `uuid-here` | Filter by language ID |
| `category` | string | `frontend` | Filter by category slug |
| `category_id` | UUID | `uuid-here` | Filter by category ID |
| `tag` | string | `react` | Filter by tag slug |
| `user_id` | UUID | `uuid-here` | Filter by user ID |
| `search` | string | `useEffect` | Search in title/description |
| `sort_by` | string | `views_count` | Options: `created_at`, `updated_at`, `title`, `views_count`, `favorites_count` |
| `sort_order` | string | `desc` | Options: `asc`, `desc` |
| `per_page` | integer | `15` | Items per page (1-100) |
| `page` | integer | `1` | Page number |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/public?language=javascript&sort_by=views_count&per_page=10
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Public snippets retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "user_id": "user-uuid-here",
            "language_id": "lang-uuid-here",
            "category_id": "cat-uuid-here",
            "team_id": null,
            "title": "React useEffect Hook Example",
            "slug": "react-useeffect-hook-example",
            "description": "A complete example of useEffect hook",
            "code": "import { useEffect } from 'react';\n\nfunction Example() {\n  useEffect(() => {\n    console.log('mounted');\n  }, []);\n}",
            "file_name": "useEffect.jsx",
            "visibility": "public",
            "expires_at": null,
            "views_count": 150,
            "favorites_count": 25,
            "comments_count": 5,
            "forks_count": 3,
            "forked_from_id": null,
            "version": 1,
            "is_pinned": false,
            "metadata": null,
            "created_at": "2026-01-05T10:30:00.000000Z",
            "updated_at": "2026-01-05T10:30:00.000000Z",
            "deleted_at": null,
            "language": {
                "id": "lang-uuid-here",
                "name": "JavaScript",
                "slug": "javascript",
                "display_name": "JavaScript",
                "icon": "javascript",
                "color": "#f7df1e"
            },
            "category": {
                "id": "cat-uuid-here",
                "name": "Frontend",
                "slug": "frontend",
                "description": "Frontend development",
                "icon": "monitor",
                "color": "#3b82f6"
            },
            "tags": [
                {
                    "id": "tag-uuid-here",
                    "name": "react",
                    "slug": "react",
                    "color": "#61dafb"
                }
            ],
            "user": {
                "id": "user-uuid-here",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": null
            }
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 150
    }
}
```

---

## 2. Get Trending Snippets

| | |
|---|---|
| **Method** | `GET` |
| **URL** | `/snippets/trending` |
| **Auth** | No |

### Query Parameters (Optional)

| Parameter | Type | Example | Description |
|-----------|------|---------|-------------|
| `days` | integer | `7` | Trending period in days (default: 7) |
| `limit` | integer | `20` | Number of results (1-100, default: 20) |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/trending?days=7&limit=10
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Trending snippets retrieved successfully.",
    "data": [
        {
            "id": "snippet-uuid",
            "title": "Popular Code Snippet",
            "slug": "popular-code-snippet",
            "description": "This snippet is trending",
            "code": "console.log('trending');",
            "views_count": 500,
            "favorites_count": 100,
            "language": { "id": "...", "name": "JavaScript", "slug": "javascript" },
            "category": { "id": "...", "name": "Backend", "slug": "backend" },
            "tags": [],
            "user": { "id": "...", "username": "janedoe", "full_name": "Jane Doe", "avatar_url": null }
        }
    ]
}
```

---

## 3. Get Featured Snippets

| | |
|---|---|
| **Method** | `GET` |
| **URL** | `/snippets/featured` |
| **Auth** | No |

### Query Parameters (Optional)

| Parameter | Type | Example | Description |
|-----------|------|---------|-------------|
| `limit` | integer | `10` | Number of results (1-50, default: 10) |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/featured?limit=5
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Featured snippets retrieved successfully.",
    "data": [
        {
            "id": "snippet-uuid",
            "title": "Featured Snippet",
            "slug": "featured-snippet",
            "is_pinned": true
        }
    ]
}
```

---

## 4. View Single Snippet

| | |
|---|---|
| **Method** | `GET` |
| **URL** | `/snippets/{slug}` |
| **Auth** | Optional (required for private snippets) |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `slug` | string | Yes | Snippet slug |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/react-useeffect-hook-example
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Snippet retrieved successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "user_id": "user-uuid-here",
        "language_id": "lang-uuid-here",
        "category_id": "cat-uuid-here",
        "team_id": null,
        "title": "React useEffect Hook Example",
        "slug": "react-useeffect-hook-example",
        "description": "A complete example of useEffect hook",
        "code": "import { useEffect } from 'react';\n\nfunction Example() {\n  useEffect(() => {\n    console.log('mounted');\n  }, []);\n}",
        "file_name": "useEffect.jsx",
        "visibility": "public",
        "expires_at": null,
        "views_count": 151,
        "favorites_count": 25,
        "comments_count": 5,
        "forks_count": 3,
        "forked_from_id": null,
        "version": 1,
        "is_pinned": false,
        "metadata": null,
        "created_at": "2026-01-05T10:30:00.000000Z",
        "updated_at": "2026-01-05T10:30:00.000000Z",
        "language": {
            "id": "lang-uuid-here",
            "name": "JavaScript",
            "slug": "javascript"
        },
        "category": {
            "id": "cat-uuid-here",
            "name": "Frontend",
            "slug": "frontend"
        },
        "tags": [
            { "id": "tag-uuid", "name": "react", "slug": "react" }
        ],
        "user": {
            "id": "user-uuid-here",
            "username": "johndoe",
            "full_name": "John Doe",
            "avatar_url": null
        },
        "forked_from": null,
        "is_favorited": true,
        "is_owner": false
    }
}
```

### Error Response (404)

```json
{
    "success": false,
    "message": "Snippet not found."
}
```

### Error Response (403)

```json
{
    "success": false,
    "message": "You do not have permission to view this snippet."
}
```

---

# PROTECTED ENDPOINTS (Auth Required)

---

## 5. List User's Snippets

| | |
|---|---|
| **Method** | `GET` |
| **URL** | `/snippets` |
| **Auth** | Yes |

### Query Parameters (Optional)

| Parameter | Type | Example | Description |
|-----------|------|---------|-------------|
| `visibility` | string | `public` | Options: `public`, `private`, `team`, `unlisted` |
| `language_id` | UUID | `uuid-here` | Filter by language ID |
| `category_id` | UUID | `uuid-here` | Filter by category ID |
| `tag` | string | `react` | Filter by tag slug |
| `search` | string | `hook` | Search in title/description/code |
| `pinned` | boolean | `true` | Filter by pinned status |
| `sort_by` | string | `created_at` | Options: `created_at`, `updated_at`, `title`, `views_count`, `favorites_count` |
| `sort_order` | string | `desc` | Options: `asc`, `desc` |
| `per_page` | integer | `15` | Items per page (1-100) |
| `page` | integer | `1` | Page number |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets?visibility=public&sort_by=created_at&per_page=10
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Snippets retrieved successfully.",
    "data": [
        {
            "id": "snippet-uuid",
            "title": "My Snippet",
            "slug": "my-snippet",
            "visibility": "public",
            "views_count": 50,
            "favorites_count": 10,
            "is_pinned": true,
            "language": { "id": "...", "name": "JavaScript", "slug": "javascript" },
            "category": { "id": "...", "name": "Frontend", "slug": "frontend" },
            "tags": [],
            "user": { "id": "...", "username": "me", "full_name": "Me", "avatar_url": null }
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 75
    }
}
```

---

## 6. Get User's Favorite Snippets

| | |
|---|---|
| **Method** | `GET` |
| **URL** | `/snippets/favorites` |
| **Auth** | Yes |

### Query Parameters (Optional)

| Parameter | Type | Example | Description |
|-----------|------|---------|-------------|
| `sort_by` | string | `created_at` | Options: `created_at`, `title`, `views_count`, `favorites_count` |
| `sort_order` | string | `desc` | Options: `asc`, `desc` |
| `per_page` | integer | `15` | Items per page (1-100) |
| `page` | integer | `1` | Page number |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/favorites?per_page=10
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Favorite snippets retrieved successfully.",
    "data": [
        {
            "id": "snippet-uuid",
            "title": "Favorited Snippet",
            "slug": "favorited-snippet",
            "user": { "id": "...", "username": "author", "full_name": "Author Name", "avatar_url": null }
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 2,
        "per_page": 15,
        "total": 25
    }
}
```

---

## 7. Create Snippet

| | |
|---|---|
| **Method** | `POST` |
| **URL** | `/snippets` |
| **Auth** | Yes |

### Request Body Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `title` | string | **Yes** | Max 255 characters |
| `code` | string | **Yes** | Max ~1MB |
| `visibility` | string | **Yes** | Options: `public`, `private`, `team`, `unlisted` |
| `description` | string | No | Max 5000 characters |
| `language_id` | UUID | No | Language UUID |
| `category_id` | UUID | No | Category UUID |
| `file_name` | string | No | Max 255 characters |
| `tags` | array | No | Max 10 tags |
| `expires_at` | datetime | No | ISO 8601 format (must be future date) |
| `team_id` | UUID | Conditional | Required if `visibility` = `team` |

### Request Body (Minimum Required)

```json
{
    "title": "My Code Snippet",
    "code": "console.log('Hello World');",
    "visibility": "public"
}
```

### Request Body (Full Example)

```json
{
    "title": "React useEffect Hook Example",
    "description": "A complete example showing how to use the useEffect hook in React",
    "code": "import { useEffect, useState } from 'react';\n\nfunction Example() {\n  const [count, setCount] = useState(0);\n\n  useEffect(() => {\n    document.title = `Clicked ${count} times`;\n  }, [count]);\n\n  return (\n    <button onClick={() => setCount(count + 1)}>\n      Click me\n    </button>\n  );\n}",
    "language_id": "your-language-uuid-here",
    "category_id": "your-category-uuid-here",
    "visibility": "public",
    "file_name": "useEffect.jsx",
    "tags": ["react", "hooks", "javascript"]
}
```

### Request Body (Private Snippet)

```json
{
    "title": "My Private Config",
    "code": "const API_KEY = 'secret-key';",
    "visibility": "private"
}
```

### Request Body (Team Snippet)

```json
{
    "title": "Team Component",
    "code": "export const Button = () => <button>Click</button>;",
    "visibility": "team",
    "team_id": "your-team-uuid-here"
}
```

### Request Body (With Expiration)

```json
{
    "title": "Temporary Share",
    "code": "// This will expire",
    "visibility": "public",
    "expires_at": "2026-02-01T00:00:00Z"
}
```

### Success Response (201)

```json
{
    "success": true,
    "message": "Snippet created successfully.",
    "data": {
        "id": "new-snippet-uuid",
        "user_id": "your-user-uuid",
        "language_id": "language-uuid",
        "category_id": "category-uuid",
        "team_id": null,
        "title": "React useEffect Hook Example",
        "slug": "react-useeffect-hook-example",
        "description": "A complete example showing how to use the useEffect hook in React",
        "code": "import { useEffect, useState } from 'react';...",
        "file_name": "useEffect.jsx",
        "visibility": "public",
        "expires_at": null,
        "views_count": 0,
        "favorites_count": 0,
        "comments_count": 0,
        "forks_count": 0,
        "forked_from_id": null,
        "version": 1,
        "is_pinned": false,
        "metadata": null,
        "created_at": "2026-01-05T12:00:00.000000Z",
        "updated_at": "2026-01-05T12:00:00.000000Z",
        "language": {
            "id": "language-uuid",
            "name": "JavaScript",
            "slug": "javascript"
        },
        "category": {
            "id": "category-uuid",
            "name": "Frontend",
            "slug": "frontend"
        },
        "tags": [
            { "id": "tag-uuid-1", "name": "react", "slug": "react" },
            { "id": "tag-uuid-2", "name": "hooks", "slug": "hooks" },
            { "id": "tag-uuid-3", "name": "javascript", "slug": "javascript" }
        ],
        "user": {
            "id": "your-user-uuid",
            "username": "yourusername",
            "full_name": "Your Name",
            "avatar_url": null
        }
    }
}
```

### Error Response (422 - Validation)

```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "title": ["The title field is required."],
        "code": ["The code field is required."],
        "visibility": ["The selected visibility is invalid."]
    }
}
```

### Error Response (422 - Team Required)

```json
{
    "success": false,
    "message": "Team ID is required for team visibility."
}
```

### Error Response (403 - Not Team Member)

```json
{
    "success": false,
    "message": "You are not a member of this team."
}
```

---

## 8. Update Snippet

| | |
|---|---|
| **Method** | `PUT` or `PATCH` |
| **URL** | `/snippets/{id}` |
| **Auth** | Yes (Owner only) |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | UUID | Yes | Snippet ID |

### Request Body Fields (All Optional)

| Field | Type | Description |
|-------|------|-------------|
| `title` | string | Max 255 characters |
| `description` | string | Max 5000 characters |
| `code` | string | Max ~1MB |
| `language_id` | UUID | Language UUID |
| `category_id` | UUID | Category UUID |
| `visibility` | string | Options: `public`, `private`, `team`, `unlisted` |
| `file_name` | string | Max 255 characters |
| `tags` | array | Replaces existing tags |
| `expires_at` | datetime | ISO 8601 format |
| `team_id` | UUID | For team visibility |
| `is_pinned` | boolean | Pin status |

### Example URL

```
{{base_url}}/snippets/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o
```

### Request Body (Update Title)

```json
{
    "title": "Updated Title"
}
```

### Request Body (Update Code)

```json
{
    "code": "// Updated code\nconsole.log('Hello World');"
}
```

### Request Body (Update Tags)

```json
{
    "tags": ["new-tag", "updated"]
}
```

### Request Body (Change Visibility)

```json
{
    "visibility": "private"
}
```

### Request Body (Pin Snippet)

```json
{
    "is_pinned": true
}
```

### Request Body (Multiple Fields)

```json
{
    "title": "Updated React Hook",
    "description": "Updated description",
    "code": "// New code here",
    "tags": ["react", "updated"],
    "is_pinned": true
}
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Snippet updated successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "title": "Updated Title",
        "slug": "updated-title",
        "version": 2,
        "updated_at": "2026-01-05T14:00:00.000000Z"
    }
}
```

### Error Response (403)

```json
{
    "success": false,
    "message": "You do not have permission to update this snippet."
}
```

### Error Response (404)

```json
{
    "success": false,
    "message": "Snippet not found."
}
```

---

## 9. Delete Snippet

| | |
|---|---|
| **Method** | `DELETE` |
| **URL** | `/snippets/{id}` |
| **Auth** | Yes (Owner only) |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | UUID | Yes | Snippet ID |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Snippet deleted successfully."
}
```

### Error Response (403)

```json
{
    "success": false,
    "message": "You do not have permission to delete this snippet."
}
```

### Error Response (404)

```json
{
    "success": false,
    "message": "Snippet not found."
}
```

---

## 10. Toggle Favorite

| | |
|---|---|
| **Method** | `POST` |
| **URL** | `/snippets/{id}/favorite` |
| **Auth** | Yes |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | UUID | Yes | Snippet ID |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/favorite
```

### Success Response - Added (200)

```json
{
    "success": true,
    "message": "Snippet added to favorites.",
    "data": {
        "is_favorited": true,
        "favorites_count": 26
    }
}
```

### Success Response - Removed (200)

```json
{
    "success": true,
    "message": "Snippet removed from favorites.",
    "data": {
        "is_favorited": false,
        "favorites_count": 25
    }
}
```

### Error Response (403)

```json
{
    "success": false,
    "message": "You do not have permission to favorite this snippet."
}
```

### Error Response (404)

```json
{
    "success": false,
    "message": "Snippet not found."
}
```

---

## 11. Fork Snippet

| | |
|---|---|
| **Method** | `POST` |
| **URL** | `/snippets/{id}/fork` |
| **Auth** | Yes |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | UUID | Yes | Snippet ID to fork |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/fork
```

### Success Response (201)

```json
{
    "success": true,
    "message": "Snippet forked successfully.",
    "data": {
        "id": "new-forked-uuid",
        "user_id": "your-user-uuid",
        "title": "React useEffect Hook Example (Fork)",
        "slug": "react-useeffect-hook-example-fork",
        "visibility": "private",
        "forked_from_id": "original-snippet-uuid",
        "version": 1,
        "forked_from": {
            "id": "original-snippet-uuid",
            "title": "React useEffect Hook Example",
            "slug": "react-useeffect-hook-example"
        }
    }
}
```

### Error Response (422)

```json
{
    "success": false,
    "message": "You cannot fork your own snippet."
}
```

### Error Response (403)

```json
{
    "success": false,
    "message": "You do not have permission to fork this snippet."
}
```

### Error Response (404)

```json
{
    "success": false,
    "message": "Snippet not found."
}
```

---

## 12. Get Forks of a Snippet

| | |
|---|---|
| **Method** | `GET` |
| **URL** | `/snippets/{id}/forks` |
| **Auth** | Yes |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `id` | UUID | Yes | Snippet ID |

### Query Parameters (Optional)

| Parameter | Type | Example | Description |
|-----------|------|---------|-------------|
| `per_page` | integer | `15` | Items per page |
| `page` | integer | `1` | Page number |

### Request Body

```
None
```

### Example URL

```
{{base_url}}/snippets/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/forks?per_page=10
```

### Success Response (200)

```json
{
    "success": true,
    "message": "Forks retrieved successfully.",
    "data": [
        {
            "id": "fork-uuid-1",
            "title": "React useEffect Hook Example (Fork)",
            "slug": "react-useeffect-hook-example-fork",
            "created_at": "2026-01-05T14:00:00.000000Z",
            "user": {
                "id": "forker-uuid",
                "username": "janedoe",
                "full_name": "Jane Doe",
                "avatar_url": null
            }
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 15,
        "total": 3
    }
}
```

### Error Response (403)

```json
{
    "success": false,
    "message": "You do not have permission to view this snippet."
}
```

### Error Response (404)

```json
{
    "success": false,
    "message": "Snippet not found."
}
```

---

# ERROR RESPONSES

## Unauthorized (401)

```json
{
    "message": "Unauthenticated."
}
```

## Rate Limit (429)

```json
{
    "message": "Too many requests. Please try again later."
}
```

## Server Error (500)

```json
{
    "success": false,
    "message": "Failed to create snippet.",
    "error": "Error details (only in debug mode)"
}
```

---

# POSTMAN VARIABLES

```
base_url = http://snippet-g11.test/api/v1
token = YOUR_BEARER_TOKEN
snippet_id = YOUR_SNIPPET_UUID
snippet_slug = your-snippet-slug
```

---

# QUICK REFERENCE

| # | Method | Endpoint | Auth | Description |
|---|--------|----------|------|-------------|
| 1 | `GET` | `/snippets/public` | No | Browse public snippets |
| 2 | `GET` | `/snippets/trending` | No | Get trending snippets |
| 3 | `GET` | `/snippets/featured` | No | Get featured snippets |
| 4 | `GET` | `/snippets/{slug}` | Optional | View single snippet |
| 5 | `GET` | `/snippets` | Yes | List user's snippets |
| 6 | `GET` | `/snippets/favorites` | Yes | Get user's favorites |
| 7 | `POST` | `/snippets` | Yes | Create snippet |
| 8 | `PUT` | `/snippets/{id}` | Yes | Update snippet |
| 9 | `DELETE` | `/snippets/{id}` | Yes | Delete snippet |
| 10 | `POST` | `/snippets/{id}/favorite` | Yes | Toggle favorite |
| 11 | `POST` | `/snippets/{id}/fork` | Yes | Fork snippet |
| 12 | `GET` | `/snippets/{id}/forks` | Yes | Get forks |
