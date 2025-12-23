import { Link } from '@inertiajs/react';
import { Code2 } from 'lucide-react';

export default function GuestLayout({ children }) {
    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-muted/40">
            <div className="mb-6">
                <Link href="/" className="flex items-center gap-2">
                    <Code2 className="h-10 w-10 text-primary" />
                    <span className="text-2xl font-bold">SnippetShare</span>
                </Link>
            </div>

            <div className="w-full sm:max-w-md px-6 py-8 bg-card shadow-md overflow-hidden sm:rounded-lg border">
                {children}
            </div>

            <p className="mt-6 text-sm text-muted-foreground">
                &copy; {new Date().getFullYear()} SnippetShare. All rights reserved.
            </p>
        </div>
    );
}
