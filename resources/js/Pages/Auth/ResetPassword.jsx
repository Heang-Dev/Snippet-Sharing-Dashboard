import { Head, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { PasswordInput } from '@/Components/PasswordInput';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card';
import { Command } from 'lucide-react';

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        token: token,
        email: email,
        password: '',
        password_confirmation: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/reset-password', {
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Reset Password" />

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
                                Reset Password
                            </CardTitle>
                            <CardDescription className="text-center">
                                Enter your new password below.
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0">
                            <form onSubmit={submit} className="space-y-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="email">Email</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        autoComplete="email"
                                        className={errors.email ? 'border-destructive' : ''}
                                    />
                                    {errors.email && (
                                        <p className="text-sm text-destructive">{errors.email}</p>
                                    )}
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="password">New Password</Label>
                                    <PasswordInput
                                        id="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Enter new password"
                                        autoComplete="new-password"
                                        autoFocus
                                        className={errors.password ? 'border-destructive' : ''}
                                    />
                                    {errors.password && (
                                        <p className="text-sm text-destructive">{errors.password}</p>
                                    )}
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="password_confirmation">Confirm Password</Label>
                                    <PasswordInput
                                        id="password_confirmation"
                                        value={data.password_confirmation}
                                        onChange={(e) => setData('password_confirmation', e.target.value)}
                                        placeholder="Confirm new password"
                                        autoComplete="new-password"
                                        className={errors.password_confirmation ? 'border-destructive' : ''}
                                    />
                                    {errors.password_confirmation && (
                                        <p className="text-sm text-destructive">{errors.password_confirmation}</p>
                                    )}
                                </div>

                                <Button type="submit" className="w-full" disabled={processing}>
                                    {processing ? 'Resetting...' : 'Reset Password'}
                                </Button>
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
                                Create a strong password to keep your account secure.
                            </p>
                        </div>
                    </div>
                </div>
            </Card>
        </GuestLayout>
    );
}
