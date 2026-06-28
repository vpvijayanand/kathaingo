@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm']) }}>
