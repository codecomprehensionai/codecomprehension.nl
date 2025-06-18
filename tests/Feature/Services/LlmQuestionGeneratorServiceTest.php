<?php

use App\Services\LlmQuestionGeneratorService;
use App\Models\Assignment;
use App\Models\Question;
use App\Data\QuestionData;
use App\Enums\QuestionLanguage;
use App\Enums\QuestionType;
use App\Enums\QuestionLevel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// ============================================================================
// UNIT TESTS - Test LlmQuestionGeneratorService methods
// ============================================================================

test('generate question success with mock', function () {
    // Set up test configuration
    config(['llm.base_url' => 'https://test-llm-api.com']);
    config(['llm.timeout' => 30]);
    
    $service = new LlmQuestionGeneratorService();
    
    $assignment = Assignment::factory()->make([
        'id' => 1,
        'title' => 'Python Basics',
        'description' => 'Basic Python concepts'
    ]);
    
    $questionParams = [
        'language' => 'python',
        'type' => 'multiple_choice',
        'level' => 'beginner',
        'estimated_answer_duration' => 180,
        'topics' => ['variables'],
        'tags' => ['python', 'basics']
    ];
    
    $prompt = 'Create a question about Python variables';
    
    // Mock response matching the actual LLM API format
    $mockResponse = [
        'success' => true,
        'data' => [
            'assignment' => [
                'id' => '1',
                'title' => 'Python Basics'
            ],
            'question' => [
                'language' => 'Python',
                'type' => 'multiple_choice',
                'level' => 'beginner',
                'estimated_answer_duration' => '3 minutes',
                'topics' => ['Variables and data types'],
                'tags' => ['python', 'basics'],
                'question' => 'What is a variable in Python?',
                'explanation' => 'A variable is a named reference to a value',
                'code' => null,
                'options' => ['A container', 'A function', 'A class', 'A module'],
                'answer' => 'A container'
            ],
            'summary' => 'Generated question about Python variables'
        ],
        'error' => null,
        'execution_time_seconds' => 1.5
    ];
    
    // Mock HTTP response
    Http::fake([
        'https://test-llm-api.com/question' => Http::response($mockResponse, 200)
    ]);
    
    // Mock log calls
    Log::shouldReceive('info')->atLeast()->once();
    Log::shouldReceive('error')->never();
    
    $result = $service->generateQuestion($assignment, $questionParams, $prompt);
    
    expect($result)->toBeInstanceOf(QuestionData::class);
    expect($result->language)->toBe(QuestionLanguage::Python);
    expect($result->type)->toBe(QuestionType::MultipleChoice);
    expect($result->level)->toBe(QuestionLevel::Beginner);
    expect($result->question)->toBe('What is a variable in Python?');
    
    Http::assertSent(function ($request) {
        return $request->url() === 'https://test-llm-api.com/question' &&
               $request->method() === 'POST';
    });
});

test('generate question handles http error', function () {
    config(['llm.base_url' => 'https://test-llm-api.com']);
    config(['llm.timeout' => 30]);
    
    $service = new LlmQuestionGeneratorService();
    
    $assignment = Assignment::factory()->make([
        'id' => 1,
        'title' => 'Python Basics'
    ]);
    
    $questionParams = ['language' => 'python'];
    $prompt = 'Test prompt';
    
    // Mock HTTP error response
    Http::fake([
        'https://test-llm-api.com/question' => Http::response(['error' => 'Internal Server Error'], 500)
    ]);
    
    Log::shouldReceive('info')->once();
    Log::shouldReceive('error')->once();
    
    $result = $service->generateQuestion($assignment, $questionParams, $prompt);
    
    expect($result)->toBeNull();
});

test('update question success with mock', function () {
    config(['llm.base_url' => 'https://test-llm-api.com']);
    config(['llm.timeout' => 30]);
    
    $service = new LlmQuestionGeneratorService();
    
    $assignment = Assignment::factory()->make([
        'id' => 1,
        'title' => 'Python Basics'
    ]);
    
    $question = Question::factory()->make([
        'id' => 10,
        'assignment_id' => $assignment->id,
        'language' => QuestionLanguage::Python,
        'type' => QuestionType::MultipleChoice,
        'level' => QuestionLevel::Beginner,
        'question' => 'Original question',
        'explanation' => 'Original explanation',
        'options' => ['A', 'B', 'C', 'D'],
        'answer' => 'A'
    ]);
    
    $updateParams = ['level' => 'intermediate'];
    $prompt = 'Make this question more challenging';
    
    $mockResponse = [
        'success' => true,
        'data' => [
            'assignment' => [
                'id' => '1',
                'title' => 'Python Basics'
            ],
            'question' => [
                'language' => 'Python',
                'type' => 'multiple_choice',
                'level' => 'intermediate',
                'estimated_answer_duration' => '5 minutes',
                'topics' => ['Advanced concepts'],
                'tags' => ['python'],
                'question' => 'Updated challenging question',
                'explanation' => 'Updated detailed explanation',
                'code' => null,
                'options' => ['A', 'B', 'C', 'D'],
                'answer' => 'B'
            ],
            'summary' => 'Updated question to intermediate level'
        ],
        'error' => null,
        'execution_time_seconds' => 2.3
    ];
    
    Http::fake([
        'https://test-llm-api.com/question' => Http::response($mockResponse, 200)
    ]);
    
    Log::shouldReceive('info')->atLeast()->once();
    Log::shouldReceive('error')->never();
    
    $result = $service->updateQuestion($assignment, $question, $updateParams, $prompt);
    
    expect($result)->toBeInstanceOf(QuestionData::class);
    expect($result->level)->toBe(QuestionLevel::Intermediate);
    expect($result->question)->toBe('Updated challenging question');
    
    Http::assertSent(function ($request) {
        return $request->url() === 'https://test-llm-api.com/question' &&
               $request->method() === 'PUT' &&
               $request['existing_question']['id'] === '10' &&
               $request['update_question']['level'] === 'intermediate';
    });
});

test('handles network timeout gracefully', function () {
    config(['llm.base_url' => 'https://test-llm-api.com']);
    config(['llm.timeout' => 30]);
    
    $service = new LlmQuestionGeneratorService();
    
    $assignment = Assignment::factory()->make([
        'id' => 1,
        'title' => 'Python Basics'
    ]);
    $questionParams = ['language' => 'python'];
    $prompt = 'Test prompt';
    
    // Mock a timeout exception
    Http::fake(function () {
        throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
    });
    
    Log::shouldReceive('info')->once();
    Log::shouldReceive('error')->once();
    
    $result = $service->generateQuestion($assignment, $questionParams, $prompt);
    
    expect($result)->toBeNull();
});

// ============================================================================
// INTEGRATION TESTS - Test against real LLM API on localhost
// ============================================================================

test('integration: health check works with real LLM service', function () {
    config(['llm.base_url' => 'http://localhost:8000']);
    config(['llm.timeout' => 30]);
    
    Http::allowStrayRequests();
    $service = new LlmQuestionGeneratorService();
    
    $result = $service->isAvailable();
    
    expect($result)->toBeTrue('LLM service should be available on localhost:8000. Make sure it is running.');
    
    // Also test that the health endpoint returns expected data
    $response = Http::get('http://localhost:8000/health');
    expect($response->successful())->toBeTrue();
    
})->group('integration');

test('integration: generate question with real LLM service', function () {
    config(['llm.base_url' => 'http://localhost:8000']);
    config(['llm.timeout' => 90]); // Increase timeout for slow AI processing
    
    Http::allowStrayRequests();
    $service = new LlmQuestionGeneratorService();
    
    $assignment = Assignment::factory()->make([
        'id' => 1,
        'title' => 'Python Basics',
        'description' => 'Learn basic Python programming concepts'
    ]);
    
    $questionParams = [
        'language' => 'python',
        'type' => 'multiple_choice',
        'level' => 'beginner',
        'estimated_answer_duration' => 180,
        'topics' => ['variables'],
        'tags' => ['python', 'basics']
    ];
    
    $prompt = 'Create a simple question about Python variables for beginners';
    
    $result = $service->generateQuestion($assignment, $questionParams, $prompt);
    
    // Verify we got a valid response
    expect($result)->toBeInstanceOf(QuestionData::class);
    expect($result->language)->toBe(QuestionLanguage::Python);
    expect($result->type)->toBe(QuestionType::MultipleChoice);
    expect($result->level)->toBe(QuestionLevel::Beginner);
    expect($result->question)->not->toBeEmpty();
    expect($result->explanation)->not->toBeEmpty();
    expect($result->options)->toHaveCount(4);
    expect($result->answer)->not->toBeEmpty();
    
    // Verify the answer is one of the options
    expect($result->options)->toContain($result->answer);
    
})->group('integration');
