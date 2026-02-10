<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">{{ __("You're logged in!") }}</p>

                    <div class="flex gap-4">
                        <a href="{{ route('posts.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-gray border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Manage My Posts
                        </a>

                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-burnt-orange border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Admin: Manage Users
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
