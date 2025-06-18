'use client'

import { useState } from 'react'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from './card'
import { Button } from './button'
import { Input } from './input'
import { Textarea } from './textarea'
import { ScrollArea } from './scroll-area'
import { MessageCircle, Send } from 'lucide-react'

interface Message {
  role: 'user' | 'assistant'
  content: string
}

interface ChatProps {
  onSendMessage: (message: string) => Promise<void>
  messages: Message[]
  isLoading?: boolean
  allowTextEdit?: boolean
  onTextEdit?: (text: string) => void
  editableText?: string
}

export function Chat({ 
  onSendMessage, 
  messages, 
  isLoading = false, 
  allowTextEdit = false,
  onTextEdit,
  editableText
}: ChatProps) {
  const [input, setInput] = useState('')

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    if (!input.trim() || isLoading) return

    await onSendMessage(input)
    setInput('')
  }

  return (
    <Card className="h-full flex flex-col">
      <CardHeader>
        <CardTitle className="flex items-center">
          <MessageCircle className="h-5 w-5 mr-2" />
          AI Assistant
        </CardTitle>
        <CardDescription>
          Chat with our AI to refine the test content
        </CardDescription>
      </CardHeader>
      <CardContent className="flex-1 flex flex-col">
        {allowTextEdit && (
          <div className="mb-4">
            <Textarea
              value={editableText}
              onChange={(e) => onTextEdit?.(e.target.value)}
              placeholder="Edit the generated content..."
              className="min-h-[200px]"
            />
          </div>
        )}
        
        <ScrollArea className="flex-1">
          <div className="space-y-4 pr-4 h-[300px]">
            {messages.map((message, index) => (
              <div
                key={index}
                className={`flex ${message.role === 'user' ? 'justify-end' : 'justify-start'}`}
              >
                <div
                  className={`rounded-lg px-4 py-2 max-w-[80%] ${
                    message.role === 'user'
                      ? 'bg-blue-600 text-white'
                      : 'bg-gray-100 text-gray-900'
                  }`}
                >
                  {message.content}
                </div>
              </div>
            ))}
            {isLoading && (
              <div className="flex justify-start">
                <div className="bg-gray-100 rounded-lg px-4 py-2">
                  <div className="flex space-x-2">
                    <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce" />
                    <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:0.2s]" />
                    <div className="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:0.4s]" />
                  </div>
                </div>
              </div>
            )}
          </div>
        </ScrollArea>
        
        <form onSubmit={handleSubmit} className="mt-4 flex gap-2">
          <Input
            value={input}
            onChange={(e) => setInput(e.target.value)}
            placeholder="Type your message..."
            disabled={isLoading}
          />
          <Button type="submit" disabled={isLoading}>
            <Send className="h-4 w-4" />
          </Button>
        </form>
      </CardContent>
    </Card>
  )
} 