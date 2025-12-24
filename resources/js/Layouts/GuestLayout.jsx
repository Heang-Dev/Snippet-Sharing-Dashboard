import { Link } from '@inertiajs/react';
import { Command } from 'lucide-react';
import { Toaster } from '@/Components/ui/sonner';
import { AuthThemeToggle } from '@/Components/auth-theme-toggle';

export default function GuestLayout({ children }) {
    return (
        <>
            <div className="flex min-h-svh flex-col items-center justify-center bg-muted p-6 md:p-10 overflow-y-auto">
                {/* Theme Toggle - Top Right */}
                <div className="fixed top-4 right-4 z-10">
                    <AuthThemeToggle className="bg-background/80 backdrop-blur-sm hover:bg-background/90" />
                </div>

                <div className="w-full max-w-sm md:max-w-5xl my-auto">
                    {children}
                </div>

                {/* Terms & Privacy - Outside Card */}
                <div className="mt-4 text-center text-xs text-muted-foreground">
                    By clicking continue, you agree to our{" "}
                    <Link href="#" className="underline underline-offset-4 hover:text-primary">
                        Terms of Service
                    </Link>{" "}
                    and{" "}
                    <Link href="#" className="underline underline-offset-4 hover:text-primary">
                        Privacy Policy
                    </Link>
                    .
                </div>
            </div>
            <Toaster richColors position="top-right" />
        </>
    );
}
