<x-card>
    <div class='flex gap-3 items-center justify-start rounded-lg'>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-code">
            <polyline points="16,18 22,12 16,6" />
            <polyline points="8,6 2,12 8,18" />
        </svg>
        <span>Code to analyse</span>
    </div>
    <div data-slot="card-description" class="text-muted-foreground text-sm">
        Study this code carefully before answering the questions
    </div>
    <div data-slot="card-content" class="px-6">
        <div class="bg-gray-900 text-gray-100 p-4 rounded-lg font-mono text-sm overflow-x-auto">
            <pre><code>{{ $question->code }}</code></pre>
        </div>
    </div>
</x-card>
