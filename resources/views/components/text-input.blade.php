@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white text-gray-900 border-gray-300 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm']) }}>
