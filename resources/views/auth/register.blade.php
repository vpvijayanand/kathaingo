<x-guest-layout>
    <!-- Flatpickr CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Mobile -->
        <div class="mt-4">
            <x-input-label for="mobile" :value="__('Mobile')" />
            <x-text-input id="mobile" class="block mt-1 w-full" type="text" name="mobile" :value="old('mobile')" required />
            <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
        </div>

        <!-- Gender -->
        <div class="mt-4">
            <x-input-label for="gender" :value="__('Gender')" />
            <select id="gender" name="gender" class="block mt-1 w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm" required>
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        <!-- Location (Optional) -->
        <div class="mt-4">
            <x-input-label for="location" :value="__('Location (Optional)')" />
            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location')" />
            <x-input-error :messages="$errors->get('location')" class="mt-2" />
        </div>

        <!-- DOB (Optional) -->
        <div class="mt-4">
            <x-input-label for="dob" :value="__('Date of Birth (Optional)')" />
            <x-text-input id="dob" class="block mt-1 w-full datepicker" type="text" name="dob" :value="old('dob')" placeholder="DD-MM-YYYY" autocomplete="off" />
            <x-input-error :messages="$errors->get('dob')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <!-- Password Strength Meter -->
            <div class="mt-2 text-xs" id="password-strength-container">
                <div class="h-1.5 w-full bg-gray-700 rounded-full overflow-hidden">
                    <div id="password-strength-bar" class="h-full bg-gray-500 w-0 transition-all duration-300"></div>
                </div>
                <div class="flex justify-between mt-1 text-gray-400">
                    <span id="password-strength-text">Password Strength</span>
                    <span id="password-strength-requirements" class="hidden sm:inline">8-12 chars, Case, Num, Symbol</span>
                </div>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Tamil Captcha Human Verification -->
        <div class="mt-4 p-4 bg-gray-800 rounded-lg border border-gray-700">
            <x-input-label for="captcha_display" :value="__('Human Verification / மனித சரிபார்ப்பு')" class="text-gray-300 text-sm font-semibold mb-2" />
            <p class="text-xs text-gray-400 mb-3" id="captcha-question">{{ $captcha['question'] }}</p>
            
            <div class="grid grid-cols-4 gap-2 mb-3" id="captcha-options-container">
                @foreach($captcha['options'] as $option)
                    <button type="button" class="captcha-option-btn py-2 px-2 bg-gray-750 hover:bg-burnt-orange hover:text-white rounded-md text-base font-bold text-gray-200 transition border border-gray-600" data-val="{{ $option }}">
                        {{ $option }}
                    </button>
                @endforeach
            </div>

            <div class="flex items-center justify-between">
                <button type="button" id="btn-refresh-captcha" class="text-xs text-burnt-orange hover:text-orange-400 font-semibold flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12" /></svg>
                    Refresh Challenge / புதிய எழுத்துக்கள்
                </button>
                <span id="captcha-selected-display" class="text-xs text-gray-400">Not selected / தேர்ந்தெடுக்கப்படவில்லை</span>
            </div>

            <input type="hidden" name="captcha_answer" id="captcha_answer" required />
            <x-input-error :messages="$errors->get('captcha_answer')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>

        <div class="mt-4 flex flex-col gap-2">
            <div class="relative flex py-5 items-center">
                <div class="flex-grow border-t border-gray-400"></div>
                <span class="flex-shrink-0 mx-4 text-gray-400">Or Register with</span>
                <div class="flex-grow border-t border-gray-400"></div>
            </div>

            <a href="{{ route('social.redirect', 'google') }}" class="flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-burnt-orange hover:bg-orange-650">
                Google
            </a>
            <a href="{{ route('social.redirect', 'facebook') }}" class="flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-burnt-orange hover:bg-orange-650">
                Facebook
            </a>
            <a href="{{ route('social.redirect', 'linkedin') }}" class="flex items-center justify-center w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-burnt-orange hover:bg-orange-650">
                LinkedIn
            </a>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Flatpickr for DOB
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",      // Backend format
            altInput: true,           // Show alternative input
            altFormat: "d-m-Y",       // User facing format
            allowInput: true,
            maxDate: "today"
        });
        // 1. Password Strength Indicator
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');

        passwordInput.addEventListener('input', function() {
            const val = passwordInput.value;
            let score = 0;

            if (val.length >= 8 && val.length <= 12) score += 20;
            if (/[a-z]/.test(val)) score += 20;
            if (/[A-Z]/.test(val)) score += 20;
            if (/\d/.test(val)) score += 20;
            if (/[^a-zA-Z\d]/.test(val)) score += 20;

            strengthBar.style.width = score + '%';
            if (val.length === 0) {
                strengthBar.className = 'h-full bg-gray-500 w-0 transition-all duration-300';
                strengthText.innerText = 'Password Strength';
                strengthText.className = 'text-gray-400';
            } else if (score <= 40) {
                strengthBar.className = 'h-full bg-red-650 transition-all duration-300';
                strengthText.innerText = 'Weak Password';
                strengthText.className = 'text-red-500 font-semibold';
            } else if (score <= 80) {
                strengthBar.className = 'h-full bg-yellow-600 transition-all duration-300';
                strengthText.innerText = 'Medium Password';
                strengthText.className = 'text-yellow-500 font-semibold';
            } else {
                strengthBar.className = 'h-full bg-green-650 transition-all duration-300';
                strengthText.innerText = 'Strong Password';
                strengthText.className = 'text-green-500 font-semibold';
            }
        });

        // 2. Captcha Buttons Selection
        const captchaContainer = document.getElementById('captcha-options-container');
        const captchaInput = document.getElementById('captcha_answer');
        const captchaSelectedDisplay = document.getElementById('captcha-selected-display');
        const refreshBtn = document.getElementById('btn-refresh-captcha');
        const questionText = document.getElementById('captcha-question');

        function bindOptionButtons() {
            const buttons = document.querySelectorAll('.captcha-option-btn');
            buttons.forEach(btn => {
                btn.addEventListener('click', function() {
                    buttons.forEach(b => {
                        b.classList.remove('bg-burnt-orange', 'text-white', 'border-burnt-orange');
                        b.classList.add('bg-gray-750', 'text-gray-200');
                    });
                    
                    btn.classList.remove('bg-gray-750', 'text-gray-200');
                    btn.classList.add('bg-burnt-orange', 'text-white', 'border-burnt-orange');

                    const val = btn.getAttribute('data-val');
                    captchaInput.value = val;
                    captchaSelectedDisplay.innerText = 'Selected: "' + val + '"';
                    captchaSelectedDisplay.className = 'text-xs text-green-400 font-semibold';
                });
            });
        }

        bindOptionButtons();

        // 3. Captcha Refresh via AJAX
        refreshBtn.addEventListener('click', function() {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = 'Loading... / ஏற்றுகிறது...';
            
            fetch("{{ route('captcha.refresh') }}")
                .then(res => res.json())
                .then(data => {
                    questionText.innerText = data.question;
                    captchaContainer.innerHTML = '';
                    captchaInput.value = '';
                    captchaSelectedDisplay.innerText = 'Not selected / தேர்ந்தெடுக்கப்படவில்லை';
                    captchaSelectedDisplay.className = 'text-xs text-gray-400';

                    data.options.forEach(option => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'captcha-option-btn py-2 px-2 bg-gray-750 hover:bg-burnt-orange hover:text-white rounded-md text-base font-bold text-gray-200 transition border border-gray-600';
                        btn.setAttribute('data-val', option);
                        btn.innerText = option;
                        captchaContainer.appendChild(btn);
                    });

                    bindOptionButtons();
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = `
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m-3-3v12" /></svg>
                        Refresh Challenge / புதிய எழுத்துக்கள்
                    `;
                })
                .catch(err => {
                    console.error(err);
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = 'Error! Try again / பிழை!';
                });
        });
    });
    </script>
</x-guest-layout>
