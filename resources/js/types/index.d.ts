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

export interface Question {
    id: number;
    assignment_id: number;
    language: string;
    type: string;
    level: string;
    estimated_answer_duration: number;
    topic: string;
    tags: string[];
    question: string;
    explanation?: string;
    code: string;
    options: any[];
    answer?: string;
    submissions?: Submission[];
    created_at: string;
    updated_at: string;
}

export interface Assignment {
  id: number
  title: string
  description: string
  language: string
  difficulty: string
  dueDate: string
  status: "submitted" | "in_progress" | "not_started"
  score: number | null
  questions: Question[]
  timeSpent: string
  submittedAt?: string
  progress?: number
  topics?: string[]
  estimatedAnswerDuration?: number
}

export interface PageProps extends InertiaPageProps {
    auth: {
      user: User;
    };
    [key: string]: any;
}
