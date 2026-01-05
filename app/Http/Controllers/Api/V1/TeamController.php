<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Get user's teams (owned and member of)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = User::find(Auth::id());

        // Get owned teams
        $ownedTeams = Team::where('owner_id', $user->id)
            ->active()
            ->withCount(['members', 'snippets'])
            ->with('owner:id,username,full_name,avatar_url')
            ->get();

        // Get teams user is member of (not owner)
        $memberTeams = $user->teams()
            ->where('owner_id', '!=', $user->id)
            ->active()
            ->withCount(['members', 'snippets'])
            ->with('owner:id,username,full_name,avatar_url')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Teams retrieved successfully.',
            'data' => [
                'owned' => $ownedTeams,
                'member_of' => $memberTeams,
            ],
        ]);
    }

    /**
     * Create a new team
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => Auth::id(),
            'is_active' => true,
        ]);

        // Add owner as admin member
        $team->members()->attach(Auth::id(), ['role' => 'admin']);

        $team->load('owner:id,username,full_name,avatar_url');
        $team->loadCount(['members', 'snippets']);

        return response()->json([
            'success' => true,
            'message' => 'Team created successfully.',
            'data' => $team,
        ], 201);
    }

    /**
     * Get a specific team
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $team = Team::with(['owner:id,username,full_name,avatar_url'])
            ->withCount(['members', 'snippets'])
            ->find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();

        // Check if user is member or owner
        if (!$team->hasMember($user) && !$team->isOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this team.',
            ], 403);
        }

        // Include members if requested
        if ($request->has('with_members') && $request->boolean('with_members')) {
            $team->load(['members' => function ($q) {
                $q->select(['users.id', 'username', 'full_name', 'avatar_url'])
                    ->withPivot('role', 'created_at');
            }]);
        }

        // Include recent snippets if requested
        if ($request->has('with_snippets') && $request->boolean('with_snippets')) {
            $team->load(['snippets' => function ($q) {
                $q->with(['user:id,username,full_name,avatar_url', 'language:id,name,slug,display_name,color'])
                    ->orderByDesc('created_at')
                    ->limit(10);
            }]);
        }

        // Include pending invitations if requested (only for owner/admin)
        $userRole = $team->getMemberRole($user);
        if ($request->has('with_invitations') && $request->boolean('with_invitations')) {
            if ($team->isOwner($user) || $userRole === 'admin') {
                $team->load(['invitations' => function ($q) {
                    $q->pending()->with('inviter:id,username,full_name,avatar_url');
                }]);
            }
        }

        // Add user role info
        $teamData = $team->toArray();
        $teamData['user_role'] = $userRole;
        $teamData['is_owner'] = $team->isOwner($user);

        return response()->json([
            'success' => true,
            'message' => 'Team retrieved successfully.',
            'data' => $teamData,
        ]);
    }

    /**
     * Update a team
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();

        // Only owner can update team
        if (!$team->isOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only the team owner can update the team.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $team->update($request->only(['name', 'description']));

        $team->load('owner:id,username,full_name,avatar_url');
        $team->loadCount(['members', 'snippets']);

        return response()->json([
            'success' => true,
            'message' => 'Team updated successfully.',
            'data' => $team,
        ]);
    }

    /**
     * Delete a team
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        if (!$team->isOwner(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'Only the team owner can delete the team.',
            ], 403);
        }

        // Delete all pending invitations
        $team->invitations()->delete();

        // Detach all members
        $team->members()->detach();

        $team->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team deleted successfully.',
        ]);
    }

    /**
     * Get team members
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function members(Request $request, string $id): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();

        if (!$team->hasMember($user) && !$team->isOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this team.',
            ], 403);
        }

        $members = $team->members()
            ->select(['users.id', 'username', 'full_name', 'avatar_url', 'bio'])
            ->withPivot('role', 'created_at')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Team members retrieved successfully.',
            'data' => $members,
        ]);
    }

    /**
     * Invite a user to the team
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function invite(Request $request, string $id): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();
        $userRole = $team->getMemberRole($user);

        // Only owner or admin can invite
        if (!$team->isOwner($user) && $userRole !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to invite members.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'role' => 'required|in:member,admin,viewer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if user is already a member
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $team->hasMember($existingUser)) {
            return response()->json([
                'success' => false,
                'message' => 'This user is already a member of the team.',
            ], 422);
        }

        // Check if invitation already exists
        $existingInvitation = TeamInvitation::where('team_id', $team->id)
            ->where('email', $request->email)
            ->pending()
            ->first();

        if ($existingInvitation) {
            return response()->json([
                'success' => false,
                'message' => 'An invitation has already been sent to this email.',
            ], 422);
        }

        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::random(64),
            'invited_by' => $user->id,
            'expires_at' => now()->addDays(7),
        ]);

        $invitation->load(['team:id,name,slug', 'inviter:id,username,full_name']);

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully.',
            'data' => $invitation,
        ], 201);
    }

    /**
     * Cancel a pending invitation
     *
     * @param string $id
     * @param string $invitationId
     * @return JsonResponse
     */
    public function cancelInvitation(string $id, string $invitationId): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();
        $userRole = $team->getMemberRole($user);

        // Only owner or admin can cancel invitations
        if (!$team->isOwner($user) && $userRole !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to cancel invitations.',
            ], 403);
        }

        $invitation = TeamInvitation::where('id', $invitationId)
            ->where('team_id', $team->id)
            ->pending()
            ->first();

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Invitation not found.',
            ], 404);
        }

        $invitation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invitation cancelled successfully.',
        ]);
    }

    /**
     * Remove a member from the team
     *
     * @param string $id
     * @param string $memberId
     * @return JsonResponse
     */
    public function removeMember(string $id, string $memberId): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();
        $userRole = $team->getMemberRole($user);

        // Only owner or admin can remove members
        if (!$team->isOwner($user) && $userRole !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to remove members.',
            ], 403);
        }

        $member = User::find($memberId);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Can't remove the owner
        if ($team->isOwner($member)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot remove the team owner.',
            ], 422);
        }

        // Admin can't remove another admin (only owner can)
        $memberRole = $team->getMemberRole($member);
        if ($userRole === 'admin' && $memberRole === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only the team owner can remove admins.',
            ], 403);
        }

        if (!$team->hasMember($member)) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a member of this team.',
            ], 422);
        }

        $team->members()->detach($member->id);

        return response()->json([
            'success' => true,
            'message' => 'Member removed from team.',
        ]);
    }

    /**
     * Update a member's role
     *
     * @param Request $request
     * @param string $id
     * @param string $memberId
     * @return JsonResponse
     */
    public function updateMemberRole(Request $request, string $id, string $memberId): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();

        // Only owner can update roles
        if (!$team->isOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only the team owner can update member roles.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'role' => 'required|in:member,admin,viewer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $member = User::find($memberId);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if (!$team->hasMember($member)) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a member of this team.',
            ], 422);
        }

        // Can't change owner's role
        if ($team->isOwner($member)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot change the team owner\'s role.',
            ], 422);
        }

        $team->members()->updateExistingPivot($member->id, ['role' => $request->role]);

        return response()->json([
            'success' => true,
            'message' => 'Member role updated successfully.',
        ]);
    }

    /**
     * Leave a team
     *
     * @param string $id
     * @return JsonResponse
     */
    public function leave(string $id): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();

        if ($team->isOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'The team owner cannot leave. Transfer ownership or delete the team.',
            ], 422);
        }

        if (!$team->hasMember($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this team.',
            ], 422);
        }

        $team->members()->detach($user->id);

        return response()->json([
            'success' => true,
            'message' => 'You have left the team.',
        ]);
    }

    /**
     * Transfer team ownership
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function transferOwnership(Request $request, string $id): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();

        if (!$team->isOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Only the team owner can transfer ownership.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'new_owner_id' => 'required|uuid|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $newOwner = User::find($request->new_owner_id);

        if (!$team->hasMember($newOwner)) {
            return response()->json([
                'success' => false,
                'message' => 'New owner must be an existing team member.',
            ], 422);
        }

        if ($team->isOwner($newOwner)) {
            return response()->json([
                'success' => false,
                'message' => 'User is already the team owner.',
            ], 422);
        }

        // Transfer ownership
        $team->update(['owner_id' => $newOwner->id]);

        // Make new owner admin
        $team->members()->updateExistingPivot($newOwner->id, ['role' => 'admin']);

        // Demote old owner to admin
        $team->members()->updateExistingPivot($user->id, ['role' => 'admin']);

        $team->load('owner:id,username,full_name,avatar_url');

        return response()->json([
            'success' => true,
            'message' => 'Team ownership transferred successfully.',
            'data' => $team,
        ]);
    }

    /**
     * Get team snippets
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function snippets(Request $request, string $id): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();

        if (!$team->hasMember($user) && !$team->isOwner($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not a member of this team.',
            ], 403);
        }

        $query = $team->snippets()
            ->with([
                'user:id,username,full_name,avatar_url',
                'language:id,name,slug,display_name,color',
                'tags:id,name,slug,color',
            ]);

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['title', 'created_at', 'updated_at', 'view_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $snippets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Team snippets retrieved successfully.',
            'data' => $snippets->items(),
            'meta' => [
                'current_page' => $snippets->currentPage(),
                'last_page' => $snippets->lastPage(),
                'per_page' => $snippets->perPage(),
                'total' => $snippets->total(),
            ],
        ]);
    }

    /**
     * Get pending invitations for the team (owner/admin only)
     *
     * @param string $id
     * @return JsonResponse
     */
    public function invitations(string $id): JsonResponse
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team not found.',
            ], 404);
        }

        $user = Auth::user();
        $userRole = $team->getMemberRole($user);

        if (!$team->isOwner($user) && $userRole !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view invitations.',
            ], 403);
        }

        $invitations = $team->invitations()
            ->pending()
            ->with('inviter:id,username,full_name,avatar_url')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Pending invitations retrieved successfully.',
            'data' => $invitations,
        ]);
    }

    /**
     * Get user's pending team invitations
     *
     * @return JsonResponse
     */
    public function myInvitations(): JsonResponse
    {
        $user = Auth::user();

        $invitations = TeamInvitation::where('email', $user->email)
            ->pending()
            ->with(['team:id,name,slug,description', 'inviter:id,username,full_name,avatar_url'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Your pending invitations retrieved successfully.',
            'data' => $invitations,
        ]);
    }

    /**
     * Accept an invitation
     *
     * @param string $invitationId
     * @return JsonResponse
     */
    public function acceptInvitation(string $invitationId): JsonResponse
    {
        $user = Auth::user();

        $invitation = TeamInvitation::with('team')
            ->find($invitationId);

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Invitation not found.',
            ], 404);
        }

        if ($invitation->email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'This invitation was not sent to you.',
            ], 403);
        }

        if ($invitation->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'This invitation has expired.',
            ], 422);
        }

        if ($invitation->isAccepted()) {
            return response()->json([
                'success' => false,
                'message' => 'This invitation has already been accepted.',
            ], 422);
        }

        // Add user to team
        $invitation->team->members()->attach($user->id, ['role' => $invitation->role]);

        // Mark invitation as accepted
        $invitation->update(['accepted_at' => now()]);

        $invitation->team->load('owner:id,username,full_name,avatar_url');
        $invitation->team->loadCount(['members', 'snippets']);

        return response()->json([
            'success' => true,
            'message' => 'You have joined the team!',
            'data' => $invitation->team,
        ]);
    }

    /**
     * Decline an invitation
     *
     * @param string $invitationId
     * @return JsonResponse
     */
    public function declineInvitation(string $invitationId): JsonResponse
    {
        $user = Auth::user();

        $invitation = TeamInvitation::find($invitationId);

        if (!$invitation) {
            return response()->json([
                'success' => false,
                'message' => 'Invitation not found.',
            ], 404);
        }

        if ($invitation->email !== $user->email) {
            return response()->json([
                'success' => false,
                'message' => 'This invitation was not sent to you.',
            ], 403);
        }

        $invitation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invitation declined.',
        ]);
    }
}
