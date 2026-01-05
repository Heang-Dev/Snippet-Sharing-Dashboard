# Notifications API Documentation

Base URL: `/api/v1`

All endpoints require Bearer token in the Authorization header.

---

## Endpoints Overview

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/notifications` | Get all notifications | Yes |
| GET | `/notifications/unread-count` | Get unread count | Yes |
| GET | `/notifications/types` | Get notification types | Yes |
| GET | `/notifications/settings` | Get notification settings | Yes |
| PUT | `/notifications/settings` | Update notification settings | Yes |
| POST | `/notifications/mark-all-read` | Mark all as read | Yes |
| POST | `/notifications/mark-read` | Mark multiple as read | Yes |
| DELETE | `/notifications/batch` | Delete multiple | Yes |
| DELETE | `/notifications/read` | Delete all read | Yes |
| DELETE | `/notifications/all` | Delete all | Yes |
| GET | `/notifications/{id}` | Get notification | Yes |
| POST | `/notifications/{id}/read` | Mark as read | Yes |
| POST | `/notifications/{id}/unread` | Mark as unread | Yes |
| DELETE | `/notifications/{id}` | Delete notification | Yes |

---

## Notification Types

| Type | Description |
|------|-------------|
| `follow` | New follower |
| `comment` | New comment on your snippet |
| `reply` | Reply to your comment |
| `favorite` | Someone favorited your snippet |
| `fork` | Someone forked your snippet |
| `mention` | You were mentioned |
| `team_invite` | Team invitation |
| `team_join` | New team member |
| `team_leave` | Member left team |
| `share` | Snippet shared with you |
| `system` | System notification |

---

## Get All Notifications

Retrieve notifications for the authenticated user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/notifications` | Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| is_read | boolean | No | - | Filter by read status |
| type | string | No | - | Filter by single type |
| types | string | No | - | Filter by multiple types (comma-separated) |
| sort_by | string | No | created_at | Sort field: `created_at`, `read_at` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notifications retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "user_id": "550e8400-e29b-41d4-a716-446655440001",
      "type": "follow",
      "title": "New Follower",
      "message": "John Doe started following you",
      "link": "/users/johndoe",
      "icon": "user-plus",
      "actor_id": "550e8400-e29b-41d4-a716-446655440002",
      "related_resource_type": "user",
      "related_resource_id": "550e8400-e29b-41d4-a716-446655440002",
      "is_read": false,
      "read_at": null,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z",
      "actor": {
        "id": "550e8400-e29b-41d4-a716-446655440002",
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
    "total": 100,
    "unread_count": 15
  }
}
```

---

## Get Unread Count

Get the count of unread notifications.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/notifications/unread-count` | Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Unread count retrieved successfully.",
  "data": {
    "total": 15,
    "by_type": {
      "follow": 5,
      "comment": 3,
      "favorite": 7
    }
  }
}
```

---

## Get Notification Types

Get all available notification types.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/notifications/types` | Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notification types retrieved successfully.",
  "data": [
    {
      "type": "follow",
      "description": "New follower"
    },
    {
      "type": "comment",
      "description": "New comment on your snippet"
    },
    {
      "type": "reply",
      "description": "Reply to your comment"
    }
  ]
}
```

---

## Get Notification Settings

Get the user's notification preferences.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/notifications/settings` | Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notification settings retrieved successfully.",
  "data": {
    "email_notifications": true,
    "push_notifications": true,
    "follow_notifications": true,
    "comment_notifications": true,
    "favorite_notifications": true,
    "fork_notifications": true,
    "mention_notifications": true,
    "team_notifications": true,
    "share_notifications": true,
    "system_notifications": true
  }
}
```

---

## Update Notification Settings

Update the user's notification preferences.

| Method | URL | Auth |
|--------|-----|------|
| PUT | `/api/v1/notifications/settings` | Required |

### Request Body

```json
{
  "email_notifications": false,
  "follow_notifications": true,
  "comment_notifications": true
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| email_notifications | boolean | No | Enable email notifications |
| push_notifications | boolean | No | Enable push notifications |
| follow_notifications | boolean | No | Enable follow notifications |
| comment_notifications | boolean | No | Enable comment notifications |
| favorite_notifications | boolean | No | Enable favorite notifications |
| fork_notifications | boolean | No | Enable fork notifications |
| mention_notifications | boolean | No | Enable mention notifications |
| team_notifications | boolean | No | Enable team notifications |
| share_notifications | boolean | No | Enable share notifications |
| system_notifications | boolean | No | Enable system notifications |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notification settings updated successfully.",
  "data": {
    "email_notifications": false,
    "push_notifications": true,
    "follow_notifications": true,
    "comment_notifications": true,
    "favorite_notifications": true,
    "fork_notifications": true,
    "mention_notifications": true,
    "team_notifications": true,
    "share_notifications": true,
    "system_notifications": true
  }
}
```

---

## Get Single Notification

Retrieve a specific notification.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/notifications/{id}` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Notification ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notification retrieved successfully.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "user_id": "550e8400-e29b-41d4-a716-446655440001",
    "type": "comment",
    "title": "New Comment",
    "message": "John Doe commented on your snippet",
    "link": "/snippets/my-snippet#comment-123",
    "icon": "message-circle",
    "actor_id": "550e8400-e29b-41d4-a716-446655440002",
    "related_resource_type": "comment",
    "related_resource_id": "550e8400-e29b-41d4-a716-446655440003",
    "is_read": false,
    "read_at": null,
    "created_at": "2024-01-15T10:30:00.000000Z",
    "updated_at": "2024-01-15T10:30:00.000000Z",
    "actor": {
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
  "message": "Notification not found."
}
```

---

## Mark Notification as Read

Mark a specific notification as read.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/notifications/{id}/read` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Notification ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notification marked as read.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "is_read": true,
    "read_at": "2024-01-15T11:00:00.000000Z"
  }
}
```

---

## Mark Notification as Unread

Mark a specific notification as unread.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/notifications/{id}/unread` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Notification ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notification marked as unread.",
  "data": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "is_read": false,
    "read_at": null
  }
}
```

---

## Mark All Notifications as Read

Mark all notifications as read.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/notifications/mark-all-read` | Required |

### Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| type | string | No | Filter by notification type |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Marked 15 notifications as read.",
  "data": {
    "updated_count": 15
  }
}
```

---

## Mark Multiple Notifications as Read

Mark specific notifications as read.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/notifications/mark-read` | Required |

### Request Body

```json
{
  "ids": [
    "550e8400-e29b-41d4-a716-446655440000",
    "550e8400-e29b-41d4-a716-446655440001",
    "550e8400-e29b-41d4-a716-446655440002"
  ]
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| ids | array | Yes | Array of notification UUIDs |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Marked 3 notifications as read.",
  "data": {
    "updated_count": 3
  }
}
```

### Error Response (422 Validation Error)

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "ids": ["The ids field is required."],
    "ids.0": ["The ids.0 must be a valid UUID."]
  }
}
```

---

## Delete Notification

Delete a specific notification.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/notifications/{id}` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Notification ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notification deleted successfully."
}
```

---

## Delete Multiple Notifications

Delete specific notifications.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/notifications/batch` | Required |

### Request Body

```json
{
  "ids": [
    "550e8400-e29b-41d4-a716-446655440000",
    "550e8400-e29b-41d4-a716-446655440001"
  ]
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| ids | array | Yes | Array of notification UUIDs |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Deleted 2 notifications.",
  "data": {
    "deleted_count": 2
  }
}
```

---

## Delete All Read Notifications

Delete all notifications that have been read.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/notifications/read` | Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Deleted 50 read notifications.",
  "data": {
    "deleted_count": 50
  }
}
```

---

## Delete All Notifications

Delete all notifications for the user.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/notifications/all` | Required |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Deleted 100 notifications.",
  "data": {
    "deleted_count": 100
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
  "message": "Notification not found."
}
```

### 422 Validation Error

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "ids": ["The ids field is required."]
  }
}
```

---

## Quick Reference

### List & Count
```
GET /api/v1/notifications                 - Get all notifications
GET /api/v1/notifications/unread-count    - Get unread count
GET /api/v1/notifications/types           - Get notification types
GET /api/v1/notifications/{id}            - Get single notification
```

### Mark as Read/Unread
```
POST /api/v1/notifications/{id}/read      - Mark single as read
POST /api/v1/notifications/{id}/unread    - Mark single as unread
POST /api/v1/notifications/mark-all-read  - Mark all as read
POST /api/v1/notifications/mark-read      - Mark multiple as read
```

### Delete
```
DELETE /api/v1/notifications/{id}         - Delete single
DELETE /api/v1/notifications/batch        - Delete multiple
DELETE /api/v1/notifications/read         - Delete all read
DELETE /api/v1/notifications/all          - Delete all
```

### Settings
```
GET /api/v1/notifications/settings        - Get settings
PUT /api/v1/notifications/settings        - Update settings
```
