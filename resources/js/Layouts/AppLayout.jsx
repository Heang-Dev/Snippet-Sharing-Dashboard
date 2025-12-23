import { useState } from 'react';
import { Link, usePage } from '@inertiajs/react';
import {
    Home,
    Code2,
    Users,
    Search,
    Settings,
    Bell,
    Menu,
    X,
    Plus,
    User,
    LogOut,
    Moon,
    Sun,
    Heart,
    FolderOpen,
    TrendingUp,
    Shield,
} from 'lucide-react';
import { Button } from '@/Components/ui/button';
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';
import { Separator } from '@/Components/ui/separator';
import { cn } from '@/lib/utils';

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: Home },
    { name: 'My Snippets', href: '/snippets', icon: Code2 },
    { name: 'Teams', href: '/teams', icon: Users },
    { name: 'Favorites', href: '/favorites', icon: Heart },
    { name: 'Collections', href: '/collections', icon: FolderOpen },
];

const secondaryNav = [
    { name: 'Explore', href: '/explore', icon: TrendingUp },
    { name: 'Search', href: '/search', icon: Search },
];

export default function AppLayout({ children, title }) {
    const { auth } = usePage().props;
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [darkMode, setDarkMode] = useState(
        typeof window !== 'undefined' && document.documentElement.classList.contains('dark')
    );

    const toggleDarkMode = () => {
        setDarkMode(!darkMode);
        document.documentElement.classList.toggle('dark');
    };

    const isActive = (href) => {
        if (typeof window === 'undefined') return false;
        return window.location.pathname.startsWith(href);
    };

    return (
        <div className="min-h-screen bg-background">
            {/* Mobile sidebar overlay */}
            {sidebarOpen && (
                <div
                    className="fixed inset-0 z-40 bg-black/50 lg:hidden"
                    onClick={() => setSidebarOpen(false)}
                />
            )}

            {/* Sidebar */}
            <aside
                className={cn(
                    'fixed inset-y-0 left-0 z-50 w-64 bg-card border-r transform transition-transform lg:translate-x-0',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full'
                )}
            >
                {/* Logo */}
                <div className="flex h-16 items-center gap-2 px-6 border-b">
                    <Code2 className="h-8 w-8 text-primary" />
                    <span className="font-bold text-xl">SnippetShare</span>
                    <button
                        onClick={() => setSidebarOpen(false)}
                        className="ml-auto lg:hidden"
                    >
                        <X className="h-5 w-5" />
                    </button>
                </div>

                {/* Navigation */}
                <nav className="flex-1 px-4 py-6 space-y-1">
                    {navigation.map((item) => (
                        <Link
                            key={item.name}
                            href={item.href}
                            className={cn(
                                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                'hover:bg-accent hover:text-accent-foreground',
                                isActive(item.href)
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-muted-foreground'
                            )}
                        >
                            <item.icon className="h-5 w-5" />
                            {item.name}
                        </Link>
                    ))}

                    <Separator className="my-4" />

                    {secondaryNav.map((item) => (
                        <Link
                            key={item.name}
                            href={item.href}
                            className={cn(
                                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                'hover:bg-accent hover:text-accent-foreground',
                                isActive(item.href)
                                    ? 'bg-accent text-accent-foreground'
                                    : 'text-muted-foreground'
                            )}
                        >
                            <item.icon className="h-5 w-5" />
                            {item.name}
                        </Link>
                    ))}

                    {auth?.user?.is_admin && (
                        <>
                            <Separator className="my-4" />
                            <Link
                                href="/admin"
                                className={cn(
                                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                    'hover:bg-accent hover:text-accent-foreground',
                                    isActive('/admin')
                                        ? 'bg-accent text-accent-foreground'
                                        : 'text-muted-foreground'
                                )}
                            >
                                <Shield className="h-5 w-5" />
                                Admin Panel
                            </Link>
                        </>
                    )}
                </nav>

                {/* Create button */}
                <div className="p-4 border-t">
                    <Button asChild className="w-full">
                        <Link href="/snippets/create">
                            <Plus className="h-4 w-4 mr-2" />
                            New Snippet
                        </Link>
                    </Button>
                </div>
            </aside>

            {/* Main content */}
            <div className="lg:pl-64">
                {/* Top header */}
                <header className="sticky top-0 z-30 flex h-16 items-center gap-4 border-b bg-background/95 backdrop-blur px-6">
                    <button
                        onClick={() => setSidebarOpen(true)}
                        className="lg:hidden"
                    >
                        <Menu className="h-6 w-6" />
                    </button>

                    <div className="flex-1">
                        {title && (
                            <h1 className="text-lg font-semibold">{title}</h1>
                        )}
                    </div>

                    <div className="flex items-center gap-2">
                        {/* Theme toggle */}
                        <Button
                            variant="ghost"
                            size="icon"
                            onClick={toggleDarkMode}
                        >
                            {darkMode ? (
                                <Sun className="h-5 w-5" />
                            ) : (
                                <Moon className="h-5 w-5" />
                            )}
                        </Button>

                        {/* Notifications */}
                        <Button variant="ghost" size="icon" asChild>
                            <Link href="/notifications">
                                <Bell className="h-5 w-5" />
                            </Link>
                        </Button>

                        {/* User menu */}
                        <DropdownMenu>
                            <DropdownMenuTrigger asChild>
                                <Button
                                    variant="ghost"
                                    className="relative h-10 w-10 rounded-full"
                                >
                                    <Avatar>
                                        <AvatarImage src={auth?.user?.avatar_url} />
                                        <AvatarFallback>
                                            {auth?.user?.username?.charAt(0).toUpperCase() || 'U'}
                                        </AvatarFallback>
                                    </Avatar>
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end" className="w-56">
                                <div className="px-2 py-1.5">
                                    <p className="text-sm font-medium">
                                        {auth?.user?.full_name || auth?.user?.username}
                                    </p>
                                    <p className="text-xs text-muted-foreground">
                                        {auth?.user?.email}
                                    </p>
                                </div>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem asChild>
                                    <Link href="/profile" className="cursor-pointer">
                                        <User className="mr-2 h-4 w-4" />
                                        Profile
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem asChild>
                                    <Link href="/settings" className="cursor-pointer">
                                        <Settings className="mr-2 h-4 w-4" />
                                        Settings
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem asChild>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        className="w-full cursor-pointer"
                                    >
                                        <LogOut className="mr-2 h-4 w-4" />
                                        Log out
                                    </Link>
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </header>

                {/* Page content */}
                <main className="p-6">{children}</main>
            </div>
        </div>
    );
}
