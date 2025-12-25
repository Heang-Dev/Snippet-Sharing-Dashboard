import * as React from "react";
import { Link, usePage } from "@inertiajs/react";
import {
    Code2,
    LayoutDashboardIcon,
    UsersIcon,
    FolderIcon,
    SettingsIcon,
    HelpCircleIcon,
    SearchIcon,
    Paintbrush,
} from "lucide-react";

import { NavMain } from "@/Components/nav-main";
import { NavSecondary } from "@/Components/nav-secondary";
import { NavUser } from "@/Components/nav-user";
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from "@/Components/ui/sidebar";
import { ThemeCustomizer } from "@/Components/ThemeCustomizer";

export function AppSidebar({ user, ...props }) {
    const { url } = usePage();

    const data = {
        navMain: [
            {
                title: "Dashboard",
                url: "/dashboard",
                icon: LayoutDashboardIcon,
            },
            {
                title: "Snippets",
                url: "/dashboard/snippets",
                icon: Code2,
            },
            {
                title: "Teams",
                url: "/dashboard/teams",
                icon: UsersIcon,
            },
        ],
        navSecondary: [
            {
                title: "Customize",
                url: "#customize",
                icon: Paintbrush,
                isCustomizer: true,
            },
            {
                title: "Settings",
                url: "/settings",
                icon: SettingsIcon,
            },
            {
                title: "Get Help",
                url: "#",
                icon: HelpCircleIcon,
            },
            {
                title: "Search",
                url: "#",
                icon: SearchIcon,
            },
        ],
    };

    return (
        <Sidebar collapsible="offcanvas" {...props}>
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton asChild className="data-[slot=sidebar-menu-button]:!p-1.5">
                            <Link href="/dashboard">
                                <Code2 className="h-5 w-5" />
                                <span className="text-base font-semibold">Snippet Share</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>
            <SidebarContent>
                <NavMain items={data.navMain} />
                <NavSecondary items={data.navSecondary} className="mt-auto" />
            </SidebarContent>
            <SidebarFooter>
                <NavUser user={user} />
            </SidebarFooter>
        </Sidebar>
    );
}
