import { Head, Link } from '@inertiajs/react';
import { Button } from '@/Components/ui/button';

export default function Welcome({ auth }) {
    return (
        <>
            <Head title="Welcome" />

            <div className="min-h-screen bg-gradient-to-b from-background to-muted">
                {/* Header */}
                <header className="container mx-auto px-4 py-6">
                    <nav className="flex items-center justify-between">
                        <div className="flex items-center space-x-2">
                            <svg
                                className="h-8 w-8 text-primary"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"
                                />
                            </svg>
                            <span className="text-xl font-bold">SnippetShare</span>
                        </div>

                        <div className="flex items-center space-x-4">
                            {auth?.user ? (
                                <Link href="/dashboard">
                                    <Button>Dashboard</Button>
                                </Link>
                            ) : (
                                <>
                                    <Link href="/login">
                                        <Button variant="ghost">Log in</Button>
                                    </Link>
                                    <Link href="/register">
                                        <Button>Get Started</Button>
                                    </Link>
                                </>
                            )}
                        </div>
                    </nav>
                </header>

                {/* Hero Section */}
                <main className="container mx-auto px-4 py-20">
                    <div className="mx-auto max-w-3xl text-center">
                        <h1 className="text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
                            Share your code snippets
                            <span className="text-primary"> effortlessly</span>
                        </h1>
                        <p className="mt-6 text-lg text-muted-foreground">
                            Create, organize, and share your code snippets with beautiful syntax highlighting.
                            Collaborate with your team and discover snippets from the community.
                        </p>
                        <div className="mt-10 flex items-center justify-center gap-4">
                            <Link href="/register">
                                <Button size="lg" className="px-8">
                                    Start for Free
                                </Button>
                            </Link>
                            <Link href="/explore">
                                <Button size="lg" variant="outline" className="px-8">
                                    Explore Snippets
                                </Button>
                            </Link>
                        </div>
                    </div>

                    {/* Features Section */}
                    <div className="mt-24 grid gap-8 md:grid-cols-3">
                        <div className="rounded-lg border bg-card p-6 text-card-foreground">
                            <div className="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                                <svg
                                    className="h-6 w-6 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                    />
                                </svg>
                            </div>
                            <h3 className="mb-2 text-lg font-semibold">Syntax Highlighting</h3>
                            <p className="text-muted-foreground">
                                Beautiful code highlighting for 30+ programming languages using Pygments.
                            </p>
                        </div>

                        <div className="rounded-lg border bg-card p-6 text-card-foreground">
                            <div className="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                                <svg
                                    className="h-6 w-6 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                            </div>
                            <h3 className="mb-2 text-lg font-semibold">Team Collaboration</h3>
                            <p className="text-muted-foreground">
                                Create teams, share snippets, and collaborate with your colleagues.
                            </p>
                        </div>

                        <div className="rounded-lg border bg-card p-6 text-card-foreground">
                            <div className="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
                                <svg
                                    className="h-6 w-6 text-primary"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <h3 className="mb-2 text-lg font-semibold">Version History</h3>
                            <p className="text-muted-foreground">
                                Track changes with full version history and easy rollback.
                            </p>
                        </div>
                    </div>
                </main>

                {/* Footer */}
                <footer className="container mx-auto border-t px-4 py-8">
                    <div className="flex items-center justify-between text-sm text-muted-foreground">
                        <p>&copy; 2024 SnippetShare. All rights reserved.</p>
                        <div className="flex space-x-6">
                            <a href="#" className="hover:text-foreground">Privacy</a>
                            <a href="#" className="hover:text-foreground">Terms</a>
                            <a href="#" className="hover:text-foreground">Contact</a>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
