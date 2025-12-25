import { useState, useEffect, useRef } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import GuestLayout from '@/Layouts/GuestLayout';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
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
import { ArrowLeft, Mail, Shield, Clock, RefreshCw, Loader2 } from 'lucide-react';
import { toast } from 'sonner';
import { detectEmailTypo } from '@/utils/emailValidation';
import { AuthThemeToggle } from '@/Components/auth-theme-toggle';
import { AuthIllustration, AuthIllustrations } from '@/Components/auth-illustration';

// Zod validation schema for email
const emailSchema = z.object({
    email: z
        .string()
        .min(1, 'Email is required')
        .email('Please enter a valid email address'),
});

export default function ForgotPassword() {
    const { props } = usePage();
    const { status, token, verified, reset_token, email: verifiedEmail, errors: serverErrors } = props;

    // State management
    const [step, setStep] = useState('request'); // 'request' | 'verify'
    const [emailSuggestion, setEmailSuggestion] = useState(null);
    const [sessionToken, setSessionToken] = useState('');
    const [submittedEmail, setSubmittedEmail] = useState('');
    const [otp, setOtp] = useState(['', '', '', '', '', '']);
    const [resendTimer, setResendTimer] = useState(0);
    const [isLoading, setIsLoading] = useState(false);
    const [isVerifying, setIsVerifying] = useState(false);
    const [isResending, setIsResending] = useState(false);

    // OTP input refs
    const otpInputs = useRef([]);

    // Email form
    const emailForm = useForm({
        resolver: zodResolver(emailSchema),
        defaultValues: {
            email: '',
        },
        mode: 'onChange',
    });

    // Check if email is valid
    const watchedEmail = emailForm.watch('email');
    const isEmailValid = watchedEmail &&
                        watchedEmail.includes('@') &&
                        watchedEmail.includes('.') &&
                        watchedEmail.length > 5;

    // Handle email blur to detect typos
    const handleEmailBlur = () => {
        const email = emailForm.getValues('email');
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
            emailForm.setValue('email', emailSuggestion.suggestion, {
                shouldValidate: true,
                shouldDirty: true
            });
            setEmailSuggestion(null);
        }
    };

    // Set server-side errors
    useEffect(() => {
        if (serverErrors?.email) {
            emailForm.setError('email', { message: serverErrors.email });
        }
    }, [serverErrors]);

    // Handle Step 1: Send OTP
    const onSubmitEmail = (data) => {
        setSubmittedEmail(data.email);
        setIsLoading(true);

        router.post('/forgot-password', data, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('OTP sent!', {
                    description: "We've sent a 6-digit code to your email.",
                });
            },
            onError: (errors) => {
                if (errors.email) {
                    emailForm.setError('email', { message: errors.email });
                    toast.error('Failed to send OTP', {
                        description: errors.email,
                    });
                }
                setIsLoading(false);
            },
            onFinish: () => {
                setIsLoading(false);
            },
        });
    };

    // Start resend timer
    const startResendTimer = () => {
        setResendTimer(60);
    };

    // Handle step transition when token is received
    useEffect(() => {
        if (token) {
            if (token !== sessionToken) {
                setSessionToken(token);

                if (step === 'request') {
                    setStep('verify');
                }

                startResendTimer();

                // Auto-focus first OTP input
                setTimeout(() => {
                    otpInputs.current[0]?.focus();
                }, 100);
            }
        }
    }, [token]);

    // Handle redirect to reset password page when OTP is verified
    useEffect(() => {
        if (verified && reset_token && verifiedEmail) {
            window.location.href = `/reset-password/${reset_token}?email=${encodeURIComponent(verifiedEmail)}`;
        }
    }, [verified, reset_token, verifiedEmail]);

    // Countdown timer effect
    useEffect(() => {
        if (resendTimer > 0) {
            const timer = setTimeout(() => setResendTimer(resendTimer - 1), 1000);
            return () => clearTimeout(timer);
        }
    }, [resendTimer]);

    // Handle OTP input
    const handleOtpInput = (index, event) => {
        const input = event.target;
        const value = input.value;

        if (value.length > 1) {
            // Handle paste
            const pasteData = value.slice(0, 6);
            const newOtp = [...otp];
            for (let i = 0; i < pasteData.length && i + index < 6; i++) {
                newOtp[index + i] = pasteData[i];
            }
            setOtp(newOtp);

            // Focus last filled input
            const lastIndex = Math.min(index + pasteData.length - 1, 5);
            otpInputs.current[lastIndex]?.focus();
        } else {
            // Single character input
            const newOtp = [...otp];
            newOtp[index] = value;
            setOtp(newOtp);

            if (value && index < 5) {
                otpInputs.current[index + 1]?.focus();
            }
        }

        // Auto-submit when all 6 digits are entered
        const updatedOtp = [...otp];
        updatedOtp[index] = value;
        if (updatedOtp.every(digit => digit !== '')) {
            setTimeout(() => handleVerifyOTP(updatedOtp.join('')), 100);
        }
    };

    // Handle OTP backspace
    const handleOtpKeydown = (index, event) => {
        if (event.key === 'Backspace' && !otp[index] && index > 0) {
            otpInputs.current[index - 1]?.focus();
        }
    };

    // Handle Step 2: Verify OTP
    const handleVerifyOTP = (otpString = null) => {
        const otpCode = otpString || otp.join('');

        if (otpCode.length !== 6) {
            return;
        }

        setIsVerifying(true);

        router.post('/verify-password-reset-otp', {
            email: submittedEmail,
            otp: otpCode,
            token: sessionToken
        }, {
            preserveScroll: true,
            onSuccess: () => {
                toast.success('OTP verified!', {
                    description: 'Redirecting to password reset...',
                });
            },
            onError: (errors) => {
                // Clear OTP on error
                setOtp(['', '', '', '', '', '']);
                otpInputs.current[0]?.focus();

                const errorMessage = errors.otp || 'Invalid OTP code';
                toast.error('Verification failed', {
                    description: errorMessage,
                });
                setIsVerifying(false);
            },
            onFinish: () => {
                setIsVerifying(false);
            },
        });
    };

    // Handle resend OTP
    const handleResendOTP = () => {
        if (resendTimer > 0) return;

        setIsResending(true);

        router.post('/resend-password-reset-otp', {
            token: sessionToken
        }, {
            preserveScroll: true,
            onSuccess: () => {
                setOtp(['', '', '', '', '', '']);
                otpInputs.current[0]?.focus();
                toast.success('New OTP sent!', {
                    description: 'Check your email for the new code.',
                });
            },
            onError: (errors) => {
                toast.error('Failed to resend OTP', {
                    description: errors.token || 'Please try again.',
                });
                setIsResending(false);
            },
            onFinish: () => {
                setIsResending(false);
            },
        });
    };

    return (
        <GuestLayout>
            <Head title="Forgot Password" />

            <Card className="overflow-hidden">
                <div className="grid lg:grid-cols-2">
                    {/* Left Column - Form */}
                    <div className="p-6 md:p-8">
                        <CardHeader className="px-0 pt-0">
                            <div className="flex justify-center mb-6">
                                {step === 'request' ? (
                                    <Mail className="h-12 w-12 text-primary" />
                                ) : (
                                    <Shield className="h-12 w-12 text-primary" />
                                )}
                            </div>
                            <CardTitle className="text-2xl font-bold text-center">
                                {step === 'request' ? 'Forgot Password?' : 'Enter Verification Code'}
                            </CardTitle>
                            <CardDescription className="text-center">
                                {step === 'request'
                                    ? "Enter your email address and we'll send you an OTP to reset your password"
                                    : `We've sent a 6-digit code to ${submittedEmail}`
                                }
                            </CardDescription>
                        </CardHeader>

                        <CardContent className="px-0 pb-0">
                            {/* Step 1: Request OTP */}
                            {step === 'request' && (
                                <Form {...emailForm}>
                                    <form onSubmit={emailForm.handleSubmit(onSubmitEmail)} className="space-y-4">
                                        <FormField
                                            control={emailForm.control}
                                            name="email"
                                            render={({ field }) => (
                                                <FormItem>
                                                    <FormLabel htmlFor="email">
                                                        Email Address <span className="text-destructive">*</span>
                                                    </FormLabel>
                                                    <FormControl>
                                                        <div className="relative">
                                                            <Mail className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
                                                            <Input
                                                                {...field}
                                                                id="email"
                                                                type="email"
                                                                placeholder="you@example.com"
                                                                autoComplete="email"
                                                                autoFocus
                                                                className="pl-10"
                                                                onBlur={handleEmailBlur}
                                                                aria-label="Email address"
                                                                aria-describedby="email-description"
                                                                aria-invalid={!!emailForm.formState.errors.email}
                                                                aria-required="true"
                                                            />
                                                        </div>
                                                    </FormControl>
                                                    <FormDescription id="email-description">
                                                        We'll send you a one-time password (OTP) via email
                                                    </FormDescription>
                                                    <FormMessage />

                                                    {/* Email Typo Suggestion */}
                                                    {emailSuggestion && (
                                                        <p className="text-sm text-blue-600 dark:text-blue-400 mt-1">
                                                            Did you mean{' '}
                                                            <button
                                                                type="button"
                                                                onClick={handleSuggestionClick}
                                                                className="font-semibold underline hover:no-underline"
                                                            >
                                                                {emailSuggestion.suggestion}
                                                            </button>
                                                            ?
                                                        </p>
                                                    )}
                                                </FormItem>
                                            )}
                                        />

                                        <Button
                                            type="submit"
                                            className="w-full"
                                            disabled={isLoading || !isEmailValid}
                                            aria-label="Send OTP"
                                        >
                                            {isLoading ? (
                                                <>
                                                    <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                    Sending OTP...
                                                </>
                                            ) : (
                                                'Send OTP'
                                            )}
                                        </Button>

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
                            )}

                            {/* Step 2: Verify OTP */}
                            {step === 'verify' && (
                                <div className="space-y-4">
                                    {/* OTP Input */}
                                    <div className="space-y-2">
                                        <label className="text-sm font-medium text-center block">
                                            Verification Code
                                        </label>
                                        <div className="flex justify-center gap-2">
                                            {otp.map((digit, index) => (
                                                <input
                                                    key={index}
                                                    ref={el => otpInputs.current[index] = el}
                                                    type="text"
                                                    inputMode="numeric"
                                                    maxLength="1"
                                                    pattern="[0-9]"
                                                    value={digit}
                                                    className="w-12 h-14 text-center text-2xl font-semibold border rounded-md focus:ring-2 focus:ring-primary focus:border-primary transition-all bg-background"
                                                    onChange={(e) => handleOtpInput(index, e)}
                                                    onKeyDown={(e) => handleOtpKeydown(index, e)}
                                                    aria-label={`Digit ${index + 1}`}
                                                />
                                            ))}
                                        </div>
                                        <p className="text-xs text-center text-muted-foreground mt-2">
                                            <Clock className="inline h-3 w-3 mr-1" />
                                            Code expires in 10 minutes
                                        </p>
                                    </div>

                                    {/* Verify Button */}
                                    <Button
                                        type="button"
                                        onClick={() => handleVerifyOTP()}
                                        className="w-full"
                                        disabled={otp.some(d => !d) || isVerifying}
                                        aria-label="Verify code"
                                    >
                                        {isVerifying ? (
                                            <>
                                                <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                                                Verifying...
                                            </>
                                        ) : (
                                            'Verify Code'
                                        )}
                                    </Button>

                                    {/* Resend code */}
                                    <div className="text-center">
                                        <span className="text-sm text-muted-foreground">Didn't receive the code? </span>
                                        <button
                                            type="button"
                                            onClick={handleResendOTP}
                                            disabled={resendTimer > 0 || isResending}
                                            className="text-sm text-primary hover:underline disabled:text-muted-foreground disabled:no-underline inline-flex items-center gap-1"
                                        >
                                            {isResending ? (
                                                <>
                                                    <Loader2 className="h-3 w-3 animate-spin" />
                                                    Sending...
                                                </>
                                            ) : (
                                                <>
                                                    <RefreshCw className="h-3 w-3" />
                                                    {resendTimer > 0 ? `Resend in ${resendTimer}s` : 'Resend code'}
                                                </>
                                            )}
                                        </button>
                                    </div>

                                    {/* Back to email step */}
                                    <div className="text-center pt-4 border-t">
                                        <button
                                            type="button"
                                            onClick={() => {
                                                setStep('request');
                                                setOtp(['', '', '', '', '', '']);
                                                setResendTimer(0);
                                            }}
                                            className="text-sm text-muted-foreground hover:text-primary transition-colors inline-flex items-center gap-1"
                                        >
                                            <ArrowLeft className="h-3 w-3" />
                                            Use different email
                                        </button>
                                    </div>
                                </div>
                            )}
                        </CardContent>
                    </div>

                    {/* Right Column - Illustration */}
                    <div className="relative hidden bg-muted lg:flex flex-col items-center justify-center p-8">
                        {/* Theme Toggle */}
                        <div className="absolute top-4 right-4">
                            <AuthThemeToggle />
                        </div>

                        <AuthIllustration
                            name={AuthIllustrations.FORGOT_PASSWORD}
                            alt="Forgot password illustration"
                            className="max-h-80"
                        />
                    </div>
                </div>
            </Card>

            {/* Additional Help Text */}
            <div className="mt-4 text-center text-sm text-muted-foreground">
                <p>
                    Need help? Contact support at{' '}
                    <a
                        href="mailto:support@example.com"
                        className="underline hover:text-primary transition-colors"
                    >
                        support@example.com
                    </a>
                </p>
            </div>
        </GuestLayout>
    );
}
