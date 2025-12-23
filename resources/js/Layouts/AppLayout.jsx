import { Link, usePage } from '@inertiajs/react';
import { AppSidebar } from '@/Components/app-sidebar';
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb';
import { Separator } from '@/Components/ui/separator';
import {
    SidebarInset,
    SidebarProvider,
    SidebarTrigger,
} from '@/Components/ui/sidebar';

export default function AppLayout({ children, title, breadcrumbs = [] }) {
    const { url } = usePage();

    // Generate breadcrumbs from URL if not provided
    const generateBreadcrumbs = () => {
        if (breadcrumbs.length > 0) return breadcrumbs;

        const segments = url.split('/').filter(Boolean);
        const crumbs = [];

        // Add "Building Your Application" style first crumb for context
        if (segments[0] === 'dashboard') {
            crumbs.push({ label: 'Building Your Application', href: '/dashboard' });
        } else if (segments[0] === 'snippets') {
            crumbs.push({ label: 'Snippets', href: '/snippets' });
        } else if (segments[0] === 'teams') {
            crumbs.push({ label: 'Teams', href: '/teams' });
        } else {
            crumbs.push({ label: 'Dashboard', href: '/dashboard' });
        }

        // Add the current page title
        if (title) {
            crumbs.push({ label: title });
        } else if (segments.length > 1) {
            const lastSegment = segments[segments.length - 1];
            const label = lastSegment
                .split('-')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
            crumbs.push({ label });
        } else {
            crumbs.push({ label: 'Data Fetching' });
        }

        return crumbs;
    };

    const finalBreadcrumbs = generateBreadcrumbs();

    return (
        <SidebarProvider>
            <AppSidebar />
            <SidebarInset>
                <header className="flex h-16 shrink-0 items-center gap-2 border-b px-4">
                    <SidebarTrigger className="-ml-1" />
                    <Separator orientation="vertical" className="mr-2 h-4" />
                    <Breadcrumb>
                        <BreadcrumbList>
                            {finalBreadcrumbs.map((crumb, index) => (
                                <BreadcrumbItem key={index} className={index === 0 ? "hidden md:block" : ""}>
                                    {index > 0 && <BreadcrumbSeparator className="hidden md:block" />}
                                    {crumb.href ? (
                                        <BreadcrumbLink asChild>
                                            <Link href={crumb.href}>{crumb.label}</Link>
                                        </BreadcrumbLink>
                                    ) : (
                                        <BreadcrumbPage>{crumb.label}</BreadcrumbPage>
                                    )}
                                </BreadcrumbItem>
                            ))}
                        </BreadcrumbList>
                    </Breadcrumb>
                </header>
                <div className="flex flex-1 flex-col gap-4 p-4">
                    {children}
                </div>
            </SidebarInset>
        </SidebarProvider>
    );
}
