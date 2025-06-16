<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\QuestionData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

final readonly class QuestionCreateAction
{
    public function handle(QuestionData $questionData, string $questionPrompt): void
    {
        DB::transaction(function () use ($questionData, $questionPrompt): void {
            $assignment = $question->assignment;

            $questionResponse = Http::connectTimeout(3)
                ->timeout(120)
                ->throw()
                ->post('https://llm.codecomprehension.nl/question', [
                    'assignment' => [
                        'id'          => $assignment->id,
                        'title'       => $assignment->title,
                        'description' => $assignment->description,
                    ],
                    'questions' => [
                        // TODO
                    ],
                    'new_question' => [
                        'language'                  => $question->language->value,
                        'type'                      => $question->type->value,
                        'level'                     => $question->level->value,
                        'estimated_answer_duration' => $question->estimatedAnswerDuration,
                        'topic'                     => $question->topic,
                        'tags'                      => $question->tags,
                    ],
                    'new_question_prompt' => $questionPrompt,
                ])
                ->json('data');

            $questionData = QuestionData::from($questionResponse['question']);

            $question->update([
                'question'    => $questionData->question,
                'explanation' => $questionData->explanation,
                'code'        => $questionData->code,
                'options'     => $questionData->options,
                'answer'      => $questionData->answer,
            ]);
        });
    }
}
