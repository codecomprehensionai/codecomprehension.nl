Below is a **single, self-contained instruction file** you can hand to another LLM (or dev) to implement the **SubmissionHandler** in your Laravel project **using the existing `JwtKey::sign()` helper for client-assertions**.

---

## 0  What we’re building

A controller that:

1. Receives a student’s attempt (`POST /submission`).
2. Auto-grades it.
3. Mints a Canvas **client-credentials** token via your helper-generated JWT.
4. Posts a **Score + Submission** payload to Canvas.
5. Returns JSON to the front-end.

---

## 1  Route

```php
Route::post('/submission', SubmissionHandler::class)
     ->middleware(['auth'])   // student must be logged in
     ->name('submission.store');
```

---

## 2  Front-end → controller contract

| Field          | Type  | Example                                        |
| -------------- | ----- | ---------------------------------------------- |
| `attempt_uuid` | uuid  | `151ef876-0917-4452-a1a2-630a101ed83b`         |
| `answer_blob`  | mixed | JSON or form-data used by your grader          |
| `line_item_id` | int   | `49` (Canvas line-item ID for this assignment) |
| `score_max`    | int   | `100`                                          |

---

## 3  Environment / config

```dotenv
CANVAS_DOMAIN=uvadlo-dev.test.instructure.com
CANVAS_CLIENT_ID=104400000000000340
CANVAS_TOKEN_AUD=https://uvadlo-dev.test.instructure.com/login/oauth2/token
CANVAS_SCOPES="https://purl.imsglobal.org/spec/lti-ags/scope/lineitem https://purl.imsglobal.org/spec/lti-ags/scope/score https://purl.imsglobal.org/spec/lti-ags/scope/result.readonly"
```

`config/services.php`

```php
'canvas' => [
    'domain'    => env('CANVAS_DOMAIN'),
    'client_id' => env('CANVAS_CLIENT_ID'),
    'token_aud' => env('CANVAS_TOKEN_AUD'),
    'scopes'    => env('CANVAS_SCOPES'),
],
```

---

## 4  `CanvasTokenService` (uses **JwtKey::sign**)

```php
namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\CarbonImmutable;
use App\Models\JwtKey;

class CanvasTokenService
{
    public static function get(): string
    {
        return Cache::remember('canvas.access_token', 55 * 60, function () {

            $clientAssertion = JwtKey::first()->sign(
                config('services.canvas.client_id'),
                config('services.canvas.token_aud'),
                CarbonImmutable::now()->addMinutes(5)
            );

            $res = Http::asForm()->post(
                config('services.canvas.token_aud'),
                [
                    'grant_type'             => 'client_credentials',
                    'client_assertion_type'  => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                    'client_assertion'       => $clientAssertion,
                    'scope'                  => config('services.canvas.scopes'),
                ]
            )->throw()->json();

            return $res['access_token'];
        });
    }
}
```

---

## 5  `SubmissionHandler`

```php
namespace App\Http\Controllers;

use App\Services\CanvasTokenService;
use App\Services\AutoGrader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SubmissionHandler
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'attempt_uuid' => 'required|uuid',
            'answer_blob'  => 'required',
            'line_item_id' => 'required|integer',
            'score_max'    => 'required|integer|min:1',
        ]);

        // 1) Grade
        $score = app(AutoGrader::class)->grade($data['answer_blob']);

        // 2) Token
        $token = CanvasTokenService::get();

        // 3) Build payload
        $launch = session('lti.launch');          // set in LtiCallbackController
        $payload = [
            'timestamp'        => now()->toIso8601String(),
            'userId'           => $launch->sub,   // UUID from launch
            'scoreGiven'       => $score,
            'scoreMaximum'     => $data['score_max'],
            'activityProgress' => 'Completed',
            'gradingProgress'  => 'FullyGraded',
            'comment'          => 'Auto-graded by Code Comprehension',
            'https://canvas.instructure.com/lti/submission' => [
                'new_submission'            => true,
                'submission_type'           => 'basic_lti_launch',
                'submission_data'           => route('lti.launch', ['attempt' => $data['attempt_uuid']]),
                'prioritize_non_tool_grade' => true,
            ],
        ];

        // 4) POST to Canvas
        $url = sprintf(
            'https://%s/api/lti/courses/%d/line_items/%d/scores',
            config('services.canvas.domain'),
            session('lti.course_id'),
            $data['line_item_id']
        );

        $res = Http::withToken($token)
                   ->acceptJson()
                   ->post($url, $payload)
                   ->throw()
                   ->json();

        return response()->json([
            'ok'        => true,
            'score'     => $score,
            'resultUrl' => $res['resultUrl'] ?? null,
        ]);
    }
}
```

---

## 6  Auto-grader stub

```php
namespace App\Services;

class AutoGrader
{
    public function grade($answer): int
    {
        // TODO: real grading logic
        return random_int(70, 100);
    }
}
```

---

## 7  Unit test outline

```php
public function test_submission_flow()
{
    Http::fake([
        '*/login/oauth2/token'        => Http::response(['access_token' => 'tok'], 200),
        '*/line_items/*/scores'       => Http::response(['resultUrl' => 'url'], 201),
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
}
```

---

### Handoff

*Use the code & config above; no additional JWT libraries are needed—`JwtKey::sign()` already returns the ES256 assertion.*
