<?php

use App\Models\User;
use App\Models\JwtKey;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

uses(Tests\TestCase::class);

it('handles submission flow', function () {
    // Test the JWT signing like the actual code does
    $jwt = JwtKey::first()->sign(config('services.canvas.client_id'), 'https://uvadlo-dev.test.instructure.com/login/oauth2/token', now()->addDay());
    expect($jwt)->toBeString();
    
    Http::fake([
        '*/login/oauth2/token'        => Http::response(['access_token' => 'tok'], 200),
        '*/line_items/*/scores'       => Http::response(['resultUrl' => 'url'], 201),
    ]);

    // Set up session data that SubmissionHandler expects
    session([
        'lti.launch' => (object) ['sub' => 'test-user-id'],
        'lti.course_id' => 123,
    ]);

    $this->actingAs(User::factory()->create())
         ->postJson(route('submission.store'), [
             'attempt_uuid' => Str::uuid(),
             'answer_blob'  => '{}',
             'line_item_id' => 49,
             'score_max'    => 100,
         ])
         ->assertJson(['ok' => true]);

    Http::assertSentTimes(2);
}); 