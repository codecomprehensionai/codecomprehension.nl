<?php

namespace App\Jobs;

use App\Models\Question;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QuestionLLMGenerateJob implements ShouldQueue // TODO, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Question $question, protected string $questionPrompt) {}

    // TODO: timeout 180 seconds

    // TODO: modify horizon timeout to be 240 seconds

    public function handle(): void
    {
        // TODO: dump($this->webhookCall->payload);
    }

    // TODO: public function uniqueId(): int
    // {
    //     return $this->webhookCall->id;
    // }
}
