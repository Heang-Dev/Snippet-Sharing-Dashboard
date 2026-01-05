# Follow/Followers API Documentation

Base URL: `/api/v1`

All authenticated endpoints require Bearer token in the Authorization header.

---

## Endpoints Overview

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/users/{userId}/follow` | Follow a user | Yes |
| DELETE | `/users/{userId}/follow` | Unfollow a user | Yes |
| POST | `/users/{userId}/follow/toggle` | Toggle follow status | Yes |
| GET | `/users/{userId}/follow/check` | Check follow status | Yes |
| GET | `/users/{userId}/followers` | Get user's followers | Yes |
| GET | `/users/{userId}/following` | Get users that user follows | Yes |
| GET | `/users/{userId}/follow/stats` | Get follow statistics | Yes |
| PUT | `/users/{userId}/follow/notifications` | Update notification settings | Yes |
| GET | `/users/{userId}/mutual-followers` | Get mutual followers | Yes |
| GET | `/follows/followers` | Get my followers | Yes |
| GET | `/follows/following` | Get users I follow | Yes |
| GET | `/follows/suggestions` | Get follow suggestions | Yes |

---

## Follow a User

Follow another user.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/users/{userId}/follow` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Request Body

None required.

### Success Response (201 Created)

```json
{
  "success": true,
  "message": "Successfully followed user.",
  "data": {
    "following": true,
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "username": "johndoe",
      "full_name": "John Doe",
      "avatar_url": "https://example.com/avatar.jpg"
    }
  }
}
```

### Error Responses

**404 Not Found - User not found**
```json
{
  "success": false,
  "message": "User not found."
}
```

**422 Unprocessable Entity - Cannot follow yourself**
```json
{
  "success": false,
  "message": "You cannot follow yourself."
}
```

**422 Unprocessable Entity - Already following**
```json
{
  "success": false,
  "message": "You are already following this user."
}
```

---

## Unfollow a User

Unfollow a user you are following.

| Method | URL | Auth |
|--------|-----|------|
| DELETE | `/api/v1/users/{userId}/follow` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Successfully unfollowed user.",
  "data": {
    "following": false,
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "username": "johndoe",
      "full_name": "John Doe",
      "avatar_url": "https://example.com/avatar.jpg"
    }
  }
}
```

### Error Responses

**404 Not Found - User not found**
```json
{
  "success": false,
  "message": "User not found."
}
```

**422 Unprocessable Entity - Not following**
```json
{
  "success": false,
  "message": "You are not following this user."
}
```

---

## Toggle Follow Status

Toggle follow/unfollow status for a user.

| Method | URL | Auth |
|--------|-----|------|
| POST | `/api/v1/users/{userId}/follow/toggle` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Successfully followed user.",
  "data": {
    "following": true,
    "user": {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "username": "johndoe",
      "full_name": "John Doe",
      "avatar_url": "https://example.com/avatar.jpg"
    }
  }
}
```

---

## Check Follow Status

Check if you are following a specific user and if they follow you back.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/users/{userId}/follow/check` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Follow status retrieved successfully.",
  "data": {
    "following": true,
    "followed_by": false,
    "mutual": false
  }
}
```

---

## Get User's Followers

Get the list of users who follow a specific user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/users/{userId}/followers` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by username or full_name |
| sort_by | string | No | created_at | Sort field: `created_at`, `username`, `full_name` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Followers retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440001",
      "username": "janedoe",
      "full_name": "Jane Doe",
      "avatar_url": "https://example.com/jane-avatar.jpg",
      "bio": "Software developer",
      "is_following": true,
      "is_followed_by": true,
      "followed_at": "2024-01-15T10:30:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 45
  }
}
```

---

## Get User's Following

Get the list of users that a specific user follows.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/users/{userId}/following` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by username or full_name |
| sort_by | string | No | created_at | Sort field: `created_at`, `username`, `full_name` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Following list retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440002",
      "username": "bobsmith",
      "full_name": "Bob Smith",
      "avatar_url": "https://example.com/bob-avatar.jpg",
      "bio": "Full-stack developer",
      "is_following": true,
      "is_followed_by": false,
      "followed_at": "2024-01-10T08:15:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 20,
    "total": 30
  }
}
```

---

## Get Follow Statistics

Get follow counts and relationship status for a user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/users/{userId}/follow/stats` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Follow statistics retrieved successfully.",
  "data": {
    "user_id": "550e8400-e29b-41d4-a716-446655440000",
    "username": "johndoe",
    "followers_count": 150,
    "following_count": 75,
    "is_following": true,
    "is_followed_by": false,
    "is_mutual": false
  }
}
```

---

## Update Notification Settings

Update notification preferences for a user you follow.

| Method | URL | Auth |
|--------|-----|------|
| PUT | `/api/v1/users/{userId}/follow/notifications` | Required |

### Path Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| userId | uuid | Yes | Target user's ID |

### Request Body

```json
{
  "notification_enabled": false
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| notification_enabled | boolean | Yes | Enable/disable notifications for this user |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Notification settings updated successfully.",
  "data": {
    "user_id": "550e8400-e29b-41d4-a716-446655440000",
    "notification_enabled": false
  }
}
```

### Error Responses

**422 Unprocessable Entity - Not following**
```json
{
  "success": false,
  "message": "You are not following this user."
}
```

---

## Get Mutual Followers

Get users that both you and another user follow.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/users/{userId}/mutual-followers` | Required |

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
  "message": "Mutual followers retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440003",
      "username": "alicewonder",
      "full_name": "Alice Wonder",
      "avatar_url": "https://example.com/alice-avatar.jpg",
      "bio": "UI/UX Designer"
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

## Get My Followers

Get the list of users who follow the authenticated user.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/follows/followers` | Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by username or full_name |
| sort_by | string | No | created_at | Sort field: `created_at`, `username`, `full_name` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Followers retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440001",
      "username": "follower1",
      "full_name": "Follower One",
      "avatar_url": "https://example.com/follower1.jpg",
      "bio": "Developer",
      "is_following": false,
      "is_followed_by": true,
      "followed_at": "2024-01-15T10:30:00.000000Z"
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

## Get My Following

Get the list of users that the authenticated user follows.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/follows/following` | Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search by username or full_name |
| sort_by | string | No | created_at | Sort field: `created_at`, `username`, `full_name` |
| sort_order | string | No | desc | Sort order: `asc`, `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |
| page | integer | No | 1 | Page number |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Following list retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440002",
      "username": "following1",
      "full_name": "Following One",
      "avatar_url": "https://example.com/following1.jpg",
      "bio": "Designer",
      "is_following": true,
      "is_followed_by": false,
      "followed_at": "2024-01-10T08:15:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "per_page": 20,
    "total": 30
  }
}
```

---

## Get Follow Suggestions

Get suggested users to follow based on mutual connections and popularity.

| Method | URL | Auth |
|--------|-----|------|
| GET | `/api/v1/follows/suggestions` | Required |

### Query Parameters

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| limit | integer | No | 10 | Number of suggestions (max: 50) |

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Follow suggestions retrieved successfully.",
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440004",
      "username": "suggested1",
      "full_name": "Suggested User One",
      "avatar_url": "https://example.com/suggested1.jpg",
      "bio": "Backend Developer",
      "followers_count": 500,
      "snippets_count": 25,
      "mutual_followers_count": 5
    },
    {
      "id": "550e8400-e29b-41d4-a716-446655440005",
      "username": "suggested2",
      "full_name": "Suggested User Two",
      "avatar_url": "https://example.com/suggested2.jpg",
      "bio": "Frontend Developer",
      "followers_count": 300,
      "snippets_count": 15,
      "mutual_followers_count": 3
    }
  ]
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

### 422 Validation Error

```json
{
  "success": false,
  "message": "Validation failed.",
  "errors": {
    "notification_enabled": ["The notification enabled field is required."]
  }
}
```

---

## Quick Reference

### Follow Actions
```
POST   /api/v1/users/{userId}/follow           - Follow user
DELETE /api/v1/users/{userId}/follow           - Unfollow user
POST   /api/v1/users/{userId}/follow/toggle    - Toggle follow
GET    /api/v1/users/{userId}/follow/check     - Check status
```

### User Lists
```
GET /api/v1/users/{userId}/followers           - Get user's followers
GET /api/v1/users/{userId}/following           - Get user's following
GET /api/v1/users/{userId}/follow/stats        - Get follow stats
GET /api/v1/users/{userId}/mutual-followers    - Get mutual followers
```

### My Follow Data
```
GET /api/v1/follows/followers    - My followers
GET /api/v1/follows/following    - Who I follow
GET /api/v1/follows/suggestions  - Suggested users
```

### Settings
```
PUT /api/v1/users/{userId}/follow/notifications - Update notifications
```
