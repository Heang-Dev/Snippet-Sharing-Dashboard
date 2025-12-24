import { AppSidebar } from "@/Components/app-sidebar";
import { SiteHeader } from "@/Components/site-header";
import { SidebarInset, SidebarProvider } from "@/Components/ui/sidebar";
import { Head, usePage } from "@inertiajs/react";
import { Toaster } from "@/Components/ui/sonner";
import { useEffect } from "react";
import { toast } from "sonner";

export default function DashboardLayout({ header, children }) {
    const { auth, flash } = usePage().props;

    useEffect(() => {
        const { success, error } = flash || {};
        if (success) {
            toast.success("Success", { description: success });
        }
        if (error) {
            toast.error("Error", { description: error });
        }
    }, [flash]);

    return (
        <SidebarProvider>
            <Head title="Dashboard" />

            <AppSidebar user={auth.user} />

            <SidebarInset>
                <div className="flex flex-col min-h-screen">
                    <SiteHeader>{header}</SiteHeader>

                    <main className="flex flex-col flex-1 p-4 md:p-6">
                        {children}
                    </main>
                </div>
            </SidebarInset>
            <Toaster richColors position="top-right" />
        </SidebarProvider>
    );
}
