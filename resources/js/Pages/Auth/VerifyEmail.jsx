import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Alert, AlertDescription } from '@/Components/ui/alert';
import { Mail, ShieldCheck, LogOut, Loader2 } from 'lucide-react';
import { AuthThemeToggle } from '@/Components/auth-theme-toggle';
import { AuthIllustration, AuthIllustrations } from '@/Components/auth-illustration';

export default function VerifyEmail({ status, pendingSocialLink }) {
    const { post, processing } = useForm({});

    const submit = (e) => {
        e.preventDefault();
        post('/email/verification-notification');
    };

    return (
        <GuestLayout>
            <Head title="Email Verification" />

            {/* Pending Social Link Info Banner */}
            {pendingSocialLink && (
                <Alert className="mb-4 border-blue-200 bg-blue-50 dark:border-blue-800 dark:bg-blue-950">
                    <ShieldCheck className="h-4 w-4 text-blue-600 dark:text-blue-400" />
                    <AlertDescription className="text-blue-800 dark:text-blue-200">
                        After verifying your email, we'll link your <strong>{pendingSocialLink.provider}</strong> account ({pendingSocialLink.email}) to your account.
                    </AlertDescription>
                </Alert>
            )}

            <Card className="overflow-hidden">
                <div className="grid lg:grid-cols-2">
                    {/* Left Column - Content */}
                    <div className="p-6 md:p-8">
                        <CardHeader className="px-0 pt-0">
                            <div className="flex justify-center mb-6">
                                <div className="flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                                    <Mail className="h-6 w-6 text-primary" />
                                </div>
                            </div>
                            <CardTitle className="text-2xl font-bold text-center">
                                Verify Your Email
                            </CardTitle>
                            <CardDescription className="text-center">
                                Thanks for signing up! Before getting started, please verify your email address.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0 space-y-4">
                            <p className="text-sm text-muted-foreground text-center">
                                We've sent a verification link to your email address. Click the link to verify your account.
                            </p>

                            {status === 'verification-link-sent' && (
                                <Alert className="border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950">
                                    <AlertDescription className="text-green-800 dark:text-green-200">
                                        A new verification link has been sent to your email address.
                                    </AlertDescription>
                                </Alert>
                            )}

                            <form onSubmit={submit} className="space-y-4">
                                <Button
                                    type="submit"
                                    className="w-full"
                                    disabled={processing}
                                    aria-label="Resend verification email"
                                >
                                    {processing ? (
                                        <>
                                            <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                            Sending...
                                        </>
                                    ) : (
                                        'Resend Verification Email'
                                    )}
                                </Button>

                                <div className="text-center">
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors"
                                    >
                                        <LogOut className="h-4 w-4" />
                                        Log Out
                                    </Link>
                                </div>
                            </form>
                        </CardContent>
                    </div>

                    {/* Right Column - Illustration */}
                    <div className="relative hidden bg-muted lg:flex flex-col items-center justify-center p-8">
                        {/* Theme Toggle */}
                        <div className="absolute top-4 right-4">
                            <AuthThemeToggle />
                        </div>

                        <AuthIllustration
                            name={AuthIllustrations.VERIFY_EMAIL}
                            alt="Verify email illustration"
                            className="max-h-80"
                        />
                    </div>
                </div>
            </Card>

            {/* Additional Help Text */}
            <div className="mt-4 text-center text-sm text-muted-foreground">
                <p>
                    Didn't receive the email? Check your spam folder or{' '}
                    <a
                        href="mailto:support@example.com"
                        className="underline hover:text-primary transition-colors"
                    >
                        contact support
                    </a>
                </p>
            </div>
        </GuestLayout>
    );
}
