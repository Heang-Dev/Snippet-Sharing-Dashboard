import { useEffect } from 'react';
import { Head, Link, useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Checkbox } from '@/Components/ui/checkbox';
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
import { toast } from 'sonner';

export default function Login({ status }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        login: 'admin@example.com',
        password: 'password',
        remember: true,
    });

    useEffect(() => {
        if (status) {
            toast.success(status);
        }
    }, [status]);

    const submit = (e) => {
        e.preventDefault();

        post('/login', {
            onFinish: () => reset('password'),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

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
                                Enter your credentials to access your account
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0">
                            <form onSubmit={submit} className="space-y-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="login">Email or Username</Label>
                                    <Input
                                        id="login"
                                        type="text"
                                        value={data.login}
                                        onChange={(e) => setData('login', e.target.value)}
                                        placeholder="name@example.com"
                                        autoComplete="username"
                                        autoFocus
                                        className={errors.login ? 'border-destructive' : ''}
                                    />
                                    {errors.login && (
                                        <p className="text-sm text-destructive">{errors.login}</p>
                                    )}
                                </div>

                                <div className="grid gap-2">
                                    <div className="flex items-center justify-between">
                                        <Label htmlFor="password">Password</Label>
                                        <Link
                                            href="/forgot-password"
                                            className="text-sm underline-offset-4 hover:underline"
                                        >
                                            Forgot your password?
                                        </Link>
                                    </div>
                                    <PasswordInput
                                        id="password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        placeholder="Enter your password"
                                        autoComplete="current-password"
                                        className={errors.password ? 'border-destructive' : ''}
                                    />
                                    {errors.password && (
                                        <p className="text-sm text-destructive">{errors.password}</p>
                                    )}
                                </div>

                                <div className="flex items-center space-x-2">
                                    <Checkbox
                                        id="remember"
                                        checked={data.remember}
                                        onCheckedChange={(checked) => setData('remember', checked)}
                                    />
                                    <Label htmlFor="remember" className="text-sm font-normal cursor-pointer">
                                        Remember me
                                    </Label>
                                </div>

                                <Button type="submit" className="w-full" disabled={processing}>
                                    {processing ? 'Signing in...' : 'Login'}
                                </Button>

                                <div className="text-center text-sm">
                                    Don&apos;t have an account?{" "}
                                    <Link href="/register" className="underline underline-offset-4">
                                        Sign up
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
                                Share your code snippets with the world. Collaborate, learn, and grow together.
                            </p>
                        </div>
                    </div>
                </div>
            </Card>
        </GuestLayout>
    );
}
