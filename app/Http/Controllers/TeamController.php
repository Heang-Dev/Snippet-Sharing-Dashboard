<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $ownedTeams = Team::where('owner_id', $user->id)
            ->withCount('members', 'snippets')
            ->get();

        $memberTeams = $user->teams()
            ->where('owner_id', '!=', $user->id)
            ->withCount('members', 'snippets')
            ->get();

        $pendingInvitations = TeamInvitation::where('email', $user->email)
            ->pending()
            ->with('team', 'inviter')
            ->get();

        return Inertia::render('Teams/Index', [
            'ownedTeams' => $ownedTeams,
            'memberTeams' => $memberTeams,
            'pendingInvitations' => $pendingInvitations,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Teams/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $team = Team::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'owner_id' => Auth::id(),
            'is_active' => true,
        ]);

        // Add owner as admin member
        $team->members()->attach(Auth::id(), ['role' => 'admin']);

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Team created successfully!');
    }

    public function show(Team $team): Response
    {
        $user = Auth::user();

        // Check if user is member or owner
        if (!$team->hasMember($user) && !$team->isOwner($user)) {
            abort(403, 'You are not a member of this team.');
        }

        $team->load([
            'owner',
            'members' => function ($query) {
                $query->withPivot('role');
            },
            'snippets' => function ($query) {
                $query->with('language', 'user')->orderByDesc('created_at')->limit(10);
            },
            'invitations' => function ($query) {
                $query->pending()->with('inviter');
            },
        ]);

        $userRole = $team->getMemberRole($user);

        return Inertia::render('Teams/Show', [
            'team' => $team,
            'userRole' => $userRole,
            'isOwner' => $team->isOwner($user),
        ]);
    }

    public function edit(Team $team): Response
    {
        if (!$team->isOwner(Auth::user())) {
            abort(403, 'Only the team owner can edit the team.');
        }

        return Inertia::render('Teams/Edit', [
            'team' => $team,
        ]);
    }

    public function update(Request $request, Team $team): RedirectResponse
    {
        if (!$team->isOwner(Auth::user())) {
            abort(403, 'Only the team owner can update the team.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'Team updated successfully!');
    }

    public function destroy(Team $team): RedirectResponse
    {
        if (!$team->isOwner(Auth::user())) {
            abort(403, 'Only the team owner can delete the team.');
        }

        $team->delete();

        return redirect()
            ->route('teams.index')
            ->with('success', 'Team deleted successfully!');
    }

    public function invite(Request $request, Team $team): RedirectResponse
    {
        $user = Auth::user();

        // Only owner or admin can invite
        $role = $team->getMemberRole($user);
        if (!$team->isOwner($user) && $role !== 'admin') {
            abort(403, 'You do not have permission to invite members.');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:member,admin'],
        ]);

        // Check if user is already a member
        $existingUser = User::where('email', $validated['email'])->first();
        if ($existingUser && $team->hasMember($existingUser)) {
            return back()->with('error', 'This user is already a member of the team.');
        }

        // Check if invitation already exists
        $existingInvitation = TeamInvitation::where('team_id', $team->id)
            ->where('email', $validated['email'])
            ->pending()
            ->first();

        if ($existingInvitation) {
            return back()->with('error', 'An invitation has already been sent to this email.');
        }

        TeamInvitation::create([
            'team_id' => $team->id,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'token' => Str::random(64),
            'invited_by' => $user->id,
            'expires_at' => now()->addDays(7),
        ]);

        return back()->with('success', 'Invitation sent successfully!');
    }

    public function acceptInvitation(TeamInvitation $invitation): RedirectResponse
    {
        $user = Auth::user();

        if ($invitation->email !== $user->email) {
            abort(403, 'This invitation was not sent to you.');
        }

        if ($invitation->isExpired()) {
            return redirect()
                ->route('teams.index')
                ->with('error', 'This invitation has expired.');
        }

        if ($invitation->isAccepted()) {
            return redirect()
                ->route('teams.index')
                ->with('error', 'This invitation has already been accepted.');
        }

        // Add user to team
        $invitation->team->members()->attach($user->id, ['role' => $invitation->role]);

        // Mark invitation as accepted
        $invitation->update(['accepted_at' => now()]);

        return redirect()
            ->route('teams.show', $invitation->team)
            ->with('success', 'You have joined the team!');
    }

    public function declineInvitation(TeamInvitation $invitation): RedirectResponse
    {
        $user = Auth::user();

        if ($invitation->email !== $user->email) {
            abort(403, 'This invitation was not sent to you.');
        }

        $invitation->delete();

        return redirect()
            ->route('teams.index')
            ->with('success', 'Invitation declined.');
    }

    public function removeMember(Team $team, User $member): RedirectResponse
    {
        $user = Auth::user();

        // Only owner can remove members
        if (!$team->isOwner($user)) {
            abort(403, 'Only the team owner can remove members.');
        }

        // Can't remove the owner
        if ($team->isOwner($member)) {
            return back()->with('error', 'You cannot remove the team owner.');
        }

        $team->members()->detach($member->id);

        return back()->with('success', 'Member removed from team.');
    }

    public function updateMemberRole(Request $request, Team $team, User $member): RedirectResponse
    {
        $user = Auth::user();

        // Only owner can update roles
        if (!$team->isOwner($user)) {
            abort(403, 'Only the team owner can update member roles.');
        }

        $validated = $request->validate([
            'role' => ['required', 'in:member,admin'],
        ]);

        $team->members()->updateExistingPivot($member->id, ['role' => $validated['role']]);

        return back()->with('success', 'Member role updated.');
    }

    public function leave(Team $team): RedirectResponse
    {
        $user = Auth::user();

        if ($team->isOwner($user)) {
            return back()->with('error', 'The team owner cannot leave. Transfer ownership or delete the team.');
        }

        $team->members()->detach($user->id);

        return redirect()
            ->route('teams.index')
            ->with('success', 'You have left the team.');
    }
}
