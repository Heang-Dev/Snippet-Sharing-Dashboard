/**
 * Theme Configuration
 * Similar to Shadcn's create feature with customizable options
 */

// Available theme colors (accent/primary colors)
export const themeColors = {
    neutral: {
        name: "Neutral",
        color: "#737373",
    },
    slate: {
        name: "Slate",
        color: "#64748b",
    },
    stone: {
        name: "Stone",
        color: "#78716c",
    },
    zinc: {
        name: "Zinc",
        color: "#71717a",
    },
    red: {
        name: "Red",
        color: "#ef4444",
    },
    rose: {
        name: "Rose",
        color: "#f43f5e",
    },
    orange: {
        name: "Orange",
        color: "#f97316",
    },
    green: {
        name: "Green",
        color: "#22c55e",
    },
    blue: {
        name: "Blue",
        color: "#3b82f6",
    },
    yellow: {
        name: "Yellow",
        color: "#eab308",
    },
    violet: {
        name: "Violet",
        color: "#8b5cf6",
    },
    lime: {
        name: "Lime",
        color: "#84cc16",
    },
    cyan: {
        name: "Cyan",
        color: "#06b6d4",
    },
    pink: {
        name: "Pink",
        color: "#ec4899",
    },
};

// Available fonts
export const themeFonts = {
    "jetbrains-mono": {
        name: "JetBrains Mono",
        value: "JetBrains Mono",
        className: "font-jetbrains",
        url: "https://fonts.bunny.net/css?family=jetbrains-mono:300,400,500,600,700",
    },
    inter: {
        name: "Inter",
        value: "Inter",
        className: "font-inter",
        url: "https://fonts.bunny.net/css?family=inter:300,400,500,600,700",
    },
    geist: {
        name: "Geist",
        value: "Geist",
        className: "font-geist",
        url: "https://fonts.bunny.net/css?family=geist:300,400,500,600,700",
    },
    "geist-mono": {
        name: "Geist Mono",
        value: "Geist Mono",
        className: "font-geist-mono",
        url: "https://fonts.bunny.net/css?family=geist-mono:300,400,500,600,700",
    },
    "fira-code": {
        name: "Fira Code",
        value: "Fira Code",
        className: "font-fira-code",
        url: "https://fonts.bunny.net/css?family=fira-code:300,400,500,600,700",
    },
    "source-code-pro": {
        name: "Source Code Pro",
        value: "Source Code Pro",
        className: "font-source-code",
        url: "https://fonts.bunny.net/css?family=source-code-pro:300,400,500,600,700",
    },
};

// Available radius options
export const themeRadii = {
    "0": {
        name: "Sharp",
        value: "0rem",
        preview: "rounded-none",
    },
    "0.3": {
        name: "Subtle",
        value: "0.3rem",
        preview: "rounded-sm",
    },
    "0.5": {
        name: "Medium",
        value: "0.5rem",
        preview: "rounded-md",
    },
    "0.75": {
        name: "Large",
        value: "0.75rem",
        preview: "rounded-lg",
    },
    "1": {
        name: "Full",
        value: "1rem",
        preview: "rounded-xl",
    },
};

// Component styles (affects overall appearance)
export const componentStyles = {
    lyra: {
        name: "Lyra",
        description: "Sharp, boxy design with no rounded corners",
        defaultRadius: "0",
    },
    vega: {
        name: "Vega",
        description: "Soft, rounded design with smooth corners",
        defaultRadius: "0.5",
    },
    default: {
        name: "Default",
        description: "Balanced design with subtle rounding",
        defaultRadius: "0.3",
    },
};

// Pre-built theme presets
export const themePresets = {
    "lime-lyra": {
        name: "Lime Lyra",
        description: "Sharp lime theme with monospace font",
        color: "lime",
        style: "lyra",
        radius: "0",
        font: "jetbrains-mono",
    },
    "blue-vega": {
        name: "Blue Vega",
        description: "Soft blue theme with Inter font",
        color: "blue",
        style: "vega",
        radius: "0.5",
        font: "inter",
    },
    "violet-default": {
        name: "Violet",
        description: "Elegant violet theme",
        color: "violet",
        style: "default",
        radius: "0.3",
        font: "inter",
    },
    "rose-vega": {
        name: "Rose",
        description: "Warm rose theme",
        color: "rose",
        style: "vega",
        radius: "0.5",
        font: "inter",
    },
    "green-lyra": {
        name: "Forest",
        description: "Nature-inspired green theme",
        color: "green",
        style: "lyra",
        radius: "0",
        font: "fira-code",
    },
    "orange-default": {
        name: "Sunset",
        description: "Warm orange theme",
        color: "orange",
        style: "default",
        radius: "0.3",
        font: "inter",
    },
    "cyan-vega": {
        name: "Ocean",
        description: "Cool cyan theme",
        color: "cyan",
        style: "vega",
        radius: "0.5",
        font: "geist",
    },
    "neutral-lyra": {
        name: "Minimal",
        description: "Clean neutral theme",
        color: "neutral",
        style: "lyra",
        radius: "0",
        font: "jetbrains-mono",
    },
};

// Default theme configuration
export const defaultThemeConfig = {
    mode: "system", // 'light' | 'dark' | 'system'
    color: "lime",
    style: "lyra",
    radius: "0",
    font: "jetbrains-mono",
};

// localStorage key
export const THEME_STORAGE_KEY = "snippet-sharing-theme";
