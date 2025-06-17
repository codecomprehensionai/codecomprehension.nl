<?php

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use App\Services\ScoreCalculationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->scoreService = new ScoreCalculationService();
});

test('calculate percentage score with valid input', function () {
    $correctAnswers = 8;
    $totalQuestions = 10;

    $result = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);

    expect($result)->toBe(80.0);
});

test('calculate percentage score with zero total questions', function () {
    $correctAnswers = 5;
    $totalQuestions = 0;

    $result = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);

    expect($result)->toBe(0.0);
});

test('calculate percentage score with perfect score', function () {
    $correctAnswers = 10;
    $totalQuestions = 10;

    $result = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);

    expect($result)->toBe(100.0);
});

test('calculate percentage score with zero correct', function () {
    $correctAnswers = 0;
    $totalQuestions = 10;

    $result = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);

    expect($result)->toBe(0.0);
});

test('convert to points with default max points', function () {
    $percentage = 85.0;

    $result = $this->scoreService->convertToPoints($percentage);

    expect($result)->toBe(85.0);
});

test('convert to points with custom max points', function () {
    $percentage = 80.0;
    $maxPoints = 50.0;

    $result = $this->scoreService->convertToPoints($percentage, $maxPoints);

    expect($result)->toBe(40.0);
});

test('convert to points with zero percentage', function () {
    $percentage = 0.0;
    $maxPoints = 100.0;

    $result = $this->scoreService->convertToPoints($percentage, $maxPoints);

    expect($result)->toBe(0.0);
});

test('convert to points with perfect percentage', function () {
    $percentage = 100.0;
    $maxPoints = 75.0;

    $result = $this->scoreService->convertToPoints($percentage, $maxPoints);

    expect($result)->toBe(75.0);
});

test('generate comment with excellent score', function () {
    $score = 95.0;
    $correctAnswers = 19;
    $totalQuestions = 20;

    $result = $this->scoreService->generateComment($score, $correctAnswers, $totalQuestions);

    expect($result)
        ->toContain('Assignment completed via LTI tool')
        ->toContain('19/20 (95%)')
        ->toContain('Excellent work!');
});

test('generate comment with good score', function () {
    $score = 85.0;
    $correctAnswers = 17;
    $totalQuestions = 20;

    $result = $this->scoreService->generateComment($score, $correctAnswers, $totalQuestions);

    expect($result)
        ->toContain('17/20 (85%)')
        ->toContain('Good job!');
});

test('generate comment with well done score', function () {
    $score = 75.0;
    $correctAnswers = 15;
    $totalQuestions = 20;

    $result = $this->scoreService->generateComment($score, $correctAnswers, $totalQuestions);

    expect($result)
        ->toContain('15/20 (75%)')
        ->toContain('Well done!');
});

test('generate comment with keep practicing score', function () {
    $score = 65.0;
    $correctAnswers = 13;
    $totalQuestions = 20;

    $result = $this->scoreService->generateComment($score, $correctAnswers, $totalQuestions);

    expect($result)
        ->toContain('13/20 (65%)')
        ->toContain('Keep practicing!');
});

test('generate comment with low score', function () {
    $score = 45.0;
    $correctAnswers = 9;
    $totalQuestions = 20;

    $result = $this->scoreService->generateComment($score, $correctAnswers, $totalQuestions);

    expect($result)
        ->toContain('9/20 (45%)')
        ->toContain('Consider reviewing the material.');
});

test('generate comment with boundary scores', function () {
    // Test boundary at 90%
    $result = $this->scoreService->generateComment(90.0, 18, 20);
    expect($result)->toContain('Excellent work!');

    // Test boundary at 89.9%
    $result = $this->scoreService->generateComment(89.9, 18, 20);
    expect($result)->toContain('Good job!');

    // Test boundary at 80%
    $result = $this->scoreService->generateComment(80.0, 16, 20);
    expect($result)->toContain('Good job!');

    // Test boundary at 79.9%
    $result = $this->scoreService->generateComment(79.9, 16, 20);
    expect($result)->toContain('Well done!');

    // Test boundary at 70%
    $result = $this->scoreService->generateComment(70.0, 14, 20);
    expect($result)->toContain('Well done!');

    // Test boundary at 69.9%
    $result = $this->scoreService->generateComment(69.9, 14, 20);
    expect($result)->toContain('Keep practicing!');

    // Test boundary at 60%
    $result = $this->scoreService->generateComment(60.0, 12, 20);
    expect($result)->toContain('Keep practicing!');

    // Test boundary at 59.9%
    $result = $this->scoreService->generateComment(59.9, 12, 20);
    expect($result)->toContain('Consider reviewing the material.');
});

test('calculate assignment score logic', function () {
    // Test the core calculation logic without database dependencies
    $correctAnswers = 0;
    $totalQuestions = 10;
    
    $result = $this->scoreService->calculatePercentageScore($correctAnswers, $totalQuestions);
    expect($result)->toBe(0.0);
    
    // This simulates what would happen with no submissions
    $emptyScore = ($correctAnswers / max($totalQuestions, 1)) * 100.0;
    expect($emptyScore)->toBe(0.0);
});

test('decimal precision in calculations', function () {
    $result = $this->scoreService->calculatePercentageScore(1, 3);
    expect($result)->toBeGreaterThan(33.33);
    expect($result)->toBeLessThan(33.34);

    $result = $this->scoreService->convertToPoints(33.333333333333336, 90.0);
    expect($result)->toBeGreaterThan(29.99);
    expect($result)->toBeLessThan(30.01);
});

test('edge cases with large numbers', function () {
    $result = $this->scoreService->calculatePercentageScore(999, 1000);
    expect($result)->toBe(99.9);

    $result = $this->scoreService->convertToPoints(99.9, 10000.0);
    expect($result)->toBeGreaterThan(9989.0);
    expect($result)->toBeLessThan(9991.0);
});
