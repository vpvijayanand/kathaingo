<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('அமைப்புகள் (Settings)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-950 border border-gray-850 overflow-hidden shadow-2xl rounded-2xl">
                <div class="p-8 text-gray-100">
                    
                    @if (session('success'))
                        <div class="bg-emerald-950/40 border border-emerald-800 text-emerald-400 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-2" role="alert">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <h3 class="text-lg font-bold text-gray-200 mb-2">{{ __('கருத்து மொழி உதவி அமைப்புகள் (Comment Language Helper Settings)') }}</h3>
                    <p class="text-xs text-gray-400 mb-6">
                        {{ __('வாசகர்களின் Tanglish அல்லது Tamil-script English தட்டச்சு எழுத்துக்களை வாசிக்க எளிதான வடிவில் பரிந்துரைக்கும் வசதியை நிர்வகிக்கவும்.') }}
                    </p>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <div class="flex items-start justify-between p-4 bg-gray-900/40 border border-gray-800/80 rounded-xl">
                            <div class="space-y-1 pr-4">
                                <label for="global_language_helper_enabled" class="text-sm font-semibold text-gray-200 flex items-center gap-2 cursor-pointer">
                                    {{ __('மொழி உதவி அம்சத்தை இயக்கு (Enable Language Helper)') }}
                                </label>
                                <p class="text-xs text-gray-400">
                                    {{ __('செயல்படுத்தப்பட்டால், கருத்துப் பெட்டிகளின் மேலே மொழிப் பரிந்துரைத் திரையிடல் காண்பிக்கப்படும்.') }}
                                </p>
                            </div>
                            <div class="flex items-center">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="global_language_helper_enabled" id="global_language_helper_enabled" 
                                           value="1" class="sr-only peer" {{ $languageHelperEnabled ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-burnt-orange peer-checked:after:bg-white"></div>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-800/50">
                            <button type="submit" class="px-6 py-2.5 bg-burnt-orange hover:bg-orange-600 rounded-full text-sm font-semibold text-white transition transform hover:scale-[1.02] active:scale-[0.98] border-0 cursor-pointer">
                                {{ __('அமைப்புகளைச் சேமி (Save Settings)') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
