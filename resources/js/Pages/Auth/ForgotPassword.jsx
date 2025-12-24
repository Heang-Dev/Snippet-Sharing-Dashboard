import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Command, ArrowLeft } from 'lucide-react';
import { toast } from 'sonner';
import { useEffect } from 'react';

export default function ForgotPassword({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    useEffect(() => {
        if (status) {
            toast.success(status);
        }
    }, [status]);

    const submit = (e) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <GuestLayout>
            <Head title="Forgot Password" />

            <Card className="overflow-hidden max-h-[85vh]">
                <div className="grid lg:grid-cols-2 h-full">
                    {/* Left Column - Form */}
                    <div className="p-6 md:p-8 overflow-y-auto">
                        <CardHeader className="px-0 pt-0">
                            <div className="flex justify-center mb-6">
                                <div className="flex aspect-square size-12 items-center justify-center rounded-lg overflow-hidden bg-primary">
                                    <Command className="size-6 text-primary-foreground" />
                                </div>
                            </div>
                            <CardTitle className="text-2xl font-bold text-center">
                                Forgot Password
                            </CardTitle>
                            <CardDescription className="text-center">
                                Enter your email address and we'll send you a link to reset your password.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0">
                            {status && (
                                <div className="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400">
                                    {status}
                                </div>
                            )}

                            <form onSubmit={submit} className="space-y-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        placeholder="name@example.com"
                                        autoComplete="email"
                                        autoFocus
                                        className={errors.email ? 'border-destructive' : ''}
                                    />
                                    {errors.email && (
                                        <p className="text-sm text-destructive">{errors.email}</p>
                                    )}
                                </div>

                                <Button type="submit" className="w-full" disabled={processing}>
                                    {processing ? 'Sending...' : 'Send Reset Link'}
                                </Button>

                                <div className="text-center">
                                    <Link
                                        href="/login"
                                        className="inline-flex items-center text-sm text-muted-foreground hover:text-primary"
                                    >
                                        <ArrowLeft className="mr-2 h-4 w-4" />
                                        Back to login
                                    </Link>
                                </div>
                            </form>
                        </CardContent>
                    </div>

                    {/* Right Column - Illustration */}
                    <div className="relative hidden bg-muted lg:flex items-center justify-center p-8">
                        <div className="flex flex-col items-center justify-center text-center space-y-4">
                            <div className="flex aspect-square size-24 items-center justify-center rounded-2xl bg-primary/10">
                                <Command className="size-12 text-primary" />
                            </div>
                            <h3 className="text-2xl font-semibold">Snippet Share</h3>
                            <p className="text-muted-foreground max-w-xs">
                                Don't worry, it happens to the best of us. We'll help you get back in.
                            </p>
                        </div>
                    </div>
                </div>
            </Card>
        </GuestLayout>
    );
}
