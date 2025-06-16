<?php

namespace App\Services;

use App\Models\Submission;
use App\Models\Assignment;
use App\Models\User;

class ScoreCalculationService
{
    /**
     * Calculate score based on user submissions for an assignment
     */
    public function calculateAssignmentScore(User $user, Assignment $assignment): float
    {
        $submissions = Submission::where('student_id', $user->id)
            ->whereHas('assignment', function ($query) use ($assignment) {
                $query->where('id', $assignment->id);
            })
            ->get();

        if ($submissions->isEmpty()) {
            return 0.0;
        }

        $totalQuestions = $submissions->count();
        $correctAnswers = $submissions->where('correct_answer', 1)->count();

        return ($correctAnswers / $totalQuestions) * 100;
    }

    /**
     * Calculate percentage score from correct/total answers
     */
    public function calculatePercentageScore(int $correctAnswers, int $totalQuestions): float
    {
        if ($totalQuestions === 0) {
            return 0.0;
        }

        return ($correctAnswers / $totalQuestions) * 100;
    }

    /**
     * Convert percentage to points based on assignment max points
     */
    public function convertToPoints(float $percentage, float $maxPoints = 100.0): float
    {
        return ($percentage / 100) * $maxPoints;
    }

    /**
     * Generate a performance-based comment
     */
    public function generateComment(float $score, int $correctAnswers, int $totalQuestions): string
    {
        $percentage = round($score, 1);
        
        $comment = "Assignment completed via LTI tool.\n";
        $comment .= "Score: {$correctAnswers}/{$totalQuestions} ({$percentage}%)\n";
        
        if ($percentage >= 90) {
            $comment .= "Excellent work!";
        } elseif ($percentage >= 80) {
            $comment .= "Good job!";
        } elseif ($percentage >= 70) {
            $comment .= "Well done!";
        } elseif ($percentage >= 60) {
            $comment .= "Keep practicing!";
        } else {
            $comment .= "Consider reviewing the material.";
        }
        
        return $comment;
    }
}
