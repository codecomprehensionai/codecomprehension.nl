<?php

namespace App\Livewire;

use App\Models\Assignment;
use App\Models\Submission;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AssignmentResults extends Component
{
    public $assignmentId;
    public $assignment;
    public $submissions;
    public $score = 0;
    public $totalQuestions = 0;
    public $correctAnswers = 0;
    public $timeSpent = 0;
    public $classRank = 0;
    public $feedback = '';

    public function mount($assignment)
    {
        $this->assignmentId = $assignment;
        $this->loadAssignmentData();
    }

    public function loadAssignmentData()
    {
        $this->assignment = Assignment::with('questions')->findOrFail($this->assignmentId);

        // Get all user's submissions for this assignment's questions
        $this->submissions = Submission::whereIn('question_id', $this->assignment->questions->pluck('id'))
            ->where('user_id', Auth::id())
            ->with('question')
            ->get();

        if ($this->submissions->isNotEmpty()) {
            $this->calculateResults();
        }
    }

    private function calculateResults()
    {
        $this->totalQuestions = $this->assignment->questions->count();
        $this->correctAnswers = $this->submissions->where('is_correct', true)->count();

        // Calculate score percentage
        $this->score = $this->totalQuestions > 0
            ? round(($this->correctAnswers / $this->totalQuestions) * 100)
            : 0;

        // Calculate time spent based on first and last submission
        if ($this->submissions->count() > 0) {
            $firstSubmission = $this->submissions->sortBy('created_at')->first();
            $lastSubmission = $this->submissions->sortByDesc('created_at')->first();

            if ($firstSubmission && $lastSubmission) {
                $this->timeSpent = $firstSubmission->created_at->diffInMinutes($lastSubmission->created_at);
            }
        }

        // Calculate class rank (simplified - count better performing students)
        $userCorrectCount = $this->correctAnswers;
        $questionIds = $this->assignment->questions->pluck('id');

        // Get all users who have more correct answers for this assignment
        $betterUsers =  Submission::correctCountsByUser($questionIds)
            ->where('user_id', '!=', Auth::id())
            ->havingRaw('COUNT(*) > ?', [$userCorrectCount])
            ->count();

        $this->classRank = $betterUsers + 1;

        // Generate feedback based on performance
        $this->generateFeedback();
    }

    private function generateFeedback()
    {
        if ($this->score >= 90) {
            $this->feedback = "Outstanding work! You've demonstrated excellent code comprehension skills.";
        } elseif ($this->score >= 75) {
            $this->feedback = "Great job! You have a solid understanding of the concepts.";
        } elseif ($this->score >= 60) {
            $this->feedback = "Good effort! Consider reviewing the areas where you had difficulty.";
        } else {
            $this->feedback = "Keep practicing! Review the concepts and try similar exercises.";
        }
    }

    public function render()
    {
        return view('livewire.assignment-results')->layout('components.layouts.app');
    }
}
