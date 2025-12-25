import { useTheme } from "@/contexts/ThemeContext";
import {
    themeColors,
    themeFonts,
    themeRadii,
    themePresets,
} from "@/lib/theme-config";
import { Button } from "@/Components/ui/button";
import { Label } from "@/Components/ui/label";
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
    SheetTrigger,
} from "@/Components/ui/sheet";
import { ScrollArea } from "@/Components/ui/scroll-area";
import { Separator } from "@/Components/ui/separator";
import { cn } from "@/lib/utils";
import {
    Paintbrush,
    Sun,
    Moon,
    Monitor,
    Check,
    RotateCcw,
    Palette,
} from "lucide-react";

export function ThemeCustomizer({ children, side = "right" }) {
    const {
        mode,
        color,
        radius,
        font,
        setMode,
        setColor,
        setRadius,
        setFont,
        applyPreset,
        resetTheme,
        getCurrentPreset,
    } = useTheme();

    const currentPreset = getCurrentPreset();

    return (
        <Sheet>
            <SheetTrigger asChild>
                {children || (
                    <Button variant="outline" size="icon" className="h-7 w-7">
                        <Paintbrush className="h-3.5 w-3.5" />
                        <span className="sr-only">Customize theme</span>
                    </Button>
                )}
            </SheetTrigger>
            <SheetContent side={side} className="w-[320px] sm:w-[400px] p-0">
                <SheetHeader className="px-4 py-3 border-b">
                    <SheetTitle className="flex items-center gap-2 text-sm">
                        <Palette className="h-4 w-4" />
                        Customize Theme
                    </SheetTitle>
                    <SheetDescription className="text-xs">
                        Personalize your experience with colors, fonts, and styles.
                    </SheetDescription>
                </SheetHeader>

                <ScrollArea className="h-[calc(100vh-120px)]">
                    <div className="p-4 space-y-6">
                        {/* Presets Section */}
                        <div className="space-y-2">
                            <Label className="text-xs font-medium">Presets</Label>
                            <div className="grid grid-cols-2 gap-2">
                                {Object.entries(themePresets).map(([key, preset]) => (
                                    <Button
                                        key={key}
                                        variant={currentPreset === key ? "default" : "outline"}
                                        size="sm"
                                        className="h-auto py-2 px-3 justify-start"
                                        onClick={() => applyPreset(key)}
                                    >
                                        <div
                                            className="w-3 h-3 rounded-full mr-2 border"
                                            style={{ backgroundColor: themeColors[preset.color]?.color }}
                                        />
                                        <div className="text-left">
                                            <div className="text-xs font-medium">{preset.name}</div>
                                        </div>
                                        {currentPreset === key && (
                                            <Check className="h-3 w-3 ml-auto" />
                                        )}
                                    </Button>
                                ))}
                            </div>
                        </div>

                        <Separator />

                        {/* Mode Section */}
                        <div className="space-y-2">
                            <Label className="text-xs font-medium">Appearance</Label>
                            <div className="grid grid-cols-3 gap-2">
                                <Button
                                    variant={mode === "light" ? "default" : "outline"}
                                    size="sm"
                                    className="h-8"
                                    onClick={() => setMode("light")}
                                >
                                    <Sun className="h-3.5 w-3.5 mr-1.5" />
                                    Light
                                </Button>
                                <Button
                                    variant={mode === "dark" ? "default" : "outline"}
                                    size="sm"
                                    className="h-8"
                                    onClick={() => setMode("dark")}
                                >
                                    <Moon className="h-3.5 w-3.5 mr-1.5" />
                                    Dark
                                </Button>
                                <Button
                                    variant={mode === "system" ? "default" : "outline"}
                                    size="sm"
                                    className="h-8"
                                    onClick={() => setMode("system")}
                                >
                                    <Monitor className="h-3.5 w-3.5 mr-1.5" />
                                    System
                                </Button>
                            </div>
                        </div>

                        <Separator />

                        {/* Color Section */}
                        <div className="space-y-2">
                            <Label className="text-xs font-medium">Theme Color</Label>
                            <div className="grid grid-cols-7 gap-1.5">
                                {Object.entries(themeColors).map(([key, value]) => (
                                    <button
                                        key={key}
                                        onClick={() => setColor(key)}
                                        className={cn(
                                            "w-7 h-7 rounded-full border-2 transition-all hover:scale-110 flex items-center justify-center",
                                            color === key
                                                ? "border-foreground scale-110"
                                                : "border-transparent"
                                        )}
                                        style={{ backgroundColor: value.color }}
                                        title={value.name}
                                    >
                                        {color === key && (
                                            <Check className="h-3 w-3 text-white drop-shadow-md" />
                                        )}
                                    </button>
                                ))}
                            </div>
                            <p className="text-[10px] text-muted-foreground">
                                Selected: {themeColors[color]?.name}
                            </p>
                        </div>

                        <Separator />

                        {/* Radius Section */}
                        <div className="space-y-2">
                            <Label className="text-xs font-medium">Border Radius</Label>
                            <div className="grid grid-cols-5 gap-1.5">
                                {Object.entries(themeRadii).map(([key, value]) => (
                                    <Button
                                        key={key}
                                        variant={radius === key ? "default" : "outline"}
                                        size="sm"
                                        className="h-8 text-[10px]"
                                        onClick={() => setRadius(key)}
                                    >
                                        {value.name}
                                    </Button>
                                ))}
                            </div>
                            <div className="flex items-center gap-2 mt-2">
                                <div
                                    className="w-full h-6 bg-primary"
                                    style={{ borderRadius: themeRadii[radius]?.value }}
                                />
                            </div>
                        </div>

                        <Separator />

                        {/* Font Section */}
                        <div className="space-y-2">
                            <Label className="text-xs font-medium">Font Family</Label>
                            <div className="grid grid-cols-2 gap-2">
                                {Object.entries(themeFonts).map(([key, value]) => (
                                    <Button
                                        key={key}
                                        variant={font === key ? "default" : "outline"}
                                        size="sm"
                                        className="h-8 text-[10px]"
                                        onClick={() => setFont(key)}
                                        style={{ fontFamily: value.value }}
                                    >
                                        {value.name}
                                        {font === key && <Check className="h-3 w-3 ml-1.5" />}
                                    </Button>
                                ))}
                            </div>
                            <p
                                className="text-xs text-muted-foreground mt-2 p-2 border rounded-md"
                                style={{ fontFamily: themeFonts[font]?.value }}
                            >
                                The quick brown fox jumps over the lazy dog. 0123456789
                            </p>
                        </div>

                        <Separator />

                        {/* Reset Button */}
                        <Button
                            variant="outline"
                            size="sm"
                            className="w-full"
                            onClick={resetTheme}
                        >
                            <RotateCcw className="h-3.5 w-3.5 mr-2" />
                            Reset to Default
                        </Button>
                    </div>
                </ScrollArea>
            </SheetContent>
        </Sheet>
    );
}

// Compact theme toggle with customizer access
export function ThemeToggleWithCustomizer({ className }) {
    const { mode, setMode, resolvedMode } = useTheme();

    return (
        <div className={cn("flex items-center gap-1", className)}>
            <Button
                variant="ghost"
                size="icon"
                className="h-7 w-7"
                onClick={() => setMode(resolvedMode === "dark" ? "light" : "dark")}
            >
                <Sun className="h-3.5 w-3.5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                <Moon className="absolute h-3.5 w-3.5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                <span className="sr-only">Toggle theme</span>
            </Button>
            <ThemeCustomizer />
        </div>
    );
}
