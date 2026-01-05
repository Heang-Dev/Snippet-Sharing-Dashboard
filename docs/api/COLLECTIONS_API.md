# Collections API Documentation

Base URL: `/api/v1`

---

## Public Endpoints (No Authentication Required)

### 1. Get Public Collections

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/collections/public` | No |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by name or description |
| user_id | uuid | No | - | Filter by user ID |
| sort_by | string | No | created_at | Sort field: `name`, `created_at`, `updated_at`, `snippets_count` |
| sort_order | string | No | desc | Sort direction: `asc` or `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/collections/public?search=react&sort_by=snippets_count&sort_order=desc&per_page=10
```

**Success Response:**
```json
{
    "success": true,
    "message": "Public collections retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "name": "React Hooks Collection",
            "slug": "react-hooks-collection",
            "description": "A collection of useful React hooks snippets",
            "visibility": "public",
            "snippets_count": 15,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z",
            "user": {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": "https://example.com/avatar.jpg"
            }
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

### 2. Get Collection by ID

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/collections/{id}` | No* |

*Authentication not required for public collections. Private collections require authentication and ownership.

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Collection ID |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| with_snippets | boolean | No | false | Include snippets in response |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/collections/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o?with_snippets=true
```

**Success Response:**
```json
{
    "success": true,
    "message": "Collection retrieved successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "name": "React Hooks Collection",
        "slug": "react-hooks-collection",
        "description": "A collection of useful React hooks snippets",
        "visibility": "public",
        "snippets_count": 15,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z",
        "user": {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "username": "johndoe",
            "full_name": "John Doe",
            "avatar_url": "https://example.com/avatar.jpg"
        },
        "snippets": [
            {
                "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                "title": "useLocalStorage Hook",
                "slug": "uselocalstorage-hook",
                "description": "Custom hook for localStorage",
                "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "language_id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
                "visibility": "public",
                "view_count": 250,
                "created_at": "2025-01-01T00:00:00.000000Z",
                "user": {
                    "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "username": "johndoe",
                    "full_name": "John Doe",
                    "avatar_url": "https://example.com/avatar.jpg"
                },
                "language": {
                    "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
                    "name": "javascript",
                    "slug": "javascript",
                    "display_name": "JavaScript",
                    "color": "#f7df1e"
                }
            }
        ]
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Collection not found."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to view this collection."
}
```

---

### 3. Get Collection Snippets

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/collections/{id}/snippets` | No* |

*Authentication not required for public collections.

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Collection ID |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| per_page | integer | No | 20 | Items per page (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/collections/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/snippets?per_page=10
```

**Success Response:**
```json
{
    "success": true,
    "message": "Collection snippets retrieved successfully.",
    "data": [
        {
            "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "title": "useLocalStorage Hook",
            "slug": "uselocalstorage-hook",
            "description": "Custom hook for localStorage",
            "code": "const useLocalStorage = (key, initialValue) => {...}",
            "visibility": "public",
            "view_count": 250,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z",
            "pivot": {
                "collection_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "snippet_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                "sort_order": 1,
                "created_at": "2025-01-01T00:00:00.000000Z",
                "updated_at": "2025-01-01T00:00:00.000000Z"
            },
            "user": {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": "https://example.com/avatar.jpg"
            },
            "language": {
                "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
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
                }
            ]
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 2,
        "per_page": 10,
        "total": 15
    }
}
```

---

## Protected Endpoints (Authentication Required)

All endpoints below require the `Authorization: Bearer {token}` header.

---

### 4. Get My Collections

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/collections` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by name or description |
| visibility | string | No | - | Filter: `public` or `private` |
| sort_by | string | No | updated_at | Sort field: `name`, `created_at`, `updated_at`, `snippets_count` |
| sort_order | string | No | desc | Sort direction: `asc` or `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/collections?visibility=public&sort_by=name&sort_order=asc
```

**Success Response:**
```json
{
    "success": true,
    "message": "Collections retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "name": "My Private Collection",
            "slug": "my-private-collection",
            "description": "Personal code snippets",
            "visibility": "private",
            "snippets_count": 10,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
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

### 5. Create Collection

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/collections` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "My Awesome Collection",
    "description": "A collection of my best code snippets",
    "visibility": "public"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Collection name (max: 255) |
| description | string | No | Collection description (max: 1000) |
| visibility | string | Yes | `public` or `private` |

**Example URL:**
```
POST /api/v1/collections
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Collection created successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "name": "My Awesome Collection",
        "slug": "my-awesome-collection",
        "description": "A collection of my best code snippets",
        "visibility": "public",
        "snippets_count": 0,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "name": ["The name field is required."],
        "visibility": ["The selected visibility is invalid."]
    }
}
```

---

### 6. Update Collection

| Method | URL | Auth Required |
|--------|-----|---------------|
| PUT/PATCH | `/api/v1/collections/{id}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Collection ID |

**Request Body:**
```json
{
    "name": "Updated Collection Name",
    "description": "Updated description",
    "visibility": "private"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | No | Collection name (max: 255) |
| description | string | No | Collection description (max: 1000) |
| visibility | string | No | `public` or `private` |

**Example URL:**
```
PUT /api/v1/collections/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o
```

**Success Response:**
```json
{
    "success": true,
    "message": "Collection updated successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "name": "Updated Collection Name",
        "slug": "updated-collection-name",
        "description": "Updated description",
        "visibility": "private",
        "snippets_count": 10,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-02T00:00:00.000000Z"
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to update this collection."
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Collection not found."
}
```

---

### 7. Delete Collection

| Method | URL | Auth Required |
|--------|-----|---------------|
| DELETE | `/api/v1/collections/{id}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Collection ID |

**Request Body:**
```
None
```

**Example URL:**
```
DELETE /api/v1/collections/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o
```

**Success Response:**
```json
{
    "success": true,
    "message": "Collection deleted successfully."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to delete this collection."
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Collection not found."
}
```

---

### 8. Add Snippet to Collection

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/collections/{id}/snippets` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Collection ID |

**Request Body:**
```json
{
    "snippet_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| snippet_id | uuid | Yes | ID of snippet to add |

**Example URL:**
```
POST /api/v1/collections/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/snippets
```

**Success Response:**
```json
{
    "success": true,
    "message": "Snippet added to collection successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "name": "My Collection",
        "slug": "my-collection",
        "description": "Collection description",
        "visibility": "public",
        "snippets_count": 11,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-02T00:00:00.000000Z",
        "snippets": [...]
    }
}
```

**Error Response (403 - Collection):**
```json
{
    "success": false,
    "message": "You do not have permission to modify this collection."
}
```

**Error Response (403 - Snippet):**
```json
{
    "success": false,
    "message": "You cannot add this snippet to your collection."
}
```

**Error Response (422 - Already exists):**
```json
{
    "success": false,
    "message": "Snippet is already in this collection."
}
```

**Error Response (422 - Validation):**
```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "snippet_id": ["The selected snippet id is invalid."]
    }
}
```

---

### 9. Remove Snippet from Collection

| Method | URL | Auth Required |
|--------|-----|---------------|
| DELETE | `/api/v1/collections/{id}/snippets/{snippetId}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Collection ID |
| snippetId | uuid | Yes | Snippet ID to remove |

**Request Body:**
```
None
```

**Example URL:**
```
DELETE /api/v1/collections/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/snippets/7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r
```

**Success Response:**
```json
{
    "success": true,
    "message": "Snippet removed from collection successfully."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to modify this collection."
}
```

**Error Response (404 - Collection):**
```json
{
    "success": false,
    "message": "Collection not found."
}
```

**Error Response (404 - Snippet):**
```json
{
    "success": false,
    "message": "Snippet is not in this collection."
}
```

---

### 10. Reorder Snippets in Collection

| Method | URL | Auth Required |
|--------|-----|---------------|
| PUT | `/api/v1/collections/{id}/reorder` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Collection ID |

**Request Body:**
```json
{
    "snippet_ids": [
        "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
        "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
        "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p"
    ]
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| snippet_ids | array | Yes | Array of snippet IDs in desired order |

**Example URL:**
```
PUT /api/v1/collections/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/reorder
```

**Success Response:**
```json
{
    "success": true,
    "message": "Snippets reordered successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "name": "My Collection",
        "slug": "my-collection",
        "description": "Collection description",
        "visibility": "public",
        "snippets_count": 3,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-02T00:00:00.000000Z",
        "snippets": [...]
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to modify this collection."
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "snippet_ids": ["The snippet ids field is required."],
        "snippet_ids.0": ["The selected snippet_ids.0 is invalid."]
    }
}
```

---

## Quick Reference

| # | Endpoint | Method | Auth | Description |
|---|----------|--------|------|-------------|
| 1 | `/api/v1/collections/public` | GET | No | Get public collections |
| 2 | `/api/v1/collections/{id}` | GET | No* | Get collection by ID |
| 3 | `/api/v1/collections/{id}/snippets` | GET | No* | Get collection snippets |
| 4 | `/api/v1/collections` | GET | Yes | Get my collections |
| 5 | `/api/v1/collections` | POST | Yes | Create collection |
| 6 | `/api/v1/collections/{id}` | PUT/PATCH | Yes | Update collection |
| 7 | `/api/v1/collections/{id}` | DELETE | Yes | Delete collection |
| 8 | `/api/v1/collections/{id}/snippets` | POST | Yes | Add snippet to collection |
| 9 | `/api/v1/collections/{id}/snippets/{snippetId}` | DELETE | Yes | Remove snippet from collection |
| 10 | `/api/v1/collections/{id}/reorder` | PUT | Yes | Reorder snippets |

*No auth required for public collections; auth required for private collections.
