import { Moon, Sun, Monitor } from "lucide-react";
import { Button } from "@/Components/ui/button";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/Components/ui/dropdown-menu";
import { useTheme } from "@/contexts/ThemeContext";

export function ThemeToggle() {
    const { mode, setMode } = useTheme();

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon" className="h-7 w-7">
                    <Sun className="h-3.5 w-3.5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                    <Moon className="absolute h-3.5 w-3.5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                    <span className="sr-only">Toggle theme</span>
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuItem
                    onClick={() => setMode("light")}
                    className={mode === "light" ? "bg-accent" : ""}
                >
                    <Sun className="mr-2 h-3.5 w-3.5" />
                    Light
                </DropdownMenuItem>
                <DropdownMenuItem
                    onClick={() => setMode("dark")}
                    className={mode === "dark" ? "bg-accent" : ""}
                >
                    <Moon className="mr-2 h-3.5 w-3.5" />
                    Dark
                </DropdownMenuItem>
                <DropdownMenuItem
                    onClick={() => setMode("system")}
                    className={mode === "system" ? "bg-accent" : ""}
                >
                    <Monitor className="mr-2 h-3.5 w-3.5" />
                    System
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
