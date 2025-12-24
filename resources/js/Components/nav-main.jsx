import { Link, usePage } from "@inertiajs/react";
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/Components/ui/sidebar";

export function NavMain({ items }) {
    const { url } = usePage();

    // Normalize URL for comparison
    const normalizeUrl = (urlToNormalize) => {
        return urlToNormalize?.split('?')[0] || '';
    };

    const isActive = (itemUrl) => {
        const currentUrl = normalizeUrl(url);
        const targetUrl = normalizeUrl(itemUrl);

        // Exact match for dashboard
        if (targetUrl === '/dashboard') {
            return currentUrl === '/dashboard';
        }

        // Prefix match for other routes
        return currentUrl.startsWith(targetUrl);
    };

    return (
        <SidebarGroup>
            <SidebarGroupContent>
                <SidebarMenu>
                    {items.map((item) => (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={isActive(item.url)}
                                tooltip={item.title}
                            >
                                <Link href={item.url}>
                                    {item.icon && <item.icon />}
                                    <span>{item.title}</span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ))}
                </SidebarMenu>
            </SidebarGroupContent>
        </SidebarGroup>
    );
}
