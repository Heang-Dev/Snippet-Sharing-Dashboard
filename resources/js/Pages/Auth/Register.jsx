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
import { Alert, AlertDescription } from "@/Components/ui/alert";
import { Command } from "lucide-react";
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
import { PasswordStrength } from "@/Components/PasswordStrength";
import { detectEmailTypo } from "@/utils/emailValidation";
import { AuthThemeToggle } from "@/Components/auth-theme-toggle";
import { AuthIllustration, AuthIllustrations } from "@/Components/auth-illustration";

// Define the validation schema
const registerSchema = z.object({
    username: z
        .string()
        .min(1, "Username is required")
        .min(3, "Username must be at least 3 characters")
        .max(20, "Username must be at most 20 characters")
        .regex(/^[a-zA-Z0-9_]+$/, "Username can only contain letters, numbers, and underscores"),
    email: z
        .string()
        .min(1, "Email is required")
        .email("Please enter a valid email address"),
    password: z
        .string()
        .min(1, "Password is required")
        .min(8, "Password must be at least 8 characters"),
    password_confirmation: z
        .string()
        .min(1, "Password confirmation is required"),
}).refine((data) => data.password === data.password_confirmation, {
    message: "Passwords don't match",
    path: ["password_confirmation"],
});

export default function Register() {
    const { errors: serverErrors } = usePage().props;
    const [emailSuggestion, setEmailSuggestion] = useState(null);

    // Initialize react-hook-form with Zod validation
    const form = useForm({
        resolver: zodResolver(registerSchema),
        defaultValues: {
            username: "",
            email: "",
            password: "",
            password_confirmation: "",
        },
        mode: "onChange", // Real-time validation
    });

    const { isSubmitting, isValid } = form.formState;

    // Handle form submission
    const onSubmit = (data) => {
        router.post("/register", data, {
            preserveScroll: true,
            onError: (errors) => {
                // Set server validation errors
                Object.keys(errors).forEach((field) => {
                    form.setError(field, {
                        type: "server",
                        message: errors[field],
                    });
                });
            },
        });
    };

    // Set server errors if they exist
    useEffect(() => {
        if (serverErrors) {
            Object.keys(serverErrors).forEach((field) => {
                form.setError(field, {
                    type: "server",
                    message: serverErrors[field],
                });
            });
        }
    }, [serverErrors]);

    // Handle email blur to detect typos
    const handleEmailBlur = () => {
        const email = form.getValues('email');

        // Only check for typos if email has valid format (basic check)
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

    // Reset password fields when component unmounts
    useEffect(() => {
        return () => {
            form.setValue("password", "");
            form.setValue("password_confirmation", "");
        };
    }, []);

    // Handle social authentication with source tracking
    const handleSocialAuth = (provider) => {
        window.location.href = `/auth/${provider}?source=register`;
    };

    return (
        <GuestLayout>
            <Head title="Create an account" />

            {/* Error Banner (Outside Card) */}
            {serverErrors?.email && (
                <Alert variant="destructive" className="mb-4">
                    <AlertDescription>
                        {serverErrors.email}
                    </AlertDescription>
                </Alert>
            )}

            {/* Password Strength Indicator - Above Card */}
            <div className="mb-4">
                <PasswordStrength password={form.watch("password")} />
            </div>

            <Card className="overflow-hidden max-h-[85vh]">
                <div className="grid lg:grid-cols-2 h-full">
                    {/* Left Column - Illustration */}
                    <div className="relative hidden bg-muted lg:flex items-center justify-center p-8">
                        {/* Theme Toggle - Top Right */}
                        <div className="absolute top-4 right-4 z-10 flex items-center gap-1">
                            <AuthThemeToggle className="bg-background/80 backdrop-blur-sm hover:bg-background/90" />
                        </div>
                        <AuthIllustration
                            name={AuthIllustrations.REGISTER}
                            alt="Register illustration"
                            className="w-full max-w-md"
                        />
                    </div>

                    {/* Right Column - Register Form */}
                    <div className="p-6 md:p-8 overflow-y-auto">
                        <CardHeader className="px-0 pt-0">
                            <div className="flex justify-center mb-6">
                                <div className="flex aspect-square size-12 items-center justify-center rounded-lg overflow-hidden bg-primary">
                                    <Command className="size-6 text-primary-foreground" />
                                </div>
                            </div>
                            <CardTitle className="text-2xl font-bold text-center">
                                Create an account
                            </CardTitle>
                            <CardDescription className="text-center">
                                Welcome! Enter your information to get started.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0">
                            <Form {...form}>
                                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                                    {/* Username and Email in same row */}
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <FormField
                                            control={form.control}
                                            name="username"
                                            render={({ field }) => (
                                                <FormItem>
                                                    <FormLabel htmlFor="username">
                                                        Username <span className="text-destructive">*</span>
                                                    </FormLabel>
                                                    <FormControl>
                                                        <Input
                                                            id="username"
                                                            type="text"
                                                            name="username"
                                                            placeholder="johndoe"
                                                            autoComplete="username"
                                                            spellCheck="true"
                                                            autoFocus
                                                            aria-invalid={!!form.formState.errors.username}
                                                            aria-describedby={form.formState.errors.username ? "username-error" : "username-description"}
                                                            {...field}
                                                        />
                                                    </FormControl>
                                                    {!form.formState.errors.username && (
                                                        <FormDescription id="username-description">
                                                            3-20 characters, letters, numbers, underscores
                                                        </FormDescription>
                                                    )}
                                                    <FormMessage id="username-error" role="alert" aria-live="polite" />
                                                </FormItem>
                                            )}
                                        />

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
                                                            We'll send verification link here
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
                                    </div>

                                    {/* Password and Confirm Password in same row */}
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <FormField
                                            control={form.control}
                                            name="password"
                                            render={({ field }) => (
                                                <FormItem>
                                                    <FormLabel htmlFor="password">
                                                        Password <span className="text-destructive">*</span>
                                                    </FormLabel>
                                                    <FormControl>
                                                        <PasswordInput
                                                            id="password"
                                                            name="password"
                                                            placeholder="Create a password"
                                                            autoComplete="new-password"
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

                                        <FormField
                                            control={form.control}
                                            name="password_confirmation"
                                            render={({ field }) => (
                                                <FormItem>
                                                    <FormLabel htmlFor="password_confirmation">
                                                        Confirm Password <span className="text-destructive">*</span>
                                                    </FormLabel>
                                                    <FormControl>
                                                        <PasswordInput
                                                            id="password_confirmation"
                                                            name="password_confirmation"
                                                            placeholder="Confirm your password"
                                                            autoComplete="new-password"
                                                            aria-invalid={!!form.formState.errors.password_confirmation}
                                                            aria-describedby={form.formState.errors.password_confirmation ? "password-confirmation-error" : "password-confirmation-description"}
                                                            {...field}
                                                        />
                                                    </FormControl>
                                                    {!form.formState.errors.password_confirmation && (
                                                        <FormDescription id="password-confirmation-description">
                                                            Re-enter password
                                                        </FormDescription>
                                                    )}
                                                    <FormMessage id="password-confirmation-error" role="alert" aria-live="polite" />
                                                </FormItem>
                                            )}
                                        />
                                    </div>

                                    <Button
                                        type="submit"
                                        className="w-full"
                                        disabled={!isValid || isSubmitting}
                                        aria-busy={isSubmitting}
                                    >
                                        {isSubmitting ? (
                                            <>
                                                <span className="sr-only">Creating your account, please wait</span>
                                                Creating account...
                                            </>
                                        ) : (
                                            "Create an account"
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
                                        Already have an account?{" "}
                                        <Link href="/login" className="underline underline-offset-4">
                                            Sign in
                                        </Link>
                                    </div>
                                </form>
                            </Form>
                        </CardContent>
                    </div>
                </div>
            </Card>

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
        </GuestLayout>
    );
}
