import { Link, usePage } from "@inertiajs/react";
import {
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/Components/ui/sidebar";

export function NavSecondary({ items, ...props }) {
    const { url } = usePage();

    const normalizeUrl = (urlToNormalize) => {
        return urlToNormalize?.split('?')[0] || '';
    };

    const isActive = (itemUrl) => {
        if (!itemUrl || itemUrl === '#') return false;
        const currentUrl = normalizeUrl(url);
        const targetUrl = normalizeUrl(itemUrl);
        return currentUrl.startsWith(targetUrl);
    };

    return (
        <SidebarGroup {...props}>
            <SidebarGroupContent>
                <SidebarMenu>
                    {items.map((item) => (
                        <SidebarMenuItem key={item.title}>
                            <SidebarMenuButton
                                asChild
                                isActive={isActive(item.url)}
                                tooltip={item.title}
                            >
                                {item.url && item.url !== '#' ? (
                                    <Link href={item.url}>
                                        <item.icon />
                                        <span>{item.title}</span>
                                    </Link>
                                ) : (
                                    <button type="button">
                                        <item.icon />
                                        <span>{item.title}</span>
                                    </button>
                                )}
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    ))}
                </SidebarMenu>
            </SidebarGroupContent>
        </SidebarGroup>
    );
}
