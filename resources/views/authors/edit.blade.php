<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
                {{ __('Author Profile Settings') }} - {{ $subcategory->name }}
            </h2>
            @if(\App\Helpers\SettingHelper::get('global_language_helper_enabled', '1') === '1')
                <div class="flex items-center gap-1 bg-gray-950 border border-gray-800/80 rounded-full p-0.5 select-none" title="Transliteration Mode">
                    <button type="button" class="lang-toggle-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold border-0 cursor-pointer transition-all duration-150 bg-burnt-orange text-white" data-lang="en">En</button>
                    <button type="button" class="lang-toggle-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold border-0 cursor-pointer transition-all duration-150 bg-transparent text-gray-400 hover:text-white" data-lang="ta">த</button>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if($errors->any())
                <div class="bg-red-950/40 border border-red-800 text-red-400 px-4 py-3 rounded-xl mb-6" role="alert">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-gray-900 border border-gray-800 overflow-hidden shadow-2xl sm:rounded-xl">
                <div class="p-6 md:p-8 text-white">
                    <form action="{{ route('authors.update', $subcategory->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="profileForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="cropped_image" id="croppedImageData">

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Left Column (General info) -->
                            <div class="space-y-6">
                                <!-- Name -->
                                <div>
                                    <x-input-label for="name" :value="__('Name / பதிவர் பெயர் (Required)')" class="text-gray-300 font-semibold mb-1.5" />
                                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-sm" :value="old('name', $subcategory->name)" required autofocus data-kathaingo-transliterate="true" />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <!-- Email -->
                                <div>
                                    <x-input-label for="email" :value="__('Email Address')" class="text-gray-300 font-semibold mb-1.5" />
                                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-sm" :value="old('email', $subcategory->email)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Phone -->
                                <div>
                                    <x-input-label for="phone" :value="__('Phone Number')" class="text-gray-300 font-semibold mb-1.5" />
                                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-sm" :value="old('phone', $subcategory->phone)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>
                            </div>

                            <!-- Right Column (Avatar upload) -->
                            <div class="flex flex-col items-center justify-center bg-gray-950/40 border border-gray-850 p-6 rounded-2xl">
                                <x-input-label :value="__('Profile Photo')" class="text-gray-300 font-bold mb-4 uppercase tracking-widest text-xs self-start" />
                                
                                <div class="mb-4">
                                    @if($subcategory->getAvatarUrl())
                                        <img src="{{ $subcategory->getAvatarUrl() }}" alt="{{ $subcategory->name }}" class="w-28 h-28 rounded-full object-cover border-2 border-burnt-orange shadow-lg shadow-orange-600/20 bg-slate-900">
                                    @else
                                        <div class="w-28 h-28 rounded-full bg-burnt-orange/10 border-2 border-burnt-orange flex items-center justify-center text-3xl font-black text-burnt-orange shadow-inner">
                                            {{ mb_substr($subcategory->name, 0, 1, 'UTF-8') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="w-full">
                                    <input type="file" id="image" name="image" class="block w-full text-xs text-gray-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-850 file:text-gray-200 hover:file:bg-gray-800 cursor-pointer" accept="image/*" />
                                    <p class="text-[10px] text-gray-500 mt-2 text-center">Allowed: JPEG, PNG, JPG, GIF (Max 2MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cropper Wrapper -->
                        <div id="cropperWrapper" class="hidden mt-6 bg-gray-950 border border-gray-850 rounded-2xl p-6">
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Adjust Profile Photo Crop (Ideal Aspect Ratio: 16:7)</span>
                            <div class="max-h-[450px] overflow-hidden rounded-xl">
                                <img id="imageToCrop" src="" class="max-w-full block mx-auto" />
                            </div>
                            <!-- Directional and Zoom Adjustment Buttons -->
                            <div class="flex flex-wrap gap-2 mt-4 justify-center">
                                <button type="button" id="btnMoveLeft" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">← Left</button>
                                <button type="button" id="btnMoveRight" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">→ Right</button>
                                <button type="button" id="btnMoveUp" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">↑ Up</button>
                                <button type="button" id="btnMoveDown" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">↓ Down</button>
                                <button type="button" id="btnZoomIn" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">＋ Zoom In</button>
                                <button type="button" id="btnZoomOut" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">－ Zoom Out</button>
                                <button type="button" id="btnReset" class="px-3 py-1.5 bg-red-900/40 hover:bg-red-800 border border-red-700 text-red-300 rounded text-sm transition">Reset</button>
                            </div>
                        </div>

                        <!-- Social Media URL Fields -->
                        <div class="bg-gray-950/30 border border-gray-850 rounded-2xl p-6 space-y-6">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 border-b border-gray-850 pb-2">Social Media Links</h3>
                            
                            <div class="grid md:grid-cols-3 gap-6">
                                <!-- Facebook -->
                                <div>
                                    <x-input-label for="facebook_url" :value="__('Facebook Profile URL')" class="text-gray-300 font-semibold mb-1.5" />
                                    <x-text-input id="facebook_url" name="facebook_url" type="url" placeholder="https://facebook.com/..." class="mt-1 block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-xs" :value="old('facebook_url', $subcategory->facebook_url)" />
                                </div>

                                <!-- Instagram -->
                                <div>
                                    <x-input-label for="instagram_url" :value="__('Instagram Profile URL')" class="text-gray-300 font-semibold mb-1.5" />
                                    <x-text-input id="instagram_url" name="instagram_url" type="url" placeholder="https://instagram.com/..." class="mt-1 block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-xs" :value="old('instagram_url', $subcategory->instagram_url)" />
                                </div>

                                <!-- LinkedIn -->
                                <div>
                                    <x-input-label for="linkedin_url" :value="__('LinkedIn Profile URL')" class="text-gray-300 font-semibold mb-1.5" />
                                    <x-text-input id="linkedin_url" name="linkedin_url" type="url" placeholder="https://linkedin.com/in/..." class="mt-1 block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-xs" :value="old('linkedin_url', $subcategory->linkedin_url)" />
                                </div>
                            </div>
                        </div>

                        <!-- Description & Topics -->
                        <div class="space-y-6">
                            <!-- Short Description (Bio) -->
                            <div>
                                <x-input-label for="description" :value="__('Short Bio / பதிவரைப் பற்றி')" class="text-gray-300 font-semibold mb-1.5" />
                                <textarea id="description" name="description" rows="4" class="mt-1 block w-full bg-gray-950 text-white border-gray-855 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-sm p-3.5" placeholder="Write a short summary about yourself..." data-kathaingo-transliterate="true">{{ old('description', $subcategory->description) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            <!-- Topics (What they usually write about) -->
                            <div>
                                <x-input-label for="topics" :value="__('What you usually write about / எழுதும் பகுதிகள்')" class="text-gray-300 font-semibold mb-1.5" />
                                <textarea id="topics" name="topics" rows="3" class="mt-1 block w-full bg-gray-950 text-white border-gray-855 focus:border-burnt-orange focus:ring-burnt-orange rounded-xl shadow-sm text-sm p-3.5" placeholder="E.g., சினிமா, தமிழ் வரலாறு, சமூக அரசியல்..." data-kathaingo-transliterate="true">{{ old('topics', $subcategory->topics) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('topics')" />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-4 border-t border-gray-850 pt-6">
                            <a href="{{ route('authors.show', $subcategory->slug) }}" class="inline-flex items-center px-5 py-2.5 bg-gray-850 hover:bg-gray-800 border border-transparent rounded-full text-xs font-bold text-gray-400 uppercase tracking-widest transition duration-150 shadow-md">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-burnt-orange hover:bg-orange-600 border border-transparent rounded-full text-xs font-bold text-white uppercase tracking-widest transition duration-150 shadow-md">
                                Save Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper.js Library Integration -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageInput = document.getElementById('image');
            const imageToCrop = document.getElementById('imageToCrop');
            const cropperWrapper = document.getElementById('cropperWrapper');
            const croppedImageData = document.getElementById('croppedImageData');
            const profileForm = document.getElementById('profileForm');
            let cropper = null;

            imageInput.addEventListener('change', function (e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imageToCrop.src = event.target.result;
                        cropperWrapper.classList.remove('hidden');
                        
                        if (cropper) {
                            cropper.destroy();
                        }
                        
                        cropper = new Cropper(imageToCrop, {
                            aspectRatio: 16 / 7, // Profile Cover matches Hero Slider wide format
                            viewMode: 1,
                            autoCropArea: 1,
                            responsive: true,
                            restore: false,
                            checkCrossOrigin: false
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Wire up positioning buttons
            document.getElementById('btnMoveLeft').addEventListener('click', () => cropper && cropper.move(-15, 0));
            document.getElementById('btnMoveRight').addEventListener('click', () => cropper && cropper.move(15, 0));
            document.getElementById('btnMoveUp').addEventListener('click', () => cropper && cropper.move(0, -15));
            document.getElementById('btnMoveDown').addEventListener('click', () => cropper && cropper.move(0, 15));
            document.getElementById('btnZoomIn').addEventListener('click', () => cropper && cropper.zoom(0.1));
            document.getElementById('btnZoomOut').addEventListener('click', () => cropper && cropper.zoom(-0.1));
            document.getElementById('btnReset').addEventListener('click', () => cropper && cropper.reset());

            profileForm.addEventListener('submit', function (e) {
                if (cropper) {
                    e.preventDefault();
                    // Crop canvas
                    const canvas = cropper.getCroppedCanvas({
                        width: 1920,
                        height: 840,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high'
                    });
                    
                    // Get base64 string (highest quality)
                    const dataUrl = canvas.toDataURL('image/jpeg', 1.0);
                    croppedImageData.value = dataUrl;
                    
                    // Clear file input to avoid sending duplicate raw file payload
                    const dataTransfer = new DataTransfer();
                    imageInput.files = dataTransfer.files;
                    
                    // Submit form
                    profileForm.submit();
                }
            });
        });
    </script>

    <!-- Language Helper Script -->
    <script src="{{ asset('js/language-helper.js') }}?v={{ file_exists(public_path('js/language-helper.js')) ? filemtime(public_path('js/language-helper.js')) : time() }}"></script>
    <script>
        if (window.KathaingoLanguageHelper) {
            window.KathaingoLanguageHelper.init({
                enabled: @json(\App\Helpers\SettingHelper::get('global_language_helper_enabled', '1') === '1'),
                suggestUrl: '{{ route("api.language-helper.suggest", [], false) }}',
                csrfToken: '{{ csrf_token() }}',
                inputSelector: '[data-kathaingo-transliterate="true"]'
            });
        }
    </script>
</x-app-layout>
