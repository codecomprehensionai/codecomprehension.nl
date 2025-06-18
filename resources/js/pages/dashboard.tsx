import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

interface DashboardProps {
    currentCourse?: {
        id: number;
        title: string;
        lti_id: string;
    } | null;
    currentAssignment?: {
        id: number;
        title: string;
        description?: string;
        lti_id: string;
        deadline_at?: string;
    } | null;
    user: {
        id: number;
        name: string;
        type: string;
    };
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

export default function Dashboard({ currentCourse, currentAssignment, user }: DashboardProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    {/* Course Card */}
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        {currentCourse ? (
                            <div className="p-4 h-full flex flex-col justify-center">
                                <h3 className="text-lg font-semibold text-foreground mb-2">Current Course</h3>
                                <h4 className="text-xl font-bold text-primary mb-1">{currentCourse.title}</h4>
                                <p className="text-sm text-muted-foreground">Course ID: {currentCourse.lti_id}</p>
                            </div>
                        ) : (
                            <div className="p-4 h-full flex flex-col justify-center">
                                <h3 className="text-lg font-semibold text-foreground mb-2">Current Course</h3>
                                <p className="text-muted-foreground">No course information available</p>
                                <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20 opacity-20" />
                            </div>
                        )}
                    </div>
                    
                    {/* Assignment Card */}
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        {currentAssignment ? (
                            <div className="p-4 h-full flex flex-col justify-between">
                                <div>
                                    <h3 className="text-lg font-semibold text-foreground mb-2">Current Assignment</h3>
                                    <h4 className="text-xl font-bold text-primary mb-1">{currentAssignment.title}</h4>
                                    {currentAssignment.description && (
                                        <p className="text-sm text-muted-foreground line-clamp-2">{currentAssignment.description}</p>
                                    )}
                                </div>
                                <div className="mt-2">
                                    {currentAssignment.deadline_at && (
                                        <p className="text-xs text-muted-foreground">
                                            Due: {new Date(currentAssignment.deadline_at).toLocaleDateString()}
                                        </p>
                                    )}
                                </div>
                            </div>
                        ) : (
                            <div className="p-4 h-full flex flex-col justify-center">
                                <h3 className="text-lg font-semibold text-foreground mb-2">Current Assignment</h3>
                                <p className="text-muted-foreground">No assignment information available</p>
                                <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20 opacity-20" />
                            </div>
                        )}
                    </div>
                    
                    {/* Third card with Submit Assignment button */}
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <div className="p-4 h-full flex flex-col justify-center items-center">
                            <h3 className="text-lg font-semibold text-foreground mb-4">Quick Actions</h3>
                            <button 
                                className="px-6 py-3 bg-primary text-primary-foreground rounded-lg font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                disabled
                            >
                                Submit Assignment
                            </button>
                            <p className="text-xs text-muted-foreground mt-2">Coming soon...</p>
                        </div>
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20 opacity-20" />
                    </div>
                </div>
                <div className="relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                    <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                </div>
            </div>
        </AppLayout>
    );
}
