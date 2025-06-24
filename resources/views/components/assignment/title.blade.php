<x-card>
    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $assignment->title }}
            </h1>
        </div>

        @if($assignment->description)
            <p class="text-gray-600 leading-relaxed">
                {!! $assignment->description !!}
            </p>
        @endif

        <div class="flex items-center justify-end text-sm text-gray-500 pt-2 border-t border-gray-100">
            {{-- Right side: Languages --}}
            @if(isset($assignment->languages) && count($assignment->languages) > 0)
                <div class="flex items-center gap-1">
                    <span>{{ implode(', ', array_map(fn($lang) => $lang->value, $assignment->languages)) }}</span>
                </div>
            @endif
        </div>
    </div>
</x-card>
