import { ChartAreaInteractive } from "@/Components/chart-area-interactive";
import { DataTable } from "@/Components/data-table";
import { SectionCards } from "@/Components/section-cards";
import { Head } from "@inertiajs/react";
import { DashboardBreadcrumb } from "@/Components/dashboard-breadcrumb";
import DashboardLayout from "@/Layouts/DashboardLayout";

import data from "./data.json";

export default function DashboardIndexPage() {
    return (
        <>
            <Head title="Overview" />
            <div className="@container/main flex flex-1 flex-col gap-2">
                <div className="flex flex-col gap-4 py-4 md:gap-6 md:py-6">
                    <SectionCards />
                    <div className="px-4 lg:px-6">
                        <ChartAreaInteractive />
                    </div>
                    <DataTable data={data} />
                </div>
            </div>
        </>
    );
}

// Use DashboardLayout with breadcrumb
DashboardIndexPage.layout = (page) => (
    <DashboardLayout header={<DashboardBreadcrumb items={[]} />} children={page} />
);
