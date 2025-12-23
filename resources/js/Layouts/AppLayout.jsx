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
        const crumbs = [{ label: 'Dashboard', href: '/dashboard' }];

        let currentPath = '';
        segments.forEach((segment, index) => {
            currentPath += `/${segment}`;

            // Skip if it's the dashboard since we already have it
            if (segment === 'dashboard') return;

            // Format the segment nicely
            const label = segment
                .split('-')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');

            if (index === segments.length - 1) {
                crumbs.push({ label: title || label });
            } else {
                crumbs.push({ label, href: currentPath });
            }
        });

        return crumbs;
    };

    const finalBreadcrumbs = generateBreadcrumbs();

    return (
        <SidebarProvider>
            <AppSidebar />
            <SidebarInset>
                <header className="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                    <div className="flex items-center gap-2 px-4">
                        <SidebarTrigger className="-ml-1" />
                        <Separator orientation="vertical" className="mr-2 h-4" />
                        <Breadcrumb>
                            <BreadcrumbList>
                                {finalBreadcrumbs.map((crumb, index) => (
                                    <BreadcrumbItem key={index}>
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
                    </div>
                </header>
                <div className="flex flex-1 flex-col gap-4 p-4 pt-0">
                    {children}
                </div>
            </SidebarInset>
        </SidebarProvider>
    );
}
