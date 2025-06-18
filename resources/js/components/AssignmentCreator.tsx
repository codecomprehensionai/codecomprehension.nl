'use client'

import { useState } from 'react'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import {
    ArrowLeft,
    Wand2,
    Code,
    Clock,
    FileText,
    CheckCircle,
    Settings,
    Calendar,
    Users,
    Sparkles,
    Plus,
    Trash2,
    X,
    ArrowRight
} from 'lucide-react'
import { Chat } from '@/components/ui/chat'
import { Assignment } from '@/types'

interface AssignmentCreatorProps {
    assignment: Assignment,
    onBack: () => void
}

enum Step {
    Setup = 1,
    Questions = 2,
    Overview = 3
}

interface Message {
    role: 'user' | 'assistant'
    content: string
}

interface QuestionBlock {
    id: string
    language: string
    type: 'multiple_choice' | 'single_choice' | 'open'
    level: 'easy' | 'medium' | 'hard'
    estimatedDuration: number // in minutes
    questionNumber: number
    questionText: string
    codeSnippet: string
    options: {
        id: string
        text: string
        isCorrect: boolean
    }[]
    modelAnswer?: string // For open questions
    messages: Message[] // Add messages to each question
    isChatLoading: boolean // Add chat loading state to each question
}

export default function AssignmentCreator({ assignment, onBack }: AssignmentCreatorProps) {
    const [step, setStep] = useState<Step>(Step.Questions)
    const [isGenerating, setIsGenerating] = useState(false)

    // Helper function to get default datetime string (today at 9:00 or 17:00)
    const getDefaultDateTime = (isPublishDate: boolean) => {
        const date = new Date()
        date.setHours(isPublishDate ? 9 : 17, 0, 0, 0) // 9 AM for publish, 5 PM for due date
        return date.toISOString().slice(0, 16) // Format: "YYYY-MM-DDThh:mm"
    }

    const [formData, setFormData] = useState({
        title: assignment.title || '',
        description: assignment.description || '',
        publishDate: getDefaultDateTime(true),
        dueDate: getDefaultDateTime(false),
        topics: assignment.topics || '',
        estimatedTime: assignment.estimatedAnswerDuration || '-'
    })

    const [generatedContent, setGeneratedContent] = useState({
        questions: assignment.questions?.map((question, index) => ({
            id: question.id || crypto.randomUUID(),
            language: question.language || 'python',
            type: question.type || 'single_choice',
            level: question.level || 'medium',
            estimatedDuration: question.estimated_answer_duration || 15,
            questionNumber: index + 1,
            questionText: question.question || '',
            codeSnippet: question.code || '',
            options: question.options?.map(opt => ({
                id: opt.id || crypto.randomUUID(),
                text: opt.text || '',
                isCorrect: opt.is_correct || false
            })) || [
                    { id: crypto.randomUUID(), text: '', isCorrect: false },
                    { id: crypto.randomUUID(), text: '', isCorrect: false },
                    { id: crypto.randomUUID(), text: '', isCorrect: false },
                    { id: crypto.randomUUID(), text: '', isCorrect: false }
                ],
            modelAnswer: question.answer || '',
            messages: [],
            isChatLoading: false
        })) || [] as QuestionBlock[]
    })

    const addNewQuestion = () => {
        const newQuestion: QuestionBlock = {
            id: crypto.randomUUID(),
            language: 'python',
            type: 'single_choice',
            level: 'medium',
            estimatedDuration: 15,
            questionNumber: generatedContent.questions.length + 1,
            questionText: '',
            codeSnippet: '',
            options: [
                { id: crypto.randomUUID(), text: '', isCorrect: false },
                { id: crypto.randomUUID(), text: '', isCorrect: false },
                { id: crypto.randomUUID(), text: '', isCorrect: false },
                { id: crypto.randomUUID(), text: '', isCorrect: false }
            ],
            modelAnswer: '',
            messages: [], // Initialize empty messages array
            isChatLoading: false // Initialize chat loading state
        }

        setGeneratedContent(prev => ({
            ...prev,
            questions: [...prev.questions, newQuestion]
        }))
    }

    const updateQuestion = (questionId: string, updates: Partial<QuestionBlock>) => {
        setGeneratedContent(prev => ({
            ...prev,
            questions: prev.questions.map(q =>
                q.id === questionId ? { ...q, ...updates } : q
            )
        }))
    }

    const addOptionToQuestion = (questionId: string) => {
        setGeneratedContent(prev => ({
            ...prev,
            questions: prev.questions.map(q =>
                q.id === questionId
                    ? {
                        ...q,
                        options: [...q.options, { id: crypto.randomUUID(), text: '', isCorrect: false }]
                    }
                    : q
            )
        }))
    }

    const updateQuestionOption = (questionId: string, optionId: string, updates: Partial<typeof generatedContent.questions[0]['options'][0]>) => {
        setGeneratedContent(prev => ({
            ...prev,
            questions: prev.questions.map(q =>
                q.id === questionId
                    ? {
                        ...q,
                        options: q.options.map(opt =>
                            opt.id === optionId ? { ...opt, ...updates } : opt
                        )
                    }
                    : q
            )
        }))
    }

    const deleteQuestion = (questionId: string) => {
        setGeneratedContent(prev => ({
            ...prev,
            questions: prev.questions.map((q, idx) => ({
                ...q,
                questionNumber: idx + 1
            })).filter(q => q.id !== questionId)
        }))
    }

    const deleteQuestionOption = (questionId: string, optionId: string) => {
        setGeneratedContent(prev => ({
            ...prev,
            questions: prev.questions.map(q =>
                q.id === questionId
                    ? {
                        ...q,
                        options: q.options.filter(opt => opt.id !== optionId)
                    }
                    : q
            )
        }))
    }

    const handleGenerate = async () => {
        setIsGenerating(true)
        // Simulate AI generation delay
        setTimeout(() => {
            setIsGenerating(false)
            setStep(Step.Overview)
            // Add initial assistant message
            setGeneratedContent(prev => ({
                ...prev,
                questions: prev.questions.map(q => ({
                    ...q,
                    messages: [
                        {
                            role: 'assistant',
                            content: 'I\'ve generated the test content. How would you like to refine it? You can ask me to modify the questions, adjust the difficulty, or edit the code example.'
                        }
                    ],
                    isChatLoading: false
                }))
            }))
        }, 3000)
    }

    const handleQuestionChatMessage = async (questionId: string, message: string) => {
        // Update the chat loading state for this specific question
        setGeneratedContent(prev => ({
            ...prev,
            questions: prev.questions.map(q =>
                q.id === questionId ? { ...q, isChatLoading: true } : q
            )
        }))

        // Add user message to this question's chat
        setGeneratedContent(prev => ({
            ...prev,
            questions: prev.questions.map(q =>
                q.id === questionId
                    ? {
                        ...q,
                        messages: [...q.messages, { role: 'user', content: message }]
                    }
                    : q
            )
        }))

        // Simulate AI response
        setTimeout(() => {
            setGeneratedContent(prev => ({
                ...prev,
                questions: prev.questions.map(q =>
                    q.id === questionId
                        ? {
                            ...q,
                            messages: [
                                ...q.messages,
                                {
                                    role: 'assistant',
                                    content: 'I understand you want to modify this question. What specific changes would you like to make to the question or code example?'
                                }
                            ],
                            isChatLoading: false
                        }
                        : q
                )
            }))
        }, 1000)
    }

    const handlePublish = () => {
        // Simulate publishing
        setTimeout(() => {
            onBack()
        }, 1000)
    }

    return (
        <div className="space-y-6">
            {/* Header */}
            <div className="flex items-center space-x-4">
                <div>
                    <h1 className="text-2xl font-bold">Create New Assignment</h1>
                    <p className="text-muted-foreground">AI-powered code comprehension exercise generator</p>
                </div>
            </div>

            {/* Progress Steps */}
            <div className="flex items-center justify-center space-x-4 mb-8">
                <div className={`flex items-center space-x-2 ${step >= Step.Questions ? 'text-blue-600' : 'text-gray-400'}`}>
                    <div className={`w-8 h-8 rounded-full flex items-center justify-center ${step >= Step.Questions ? 'bg-blue-600 text-white' : 'bg-gray-200'}`}>
                        {step > Step.Questions ? <CheckCircle className="h-4 w-4" /> : '1'}
                    </div>
                    <span className="font-medium">Questions</span>
                </div>
                <div className="w-12 h-px bg-gray-300"></div>
                <div className={`flex items-center space-x-2 ${step >= Step.Overview ? 'text-blue-600' : 'text-gray-400'}`}>
                    <div className={`w-8 h-8 rounded-full flex items-center justify-center ${step >= Step.Overview ? 'bg-blue-600 text-white' : 'bg-gray-200'}`}>
                        {step > Step.Overview ? <CheckCircle className="h-4 w-4" /> : '2'}
                    </div>
                    <span className="font-medium">Overview</span>
                </div>
            </div>

            {/* Questions Step */}
            {step === Step.Questions && (
                <div className="grid grid-cols-[1fr,400px] gap-6">
                    {/* Assignment Info Card */}
                    <Card className="mb-6">
                        <CardContent className="p-6 space-y-4">
                            <div className="flex justify-between items-start">
                                <div>
                                    <h2 className="text-2xl font-bold">{formData.title || "Untitled Assignment"}</h2>
                                    <p className="text-muted-foreground mt-1">{formData.description}</p>
                                </div>
                                <div className="text-right">
                                    <p className="text-sm text-muted-foreground">Due Date</p>
                                    <p className="font-medium">{new Date(formData.dueDate).toLocaleDateString('en-GB', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        hour12: false
                                    })}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Questions Column */}
                    <div className="space-y-8">
                        {generatedContent.questions.map((question) => (
                            <div key={question.id} className="flex gap-6">
                                {/* Question Content */}
                                <Card className="flex-1">
                                    <CardContent className="p-6 space-y-4">
                                        <div className="flex justify-between items-start mb-4">
                                            <h3 className="text-lg font-medium">Question {question.questionNumber}</h3>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                className="text-red-600 hover:text-red-700 hover:bg-red-50"
                                                onClick={() => deleteQuestion(question.id)}
                                            >
                                                <Trash2 className="h-4 w-4" />
                                            </Button>
                                        </div>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <Label>Language</Label>
                                                <Select
                                                    value={question.language}
                                                    onValueChange={(value) => updateQuestion(question.id, { language: value })}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="python">Python</SelectItem>
                                                        <SelectItem value="javascript">JavaScript</SelectItem>
                                                        <SelectItem value="java">Java</SelectItem>
                                                        <SelectItem value="cpp">C++</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            <div>
                                                <Label>Question Type</Label>
                                                <Select
                                                    value={question.type}
                                                    onValueChange={(value) => {
                                                        // Reset options array if switching to open question
                                                        if (value === 'open') {
                                                            updateQuestion(question.id, {
                                                                type: value as 'multiple_choice' | 'single_choice' | 'open',
                                                                options: []
                                                            })
                                                        } else {
                                                            // If switching from open to choice question, initialize with 4 empty options
                                                            if (question.type === 'open') {
                                                                updateQuestion(question.id, {
                                                                    type: value as 'multiple_choice' | 'single_choice' | 'open',
                                                                    options: [
                                                                        { id: crypto.randomUUID(), text: '', isCorrect: false },
                                                                        { id: crypto.randomUUID(), text: '', isCorrect: false },
                                                                        { id: crypto.randomUUID(), text: '', isCorrect: false },
                                                                        { id: crypto.randomUUID(), text: '', isCorrect: false }
                                                                    ]
                                                                })
                                                            } else {
                                                                updateQuestion(question.id, {
                                                                    type: value as 'multiple_choice' | 'single_choice' | 'open'
                                                                })
                                                            }
                                                        }
                                                    }}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="single_choice">Single Choice</SelectItem>
                                                        <SelectItem value="multiple_choice">Multiple Choice</SelectItem>
                                                        <SelectItem value="open">Open Question</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            <div>
                                                <Label>Difficulty Level</Label>
                                                <Select
                                                    value={question.level}
                                                    onValueChange={(value) => updateQuestion(question.id, { level: value as 'easy' | 'medium' | 'hard' })}
                                                >
                                                    <SelectTrigger>
                                                        <SelectValue />
                                                    </SelectTrigger>
                                                    <SelectContent>
                                                        <SelectItem value="easy">Easy</SelectItem>
                                                        <SelectItem value="medium">Medium</SelectItem>
                                                        <SelectItem value="hard">Hard</SelectItem>
                                                    </SelectContent>
                                                </Select>
                                            </div>
                                            <div>
                                                <Label>Estimated Duration (minutes)</Label>
                                                <Input
                                                    type="number"
                                                    min="1"
                                                    value={question.estimatedDuration}
                                                    onChange={(e) => updateQuestion(question.id, { estimatedDuration: parseInt(e.target.value) })}
                                                />
                                            </div>
                                        </div>

                                        <div>
                                            <Label>Question {question.questionNumber}</Label>
                                            <Textarea
                                                value={question.questionText}
                                                onChange={(e) => updateQuestion(question.id, { questionText: e.target.value })}
                                                placeholder="Enter your question here..."
                                                className="mt-1"
                                            />
                                        </div>

                                        <div>
                                            <Label>Code Snippet</Label>
                                            <Textarea
                                                value={question.codeSnippet}
                                                onChange={(e) => updateQuestion(question.id, { codeSnippet: e.target.value })}
                                                placeholder="Enter code snippet here..."
                                                className="mt-1 font-mono"
                                                rows={6}
                                            />
                                        </div>

                                        {question.type === 'open' ? (
                                            <div>
                                                <Label>Model Answer / Grading Rubric</Label>
                                                <Textarea
                                                    value={question.modelAnswer || ''}
                                                    onChange={(e) => updateQuestion(question.id, { modelAnswer: e.target.value })}
                                                    placeholder="Enter the model answer or grading rubric for this open question..."
                                                    className="mt-1"
                                                    rows={6}
                                                />
                                            </div>
                                        ) : (
                                            <div className="space-y-2">
                                                <div className="flex items-center justify-between">
                                                    <Label>Answer Options</Label>
                                                    <Button
                                                        onClick={() => addOptionToQuestion(question.id)}
                                                        variant="outline"
                                                        size="sm"
                                                        disabled={question.options.length >= 6}
                                                    >
                                                        Add Option
                                                    </Button>
                                                </div>
                                                {question.options.map((option, optIndex) => (
                                                    <div key={option.id} className="flex items-center space-x-2">
                                                        <div className="flex-none">
                                                            {question.type === 'single_choice' ? (
                                                                <input
                                                                    type="radio"
                                                                    checked={option.isCorrect}
                                                                    onChange={() => {
                                                                        question.options.forEach(opt => {
                                                                            if (opt.id !== option.id) {
                                                                                updateQuestionOption(question.id, opt.id, { isCorrect: false })
                                                                            }
                                                                        })
                                                                        updateQuestionOption(question.id, option.id, { isCorrect: true })
                                                                    }}
                                                                    className="mr-2"
                                                                />
                                                            ) : (
                                                                <input
                                                                    type="checkbox"
                                                                    checked={option.isCorrect}
                                                                    onChange={(e) => updateQuestionOption(question.id, option.id, { isCorrect: e.target.checked })}
                                                                    className="mr-2"
                                                                />
                                                            )}
                                                        </div>
                                                        <Input
                                                            value={option.text}
                                                            onChange={(e) => updateQuestionOption(question.id, option.id, { text: e.target.value })}
                                                            placeholder={`Option ${optIndex + 1}`}
                                                            className="flex-1"
                                                        />
                                                        <Button
                                                            variant="ghost"
                                                            size="sm"
                                                            className="text-red-600 hover:text-red-700 hover:bg-red-50"
                                                            onClick={() => deleteQuestionOption(question.id, option.id)}
                                                            disabled={question.options.length <= 2}
                                                        >
                                                            <X className="h-4 w-4" />
                                                        </Button>
                                                    </div>
                                                ))}
                                            </div>
                                        )}
                                    </CardContent>
                                </Card>

                                {/* Chat Window */}
                                <Card className="w-[400px] flex flex-col">
                                    <CardHeader className="pb-2">
                                        <div className="flex items-center justify-between">
                                            <CardTitle className="text-sm">Question {question.questionNumber} Assistant</CardTitle>
                                            <Badge variant="outline">{question.language}</Badge>
                                        </div>
                                    </CardHeader>
                                    <CardContent className="flex-1 p-4 pt-0">
                                        <div className="h-full">
                                            <Chat
                                                messages={question.messages}
                                                onSendMessage={(message) => handleQuestionChatMessage(question.id, message)}
                                                isLoading={question.isChatLoading}
                                                allowTextEdit={false}
                                                editableText=""
                                                onTextEdit={undefined}
                                            />
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>
                        ))}

                        <Button
                            onClick={addNewQuestion}
                            variant="outline"
                            className="w-full"
                        >
                            <Plus className="h-4 w-4 mr-2" />
                            Add Question
                        </Button>
                    </div>

                    <div className="flex justify-end mt-6">
                        <Button
                            onClick={() => setStep(Step.Overview)}
                            className="bg-blue-600 hover:bg-blue-700"
                            disabled={
                                generatedContent.questions.length === 0 || // No questions
                                generatedContent.questions.some(q => !q.questionText.trim()) || // Empty question text
                                generatedContent.questions.some(q => !q.codeSnippet.trim()) || // Empty code snippet
                                generatedContent.questions.some(q =>
                                    q.type !== 'open' && // Only check options for multiple/single choice questions
                                    (
                                        q.options.some(opt => !opt.text.trim()) || // Empty option text
                                        !q.options.some(opt => opt.isCorrect) // No correct answer selected
                                    )
                                ) ||
                                generatedContent.questions.some(q =>
                                    q.type === 'open' && // Only check model answer for open questions
                                    (!q.modelAnswer || !q.modelAnswer.trim()) // Empty model answer/grading rubric
                                )
                            }
                        >
                            Next: Overview
                            <ArrowRight className="h-4 w-4 ml-2" />
                        </Button>
                    </div>
                </div>
            )}

            {/* Step 3: Overview */}
            {step === Step.Overview && (
                <div className="max-w-4xl mx-auto">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center">
                                <FileText className="h-5 w-5 mr-2" />
                                Assignment Overview
                            </CardTitle>
                            <CardDescription>
                                Review your assignment details
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-6">
                                <div className="bg-blue-50 p-6 rounded-lg">
                                    <h3 className="font-medium mb-4">Assignment Details:</h3>
                                    <div className="space-y-3">
                                        <div className="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span className="text-muted-foreground">Title:</span>
                                                <p className="font-medium">{formData.title}</p>
                                            </div>
                                            <div>
                                                <span className="text-muted-foreground">Status:</span>
                                                <p className="font-medium">Draft</p>
                                            </div>
                                            <div>
                                                <span className="text-muted-foreground">Opens:</span>
                                                <p className="font-medium">{new Date(formData.publishDate).toLocaleDateString('en-GB', {
                                                    day: '2-digit',
                                                    month: '2-digit',
                                                    year: 'numeric',
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                    hour12: false
                                                })}</p>
                                            </div>
                                            <div>
                                                <span className="text-muted-foreground">Due:</span>
                                                <p className="font-medium">{new Date(formData.dueDate).toLocaleDateString('en-GB', {
                                                    day: '2-digit',
                                                    month: '2-digit',
                                                    year: 'numeric',
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                    hour12: false
                                                })}</p>
                                            </div>
                                        </div>
                                        {formData.description && (
                                            <div className="text-sm">
                                                <span className="text-muted-foreground block">Description:</span>
                                                <p className="font-medium">{formData.description}</p>
                                            </div>
                                        )}
                                        {formData.topics && (
                                            <div className="text-sm">
                                                <span className="text-muted-foreground block">Topics:</span>
                                                <p className="font-medium">{formData.topics}</p>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                <div>
                                    <h3 className="font-medium mb-4">Questions Summary:</h3>
                                    <div className="space-y-4">
                                        {generatedContent.questions.map((question, index) => (
                                            <div key={question.id} className="border rounded-lg p-4">
                                                <div className="flex items-center justify-between mb-2">
                                                    <h4 className="font-medium">Question {index + 1}</h4>
                                                    <div className="flex items-center space-x-2">
                                                        <Badge variant="outline">{question.language}</Badge>
                                                        <Badge variant="secondary">{question.level}</Badge>
                                                        <Badge>{question.type}</Badge>
                                                    </div>
                                                </div>
                                                <p className="text-sm mb-2">{question.questionText}</p>
                                                {question.codeSnippet && (
                                                    <div className="bg-gray-900 text-gray-100 p-3 rounded text-xs font-mono mb-2">
                                                        <pre>{question.codeSnippet.split('\n').slice(0, 3).join('\n')}
                                                            {question.codeSnippet.split('\n').length > 3 ? '...' : ''}</pre>
                                                    </div>
                                                )}
                                                <div className="text-sm text-muted-foreground">
                                                    {question.type === 'open' ? (
                                                        <p>Open question with model answer</p>
                                                    ) : (
                                                        <p>{question.options.length} options, {question.options.filter(opt => opt.isCorrect).length} correct</p>
                                                    )}
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <div className="flex justify-between mt-6">
                        <Button variant="outline" onClick={() => setStep(Step.Questions)}>
                            <ArrowLeft className="h-4 w-4 mr-2" />
                            Back
                        </Button>
                        <Button onClick={handlePublish} className="bg-green-600 hover:bg-green-700">
                            <CheckCircle className="h-4 w-4 mr-2" />
                            Save Assignment
                        </Button>
                    </div>
                </div>
            )}
        </div>
    )
}
