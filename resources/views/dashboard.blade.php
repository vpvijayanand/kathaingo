<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Dashboard - ' . ucfirst(str_replace('_', ' ', $role))) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 border border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    @if (session('success'))
                        <div class="bg-green-950/30 border border-green-800 text-green-400 px-4 py-3 rounded-xl relative mb-6" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($role === 'admin')
                        @include('dashboard.admin')
                    @elseif($role === 'editor')
                        @include('dashboard.editor')
                    @elseif($role === 'author')
                        @include('dashboard.author')
                    @else
                        @include('dashboard.visitor')
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
