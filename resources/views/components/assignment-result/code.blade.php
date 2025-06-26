    <div data-slot="card-content" class="px-3 my-5">
        <div class="bg-gray-900 text-gray-100 rounded-lg overflow-hidden">
            <div class="flex">
                <div class="bg-gray-800 text-gray-400 text-right select-none border-r border-gray-700 flex-shrink-0">
                    @php
                        $lines = explode("\n", $question->code);
                        $lineCount = count($lines);
                        $maxDigits = strlen((string)$lineCount);
                    @endphp
                    @foreach($lines as $index => $line)
                        <div class="px-3 py-1 text-xs font-mono leading-5"
                             style="min-width: {{ ($maxDigits * 0.6) + 1.5 }}rem;">
                            {{ $index + 1 }}
                        </div>
                    @endforeach
                </div>

                <div class="flex-1 min-w-0">
                    <pre class="text-sm font-mono leading-5 p-4 m-0 whitespace-pre-wrap break-words overflow-hidden"><code>{{ $question->code }}</code></pre>
                </div>
                
            </div>
        </div>
    </div>
