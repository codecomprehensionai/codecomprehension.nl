import { LucideIcon } from 'lucide-react';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    type: 'student' | 'teacher';
    name: string;
    email: string;
    avatar?: string;
}

export interface Course {
    ltiId: number;
    title: string;
}

export interface Submission {
    id: number;
    answer: number | string;
    is_correct: boolean;
    feedback?: string;
}

export interface Option {
    id: string;
    text: string;
    is_correct?: boolean;
}

export interface Question {
    id: number | string;
    assignment_id?: number;
    level: 'beginner' | 'intermediate' | 'advanced' | 'expert';
    type: 'code_explanation' | 'multiple_choice' | 'fill_in_the_blanks' | 'single_choice' | 'open';
    language: string;
    topic?: string;
    tags?: string[];
    estimated_answer_duration: number;
    question_number?: number;
    question: string;
    explanation?: string;
    answer?: string;
    code: string;
    options: Array<Option>;
    created_at?: string;
    updated_at?: string;
}

export interface QuestionBlock {
    question: Question;
    messages?: Array<{
        role: 'user' | 'assistant';
        content: string;
    }>;
    isChatLoading?: boolean;
}

export interface Assignment {
    id: number;
    title: string;
    description: string;
    language: string;
    difficulty: string;
    dueDate: string;
    status: "submitted" | "in_progress" | "not_started";
    score: number | null;
    questions: Question[];
    timeSpent: string;
    submittedAt?: string;
    progress?: number;
    topics?: string | string[];
    estimatedAnswerDuration?: number;
    published_at?: string;
    deadline_at?: string;
}
export interface PageProps extends InertiaPageProps {
    auth: {
      user: User;
    };
    [key: string]: any;
}
