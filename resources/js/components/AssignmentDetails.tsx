'use client'

import { useState } from 'react'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Progress } from '@/components/ui/progress'
import { ArrowLeft, Code, CheckCircle, Clock, AlertCircle, User, Search } from 'lucide-react'
import { Input } from '@/components/ui/input'

type AssignmentDetailsProps = {
  assignment: {
    id: number
    title: string
    language: string
    difficulty: string
    dueDate: string
    totalStudents: number
    submitted: number
    graded: number
    avgScore: number
    status: string
  }
  onBack: () => void
}

// Mock student data
const studentData = [
  { id: 1, name: "Alex Chen", status: "submitted", score: 95, submittedAt: "2024-12-12 14:30" },
  { id: 2, name: "Maria Rodriguez", status: "submitted", score: 88, submittedAt: "2024-12-12 15:45" },
  { id: 3, name: "David Kim", status: "submitted", score: 91, submittedAt: "2024-12-11 09:15" },
  { id: 4, name: "Sarah Wilson", status: "not_submitted", score: null, submittedAt: null },
  { id: 5, name: "James Brown", status: "submitted", score: 96, submittedAt: "2024-12-10 16:20" },
  { id: 6, name: "Emma Davis", status: "not_submitted", score: null, submittedAt: null },
  { id: 7, name: "Michael Lee", status: "submitted", score: 82, submittedAt: "2024-12-12 11:05" },
  { id: 8, name: "Sofia Garcia", status: "submitted", score: 89, submittedAt: "2024-12-11 14:50" }
]

export default function AssignmentDetails({ assignment, onBack }: AssignmentDetailsProps) {
  const submissionRate = (assignment.submitted / assignment.totalStudents) * 100
  const [searchTerm, setSearchTerm] = useState('')

  // Filter students based on search term
  const filteredStudents = studentData.filter(student =>
    student.name.toLowerCase().includes(searchTerm.toLowerCase())
  )

  return (
    <div className="space-y-6">
      <div className="flex items-center space-x-4">
        <Button variant="outline" onClick={onBack}>
          <ArrowLeft className="h-4 w-4 mr-2" />
          Back to Assignments
        </Button>
        <h2 className="text-2xl font-bold">{assignment.title}</h2>
      </div>

      {/* Overview Card */}
      <Card>
        <CardHeader>
          <CardTitle>Assignment Overview</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-6">
            <div className="flex items-center space-x-4">
              <Code className="h-5 w-5 text-blue-600" />
              <div className="flex items-center space-x-2">
                <Badge variant="outline">{assignment.language}</Badge>
                <Badge variant="secondary">{assignment.difficulty}</Badge>
                <Badge variant={assignment.status === 'active' ? 'default' : 'secondary'}>
                  {assignment.status === 'active' ? 'Active' : 'Completed'}
                </Badge>
              </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              <Card>
                <CardContent className="pt-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-muted-foreground">Due Date</p>
                      <p className="text-xl font-bold">{assignment.dueDate}</p>
                    </div>
                    <Clock className="h-8 w-8 text-blue-600" />
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardContent className="pt-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-muted-foreground">Total Students</p>
                      <p className="text-xl font-bold">{assignment.totalStudents}</p>
                    </div>
                    <User className="h-8 w-8 text-green-600" />
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardContent className="pt-6">
                  <div className="flex items-center justify-between">
                    <div>
                      <p className="text-sm font-medium text-muted-foreground">Average Score</p>
                      <p className="text-xl font-bold">{assignment.avgScore}%</p>
                    </div>
                    <CheckCircle className="h-8 w-8 text-orange-600" />
                  </div>
                </CardContent>
              </Card>
            </div>
          </div>
        </CardContent>
      </Card>

      {/* Progress and Student List */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card className="h-fit">
          <CardHeader>
            <CardTitle>Submission Progress</CardTitle>
            <CardDescription>
              {assignment.submitted} out of {assignment.totalStudents} students have submitted
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="flex items-center justify-between">
                <span className="text-sm">Submission Rate</span>
                <span className="font-semibold">{Math.round(submissionRate)}%</span>
              </div>
              <Progress value={submissionRate} />

              <div className="grid grid-cols-2 gap-4 pt-4">
                <div className="text-center p-4 bg-gray-50 rounded-lg">
                  <p className="text-2xl font-bold text-blue-600">{assignment.submitted}</p>
                  <p className="text-sm text-muted-foreground">Submitted</p>
                </div>
                <div className="text-center p-4 bg-gray-50 rounded-lg">
                  <p className="text-2xl font-bold text-orange-600">
                    {assignment.totalStudents - assignment.submitted}
                  </p>
                  <p className="text-sm text-muted-foreground">Pending</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Student Submissions</CardTitle>
            <CardDescription>
              Overview of all student submissions and scores
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <div className="relative">
                <Search className="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                <Input
                  placeholder="Search students..."
                  className="pl-8"
                  value={searchTerm}
                  onChange={(e) => setSearchTerm(e.target.value)}
                />
              </div>
              <div className="space-y-2 max-h-[400px] overflow-y-auto pr-2">
                {filteredStudents.map((student) => (
                  <div
                    key={student.id}
                    className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                  >
                    <div>
                      <p className="font-medium">{student.name}</p>
                      <p className="text-sm text-muted-foreground">
                        {student.status === 'submitted'
                          ? `Submitted: ${new Date(student.submittedAt!).toLocaleDateString()}`
                          : 'Not submitted'}
                      </p>
                    </div>
                    <div className="text-right">
                      {student.score ? (
                        <p className={`text-lg font-bold ${
                          student.score >= 90 ? 'text-green-600' :
                          student.score >= 80 ? 'text-blue-600' :
                          student.score >= 70 ? 'text-orange-600' :
                          'text-red-600'
                        }`}>
                          {student.score}%
                        </p>
                      ) : (
                        <Badge variant="secondary">Pending</Badge>
                      )}
                    </div>
                  </div>
                ))}
                {filteredStudents.length === 0 && (
                  <div className="text-center py-4 text-muted-foreground">
                    No students found matching "{searchTerm}"
                  </div>
                )}
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}