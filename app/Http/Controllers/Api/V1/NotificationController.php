<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * Notification types
     */
    const TYPES = [
        'follow' => 'New follower',
        'comment' => 'New comment on your snippet',
        'reply' => 'Reply to your comment',
        'favorite' => 'Someone favorited your snippet',
        'fork' => 'Someone forked your snippet',
        'mention' => 'You were mentioned',
        'team_invite' => 'Team invitation',
        'team_join' => 'New team member',
        'team_leave' => 'Member left team',
        'share' => 'Snippet shared with you',
        'system' => 'System notification',
    ];

    /**
     * Get all notifications for the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Notification::where('user_id', Auth::id())
            ->with(['actor:id,username,full_name,avatar_url']);

        // Filter by read status
        if ($request->has('is_read')) {
            if ($request->boolean('is_read')) {
                $query->read();
            } else {
                $query->unread();
            }
        }

        // Filter by type
        if ($request->has('type')) {
            $type = $request->get('type');
            if (array_key_exists($type, self::TYPES)) {
                $query->ofType($type);
            }
        }

        // Filter by types (multiple)
        if ($request->has('types')) {
            $types = explode(',', $request->get('types'));
            $validTypes = array_intersect($types, array_keys(self::TYPES));
            if (!empty($validTypes)) {
                $query->whereIn('type', $validTypes);
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'read_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $notifications = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Notifications retrieved successfully.',
            'data' => $notifications->items(),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'unread_count' => Notification::where('user_id', Auth::id())->unread()->count(),
            ],
        ]);
    }

    /**
     * Get unread notifications count
     *
     * @return JsonResponse
     */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', Auth::id())
            ->unread()
            ->count();

        // Get count by type
        $countByType = Notification::where('user_id', Auth::id())
            ->unread()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        return response()->json([
            'success' => true,
            'message' => 'Unread count retrieved successfully.',
            'data' => [
                'total' => $count,
                'by_type' => $countByType,
            ],
        ]);
    }

    /**
     * Get a specific notification
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $notification = Notification::with(['actor:id,username,full_name,avatar_url'])
            ->where('user_id', Auth::id())
            ->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification retrieved successfully.',
            'data' => $notification,
        ]);
    }

    /**
     * Mark a notification as read
     *
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = Notification::where('user_id', Auth::id())->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'data' => $notification->fresh(),
        ]);
    }

    /**
     * Mark a notification as unread
     *
     * @param string $id
     * @return JsonResponse
     */
    public function markAsUnread(string $id): JsonResponse
    {
        $notification = Notification::where('user_id', Auth::id())->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->markAsUnread();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as unread.',
            'data' => $notification->fresh(),
        ]);
    }

    /**
     * Mark all notifications as read
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $query = Notification::where('user_id', Auth::id())->unread();

        // Optionally filter by type
        if ($request->has('type')) {
            $type = $request->get('type');
            if (array_key_exists($type, self::TYPES)) {
                $query->ofType($type);
            }
        }

        $updated = $query->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Marked {$updated} notifications as read.",
            'data' => [
                'updated_count' => $updated,
            ],
        ]);
    }

    /**
     * Mark multiple notifications as read
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function markMultipleAsRead(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updated = Notification::where('user_id', Auth::id())
            ->whereIn('id', $request->ids)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => "Marked {$updated} notifications as read.",
            'data' => [
                'updated_count' => $updated,
            ],
        ]);
    }

    /**
     * Delete a notification
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $notification = Notification::where('user_id', Auth::id())->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully.',
        ]);
    }

    /**
     * Delete multiple notifications
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroyMultiple(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'uuid',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deleted = Notification::where('user_id', Auth::id())
            ->whereIn('id', $request->ids)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} notifications.",
            'data' => [
                'deleted_count' => $deleted,
            ],
        ]);
    }

    /**
     * Delete all read notifications
     *
     * @return JsonResponse
     */
    public function destroyAllRead(): JsonResponse
    {
        $deleted = Notification::where('user_id', Auth::id())
            ->read()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} read notifications.",
            'data' => [
                'deleted_count' => $deleted,
            ],
        ]);
    }

    /**
     * Delete all notifications
     *
     * @return JsonResponse
     */
    public function destroyAll(): JsonResponse
    {
        $deleted = Notification::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted {$deleted} notifications.",
            'data' => [
                'deleted_count' => $deleted,
            ],
        ]);
    }

    /**
     * Get notification types
     *
     * @return JsonResponse
     */
    public function types(): JsonResponse
    {
        $types = collect(self::TYPES)->map(function ($description, $type) {
            return [
                'type' => $type,
                'description' => $description,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Notification types retrieved successfully.',
            'data' => $types,
        ]);
    }

    /**
     * Get notification settings for user
     *
     * @return JsonResponse
     */
    public function settings(): JsonResponse
    {
        $user = Auth::user();
        $settings = $user->settings['notifications'] ?? $this->getDefaultSettings();

        return response()->json([
            'success' => true,
            'message' => 'Notification settings retrieved successfully.',
            'data' => $settings,
        ]);
    }

    /**
     * Update notification settings for user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'sometimes|boolean',
            'push_notifications' => 'sometimes|boolean',
            'follow_notifications' => 'sometimes|boolean',
            'comment_notifications' => 'sometimes|boolean',
            'favorite_notifications' => 'sometimes|boolean',
            'fork_notifications' => 'sometimes|boolean',
            'mention_notifications' => 'sometimes|boolean',
            'team_notifications' => 'sometimes|boolean',
            'share_notifications' => 'sometimes|boolean',
            'system_notifications' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();
        $currentSettings = $user->settings ?? [];
        $notificationSettings = $currentSettings['notifications'] ?? $this->getDefaultSettings();

        // Update only provided settings
        foreach ($request->only(array_keys($this->getDefaultSettings())) as $key => $value) {
            $notificationSettings[$key] = $value;
        }

        $currentSettings['notifications'] = $notificationSettings;
        $user->settings = $currentSettings;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully.',
            'data' => $notificationSettings,
        ]);
    }

    /**
     * Get default notification settings
     *
     * @return array
     */
    private function getDefaultSettings(): array
    {
        return [
            'email_notifications' => true,
            'push_notifications' => true,
            'follow_notifications' => true,
            'comment_notifications' => true,
            'favorite_notifications' => true,
            'fork_notifications' => true,
            'mention_notifications' => true,
            'team_notifications' => true,
            'share_notifications' => true,
            'system_notifications' => true,
        ];
    }
}
