import { useEffect } from 'react';
import { Head, router, usePage } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
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
} from '@/Components/ui/form';
import { KeyRound, Loader2 } from 'lucide-react';
import { toast } from 'sonner';

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
        .min(8, 'Password must be at least 8 characters')
        .regex(/[A-Z]/, 'Password must contain at least one uppercase letter')
        .regex(/[a-z]/, 'Password must contain at least one lowercase letter')
        .regex(/[0-9]/, 'Password must contain at least one number'),
    password_confirmation: z
        .string()
        .min(1, 'Please confirm your password'),
}).refine((data) => data.password === data.password_confirmation, {
    message: 'Passwords do not match',
    path: ['password_confirmation'],
});

export default function ResetPassword({ token, email }) {
    const { props } = usePage();
    const { errors: serverErrors } = props;

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

    const { isSubmitting, isValid } = form.formState;

    // Set server-side errors
    useEffect(() => {
        if (serverErrors?.email) {
            form.setError('email', { message: serverErrors.email });
        }
        if (serverErrors?.password) {
            form.setError('password', { message: serverErrors.password });
        }
    }, [serverErrors]);

    const onSubmit = (data) => {
        router.post('/reset-password', data, {
            onSuccess: () => {
                toast.success('Password reset successful!', {
                    description: 'Redirecting to login...',
                });
            },
            onError: (errors) => {
                if (errors.email) {
                    form.setError('email', { message: errors.email });
                    toast.error('Password reset failed', {
                        description: errors.email,
                    });
                }
                if (errors.password) {
                    form.setError('password', { message: errors.password });
                }
            },
            onFinish: () => {
                form.setValue('password', '');
                form.setValue('password_confirmation', '');
            },
        });
    };

    return (
        <GuestLayout>
            <Head title="Reset Password" />

            <Card className="overflow-hidden">
                <div className="grid lg:grid-cols-2">
                    {/* Left Column - Form */}
                    <div className="p-6 md:p-8">
                        <CardHeader className="px-0 pt-0">
                            <div className="flex justify-center mb-6">
                                <KeyRound className="h-12 w-12 text-primary" />
                            </div>
                            <CardTitle className="text-2xl font-bold text-center">
                                Reset Password
                            </CardTitle>
                            <CardDescription className="text-center">
                                Enter your new password below.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0">
                            <Form {...form}>
                                <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                                    <FormField
                                        control={form.control}
                                        name="email"
                                        render={({ field }) => (
                                            <FormItem>
                                                <FormLabel>Email</FormLabel>
                                                <FormControl>
                                                    <Input
                                                        type="email"
                                                        autoComplete="email"
                                                        readOnly
                                                        className="bg-muted"
                                                        {...field}
                                                    />
                                                </FormControl>
                                                <FormMessage />
                                            </FormItem>
                                        )}
                                    />

                                    <FormField
                                        control={form.control}
                                        name="password"
                                        render={({ field }) => (
                                            <FormItem>
                                                <FormLabel>New Password <span className="text-destructive">*</span></FormLabel>
                                                <FormControl>
                                                    <PasswordInput
                                                        placeholder="Enter new password"
                                                        autoComplete="new-password"
                                                        autoFocus
                                                        {...field}
                                                    />
                                                </FormControl>
                                                <FormMessage />
                                            </FormItem>
                                        )}
                                    />

                                    <FormField
                                        control={form.control}
                                        name="password_confirmation"
                                        render={({ field }) => (
                                            <FormItem>
                                                <FormLabel>Confirm Password <span className="text-destructive">*</span></FormLabel>
                                                <FormControl>
                                                    <PasswordInput
                                                        placeholder="Confirm new password"
                                                        autoComplete="new-password"
                                                        {...field}
                                                    />
                                                </FormControl>
                                                <FormMessage />
                                            </FormItem>
                                        )}
                                    />

                                    <Button type="submit" className="w-full" disabled={isSubmitting || !isValid}>
                                        {isSubmitting ? (
                                            <>
                                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                Resetting...
                                            </>
                                        ) : (
                                            'Reset Password'
                                        )}
                                    </Button>
                                </form>
                            </Form>
                        </CardContent>
                    </div>

                    {/* Right Column - Illustration */}
                    <div className="relative hidden bg-muted lg:flex items-center justify-center p-8">
                        <img
                            src="/images/illustrations/forgot-password.svg"
                            alt="Reset password illustration"
                            className="max-w-full max-h-80 object-contain"
                        />
                    </div>
                </div>
            </Card>
        </GuestLayout>
    );
}
