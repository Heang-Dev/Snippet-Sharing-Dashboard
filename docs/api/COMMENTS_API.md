# Comments API Documentation

Base URL: `/api/v1`

---

## Public Endpoints (No Authentication Required)

### 1. Get Comments for a Snippet

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/snippets/{snippetId}/comments` | No* |

*No auth required for public snippets. Private snippets require authentication and ownership.

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| all | boolean | No | false | Get all comments (flat list). Default returns root comments with nested replies |
| sort_order | string | No | desc | Sort direction: `asc` or `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/snippets/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/comments?sort_order=asc&per_page=10
```

**Success Response (With Nested Replies):**
```json
{
    "success": true,
    "message": "Comments retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "user_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "parent_id": null,
            "content": "This is a great snippet! Very helpful.",
            "is_edited": false,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z",
            "user": {
                "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": "https://example.com/avatar.jpg"
            },
            "replies": [
                {
                    "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
                    "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "user_id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                    "parent_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                    "content": "Thanks! Glad you found it helpful.",
                    "is_edited": false,
                    "created_at": "2025-01-01T01:00:00.000000Z",
                    "updated_at": "2025-01-01T01:00:00.000000Z",
                    "user": {
                        "id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                        "username": "janesmith",
                        "full_name": "Jane Smith",
                        "avatar_url": "https://example.com/avatar2.jpg"
                    }
                }
            ]
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

**Error Response (404):**
```json
{
    "success": false,
    "message": "Snippet not found."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to view comments for this snippet."
}
```

---

### 2. Get Comment by ID

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/comments/{id}` | No* |

*No auth required for comments on public snippets.

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Comment ID |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| with_replies | boolean | No | false | Include replies to this comment |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/comments/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o?with_replies=true
```

**Success Response:**
```json
{
    "success": true,
    "message": "Comment retrieved successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "user_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
        "parent_id": null,
        "content": "This is a great snippet! Very helpful.",
        "is_edited": false,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z",
        "user": {
            "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "username": "johndoe",
            "full_name": "John Doe",
            "avatar_url": "https://example.com/avatar.jpg"
        },
        "snippet": {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "title": "React useLocalStorage Hook",
            "slug": "react-uselocalstorage-hook",
            "user_id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
            "visibility": "public"
        },
        "replies": [
            {
                "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
                "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "user_id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                "parent_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "content": "Thanks! Glad you found it helpful.",
                "is_edited": false,
                "created_at": "2025-01-01T01:00:00.000000Z",
                "updated_at": "2025-01-01T01:00:00.000000Z",
                "user": {
                    "id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                    "username": "janesmith",
                    "full_name": "Jane Smith",
                    "avatar_url": "https://example.com/avatar2.jpg"
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
    "message": "Comment not found."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to view this comment."
}
```

---

### 3. Get Replies to a Comment

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/comments/{id}/replies` | No* |

*No auth required for comments on public snippets.

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Parent comment ID |

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
GET /api/v1/comments/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/replies?per_page=10
```

**Success Response:**
```json
{
    "success": true,
    "message": "Replies retrieved successfully.",
    "data": [
        {
            "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
            "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "user_id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
            "parent_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "content": "Thanks! Glad you found it helpful.",
            "is_edited": false,
            "created_at": "2025-01-01T01:00:00.000000Z",
            "updated_at": "2025-01-01T01:00:00.000000Z",
            "user": {
                "id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                "username": "janesmith",
                "full_name": "Jane Smith",
                "avatar_url": "https://example.com/avatar2.jpg"
            }
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

**Error Response (404):**
```json
{
    "success": false,
    "message": "Comment not found."
}
```

---

## Protected Endpoints (Authentication Required)

All endpoints below require the `Authorization: Bearer {token}` header.

---

### 4. Create Comment

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/snippets/{snippetId}/comments` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| snippetId | uuid | Yes | Snippet ID to comment on |

**Request Body:**
```json
{
    "content": "This is a great snippet! Very helpful for my project.",
    "parent_id": null
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| content | string | Yes | Comment content (1-5000 characters) |
| parent_id | uuid | No | Parent comment ID for replies |

**Example URL:**
```
POST /api/v1/snippets/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/comments
```

**Example Request Body (New Comment):**
```json
{
    "content": "This is a great snippet! Very helpful for my project."
}
```

**Example Request Body (Reply):**
```json
{
    "content": "Thanks for the feedback!",
    "parent_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q"
}
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Comment created successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "user_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
        "parent_id": null,
        "content": "This is a great snippet! Very helpful for my project.",
        "is_edited": false,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z",
        "user": {
            "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "username": "johndoe",
            "full_name": "John Doe",
            "avatar_url": "https://example.com/avatar.jpg"
        }
    }
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Snippet not found."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to comment on this snippet."
}
```

**Error Response (422 - Validation):**
```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "content": ["The content field is required."]
    }
}
```

**Error Response (422 - Invalid Parent):**
```json
{
    "success": false,
    "message": "Invalid parent comment."
}
```

---

### 5. Update Comment

| Method | URL | Auth Required |
|--------|-----|---------------|
| PUT/PATCH | `/api/v1/comments/{id}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Comment ID |

**Request Body:**
```json
{
    "content": "Updated comment content with more details."
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| content | string | Yes | Updated comment content (1-5000 characters) |

**Example URL:**
```
PUT /api/v1/comments/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o
```

**Success Response:**
```json
{
    "success": true,
    "message": "Comment updated successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "user_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
        "parent_id": null,
        "content": "Updated comment content with more details.",
        "is_edited": true,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-02T00:00:00.000000Z",
        "user": {
            "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "username": "johndoe",
            "full_name": "John Doe",
            "avatar_url": "https://example.com/avatar.jpg"
        }
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to update this comment."
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Comment not found."
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "content": ["The content field is required."]
    }
}
```

---

### 6. Delete Comment

| Method | URL | Auth Required |
|--------|-----|---------------|
| DELETE | `/api/v1/comments/{id}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Comment ID |

**Request Body:**
```
None
```

**Example URL:**
```
DELETE /api/v1/comments/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o
```

**Success Response:**
```json
{
    "success": true,
    "message": "Comment deleted successfully."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to delete this comment."
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Comment not found."
}
```

**Note:**
- Comment owner can delete their own comments
- Snippet owner can delete any comment on their snippet
- Deleting a root comment will also delete all its replies

---

### 7. Get My Comments

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/comments/me` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| sort_by | string | No | created_at | Sort field: `created_at`, `updated_at` |
| sort_order | string | No | desc | Sort direction: `asc` or `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/comments/me?sort_by=created_at&sort_order=desc&per_page=10
```

**Success Response:**
```json
{
    "success": true,
    "message": "Your comments retrieved successfully.",
    "data": [
        {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "user_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "parent_id": null,
            "content": "This is a great snippet! Very helpful.",
            "is_edited": false,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z",
            "snippet": {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "title": "React useLocalStorage Hook",
                "slug": "react-uselocalstorage-hook",
                "user_id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                "visibility": "public"
            },
            "parent": null
        },
        {
            "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
            "snippet_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "user_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "parent_id": "4a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p",
            "content": "I agree with this point!",
            "is_edited": true,
            "created_at": "2025-01-02T00:00:00.000000Z",
            "updated_at": "2025-01-03T00:00:00.000000Z",
            "snippet": {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "title": "React useLocalStorage Hook",
                "slug": "react-uselocalstorage-hook",
                "user_id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
                "visibility": "public"
            },
            "parent": {
                "id": "4a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p",
                "content": "The original parent comment content...",
                "user_id": "3a1b2c3d-4e5f-6g7h-8i9j-0k1l2m3n4o5p"
            }
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

## Quick Reference

| # | Endpoint | Method | Auth | Description |
|---|----------|--------|------|-------------|
| 1 | `/api/v1/snippets/{snippetId}/comments` | GET | No* | Get comments for a snippet |
| 2 | `/api/v1/comments/{id}` | GET | No* | Get comment by ID |
| 3 | `/api/v1/comments/{id}/replies` | GET | No* | Get replies to a comment |
| 4 | `/api/v1/snippets/{snippetId}/comments` | POST | Yes | Create comment |
| 5 | `/api/v1/comments/{id}` | PUT/PATCH | Yes | Update comment |
| 6 | `/api/v1/comments/{id}` | DELETE | Yes | Delete comment |
| 7 | `/api/v1/comments/me` | GET | Yes | Get my comments |

*No auth required for public snippets; auth required for private snippets.

---

## Comment Features

- **Nested Replies**: Comments support one level of nesting (replies to root comments)
- **Edit Tracking**: `is_edited` flag indicates if a comment has been modified
- **Soft Deletes**: Deleted comments are soft-deleted and can be restored
- **Ownership Permissions**:
  - Users can edit/delete their own comments
  - Snippet owners can delete any comment on their snippets
- **Cascade Delete**: Deleting a root comment also deletes all its replies
