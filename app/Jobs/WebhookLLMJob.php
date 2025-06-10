<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class WebhookLLMJob extends ProcessWebhookJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        // TODO: dump($this->webhookCall->payload);
    }
}
