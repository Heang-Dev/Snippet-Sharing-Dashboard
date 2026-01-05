<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property string $id
 * @property string|null $user_id
 * @property string $action
 * @property string $resource_type
 * @property string|null $resource_id
 * @property array<array-key, mixed>|null $old_values
 * @property array<array-key, mixed>|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $method
 * @property string|null $endpoint
 * @property int|null $status_code
 * @property string|null $error_message
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog forResource(string $type, ?string $id = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog forUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog ofAction(string $action)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereEndpoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereNewValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereOldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AuditLog whereUserId($value)
 */
	class AuditLog extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $color
 * @property string|null $parent_category_id
 * @property int $snippet_count
 * @property int $order
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Snippet> $snippets
 * @property-read int|null $snippets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category roots()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSnippetCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $cover_image_url
 * @property string $privacy
 * @property int $snippet_count
 * @property int $view_count
 * @property int $is_featured
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\CollectionSnippet|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Snippet> $snippets
 * @property-read int|null $snippets_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection visible(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereCoverImageUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection wherePrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereSnippetCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection whereViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Collection withoutTrashed()
 */
	class Collection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $collection_id
 * @property string $snippet_id
 * @property int $position
 * @property string|null $note
 * @property \Illuminate\Support\Carbon $added_at
 * @property-read \App\Models\Collection $collection
 * @property-read \App\Models\Snippet $snippet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet whereAddedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet whereCollectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CollectionSnippet whereSnippetId($value)
 */
	class CollectionSnippet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $snippet_id
 * @property string $user_id
 * @property string|null $parent_comment_id
 * @property string $content
 * @property int|null $line_number
 * @property bool $is_edited
 * @property string|null $edited_at
 * @property int $upvote_count
 * @property int $reply_count
 * @property int $is_pinned
 * @property int $is_resolved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Comment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\Snippet $snippet
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment roots()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereIsEdited($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereIsPinned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereIsResolved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereLineNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereParentCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereReplyCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereSnippetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpvoteCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment withoutTrashed()
 */
	class Comment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string $snippet_id
 * @property string|null $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Snippet $snippet
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereSnippetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Favorite whereUserId($value)
 */
	class Favorite extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $follower_id
 * @property string $following_id
 * @property bool $notification_enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $follower
 * @property-read \App\Models\User $following
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereNotificationEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereUpdatedAt($value)
 */
	class Follow extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string $display_name
 * @property array<array-key, mixed> $file_extensions
 * @property string $pygments_lexer
 * @property string|null $monaco_language
 * @property string|null $icon
 * @property string|null $color
 * @property int $snippet_count
 * @property int|null $popularity_rank
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Snippet> $snippets
 * @property-read int|null $snippets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereFileExtensions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereMonacoLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language wherePopularityRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language wherePygmentsLexer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereSnippetCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Language whereUpdatedAt($value)
 */
	class Language extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string $type
 * @property string $title
 * @property string|null $message
 * @property string|null $link
 * @property string|null $icon
 * @property string|null $actor_id
 * @property string|null $related_resource_type
 * @property string|null $related_resource_id
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $actor
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification read()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification recent(int $days = 30)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification unread()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereRelatedResourceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereRelatedResourceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $snippet_id
 * @property string $shared_by
 * @property string|null $shared_with
 * @property string|null $team_id
 * @property string $share_type
 * @property string|null $share_token
 * @property string $permission
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property int $access_count
 * @property \Illuminate\Support\Carbon|null $last_accessed_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $sharedBy
 * @property-read \App\Models\User|null $sharedWith
 * @property-read \App\Models\Snippet $snippet
 * @property-read \App\Models\Team|null $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share ofType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereAccessCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereLastAccessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereShareToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereShareType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereSharedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereSharedWith($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereSnippetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Share whereUpdatedAt($value)
 */
	class Share extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $user_id
 * @property string|null $team_id
 * @property string $title
 * @property string|null $description
 * @property string $code
 * @property string|null $highlighted_html
 * @property \App\Models\Language|null $language
 * @property string|null $category_id
 * @property string $privacy
 * @property string $slug
 * @property int $version_number
 * @property string|null $parent_snippet_id
 * @property int $is_fork
 * @property int $is_featured
 * @property int $allow_comments
 * @property int $allow_forks
 * @property string|null $license
 * @property int $view_count
 * @property int $unique_view_count
 * @property int $fork_count
 * @property int $favorite_count
 * @property int $comment_count
 * @property int $share_count
 * @property float $trending_score
 * @property string|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $allComments
 * @property-read int|null $all_comments_count
 * @property-read \App\Models\Category|null $category
 * @property-read \App\Models\Favorite|\App\Models\CollectionSnippet|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Collection> $collections
 * @property-read int|null $collections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $favoritedBy
 * @property-read int|null $favorited_by_count
 * @property-read Snippet|null $forkedFrom
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Snippet> $forks
 * @property-read int|null $forks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Share> $shares
 * @property-read int|null $shares_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\Team|null $team
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SnippetVersion> $versions
 * @property-read int|null $versions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SnippetView> $views
 * @property-read int|null $views_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet notExpired()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet private()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet public()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet teamVisible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet visible(?\App\Models\User $user = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereAllowComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereAllowForks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereCommentCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereFavoriteCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereForkCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereHighlightedHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereIsFeatured($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereIsFork($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereLicense($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereParentSnippetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet wherePrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereShareCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereTrendingScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereUniqueViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereVersionNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet whereViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Snippet withoutTrashed()
 */
	class Snippet extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $snippet_id
 * @property int $version_number
 * @property string $title
 * @property string|null $description
 * @property string $code
 * @property string $language
 * @property string|null $change_summary
 * @property string $change_type
 * @property int $lines_added
 * @property int $lines_removed
 * @property string $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\Snippet $snippet
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion latest()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereChangeSummary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereChangeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereLinesAdded($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereLinesRemoved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereSnippetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetVersion whereVersionNumber($value)
 */
	class SnippetVersion extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $snippet_id
 * @property string|null $user_id
 * @property string|null $session_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referrer
 * @property string|null $country
 * @property string|null $city
 * @property string $viewed_at
 * @property-read \App\Models\Snippet $snippet
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereReferrer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereSnippetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SnippetView whereViewedAt($value)
 */
	class SnippetView extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $color
 * @property int $usage_count
 * @property int $is_official
 * @property string|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Snippet> $snippets
 * @property-read int|null $snippets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag popular(int $limit = 20)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereIsOfficial($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUsageCount($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $avatar_url
 * @property string $owner_id
 * @property string $privacy
 * @property int $member_count
 * @property int $snippet_count
 * @property int $allow_member_invite
 * @property string $default_snippet_privacy
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TeamInvitation> $invitations
 * @property-read int|null $invitations_count
 * @property-read \App\Models\TeamMember|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $members
 * @property-read int|null $members_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Share> $shares
 * @property-read int|null $shares_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Snippet> $snippets
 * @property-read int|null $snippets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereAllowMemberInvite($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDefaultSnippetPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereMemberCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team wherePrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSnippetCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Team withoutTrashed()
 */
	class Team extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $team_id
 * @property string $invited_by
 * @property string $email
 * @property string|null $user_id
 * @property string $role
 * @property string $token
 * @property string|null $message
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $accepted_at
 * @property string|null $declined_at
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $inviter
 * @property-read \App\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation pending()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereAcceptedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereDeclinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamInvitation whereUserId($value)
 */
	class TeamInvitation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $team_id
 * @property string $user_id
 * @property string $role
 * @property bool $can_create_snippets
 * @property bool $can_edit_snippets
 * @property bool $can_delete_snippets
 * @property bool $can_manage_members
 * @property bool $can_invite_members
 * @property string|null $invited_by
 * @property \Illuminate\Support\Carbon $joined_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $inviter
 * @property-read \App\Models\Team $team
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCanCreateSnippets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCanDeleteSnippets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCanEditSnippets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCanInviteMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCanManageMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereInvitedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereJoinedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember whereUserId($value)
 */
	class TeamMember extends \Eloquent {}
}

namespace App\Models{
/**
 * @property string $id
 * @property string $username
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
 * @property string|null $full_name
 * @property string|null $bio
 * @property string|null $avatar_url
 * @property string|null $location
 * @property string|null $company
 * @property string|null $github_url
 * @property string|null $twitter_url
 * @property string|null $website_url
 * @property bool $is_admin
 * @property bool $is_active
 * @property string $profile_visibility
 * @property int $show_email
 * @property int $show_activity
 * @property string $default_snippet_privacy
 * @property string $theme_preference
 * @property-read int|null $snippets_count
 * @property-read int|null $followers_count
 * @property-read int|null $following_count
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $social_provider
 * @property string|null $social_id
 * @property string|null $avatar
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuditLog> $auditLogs
 * @property-read int|null $audit_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Collection> $collections
 * @property-read int|null $collections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\TeamMember|\App\Models\Follow|\App\Models\Favorite|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Snippet> $favorites
 * @property-read int|null $favorites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $followers
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $following
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $ownedTeams
 * @property-read int|null $owned_teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Share> $receivedShares
 * @property-read int|null $received_shares_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Share> $shares
 * @property-read int|null $shares_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Snippet> $snippets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Team> $teams
 * @property-read int|null $teams_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCompany($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDefaultSnippetPrivacy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFollowersCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFollowingCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereGithubUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfileVisibility($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereShowActivity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereShowEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSnippetsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSocialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereSocialProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereThemePreference($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwitterUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereWebsiteUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

