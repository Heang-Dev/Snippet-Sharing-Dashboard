<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Snippet;
use App\Models\SnippetVersion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SnippetVersionController extends Controller
{
    /**
     * Get all versions for a snippet
     *
     * @param Request $request
     * @param string $snippetId
     * @return JsonResponse
     */
    public function index(Request $request, string $snippetId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Check access
        $user = Auth::user();
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet\'s versions.',
            ], 403);
        }

        $query = SnippetVersion::where('snippet_id', $snippetId)
            ->with(['createdBy:id,username,full_name,avatar_url']);

        // Filter by change type
        if ($request->has('change_type')) {
            $changeType = $request->get('change_type');
            if (in_array($changeType, ['create', 'update', 'restore'])) {
                $query->where('change_type', $changeType);
            }
        }

        // Sort (default: latest first)
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy('version_number', $sortOrder === 'asc' ? 'asc' : 'desc');

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $versions = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Versions retrieved successfully.',
            'data' => $versions->items(),
            'meta' => [
                'current_page' => $versions->currentPage(),
                'last_page' => $versions->lastPage(),
                'per_page' => $versions->perPage(),
                'total' => $versions->total(),
                'latest_version' => SnippetVersion::where('snippet_id', $snippetId)->max('version_number'),
            ],
        ]);
    }

    /**
     * Get a specific version
     *
     * @param string $snippetId
     * @param string $versionId
     * @return JsonResponse
     */
    public function show(string $snippetId, string $versionId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Check access
        $user = Auth::user();
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet\'s versions.',
            ], 403);
        }

        $version = SnippetVersion::with(['createdBy:id,username,full_name,avatar_url'])
            ->where('snippet_id', $snippetId)
            ->find($versionId);

        if (!$version) {
            return response()->json([
                'success' => false,
                'message' => 'Version not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Version retrieved successfully.',
            'data' => $version,
        ]);
    }

    /**
     * Get a version by version number
     *
     * @param string $snippetId
     * @param int $versionNumber
     * @return JsonResponse
     */
    public function showByNumber(string $snippetId, int $versionNumber): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Check access
        $user = Auth::user();
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet\'s versions.',
            ], 403);
        }

        $version = SnippetVersion::with(['createdBy:id,username,full_name,avatar_url'])
            ->where('snippet_id', $snippetId)
            ->where('version_number', $versionNumber)
            ->first();

        if (!$version) {
            return response()->json([
                'success' => false,
                'message' => 'Version not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Version retrieved successfully.',
            'data' => $version,
        ]);
    }

    /**
     * Get the latest version
     *
     * @param string $snippetId
     * @return JsonResponse
     */
    public function latest(string $snippetId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Check access
        $user = Auth::user();
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet\'s versions.',
            ], 403);
        }

        $version = SnippetVersion::with(['createdBy:id,username,full_name,avatar_url'])
            ->where('snippet_id', $snippetId)
            ->orderBy('version_number', 'desc')
            ->first();

        if (!$version) {
            return response()->json([
                'success' => false,
                'message' => 'No versions found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Latest version retrieved successfully.',
            'data' => $version,
        ]);
    }

    /**
     * Compare two versions
     *
     * @param Request $request
     * @param string $snippetId
     * @return JsonResponse
     */
    public function compare(Request $request, string $snippetId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Check access
        $user = Auth::user();
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet\'s versions.',
            ], 403);
        }

        $fromVersion = $request->get('from');
        $toVersion = $request->get('to');

        if (!$fromVersion || !$toVersion) {
            return response()->json([
                'success' => false,
                'message' => 'Both from and to version numbers are required.',
            ], 422);
        }

        $from = SnippetVersion::where('snippet_id', $snippetId)
            ->where('version_number', $fromVersion)
            ->first();

        $to = SnippetVersion::where('snippet_id', $snippetId)
            ->where('version_number', $toVersion)
            ->first();

        if (!$from || !$to) {
            return response()->json([
                'success' => false,
                'message' => 'One or both versions not found.',
            ], 404);
        }

        // Calculate diff
        $diff = $this->calculateDiff($from->code, $to->code);

        return response()->json([
            'success' => true,
            'message' => 'Version comparison retrieved successfully.',
            'data' => [
                'from' => [
                    'version_number' => $from->version_number,
                    'title' => $from->title,
                    'code' => $from->code,
                    'language' => $from->language,
                    'created_at' => $from->created_at,
                ],
                'to' => [
                    'version_number' => $to->version_number,
                    'title' => $to->title,
                    'code' => $to->code,
                    'language' => $to->language,
                    'created_at' => $to->created_at,
                ],
                'diff' => $diff,
            ],
        ]);
    }

    /**
     * Restore a previous version
     *
     * @param string $snippetId
     * @param string $versionId
     * @return JsonResponse
     */
    public function restore(string $snippetId, string $versionId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Only owner can restore
        if (!$snippet->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to restore this snippet\'s version.',
            ], 403);
        }

        $version = SnippetVersion::where('snippet_id', $snippetId)->find($versionId);

        if (!$version) {
            return response()->json([
                'success' => false,
                'message' => 'Version not found.',
            ], 404);
        }

        // Get current version number
        $latestVersionNumber = SnippetVersion::where('snippet_id', $snippetId)
            ->max('version_number');

        // Calculate diff for the restore
        $oldCode = $snippet->code;
        $diff = $this->calculateDiff($oldCode, $version->code);

        // Update the snippet with the old version's content
        $snippet->update([
            'title' => $version->title,
            'description' => $version->description,
            'code' => $version->code,
        ]);

        // Create a new version record for the restore
        $newVersion = SnippetVersion::create([
            'snippet_id' => $snippetId,
            'version_number' => $latestVersionNumber + 1,
            'title' => $version->title,
            'description' => $version->description,
            'code' => $version->code,
            'language' => $version->language,
            'change_summary' => "Restored from version {$version->version_number}",
            'change_type' => 'restore',
            'lines_added' => $diff['lines_added'],
            'lines_removed' => $diff['lines_removed'],
            'created_by' => Auth::id(),
        ]);

        $newVersion->load(['createdBy:id,username,full_name,avatar_url']);

        return response()->json([
            'success' => true,
            'message' => "Successfully restored to version {$version->version_number}.",
            'data' => [
                'snippet' => $snippet->fresh(),
                'version' => $newVersion,
                'restored_from' => $version->version_number,
            ],
        ]);
    }

    /**
     * Get version history summary (stats)
     *
     * @param string $snippetId
     * @return JsonResponse
     */
    public function stats(string $snippetId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Check access
        $user = Auth::user();
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet\'s version stats.',
            ], 403);
        }

        $totalVersions = SnippetVersion::where('snippet_id', $snippetId)->count();
        $latestVersion = SnippetVersion::where('snippet_id', $snippetId)
            ->orderBy('version_number', 'desc')
            ->first();
        $firstVersion = SnippetVersion::where('snippet_id', $snippetId)
            ->orderBy('version_number', 'asc')
            ->first();

        $totalLinesAdded = SnippetVersion::where('snippet_id', $snippetId)->sum('lines_added');
        $totalLinesRemoved = SnippetVersion::where('snippet_id', $snippetId)->sum('lines_removed');

        // Count by change type
        $changeTypeCounts = SnippetVersion::where('snippet_id', $snippetId)
            ->selectRaw('change_type, COUNT(*) as count')
            ->groupBy('change_type')
            ->pluck('count', 'change_type');

        // Get unique contributors
        $contributors = SnippetVersion::where('snippet_id', $snippetId)
            ->with('createdBy:id,username,full_name,avatar_url')
            ->select('created_by')
            ->distinct()
            ->get()
            ->pluck('createdBy');

        return response()->json([
            'success' => true,
            'message' => 'Version statistics retrieved successfully.',
            'data' => [
                'total_versions' => $totalVersions,
                'latest_version_number' => $latestVersion?->version_number,
                'first_created_at' => $firstVersion?->created_at,
                'last_updated_at' => $latestVersion?->created_at,
                'total_lines_added' => (int) $totalLinesAdded,
                'total_lines_removed' => (int) $totalLinesRemoved,
                'change_type_counts' => $changeTypeCounts,
                'contributors' => $contributors,
                'contributors_count' => $contributors->count(),
            ],
        ]);
    }

    /**
     * Calculate diff between two code strings
     *
     * @param string $oldCode
     * @param string $newCode
     * @return array
     */
    private function calculateDiff(string $oldCode, string $newCode): array
    {
        $oldLines = explode("\n", $oldCode);
        $newLines = explode("\n", $newCode);

        $oldLineCount = count($oldLines);
        $newLineCount = count($newLines);

        // Simple diff calculation
        $linesAdded = 0;
        $linesRemoved = 0;
        $changes = [];

        // Use simple line-by-line comparison
        $maxLines = max($oldLineCount, $newLineCount);

        for ($i = 0; $i < $maxLines; $i++) {
            $oldLine = $oldLines[$i] ?? null;
            $newLine = $newLines[$i] ?? null;

            if ($oldLine === null && $newLine !== null) {
                $linesAdded++;
                $changes[] = [
                    'type' => 'add',
                    'line_number' => $i + 1,
                    'content' => $newLine,
                ];
            } elseif ($oldLine !== null && $newLine === null) {
                $linesRemoved++;
                $changes[] = [
                    'type' => 'remove',
                    'line_number' => $i + 1,
                    'content' => $oldLine,
                ];
            } elseif ($oldLine !== $newLine) {
                $linesRemoved++;
                $linesAdded++;
                $changes[] = [
                    'type' => 'modify',
                    'line_number' => $i + 1,
                    'old_content' => $oldLine,
                    'new_content' => $newLine,
                ];
            }
        }

        return [
            'lines_added' => $linesAdded,
            'lines_removed' => $linesRemoved,
            'total_changes' => count($changes),
            'changes' => array_slice($changes, 0, 100), // Limit to first 100 changes
        ];
    }
}
