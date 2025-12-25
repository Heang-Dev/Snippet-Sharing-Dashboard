import { useState, useEffect } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { PasswordInput } from '@/Components/PasswordInput';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
    FormDescription,
} from '@/Components/ui/form';
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert';
import { KeyRound, Loader2, Mail, XCircle, ArrowLeft } from 'lucide-react';
import { toast } from 'sonner';
import { PasswordStrength } from '@/Components/PasswordStrength';
import { AuthThemeToggle } from '@/Components/auth-theme-toggle';
import { AuthIllustration, AuthIllustrations } from '@/Components/auth-illustration';

// Zod validation schema
const resetPasswordSchema = z.object({
    token: z.string(),
    email: z
        .string()
        .min(1, 'Email is required')
        .email('Please enter a valid email address'),
    password: z
        .string()
        .min(1, 'Password is required')
        .min(8, 'Password must be at least 8 characters'),
    password_confirmation: z
        .string()
        .min(1, 'Please confirm your password'),
}).refine((data) => data.password === data.password_confirmation, {
    message: "Passwords don't match",
    path: ['password_confirmation'],
});

export default function ResetPassword({ token, email }) {
    const { props } = usePage();
    const { errors: serverErrors } = props;
    const [isLoading, setIsLoading] = useState(false);

    const form = useForm({
        resolver: zodResolver(resetPasswordSchema),
        defaultValues: {
            token: token || '',
            email: email || '',
            password: '',
            password_confirmation: '',
        },
        mode: 'onChange',
    });

    // Watch password field
    const password = form.watch('password');

    // Set server-side errors
    useEffect(() => {
        if (serverErrors?.email) {
            form.setError('email', { message: serverErrors.email });
        }
        if (serverErrors?.password) {
            form.setError('password', { message: serverErrors.password });
        }
        if (serverErrors?.password_confirmation) {
            form.setError('password_confirmation', { message: serverErrors.password_confirmation });
        }
    }, [serverErrors]);

    const onSubmit = (data) => {
        setIsLoading(true);

        router.post('/reset-password', data, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('Password reset successfully!', {
                    description: 'You can now login with your new password.',
                });
                setIsLoading(false);
            },
            onError: (errors) => {
                if (errors.email) {
                    form.setError('email', { message: errors.email });
                }
                if (errors.password) {
                    form.setError('password', { message: errors.password });
                }
                if (errors.password_confirmation) {
                    form.setError('password_confirmation', { message: errors.password_confirmation });
                }

                toast.error('Failed to reset password', {
                    description: errors.email || errors.password || 'Please check your inputs and try again.',
                });
                setIsLoading(false);
            },
        });
    };

    return (
        <GuestLayout>
            <Head title="Reset Password" />

            {/* Token Invalid Alert */}
            {serverErrors?.token && (
                <Alert variant="default" className="mb-4 border-red-500/50 bg-red-50 text-red-900 dark:border-red-500/50 dark:bg-red-950 dark:text-red-200">
                    <XCircle className="h-5 w-5" />
                    <AlertTitle className="font-semibold">
                        Invalid or Expired Token
                    </AlertTitle>
                    <AlertDescription>
                        <p className="text-sm">
                            This password reset token is invalid or has expired. Please request a new OTP.
                        </p>
                        <Link
                            href="/forgot-password"
                            className="mt-2 inline-block underline hover:no-underline font-medium"
                        >
                            Request New OTP
                        </Link>
                    </AlertDescription>
                </Alert>
            )}

            {/* Email Banner */}
            {email && !serverErrors?.token && (
                <Alert className="mb-4 border-blue-500/50 bg-blue-50 text-blue-900 dark:border-blue-500/50 dark:bg-blue-950 dark:text-blue-200">
                    <Mail className="h-5 w-5" />
                    <AlertTitle className="font-semibold">
                        Resetting password for
                    </AlertTitle>
                    <AlertDescription>
                        <p className="text-sm font-medium">{email}</p>
                    </AlertDescription>
                </Alert>
            )}

            {/* Password Strength Indicator - Outside card */}
            <PasswordStrength password={password} />

            <Card className="overflow-hidden mt-4">
                <div className="grid lg:grid-cols-2">
                    {/* Left Column - Form */}
                    <div className="p-6 md:p-8">
                        <CardHeader className="px-0 pt-0">
                            <div className="flex justify-center mb-6">
                                <KeyRound className="h-12 w-12 text-primary" />
                            </div>
                            <CardTitle className="text-2xl font-bold text-center">
                                Reset Your Password
                            </CardTitle>
                            <CardDescription className="text-center">
                                Enter your new password below
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0">
                            <Form {...form}>
                                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                                    <FormField
                                        control={form.control}
                                        name="password"
                                        render={({ field }) => (
                                            <FormItem>
                                                <FormLabel htmlFor="password">
                                                    New Password <span className="text-destructive">*</span>
                                                </FormLabel>
                                                <FormControl>
                                                    <PasswordInput
                                                        {...field}
                                                        id="password"
                                                        placeholder="Create a strong password"
                                                        autoComplete="new-password"
                                                        autoFocus
                                                        aria-label="New password"
                                                        aria-describedby="password-description"
                                                        aria-invalid={!!form.formState.errors.password}
                                                        aria-required="true"
                                                    />
                                                </FormControl>
                                                {!form.formState.errors.password && (
                                                    <FormDescription id="password-description">
                                                        Choose a strong password with at least 8 characters
                                                    </FormDescription>
                                                )}
                                                <FormMessage />
                                            </FormItem>
                                        )}
                                    />

                                    <FormField
                                        control={form.control}
                                        name="password_confirmation"
                                        render={({ field }) => (
                                            <FormItem>
                                                <FormLabel htmlFor="password_confirmation">
                                                    Confirm New Password <span className="text-destructive">*</span>
                                                </FormLabel>
                                                <FormControl>
                                                    <PasswordInput
                                                        {...field}
                                                        id="password_confirmation"
                                                        placeholder="Confirm your password"
                                                        autoComplete="new-password"
                                                        aria-label="Confirm new password"
                                                        aria-describedby="password-confirmation-description"
                                                        aria-invalid={!!form.formState.errors.password_confirmation}
                                                        aria-required="true"
                                                    />
                                                </FormControl>
                                                {!form.formState.errors.password_confirmation && (
                                                    <FormDescription id="password-confirmation-description">
                                                        Re-enter your password to confirm
                                                    </FormDescription>
                                                )}
                                                <FormMessage />
                                            </FormItem>
                                        )}
                                    />

                                    <Button
                                        type="submit"
                                        className="w-full"
                                        disabled={isLoading || !form.formState.isValid}
                                        aria-label="Reset password"
                                    >
                                        {isLoading ? (
                                            <>
                                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                Resetting Password...
                                            </>
                                        ) : (
                                            'Reset Password'
                                        )}
                                    </Button>

                                    {/* Back to Login Link */}
                                    <div className="text-center">
                                        <Link
                                            href="/login"
                                            className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors"
                                        >
                                            <ArrowLeft className="h-4 w-4" />
                                            Back to Login
                                        </Link>
                                    </div>
                                </form>
                            </Form>
                        </CardContent>
                    </div>

                    {/* Right Column - Illustration */}
                    <div className="relative hidden bg-muted lg:flex flex-col items-center justify-center p-8">
                        {/* Theme Toggle */}
                        <div className="absolute top-4 right-4">
                            <AuthThemeToggle />
                        </div>

                        <AuthIllustration
                            name={AuthIllustrations.RESET_PASSWORD}
                            alt="Reset password illustration"
                            className="max-h-80"
                        />
                    </div>
                </div>
            </Card>
        </GuestLayout>
    );
}
