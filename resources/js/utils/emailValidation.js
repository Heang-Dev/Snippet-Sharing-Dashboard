/**
 * Email Validation and Typo Detection Utility
 * Detects common email domain typos and suggests corrections
 */

// Common email domains that users typically use
const COMMON_DOMAINS = [
    'gmail.com',
    'yahoo.com',
    'hotmail.com',
    'outlook.com',
    'icloud.com',
    'aol.com',
    'protonmail.com',
    'zoho.com',
    'live.com',
    'msn.com',
    'yandex.com',
    'mail.com',
];

// Direct mapping of common typos to correct domains
const COMMON_TYPOS = {
    // Gmail typos
    'gmial.com': 'gmail.com',
    'gmai.com': 'gmail.com',
    'gmil.com': 'gmail.com',
    'gmaill.com': 'gmail.com',
    'gmali.com': 'gmail.com',
    'gmal.com': 'gmail.com',
    'gnail.com': 'gmail.com',
    'gmaiil.com': 'gmail.com',

    // Yahoo typos
    'yahooo.com': 'yahoo.com',
    'yaho.com': 'yahoo.com',
    'yahho.com': 'yahoo.com',
    'yahou.com': 'yahoo.com',
    'yaboo.com': 'yahoo.com',

    // Hotmail typos
    'hotmial.com': 'hotmail.com',
    'hotmil.com': 'hotmail.com',
    'hotmall.com': 'hotmail.com',
    'hotmai.com': 'hotmail.com',
    'hotmaill.com': 'hotmail.com',

    // Outlook typos
    'outlok.com': 'outlook.com',
    'outloo.com': 'outlook.com',
    'outlookk.com': 'outlook.com',
    'outloook.com': 'outlook.com',

    // iCloud typos
    'iclou.com': 'icloud.com',
    'icloude.com': 'icloud.com',
    'iclod.com': 'icloud.com',
};

/**
 * Calculate Levenshtein distance between two strings
 * Measures how many single-character edits are needed to change one word into another
 */
function levenshteinDistance(str1, str2) {
    const matrix = [];

    // Initialize first column
    for (let i = 0; i <= str2.length; i++) {
        matrix[i] = [i];
    }

    // Initialize first row
    for (let j = 0; j <= str1.length; j++) {
        matrix[0][j] = j;
    }

    // Fill in the rest of the matrix
    for (let i = 1; i <= str2.length; i++) {
        for (let j = 1; j <= str1.length; j++) {
            if (str2.charAt(i - 1) === str1.charAt(j - 1)) {
                matrix[i][j] = matrix[i - 1][j - 1];
            } else {
                matrix[i][j] = Math.min(
                    matrix[i - 1][j - 1] + 1, // substitution
                    matrix[i][j - 1] + 1,     // insertion
                    matrix[i - 1][j] + 1      // deletion
                );
            }
        }
    }

    return matrix[str2.length][str1.length];
}

/**
 * Validate basic email format
 */
export function isValidEmailFormat(email) {
    if (!email) return false;

    // Basic email regex
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

/**
 * Comprehensive email validation
 */
export function validateEmail(email) {
    if (!email) {
        return { valid: false, error: 'Email is required' };
    }

    if (!isValidEmailFormat(email)) {
        return { valid: false, error: 'Please enter a valid email address' };
    }

    // Check for consecutive dots
    if (email.includes('..')) {
        return { valid: false, error: 'Email contains consecutive dots' };
    }

    // Check if ends with dot
    if (email.endsWith('.')) {
        return { valid: false, error: 'Email cannot end with a dot' };
    }

    // Check if local part is empty
    const [localPart] = email.split('@');
    if (!localPart || localPart.length === 0) {
        return { valid: false, error: 'Email local part cannot be empty' };
    }

    return { valid: true };
}

/**
 * Detect potential email typos and suggest corrections
 * Returns suggested email or null if no typo detected
 */
export function detectEmailTypo(email) {
    if (!email || !email.includes('@')) return null;

    const [localPart, domain] = email.split('@');

    // Don't suggest if domain is missing or empty
    if (!domain || domain.length === 0) return null;

    // Lowercase domain for comparison
    const lowerDomain = domain.toLowerCase();

    // Check direct typo mapping first
    if (COMMON_TYPOS[lowerDomain]) {
        return {
            suggestion: `${localPart}@${COMMON_TYPOS[lowerDomain]}`,
            original: email,
            confidence: 'high'
        };
    }

    // Don't suggest if domain is already in common domains list (it's correct)
    if (COMMON_DOMAINS.includes(lowerDomain)) {
        return null;
    }

    // Check Levenshtein distance for similar domains
    let bestMatch = null;
    let bestDistance = Infinity;

    for (const correctDomain of COMMON_DOMAINS) {
        const distance = levenshteinDistance(lowerDomain, correctDomain);

        // If only 1-2 characters different, suggest correction
        if (distance > 0 && distance <= 2 && distance < bestDistance) {
            bestDistance = distance;
            bestMatch = correctDomain;
        }
    }

    if (bestMatch) {
        return {
            suggestion: `${localPart}@${bestMatch}`,
            original: email,
            confidence: bestDistance === 1 ? 'high' : 'medium'
        };
    }

    return null;
}

/**
 * Check if email domain exists in common domains list
 */
export function isCommonDomain(email) {
    if (!email || !email.includes('@')) return false;

    const [, domain] = email.split('@');
    return COMMON_DOMAINS.includes(domain.toLowerCase());
}
