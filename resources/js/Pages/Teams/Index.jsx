import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Plus, Users, Code2, Crown, Mail, Check, X } from 'lucide-react';
import { formatDistanceToNow } from '@/lib/utils';

export default function TeamsIndex({ ownedTeams, memberTeams, pendingInvitations }) {
    const handleAcceptInvitation = (invitation) => {
        router.post(`/invitations/${invitation.id}/accept`);
    };

    const handleDeclineInvitation = (invitation) => {
        router.post(`/invitations/${invitation.id}/decline`);
    };

    return (
        <AppLayout title="Teams">
            <Head title="Teams" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold tracking-tight">Teams</h2>
                        <p className="text-muted-foreground">
                            Manage your teams and collaborate with others
                        </p>
                    </div>
                    <Link href="/teams/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            New Team
                        </Button>
                    </Link>
                </div>

                {/* Pending Invitations */}
                {pendingInvitations.length > 0 && (
                    <Card className="border-primary">
                        <CardHeader>
                            <div className="flex items-center gap-2">
                                <Mail className="h-5 w-5 text-primary" />
                                <CardTitle>Pending Invitations</CardTitle>
                            </div>
                            <CardDescription>
                                You have been invited to join these teams
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {pendingInvitations.map((invitation) => (
                                    <div
                                        key={invitation.id}
                                        className="flex items-center justify-between rounded-lg border p-4"
                                    >
                                        <div className="flex items-center gap-4">
                                            <Avatar>
                                                <AvatarImage src={invitation.team?.avatar_url} />
                                                <AvatarFallback>
                                                    {invitation.team?.name?.charAt(0).toUpperCase()}
                                                </AvatarFallback>
                                            </Avatar>
                                            <div>
                                                <p className="font-medium">{invitation.team?.name}</p>
                                                <p className="text-sm text-muted-foreground">
                                                    Invited by {invitation.inviter?.username} as {invitation.role}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex gap-2">
                                            <Button
                                                size="sm"
                                                onClick={() => handleAcceptInvitation(invitation)}
                                            >
                                                <Check className="mr-1 h-4 w-4" />
                                                Accept
                                            </Button>
                                            <Button
                                                size="sm"
                                                variant="outline"
                                                onClick={() => handleDeclineInvitation(invitation)}
                                            >
                                                <X className="mr-1 h-4 w-4" />
                                                Decline
                                            </Button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </CardContent>
                    </Card>
                )}

                {/* Owned Teams */}
                <div className="space-y-4">
                    <div className="flex items-center gap-2">
                        <Crown className="h-5 w-5 text-yellow-500" />
                        <h3 className="text-lg font-semibold">Teams You Own</h3>
                    </div>
                    {ownedTeams.length > 0 ? (
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {ownedTeams.map((team) => (
                                <TeamCard key={team.id} team={team} isOwner />
                            ))}
                        </div>
                    ) : (
                        <Card>
                            <CardContent className="py-8 text-center">
                                <Users className="mx-auto h-12 w-12 text-muted-foreground/50" />
                                <p className="mt-2 text-muted-foreground">
                                    You haven't created any teams yet.
                                </p>
                                <Link href="/teams/create">
                                    <Button className="mt-4" variant="outline">
                                        <Plus className="mr-2 h-4 w-4" />
                                        Create Your First Team
                                    </Button>
                                </Link>
                            </CardContent>
                        </Card>
                    )}
                </div>

                {/* Member Teams */}
                <div className="space-y-4">
                    <div className="flex items-center gap-2">
                        <Users className="h-5 w-5 text-blue-500" />
                        <h3 className="text-lg font-semibold">Teams You're In</h3>
                    </div>
                    {memberTeams.length > 0 ? (
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {memberTeams.map((team) => (
                                <TeamCard key={team.id} team={team} />
                            ))}
                        </div>
                    ) : (
                        <Card>
                            <CardContent className="py-8 text-center">
                                <Users className="mx-auto h-12 w-12 text-muted-foreground/50" />
                                <p className="mt-2 text-muted-foreground">
                                    You're not a member of any other teams.
                                </p>
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}

function TeamCard({ team, isOwner = false }) {
    return (
        <Card className="hover:bg-muted/50 transition-colors">
            <CardHeader className="pb-2">
                <div className="flex items-start justify-between">
                    <div className="flex items-center gap-3">
                        <Avatar>
                            <AvatarImage src={team.avatar_url} />
                            <AvatarFallback>
                                {team.name?.charAt(0).toUpperCase()}
                            </AvatarFallback>
                        </Avatar>
                        <div>
                            <Link
                                href={`/teams/${team.slug}`}
                                className="font-semibold hover:underline"
                            >
                                {team.name}
                            </Link>
                            {isOwner && (
                                <Badge variant="outline" className="ml-2 text-xs">
                                    Owner
                                </Badge>
                            )}
                        </div>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                {team.description && (
                    <p className="text-sm text-muted-foreground line-clamp-2 mb-4">
                        {team.description}
                    </p>
                )}
                <div className="flex items-center gap-4 text-sm text-muted-foreground">
                    <span className="flex items-center gap-1">
                        <Users className="h-4 w-4" />
                        {team.members_count} members
                    </span>
                    <span className="flex items-center gap-1">
                        <Code2 className="h-4 w-4" />
                        {team.snippets_count} snippets
                    </span>
                </div>
            </CardContent>
        </Card>
    );
}
