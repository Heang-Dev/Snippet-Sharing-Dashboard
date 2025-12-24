import { Separator } from "@/Components/ui/separator";
import { SidebarTrigger } from "@/Components/ui/sidebar";
import { ThemeToggle } from "@/Components/theme-toggle";

export function SiteHeader({ children }) {
    return (
        <header
            className="sticky top-0 z-10 group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 flex h-12 shrink-0 items-center gap-2 border-b border-border/50 bg-background/80 dark:bg-background/70 backdrop-blur-2xl backdrop-saturate-150 shadow-sm transition-[width,height] ease-linear"
            style={{
                backdropFilter: 'blur(20px) saturate(180%)',
                WebkitBackdropFilter: 'blur(20px) saturate(180%)',
            }}
        >
            <div className="flex gap-1 items-center px-4 w-full lg:gap-2 lg:px-6">
                <SidebarTrigger className="-ml-1" />
                <Separator
                    orientation="vertical"
                    className="mx-2 data-[orientation=vertical]:h-4"
                />

                <div className="flex-1">{children}</div>

                <ThemeToggle />
            </div>
        </header>
    );
}
