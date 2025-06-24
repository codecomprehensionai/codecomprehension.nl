<div class="bg-blue-50 p-6 rounded-md">
    <h2 class="text-lg font-semibold mb-4">Assignment Overview</h2>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <p><strong>Title:</strong> {{ $assignment->title }}</p>
        </div>
        <div>
            <p><strong>Status:</strong> Draft</p>
        </div>
    </div>
</div>

{{-- Loop questions --}}
<div class="mt-6">
    <h3 class="text-md font-semibold mb-2">Questions Summary:</h3>
    @foreach ($assignment->questions as $index => $question)
        <div class="border p-4 rounded mb-4">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="font-medium">Question {{ $index + 1 }}</p>
                    <p>{{ $question->prompt ?? $question->question ?? 'No prompt available' }}</p>
                </div>
                <div class="text-sm text-right space-y-1 ml-4 text-gray-600">
                    <p><strong>Language:</strong> {{ $question->language ?? 'N/A' }}</p>
                    <p><strong>Level:</strong> {{ $question->level ?? 'N/A' }}</p>
                    <p><strong>Type:</strong> {{ $question->type ?? 'N/A' }}</p>
                </div>
            </div>

            @if (!empty($question->tags) && is_iterable($question->tags))
                <div class="mb-2">
                    @foreach ($question->tags as $tag)
                        <span class="inline-block bg-gray-200 text-sm text-gray-700 px-2 py-1 rounded mr-1">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif

            @if (!empty($question->code))
                <div class="mt-3">
                    <p class="text-sm font-medium mb-1">Code:</p>
                    <pre class="bg-gray-100 p-3 rounded overflow-x-auto text-sm"><code>{{ $question->code }}</code></pre>
                </div>
            @endif
        </div>
    @endforeach
</div>
