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
  Plus,
  Trash2,
  X,
  CheckCircle
} from 'lucide-react'
import { Chat } from '@/components/ui/chat'

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
  messages: Message[] // Chat messages for this question
  isChatLoading: boolean // Chat loading state for this question
}

interface Assignment {
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
  description?: string
  topics?: string
  estimatedTime?: string
  questions?: Array<{
    id: number
    question: string
    options: string[]
    correct: number
    explanation: string
  }>
}

interface AssignmentEditProps {
  assignment: Assignment
  onBack: () => void
}

export default function AssignmentEdit({ assignment, onBack }: AssignmentEditProps) {
  // Convert the old question format to the new QuestionBlock format
  const convertOldQuestions = (oldQuestions: Assignment['questions'] = []): QuestionBlock[] => {
    return [
      {
        id: crypto.randomUUID(),
        language: 'python',
        type: 'single_choice',
        level: 'medium',
        estimatedDuration: 10,
        questionNumber: 1,
        questionText: "What is the time complexity of this recursive Fibonacci implementation?",
        codeSnippet: `def fibonacci(n):
    """
    Generate the nth Fibonacci number using recursion.

    Args:
        n (int): The position in the Fibonacci sequence

    Returns:
        int: The nth Fibonacci number
    """
    if n <= 1:
        return n
    return fibonacci(n-1) + fibonacci(n-2)

# Test the function
result = fibonacci(10)
print(f"The 10th Fibonacci number is: {result}")`,
        options: [
          { id: crypto.randomUUID(), text: "O(n)", isCorrect: false },
          { id: crypto.randomUUID(), text: "O(n²)", isCorrect: false },
          { id: crypto.randomUUID(), text: "O(2^n)", isCorrect: true },
          { id: crypto.randomUUID(), text: "O(log n)", isCorrect: false }
        ],
        modelAnswer: "This recursive implementation has exponential time complexity O(2^n) because it recalculates the same values multiple times.",
        messages: [],
        isChatLoading: false
      },
      {
        id: crypto.randomUUID(),
        language: 'python',
        type: 'single_choice',
        level: 'easy',
        estimatedDuration: 5,
        questionNumber: 2,
        questionText: "What will happen if you call fibonacci(-1)?",
        codeSnippet: "",  // Using the same code from above
        options: [
          { id: crypto.randomUUID(), text: "Return 0", isCorrect: false },
          { id: crypto.randomUUID(), text: "Return -1", isCorrect: true },
          { id: crypto.randomUUID(), text: "Infinite recursion", isCorrect: false },
          { id: crypto.randomUUID(), text: "Runtime error", isCorrect: false }
        ],
        modelAnswer: "The function will return -1 because the base case n <= 1 will be true, and the function returns n.",
        messages: [],
        isChatLoading: false
      },
      {
        id: crypto.randomUUID(),
        language: 'python',
        type: 'multiple_choice',
        level: 'hard',
        estimatedDuration: 15,
        questionNumber: 3,
        questionText: "How could you optimize this function?",
        codeSnippet: "",  // Using the same code from above
        options: [
          { id: crypto.randomUUID(), text: "Use iteration", isCorrect: true },
          { id: crypto.randomUUID(), text: "Use memoization", isCorrect: true },
          { id: crypto.randomUUID(), text: "Use dynamic programming", isCorrect: true },
          { id: crypto.randomUUID(), text: "All of the above", isCorrect: true }
        ],
        modelAnswer: "All these approaches can optimize the function by avoiding redundant calculations. Iteration eliminates recursion overhead, memoization caches results, and dynamic programming builds solutions bottom-up.",
        messages: [],
        isChatLoading: false
      }
    ]
  }

  const [generatedContent, setGeneratedContent] = useState({
    questions: convertOldQuestions([])  // We're ignoring the passed questions and using our static data
  })

  const addNewQuestion = () => {
    const newQuestion: QuestionBlock = {
      id: crypto.randomUUID(),
      language: assignment.language.toLowerCase(),
      type: 'single_choice',
      level: assignment.difficulty.toLowerCase() as 'easy' | 'medium' | 'hard',
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
      messages: [],
      isChatLoading: false
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

  const handleUpdate = () => {
    // TODO: Implement update logic
    setTimeout(() => {
      onBack()
    }, 1000)
  }

  return (
    <div className="space-y-6">
      {/* Header */}
      <div className="flex items-center space-x-4">
        <Button variant="ghost" onClick={onBack}>
          <ArrowLeft className="h-4 w-4 mr-2" />
          Back to Dashboard
        </Button>
        <div>
          <h1 className="text-2xl font-bold">Edit Assignment</h1>
          <p className="text-muted-foreground">Modify your code comprehension exercise</p>
        </div>
      </div>

      {/* Questions Section */}
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
                        if (value === 'open') {
                          updateQuestion(question.id, {
                            type: value as 'multiple_choice' | 'single_choice' | 'open',
                            options: []
                          })
                        } else {
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
                    {question.options.map((option) => (
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

        <div className="flex justify-end">
          <Button
            onClick={handleUpdate}
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
            Update Assignment
            <CheckCircle className="h-4 w-4 ml-2" />
          </Button>
        </div>
      </div>
    </div>
  )
}