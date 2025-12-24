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
import { Command, Mail } from 'lucide-react';

export default function VerifyEmail({ status }) {
    const { post, processing } = useForm({});

    const submit = (e) => {
        e.preventDefault();
        post('/email/verification-notification');
    };

    return (
        <GuestLayout>
            <Head title="Verify Email" />

            <Card className="overflow-hidden max-h-[85vh]">
                <div className="grid lg:grid-cols-2 h-full">
                    {/* Left Column - Content */}
                    <div className="p-6 md:p-8 overflow-y-auto">
                        <CardHeader className="px-0 pt-0">
                            <div className="flex justify-center mb-6">
                                <div className="flex aspect-square size-12 items-center justify-center rounded-lg overflow-hidden bg-primary">
                                    <Mail className="size-6 text-primary-foreground" />
                                </div>
                            </div>
                            <CardTitle className="text-2xl font-bold text-center">
                                Verify Your Email
                            </CardTitle>
                            <CardDescription className="text-center">
                                Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just emailed to you.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0 space-y-4">
                            {status === 'verification-link-sent' && (
                                <div className="rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400">
                                    A new verification link has been sent to your email address.
                                </div>
                            )}

                            <form onSubmit={submit} className="space-y-4">
                                <Button type="submit" className="w-full" disabled={processing}>
                                    {processing ? 'Sending...' : 'Resend Verification Email'}
                                </Button>
                            </form>

                            <form method="POST" action="/logout" className="text-center">
                                <Link
                                    href="/logout"
                                    method="post"
                                    as="button"
                                    className="text-sm text-muted-foreground underline underline-offset-4 hover:text-primary"
                                >
                                    Log Out
                                </Link>
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
                                Check your inbox and click the verification link to get started.
                            </p>
                        </div>
                    </div>
                </div>
            </Card>
        </GuestLayout>
    );
}
