import { useEffect, useState } from "react";
import { cn } from "@/lib/utils";
import { Alert, AlertDescription } from "@/Components/ui/alert";

/**
 * Password Strength Indicator Component - Alert Banner Style
 *
 * Calculates password strength based on:
 * - Length (8+ chars = 1 point, 12+ = 2 points)
 * - Uppercase letters (1 point)
 * - Lowercase letters (1 point)
 * - Numbers (1 point)
 * - Special characters (1 point)
 *
 * Strength levels:
 * - 0-1: Very Weak (red)
 * - 2: Weak (orange)
 * - 3: Fair (yellow)
 * - 4: Good (lime)
 * - 5-6: Strong (green)
 */
export function PasswordStrength({ password }) {
    const [strength, setStrength] = useState(0);
    const [label, setLabel] = useState("");

    useEffect(() => {
        if (!password) {
            setStrength(0);
            setLabel("");
            return;
        }

        let score = 0;

        // Length scoring
        if (password.length >= 8) score += 1;
        if (password.length >= 12) score += 1;

        // Character variety scoring
        if (/[a-z]/.test(password)) score += 1; // lowercase
        if (/[A-Z]/.test(password)) score += 1; // uppercase
        if (/[0-9]/.test(password)) score += 1; // numbers
        if (/[^a-zA-Z0-9]/.test(password)) score += 1; // special chars

        setStrength(score);

        // Set label based on score
        if (score <= 1) {
            setLabel("Very Weak");
        } else if (score === 2) {
            setLabel("Weak");
        } else if (score === 3) {
            setLabel("Fair");
        } else if (score === 4) {
            setLabel("Good");
        } else {
            setLabel("Strong");
        }
    }, [password]);

    if (!password) return null;

    const percentage = (strength / 6) * 100;

    return (
        <Alert
            className={cn("transition-all duration-300", {
                "border-red-500/50 bg-red-50 text-red-900 dark:border-red-500/50 dark:bg-red-950 dark:text-red-200": strength <= 1,
                "border-orange-500/50 bg-orange-50 text-orange-900 dark:border-orange-500/50 dark:bg-orange-950 dark:text-orange-200": strength === 2,
                "border-yellow-500/50 bg-yellow-50 text-yellow-900 dark:border-yellow-500/50 dark:bg-yellow-950 dark:text-yellow-200": strength === 3,
                "border-lime-500/50 bg-lime-50 text-lime-900 dark:border-lime-500/50 dark:bg-lime-950 dark:text-lime-200": strength === 4,
                "border-green-500/50 bg-green-50 text-green-900 dark:border-green-500/50 dark:bg-green-950 dark:text-green-200": strength >= 5,
            })}
            role="status"
            aria-live="polite"
        >
            <AlertDescription>
                <div className="space-y-3">
                    {/* Strength header with progress bar */}
                    <div className="flex items-center justify-between">
                        <span className="font-semibold text-sm">
                            Password strength: {label}
                        </span>
                        <span className="text-xs opacity-80">
                            {strength}/6 criteria met
                        </span>
                    </div>

                    {/* Progress bar */}
                    <div className="h-2 w-full overflow-hidden rounded-full bg-black/10 dark:bg-white/10">
                        <div
                            className={cn(
                                "h-full transition-all duration-300 ease-in-out",
                                {
                                    "bg-red-600": strength <= 1,
                                    "bg-orange-600": strength === 2,
                                    "bg-yellow-600": strength === 3,
                                    "bg-lime-600": strength === 4,
                                    "bg-green-600": strength >= 5,
                                }
                            )}
                            style={{ width: `${percentage}%` }}
                            aria-hidden="true"
                        />
                    </div>

                    {/* Requirements checklist */}
                    <div className="grid grid-cols-1 gap-1.5 text-xs">
                        <div className="flex items-center gap-2">
                            <CheckIcon checked={password.length >= 8} />
                            <span>At least 8 characters</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckIcon checked={password.length >= 12} />
                            <span>At least 12 characters (bonus)</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckIcon checked={/[a-z]/.test(password)} />
                            <span>Contains lowercase letter</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckIcon checked={/[A-Z]/.test(password)} />
                            <span>Contains uppercase letter</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckIcon checked={/[0-9]/.test(password)} />
                            <span>Contains number</span>
                        </div>
                        <div className="flex items-center gap-2">
                            <CheckIcon checked={/[^a-zA-Z0-9]/.test(password)} />
                            <span>Contains special character</span>
                        </div>
                    </div>
                </div>
            </AlertDescription>
        </Alert>
    );
}

function CheckIcon({ checked }) {
    if (checked) {
        return (
            <svg
                className="h-4 w-4 text-green-600 dark:text-green-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                aria-hidden="true"
            >
                <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
        );
    }

    return (
        <svg
            className="h-4 w-4 text-muted-foreground"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            aria-hidden="true"
        >
            <circle cx="12" cy="12" r="9" strokeWidth={2} />
        </svg>
    );
}
