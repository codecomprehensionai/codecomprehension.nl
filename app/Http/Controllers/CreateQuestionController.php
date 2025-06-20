<?php

namespace App\Http\Controllers;

use App\Enums\QuestionLanguage;
use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateQuestionController
{
    public function __invoke(Request $request): JsonResponse
    {
        $assignment = Assignment::first();

        $data = [
            "assignment" => [
                "id" => 1,
                "title" => "Dummy Assignment",
                "description" => "Dit is een dummy assignment."
            ],
            "questions" => [
                [
                    "id" => 1,
                    "language" => "php",
                    "type" => "multiple_choice",
                    "level" => "easy",
                    "estimated_answer_duration" => 60,
                    "topics" => ["arrays"],
                    "tags" => ["beginner"],
                    "question" => "Wat doet deze PHP code?",
                    "explanation" => "Uitleg over het antwoord.",
                    "code" => "<?php echo 'Hello World'; ?>",
                    "options" => ["Print Hello World", "Foutmelding", "Niets"],
                    "answer" => "Print Hello World"
                ]
            ],
            "new_question" => [
                "language" => "php",
                "type" => "open",
                "level" => "medium",
                "estimated_answer_duration" => 120,
                "topics" => ["strings"],
                "tags" => ["intermediate"]
            ],
            "new_question_prompt" => "Maak een vraag over string functies in PHP."
        ];
       
        // $questions = $assignment->questions->map(function ($question) {
        //     return [
        //         'id' => $question->id,
        //         'language' => $question->language,
        //         'type' => $question->type,
        //         'level' => $question->level,
        //         'estimated_answer_duration' => $question->estimated_answer_duration,
        //         'topics' => $question->topics->pluck('topic')->toArray(),
        //         'tags' => $question->tags->pluck('tags')->toArray(),
        //         'question' => $question->question,
        //         'explanation' => $question->explanation,
        //         'code' => $question->code,
        //         'options' => $question->options,
        //         'answer' => $question->answer,
        //     ];
        // })->toArray();

        // $payload = [
        //     "assignment" => [
        //         "id" => $assignment->id,
        //         "title" => $assignment->title,
        //         "description" => $assignment->description,
        //     ],
        //     "questions" => $questions,
        //     "new_question" => ,
        //     "new_question_prompt" => "", // TODO
        // ];

        $data = Http::post('https://llm.codecomprehension.nl/question', $data) // verander naar payload            ->throw()
            ->json();

        return response()->json($data);
    }
}