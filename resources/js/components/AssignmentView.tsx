'use client'

import { useState } from 'react'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Progress } from '@/components/ui/progress'
import { Assignment } from '@/types'
import { Alert, AlertDescription } from '@/components/ui/alert'
import {
    ArrowLeft,
    Code,
    Clock,
    CheckCircle,
    X,
    ArrowRight,
    FileText,
    Lightbulb,
    Trophy,
    Star
} from 'lucide-react'

interface AssignmentViewProps {
    assignment: Assignment
    onBack: () => void
}

export default function AssignmentView({ assignment, onBack }:
    AssignmentViewProps) {
    console.log('AssignmentView props:', assignment)
    const [currentQuestion, setCurrentQuestion] = useState(0)
    const [answers, setAnswers] = useState<number[]>([])
    const [showResults, setShowResults] = useState(assignment.status === 'submitted')
    const [isSubmitting, setIsSubmitting] = useState(false)


    const questions = assignment.questions || []
    console.log('Questions:', questions)

    // TODO:API call to fetch correct answer.
    const handleAnswer = (optionIndex: number) => {
        const newAnswers = [...answers]
        newAnswers[currentQuestion] = optionIndex
        setAnswers(newAnswers)
    }

    const handleNext = () => {
        if (currentQuestion < questions.length - 1) {
            setCurrentQuestion(currentQuestion + 1)
        }
    }

    const handlePrevious = () => {
        if (currentQuestion > 0) {
            setCurrentQuestion(currentQuestion - 1)
        }
    }

    const handleSubmit = () => {
        setIsSubmitting(true)
        setTimeout(() => {
            setIsSubmitting(false)
            setShowResults(true)
        }, 2000)
    }

    const getScore = () => {
        if (assignment.status === 'submitted' && assignment.score !== null) return assignment.score
        let correct = 0
        answers.forEach((answer, index) => {
            if (questions[index].submissions && answer === questions[index].submissions[-1].answer) correct++
        })
        return Math.round((correct / questions.length) * 100)
    }

    const getGradeColor = (score: number) => {
        if (score >= 90) return 'text-green-600'
        if (score >= 80) return 'text-blue-600'
        if (score >= 70) return 'text-orange-600'
        return 'text-red-600'
    }

    if (showResults) {
        const score = getScore()
        return (
            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center space-x-4">
                    <div>
                        <h1 className="text-2xl font-bold">{assignment.title}</h1>
                        <p className="text-muted-foreground">Assignment Results</p>
                    </div>
                </div>

                {/* Score Overview */}
                <Card>
                    <CardContent className="p-8 text-center">
                        <div className="flex items-center justify-center space-x-4 mb-6">
                            <Trophy className="h-12 w-12 text-yellow-500" />
                            <div>
                                <p className="text-4xl font-bold text-blue-600">{score}%</p>
                                <p className="text-muted-foreground">Your Score</p>
                            </div>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <p className="text-lg font-bold">{answers.filter((answer, idx) => {
                                    const question = questions[idx];
                                    const latestSubmission = question.submissions?.[question.submissions.length - 1];
                                    return answer !== undefined && latestSubmission && answer === latestSubmission.answer && latestSubmission.is_correct;
                                }).length}/{questions.length}</p>
                                <p className="text-sm text-muted-foreground">Correct Answers</p>
                            </div>
                            <div>
                                <p className="text-lg font-bold">{assignment.timeSpent}</p>
                                <p className="text-sm text-muted-foreground">Time Spent</p>
                            </div>
                            <div>
                                <p className="text-lg font-bold">#{Math.floor(Math.random() * 10) + 1}</p>
                                <p className="text-sm text-muted-foreground">Class Rank</p>
                            </div>
                        </div>
                        {score !== null && score >= 90 && (
                            <Alert className="bg-green-50 border-green-200">
                                <Star className="h-4 w-4" />
                                <AlertDescription>
                                    Outstanding work! You've demonstrated excellent code comprehension skills.
                                </AlertDescription>
                            </Alert>
                        )}
                    </CardContent>
                </Card>

                {/* Detailed Feedback */}
                <Card>
                    <CardHeader>
                        <CardTitle>Detailed Feedback</CardTitle>
                        <CardDescription>AI-generated insights on your performance</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div className="space-y-6">
                            {questions.map((question, index) => (
                                <div key={question.id} className="border rounded-lg p-4">
                                    <div className="flex items-start justify-between mb-3">
                                        <h4 className="font-medium">Question {index + 1}: {question.question}</h4>
                                        {(() => {
                                            const latestSubmission = question.submissions?.[question.submissions.length - 1];
                                            const isCorrect = latestSubmission && answers[index] === latestSubmission.answer && latestSubmission.is_correct;
                                            return isCorrect ? (
                                                <CheckCircle className="h-5 w-5 text-green-600 mt-1" />
                                            ) : (
                                                <X className="h-5 w-5 text-red-600 mt-1" />
                                            );
                                        })()}
                                    </div>

                                    <div className="space-y-2 mb-4">
                                        {question.options.map((option, optIndex) => {
                                            const latestSubmission = question.submissions?.[question.submissions.length - 1];
                                            const isCorrectOption = latestSubmission && latestSubmission.answer === optIndex && latestSubmission.is_correct;
                                            const isUserAnswer = answers[index] === optIndex;
                                            const isWrongAnswer = isUserAnswer && !isCorrectOption;

                                            return (
                                                <div key={optIndex} className={`p-2 rounded text-sm ${isCorrectOption
                                                    ? 'bg-green-50 border border-green-200'
                                                    : isWrongAnswer
                                                        ? 'bg-red-50 border border-red-200'
                                                        : 'bg-gray-50'
                                                    }`}>
                                                    <span className="font-mono">{String.fromCharCode(65 + optIndex)})</span> {option.text}
                                                    {isCorrectOption && <Badge variant="secondary" className="ml-2">Correct</Badge>}
                                                    {isWrongAnswer && <Badge variant="destructive" className="ml-2">Your Answer</Badge>}
                                                </div>
                                            );
                                        })}
                                    </div>

                                    <div className="bg-blue-50 p-3 rounded mb-3">
                                        <p className="text-sm"><strong>Explanation:</strong> {question.explanation}</p>
                                    </div>

                                    {(() => {
                                        const latestSubmission = question.submissions?.[question.submissions.length - 1];
                                        const isCorrect = latestSubmission && answers[index] === latestSubmission.answer && latestSubmission.is_correct;
                                        return isCorrect && (
                                            <div className="bg-green-50 p-3 rounded">
                                                <p className="text-sm text-green-800"><strong>AI Feedback:</strong> {latestSubmission.feedback}</p>
                                            </div>
                                        );
                                    })()}
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>
            </div>
        )
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold">{assignment.title}</h1>
                    <p className="text-muted-foreground">{assignment.description}</p>
                </div>
                <div className="flex items-center space-x-2">
                    <Badge variant="outline">{assignment.language}</Badge>
                    <Badge variant="secondary">{assignment.difficulty}</Badge>
                </div>
            </div>

            {/* Progress */}
            <Card>
                <CardContent className="p-4">
                    <div className="flex items-center justify-between mb-2">
                        <span className="text-sm font-medium">Progress</span>
                        <span className="text-sm text-muted-foreground">
                            Question {currentQuestion + 1} of {questions.length}
                        </span>
                    </div>
                    <Progress value={((currentQuestion + 1) / questions.length) * 100} />
                </CardContent>
            </Card>

            {/* Code Example */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center">
                        <Code className="h-5 w-5 mr-2" />
                        Code to Analyze
                    </CardTitle>
                    <CardDescription>
                        Study this code carefully before answering the questions
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div className="bg-gray-900 text-gray-100 p-4 rounded-lg font-mono text-sm overflow-x-auto">
                        <pre>{questions[currentQuestion].code}</pre>
                    </div>
                </CardContent>
            </Card>

            {/* Current Question */}
            <Card>
                <CardHeader>
                    <CardTitle className="flex items-center justify-between">
                        <span className="flex items-center">
                            <FileText className="h-5 w-5 mr-2" />
                            Question {currentQuestion + 1}
                        </span>
                        <div className="flex items-center space-x-2">
                            <Clock className="h-4 w-4" />
                            <span className="text-sm text-muted-foreground">{assignment.timeSpent}</span>
                        </div>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="space-y-4">
                        <h3 className="font-medium text-lg">{questions[currentQuestion].question}</h3>

                        <div className="space-y-2">
                            {questions[currentQuestion]?.options?.map((option, index) => (
                                <button
                                    key={index}
                                    onClick={() => handleAnswer(index)}
                                    className={`w-full text-left p-3 rounded-lg border transition-colors ${answers[currentQuestion] === index
                                        ? 'bg-blue-50 border-blue-300 ring-2 ring-blue-200'
                                        : 'bg-white border-gray-200 hover:bg-gray-50'
                                        }`}
                                >
                                    <span className="font-mono text-sm mr-3">{String.fromCharCode(65 + index)}</span>
                                    {option.text}
                                </button>
                            ))}
                        </div>

                        <div className="flex justify-between pt-4">
                            <Button
                                variant="outline"
                                onClick={handlePrevious}
                                disabled={currentQuestion === 0}
                            >
                                Previous
                            </Button>

                            <div className="flex space-x-2">
                                {currentQuestion === questions.length - 1 ? (
                                    <Button
                                        onClick={handleSubmit}
                                        disabled={answers.length !== questions.length || isSubmitting}
                                        className="bg-green-600 hover:bg-green-700"
                                    >
                                        {isSubmitting ? (
                                            <>
                                                <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white mr-2"></div>
                                                Submitting...
                                            </>
                                        ) : (
                                            <>
                                                <CheckCircle className="h-4 w-4 mr-2" />
                                                Submit Assignment
                                            </>
                                        )}
                                    </Button>
                                ) : (
                                    <Button
                                        onClick={handleNext}
                                        disabled={
                                            questions[currentQuestion]?.options?.length > 0
                                                ? answers[currentQuestion] === undefined
                                                : false
                                        }
                                    >
                                        Next
                                        <ArrowRight className="h-4 w-4 ml-2" />
                                    </Button>
                                )}
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            {/* Hint */}
            <Alert>
                <Lightbulb className="h-4 w-4" />
                <AlertDescription>
                    <strong>Tip:</strong> Take your time to analyze the code structure, variable flow, and potential edge cases before selecting your answer.
                </AlertDescription>
            </Alert>
        </div>
    )
}
