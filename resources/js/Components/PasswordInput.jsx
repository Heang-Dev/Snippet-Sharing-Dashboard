import { useState, forwardRef } from "react";
import { Input } from "@/Components/ui/input";
import { Button } from "@/Components/ui/button";
import { Eye, EyeOff } from "lucide-react";
import { cn } from "@/lib/utils";

/**
 * Password Input Component with Show/Hide Toggle
 *
 * Features:
 * - Eye icon to toggle password visibility
 * - Maintains all Input component functionality
 * - Accessible with proper ARIA attributes
 * - Properly forwards refs for form libraries
 */
export const PasswordInput = forwardRef(({ className, ...props }, ref) => {
    const [showPassword, setShowPassword] = useState(false);

    const togglePasswordVisibility = () => {
        setShowPassword((prev) => !prev);
    };

    return (
        <div className="relative">
            <Input
                ref={ref}
                type={showPassword ? "text" : "password"}
                className={cn("pr-10", className)}
                {...props}
            />
            <Button
                type="button"
                variant="ghost"
                size="sm"
                className="absolute right-0 top-0 h-full px-3 py-2 hover:bg-transparent"
                onClick={togglePasswordVisibility}
                aria-label={showPassword ? "Hide password" : "Show password"}
                tabIndex={-1}
            >
                {showPassword ? (
                    <EyeOff className="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                ) : (
                    <Eye className="h-4 w-4 text-muted-foreground" aria-hidden="true" />
                )}
            </Button>
        </div>
    );
});

PasswordInput.displayName = "PasswordInput";
