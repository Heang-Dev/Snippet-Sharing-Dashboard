import { Head, Link, router, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Textarea } from '@/Components/ui/textarea';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import {
    ArrowLeft,
    Star,
    Eye,
    Clock,
    Copy,
    Share2,
    GitFork,
    MoreVertical,
    Edit,
    Trash2,
    Code2,
    MessageSquare,
    History,
} from 'lucide-react';
import { useState } from 'react';
import { toast } from 'sonner';
import { formatDistanceToNow, formatDate } from '@/lib/utils';

export default function SnippetShow({ snippet, isFavorited, auth }) {
    const [showVersions, setShowVersions] = useState(false);
    const isOwner = auth?.user?.id === snippet.user_id;

    const copyToClipboard = () => {
        navigator.clipboard.writeText(snippet.code);
        toast.success('Code copied to clipboard!');
    };

    const handleFavorite = () => {
        router.post(`/snippets/${snippet.slug}/favorite`, {}, { preserveScroll: true });
    };

    const handleFork = () => {
        router.post(`/snippets/${snippet.slug}/fork`);
    };

    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this snippet?')) {
            router.delete(`/snippets/${snippet.slug}`);
        }
    };

    return (
        <AppLayout title={snippet.title}>
            <Head title={snippet.title} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-start justify-between gap-4">
                    <div className="flex items-start gap-4">
                        <Link href="/snippets">
                            <Button variant="ghost" size="icon">
                                <ArrowLeft className="h-4 w-4" />
                            </Button>
                        </Link>
                        <div className="space-y-1">
                            <div className="flex items-center gap-3">
                                <h1 className="text-2xl font-bold">{snippet.title}</h1>
                                <Badge
                                    variant={
                                        snippet.visibility === 'public'
                                            ? 'default'
                                            : snippet.visibility === 'team'
                                            ? 'secondary'
                                            : 'outline'
                                    }
                                >
                                    {snippet.visibility}
                                </Badge>
                            </div>
                            {snippet.description && (
                                <p className="text-muted-foreground">{snippet.description}</p>
                            )}
                            <div className="flex items-center gap-4 text-sm text-muted-foreground">
                                <div className="flex items-center gap-2">
                                    <Avatar className="h-5 w-5">
                                        <AvatarImage src={snippet.user?.avatar_url} />
                                        <AvatarFallback>
                                            {snippet.user?.username?.charAt(0).toUpperCase()}
                                        </AvatarFallback>
                                    </Avatar>
                                    <span>{snippet.user?.username}</span>
                                </div>
                                <span className="flex items-center gap-1">
                                    <Clock className="h-3 w-3" />
                                    {formatDistanceToNow(snippet.created_at)}
                                </span>
                                <span className="flex items-center gap-1">
                                    <Eye className="h-3 w-3" />
                                    {snippet.views_count} views
                                </span>
                                <span className="flex items-center gap-1">
                                    <Star className="h-3 w-3" />
                                    {snippet.favorites_count} favorites
                                </span>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center gap-2">
                        <Button
                            variant={isFavorited ? 'default' : 'outline'}
                            size="sm"
                            onClick={handleFavorite}
                        >
                            <Star className={`mr-2 h-4 w-4 ${isFavorited ? 'fill-current' : ''}`} />
                            {isFavorited ? 'Favorited' : 'Favorite'}
                        </Button>
                        <Button variant="outline" size="sm" onClick={handleFork}>
                            <GitFork className="mr-2 h-4 w-4" />
                            Fork
                        </Button>
                        <Button variant="outline" size="sm" onClick={copyToClipboard}>
                            <Copy className="mr-2 h-4 w-4" />
                            Copy
                        </Button>

                        {isOwner && (
                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="ghost" size="icon">
                                        <MoreVertical className="h-4 w-4" />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem asChild>
                                        <Link href={`/snippets/${snippet.slug}/edit`}>
                                            <Edit className="mr-2 h-4 w-4" />
                                            Edit
                                        </Link>
                                    </DropdownMenuItem>
                                    <DropdownMenuItem
                                        className="text-destructive"
                                        onClick={handleDelete}
                                    >
                                        <Trash2 className="mr-2 h-4 w-4" />
                                        Delete
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        )}
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Code Section */}
                    <div className="lg:col-span-2 space-y-6">
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <div className="flex items-center gap-2">
                                    <Code2 className="h-4 w-4" />
                                    <CardTitle className="text-base">
                                        {snippet.file_name || 'Code'}
                                    </CardTitle>
                                    {snippet.language && (
                                        <Badge variant="secondary">{snippet.language.name}</Badge>
                                    )}
                                </div>
                                <span className="text-sm text-muted-foreground">
                                    v{snippet.version}
                                </span>
                            </CardHeader>
                            <CardContent>
                                <pre className="p-4 rounded-lg bg-muted overflow-x-auto">
                                    <code className="text-sm font-mono">{snippet.code}</code>
                                </pre>
                            </CardContent>
                        </Card>

                        {/* Version History */}
                        {snippet.versions && snippet.versions.length > 0 && (
                            <Card>
                                <CardHeader className="cursor-pointer" onClick={() => setShowVersions(!showVersions)}>
                                    <div className="flex items-center justify-between">
                                        <div className="flex items-center gap-2">
                                            <History className="h-4 w-4" />
                                            <CardTitle className="text-base">Version History</CardTitle>
                                        </div>
                                        <Badge variant="outline">{snippet.versions.length} versions</Badge>
                                    </div>
                                </CardHeader>
                                {showVersions && (
                                    <CardContent>
                                        <div className="space-y-3">
                                            {snippet.versions.map((version) => (
                                                <div
                                                    key={version.id}
                                                    className="flex items-start justify-between p-3 rounded-lg bg-muted/50"
                                                >
                                                    <div>
                                                        <div className="flex items-center gap-2">
                                                            <span className="font-medium">
                                                                v{version.version_number}
                                                            </span>
                                                            {version.version_number === snippet.version && (
                                                                <Badge variant="secondary" className="text-xs">
                                                                    Current
                                                                </Badge>
                                                            )}
                                                        </div>
                                                        <p className="text-sm text-muted-foreground">
                                                            {version.change_description}
                                                        </p>
                                                    </div>
                                                    <span className="text-xs text-muted-foreground">
                                                        {formatDate(version.created_at)}
                                                    </span>
                                                </div>
                                            ))}
                                        </div>
                                    </CardContent>
                                )}
                            </Card>
                        )}

                        {/* Comments Section */}
                        <Card>
                            <CardHeader>
                                <div className="flex items-center gap-2">
                                    <MessageSquare className="h-4 w-4" />
                                    <CardTitle className="text-base">
                                        Comments ({snippet.comments?.length || 0})
                                    </CardTitle>
                                </div>
                            </CardHeader>
                            <CardContent>
                                {snippet.comments && snippet.comments.length > 0 ? (
                                    <div className="space-y-4">
                                        {snippet.comments.map((comment) => (
                                            <div key={comment.id} className="flex gap-3">
                                                <Avatar className="h-8 w-8">
                                                    <AvatarImage src={comment.user?.avatar_url} />
                                                    <AvatarFallback>
                                                        {comment.user?.username?.charAt(0).toUpperCase()}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <div className="flex-1">
                                                    <div className="flex items-center gap-2">
                                                        <span className="font-medium text-sm">
                                                            {comment.user?.username}
                                                        </span>
                                                        <span className="text-xs text-muted-foreground">
                                                            {formatDistanceToNow(comment.created_at)}
                                                        </span>
                                                    </div>
                                                    <p className="text-sm mt-1">{comment.content}</p>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <p className="text-sm text-muted-foreground text-center py-4">
                                        No comments yet. Be the first to comment!
                                    </p>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">Information</CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Language</span>
                                    <span>{snippet.language?.name || 'Unknown'}</span>
                                </div>
                                {snippet.category && (
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">Category</span>
                                        <span>{snippet.category.name}</span>
                                    </div>
                                )}
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Version</span>
                                    <span>v{snippet.version}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Created</span>
                                    <span>{formatDate(snippet.created_at)}</span>
                                </div>
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted-foreground">Updated</span>
                                    <span>{formatDate(snippet.updated_at)}</span>
                                </div>
                                {snippet.forked_from_id && (
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">Forked from</span>
                                        <Link
                                            href={`/snippets/${snippet.forked_from?.slug}`}
                                            className="text-primary hover:underline"
                                        >
                                            Original
                                        </Link>
                                    </div>
                                )}
                                {snippet.forks_count > 0 && (
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted-foreground">Forks</span>
                                        <span>{snippet.forks_count}</span>
                                    </div>
                                )}
                            </CardContent>
                        </Card>

                        {snippet.tags && snippet.tags.length > 0 && (
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-base">Tags</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex flex-wrap gap-2">
                                        {snippet.tags.map((tag) => (
                                            <Badge key={tag.id} variant="secondary">
                                                {tag.name}
                                            </Badge>
                                        ))}
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {/* Author Card */}
                        <Card>
                            <CardHeader>
                                <CardTitle className="text-base">Author</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div className="flex items-center gap-3">
                                    <Avatar>
                                        <AvatarImage src={snippet.user?.avatar_url} />
                                        <AvatarFallback>
                                            {snippet.user?.username?.charAt(0).toUpperCase()}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <p className="font-medium">{snippet.user?.full_name || snippet.user?.username}</p>
                                        <p className="text-sm text-muted-foreground">@{snippet.user?.username}</p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
