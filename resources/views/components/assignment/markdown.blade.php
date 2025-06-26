@props(['content'])

<div {{ $attributes->merge(['class' => 'prose prose-lg prose-gray max-w-none']) }}>
    <div class="
        prose-headings:font-semibold
        prose-h1:text-2xl prose-h1:mb-4
        prose-h2:text-xl prose-h2:mb-3
        prose-h3:text-lg prose-h3:mb-2
        prose-p:mb-4 prose-p:leading-relaxed
        prose-code:bg-gray-100 prose-code:px-2 prose-code:py-1 prose-code:rounded prose-code:text-sm
        prose-pre:bg-gray-900 prose-pre:text-gray-100 prose-pre:p-4 prose-pre:rounded-lg prose-pre:overflow-x-auto
        prose-ol:pl-6 prose-ul:pl-6
        prose-li:mb-2
        prose-strong:font-semibold prose-strong:text-gray-900
        dark:prose-invert
        dark:prose-code:bg-gray-800 dark:prose-code:text-gray-200
    ">
        {!! Str::markdown($content) !!}
    </div>
</div>
