/**
 * Auth Illustration Component
 * Renders SVG illustrations for authentication pages
 */

/**
 * Pre-defined illustration names for auth pages
 */
export const AuthIllustrations = {
    LOGIN: 'login',
    REGISTER: 'register',
    FORGOT_PASSWORD: 'forgot-password',
    RESET_PASSWORD: 'forgot-password', // Use same as forgot password
    VERIFY_EMAIL: 'verify-email',
};

/**
 * AuthIllustration - Simple wrapper for auth page illustrations
 *
 * @param {string} name - The illustration name (e.g., "login", "register")
 * @param {string} className - Additional CSS classes
 * @param {string} alt - Alt text for accessibility
 */
export function AuthIllustration({
    name,
    className = "",
    alt = "Illustration",
}) {
    // Map semantic names to file paths
    const illustrationPath = `/images/illustrations/${name}.svg`;

    return (
        <img
            src={illustrationPath}
            alt={alt}
            className={`w-full max-w-md ${className}`}
            loading="lazy"
        />
    );
}

export default AuthIllustration;
