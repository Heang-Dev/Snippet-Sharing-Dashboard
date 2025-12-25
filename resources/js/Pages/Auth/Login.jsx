import { useEffect, useState } from "react";
import { Head, Link, router, usePage } from "@inertiajs/react";
import GuestLayout from "@/Layouts/GuestLayout";
import { Button } from "@/Components/ui/button";
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from "@/Components/ui/card";
import { Input } from "@/Components/ui/input";
import { PasswordInput } from "@/Components/PasswordInput";
import { Alert, AlertDescription, AlertTitle } from "@/Components/ui/alert";
import { ShieldAlert, Command } from "lucide-react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import * as z from "zod";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
    FormDescription,
} from "@/Components/ui/form";
import { detectEmailTypo } from "@/utils/emailValidation";
import { toast } from "sonner";
import { AuthThemeToggle } from "@/Components/auth-theme-toggle";
import { AuthIllustration, AuthIllustrations } from "@/Components/auth-illustration";

// Define the validation schema
const loginSchema = z.object({
    email: z.string().min(1, "Email is required").email("Please enter a valid email address"),
    password: z.string().min(1, "Password is required").min(8, "Password must be at least 8 characters"),
});

export default function Login({ status, canResetPassword = true }) {
    const { errors: serverErrors, flash } = usePage().props;
    const [emailSuggestion, setEmailSuggestion] = useState(null);
    const [rateLimited, setRateLimited] = useState(false);
    const [retryAfter, setRetryAfter] = useState(0);
    const [countdown, setCountdown] = useState(0);
    const [failedAttempts, setFailedAttempts] = useState(0);

    // Initialize react-hook-form with Zod validation
    const form = useForm({
        resolver: zodResolver(loginSchema),
        defaultValues: {
            email: "admin@example.com",
            password: "password",
        },
        mode: "onChange", // Real-time validation
    });

    const { isSubmitting, isValid } = form.formState;

    // Handle form submission
    const onSubmit = (data) => {
        // Don't submit if rate limited
        if (rateLimited) {
            return;
        }

        router.post("/login", data, {
            preserveScroll: true,
            onError: (errors) => {
                // Check for rate limit error in email field
                if (errors.email && (errors.email.includes('Too many') || errors.email.includes('seconds') || errors.email.includes('minute'))) {
                    const secondsMatch = errors.email.match(/(\d+)\s+second/);
                    const minutesMatch = errors.email.match(/(\d+)\s+minute/);

                    let seconds = 60;
                    if (secondsMatch) {
                        seconds = parseInt(secondsMatch[1], 10);
                    } else if (minutesMatch) {
                        seconds = parseInt(minutesMatch[1], 10) * 60;
                    }

                    handleRateLimitError(seconds);
                    setFailedAttempts(6);
                    return;
                }

                // Track failed attempts for credential errors
                if (errors.email && errors.email.includes('credentials')) {
                    setFailedAttempts(prev => {
                        const newCount = prev + 1;
                        sessionStorage.setItem('loginFailedAttempts', newCount.toString());
                        return newCount;
                    });
                }

                // Handle server-side validation errors
                if (errors.email) {
                    form.setError("email", { message: errors.email });
                }
                if (errors.password) {
                    form.setError("password", { message: errors.password });
                }
            },
            onSuccess: () => {
                // Reset failed attempts on successful login
                setFailedAttempts(0);
                sessionStorage.removeItem('loginFailedAttempts');
            },
        });
    };

    // Show toast for status messages
    useEffect(() => {
        if (status) {
            toast.success("Success!", {
                description: status,
            });
        }
    }, [status]);

    // Set server errors if they exist
    useEffect(() => {
        if (serverErrors?.email) {
            form.setError("email", { message: serverErrors.email });
        }
        if (serverErrors?.password) {
            form.setError("password", { message: serverErrors.password });
        }
    }, [serverErrors]);

    // Handle email blur to detect typos
    const handleEmailBlur = () => {
        const email = form.getValues('email');

        if (email && email.includes('@')) {
            const typoDetection = detectEmailTypo(email);

            if (typoDetection) {
                setEmailSuggestion(typoDetection);
            } else {
                setEmailSuggestion(null);
            }
        } else {
            setEmailSuggestion(null);
        }
    };

    // Handle suggestion click
    const handleSuggestionClick = () => {
        if (emailSuggestion) {
            form.setValue('email', emailSuggestion.suggestion, {
                shouldValidate: true,
                shouldDirty: true
            });
            setEmailSuggestion(null);
        }
    };

    // Handle rate limit error
    const handleRateLimitError = (seconds) => {
        setRateLimited(true);
        setRetryAfter(seconds);
        setCountdown(seconds);

        const expiry = Math.floor(Date.now() / 1000) + seconds;
        sessionStorage.setItem('rateLimitExpiry', expiry.toString());
    };

    // Check sessionStorage on mount
    useEffect(() => {
        const stored = sessionStorage.getItem('rateLimitExpiry');
        if (stored) {
            const expiry = parseInt(stored, 10);
            const now = Math.floor(Date.now() / 1000);

            if (expiry > now) {
                const remaining = expiry - now;
                setRateLimited(true);
                setRetryAfter(remaining);
                setCountdown(remaining);
                setFailedAttempts(6);
            } else {
                sessionStorage.removeItem('rateLimitExpiry');
            }
        }

        const storedAttempts = sessionStorage.getItem('loginFailedAttempts');
        if (storedAttempts) {
            setFailedAttempts(parseInt(storedAttempts, 10));
        }
    }, []);

    // Countdown timer effect
    useEffect(() => {
        if (countdown <= 0) {
            if (rateLimited) {
                setRateLimited(false);
                setFailedAttempts(0);
                sessionStorage.removeItem('rateLimitExpiry');
                sessionStorage.removeItem('loginFailedAttempts');
            }
            return;
        }

        const interval = setInterval(() => {
            setCountdown((prev) => {
                if (prev <= 1) {
                    setRateLimited(false);
                    setFailedAttempts(0);
                    sessionStorage.removeItem('rateLimitExpiry');
                    sessionStorage.removeItem('loginFailedAttempts');
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);

        return () => clearInterval(interval);
    }, [countdown, rateLimited]);

    // Handle social authentication
    const handleSocialAuth = (provider) => {
        window.location.href = `/auth/${provider}`;
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            {/* Warning Alert - After 3 failed attempts */}
            {!rateLimited && failedAttempts >= 3 && failedAttempts <= 5 && (
                <Alert variant="default" className="mb-4 border-yellow-500/50 bg-yellow-50 text-yellow-900 dark:border-yellow-500/50 dark:bg-yellow-950 dark:text-yellow-200">
                    <ShieldAlert className="h-5 w-5" />
                    <AlertTitle className="font-semibold">
                        {failedAttempts === 5 ? 'Final Warning: Last Attempt!' : `Warning: ${5 - failedAttempts} Attempts Remaining`}
                    </AlertTitle>
                    <AlertDescription>
                        <p className="text-sm">
                            {failedAttempts === 5 ? (
                                <>
                                    If you fail this attempt, your account will be <strong>temporarily locked for 60 seconds</strong>.
                                </>
                            ) : (
                                <>
                                    You have <strong>{5 - failedAttempts}</strong> more attempts before your account is temporarily locked for 60 seconds.
                                </>
                            )}
                        </p>
                        {canResetPassword && (
                            <p className="text-sm mt-2">
                                Having trouble? Try{' '}
                                <Link
                                    href="/forgot-password"
                                    className="underline hover:no-underline font-medium"
                                >
                                    resetting your password
                                </Link>
                                .
                            </p>
                        )}
                    </AlertDescription>
                </Alert>
            )}

            {/* Rate Limit Alert */}
            {rateLimited && (
                <Alert variant="default" className="mb-4 border-red-500/50 bg-red-50 text-red-900 dark:border-red-500/50 dark:bg-red-950 dark:text-red-200">
                    <ShieldAlert className="h-5 w-5" />
                    <AlertTitle className="text-lg font-semibold">
                        Too Many Login Attempts
                    </AlertTitle>
                    <AlertDescription className="space-y-3">
                        <p>
                            For security, we've temporarily blocked login attempts.{' '}
                            Please wait {countdown} seconds before trying again.
                        </p>

                        {/* Visual countdown progress bar */}
                        <div className="w-full bg-red-200 dark:bg-red-900 rounded-full h-2">
                            <div
                                className="bg-red-600 dark:bg-red-400 h-2 rounded-full transition-all duration-1000 ease-linear"
                                style={{ width: `${(countdown / retryAfter) * 100}%` }}
                                aria-hidden="true"
                            />
                        </div>

                        {/* Helpful tips */}
                        <div className="space-y-2 text-sm">
                            <p className="font-medium">While you wait:</p>
                            <ul className="list-disc list-inside space-y-1 opacity-90">
                                <li>Check if Caps Lock is on</li>
                                <li>Verify you're using the correct email</li>
                                {canResetPassword && (
                                    <li>
                                        <Link
                                            href="/forgot-password"
                                            className="underline hover:no-underline"
                                        >
                                            Reset your password
                                        </Link>
                                    </li>
                                )}
                            </ul>
                        </div>
                    </AlertDescription>
                </Alert>
            )}

            {/* Error Banner */}
            {flash?.error && (
                <Alert variant="destructive" className="mb-4">
                    <AlertDescription>
                        {flash.error}
                    </AlertDescription>
                </Alert>
            )}

            <Card className="overflow-hidden max-h-[85vh]">
                <div className="grid lg:grid-cols-2 h-full">
                    {/* Left Column - Login Form */}
                    <div className="p-6 md:p-8 overflow-y-auto">
                        <CardHeader className="px-0 pt-0">
                            <div className="flex justify-center mb-6">
                                <div className="flex aspect-square size-12 items-center justify-center rounded-lg overflow-hidden bg-primary">
                                    <Command className="size-6 text-primary-foreground" />
                                </div>
                            </div>
                            <CardTitle className="text-2xl font-bold text-center">
                                Welcome back
                            </CardTitle>
                            <CardDescription className="text-center">
                                Login to your account
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0">
                            {status && (
                                <div
                                    role="status"
                                    aria-live="polite"
                                    className="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400"
                                >
                                    {status}
                                </div>
                            )}

                            <Form {...form}>
                                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                                    <FormField
                                        control={form.control}
                                        name="email"
                                        render={({ field }) => (
                                            <FormItem>
                                                <FormLabel htmlFor="email">
                                                    Email <span className="text-destructive">*</span>
                                                </FormLabel>
                                                <FormControl>
                                                    <Input
                                                        {...field}
                                                        id="email"
                                                        type="email"
                                                        name="email"
                                                        placeholder="m@example.com"
                                                        autoComplete="email username"
                                                        inputMode="email"
                                                        spellCheck="false"
                                                        autoFocus
                                                        aria-invalid={!!form.formState.errors.email}
                                                        aria-describedby={form.formState.errors.email ? "email-error" : "email-description"}
                                                        onBlur={(e) => {
                                                            field.onBlur(e);
                                                            handleEmailBlur();
                                                        }}
                                                    />
                                                </FormControl>
                                                {!form.formState.errors.email && !emailSuggestion && (
                                                    <FormDescription id="email-description">
                                                        Your account email
                                                    </FormDescription>
                                                )}
                                                {emailSuggestion && (
                                                    <p className="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                                        Did you mean{' '}
                                                        <button
                                                            type="button"
                                                            onClick={handleSuggestionClick}
                                                            className="font-semibold underline hover:no-underline focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 rounded px-1"
                                                        >
                                                            {emailSuggestion.suggestion}
                                                        </button>
                                                        ?
                                                    </p>
                                                )}
                                                <FormMessage id="email-error" role="alert" aria-live="polite" />
                                            </FormItem>
                                        )}
                                    />

                                    <FormField
                                        control={form.control}
                                        name="password"
                                        render={({ field }) => (
                                            <FormItem>
                                                <div className="flex items-center justify-between">
                                                    <FormLabel htmlFor="password">
                                                        Password <span className="text-destructive">*</span>
                                                    </FormLabel>
                                                    {canResetPassword && (
                                                        <Link
                                                            href="/forgot-password"
                                                            className="text-sm underline-offset-4 hover:underline"
                                                        >
                                                            Forgot your password?
                                                        </Link>
                                                    )}
                                                </div>
                                                <FormControl>
                                                    <PasswordInput
                                                        id="password"
                                                        name="password"
                                                        placeholder="Enter your password"
                                                        autoComplete="current-password"
                                                        aria-invalid={!!form.formState.errors.password}
                                                        aria-describedby={form.formState.errors.password ? "password-error" : "password-description"}
                                                        {...field}
                                                    />
                                                </FormControl>
                                                {!form.formState.errors.password && (
                                                    <FormDescription id="password-description">
                                                        Min 8 characters
                                                    </FormDescription>
                                                )}
                                                <FormMessage id="password-error" role="alert" aria-live="polite" />
                                            </FormItem>
                                        )}
                                    />

                                    <Button
                                        type="submit"
                                        className="w-full"
                                        disabled={!isValid || isSubmitting || rateLimited}
                                        aria-busy={isSubmitting}
                                    >
                                        {rateLimited ? (
                                            `Please wait ${countdown}s...`
                                        ) : isSubmitting ? (
                                            <>
                                                <span className="sr-only">Logging you in, please wait</span>
                                                Logging in...
                                            </>
                                        ) : (
                                            "Login"
                                        )}
                                    </Button>

                                    {/* Social Auth */}
                                    <div className="relative text-center text-sm after:absolute after:inset-0 after:top-1/2 after:z-0 after:flex after:items-center after:border-t after:border-border">
                                        <span className="relative z-10 bg-background px-2 text-muted-foreground">
                                            Or continue with
                                        </span>
                                    </div>

                                    <div className="grid grid-cols-2 gap-4">
                                        <Button
                                            variant="outline"
                                            type="button"
                                            className="w-full"
                                            aria-label="Continue with Google"
                                            onClick={() => handleSocialAuth('google')}
                                        >
                                            <svg className="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                            </svg>
                                        </Button>
                                        <Button
                                            variant="outline"
                                            type="button"
                                            className="w-full"
                                            aria-label="Continue with GitHub"
                                            onClick={() => handleSocialAuth('github')}
                                        >
                                            <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path fillRule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clipRule="evenodd" />
                                            </svg>
                                        </Button>
                                    </div>

                                    <div className="text-center text-sm">
                                        Don't have an account?{" "}
                                        <Link href="/register" className="underline underline-offset-4">
                                            Sign up
                                        </Link>
                                    </div>
                                </form>
                            </Form>
                        </CardContent>
                    </div>

                    {/* Right Column - Illustration */}
                    <div className="relative hidden bg-muted lg:flex items-center justify-center p-8">
                        {/* Theme Toggle - Top Right */}
                        <div className="absolute top-4 right-4 z-10 flex items-center gap-1">
                            <AuthThemeToggle className="bg-background/80 backdrop-blur-sm hover:bg-background/90" />
                        </div>
                        <AuthIllustration
                            name={AuthIllustrations.LOGIN}
                            alt="Login illustration"
                            className="w-full max-w-md"
                        />
                    </div>
                </div>
            </Card>

        </GuestLayout>
    );
}
