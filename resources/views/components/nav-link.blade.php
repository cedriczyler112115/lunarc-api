@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-bold leading-5 text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200/80 dark:border-emerald-800/60 focus:outline-none transition duration-200 ease-in-out'
            : 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium leading-5 text-gray-600 dark:text-gray-400 hover:text-emerald-700 dark:hover:text-emerald-300 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 focus:outline-none focus:text-emerald-700 dark:focus:text-emerald-300 transition duration-200 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

