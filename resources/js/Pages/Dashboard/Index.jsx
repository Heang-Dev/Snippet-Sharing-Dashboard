import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Code2, Users, Eye, Heart, Star, Plus } from 'lucide-react';

export default function Dashboard({ stats = {}, recentSnippets = [], popularSnippets = [] }) {
    const statsData = [
        {
            name: 'Total Snippets',
            value: stats.total_snippets || 0,
            icon: Code2,
            description: 'Your snippets'
        },
        {
            name: 'Public',
            value: stats.public_snippets || 0,
            icon: Users,
            description: 'Publicly visible'
        },
        {
            name: 'Total Views',
            value: stats.total_views || 0,
            icon: Eye,
            description: 'All time views'
        },
        {
            name: 'Favorites',
            value: stats.total_favorites || 0,
            icon: Heart,
            description: 'Times favorited'
        },
    ];

    return (
        <AppLayout title="Dashboard">
            <Head title="Dashboard" />

            <div className="space-y-6">
                {/* Welcome message */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold tracking-tight">Welcome back!</h2>
                        <p className="text-muted-foreground">
                            Here's an overview of your snippet activity.
                        </p>
                    </div>
                    <Link href="/snippets/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            New Snippet
                        </Button>
                    </Link>
                </div>

                {/* Stats grid */}
                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {statsData.map((stat) => (
                        <Card key={stat.name}>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">
                                    {stat.name}
                                </CardTitle>
                                <stat.icon className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{stat.value}</div>
                                <p className="text-xs text-muted-foreground">
                                    {stat.description}
                                </p>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                {/* Recent activity */}
                <div className="grid gap-4 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Snippets</CardTitle>
                            <CardDescription>
                                Your recently created or edited snippets
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {recentSnippets.length > 0 ? (
                                <div className="space-y-4">
                                    {recentSnippets.map((snippet) => (
                                        <div key={snippet.id} className="flex items-center justify-between">
                                            <div className="space-y-1">
                                                <Link
                                                    href={`/snippets/${snippet.slug}`}
                                                    className="font-medium hover:underline"
                                                >
                                                    {snippet.title}
                                                </Link>
                                                <div className="flex items-center gap-2">
                                                    <Badge variant="secondary" className="text-xs">
                                                        {snippet.language?.name || 'Unknown'}
                                                    </Badge>
                                                    <span className="text-xs text-muted-foreground">
                                                        {snippet.visibility}
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex items-center text-sm text-muted-foreground">
                                                <Eye className="mr-1 h-3 w-3" />
                                                {snippet.views_count}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="py-8 text-center">
                                    <Code2 className="mx-auto h-12 w-12 text-muted-foreground/50" />
                                    <p className="mt-2 text-sm text-muted-foreground">
                                        No snippets yet. Create your first snippet!
                                    </p>
                                    <Link href="/snippets/create">
                                        <Button variant="outline" size="sm" className="mt-4">
                                            <Plus className="mr-2 h-4 w-4" />
                                            Create Snippet
                                        </Button>
                                    </Link>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Popular Snippets</CardTitle>
                            <CardDescription>
                                Your most viewed snippets
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            {popularSnippets.length > 0 ? (
                                <div className="space-y-4">
                                    {popularSnippets.map((snippet) => (
                                        <div key={snippet.id} className="flex items-center justify-between">
                                            <div className="space-y-1">
                                                <Link
                                                    href={`/snippets/${snippet.slug}`}
                                                    className="font-medium hover:underline"
                                                >
                                                    {snippet.title}
                                                </Link>
                                                <div className="flex items-center gap-2">
                                                    <Badge variant="secondary" className="text-xs">
                                                        {snippet.language?.name || 'Unknown'}
                                                    </Badge>
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-3 text-sm text-muted-foreground">
                                                <span className="flex items-center">
                                                    <Eye className="mr-1 h-3 w-3" />
                                                    {snippet.views_count}
                                                </span>
                                                <span className="flex items-center">
                                                    <Star className="mr-1 h-3 w-3" />
                                                    {snippet.favorites_count}
                                                </span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            ) : (
                                <div className="py-8 text-center">
                                    <Star className="mx-auto h-12 w-12 text-muted-foreground/50" />
                                    <p className="mt-2 text-sm text-muted-foreground">
                                        No popular snippets yet.
                                    </p>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                {/* Quick Stats Footer */}
                <Card>
                    <CardContent className="pt-6">
                        <div className="flex items-center justify-around text-center">
                            <div>
                                <div className="text-2xl font-bold">{stats.followers_count || 0}</div>
                                <p className="text-sm text-muted-foreground">Followers</p>
                            </div>
                            <div className="h-8 w-px bg-border" />
                            <div>
                                <div className="text-2xl font-bold">{stats.following_count || 0}</div>
                                <p className="text-sm text-muted-foreground">Following</p>
                            </div>
                            <div className="h-8 w-px bg-border" />
                            <div>
                                <div className="text-2xl font-bold">{stats.private_snippets || 0}</div>
                                <p className="text-sm text-muted-foreground">Private Snippets</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </AppLayout>
    );
}
