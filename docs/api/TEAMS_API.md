# Teams & Invitations API Documentation

Base URL: `/api/v1`

All endpoints require authentication (`Authorization: Bearer {token}`) unless otherwise noted.

---

## Team Roles & Permissions

| Permission | Owner | Admin | Member | Viewer |
|------------|:-----:|:-----:|:------:|:------:|
| View team & snippets | ✅ | ✅ | ✅ | ✅ |
| Create snippets | ✅ | ✅ | ✅ | ❌ |
| Edit own snippets | ✅ | ✅ | ✅ | ❌ |
| Edit any snippet | ✅ | ✅ | ❌ | ❌ |
| Invite members | ✅ | ✅ | ❌ | ❌ |
| Remove members | ✅ | ✅ | ❌ | ❌ |
| Remove admins | ✅ | ❌ | ❌ | ❌ |
| Change roles | ✅ | ❌ | ❌ | ❌ |
| Update team | ✅ | ❌ | ❌ | ❌ |
| Delete team | ✅ | ❌ | ❌ | ❌ |
| Transfer ownership | ✅ | ❌ | ❌ | ❌ |

---

## Team Endpoints

### 1. Get My Teams

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/teams` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/teams
```

**Success Response:**
```json
{
    "success": true,
    "message": "Teams retrieved successfully.",
    "data": {
        "owned": [
            {
                "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "name": "My Development Team",
                "slug": "my-development-team",
                "description": "A team for development snippets",
                "owner_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "avatar_url": null,
                "is_active": true,
                "created_at": "2025-01-01T00:00:00.000000Z",
                "updated_at": "2025-01-01T00:00:00.000000Z",
                "members_count": 5,
                "snippets_count": 25,
                "owner": {
                    "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "username": "johndoe",
                    "full_name": "John Doe",
                    "avatar_url": "https://example.com/avatar.jpg"
                }
            }
        ],
        "member_of": [
            {
                "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                "name": "Frontend Team",
                "slug": "frontend-team",
                "description": "Frontend development team",
                "owner_id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
                "avatar_url": null,
                "is_active": true,
                "created_at": "2025-01-01T00:00:00.000000Z",
                "updated_at": "2025-01-01T00:00:00.000000Z",
                "members_count": 8,
                "snippets_count": 42,
                "owner": {
                    "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
                    "username": "janesmith",
                    "full_name": "Jane Smith",
                    "avatar_url": "https://example.com/avatar2.jpg"
                }
            }
        ]
    }
}
```

---

### 2. Create Team

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/teams` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "name": "My New Team",
    "description": "A team for sharing code snippets"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | Yes | Team name (max: 100) |
| description | string | No | Team description (max: 1000) |

**Example URL:**
```
POST /api/v1/teams
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Team created successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "name": "My New Team",
        "slug": "my-new-team",
        "description": "A team for sharing code snippets",
        "owner_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "avatar_url": null,
        "is_active": true,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z",
        "members_count": 1,
        "snippets_count": 0,
        "owner": {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "username": "johndoe",
            "full_name": "John Doe",
            "avatar_url": "https://example.com/avatar.jpg"
        }
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Validation failed.",
    "errors": {
        "name": ["The name field is required."]
    }
}
```

---

### 3. Get Team Details

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/teams/{id}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| with_members | boolean | No | false | Include team members |
| with_snippets | boolean | No | false | Include recent snippets (limit 10) |
| with_invitations | boolean | No | false | Include pending invitations (owner/admin only) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o?with_members=true&with_invitations=true
```

**Success Response:**
```json
{
    "success": true,
    "message": "Team retrieved successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "name": "My Development Team",
        "slug": "my-development-team",
        "description": "A team for development snippets",
        "owner_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "avatar_url": null,
        "is_active": true,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z",
        "members_count": 3,
        "snippets_count": 25,
        "user_role": "admin",
        "is_owner": true,
        "owner": {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "username": "johndoe",
            "full_name": "John Doe",
            "avatar_url": "https://example.com/avatar.jpg"
        },
        "members": [
            {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": "https://example.com/avatar.jpg",
                "pivot": {
                    "team_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                    "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "role": "admin",
                    "created_at": "2025-01-01T00:00:00.000000Z"
                }
            }
        ],
        "invitations": [
            {
                "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
                "team_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "email": "newmember@example.com",
                "role": "member",
                "expires_at": "2025-01-08T00:00:00.000000Z",
                "inviter": {
                    "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                    "username": "johndoe",
                    "full_name": "John Doe",
                    "avatar_url": "https://example.com/avatar.jpg"
                }
            }
        ]
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You are not a member of this team."
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Team not found."
}
```

---

### 4. Update Team

| Method | URL | Auth Required |
|--------|-----|---------------|
| PUT/PATCH | `/api/v1/teams/{id}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Request Body:**
```json
{
    "name": "Updated Team Name",
    "description": "Updated team description"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| name | string | No | Team name (max: 100) |
| description | string | No | Team description (max: 1000) |

**Example URL:**
```
PUT /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o
```

**Success Response:**
```json
{
    "success": true,
    "message": "Team updated successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "name": "Updated Team Name",
        "slug": "updated-team-name",
        "description": "Updated team description",
        "owner_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "avatar_url": null,
        "is_active": true,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-02T00:00:00.000000Z",
        "members_count": 3,
        "snippets_count": 25,
        "owner": {...}
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Only the team owner can update the team."
}
```

---

### 5. Delete Team

| Method | URL | Auth Required |
|--------|-----|---------------|
| DELETE | `/api/v1/teams/{id}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Request Body:**
```
None
```

**Example URL:**
```
DELETE /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o
```

**Success Response:**
```json
{
    "success": true,
    "message": "Team deleted successfully."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Only the team owner can delete the team."
}
```

---

### 6. Get Team Members

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/teams/{id}/members` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/members
```

**Success Response:**
```json
{
    "success": true,
    "message": "Team members retrieved successfully.",
    "data": [
        {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "username": "johndoe",
            "full_name": "John Doe",
            "avatar_url": "https://example.com/avatar.jpg",
            "bio": "Full-stack developer",
            "pivot": {
                "team_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "user_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "role": "admin",
                "created_at": "2025-01-01T00:00:00.000000Z"
            }
        },
        {
            "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "username": "janesmith",
            "full_name": "Jane Smith",
            "avatar_url": "https://example.com/avatar2.jpg",
            "bio": "Frontend developer",
            "pivot": {
                "team_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "user_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
                "role": "member",
                "created_at": "2025-01-02T00:00:00.000000Z"
            }
        }
    ]
}
```

---

### 7. Update Member Role

| Method | URL | Auth Required |
|--------|-----|---------------|
| PUT | `/api/v1/teams/{id}/members/{memberId}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |
| memberId | uuid | Yes | Member's user ID |

**Request Body:**
```json
{
    "role": "admin"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| role | string | Yes | New role: `admin`, `member`, or `viewer` |

**Example URL:**
```
PUT /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/members/7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r
```

**Success Response:**
```json
{
    "success": true,
    "message": "Member role updated successfully."
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "Only the team owner can update member roles."
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "Cannot change the team owner's role."
}
```

---

### 8. Remove Member

| Method | URL | Auth Required |
|--------|-----|---------------|
| DELETE | `/api/v1/teams/{id}/members/{memberId}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |
| memberId | uuid | Yes | Member's user ID |

**Request Body:**
```
None
```

**Example URL:**
```
DELETE /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/members/7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r
```

**Success Response:**
```json
{
    "success": true,
    "message": "Member removed from team."
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "You cannot remove the team owner."
}
```

---

### 9. Get Team Snippets

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/teams/{id}/snippets` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Query Parameters:**

| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| search | string | No | - | Search in title/description |
| sort_by | string | No | created_at | Sort: `title`, `created_at`, `updated_at`, `view_count` |
| sort_order | string | No | desc | Sort direction: `asc` or `desc` |
| per_page | integer | No | 20 | Items per page (max: 100) |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/snippets?search=react&per_page=10
```

**Success Response:**
```json
{
    "success": true,
    "message": "Team snippets retrieved successfully.",
    "data": [
        {
            "id": "5a3b4c5d-6e7f-8g9h-0i1j-2k3l4m5n6o7p",
            "title": "React Hook Example",
            "slug": "react-hook-example",
            "description": "Custom React hook",
            "code": "const useCustomHook = () => {...}",
            "visibility": "team",
            "view_count": 50,
            "created_at": "2025-01-01T00:00:00.000000Z",
            "user": {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": "https://example.com/avatar.jpg"
            },
            "language": {
                "id": "4a2b3c4d-5e6f-7g8h-9i0j-1k2l3m4n5o6p",
                "name": "javascript",
                "slug": "javascript",
                "display_name": "JavaScript",
                "color": "#f7df1e"
            },
            "tags": [...]
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 3,
        "per_page": 10,
        "total": 25
    }
}
```

---

### 10. Invite Member

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/teams/{id}/invite` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Request Body:**
```json
{
    "email": "newmember@example.com",
    "role": "member"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| email | string | Yes | Email address to invite |
| role | string | Yes | Role to assign: `admin`, `member`, or `viewer` |

**Example URL:**
```
POST /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/invite
```

**Success Response (201):**
```json
{
    "success": true,
    "message": "Invitation sent successfully.",
    "data": {
        "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
        "team_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "email": "newmember@example.com",
        "role": "member",
        "token": "abc123...",
        "invited_by": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "expires_at": "2025-01-08T00:00:00.000000Z",
        "accepted_at": null,
        "created_at": "2025-01-01T00:00:00.000000Z",
        "team": {
            "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "name": "My Development Team",
            "slug": "my-development-team"
        },
        "inviter": {
            "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
            "username": "johndoe",
            "full_name": "John Doe"
        }
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "You do not have permission to invite members."
}
```

**Error Response (422 - Already member):**
```json
{
    "success": false,
    "message": "This user is already a member of the team."
}
```

**Error Response (422 - Pending invitation):**
```json
{
    "success": false,
    "message": "An invitation has already been sent to this email."
}
```

---

### 11. Get Team Invitations

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/teams/{id}/invitations` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/invitations
```

**Success Response:**
```json
{
    "success": true,
    "message": "Pending invitations retrieved successfully.",
    "data": [
        {
            "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
            "team_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "email": "newmember@example.com",
            "role": "member",
            "expires_at": "2025-01-08T00:00:00.000000Z",
            "created_at": "2025-01-01T00:00:00.000000Z",
            "inviter": {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": "https://example.com/avatar.jpg"
            }
        }
    ]
}
```

---

### 12. Cancel Invitation

| Method | URL | Auth Required |
|--------|-----|---------------|
| DELETE | `/api/v1/teams/{id}/invitations/{invitationId}` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |
| invitationId | uuid | Yes | Invitation ID |

**Request Body:**
```
None
```

**Example URL:**
```
DELETE /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/invitations/6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s
```

**Success Response:**
```json
{
    "success": true,
    "message": "Invitation cancelled successfully."
}
```

---

### 13. Leave Team

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/teams/{id}/leave` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Request Body:**
```
None
```

**Example URL:**
```
POST /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/leave
```

**Success Response:**
```json
{
    "success": true,
    "message": "You have left the team."
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "The team owner cannot leave. Transfer ownership or delete the team."
}
```

---

### 14. Transfer Ownership

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/teams/{id}/transfer` | Yes |

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | uuid | Yes | Team ID |

**Request Body:**
```json
{
    "new_owner_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| new_owner_id | uuid | Yes | User ID of new owner (must be existing member) |

**Example URL:**
```
POST /api/v1/teams/9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o/transfer
```

**Success Response:**
```json
{
    "success": true,
    "message": "Team ownership transferred successfully.",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "name": "My Development Team",
        "owner_id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
        "owner": {
            "id": "7c3d4e5f-6g7h-8i9j-0k1l-2m3n4o5p6q7r",
            "username": "janesmith",
            "full_name": "Jane Smith",
            "avatar_url": "https://example.com/avatar2.jpg"
        }
    }
}
```

**Error Response (422):**
```json
{
    "success": false,
    "message": "New owner must be an existing team member."
}
```

---

## Invitation Endpoints (User's Invitations)

### 15. Get My Pending Invitations

| Method | URL | Auth Required |
|--------|-----|---------------|
| GET | `/api/v1/invitations` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```
None
```

**Example URL:**
```
GET /api/v1/invitations
```

**Success Response:**
```json
{
    "success": true,
    "message": "Your pending invitations retrieved successfully.",
    "data": [
        {
            "id": "6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s",
            "team_id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
            "email": "myemail@example.com",
            "role": "member",
            "expires_at": "2025-01-08T00:00:00.000000Z",
            "created_at": "2025-01-01T00:00:00.000000Z",
            "team": {
                "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
                "name": "My Development Team",
                "slug": "my-development-team",
                "description": "A team for development snippets"
            },
            "inviter": {
                "id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
                "username": "johndoe",
                "full_name": "John Doe",
                "avatar_url": "https://example.com/avatar.jpg"
            }
        }
    ]
}
```

---

### 16. Accept Invitation

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/invitations/{invitationId}/accept` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| invitationId | uuid | Yes | Invitation ID |

**Request Body:**
```
None
```

**Example URL:**
```
POST /api/v1/invitations/6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s/accept
```

**Success Response:**
```json
{
    "success": true,
    "message": "You have joined the team!",
    "data": {
        "id": "9e1a2b3c-4d5e-6f7g-8h9i-0j1k2l3m4n5o",
        "name": "My Development Team",
        "slug": "my-development-team",
        "description": "A team for development snippets",
        "owner_id": "8d2b3c4e-5f6g-7h8i-9j0k-1l2m3n4o5p6q",
        "members_count": 4,
        "snippets_count": 25,
        "owner": {...}
    }
}
```

**Error Response (403):**
```json
{
    "success": false,
    "message": "This invitation was not sent to you."
}
```

**Error Response (422 - Expired):**
```json
{
    "success": false,
    "message": "This invitation has expired."
}
```

**Error Response (422 - Already accepted):**
```json
{
    "success": false,
    "message": "This invitation has already been accepted."
}
```

---

### 17. Decline Invitation

| Method | URL | Auth Required |
|--------|-----|---------------|
| POST | `/api/v1/invitations/{invitationId}/decline` | Yes |

**Headers:**
```
Authorization: Bearer {token}
```

**Path Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| invitationId | uuid | Yes | Invitation ID |

**Request Body:**
```
None
```

**Example URL:**
```
POST /api/v1/invitations/6b4e5f6g-7h8i-9j0k-1l2m-3n4o5p6q7r8s/decline
```

**Success Response:**
```json
{
    "success": true,
    "message": "Invitation declined."
}
```

---

## Quick Reference

| # | Endpoint | Method | Auth | Description |
|---|----------|--------|------|-------------|
| 1 | `/api/v1/teams` | GET | Yes | Get my teams |
| 2 | `/api/v1/teams` | POST | Yes | Create team |
| 3 | `/api/v1/teams/{id}` | GET | Yes | Get team details |
| 4 | `/api/v1/teams/{id}` | PUT/PATCH | Yes | Update team (owner only) |
| 5 | `/api/v1/teams/{id}` | DELETE | Yes | Delete team (owner only) |
| 6 | `/api/v1/teams/{id}/members` | GET | Yes | Get team members |
| 7 | `/api/v1/teams/{id}/members/{memberId}` | PUT | Yes | Update member role (owner only) |
| 8 | `/api/v1/teams/{id}/members/{memberId}` | DELETE | Yes | Remove member (owner/admin) |
| 9 | `/api/v1/teams/{id}/snippets` | GET | Yes | Get team snippets |
| 10 | `/api/v1/teams/{id}/invite` | POST | Yes | Invite member (owner/admin) |
| 11 | `/api/v1/teams/{id}/invitations` | GET | Yes | Get pending invitations (owner/admin) |
| 12 | `/api/v1/teams/{id}/invitations/{invitationId}` | DELETE | Yes | Cancel invitation (owner/admin) |
| 13 | `/api/v1/teams/{id}/leave` | POST | Yes | Leave team |
| 14 | `/api/v1/teams/{id}/transfer` | POST | Yes | Transfer ownership (owner only) |
| 15 | `/api/v1/invitations` | GET | Yes | Get my pending invitations |
| 16 | `/api/v1/invitations/{invitationId}/accept` | POST | Yes | Accept invitation |
| 17 | `/api/v1/invitations/{invitationId}/decline` | POST | Yes | Decline invitation |
