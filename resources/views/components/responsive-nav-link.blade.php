@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-4 pe-4 py-2 border-l-4 border-white shadow-md text-start text-base font-medium text-white bg-blue-800 dark:bg-blue-800 rounded-md transition duration-200 ease-in-out flex justify-between items-center'
            : 'block w-full ps-4 pe-4 py-2  rounded-lg border border-white/20 shadow-md shadow-gray-800/50 transition-all duration-200 hover:bg-gray-700 active:bg-gray-600 border-l-4 border-transparent text-start text-base font-medium text-gray-300 rounded-md transition duration-200 ease-in-out  flex justify-between items-center ';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} >
    {{ $slot }}
</a>
