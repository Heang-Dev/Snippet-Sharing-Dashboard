import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Code2, Users, Eye, Heart } from 'lucide-react';

const stats = [
    { name: 'Total Snippets', value: '0', icon: Code2, description: 'Your snippets' },
    { name: 'Team Snippets', value: '0', icon: Users, description: 'Shared with teams' },
    { name: 'Total Views', value: '0', icon: Eye, description: 'All time views' },
    { name: 'Favorites', value: '0', icon: Heart, description: 'Saved snippets' },
];

export default function Dashboard({ snippetsCount = 0, teamSnippetsCount = 0, totalViews = 0, favoritesCount = 0 }) {
    const statsData = [
        { ...stats[0], value: snippetsCount.toString() },
        { ...stats[1], value: teamSnippetsCount.toString() },
        { ...stats[2], value: totalViews.toString() },
        { ...stats[3], value: favoritesCount.toString() },
    ];

    return (
        <AppLayout title="Dashboard">
            <Head title="Dashboard" />

            <div className="space-y-6">
                {/* Welcome message */}
                <div>
                    <h2 className="text-2xl font-bold tracking-tight">Welcome back!</h2>
                    <p className="text-muted-foreground">
                        Here's an overview of your snippet activity.
                    </p>
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
                            <p className="text-sm text-muted-foreground py-8 text-center">
                                No snippets yet. Create your first snippet!
                            </p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Team Activity</CardTitle>
                            <CardDescription>
                                Recent activity from your teams
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <p className="text-sm text-muted-foreground py-8 text-center">
                                No team activity yet.
                            </p>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </AppLayout>
    );
}
