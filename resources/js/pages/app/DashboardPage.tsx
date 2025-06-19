import Dashboard from '@/components/Dashboard'
import { Assignment, Course, User } from '@/types';

interface DashboardProps {
    assignment: Assignment;
    course: Course;
}


export default function DashboardPage({ assignment, course }: DashboardProps) {
    console.log('Assignment:', assignment);
    return (
        <main className="min-h-screen bg-gray-50">
        <Dashboard course={course} assignment={assignment} />
        </main>
    )
    }
