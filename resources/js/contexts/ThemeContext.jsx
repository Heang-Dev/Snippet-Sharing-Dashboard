import { createContext, useContext, useEffect, useState, useCallback } from "react";
import {
    defaultThemeConfig,
    THEME_STORAGE_KEY,
    themeColors,
    themeFonts,
    themeRadii,
    themePresets,
} from "@/lib/theme-config";

const ThemeContext = createContext(undefined);

export function ThemeProvider({ children }) {
    const [themeConfig, setThemeConfig] = useState(() => {
        // Try to load from localStorage on initial render
        if (typeof window !== "undefined") {
            try {
                const stored = localStorage.getItem(THEME_STORAGE_KEY);
                if (stored) {
                    return { ...defaultThemeConfig, ...JSON.parse(stored) };
                }
            } catch (e) {
                console.error("Failed to load theme from localStorage:", e);
            }
        }
        return defaultThemeConfig;
    });

    const [resolvedMode, setResolvedMode] = useState("light");

    // Resolve system theme preference
    const resolveMode = useCallback(() => {
        if (themeConfig.mode === "system") {
            return window.matchMedia("(prefers-color-scheme: dark)").matches
                ? "dark"
                : "light";
        }
        return themeConfig.mode;
    }, [themeConfig.mode]);

    // Apply theme to document
    const applyTheme = useCallback(() => {
        const root = document.documentElement;
        const mode = resolveMode();
        setResolvedMode(mode);

        // Apply mode (light/dark)
        root.classList.remove("light", "dark");
        root.classList.add(mode);

        // Apply theme color class
        const themeClasses = Object.keys(themeColors).map((c) => `theme-${c}`);
        root.classList.remove(...themeClasses);
        root.classList.add(`theme-${themeConfig.color}`);

        // Apply radius as CSS variable
        const radiusValue = themeRadii[themeConfig.radius]?.value || "0rem";
        root.style.setProperty("--radius", radiusValue);

        // Apply font
        const fontConfig = themeFonts[themeConfig.font];
        if (fontConfig) {
            root.style.setProperty("--font-sans", fontConfig.value);
            root.style.setProperty("--font-mono", fontConfig.value);

            // Load font if not already loaded
            loadFont(fontConfig);
        }
    }, [themeConfig, resolveMode]);

    // Load font dynamically
    const loadFont = useCallback((fontConfig) => {
        const existingLink = document.querySelector(
            `link[href="${fontConfig.url}"]`
        );
        if (!existingLink) {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = fontConfig.url;
            document.head.appendChild(link);
        }
    }, []);

    // Save to localStorage whenever config changes
    useEffect(() => {
        try {
            localStorage.setItem(THEME_STORAGE_KEY, JSON.stringify(themeConfig));
        } catch (e) {
            console.error("Failed to save theme to localStorage:", e);
        }
        applyTheme();
    }, [themeConfig, applyTheme]);

    // Listen for system theme changes
    useEffect(() => {
        const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
        const handleChange = () => {
            if (themeConfig.mode === "system") {
                applyTheme();
            }
        };

        mediaQuery.addEventListener("change", handleChange);
        return () => mediaQuery.removeEventListener("change", handleChange);
    }, [themeConfig.mode, applyTheme]);

    // Apply theme on initial render
    useEffect(() => {
        applyTheme();
    }, [applyTheme]);

    // Theme setters
    const setMode = useCallback((mode) => {
        setThemeConfig((prev) => ({ ...prev, mode }));
    }, []);

    const setColor = useCallback((color) => {
        setThemeConfig((prev) => ({ ...prev, color }));
    }, []);

    const setRadius = useCallback((radius) => {
        setThemeConfig((prev) => ({ ...prev, radius }));
    }, []);

    const setFont = useCallback((font) => {
        setThemeConfig((prev) => ({ ...prev, font }));
    }, []);

    const setStyle = useCallback((style) => {
        setThemeConfig((prev) => ({ ...prev, style }));
    }, []);

    // Apply a preset
    const applyPreset = useCallback((presetKey) => {
        const preset = themePresets[presetKey];
        if (preset) {
            setThemeConfig((prev) => ({
                ...prev,
                color: preset.color,
                style: preset.style,
                radius: preset.radius,
                font: preset.font,
            }));
        }
    }, []);

    // Reset to default
    const resetTheme = useCallback(() => {
        setThemeConfig(defaultThemeConfig);
    }, []);

    // Get current preset name if matches
    const getCurrentPreset = useCallback(() => {
        for (const [key, preset] of Object.entries(themePresets)) {
            if (
                preset.color === themeConfig.color &&
                preset.style === themeConfig.style &&
                preset.radius === themeConfig.radius &&
                preset.font === themeConfig.font
            ) {
                return key;
            }
        }
        return null;
    }, [themeConfig]);

    const value = {
        // Current config
        mode: themeConfig.mode,
        color: themeConfig.color,
        radius: themeConfig.radius,
        font: themeConfig.font,
        style: themeConfig.style,
        resolvedMode,

        // Setters
        setMode,
        setColor,
        setRadius,
        setFont,
        setStyle,

        // Utilities
        applyPreset,
        resetTheme,
        getCurrentPreset,

        // Full config access
        themeConfig,
        setThemeConfig,
    };

    return (
        <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>
    );
}

export function useTheme() {
    const context = useContext(ThemeContext);
    if (context === undefined) {
        throw new Error("useTheme must be used within a ThemeProvider");
    }
    return context;
}

export { ThemeContext };
