'use client'

import { useState } from 'react'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Assignment, Course, PageProps, User } from '@/types'
import logo from '@/assets/CodeComprehension.png'
import {
    BookOpen,
    Code,
    Users,
    BarChart3,
    Plus,
    Settings,
    LogOut,
    GraduationCap,
    CheckCircle,
    Clock,
    AlertCircle,
    TrendingUp
} from 'lucide-react'
import TeacherDashboard from './TeacherDashboard'
import AssignmentCreator from './AssignmentCreator'
import AssignmentView from './AssignmentView'
import { usePage } from '@inertiajs/react'

type UserRole = 'teacher' | 'student'

interface DashboardProps {
    course: Course,
    assignment: Assignment,
}

export default function Dashboard({ course, assignment }: DashboardProps) {
    const page = usePage<PageProps>();
    const user: User = page.props?.auth?.user;

      const [userRole, setUserRole] = useState<UserRole>(user.type)
    // const [userRole, setUserRole] = useState<UserRole>('teacher') // om te testen

    const [showTeacherDashboard, setShowTeacherDashboard] = useState(false)

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Header */}
            <header className="bg-white border-b border-gray-200 px-6 py-4">
                <div className="flex items-center justify-between max-w-7xl mx-auto">
                    <div className="flex items-center space-x-4">
                        <div className="flex items-center space-x-3">
                            <div className="p-0.5">
                                <img
                                    src={logo}
                                    alt="CodeComprehension Logo"
                                    width={40}
                                    height={40}
                                    className="rounded-lg"
                                />
                            </div>
                            <h1 className="text-2xl font-bold text-gray-900">CodeComprehension</h1>
                        </div>
                    </div>

                    <div className="flex items-center space-x-4">
                        <div className="flex items-center space-x-3">
                            <Avatar>
                                <AvatarFallback>{user.name.split(' ').map(n => n[0]).join('')}</AvatarFallback>
                            </Avatar>
                            <div className="hidden md:block">
                                <p className="text-sm font-medium text-gray-900">{user.name}</p>
                                <p className="text-xs text-gray-500">{user.email}</p>
                            </div>
                            <Button variant="ghost" size="sm">
                                <Settings className="h-4 w-4" />
                            </Button>
                            <Button variant="ghost" size="sm">
                                <LogOut className="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                </div>
            </header>

            {/* Main Content */}
            <main className="max-w-7xl mx-auto px-6 py-8">
                {userRole === 'teacher' ? (
                    showTeacherDashboard ? (
                        <TeacherDashboard />
                    ) : (
                        <AssignmentCreator assignment={assignment} onBack={() => setShowTeacherDashboard(true)} />
                    )
                ) : (
                    <AssignmentView assignment={assignment} onBack={() => { }} />
                )}
            </main>
        </div>
    )
}
