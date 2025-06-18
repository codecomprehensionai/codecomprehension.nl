'use client'

import { useState } from 'react'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Progress } from '@/components/ui/progress'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import {
  Plus,
  Users,
  BookOpen,
  BarChart3,
  Calendar,
  CheckCircle,
  Clock,
  AlertCircle,
  TrendingUp,
  Code,
  FileText,
  Eye,
  Edit3
} from 'lucide-react'
import AssignmentCreator from './AssignmentCreator'
import AssignmentDetails from './AssignmentDetails'
import AssignmentEdit from './AssignmentEdit'

export default function TeacherDashboard() {
  const [showAssignmentCreator, setShowAssignmentCreator] = useState(false)
  const [selectedAssignment, setSelectedAssignment] = useState<number | null>(null)
  const [editingAssignment, setEditingAssignment] = useState<number | null>(null)

  const assignments = [
    {
      id: 1,
      title: "Python Functions & Loops",
      language: "Python",
      difficulty: "Intermediate",
      dueDate: "2024-12-15",
      totalStudents: 45,
      submitted: 32,
      graded: 28,
      avgScore: 87,
      status: "active"
    },
    {
      id: 2,
      title: "JavaScript Array Methods",
      language: "JavaScript",
      difficulty: "Beginner",
      dueDate: "2024-12-20",
      totalStudents: 45,
      submitted: 15,
      graded: 0,
      avgScore: 0,
      status: "active"
    },
    {
      id: 3,
      title: "Java Classes & Objects",
      language: "Java",
      difficulty: "Advanced",
      dueDate: "2024-12-10",
      totalStudents: 45,
      submitted: 45,
      graded: 45,
      avgScore: 92,
      status: "completed"
    }
  ]

  const recentSubmissions = [
    { student: "Alex Chen", assignment: "Python Functions & Loops", score: 95, submittedAt: "2 hours ago" },
    { student: "Maria Rodriguez", assignment: "Python Functions & Loops", score: 88, submittedAt: "4 hours ago" },
    { student: "David Kim", assignment: "JavaScript Array Methods", score: 91, submittedAt: "1 day ago" },
    { student: "Sarah Wilson", assignment: "Python Functions & Loops", score: 79, submittedAt: "1 day ago" },
    { student: "James Brown", assignment: "Java Classes & Objects", score: 96, submittedAt: "2 days ago" }
  ]

  if (showAssignmentCreator) {
    return <AssignmentCreator onBack={() => setShowAssignmentCreator(false)} />
  }

  if (editingAssignment) {
    const assignment = assignments.find(a => a.id === editingAssignment)
    if (assignment) {
      return <AssignmentEdit assignment={assignment} onBack={() => setEditingAssignment(null)} />
    }
  }

  if (selectedAssignment) {
    const assignment = assignments.find(a => a.id === selectedAssignment)
    if (assignment) {
      return <AssignmentDetails assignment={assignment} onBack={() => setSelectedAssignment(null)} />
    }
  }

  return (
    <div className="space-y-6 mb-4">
      {/* Quick Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <Card>
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Total Students</p>
                <p className="text-3xl font-bold">45</p>
              </div>
              <Users className="h-8 w-8 text-blue-600" />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Active Assignments</p>
                <p className="text-3xl font-bold">3</p>
              </div>
              <BookOpen className="h-8 w-8 text-green-600" />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Avg Score</p>
                <p className="text-3xl font-bold">89%</p>
              </div>
              <TrendingUp className="h-8 w-8 text-orange-600" />
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardContent className="p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-sm font-medium text-muted-foreground">Pending Reviews</p>
                <p className="text-3xl font-bold">12</p>
              </div>
              <AlertCircle className="h-8 w-8 text-red-600" />
            </div>
          </CardContent>
        </Card>
      </div>

      <Tabs defaultValue="assignments" className="space-y-6">
        <div className="flex items-center justify-between">
          <TabsList className="grid w-full max-w-md grid-cols-3">
            <TabsTrigger value="assignments">Assignments</TabsTrigger>
            <TabsTrigger value="students">Students</TabsTrigger>
            <TabsTrigger value="analytics">Analytics</TabsTrigger>
          </TabsList>

          <Button onClick={() => setShowAssignmentCreator(true)} className="bg-blue-600 hover:bg-blue-700">
            <Plus className="h-4 w-4 mr-2" />
            Create Assignment
          </Button>
        </div>

        <div className="min-h-[500px]">
          <TabsContent value="assignments" className="space-y-4">
            <div className="grid gap-4 mb-2">
              {assignments.map((assignment) => (
                <Card key={assignment.id}>
                  <CardContent className="p-6">
                    <div className="flex items-center justify-between mb-4">
                      <div className="flex items-center space-x-3">
                        <Code className="h-5 w-5 text-blue-600" />
                        <div>
                          <h3 className="font-semibold text-lg">{assignment.title}</h3>
                          <div className="flex items-center space-x-2 mt-1">
                            <Badge variant="outline">{assignment.language}</Badge>
                            <Badge variant="secondary">{assignment.difficulty}</Badge>
                            <span className="text-sm text-muted-foreground">Due: {assignment.dueDate}</span>
                          </div>
                        </div>
                      </div>
                      <div className="flex items-center space-x-2">
                        <Button variant="outline" size="sm" onClick={() => setSelectedAssignment(assignment.id)}>
                          <Eye className="h-4 w-4 mr-1" />
                          View
                        </Button>
                        <Button variant="outline" size="sm" onClick={() => setEditingAssignment(assignment.id)}>
                          <Edit3 className="h-4 w-4 mr-1" />
                          Edit
                        </Button>
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                      <div className="text-center">
                        <p className="text-2xl font-bold text-blue-600">{assignment.submitted}/{assignment.totalStudents}</p>
                        <p className="text-sm text-muted-foreground">Submitted</p>
                      </div>
                      <div className="text-center">
                        <p className="text-2xl font-bold text-green-600">{assignment.graded}</p>
                        <p className="text-sm text-muted-foreground">Graded</p>
                      </div>
                      <div className="text-center">
                        <p className="text-2xl font-bold text-orange-600">{assignment.avgScore}%</p>
                        <p className="text-sm text-muted-foreground">Avg Score</p>
                      </div>
                      <div className="flex items-center justify-center">
                        <Progress value={(assignment.submitted / assignment.totalStudents) * 100} className="w-full" />
                      </div>
                    </div>
                  </CardContent>
                </Card>
              ))}
            </div>
          </TabsContent>

          <TabsContent value="students" className="space-y-4">
            <Card className="mb-8">
              <CardHeader>
                <CardTitle>Recent Submissions</CardTitle>
                <CardDescription>Latest student submissions across all assignments</CardDescription>
              </CardHeader>
              <CardContent>
                <div className="space-y-4">
                  {recentSubmissions.map((submission, index) => (
                    <div key={index} className="flex items-center justify-between py-3 border-b last:border-b-0">
                      <div className="flex items-center space-x-3">
                        <div className="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                          <span className="text-sm font-medium text-blue-600">
                            {submission.student.split(' ').map(n => n[0]).join('')}
                          </span>
                        </div>
                        <div>
                          <p className="font-medium">{submission.student}</p>
                          <p className="text-sm text-muted-foreground">{submission.assignment}</p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="font-semibold text-lg">{submission.score}%</p>
                        <p className="text-xs text-muted-foreground">{submission.submittedAt}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </CardContent>
            </Card>
          </TabsContent>

          <TabsContent value="analytics" className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-1 gap-6 mb-8">
              <Card>
                <CardHeader>
                  <CardTitle>Performance Overview</CardTitle>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    <div className="flex items-center justify-between">
                      <span className="text-sm">Class Average</span>
                      <span className="font-semibold">89%</span>
                    </div>
                    <Progress value={89} />

                    <div className="flex items-center justify-between">
                      <span className="text-sm">Completion Rate</span>
                      <span className="font-semibold">76%</span>
                    </div>
                    <Progress value={76} />

                    <div className="flex items-center justify-between">
                      <span className="text-sm">On-time Submissions</span>
                      <span className="font-semibold">92%</span>
                    </div>
                    <Progress value={92} />
                  </div>
                </CardContent>
              </Card>
            </div>
          </TabsContent>
        </div>
      </Tabs>
    </div>
  )
}