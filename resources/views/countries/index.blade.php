<x-public-layout>
    <x-slot name="title">
        {{ config('app.name', 'கதைங்கோ') }} - {{ __('Countries') }}
    </x-slot>

    <x-slot name="styles">
        <style>
            .map-container {
                position: relative;
                background: radial-gradient(circle, #1e293b 0%, #0f172a 100%);
                border: 1px solid #334155;
            }

            #world-map {
                width: 100%;
                height: auto;
                max-height: 65vh;
                filter: drop-shadow(0 10px 15px rgba(0, 0, 0, 0.5));
            }

            /* Default non-active country paths & groups */
            #world-map path, #world-map g {
                fill: #1e293b;
                stroke: #0f172a;
                stroke-width: 0.6;
                transition: all 0.3s ease;
            }

            #world-map path:hover, #world-map g:hover {
                fill: #334155;
            }

            /* Active country highlighted in burnt orange */
            #world-map .active-country {
                fill: #d35400 !important;
                stroke: #ff7675 !important;
                stroke-width: 1.0;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            #world-map .active-country:hover {
                fill: #f39c12 !important;
                filter: drop-shadow(0 0 12px rgba(211, 84, 0, 0.8));
                transform: scale(1.02);
                outline: none;
            }
        </style>
    </x-slot>

    <!-- Main Content -->
    <main class="flex-grow pt-32 pb-16 px-6 lg:px-8 max-w-7xl mx-auto w-full">
        <div class="mb-12">
            <h1 class="text-5xl font-black mb-4 tracking-tight">
                @if(app()->getLocale() === 'en')
                    Stories by <span class="text-gradient">Country</span>
                @else
                    நாடுகள் வாரியாக <span class="text-gradient">படைப்புகள்</span>
                @endif
            </h1>
            <p class="text-lg text-gray-400 max-w-2xl leading-relaxed">
                {{ __('Click on the highlighted countries on the world map and enjoy reading the stories from those regions.') }}
            </p>
        </div>

        <!-- Interactive Map Container -->
        <div class="map-container rounded-3xl p-8 overflow-hidden shadow-2xl relative">
            <div class="absolute top-4 left-4 bg-gray-900/80 backdrop-blur border border-gray-800 rounded-lg px-4 py-2 text-xs text-gray-400 z-10 flex items-center gap-2">
                <span class="w-3 h-3 bg-burnt-orange rounded-full inline-block border border-orange-400 animate-pulse"></span>
                <span>{{ __('Glowing countries have published stories') }}</span>
            </div>

            <!-- Inlined Vector SVG World Map -->
            @include('countries.svg')
        </div>
    </main>

    <!-- Map Tooltip -->
    <div id="map-tooltip" class="absolute hidden bg-gray-900/95 border border-gray-800 rounded-xl px-4 py-2.5 shadow-2xl z-50 pointer-events-none text-sm transition-all duration-100 flex flex-col justify-center">
    </div>

    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const activeCountries = @json($activeCountries);
                const countriesMeta = @json(\App\Helpers\CountryHelper::getCountries());
                const tooltip = document.getElementById('map-tooltip');
                
                // Select all paths and groups inside the world-map SVG
                const paths = document.querySelectorAll('#world-map path');
                const groups = document.querySelectorAll('#world-map g');
                
                function registerInteractivity(el) {
                    const id = el.getAttribute('id');
                    if (!id) return;
                    
                    const code = id.toLowerCase();
                    
                    if (activeCountries.hasOwnProperty(code)) {
                        el.classList.add('active-country');
                        
                        el.addEventListener('click', () => {
                            window.location.href = `/countries/${code}`;
                        });
                        
                        el.addEventListener('mouseenter', (e) => {
                            const count = activeCountries[code];
                            const meta = countriesMeta[code] || { name_ta: code.toUpperCase(), name_en: code.toUpperCase() };
                            const locale = @json(app()->getLocale());
                            const countryName = locale === 'en' ? meta.name_en : meta.name_ta;
                            const countrySubName = locale === 'en' ? meta.name_ta : meta.name_en;
                            const storiesLabel = locale === 'en' ? (count === 1 ? 'Story' : 'Stories') : 'பதிவுகள்';
                            tooltip.innerHTML = `
                                <div class="font-extrabold text-burnt-orange text-sm mb-0.5">${countryName}</div>
                                <div class="text-[10px] text-gray-400 font-semibold tracking-wide uppercase mb-1.5">${countrySubName}</div>
                                <div class="text-xs text-white font-bold bg-burnt-orange/20 px-2.5 py-1 rounded-md border border-burnt-orange/40 w-fit">
                                    ${count} ${storiesLabel}
                                </div>
                            `;
                            tooltip.classList.remove('hidden');
                        });
                        
                        el.addEventListener('mousemove', (e) => {
                            tooltip.style.left = (e.pageX + 15) + 'px';
                            tooltip.style.top = (e.pageY + 15) + 'px';
                        });
                        
                        el.addEventListener('mouseleave', () => {
                            tooltip.classList.add('hidden');
                        });
                    }
                }

                paths.forEach(registerInteractivity);
                groups.forEach(registerInteractivity);
            });
        </script>
    </x-slot>
</x-public-layout>
