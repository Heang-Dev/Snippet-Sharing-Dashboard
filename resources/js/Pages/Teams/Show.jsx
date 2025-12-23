import { Head, Link, router, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import {
    ArrowLeft,
    Users,
    Code2,
    Settings,
    UserPlus,
    MoreVertical,
    Crown,
    Shield,
    User,
    LogOut,
    Trash2,
    Eye,
    Clock,
} from 'lucide-react';
import { useState } from 'react';
import { formatDistanceToNow } from '@/lib/utils';

export default function TeamsShow({ team, userRole, isOwner }) {
    const [showInviteForm, setShowInviteForm] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        role: 'member',
    });

    const handleInvite = (e) => {
        e.preventDefault();
        post(`/teams/${team.slug}/invite`, {
            onSuccess: () => {
                reset();
                setShowInviteForm(false);
            },
        });
    };

    const handleRemoveMember = (memberId) => {
        if (confirm('Are you sure you want to remove this member?')) {
            router.delete(`/teams/${team.slug}/members/${memberId}`);
        }
    };

    const handleUpdateRole = (memberId, newRole) => {
        router.patch(`/teams/${team.slug}/members/${memberId}/role`, { role: newRole });
    };

    const handleLeaveTeam = () => {
        if (confirm('Are you sure you want to leave this team?')) {
            router.post(`/teams/${team.slug}/leave`);
        }
    };

    const canManageMembers = isOwner || userRole === 'admin';

    return (
        <AppLayout title={team.name}>
            <Head title={team.name} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-start justify-between">
                    <div className="flex items-start gap-4">
                        <Link href="/teams">
                            <Button variant="ghost" size="icon">
                                <ArrowLeft className="h-4 w-4" />
                            </Button>
                        </Link>
                        <div className="flex items-center gap-4">
                            <Avatar className="h-16 w-16">
                                <AvatarImage src={team.avatar_url} />
                                <AvatarFallback className="text-2xl">
                                    {team.name?.charAt(0).toUpperCase()}
                                </AvatarFallback>
                            </Avatar>
                            <div>
                                <div className="flex items-center gap-2">
                                    <h1 className="text-2xl font-bold">{team.name}</h1>
                                    {isOwner && (
                                        <Badge variant="outline">
                                            <Crown className="mr-1 h-3 w-3" />
                                            Owner
                                        </Badge>
                                    )}
                                </div>
                                {team.description && (
                                    <p className="text-muted-foreground mt-1">{team.description}</p>
                                )}
                            </div>
                        </div>
                    </div>

                    <div className="flex gap-2">
                        {isOwner && (
                            <Link href={`/teams/${team.slug}/edit`}>
                                <Button variant="outline" size="sm">
                                    <Settings className="mr-2 h-4 w-4" />
                                    Settings
                                </Button>
                            </Link>
                        )}
                        {!isOwner && (
                            <Button variant="outline" size="sm" onClick={handleLeaveTeam}>
                                <LogOut className="mr-2 h-4 w-4" />
                                Leave Team
                            </Button>
                        )}
                    </div>
                </div>

                <div className="grid gap-6 lg:grid-cols-3">
                    {/* Main Content */}
                    <div className="lg:col-span-2 space-y-6">
                        {/* Team Snippets */}
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Code2 className="h-5 w-5" />
                                        <CardTitle>Team Snippets</CardTitle>
                                    </div>
                                    <Link href={`/snippets/create?team=${team.id}`}>
                                        <Button size="sm">Add Snippet</Button>
                                    </Link>
                                </div>
                                <CardDescription>
                                    Snippets shared within this team
                                </CardDescription>
                            </CardHeader>
                            <CardContent>
                                {team.snippets && team.snippets.length > 0 ? (
                                    <div className="space-y-4">
                                        {team.snippets.map((snippet) => (
                                            <div
                                                key={snippet.id}
                                                className="flex items-center justify-between p-3 rounded-lg bg-muted/50"
                                            >
                                                <div className="flex-1">
                                                    <Link
                                                        href={`/snippets/${snippet.slug}`}
                                                        className="font-medium hover:underline"
                                                    >
                                                        {snippet.title}
                                                    </Link>
                                                    <div className="flex items-center gap-3 mt-1 text-sm text-muted-foreground">
                                                        <span>{snippet.language?.name}</span>
                                                        <span className="flex items-center gap-1">
                                                            <User className="h-3 w-3" />
                                                            {snippet.user?.username}
                                                        </span>
                                                        <span className="flex items-center gap-1">
                                                            <Clock className="h-3 w-3" />
                                                            {formatDistanceToNow(snippet.created_at)}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                    <Eye className="h-4 w-4" />
                                                    {snippet.views_count}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="py-8 text-center">
                                        <Code2 className="mx-auto h-12 w-12 text-muted-foreground/50" />
                                        <p className="mt-2 text-muted-foreground">
                                            No snippets in this team yet.
                                        </p>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-6">
                        {/* Members */}
                        <Card>
                            <CardHeader>
                                <div className="flex items-center justify-between">
                                    <div className="flex items-center gap-2">
                                        <Users className="h-5 w-5" />
                                        <CardTitle>Members ({team.members?.length || 0})</CardTitle>
                                    </div>
                                    {canManageMembers && (
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            onClick={() => setShowInviteForm(!showInviteForm)}
                                        >
                                            <UserPlus className="h-4 w-4" />
                                        </Button>
                                    )}
                                </div>
                            </CardHeader>
                            <CardContent className="space-y-4">
                                {/* Invite Form */}
                                {showInviteForm && canManageMembers && (
                                    <form onSubmit={handleInvite} className="space-y-3 p-3 rounded-lg bg-muted">
                                        <div className="space-y-2">
                                            <Label htmlFor="email">Email Address</Label>
                                            <Input
                                                id="email"
                                                type="email"
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                placeholder="member@example.com"
                                                className={errors.email ? 'border-destructive' : ''}
                                            />
                                            {errors.email && (
                                                <p className="text-sm text-destructive">{errors.email}</p>
                                            )}
                                        </div>
                                        <div className="space-y-2">
                                            <Label htmlFor="role">Role</Label>
                                            <Select
                                                value={data.role}
                                                onValueChange={(value) => setData('role', value)}
                                            >
                                                <SelectTrigger>
                                                    <SelectValue />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="member">Member</SelectItem>
                                                    <SelectItem value="admin">Admin</SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>
                                        <Button type="submit" size="sm" disabled={processing} className="w-full">
                                            {processing ? 'Sending...' : 'Send Invitation'}
                                        </Button>
                                    </form>
                                )}

                                {/* Member List */}
                                <div className="space-y-3">
                                    {team.members?.map((member) => (
                                        <div key={member.id} className="flex items-center justify-between">
                                            <div className="flex items-center gap-3">
                                                <Avatar className="h-8 w-8">
                                                    <AvatarImage src={member.avatar_url} />
                                                    <AvatarFallback>
                                                        {member.username?.charAt(0).toUpperCase()}
                                                    </AvatarFallback>
                                                </Avatar>
                                                <div>
                                                    <p className="text-sm font-medium">{member.username}</p>
                                                    <div className="flex items-center gap-1">
                                                        {team.owner_id === member.id ? (
                                                            <Badge variant="outline" className="text-xs">
                                                                <Crown className="mr-1 h-2 w-2" />
                                                                Owner
                                                            </Badge>
                                                        ) : member.pivot?.role === 'admin' ? (
                                                            <Badge variant="secondary" className="text-xs">
                                                                <Shield className="mr-1 h-2 w-2" />
                                                                Admin
                                                            </Badge>
                                                        ) : (
                                                            <Badge variant="outline" className="text-xs">
                                                                Member
                                                            </Badge>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>

                                            {isOwner && team.owner_id !== member.id && (
                                                <DropdownMenu>
                                                    <DropdownMenuTrigger asChild>
                                                        <Button variant="ghost" size="icon" className="h-8 w-8">
                                                            <MoreVertical className="h-4 w-4" />
                                                        </Button>
                                                    </DropdownMenuTrigger>
                                                    <DropdownMenuContent align="end">
                                                        {member.pivot?.role === 'member' ? (
                                                            <DropdownMenuItem
                                                                onClick={() => handleUpdateRole(member.id, 'admin')}
                                                            >
                                                                <Shield className="mr-2 h-4 w-4" />
                                                                Make Admin
                                                            </DropdownMenuItem>
                                                        ) : (
                                                            <DropdownMenuItem
                                                                onClick={() => handleUpdateRole(member.id, 'member')}
                                                            >
                                                                <User className="mr-2 h-4 w-4" />
                                                                Make Member
                                                            </DropdownMenuItem>
                                                        )}
                                                        <DropdownMenuItem
                                                            className="text-destructive"
                                                            onClick={() => handleRemoveMember(member.id)}
                                                        >
                                                            <Trash2 className="mr-2 h-4 w-4" />
                                                            Remove
                                                        </DropdownMenuItem>
                                                    </DropdownMenuContent>
                                                </DropdownMenu>
                                            )}
                                        </div>
                                    ))}
                                </div>

                                {/* Pending Invitations */}
                                {team.invitations && team.invitations.length > 0 && canManageMembers && (
                                    <div className="pt-4 border-t">
                                        <p className="text-sm font-medium mb-3">Pending Invitations</p>
                                        <div className="space-y-2">
                                            {team.invitations.map((invitation) => (
                                                <div
                                                    key={invitation.id}
                                                    className="flex items-center justify-between text-sm"
                                                >
                                                    <span className="text-muted-foreground">{invitation.email}</span>
                                                    <Badge variant="outline" className="text-xs">
                                                        {invitation.role}
                                                    </Badge>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
