<x-public-layout>
    <x-slot name="title">
        {{ config('app.name', 'கதைங்கோ') }} - {{ __('Stories & Learning') }}
    </x-slot>

    <x-slot name="styles">
        <style>
            .text-gradient {
                background: linear-gradient(135deg, #f39c12 0%, #ff6b6b 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero-title {
                filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.95)) drop-shadow(0 2px 4px rgba(0, 0, 0, 0.8));
                -webkit-text-stroke: 1.2px rgba(0, 0, 0, 0.85);
            }

            .hero-desc {
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.95);
                filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.95));
                -webkit-text-stroke: 0.5px rgba(0, 0, 0, 0.6);
            }

            .stylish-desc {
                color: #f8fafc;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.95);
                filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.95));
                -webkit-text-stroke: 0.4px rgba(0, 0, 0, 0.75);
                font-weight: 600;
                letter-spacing: 0.025em;
            }

            /* Scroll Animation styles */
            .scroll-trigger {
                opacity: 0;
                transform: translateY(30px);
                transition: opacity 1.2s cubic-bezier(0.16, 1, 0.3, 1), transform 1.2s cubic-bezier(0.16, 1, 0.3, 1);
            }

            .scroll-trigger.is-visible {
                opacity: 1;
                transform: translateY(0);
            }

            /* Parchment Story Styling */
            .story-card {
                background: linear-gradient(145deg, rgba(26, 26, 46, 0.8) 0%, rgba(15, 12, 30, 0.9) 100%);
                border: 1px solid rgba(243, 156, 18, 0.15);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(243, 156, 18, 0.05);
                backdrop-filter: blur(12px);
            }

            /* Universe Night Sky Background (Absolute Dark Black Sky) */
            .universe-bg {
                background: #020205 !important;
                position: relative;
                overflow: hidden;
            }

            .universe-bg::before {
                content: '';
                position: absolute;
                inset: 0;
                background-image: 
                    radial-gradient(white, rgba(255,255,255,.2) 2px, transparent 40px),
                    radial-gradient(white, rgba(255,255,255,.15) 1px, transparent 30px),
                    radial-gradient(white, rgba(255,255,255,.1) 2px, transparent 40px);
                background-size: 550px 550px, 350px 350px, 250px 250px;
                background-position: 0 0, 40px 60px, 130px 270px;
                opacity: 0.08;
                z-index: 1;
            }

            /* Galaxy Constellation Animation Styles */
            .universe-container {
                position: relative;
                height: 220px;
                width: 100%;
                overflow: hidden;
                z-index: 2;
            }

            @media (min-width: 1024px) {
                .universe-container {
                    height: 280px;
                }
            }

            /* Keyframes for natural floating drift */
            @keyframes float-drift {
                0% {
                    transform: translate(0px, 0px) rotate(0deg);
                }
                33% {
                    transform: translate(15px, -15px) rotate(1deg);
                }
                66% {
                    transform: translate(-10px, 15px) rotate(-1deg);
                }
                100% {
                    transform: translate(0px, 0px) rotate(0deg);
                }
            }

            /* Twinkle glow effect with fade in and fade out */
            @keyframes twinkle-glow {
                0% {
                    opacity: 0;
                    filter: blur(1px);
                    text-shadow: 0 0 2px rgba(255, 255, 255, 0.1);
                }
                15% {
                    opacity: 0.8;
                    filter: blur(0px);
                    text-shadow: 0 0 10px rgba(243, 156, 18, 0.4);
                }
                50% {
                    opacity: 1;
                    text-shadow: 0 0 15px rgba(243, 156, 18, 0.8), 0 0 8px rgba(255, 255, 255, 0.5);
                }
                85% {
                    opacity: 0.8;
                    filter: blur(0px);
                    text-shadow: 0 0 10px rgba(243, 156, 18, 0.4);
                }
                100% {
                    opacity: 0;
                    filter: blur(1px);
                    text-shadow: 0 0 2px rgba(255, 255, 255, 0.1);
                }
            }

            .universe-item {
                position: absolute;
                transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1), color 0.4s ease, text-shadow 0.4s ease, opacity 0.4s ease;
                z-index: 10;
            }

            /* Hover enhancement */
            .universe-item:hover {
                animation-play-state: paused !important;
                opacity: 1 !important;
                transform: scale(1.12) !important;
                color: #ff8533 !important;
                text-shadow: 0 0 25px #ff8533, 0 0 10px rgba(255, 255, 255, 0.9) !important;
                z-index: 50;
            }

            /* Custom scrollbar styling for directories */
            .custom-scroll-container {
                scrollbar-width: thin;
                scrollbar-color: rgba(243, 156, 18, 0.4) rgba(15, 12, 30, 0.5);
            }
            .custom-scroll-container::-webkit-scrollbar {
                width: 6px;
            }
            .custom-scroll-container::-webkit-scrollbar-track {
                background: rgba(15, 12, 30, 0.3);
                border-radius: 9999px;
            }
            .custom-scroll-container::-webkit-scrollbar-thumb {
                background: rgba(243, 156, 18, 0.35);
                border-radius: 9999px;
                border: 1px solid rgba(15, 12, 30, 0.2);
            }
            .custom-scroll-container::-webkit-scrollbar-thumb:hover {
                background: rgba(243, 156, 18, 0.65);
            }

            /* Celestial Star & Planet Styles */
            .celestial-star {
                position: absolute;
                background-color: #ffffff;
                border-radius: 50%;
                pointer-events: none;
                will-change: opacity, transform;
            }
            
            .star-small {
                width: 2.5px;
                height: 2.5px;
            }
            
            .star-big {
                width: 5.5px;
                height: 5.5px;
                box-shadow: 0 0 12px rgba(255, 255, 255, 0.9), 0 0 4px rgba(255, 255, 255, 0.5);
            }

            .star-yellow {
                background-color: #fff4d6;
            }
            
            .star-blue {
                background-color: #d6f0ff;
            }

            /* Twinkle animations */
            @keyframes twinkle-slow {
                0%, 100% { opacity: 0.15; transform: scale(0.8); }
                50% { opacity: 1; transform: scale(1.2); }
            }
            
            @keyframes twinkle-fast {
                0%, 100% { opacity: 0.25; transform: scale(0.9); }
                50% { opacity: 0.95; transform: scale(1.3); }
            }

            /* Planet Styles */
            .celestial-planet {
                position: absolute;
                border-radius: 50%;
                pointer-events: none;
                will-change: transform;
                animation: planet-drift 45s ease-in-out infinite;
            }

            @keyframes planet-drift {
                0%, 100% { transform: translateY(0) rotate(0deg); }
                50% { transform: translateY(-15px) rotate(2deg); }
            }

            /* Saturn Styling (Deep Blue with rings & backlight glow & moons matching photo) */
            .planet-saturn-body {
                position: relative;
                width: 80px;
                height: 80px;
                background: radial-gradient(circle at 30% 30%, #4f6bff 0%, #293fa3 35%, #152059 65%, #080a24 100%);
                border-radius: 50%;
                box-shadow: inset -10px -10px 25px rgba(0, 0, 0, 0.95), 0 0 45px rgba(77, 105, 255, 0.45);
            }
            .planet-saturn-body::after {
                content: '';
                position: absolute;
                top: 5%;
                right: 15%;
                width: 25px;
                height: 25px;
                background: #ffffff;
                border-radius: 50%;
                box-shadow: 0 0 40px 18px rgba(255, 255, 255, 0.95), 0 0 70px 35px rgba(77, 105, 255, 0.85);
                opacity: 0.9;
                z-index: -1;
            }
            
            .planet-saturn-ring {
                position: absolute;
                top: 50%;
                left: 50%;
                width: 155px;
                height: 32px;
                border: 5px solid rgba(162, 187, 255, 0.55);
                border-radius: 50%;
                transform: translate(-50%, -50%) rotate(-26deg) skewX(22deg);
                box-shadow: 0 0 25px rgba(77, 105, 255, 0.5), inset 0 0 15px rgba(162, 187, 255, 0.25);
                z-index: 1;
            }

            .saturn-moon {
                position: absolute;
                background: radial-gradient(circle at 30% 30%, #ffffff 0%, #6d81a3 100%);
                border-radius: 50%;
                box-shadow: 0 0 8px rgba(255, 255, 255, 0.35);
            }

            .saturn-moon-1 {
                width: 6px;
                height: 6px;
                top: 25%;
                left: -15px;
            }

            .saturn-moon-2 {
                width: 10px;
                height: 10px;
                bottom: 35%;
                right: -25px;
                background: radial-gradient(circle at 30% 30%, #6d81a3 0%, #1a2233 100%);
                box-shadow: inset -2px -2px 5px rgba(0, 0, 0, 0.85), 0 0 6px rgba(109, 129, 163, 0.25);
            }

            /* Mars Styling */
            .planet-mars {
                width: 48px;
                height: 48px;
                background: radial-gradient(circle at 30% 30%, #ff6b6b 0%, #c0392b 60%, #4a0000 100%);
                box-shadow: inset -3px -3px 10px rgba(0, 0, 0, 0.8), 0 0 20px rgba(255, 107, 107, 0.2);
            }

            /* Neptune Styling */
            .planet-neptune {
                width: 58px;
                height: 58px;
                background: radial-gradient(circle at 30% 30%, #3498db 0%, #2980b9 65%, #0f324c 100%);
                box-shadow: inset -4px -4px 12px rgba(0, 0, 0, 0.8), 0 0 25px rgba(52, 152, 219, 0.2);
            }

            /* Jupiter Styling */
            .planet-jupiter {
                width: 95px;
                height: 95px;
                background: radial-gradient(circle at 30% 30%, #e5c290 0%, #b88a55 40%, #7d5027 75%, #3a1c06 100%);
                box-shadow: inset -6px -6px 20px rgba(0, 0, 0, 0.9), 0 0 30px rgba(229, 194, 144, 0.15);
            }

            /* Earth Styling (Atmospheric glow, moon, realistic details) */
            .planet-earth-container {
                position: relative;
                width: 82px;
                height: 82px;
            }
            
            .earth-moon {
                position: absolute;
                top: -15px;
                left: -20px;
                width: 16px;
                height: 16px;
                background: radial-gradient(circle at 30% 30%, #f1f2f6 0%, #a4b0be 55%, #2f3542 100%);
                border-radius: 50%;
                box-shadow: inset -3px -3px 6px rgba(0, 0, 0, 0.95), 0 0 10px rgba(241, 242, 246, 0.4);
                z-index: 2;
            }

            .planet-earth {
                width: 82px;
                height: 82px;
                border-radius: 50%;
                box-shadow: inset -8px -8px 20px rgba(0, 0, 0, 0.95), 0 0 22px rgba(43, 127, 211, 0.45);
                background: #0b224e;
            }

            /* Clouds drift for Earth */
            @keyframes rotate-clouds {
                0% { transform: translateX(-80px); }
                100% { transform: translateX(0); }
            }
            .clouds-group {
                animation: rotate-clouds 55s linear infinite;
            }

            /* Venus Styling */
            .planet-venus {
                width: 48px;
                height: 48px;
                background: radial-gradient(circle at 30% 30%, #f5d77f 0%, #d39331 50%, #613904 100%);
                box-shadow: inset -3px -3px 10px rgba(0, 0, 0, 0.8), 0 0 20px rgba(245, 215, 127, 0.2);
            }

            /* Antique Glittering Golden Key Styling */
            .magic-key-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: transform 0.3s ease;
                z-index: 10;
            }

            .magic-key {
                animation: key-float 3.5s ease-in-out infinite;
                filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.4));
                transform-origin: center;
            }

            .magic-key-link:hover .magic-key {
                transform: scale(1.15) rotate(-5deg);
                filter: drop-shadow(0 0 15px rgba(245, 176, 65, 0.9)) drop-shadow(0 6px 8px rgba(0, 0, 0, 0.5));
            }

            @keyframes key-float {
                0%, 100% { transform: translateY(0) rotate(0deg); }
                50% { transform: translateY(-5px) rotate(4deg); }
            }

            /* Key Sparkle Glints */
            .key-glint {
                opacity: 0;
                transform-origin: center;
                mix-blend-mode: overlay;
                filter: drop-shadow(0 0 4px #FFF);
            }

            .kg-1 { animation: glint-shine 2.8s ease-in-out infinite; }
            .kg-2 { animation: glint-shine 3.2s ease-in-out infinite; animation-delay: 0.9s; }
            .kg-3 { animation: glint-shine 2.5s ease-in-out infinite; animation-delay: 1.6s; }

            @keyframes glint-shine {
                0%, 100% { opacity: 0; transform: scale(0); }
                35%, 65% { opacity: 1; transform: scale(1.5); }
            }
        </style>
    </x-slot>


    <!-- Hero Section -->
    <section x-data="{
        images: {{ $heroImages->toJson() }},
        currentIndex: 0,
        init() {
            if (this.images.length > 1) {
                setInterval(() => {
                    this.currentIndex = (this.currentIndex + 1) % this.images.length;
                }, 7000);
            }
        }
    }" class="relative h-[85vh] min-h-[600px] flex items-center overflow-hidden">

        <!-- Background Slideshow -->
        <div class="absolute inset-0 z-0 bg-gray-900">
            <template x-for="(image, index) in images" :key="image.id">
                <div x-show="currentIndex === index" x-transition:enter="transition ease-in-out duration-[3000ms]"
                    x-transition:enter-start="opacity-0 scale-105 blur-sm"
                    x-transition:enter-end="opacity-100 scale-100 blur-0"
                    x-transition:leave="transition ease-in-out duration-[3000ms]"
                    x-transition:leave-start="opacity-100 scale-100 blur-0"
                    x-transition:leave-end="opacity-0 scale-110 blur-sm"
                    class="absolute inset-0 bg-cover bg-center origin-center will-change-transform"
                    :style="`background-image: url('${image.image_path}')`">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/20 to-transparent"></div>
                </div>
            </template>
            <!-- Fallback if no images -->
            <div x-show="images.length === 0" class="absolute inset-0 hero-gradient"></div>
            
            <!-- Ambient Glow Overlay (Always active) -->
            <div class="hero-glow-overlay"></div>
        </div>

        <!-- Content -->
        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 w-full">
            <div class="max-w-4xl">
                <h1 class="hero-title text-5xl lg:text-7xl font-black mb-8 leading-normal">
                    {{ __('Stories &') }}
                    <span class="text-gradient block mt-2 pb-2">{{ __('Learning') }}</span>
                </h1>
                <p class="hero-desc text-xl lg:text-2xl text-gray-200 mb-12 max-w-2xl leading-relaxed">
                    {{ __('A sparrow that absorbs, digests, cooks stories, and sows them as seeds') }}
                </p>
                <a href="#about"
                    class="inline-flex items-center gap-3 px-8 py-4 bg-burnt-orange hover:bg-orange-600 rounded-full text-lg font-bold transition transform hover:scale-105 shadow-2xl">
                    {{ __('Start Reading') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 9l-7 7-7-7" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-24 px-6 lg:px-8 bg-gray-950 relative overflow-hidden">
        
        <!-- Dynamic Mural Background (Physics-simulated bouncing particles) -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
            <!-- Dark immersive space overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-gray-950 via-gray-900/40 to-gray-950 opacity-80"></div>
            
            <!-- Physics-simulated elements will be dynamically appended here -->
            <div class="mural-bg-container absolute inset-0"></div>
        </div>

        <div class="max-w-4xl mx-auto relative z-10">
            <div id="about" class="scroll-trigger text-center mb-16" style="scroll-margin-top: 135px;">
                <h2 class="text-gradient text-4xl lg:text-6xl font-black mb-4 tracking-tight pb-2">
                    “கதைங்கோவின்” கதை!!!
                </h2>
                <div class="w-24 h-1 bg-burnt-orange mx-auto rounded-full"></div>
            </div>

            <div class="scroll-trigger story-card rounded-2xl p-8 lg:p-12 leading-relaxed text-gray-200">
                <div class="space-y-8 text-base lg:text-lg leading-loose text-justify tracking-wide">
                    <p class="first-letter:text-5xl first-letter:font-black first-letter:text-burnt-orange first-letter:mr-3 first-letter:float-left">
                        <strong>கதைங்கோ</strong> – கதைகளின் பெருவெளி. 
                        ஊர்க்குருவி ஒன்று காற்றில் மிதந்து வந்து கதைகளைச் சேகரிக்கிறது. அது கேட்ட கதைகள், கண்ட காட்சிகள், உணர்ந்த உணர்வுகள் யாவும் அதன் நெஞ்சில் தங்கிவிடுகின்றன. 
                    </p>
                    <p>
                        அந்தக் கதைகளை அது ஜீரணித்து, பக்குவமாகச் சமைத்து, பின் ஒரு விதையாக இந்த மண்ணில் தூவுகிறது. அந்த விதைகள் முளைத்து இன்று ஒரு பெரிய சோலையாக, <strong>"கதைங்கோ"</strong> என்ற இந்தத் இணையத் தளமாக உருவெடுத்துள்ளது.
                    </p>
                    <p>
                        இங்கு எழுதும் ஒவ்வொரு எழுத்தாளரும் விண்மீன்களைப் போலத் தனித்துவமானவர்கள். அவர்கள் எழுதும் ஒவ்வொரு கதையும் இந்த அண்டவெளியில் சுடரும் ஒளிக் கீற்றுகள். வாசகர்களாகிய நீங்களும், எழுத்தாளர்களாகிய நாமும் இணைந்து இந்தக் கதை அண்டத்தை இன்னும் அழகாக்குகிறோம்.
                    </p>
                    <div class="flex items-center justify-center gap-4 mt-12 flex-wrap">
                        <p class="text-center font-bold text-gradient text-xl lg:text-2xl m-0">
                            கதைப் பெருவெளிக்குள் நுழைய...
                        </p>
                        <a href="#kathaingos-universe" onclick="event.preventDefault(); document.getElementById('kathaingos-universe').scrollIntoView({ behavior: 'smooth' })" class="magic-key-link group">
                            <!-- Golden Key SVG -->
                            <svg class="magic-key w-20 h-20" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    <linearGradient id="gold-key-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#FFE066" />
                                        <stop offset="50%" stop-color="#F5B041" />
                                        <stop offset="100%" stop-color="#9A7D0A" />
                                    </linearGradient>
                                    <filter id="gold-key-glow" x="-20%" y="-20%" width="140%" height="140%">
                                        <feGaussianBlur stdDeviation="2.5" result="blur" />
                                        <feComposite in="SourceGraphic" in2="blur" operator="over" />
                                    </filter>
                                </defs>
                                <!-- Antique Head of the key -->
                                <circle cx="30" cy="50" r="15" stroke="url(#gold-key-grad)" stroke-width="4" fill="none" filter="url(#gold-key-glow)" />
                                <circle cx="30" cy="50" r="7" stroke="url(#gold-key-grad)" stroke-width="2" fill="none" />
                                <path d="M30,35 L30,65 M15,50 L45,50" stroke="url(#gold-key-grad)" stroke-width="2" stroke-linecap="round" />
                                
                                <!-- Shaft/Stem -->
                                <path d="M45,50 L80,50" stroke="url(#gold-key-grad)" stroke-width="4.5" stroke-linecap="round" filter="url(#gold-key-glow)" />
                                
                                <!-- Key cuts/bit (antique shape) -->
                                <path d="M70,50 L70,62 L75,62 L75,50 M75,50 L75,62 L80,62 L80,50" fill="url(#gold-key-grad)" filter="url(#gold-key-glow)" />
                                <path d="M72,55 L74,55 M77,55 L79,55" stroke="#3D2905" stroke-width="1" stroke-linecap="round" />
                                
                                <!-- Glitter sparkles on the key -->
                                <circle class="key-glint kg-1" cx="30" cy="35" r="1.5" fill="#FFF" />
                                <circle class="key-glint kg-2" cx="70" cy="62" r="1.5" fill="#FFF" />
                                <circle class="key-glint kg-3" cx="45" cy="50" r="1.5" fill="#FFF" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Story Universe Section -->
    <section id="kathaingos-universe" class="py-24 universe-bg relative border-t border-gray-800">
        <!-- Celestial Starfield & Planets Background -->
        <div class="celestial-background absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <!-- Dynamic stars and planets will be generated here -->
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10">
            <div class="scroll-trigger text-center mb-16">
                <!-- Level 1: Main Title -->
                <h2 class="text-gradient text-4xl lg:text-5xl font-black mb-3">
                    {{ app()->getLocale() === 'ta' ? 'கதைங்கோவின் பிரபஞ்சம்' : "Kathaingo's Universe" }}
                </h2>
                <div class="w-32 h-1 bg-burnt-orange mx-auto rounded-full mb-4"></div>
                
                <!-- Level 2: Philosophy / Tagline / Brand Statement -->
                <p class="text-burnt-orange font-bold text-lg lg:text-xl tracking-wide mt-4 mb-3">
                    {{ app()->getLocale() === 'ta'
                        ? 'அபுனைவையும் புனைவைப்போல் சுவையாகச் சொல்வதே கதைங்கோவின் உயிர்நாடி'
                        : 'Telling non-fiction as deliciously as fiction is the lifeblood of Kathaingo.' }}
                </p>
                
                <!-- Level 3: Description / Explanation Text -->
                <p class="font-bold text-sm lg:text-base max-w-xl mx-auto mt-2 leading-relaxed pb-1" style="background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.95)); letter-spacing: 0.025em;">
                    {{ app()->getLocale() === 'ta' 
                        ? 'பல்வேறு தலைப்புகளும் எழுத்தாளர்களும், கதைங்கோவின் பிரபஞ்சத்தில் விண்மீன்களாய் மிளிர்கின்றனர்.' 
                        : 'Various topics and bloggers sparkle like stars in Kathaingo\'s universe.' }}
                </p>
            </div>

            <!-- Two-Column Universe Space -->
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-16">
                <!-- Left Column (Categories) -->
                <div class="scroll-trigger flex flex-col bg-gray-950/40 border border-gray-800/80 rounded-2xl p-6 backdrop-blur-sm shadow-2xl">
                    <h3 class="text-white text-xl lg:text-2xl font-black mb-6 flex items-center gap-2 pb-2 border-b border-gray-800">
                        <span>🔖</span> {{ app()->getLocale() === 'ta' ? 'தலைப்புகள்' : 'Topics' }}
                    </h3>
                    <div id="categories-universe" class="universe-container rounded-xl bg-gray-950/20 border border-gray-900/50">
                        <!-- Floating words will be appended here dynamically by JS -->
                    </div>
                </div>

                <!-- Right Column (Writers) -->
                <div class="scroll-trigger flex flex-col bg-gray-950/40 border border-gray-800/80 rounded-2xl p-6 backdrop-blur-sm shadow-2xl">
                    <h3 class="text-white text-xl lg:text-2xl font-black mb-6 flex items-center gap-2 pb-2 border-b border-gray-800">
                        <span>👥</span> {{ app()->getLocale() === 'ta' ? 'நம்ம பதிவர்கள்' : 'Our Bloggers' }}
                    </h3>
                    <div id="writers-universe" class="universe-container rounded-xl bg-gray-950/20 border border-gray-900/50">
                        <!-- Floating words will be appended here dynamically by JS -->
                    </div>
                </div>
            </div>

            <!-- Section Divider -->
            <div class="my-20 border-t border-gray-800/60"></div>

            <!-- Complete List Section -->
            <div id="core-categories-featured-bloggers" class="scroll-trigger text-center mb-12" style="scroll-margin-top: 140px;">
                <h3 class="text-gradient text-3xl font-black mb-3">
                    {{ app()->getLocale() === 'ta' ? 'முக்கிய பிரிவுகளும் சிறப்புப் பதிவர்களும்' : 'Core Categories & Featured Bloggers' }}
                </h3>
                <p class="font-bold text-sm lg:text-base max-w-lg mx-auto mt-2 leading-relaxed pb-1" style="background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.95)); letter-spacing: 0.025em;">
                    {{ app()->getLocale() === 'ta' 
                        ? 'முக்கிய பிரிவுகளையும் சிறப்புப் பதிவர்களையும் கண்டறியக் கீழே உருட்டவும்.' 
                        : 'Scroll down to explore core categories and featured bloggers.' }}
                </p>
            </div>

            <!-- Side-by-side Scrolling Wheel Lists -->
            <div class="grid md:grid-cols-2 gap-8 lg:gap-12 max-w-5xl mx-auto">
                <!-- Categories Scroll Wheel -->
                <div class="bg-gray-950/60 border border-gray-800/80 rounded-2xl p-6 backdrop-blur-md shadow-xl flex flex-col">
                    <h4 class="text-white font-bold text-xl mb-4 flex items-center justify-between pb-2 border-b border-gray-800">
                        <span>📚 {{ app()->getLocale() === 'ta' ? 'உள்ளடக்கம்' : 'Content' }}</span>
                        <span class="text-xs text-gray-500 font-normal">({{ count($universeCategories) }} {{ app()->getLocale() === 'ta' ? 'தலைப்புகள்' : 'items' }})</span>
                    </h4>
                    
                    <div class="overflow-y-auto pr-2 space-y-2.5 max-h-[380px] scrollbar-thin scrollbar-thumb-burnt-orange scrollbar-track-gray-900 custom-scroll-container">
                        @foreach($universeCategories as $category)
                            <a href="{{ $category['url'] }}" 
                               class="group flex items-center justify-between p-3.5 bg-gray-900/40 hover:bg-gray-900/90 border border-gray-800/40 hover:border-burnt-orange/50 rounded-xl transition duration-300 transform hover:-translate-x-1">
                                <div class="flex flex-col">
                                    <span class="text-gray-200 group-hover:text-white font-semibold text-sm transition">
                                        {{ $category['name'] }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 uppercase tracking-wider mt-0.5">
                                        {{ str_replace('_', ' ', $category['type']) }}
                                    </span>
                                </div>
                                <span class="w-6 h-6 rounded-full bg-gray-900 border border-gray-800 group-hover:border-burnt-orange/40 text-gray-500 group-hover:text-burnt-orange flex items-center justify-center text-xs transition duration-300">
                                    →
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Writers Scroll Wheel -->
                <div class="bg-gray-950/60 border border-gray-800/80 rounded-2xl p-6 backdrop-blur-md shadow-xl flex flex-col">
                    <h4 class="text-white font-bold text-lg mb-4 flex items-center justify-between pb-2 border-b border-gray-800">
                        <span>⭐ {{ app()->getLocale() === 'ta' ? 'சிறப்புப் பதிவர்கள்' : 'Featured Bloggers' }}</span>
                        <span class="text-xs text-gray-500 font-normal">({{ count($universeWriters) }} {{ app()->getLocale() === 'ta' ? 'பதிவர்கள்' : 'bloggers' }})</span>
                    </h4>
                    <div class="overflow-y-auto pr-2 space-y-2.5 max-h-[380px] scrollbar-thin scrollbar-thumb-burnt-orange scrollbar-track-gray-900 custom-scroll-container">
                        @foreach($universeWriters as $writer)
                            <a href="{{ url('/authors/' . $writer['slug']) }}" 
                               class="group flex items-center justify-between p-3 bg-gray-900/40 hover:bg-gray-900/90 border border-gray-800/40 hover:border-burnt-orange/50 rounded-xl transition duration-300 transform hover:translate-x-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-burnt-orange/15 border border-burnt-orange/30 group-hover:border-burnt-orange/60 text-burnt-orange font-bold text-sm flex items-center justify-center transition overflow-hidden">
                                        @php
                                            $isKathaingo = str_contains(strtolower($writer['slug'] ?? ''), 'kathaingo') ||
                                                           str_contains(mb_strtolower($writer['name'] ?? '', 'UTF-8'), 'கதைங்கோ');
                                        @endphp
                                        @if($isKathaingo)
                                            <img src="{{ asset('images/logo/apple-touch-icon.png') }}" alt="" class="w-full h-full object-cover bg-slate-900">
                                        @else
                                            {{ mb_substr($writer['name'], 0, 1, 'UTF-8') }}
                                        @endif
                                    </div>
                                    <span class="text-gray-200 group-hover:text-white font-semibold text-sm transition">
                                        {{ $writer['name'] }}
                                    </span>
                                </div>
                                <span class="w-6 h-6 rounded-full bg-gray-900 border border-gray-800 group-hover:border-burnt-orange/40 text-gray-500 group-hover:text-burnt-orange flex items-center justify-center text-xs transition duration-300">
                                    →
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>



    <x-slot name="scripts">
        <!-- Universe Constellation Controller JS -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Data sources injected from Laravel
                const categoriesPool = @json($universeCategories);
                const writersPool = @json($universeWriters);

                // Setup Intersection Observer for scroll triggers
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -50px 0px'
                });

                document.querySelectorAll('.scroll-trigger').forEach(el => observer.observe(el));

                // Celestial Background Spawning (Stars & Planets)
                const celestialBg = document.querySelector('.celestial-background');
                if (celestialBg) {
                    // Spawn 65 stars
                    const totalStars = 65;
                    for (let i = 0; i < totalStars; i++) {
                        const star = document.createElement('div');
                        const isBig = Math.random() > 0.75;
                        const colorRand = Math.random();
                        
                        star.className = `celestial-star ${isBig ? 'star-big' : 'star-small'}`;
                        if (colorRand > 0.85) {
                            star.classList.add('star-yellow');
                        } else if (colorRand > 0.7) {
                            star.classList.add('star-blue');
                        }
                        
                        // Random positions
                        star.style.left = `${Math.random() * 100}%`;
                        star.style.top = `${Math.random() * 100}%`;
                        
                        // Animation parameters
                        const duration = 3 + Math.random() * 6; // 3s to 9s
                        const delay = Math.random() * -8; // start immediately
                        const animType = Math.random() > 0.5 ? 'twinkle-slow' : 'twinkle-fast';
                        
                        star.style.animation = `${animType} ${duration}s ease-in-out ${delay}s infinite`;
                        celestialBg.appendChild(star);
                    }

                    // Spawn Planets (placed at distinct positions in the background)
                    const planets = [
                        {
                            html: `
                                <div class="planet-saturn-body">
                                    <div class="planet-saturn-ring"></div>
                                    <div class="saturn-moon saturn-moon-1"></div>
                                    <div class="saturn-moon saturn-moon-2"></div>
                                </div>
                            `,
                            style: { left: '8%', top: '15%', animationDelay: '0s' }
                        },
                        {
                            html: '<div class="planet-mars"></div>',
                            style: { right: '12%', top: '35%', animationDelay: '-10s' }
                        },
                        {
                            html: `
                                <div class="planet-earth-container">
                                    <div class="earth-moon"></div>
                                    <div class="planet-earth">
                                        <svg viewBox="0 0 100 100" class="w-full h-full">
                                            <defs>
                                                <radialGradient id="oceanGrad" cx="30%" cy="30%" r="70%">
                                                    <stop offset="0%" stop-color="#2b7fd3" />
                                                    <stop offset="60%" stop-color="#154283" />
                                                    <stop offset="100%" stop-color="#051530" />
                                                </radialGradient>
                                                <linearGradient id="landGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" stop-color="#27ae60" />
                                                    <stop offset="40%" stop-color="#2ecc71" />
                                                    <stop offset="75%" stop-color="#e2b13c" />
                                                    <stop offset="100%" stop-color="#a05e15" />
                                                </linearGradient>
                                                <clipPath id="planetClip">
                                                    <circle cx="50" cy="50" r="49" />
                                                </clipPath>
                                            </defs>
                                            <g clip-path="url(#planetClip)">
                                                <!-- Water -->
                                                <circle cx="50" cy="50" r="49" fill="url(#oceanGrad)" />
                                                <!-- Greenland -->
                                                <path d="M 52,10 Q 58,12 55,6 T 47,8 Z" fill="url(#landGrad)" opacity="0.95" />
                                                <!-- North America -->
                                                <path d="M 12,22 Q 25,12 48,15 T 62,24 T 48,32 T 38,40 T 32,52 Q 28,45 22,48 T 20,38 T 12,22 Z" fill="url(#landGrad)" opacity="0.95" />
                                                <!-- South America -->
                                                <path d="M 32,52 Q 44,52 64,55 Q 60,65 52,82 Q 48,88 47,93 Q 41,75 32,52 Z" fill="url(#landGrad)" opacity="0.95" />
                                                <!-- Swirling Clouds -->
                                                <g class="clouds-group">
                                                    <path d="M -90,20 Q -70,30 -50,15 T -20,25 T 10,12 T 40,28 T 70,15 T 100,25" fill="none" stroke="rgba(255,255,255,0.75)" stroke-width="5" stroke-linecap="round" style="filter: blur(1.5px);" />
                                                    <path d="M -80,50 Q -60,40 -30,55 T 0,45 T 30,55 T 60,42 T 90,52" fill="none" stroke="rgba(255,255,255,0.60)" stroke-width="4.5" stroke-linecap="round" style="filter: blur(1.2px);" />
                                                    <path d="M 18,24 A 6,6 0 1,1 28,34 A 4,4 0 1,1 24,28" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="2" style="filter: blur(0.8px);" />
                                                    <path d="M 50,62 A 8,8 0 1,1 62,74 A 6,6 0 1,1 56,66" fill="none" stroke="rgba(255,255,255,0.65)" stroke-width="2.5" style="filter: blur(1px);" />
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            `,
                            style: { left: '72%', top: '17%', animationDelay: '-15s' }
                        },
                        {
                            html: '<div class="planet-venus"></div>',
                            style: { right: '35%', top: '48%', animationDelay: '-8s' }
                        },
                        {
                            html: '<div class="planet-neptune"></div>',
                            style: { left: '15%', bottom: '20%', animationDelay: '-22s' }
                        },
                        {
                            html: '<div class="planet-jupiter"></div>',
                            style: { right: '8%', bottom: '10%', animationDelay: '-5s' }
                        }
                    ];

                    planets.forEach(p => {
                        const pDiv = document.createElement('div');
                        pDiv.className = 'celestial-planet';
                        pDiv.innerHTML = p.html;
                        Object.assign(pDiv.style, p.style);
                        celestialBg.appendChild(pDiv);
                    });
                }

                // Universe Constellation system
                function runUniverse(containerId, items, itemType) {
                    const container = document.getElementById(containerId);
                    if (!container || items.length === 0) return;

                    // Configuration parameters
                    const numCols = 3;
                    const numRows = 3;
                    const totalSlots = numCols * numRows;
                    const maxVisible = Math.min(6, items.length);
                    const activeSlots = new Array(totalSlots).fill(false);
                    let itemIndex = 0;

                    // Shuffle array helper to randomize pool sequence
                    const shuffledItems = [...items].sort(() => Math.random() - 0.5);

                    function getFreeSlot() {
                        const freeIndices = [];
                        for (let i = 0; i < totalSlots; i++) {
                            if (!activeSlots[i]) freeIndices.push(i);
                        }
                        if (freeIndices.length === 0) return -1;
                        return freeIndices[Math.floor(Math.random() * freeIndices.length)];
                    }

                    function spawnItem() {
                        const slot = getFreeSlot();
                        if (slot === -1) return;

                        activeSlots[slot] = true;

                        // Choose next item in pool
                        const item = shuffledItems[itemIndex];
                        itemIndex = (itemIndex + 1) % shuffledItems.length;

                        // Construct grid cell coordinates
                        const col = slot % numCols;
                        const row = Math.floor(slot / numCols);

                        // Add padding to keep away from edges
                        const cellWidth = 100 / numCols;
                        const cellHeight = 100 / numRows;
                        
                        const left = (col * cellWidth) + 5 + (Math.random() * (cellWidth - 10));
                        const top = (row * cellHeight) + 5 + (Math.random() * (cellHeight - 10));

                        // Create link element
                        const el = document.createElement('a');
                        el.href = itemType === 'category' ? item.url : `/authors/${item.slug}`;
                        el.className = 'universe-item text-xs md:text-sm font-semibold tracking-wide text-gray-300 max-w-[200px] text-center whitespace-normal break-words';
                        el.textContent = item.name;
                        
                        // Star styling offsets
                        el.style.left = `${left}%`;
                        el.style.top = `${top}%`;

                        // Generate random parameters for float and twinkle cycles
                        const floatDuration = 10 + Math.random() * 6; // 10s to 16s
                        const floatDelay = -(Math.random() * 5); // randomized initial state
                        const twinkleDuration = 7 + Math.random() * 4; // 7s to 11s

                        el.style.animation = `
                            float-drift ${floatDuration}s ease-in-out ${floatDelay}s infinite, 
                            twinkle-glow ${twinkleDuration}s ease-in-out infinite
                        `;

                        // We check when twinkle is done to cycle to the next element
                        el.addEventListener('animationiteration', (e) => {
                            if (e.animationName === 'twinkle-glow') {
                                // Let the element finish this loop, then remove it
                                el.style.opacity = 0;
                                setTimeout(() => {
                                    el.remove();
                                    activeSlots[slot] = false;
                                    spawnItem();
                                }, 500);
                            }
                        });

                        container.appendChild(el);
                    }

                    // Initial populate
                    for (let i = 0; i < maxVisible; i++) {
                        // Stagger spawn times
                        setTimeout(spawnItem, i * 1400);
                    }
                }

                // Launch Left Column (Categories)
                runUniverse('categories-universe', categoriesPool, 'category');

                // Launch Right Column (Writers)
                runUniverse('writers-universe', writersPool, 'writer');

                // Dynamic Mural Physics Simulator (10 elements bouncing and swirling in slow motion)
                const muralAbout = document.querySelector('#about');
                const muralContainer = muralAbout ? muralAbout.closest('section') : null;
                const muralBg = document.querySelector('.mural-bg-container');
                if (muralContainer && muralBg) {
                    let width = muralContainer.clientWidth;
                    let height = muralContainer.clientHeight;
                    
                    window.addEventListener('resize', () => {
                        width = muralContainer.clientWidth;
                        height = muralContainer.clientHeight;
                    });

                    const imagesPool = [
                        '{{ asset("images/mural/fairy_tale_mural.png") }}',
                        '{{ asset("images/mural/scifi_mural.png") }}',
                        '{{ asset("images/mural/epic_mural.png") }}',
                        '{{ asset("images/mural/mystery_mural.png") }}',
                        '{{ asset("images/mural/travel_mural.png") }}',
                        '{{ asset("images/mural/carpet_mural.png") }}',
                        '{{ asset("images/mural/pirate_mural.png") }}',
                        '{{ asset("images/mural/war_mural.png") }}',
                        '{{ asset("images/mural/yali_mural.png") }}',
                        '{{ asset("images/mural/anime_mural.png") }}'
                    ];

                    const borderColors = [
                        'rgba(243, 156, 18, 0.45)',
                        'rgba(168, 85, 247, 0.45)',
                        'rgba(245, 158, 11, 0.45)',
                        'rgba(20, 184, 166, 0.45)',
                        'rgba(249, 115, 22, 0.45)'
                    ];

                    const shadowGlows = [
                        'rgba(243,156,18,0.4)',
                        'rgba(168,85,247,0.4)',
                        'rgba(245,158,11,0.4)',
                        'rgba(20,184,166,0.4)',
                        'rgba(249,115,22,0.4)'
                    ];

                    const items = [];
                    const isMobile = window.innerWidth < 768;
                    
                    imagesPool.forEach((src, idx) => {
                        const el = document.createElement('div');
                        const size = isMobile 
                            ? (80 + Math.random() * 40)
                            : (130 + Math.random() * 50);
                        
                        const radius = size / 2;
                        
                        el.className = 'absolute rounded-full overflow-hidden pointer-events-none transition-opacity duration-1000 opacity-0 will-change-[left,top]';
                        el.style.width = `${size}px`;
                        el.style.height = `${size}px`;
                        el.style.border = `2.5px solid ${borderColors[idx % borderColors.length]}`;
                        el.style.boxShadow = `0 0 30px ${shadowGlows[idx % shadowGlows.length]}`;
                        el.style.filter = 'brightness(1.15) contrast(1.1)';
                        
                        const img = document.createElement('img');
                        img.src = src;
                        if (src.includes('fairy_tale_mural.png')) {
                            img.className = 'w-full h-full object-cover scale-[1.5] select-none';
                        } else {
                            img.className = 'w-full h-full object-cover scale-115 select-none';
                        }
                        el.appendChild(img);
                        
                        muralBg.appendChild(el);
                        
                        const x = radius + Math.random() * (width - size);
                        const y = radius + Math.random() * (height - size);
                        
                        const angle = Math.random() * Math.PI * 2;
                        const speed = 0.35 + Math.random() * 0.35;
                        const vx = Math.cos(angle) * speed;
                        const vy = Math.sin(angle) * speed;
                        
                        items.push({
                            el,
                            x,
                            y,
                            vx,
                            vy,
                            radius,
                            size,
                            mass: size
                        });

                        setTimeout(() => {
                            el.style.opacity = '0.38';
                        }, idx * 150);
                    });

                    function updatePhysics() {
                        items.forEach(item => {
                            item.x += item.vx;
                            item.y += item.vy;

                            if (item.x - item.radius < 0) {
                                item.x = item.radius;
                                item.vx = Math.abs(item.vx);
                            } else if (item.x + item.radius > width) {
                                item.x = width - item.radius;
                                item.vx = -Math.abs(item.vx);
                            }

                            if (item.y - item.radius < 0) {
                                item.y = item.radius;
                                item.vy = Math.abs(item.vy);
                            } else if (item.y + item.radius > height) {
                                item.y = height - item.radius;
                                item.vy = -Math.abs(item.vy);
                            }
                        });

                        for (let i = 0; i < items.length; i++) {
                            for (let j = i + 1; j < items.length; j++) {
                                const c1 = items[i];
                                const c2 = items[j];

                                const dx = c2.x - c1.x;
                                const dy = c2.y - c1.y;
                                const dist = Math.sqrt(dx * dx + dy * dy);
                                const minDist = c1.radius + c2.radius;

                                if (dist < minDist) {
                                    const overlap = minDist - dist;
                                    const nx = dist > 0 ? (dx / dist) : 1;
                                    const ny = dist > 0 ? (dy / dist) : 0;
                                    
                                    c1.x -= nx * (overlap / 2);
                                    c1.y -= ny * (overlap / 2);
                                    c2.x += nx * (overlap / 2);
                                    c2.y += ny * (overlap / 2);

                                    const kx = c1.vx - c2.vx;
                                    const ky = c1.vy - c2.vy;
                                    const vn = kx * nx + ky * ny;

                                    if (vn > 0) {
                                        const impulse = (2 * vn) / (c1.mass + c2.mass);
                                        c1.vx -= impulse * c2.mass * nx;
                                        c1.vy -= impulse * c2.mass * ny;
                                        c2.vx += impulse * c1.mass * nx;
                                        c2.vy += impulse * c1.mass * ny;
                                    }
                                }
                            }
                        }

                        items.forEach(item => {
                            item.el.style.left = `${item.x - item.radius}px`;
                            item.el.style.top = `${item.y - item.radius}px`;
                        });

                        requestAnimationFrame(updatePhysics);
                    }

                    requestAnimationFrame(updatePhysics);
                }
            });
        </script>
    </x-slot>
</x-public-layout>
