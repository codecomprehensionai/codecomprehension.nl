<x-card>
    @php
        $totalQuestions = $assignment->questions->count();
        $answeredCount = $assignment->questions->whereNotNull('saved_answer')->count();
        $progressPercentage = $totalQuestions > 0 ? ($answeredCount / $totalQuestions) * 100 : 0;
    @endphp

    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Progress</h3>
            <span class="text-sm text-gray-500">
                {{ $answeredCount }} of {{ $totalQuestions }} completed
            </span>
        </div>

        <div class="w-full bg-gray-200 rounded-full h-2">
            <div
                class="bg-blue-600 h-2 rounded-full"
                style="width: {{ $progressPercentage }}%"
            ></div>
        </div>

        <div class="text-right">
            <span class="font-medium text-gray-900">{{ round($progressPercentage) }}%</span>
        </div>
    </div>
</x-card>
