<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Create New Post') }}
        </h2>
    </x-slot>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 border border-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-white">
                    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" x-data="postTaxonomy()" id="post-form" @submit.prevent="handleFormSubmit($event)">
                        @csrf

                        <!-- Autosave Notice Banner -->
                        <div x-show="hasAutosave" x-transition class="mb-6 bg-orange-950/30 border border-burnt-orange/40 text-orange-400 p-4 rounded-xl flex items-center justify-between shadow-lg" x-cloak>
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span class="text-sm">நாங்கள் ஒரு சேமிக்கப்படாத உள்ளூர் நகலைக் கண்டறிந்துள்ளோம் (<span x-text="autosaveTime"></span>). We found an unsaved local draft.</span>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="loadAutosave()" class="text-xs bg-burnt-orange hover:bg-orange-600 text-white font-bold py-1.5 px-3 rounded-full transition shadow-md">Load Draft</button>
                                <button type="button" @click="discardAutosave()" class="text-xs bg-gray-800 hover:bg-gray-750 text-gray-300 font-bold py-1.5 px-3 rounded-full transition border border-gray-700">Discard</button>
                            </div>
                        </div>


                        <!-- Title -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center">
                                <x-input-label for="title" :value="__('Title')" />
                                <div class="flex items-center gap-2">
                                    <!-- Parent Writing Assistant Button -->
                                    <button type="button" id="parent-writing-assistant-btn" class="flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full border border-gray-800 bg-gray-950 text-gray-400 hover:text-white hover:border-gray-700 cursor-pointer transition-all duration-150" title="எழுத்துதவியாளர்">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Assistant</span>
                                    </button>
                                    
                                    <!-- Parent Review Article Button -->
                                    <button type="button" id="parent-review-article-btn" class="flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full border border-gray-800 bg-gray-950 text-gray-400 hover:text-white hover:bg-burnt-orange/20 hover:border-burnt-orange/50 hover:shadow-lg hover:shadow-burnt-orange/10 cursor-pointer transition-all duration-150" title="பதிவை மதிப்பிடு">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Review Article</span>
                                    </button>

                                    @if(\App\Helpers\SettingHelper::get('global_language_helper_enabled', '1') === '1')
                                        <div class="flex items-center gap-1 bg-gray-950 border border-gray-800/80 rounded-full p-0.5 select-none" title="Transliteration Mode">
                                            <button type="button" class="lang-toggle-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold border-0 cursor-pointer transition-all duration-150 bg-burnt-orange text-white" data-lang="en">En</button>
                                            <button type="button" class="lang-toggle-btn px-2.5 py-0.5 rounded-full text-[10px] font-bold border-0 cursor-pointer transition-all duration-150 bg-transparent text-gray-400 hover:text-white" data-lang="ta">த</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                                :value="old('title')" required autofocus data-kathaingo-transliterate="true" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Content with Rich Text Editor -->
                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <textarea id="content" name="content"
                                class="block mt-1 w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm"
                                rows="15">{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <!-- Excerpt -->
                        <div class="mb-4">
                            <x-input-label for="excerpt" :value="__('Excerpt / சுருக்கம் (Optional)')" />
                            <textarea id="excerpt" name="excerpt"
                                class="block mt-1 w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm"
                                rows="3" data-kathaingo-transliterate="true">{{ old('excerpt') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Brief summary of the article to show on search and listings</p>
                            <x-input-error :messages="$errors->get('excerpt')" class="mt-2" />
                        </div>

                        <!-- Featured Image Upload -->
                        <div class="mb-4">
                            <x-input-label for="featured_image" :value="__('Featured Image')" />
                            <input type="file" id="featured_image" name="featured_image" accept="image/*"
                                class="block mt-1 w-full text-white border border-gray-800 bg-gray-950 rounded-md shadow-sm focus:border-burnt-orange focus:ring-burnt-orange" />
                            <p class="mt-1 text-sm text-gray-400">Upload a featured image for your post (JPG, PNG, GIF - Max 2MB)</p>
                            <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />
                        </div>

                        <!-- Image URL (Alternative) -->
                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Image URL (Alternative)')" />
                            <x-text-input id="image" class="block mt-1 w-full" type="url" name="image"
                                :value="old('image')" placeholder="https://example.com/image.jpg" />
                            <p class="mt-1 text-sm text-gray-500">Or provide an external image URL</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <!-- Video / Social Media URLs -->
                        <div class="mb-4">
                            <x-input-label for="video_url" :value="__('Social Media / Video URLs (YouTube, X, Instagram, Facebook, TikTok) / சமூக ஊடக இணைப்புகள் (Optional)')" />
                            <textarea id="video_url" name="video_url" rows="3"
                                class="block mt-1 w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm"
                                placeholder="Paste URLs here (one per line or comma separated)&#10;e.g.&#10;https://www.youtube.com/watch?v=...&#10;https://x.com/username/status/...&#10;https://www.instagram.com/p/...&#10;https://www.tiktok.com/@user/video/...&#10;https://www.facebook.com/.../posts/...&#10;https://www.facebook.com/.../videos/...">{{ old('video_url') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">சமூக ஊடக அல்லது வீடியோ இணைப்புகளை ஒரு வரியில் ஒன்று வீதம் அல்லது கமாவால் பிரித்து சேர்க்கலாம் (YouTube, X, Instagram, Facebook, TikTok).</p>
                            <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                        </div>


                        <!-- Author (பதிவர்) Selection -->
                        @if(auth()->user()->is_admin)
                            <div class="mb-4">
                                <x-input-label for="author_subcategory_id" :value="__('Author Name / பதிவர் (Required)')" />
                                <select id="author_subcategory_id" name="author_subcategory_id" required
                                    class="block mt-1 w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm">
                                    <option value="">Select an author (பதிவர்)</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->id }}" {{ old('author_subcategory_id') == $author->id ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('author_subcategory_id')" class="mt-2" />
                            </div>
                        @else
                            @if(auth()->user()->authorProfile)
                                <input type="hidden" name="author_subcategory_id" value="{{ auth()->user()->authorProfile->id }}">
                            @endif
                        @endif

                        <!-- Category Selection with Dynamic Taxonomy -->
                        <div>
                            <!-- Advanced Classification Accordion -->
                            <div x-data="{ open: {{ ($errors->has('category_id') || $errors->has('metadata_value_ids') || $errors->has('metadata_value_ids.*') || old('category_id') || old('metadata_value_ids')) ? 'true' : 'false' }} }" class="mb-6 border border-gray-800 rounded-xl bg-gray-950/20 overflow-hidden">
                                <!-- Accordion Header -->
                                <button type="button" @click="open = !open" class="w-full flex items-center justify-between p-4 bg-gray-900/40 hover:bg-gray-900/60 text-left transition select-none cursor-pointer">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <span class="font-bold text-gray-200 text-sm">Advanced Classification (Optional) / கூடுதல் விவரங்கள்</span>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- Accordion Body -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-2"
                                     class="p-4 border-t border-gray-800 bg-gray-950/40"
                                     x-cloak>
                                     
                                    <!-- Category -->
                                    <div class="mb-4">
                                        <x-input-label for="category_id" :value="__('Category / பகுதி (Optional)')" />
                                        <div class="flex flex-col md:flex-row gap-3 mt-1">
                                            <select id="category_id" name="category_id" x-model="selectedCategory"
                                                class="block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm">
                                                <option value="">✨ Auto-Categorize based on content (தானியங்கி வகைப்பாடு)</option>
                                                <template x-for="category in categories" :key="category.id">
                                                    <option :value="category.id" x-text="category.name" :selected="category.id == selectedCategory"></option>
                                                </template>
                                            </select>
                                            <button type="button" @click="runAiClassification" :disabled="aiLoading"
                                                class="bg-gradient-to-r from-burnt-orange to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center gap-2 transition duration-200 disabled:opacity-50 min-w-[200px]">
                                                <template x-if="!aiLoading">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                                        <span>AI பகுப்பாய்வு</span>
                                                    </div>
                                                </template>
                                                <template x-if="aiLoading">
                                                    <div class="flex items-center gap-2">
                                                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                        <span>பகுப்பாய்வு செய்கிறது...</span>
                                                    </div>
                                                </template>
                                            </button>
                                        </div>
                                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                    </div>

                                    <!-- Dynamic Category-Specific Metadata -->
                                    <div x-show="selectedCategory && getMetadataTypesForCategory().length > 0" class="p-4 bg-gray-950 border border-gray-800 rounded-xl mb-4 transition duration-300">
                                        <h3 class="text-sm font-semibold text-orange-400 mb-3 flex items-center gap-2">
                                            <svg class="w-4 h-4 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            கூடுதல் விவரங்கள் (Metadata)
                                        </h3>

                                        <!-- Hidden inputs for selected metadata values to ensure they are submitted even when filtered out of view -->
                                        <div class="hidden">
                                            <template x-for="valId in selectedMetadataValues.filter(id => getMetadataTypesForCategory().flatMap(t => (t.values || []).map(v => v.id)).includes(id))" :key="'hidden-meta-' + valId">
                                                <input type="hidden" name="metadata_value_ids[]" :value="valId">
                                            </template>
                                        </div>

                                        <template x-for="type in getMetadataTypesForCategory()" :key="type.id">
                                            <div class="mb-4" x-data="{ showAddForm: false, newValueName: '', searchQuery: '' }">
                                                <div class="flex items-center justify-between mb-2">
                                                    <label class="block text-xs font-medium text-gray-400" x-text="type.name"></label>
                                                    <button type="button" @click="showAddForm = !showAddForm" class="text-xs text-burnt-orange hover:text-orange-400 flex items-center gap-1 transition duration-200">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        மதிப்பு சேர்க்க (Add Value)
                                                    </button>
                                                </div>

                                                <!-- Inline form to add custom metadata value -->
                                                <div x-show="showAddForm" x-transition class="flex items-center gap-2 mb-3 bg-gray-900/50 p-2 border border-gray-800 rounded-lg">
                                                    <input type="text" x-model="newValueName" placeholder="மதிப்பு (e.g. ஏரி / Dream Destination)" class="text-xs px-2 py-1.5 w-full bg-gray-950 text-white border border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded transition" data-kathaingo-transliterate="true">
                                                    <button type="button" @click="if (newValueName.trim()) { addCustomValue(type, newValueName); newValueName = ''; showAddForm = false; }" class="px-3 py-1.5 bg-burnt-orange hover:bg-orange-600 text-white text-xs font-semibold rounded transition whitespace-nowrap">
                                                        சேமி (Save)
                                                    </button>
                                                    <button type="button" @click="showAddForm = false; newValueName = '';" class="px-2 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 text-xs rounded transition whitespace-nowrap">
                                                        ரத்து (Cancel)
                                                    </button>
                                                </div>

                                                <!-- Search Input (only shown when there are 5 or more values) -->
                                                <div class="mb-2 relative" x-show="type.values && type.values.length >= 5">
                                                    <span class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
                                                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                        </svg>
                                                    </span>
                                                    <input type="text" x-model="searchQuery" placeholder="தேடுக (Search values...)" class="text-xs pl-8 pr-2.5 py-1.5 w-full bg-gray-900 text-white border border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-lg transition" data-kathaingo-transliterate="true">
                                                </div>

                                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                    <template x-for="value in (type.values || []).filter(v => !searchQuery || v.name.toLowerCase().includes(searchQuery.toLowerCase()))" :key="value.id">
                                                        <label class="flex items-center gap-2 p-2 bg-gray-900 border border-gray-800 rounded-lg cursor-pointer hover:border-burnt-orange/50 transition">
                                                            <input type="checkbox" :value="value.id" :checked="selectedMetadataValues.includes(value.id)" @change="toggleMetadata(value.id)" class="text-burnt-orange focus:ring-burnt-orange border-gray-700 bg-gray-955 rounded">
                                                            <span class="text-sm text-gray-200" x-text="value.name"></span>
                                                        </label>
                                                    </template>
                                                </div>

                                                <!-- No matching values fallback -->
                                                <div x-show="searchQuery && (type.values || []).filter(v => v.name.toLowerCase().includes(searchQuery.toLowerCase())).length === 0" class="text-xs text-gray-500 py-1.5 pl-1">
                                                    பொருந்தும் மதிப்புகள் இல்லை (No matching values)
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                              <!-- Series Selection -->
                             <div class="p-4 bg-gray-950 border border-gray-800 rounded-xl mb-4" x-data="{
                                  inSeries: {{ old('series_id') ? 'true' : 'false' }},
                                  showNewSeriesForm: false,
                                  newSeriesTitle: '',
                                  newSeriesDesc: '',
                                  isSubmittingSeries: false,
                                  seriesCropper: null,
                                  init() {
                                      const fileInput = document.getElementById('new_series_image_input');
                                      if (fileInput) {
                                          fileInput.addEventListener('change', (e) => {
                                              const files = e.target.files;
                                              if (files && files.length > 0) {
                                                  const file = files[0];
                                                  const reader = new FileReader();
                                                  reader.onload = (event) => {
                                                      const img = document.getElementById('series_image_to_crop');
                                                      img.src = event.target.result;
                                                      document.getElementById('series_cropper_container').classList.remove('hidden');
                                                      
                                                      if (this.seriesCropper) {
                                                          this.seriesCropper.destroy();
                                                      }
                                                      
                                                      this.seriesCropper = new Cropper(img, {
                                                          aspectRatio: 16 / 10,
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

                                          document.getElementById('btn_series_zoom_in').addEventListener('click', () => this.seriesCropper && this.seriesCropper.zoom(0.1));
                                          document.getElementById('btn_series_zoom_out').addEventListener('click', () => this.seriesCropper && this.seriesCropper.zoom(-0.1));
                                          document.getElementById('btn_series_reset').addEventListener('click', () => this.seriesCropper && this.seriesCropper.reset());
                                      }
                                  },
                                  async createNewSeries() {
                                      if (!this.newSeriesTitle.trim()) {
                                          alert('தயவுசெய்து தொடரின் பெயரை உள்ளிடவும் (Please enter a series title)');
                                          return;
                                      }
                                      this.isSubmittingSeries = true;
                                      try {
                                          const formData = new FormData();
                                          formData.append('title', this.newSeriesTitle);
                                          formData.append('description', this.newSeriesDesc);

                                          if (this.seriesCropper) {
                                              const canvas = this.seriesCropper.getCroppedCanvas({
                                                  width: 800,
                                                  height: 500,
                                                  imageSmoothingEnabled: true,
                                                  imageSmoothingQuality: 'high'
                                              });
                                              const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.9));
                                              if (blob) {
                                                  formData.append('image', blob, 'series-cover.jpg');
                                              }
                                          }

                                          const response = await fetch('{{ route('api.series.store') }}', {
                                              method: 'POST',
                                              headers: {
                                                  'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
                                              },
                                              body: formData
                                          });
                                          
                                          if (!response.ok) {
                                              const data = await response.json();
                                              throw new Error(data.message || 'Error creating series');
                                          }
                                          const series = await response.json();
                                          
                                          // Add new option to dropdown and select it
                                          const selectEl = document.getElementById('series_id');
                                          const newOption = new Option(series.title, series.id, true, true);
                                          selectEl.add(newOption);
                                          
                                          // Reset form
                                          this.newSeriesTitle = '';
                                          this.newSeriesDesc = '';
                                          if (this.seriesCropper) {
                                              this.seriesCropper.destroy();
                                              this.seriesCropper = null;
                                          }
                                          document.getElementById('series_cropper_container').classList.add('hidden');
                                          document.getElementById('new_series_image_input').value = '';
                                          this.showNewSeriesForm = false;
                                      } catch (error) {
                                          alert('தொடரை உருவாக்குவதில் தோல்வி: ' + error.message);
                                      } finally {
                                          this.isSubmittingSeries = false;
                                      }
                                  }
                              }">
                                 <div class="flex items-center gap-2 mb-3">
                                     <input type="checkbox" id="is_series_post" x-model="inSeries" class="text-burnt-orange focus:ring-burnt-orange border-gray-700 bg-gray-950 rounded">
                                     <label for="is_series_post" class="text-sm text-gray-200 cursor-pointer">இது ஒரு தொடர் கட்டுரை (Is this part of a Series?)</label>
                                 </div>
                                 <div x-show="inSeries" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                     <div>
                                         <x-input-label for="series_id" :value="__('தொடர் (Select Series)')" />
                                         <div class="flex gap-2 mt-1">
                                             <select id="series_id" name="series_id" class="block w-full bg-gray-900 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm">
                                                 <option value="">Select a Series</option>
                                                 @foreach($series as $s)
                                                     <option value="{{ $s->id }}" {{ old('series_id') == $s->id ? 'selected' : '' }}>
                                                         {{ $s->title }}
                                                     </option>
                                                 @endforeach
                                             </select>
                                             <button type="button" @click="showNewSeriesForm = !showNewSeriesForm" class="px-3 py-2 bg-burnt-orange hover:bg-orange-600 text-white font-medium text-sm rounded-md transition duration-150 flex-shrink-0">
                                                 + புதியது (New)
                                             </button>
                                         </div>
                                     </div>
                                     <div>
                                         <x-input-label for="volume" :value="__('பாகம் / Volume (Optional)')" />
                                         <x-text-input id="volume" name="volume" class="block mt-1 w-full" type="text" :value="old('volume')" placeholder="e.g. Volume 1, Part 2" />
                                     </div>
                                     <div>
                                         <x-input-label for="chapter_number" :value="__('அத்தியாய எண் / Chapter Number (Optional)')" />
                                         <x-text-input id="chapter_number" name="chapter_number" class="block mt-1 w-full" type="number" :value="old('chapter_number')" placeholder="e.g. 1, 2" />
                                     </div>

                                     <!-- Inline New Series Form -->
                                     <div x-show="showNewSeriesForm" x-transition class="col-span-1 md:col-span-3 mt-2 p-4 bg-gray-900 border border-gray-850 rounded-xl">
                                         <h4 class="text-sm font-bold text-burnt-orange mb-3">புதிய தொடரை உருவாக்கு (Create New Series)</h4>
                                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                             <div>
                                                 <label class="block text-xs font-semibold text-gray-400 mb-1">தொடர் பெயர் / Title (Required)</label>
                                                 <input type="text" x-model="newSeriesTitle" class="block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm text-sm" placeholder="e.g. பொன்னியின் செல்வன், அறிவியல் தேடல்" data-kathaingo-transliterate="true">
                                             </div>
                                             <div>
                                                 <label class="block text-xs font-semibold text-gray-400 mb-1">விளக்கம் / Description (Optional)</label>
                                                 <input type="text" x-model="newSeriesDesc" class="block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm text-sm" placeholder="e.g. ஒரு சுருக்கமான விளக்கம்" data-kathaingo-transliterate="true">
                                             </div>
                                         </div>

                                         <!-- Cover Image Cropper -->
                                         <div class="mb-4">
                                             <label class="block text-xs font-semibold text-gray-400 mb-1">தொடர் முகப்புப் படம் / Series Cover Image (Optional)</label>
                                             <input type="file" id="new_series_image_input" accept="image/*" class="block w-full text-xs text-gray-400 bg-gray-950 border border-gray-800 rounded-md py-1.5 px-2 focus:outline-none">
                                             
                                             <div id="series_cropper_container" class="hidden mt-3 p-3 bg-gray-950 border border-gray-850 rounded-lg">
                                                 <div class="max-h-[300px] overflow-hidden rounded-lg bg-gray-900 border border-gray-800">
                                                     <img id="series_image_to_crop" src="" alt="Series Cover Source" class="max-w-full block">
                                                 </div>
                                                 <div class="flex flex-wrap gap-2 mt-3 justify-center">
                                                     <button type="button" id="btn_series_zoom_in" class="px-3 py-1 bg-gray-800 hover:bg-gray-700 text-white rounded text-xs font-bold transition">🔍+ Zoom In</button>
                                                     <button type="button" id="btn_series_zoom_out" class="px-3 py-1 bg-gray-800 hover:bg-gray-700 text-white rounded text-xs font-bold transition">🔍- Zoom Out</button>
                                                     <button type="button" id="btn_series_reset" class="px-3 py-1 bg-gray-800 hover:bg-gray-700 text-white rounded text-xs font-bold transition">🔄 Reset</button>
                                                 </div>
                                             </div>
                                         </div>

                                         <div class="flex justify-end gap-2 mt-4">
                                             <button type="button" @click="showNewSeriesForm = false" class="px-3 py-1.5 bg-gray-850 hover:bg-gray-800 text-gray-300 font-medium text-xs rounded transition duration-150">
                                                 இரத்து (Cancel)
                                             </button>
                                             <button type="button" @click="createNewSeries()" :disabled="isSubmittingSeries" class="px-3 py-1.5 bg-burnt-orange hover:bg-orange-600 disabled:opacity-50 text-white font-medium text-xs rounded transition duration-150 flex items-center gap-1.5">
                                                 <span x-show="isSubmittingSeries" class="inline-block w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                                 உருவாக்கு (Create)
                                             </button>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                            <!-- Tags Input -->
                            <div class="mb-4">
                                <x-input-label for="tags" :value="__('குறிச்சொற்கள் / Tags (Comma separated)')" />
                                <x-text-input id="tags" name="tags" class="block mt-1 w-full" type="text" x-model="tags" placeholder="e.g. சினிமா, விமர்சனம், புதுமை" data-kathaingo-transliterate="true" />
                                <p class="mt-1 text-sm text-gray-500">சுமார் 3-5 குறிச்சொற்களை கமாவால் பிரித்து எழுதவும் (comma separated)</p>
                                <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                            </div>


                        </div>

                        <!-- Country Selection -->
                        <div class="mb-4">
                            <x-input-label for="country_code" :value="__('Country / நாடு (Optional)')" />
                            <select id="country_code" name="country_code"
                                class="block mt-1 w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm">
                                <option value="">Select a Country (நாடு தேர்வு செய்ய)</option>
                                @foreach(\App\Helpers\CountryHelper::getCountries() as $code => $country)
                                    <option value="{{ $code }}" {{ old('country_code') == $code ? 'selected' : '' }}>
                                        {{ $country['name_ta'] }} ({{ $country['name_en'] }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-sm text-gray-500">இது ஒரு குறிப்பிட்ட நாட்டுடன் தொடர்புடைய பதிவு எனில் தேர்வு செய்யவும்</p>
                            <x-input-error :messages="$errors->get('country_code')" class="mt-2" />
                        </div>

                        <script>
                            function postTaxonomy() {
                                return {
                                    categories: @json($categories),
                                    metadataTypes: @json($metadataTypes),
                                    selectedCategory: '{{ old('category_id') }}',
                                    selectedMetadataValues: @json(array_map('intval', old('metadata_value_ids', []))),
                                    tags: '{{ old('tags') }}',
                                    selectedStatus: '{{ old('status', 'draft') }}',
                                    aiLoading: false,

                                    // Pre-submission Review & Preview System Properties
                                    isReviewed: false,
                                    hasMajorIssues: false,
                                    reviewStatusText: 'Not Reviewed',
                                    reviewStatusClass: 'text-gray-500',
                                    reviewDotClass: 'bg-gray-500',
                                    showPreviewModal: false,
                                    showReviewModal: false,
                                    showWarningModal: false,
                                    warningModalType: '', // 'unreviewed' | 'major_issues'
                                    reviewReport: null,
                                    previewData: {
                                        title: '',
                                        excerpt: '',
                                        content: '',
                                        category: '',
                                        tags: '',
                                        author: '',
                                        readTime: '',
                                        imageSrc: ''
                                    },
                                    isLoadingReview: false,

                                    // Autosave Properties
                                    hasAutosave: false,
                                    autosaveTime: '',
                                    autosaveData: null,
                                    saveTimeout: null,

                                    getAutoSaveKey() {
                                        return 'kathaingo_draft_autosave_create';
                                    },

                                    init() {
                                        const saved = localStorage.getItem(this.getAutoSaveKey());
                                        if (saved) {
                                            try {
                                                const data = JSON.parse(saved);
                                                if ((data.title || data.content || data.excerpt || data.category_id) && (Date.now() - data.timestamp < 24 * 60 * 60 * 1000)) {
                                                    this.hasAutosave = true;
                                                    this.autosaveTime = new Date(data.timestamp).toLocaleTimeString();
                                                    this.autosaveData = data;
                                                }
                                            } catch(e) {}
                                        }

                                        // Save draft on state changes (step-by-step saves)
                                        this.$watch('selectedCategory', () => this.triggerSave());
                                        this.$watch('selectedMetadataValues', () => this.triggerSave());
                                        this.$watch('tags', () => this.triggerSave());
                                        this.$watch('selectedStatus', () => this.triggerSave());

                                        // Set up change listeners for form inputs
                                        const inputs = ['title', 'excerpt', 'image', 'video_url', 'country_code', 'volume', 'chapter_number', 'published_at'];
                                        inputs.forEach(id => {
                                            const el = document.getElementById(id);
                                            if (el) {
                                                el.addEventListener('input', () => this.triggerSave());
                                                el.addEventListener('change', () => this.triggerSave());
                                                el.addEventListener('blur', () => this.triggerSave());
                                            }
                                        });

                                        // TinyMCE keyup/change listeners
                                        setTimeout(() => {
                                            if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                                tinymce.get('content').on('keyup change', () => this.triggerSave());
                                            }
                                        }, 2000);

                                        // Save draft at regular intervals (every 15 seconds)
                                        setInterval(() => {
                                            this.saveDraft();
                                        }, 15000);
                                    },

                                    saveDraft() {
                                        const draftData = {
                                            title: document.getElementById('title') ? document.getElementById('title').value : '',
                                            excerpt: document.getElementById('excerpt') ? document.getElementById('excerpt').value : '',
                                            content: typeof tinymce !== 'undefined' && tinymce.get('content') ? tinymce.get('content').getContent() : '',
                                            image: document.getElementById('image') ? document.getElementById('image').value : '',
                                            video_url: document.getElementById('video_url') ? document.getElementById('video_url').value : '',
                                            category_id: this.selectedCategory,
                                            metadata_value_ids: this.selectedMetadataValues,
                                            tags: this.tags,
                                            selectedStatus: this.selectedStatus,
                                            country_code: document.getElementById('country_code') ? document.getElementById('country_code').value : '',
                                            volume: document.getElementById('volume') ? document.getElementById('volume').value : '',
                                            chapter_number: document.getElementById('chapter_number') ? document.getElementById('chapter_number').value : '',
                                            published_at: document.getElementById('published_at') ? document.getElementById('published_at').value : '',
                                            timestamp: Date.now()
                                        };
                                        localStorage.setItem(this.getAutoSaveKey(), JSON.stringify(draftData));
                                    },

                                    triggerSave() {
                                        clearTimeout(this.saveTimeout);
                                        this.saveTimeout = setTimeout(() => this.saveDraft(), 1000);
                                    },

                                    loadAutosave() {
                                        if (!this.autosaveData) return;
                                        const data = this.autosaveData;

                                        // Restore Alpine reactive properties
                                        this.selectedCategory = data.category_id || '';
                                        this.selectedMetadataValues = data.metadata_value_ids || [];
                                        this.tags = data.tags || '';
                                        this.selectedStatus = data.selectedStatus || 'draft';

                                        // Restore standard inputs
                                        const fields = ['title', 'excerpt', 'image', 'video_url', 'country_code', 'volume', 'chapter_number', 'published_at'];
                                        fields.forEach(id => {
                                            const el = document.getElementById(id);
                                            if (el && data[id] !== undefined) {
                                                el.value = data[id];
                                            }
                                        });

                                        // Restore TinyMCE editor
                                        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                            tinymce.get('content').setContent(data.content || '');
                                        }

                                        this.hasAutosave = false;
                                    },

                                    discardAutosave() {
                                        localStorage.removeItem(this.getAutoSaveKey());
                                        this.hasAutosave = false;
                                        this.autosaveData = null;
                                    },

                                    getMetadataTypesForCategory() {
                                        if (!this.selectedCategory) return [];
                                        return this.metadataTypes.filter(t => t.category_id == this.selectedCategory);
                                    },

                                    toggleMetadata(valId) {
                                        const index = this.selectedMetadataValues.indexOf(valId);
                                        if (index > -1) {
                                            this.selectedMetadataValues.splice(index, 1);
                                        } else {
                                            this.selectedMetadataValues.push(valId);
                                        }
                                    },

                                    addCustomValue(type, name) {
                                        if (!name.trim()) return;

                                        fetch(`/api/metadata-types/${type.id}/values`, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({ name: name })
                                        })
                                        .then(res => {
                                            if (!res.ok) {
                                                return res.json().then(err => { throw err; });
                                            }
                                            return res.json();
                                        })
                                        .then(data => {
                                            if (!type.values) {
                                                type.values = [];
                                            }
                                            type.values.push(data);

                                            if (!this.selectedMetadataValues.includes(data.id)) {
                                                this.selectedMetadataValues.push(data.id);
                                            }
                                        })
                                        .catch(err => {
                                            alert(err.message || 'விவரத்தை சேமிப்பதில் தவறு நிகழ்ந்தது.');
                                        });
                                    },

                                    runAiClassification() {
                                        let titleVal = document.getElementById('title').value;
                                        let contentVal = '';
                                        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                            contentVal = tinymce.get('content').getContent();
                                        } else {
                                            contentVal = document.getElementById('content').value;
                                        }

                                        if (!titleVal || !contentVal) {
                                            alert('AI பகுப்பாய்வு செய்ய தலைப்பு மற்றும் உள்ளடக்கத்தை முதலில் எழுதவும்.');
                                            return;
                                        }

                                        this.aiLoading = true;
                                        fetch('{{ route("api.posts.classify") }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ title: titleVal, content: contentVal })
                                        })
                                        .then(res => res.json())
                                        .then(data => {
                                            this.aiLoading = false;
                                            if (data.category_id) {
                                                this.selectedCategory = data.category_id;
                                                this.selectedMetadataValues = data.metadata_value_ids;
                                                
                                                if (data.tags && data.tags.length > 0) {
                                                    this.tags = data.tags.join(', ');
                                                }
                                                alert('AI பகுப்பாய்வு நிறைவுற்றது! பகுதி மற்றும் கூடுதல் விவரங்கள் தேர்ந்தெடுக்கப்பட்டுள்ளன.');
                                            }
                                        })
                                        .catch(err => {
                                            this.aiLoading = false;
                                            alert('AI பகுப்பாய்வில் ஏதோ தவறு நிகழ்ந்துள்ளது.');
                                        });
                                    },

                                    runPreview() {
                                        const titleEl = document.getElementById('title');
                                        this.previewData.title = titleEl ? titleEl.value : '';

                                        const excerptEl = document.getElementById('excerpt');
                                        this.previewData.excerpt = excerptEl ? excerptEl.value : '';

                                        let editorContent = '';
                                        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                            editorContent = tinymce.get('content').getContent();
                                        }
                                        this.previewData.content = editorContent;

                                        const categoryEl = document.getElementById('category_id');
                                        let catName = '';
                                        if (categoryEl && categoryEl.selectedIndex >= 0) {
                                            catName = categoryEl.options[categoryEl.selectedIndex].text;
                                        }
                                        this.previewData.category = catName;

                                        this.previewData.tags = this.tags ? this.tags.split(',').map(t => t.trim()).filter(t => t.length > 0) : [];

                                        const authorEl = document.getElementById('author_subcategory_id');
                                        let authorName = '';
                                        if (authorEl && authorEl.selectedIndex >= 0 && authorEl.options[authorEl.selectedIndex].value !== '') {
                                            authorName = authorEl.options[authorEl.selectedIndex].text;
                                        } else {
                                            authorName = '{{ auth()->user()->name }}';
                                        }
                                        this.previewData.author = authorName;

                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = editorContent;
                                        const text = tempDiv.textContent || tempDiv.innerText || '';
                                        const words = text.trim().split(/\s+/).filter(w => w.length > 0).length;
                                        const minutes = Math.max(1, Math.ceil(words / 150));
                                        this.previewData.readTime = minutes;

                                        const fileEl = document.getElementById('featured_image');
                                        const urlEl = document.getElementById('image');
                                        if (fileEl && fileEl.files && fileEl.files[0]) {
                                            this.previewData.imageSrc = URL.createObjectURL(fileEl.files[0]);
                                        } else if (urlEl && urlEl.value) {
                                            this.previewData.imageSrc = urlEl.value;
                                        } else {
                                            this.previewData.imageSrc = '';
                                        }

                                        this.showPreviewModal = true;
                                    },

                                    runReviewReport() {
                                        let editorContent = '';
                                        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                                            editorContent = tinymce.get('content').getContent();
                                        }
                                        const tempDiv = document.createElement('div');
                                        tempDiv.innerHTML = editorContent;
                                        const text = tempDiv.textContent || tempDiv.innerText || '';

                                        const titleVal = document.getElementById('title') ? document.getElementById('title').value.trim() : '';
                                        const excerptVal = document.getElementById('excerpt') ? document.getElementById('excerpt').value.trim() : '';
                                        const fileEl = document.getElementById('featured_image');
                                        const urlVal = document.getElementById('image') ? document.getElementById('image').value.trim() : '';
                                        const hasImage = (fileEl && fileEl.files && fileEl.files.length > 0) || urlVal !== '';

                                        this.isLoadingReview = true;
                                        const reviewUrl = '{{ route("api.writing-assistant.review-article") }}';

                                        fetch(reviewUrl, {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                            },
                                            body: JSON.stringify({ text: text })
                                        })
                                        .then(res => {
                                            if (!res.ok) throw new Error('API error');
                                            return res.json();
                                        })
                                        .then(data => {
                                            const summary = data.summary || {
                                                spelling: 0,
                                                grammar: 0,
                                                style: 0,
                                                consistency: 0,
                                                unknown: 0,
                                                readability: 'Easy (எளிமை)',
                                                punctuation: 0,
                                                repeated_words: 0,
                                                long_sentences: 0,
                                                safety: 0,
                                                plagiarism: 0
                                            };

                                            const checklist = [
                                                { id: 1, label: 'Spelling issues (எழுத்துப்பிழைகள்)', count: summary.spelling, status: summary.spelling > 5 ? 'red' : (summary.spelling > 0 ? 'yellow' : 'green'), message: summary.spelling > 0 ? `${summary.spelling} எழுத்துப்பிழைகள் கண்டறியப்பட்டன.` : 'எழுத்துப்பிழைகள் இல்லை.' },
                                                { id: 2, label: 'Grammar issues (இலக்கணம்)', count: summary.grammar, status: summary.grammar > 3 ? 'red' : (summary.grammar > 0 ? 'yellow' : 'green'), message: summary.grammar > 0 ? `${summary.grammar} இலக்கணப் பிழைகள் கண்டறியப்பட்டன.` : 'இலக்கணப் பிழைகள் இல்லை.' },
                                                { id: 3, label: 'Punctuation issues (நிறுத்தற்குறிகள்)', count: summary.punctuation, status: summary.punctuation > 5 ? 'red' : (summary.punctuation > 0 ? 'yellow' : 'green'), message: summary.punctuation > 0 ? `${summary.punctuation} நிறுத்தற்குறி பிழைகள் கண்டறியப்பட்டன.` : 'நிறுத்தற்குறி பயன்பாடு சரியாக உள்ளது.' },
                                                { id: 4, label: 'Repeated words (அடுத்தடுத்து வரும் ஒரே சொற்கள்)', count: summary.repeated_words, status: summary.repeated_words > 3 ? 'red' : (summary.repeated_words > 0 ? 'yellow' : 'green'), message: summary.repeated_words > 0 ? `${summary.repeated_words} அடுத்தடுத்து வரும் ஒரே சொற்கள் கண்டறியப்பட்டன.` : 'சொல் அடுக்குகள் இல்லை.' },
                                                { id: 5, label: 'Very long sentences (நீளமான வாக்கியங்கள்)', count: summary.long_sentences, status: summary.long_sentences > 3 ? 'red' : (summary.long_sentences > 0 ? 'yellow' : 'green'), message: summary.long_sentences > 0 ? `${summary.long_sentences} மிக நீளமான வாக்கியங்கள் உள்ளன (பிரித்து எழுதலாம்).` : 'வாக்கிய நீளம் சரியாக உள்ளது.' },
                                                { id: 6, label: 'Missing title (தலைப்பு)', count: titleVal ? 0 : 1, status: titleVal ? 'green' : 'red', message: titleVal ? 'தலைப்பு உள்ளிடப்பட்டுள்ளது.' : 'தலைப்பு உள்ளிடப்படவில்லை (கட்டாயம்).' },
                                                { id: 7, label: 'Missing featured image (முகப்புப் படம்)', count: hasImage ? 0 : 1, status: hasImage ? 'green' : 'yellow', message: hasImage ? 'முகப்புப் படம் சேர்க்கப்பட்டுள்ளது.' : 'முகப்புப் படம் இல்லை (பரிந்துரைக்கப்படுகிறது).' },
                                                { id: 8, label: 'Missing category (பகுதி)', count: this.selectedCategory ? 0 : 1, status: this.selectedCategory ? 'green' : 'red', message: this.selectedCategory ? 'பகுதி தேர்ந்தெடுக்கப்பட்டுள்ளது.' : 'பகுதி தேர்ந்தெடுக்கப்படவில்லை (கட்டாயம்).' },
                                                { id: 9, label: 'Missing tags (குறிச்சொற்கள்)', count: this.tags ? 0 : 1, status: this.tags.trim() ? 'green' : 'yellow', message: this.tags.trim() ? 'குறிச்சொற்கள் சேர்க்கப்பட்டுள்ளன.' : 'குறிச்சொற்கள் இல்லை (பரிந்துரைக்கப்படுகிறது).' },
                                                { id: 10, label: 'Missing excerpt / short description (சுருக்கம்)', count: excerptVal ? 0 : 1, status: excerptVal ? 'green' : 'yellow', message: excerptVal ? 'பதிவின் சுருக்கம் உள்ளிடப்பட்டுள்ளது.' : 'பதிவின் சுருக்கம் இல்லை (பரிந்துரைக்கப்படுகிறது).' },
                                                { id: 11, label: 'Community safety concerns (சமூக பாதுகாப்பு)', count: summary.safety, status: summary.safety > 0 ? 'red' : 'green', message: summary.safety > 0 ? `${summary.safety} பொருத்தமற்ற சொற்கள் கண்டறியப்பட்டன.` : 'பாதுகாப்பற்ற சொற்கள் எதுவும் இல்லை.' },
                                                { id: 12, label: 'Possible plagiarism (நகல் சரிபார்ப்பு)', count: 0, status: 'green', message: 'நகல் பிழைகள் எதுவும் கண்டறியப்படவில்லை (Placeholder).' }
                                            ];

                                            let redCount = checklist.filter(item => item.status === 'red').length;
                                            let yellowCount = checklist.filter(item => item.status === 'yellow').length;

                                            let overallStatus = 'Reviewed';
                                            if (redCount > 0) {
                                                overallStatus = 'Major Issues';
                                                this.hasMajorIssues = true;
                                                this.reviewStatusText = 'Major Issues';
                                                this.reviewStatusClass = 'text-rose-400';
                                                this.reviewDotClass = 'bg-rose-500 animate-pulse';
                                            } else if (yellowCount > 2) {
                                                overallStatus = 'Needs Attention';
                                                this.hasMajorIssues = false;
                                                this.reviewStatusText = 'Needs Attention';
                                                this.reviewStatusClass = 'text-amber-400';
                                                this.reviewDotClass = 'bg-amber-500';
                                            } else {
                                                overallStatus = 'Reviewed';
                                                this.hasMajorIssues = false;
                                                this.reviewStatusText = 'Reviewed';
                                                this.reviewStatusClass = 'text-emerald-400';
                                                this.reviewDotClass = 'bg-emerald-500';
                                            }

                                            this.reviewReport = {
                                                readiness: overallStatus,
                                                checklist: checklist,
                                                redCount: redCount,
                                                yellowCount: yellowCount,
                                                readability: summary.readability
                                            };

                                            this.isReviewed = true;
                                            this.isLoadingReview = false;
                                            this.showReviewModal = true;
                                        })
                                        .catch(err => {
                                            this.isLoadingReview = false;
                                            alert('மதிப்பாய்வு செய்வதில் ஏதோ தவறு நிகழ்ந்துள்ளது (Error running review).');
                                        });
                                    },

                                    handleFormSubmit(event) {
                                        if (!this.isReviewed) {
                                             this.warningModalType = 'unreviewed';
                                             this.showWarningModal = true;
                                             return false;
                                        }
                                        if (this.hasMajorIssues) {
                                             this.warningModalType = 'major_issues';
                                             this.showWarningModal = true;
                                             return false;
                                        }
                                        event.target.submit();
                                    },

                                    submitAnyway() {
                                        this.showWarningModal = false;
                                        this.isReviewed = true;
                                        this.hasMajorIssues = false;
                                        document.getElementById('post-form').submit();
                                    },

                                    reviewNow() {
                                        this.showWarningModal = false;
                                        this.runReviewReport();
                                    }
                                }
                            }
                        </script>

                        <!-- Publication Date -->
                        <div class="mb-4">
                            <x-input-label for="published_at" :value="__('Publication Date (Optional / Default to Now)')" />
                            <x-text-input id="published_at" class="block mt-1 w-full" type="datetime-local" name="published_at"
                                :value="old('published_at')" />
                            <p class="mt-1 text-sm text-gray-400">Specify the publication date. Useful for backdating old Facebook posts or scheduling.</p>
                            <x-input-error :messages="$errors->get('published_at')" class="mt-2" />
                        </div>

                        <!-- Status Dropdown & Action Buttons Footer Row -->
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mt-8 border-t border-gray-800 pt-6">
                            <!-- Status Dropdown -->
                            <div class="w-full md:w-64">
                                <x-input-label for="status" :value="__('Status (பதிவின் நிலை)')" class="text-xs font-semibold text-gray-400 mb-1" />
                                <select id="status" name="status" x-model="selectedStatus"
                                    class="block w-full bg-gray-950 text-white border-gray-800 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm text-sm">
                                    @php
                                        $user = auth()->user();
                                        $trustLevel = $user->authorProfile->trust_level ?? 1;
                                        $isAdminOrEditor = $user->isAdmin() || $user->isEditor();
                                    @endphp
                                    @if($isAdminOrEditor)
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (வரைவு)</option>
                                        <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submit for Review (மதிப்பாய்வுக்கு அனுப்பு)</option>
                                        <option value="under_review" {{ old('status') == 'under_review' ? 'selected' : '' }}>Under Review (மதிப்பாய்வில் உள்ளது)</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved (அங்கீகரிக்கப்பட்டது)</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published (வெளியிடப்பட்டது)</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected (நிராகரிக்கப்பட்டது)</option>
                                    @elseif($trustLevel >= 3)
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (வரைவு)</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publish (வெளியிடு)</option>
                                    @elseif($trustLevel == 2)
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (வரைவு)</option>
                                        <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submit for Review (மதிப்பாய்வுக்கு அனுப்பு)</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publish (வெளியிடு)</option>
                                    @else
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft (வரைவு)</option>
                                        <option value="submitted" {{ old('status') == 'submitted' ? 'selected' : '' }}>Submit for Review (மதிப்பாய்வுக்கு அனுப்பு)</option>
                                    @endif
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <!-- Buttons & Status Indicators -->
                            <div class="flex flex-col items-stretch sm:items-end gap-2 w-full md:w-auto">
                                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                    <!-- Preview Article -->
                                    <button type="button" @click="runPreview()"
                                        class="px-4 py-2 border border-gray-800 hover:border-gray-700 bg-gray-950 text-gray-300 hover:text-white hover:bg-gray-900 font-semibold rounded-lg transition-all duration-150 cursor-pointer text-center text-sm flex items-center justify-center gap-2 select-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Preview Article
                                    </button>

                                    <!-- Review Article -->
                                    <button type="button" @click="runReviewReport()" :disabled="isLoadingReview"
                                        class="px-4 py-2 border border-burnt-orange/40 hover:border-burnt-orange bg-gray-950 text-burnt-orange hover:text-white hover:bg-burnt-orange/10 font-semibold rounded-lg transition-all duration-150 cursor-pointer text-center text-sm flex items-center justify-center gap-2 select-none disabled:opacity-55 disabled:cursor-not-allowed">
                                        <template x-if="!isLoadingReview">
                                            <span class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                                Review Article
                                            </span>
                                        </template>
                                        <template x-if="isLoadingReview">
                                            <span class="flex items-center gap-2">
                                                <svg class="animate-spin h-4 w-4 text-burnt-orange" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                Reviewing...
                                            </span>
                                        </template>
                                    </button>

                                    <!-- Submit / Publish Button -->
                                    <button type="submit"
                                        class="px-5 py-2 bg-burnt-orange hover:bg-orange-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-burnt-orange/20 transition-all duration-150 cursor-pointer text-center text-sm select-none"
                                        x-text="selectedStatus === 'published' ? '{{ __('Publish') }}' : (selectedStatus === 'submitted' ? '{{ __('Submit for Review') }}' : '{{ __('Save as Draft') }}')">
                                        {{ __('Save as Draft') }}
                                    </button>
                                </div>

                                <!-- Review Status Indicator Row -->
                                <div class="flex items-center justify-center sm:justify-end gap-1.5 px-1 mt-1 text-xs text-gray-400 select-none">
                                    <span>Review Status:</span>
                                    <span class="inline-flex items-center gap-1 font-semibold transition-colors duration-150" :class="reviewStatusClass">
                                        <span class="h-2 w-2 rounded-full" :class="reviewDotClass"></span>
                                        <span x-text="reviewStatusText">Not Reviewed</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper.js CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" referrerpolicy="origin">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js" referrerpolicy="origin"></script>

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

        // Delegated click event listeners for parent-writing-assistant-btn and parent-review-article-btn
        document.addEventListener('click', function(e) {
            const assistantBtn = e.target.closest('#parent-writing-assistant-btn');
            if (assistantBtn) {
                e.preventDefault();
                const editor = typeof tinymce !== 'undefined' ? tinymce.get('content') : null;
                if (editor) {
                    editor.execCommand('mceWritingAssistantToggle');
                }
            }

            const reviewBtn = e.target.closest('#parent-review-article-btn');
            if (reviewBtn) {
                e.preventDefault();
                const editor = typeof tinymce !== 'undefined' ? tinymce.get('content') : null;
                if (editor) {
                    editor.execCommand('mceReviewArticle');
                }
            }
        });
    </script>

    <!-- TinyMCE Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        window.Kathaingo = {
            routes: {
                checkBlock: "{{ route('api.writing-assistant.check-block') }}",
                addDictionary: "{{ route('api.writing-assistant.dictionary.add') }}",
                suggestWord: "{{ route('api.writing-assistant.suggest-word') }}"
            }
        };

        tinymce.addI18n('en', {
            'Line height': 'வரி இடைவெளி',
            'Line Height': 'வரி இடைவெளி',
            'Insert/edit media': 'ஓலி / ஒளி செருக',
            'Insert/edit link': 'சுட்டிகளை இணைக்க',
            'Reveal additional toolbar items': 'மேலும்',
            'Reveal or hide additional toolbar': 'மேலும்',
            'Reveal or hide additional tool bar': 'மேலும்',
            'Reveal or hide additional toolbar items': 'மேலும்'
        });

        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            external_plugins: {
                'writingassistant': "{{ asset('js/writing-assistant.js') }}?v={{ file_exists(public_path('js/writing-assistant.js')) ? filemtime(public_path('js/writing-assistant.js')) : time() }}"
            },
            toolbar: 'undo redo | blocks | link cropimage media insertemoji | bold italic underline strikethrough lineheight | ' +
                'forecolor backcolor | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | removeformat code | help',
            toolbar_mode: 'wrap',
            line_height_formats: '0 1 1.5 2 3 4',
            contextmenu: 'link image table | writingassistant',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px; background-color: #030712; color: #ffffff; } ' +
                'img.kathaingo-img-left { float: left; margin: 0 16px 12px 0; max-width: 45%; height: auto; border-radius: 12px; } ' +
                'img.kathaingo-img-right { float: right; margin: 0 0 12px 16px; max-width: 45%; height: auto; border-radius: 12px; } ' +
                'img.kathaingo-img-center { display: block; margin: 24px auto; max-width: 80%; height: auto; border-radius: 12px; } ' +
                'img.kathaingo-img-full { display: block; margin: 24px auto; max-width: 100%; height: auto; border-radius: 12px; } ' +
                '.kathaingo-spell-error { background-color: rgba(239, 68, 68, 0.15) !important; text-decoration: underline wavy #ef4444 !important; text-underline-offset: 3px !important; text-decoration-skip-ink: none !important; cursor: pointer !important; } ' +
                '.kathaingo-punctuation-error { background-color: rgba(59, 130, 246, 0.15) !important; text-decoration: underline wavy #3b82f6 !important; text-underline-offset: 3px !important; text-decoration-skip-ink: none !important; cursor: pointer !important; } ' +
                '.kathaingo-style-warning { background-color: rgba(16, 185, 129, 0.15) !important; text-decoration: underline wavy #10b981 !important; text-underline-offset: 3px !important; text-decoration-skip-ink: none !important; cursor: pointer !important; }',
            extended_valid_elements: 'img[class|src|border|alt|title|width|height|style]',
            skin: 'oxide-dark',
            content_css: 'dark',

            setup: function (editor) {
                // Register custom 'cropimage' toolbar button to directly trigger our custom cropper
                editor.ui.registry.addButton('cropimage', {
                    icon: 'image',
                    tooltip: 'Crop, Zoom & Wrap Image (படத்தை செதுக்கி உரை வடிவமைப்புடன் செருகு)',
                    onAction: function () {
                        document.getElementById('post-image-cropper-input').click();
                    }
                });

                // Register custom 'insertemoji' button
                editor.ui.registry.addButton('insertemoji', {
                    text: '😊',
                    tooltip: 'இமோஜி சேர்க்க',
                    onAction: function () {
                        const btn = document.querySelector('button[aria-label="இமோஜி சேர்க்க"]');
                        if (window.KathaingoEmojiPicker) {
                            window.KathaingoEmojiPicker.toggle(btn, editor);
                        }
                    }
                });

                // Override default image command to open our custom cropper modal
                editor.addCommand('mceImage', function () {
                    document.getElementById('post-image-cropper-input').click();
                });

                // Bind transliteration helper if loaded
                if (window.KathaingoLanguageHelper) {
                    window.KathaingoLanguageHelper.bindTinyMCE(editor);
                }
            },

            // Image upload configuration
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append('image', blobInfo.blob(), blobInfo.filename());
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route("posts.uploadImage") }}', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.location) {
                                resolve(data.location);
                            } else {
                                reject('Upload failed: ' + (data.error || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            reject('Upload failed: ' + error);
                        });
                });
            },

            // Paste image handling
            paste_data_images: true,
            automatic_uploads: true,

            paste_preprocess: function(plugin, args) {
                try {
                    var doc = new DOMParser().parseFromString(args.content, 'text/html');
                    var allElems = doc.querySelectorAll('*');
                    allElems.forEach(function(el) {
                        var tagName = el.tagName.toLowerCase();
                        
                        // 1. Remove lang attribute
                        el.removeAttribute('lang');
                        
                        // 2. Remove class if it's Word junk or empty
                        var className = el.getAttribute('class') || '';
                        if (tagName !== 'img' && (/^Mso/i.test(className) || !className.trim())) {
                            el.removeAttribute('class');
                        }
                        
                        // 3. Remove inline styles, colors, font overrides
                        var style = el.getAttribute('style') || '';
                        if (tagName === 'img') {
                            // Keep only safe layout styles for images
                            var allowedStyles = [];
                            var styleParts = style.split(';');
                            styleParts.forEach(function(part) {
                                var propVal = part.split(':');
                                if (propVal.length === 2) {
                                    var prop = propVal[0].trim().toLowerCase();
                                    var val = propVal[1].trim();
                                    if (['float', 'margin', 'margin-left', 'margin-right', 'margin-top', 'margin-bottom', 'max-width', 'display', 'width', 'height'].indexOf(prop) > -1) {
                                        allowedStyles.push(prop + ': ' + val);
                                    }
                                }
                            });
                            if (allowedStyles.length > 0) {
                                el.setAttribute('style', allowedStyles.join('; ') + ';');
                            } else {
                                el.removeAttribute('style');
                            }
                        } else {
                            // For non-images, we strip fonts, colors, and justify alignment
                            var match = style.match(/text-align\s*:\s*([^;]+)/i);
                            if (match) {
                                var alignVal = match[1].trim().toLowerCase();
                                if (alignVal !== 'justify') {
                                    el.setAttribute('style', 'text-align: ' + alignVal + ';');
                                } else {
                                    el.removeAttribute('style');
                                }
                            } else {
                                el.removeAttribute('style');
                            }
                        }
                        
                        // 4. Remove align attribute if it is justify
                        var alignAttr = el.getAttribute('align') || '';
                        if (alignAttr.toLowerCase() === 'justify') {
                            el.removeAttribute('align');
                        }
                    });
                    
                    args.content = doc.body.innerHTML;
                } catch (e) {
                    // Fail-safe: keep original pasted content if parser fails
                }
            },

            // Font options
            font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
            font_family_formats: 'Arial=arial,helvetica,sans-serif; ' +
                'Courier New=courier new,courier,monospace; ' +
                'Georgia=georgia,palatino; ' +
                'Tahoma=tahoma,arial,helvetica,sans-serif; ' +
                'Times New Roman=times new roman,times; ' +
                'Verdana=verdana,geneva',

            // Enable resize
            resize: true,
        });
    </script>

    <!-- Draft Autosave is now managed inside the Alpine.js postTaxonomy component -->


    <!-- Hidden File Input for Cropper -->
    <input type="file" id="post-image-cropper-input" class="hidden" accept="image/*">

    <!-- Premium Image Cropper Modal -->
    <div id="cropper-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4 transition-all duration-300">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full flex flex-col max-h-[90vh] overflow-hidden transform scale-95 transition-all duration-300" id="cropper-modal-card">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800 bg-gray-900/50">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <svg class="w-4 h-4 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    படத்தை செதுக்க & வடிவமைக்க (Crop & Format Image)
                </h3>
                <button type="button" onclick="closeCropperModal()" class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex flex-col md:flex-row flex-1 overflow-hidden">
                <!-- Left: Cropping Area -->
                <div class="flex-1 bg-black/40 p-6 flex items-center justify-center min-h-[300px] max-h-[50vh] md:max-h-none overflow-hidden relative">
                    <img id="cropper-target-image" src="" alt="Source Image" class="max-w-full max-h-full block">
                </div>

                <!-- Right: Control Sidebar -->
                <div class="w-full md:w-80 border-t md:border-t-0 md:border-l border-gray-800 bg-gray-900/30 p-6 flex flex-col gap-6 overflow-y-auto">
                    <!-- Zoom & Rotate Controls -->
                    <div>
                        <span class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">வடிவமைப்பு கருவிகள் (Tools)</span>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="cropperZoom(0.1)" class="flex-1 min-w-[70px] px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1 transition">
                                Zoom +
                            </button>
                            <button type="button" onclick="cropperZoom(-0.1)" class="flex-1 min-w-[70px] px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1 transition">
                                Zoom -
                            </button>
                            <button type="button" onclick="cropperRotate(-45)" class="px-3 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-xs font-semibold flex items-center justify-center gap-1 transition" title="Rotate Left">
                                Rotate
                            </button>
                        </div>
                    </div>

                    <!-- Aspect Ratio -->
                    <div>
                        <span class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">விகிதம் (Aspect Ratio)</span>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" onclick="setCropperRatio(NaN)" class="px-2 py-1.5 bg-burnt-orange text-white rounded-lg text-xs font-semibold border border-burnt-orange/50 transition duration-150 ratio-btn" id="ratio-free">Free</button>
                            <button type="button" onclick="setCropperRatio(1)" class="px-2 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-xs font-semibold transition duration-150 ratio-btn" id="ratio-1-1">1:1 Square</button>
                            <button type="button" onclick="setCropperRatio(1.77777777778)" class="px-2 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-xs font-semibold transition duration-150 ratio-btn" id="ratio-16-9">16:9 Wide</button>
                            <button type="button" onclick="setCropperRatio(1.33333333333)" class="px-2 py-1.5 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-lg text-xs font-semibold transition duration-150 ratio-btn" id="ratio-4-3">4:3 Standard</button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-2.5 mt-auto pt-4 border-t border-gray-800 relative">
                        <!-- Loading Overlay inside Sidebar -->
                        <div id="cropper-upload-spinner" class="absolute inset-0 bg-gray-900/80 backdrop-blur-xs flex flex-col items-center justify-center gap-2 rounded-xl hidden z-10">
                            <svg class="animate-spin h-6 w-6 text-burnt-orange" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">செருகுகிறது... (Inserting...)</span>
                        </div>

                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">உரை வடிவமைப்பு & செருகல் (Format & Insert)</span>
                        
                        <button type="button" onclick="submitCroppedImage('left')" class="w-full py-2.5 bg-burnt-orange hover:bg-orange-600 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-orange-600/10 cursor-pointer">
                            இடப்புறம் செருக (Insert Left)
                        </button>
                        <button type="button" onclick="submitCroppedImage('right')" class="w-full py-2.5 bg-burnt-orange hover:bg-orange-600 text-white font-bold rounded-xl text-xs transition shadow-lg shadow-orange-600/10 cursor-pointer">
                            வலப்புறம் செருக (Insert Right)
                        </button>
                        <button type="button" onclick="submitCroppedImage('center')" class="w-full py-2.5 bg-gray-800 hover:bg-gray-705 text-white font-bold rounded-xl text-xs border border-gray-700 transition cursor-pointer">
                            மத்தியில் செருக (Insert Center)
                        </button>
                        <button type="button" onclick="submitCroppedImage('full')" class="w-full py-2.5 bg-gray-800 hover:bg-gray-750 text-gray-300 font-medium rounded-xl text-xs border border-gray-700 transition cursor-pointer">
                            முழு அகலத்தில் செருக (Full Width)
                        </button>
                        
                        <button type="button" onclick="closeCropperModal()" class="w-full py-2 bg-transparent hover:bg-gray-800 text-gray-400 font-semibold rounded-xl text-xs transition border border-gray-800 mt-2 cursor-pointer">
                            ரத்து செய்க (Cancel)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cropper Javascript Handler -->
    <script>
        let activeCropper = null;
        const cropperInput = document.getElementById('post-image-cropper-input');
        const cropperModal = document.getElementById('cropper-modal');
        const cropperModalCard = document.getElementById('cropper-modal-card');
        const cropperTargetImage = document.getElementById('cropper-target-image');

        cropperInput.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const file = files[0];
                const reader = new FileReader();
                reader.onload = function (e) {
                    // Open Modal
                    cropperModal.classList.remove('hidden');
                    cropperModal.classList.add('flex');
                    setTimeout(() => {
                        cropperModalCard.classList.remove('scale-95');
                        cropperModalCard.classList.add('scale-100');
                    }, 50);

                    // Set target image source
                    cropperTargetImage.src = e.target.result;

                    // Initialize Cropper
                    if (activeCropper) {
                        activeCropper.destroy();
                    }
                    activeCropper = new Cropper(cropperTargetImage, {
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.9,
                        responsive: true,
                        restore: false,
                        checkCrossOrigin: false,
                        modal: true,
                        guides: true,
                        center: true,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        function closeCropperModal() {
            cropperModalCard.classList.remove('scale-100');
            cropperModalCard.classList.add('scale-95');
            setTimeout(() => {
                cropperModal.classList.remove('flex');
                cropperModal.classList.add('hidden');
                if (activeCropper) {
                    activeCropper.destroy();
                    activeCropper = null;
                }
                cropperInput.value = '';
            }, 150);
        }

        function cropperZoom(value) {
            if (activeCropper) {
                activeCropper.zoom(value);
            }
        }

        function cropperRotate(deg) {
            if (activeCropper) {
                activeCropper.rotate(deg);
            }
        }

        function setCropperRatio(ratio) {
            if (activeCropper) {
                activeCropper.setAspectRatio(ratio);
            }
            // Update active state in UI buttons
            document.querySelectorAll('.ratio-btn').forEach(btn => {
                btn.classList.remove('bg-burnt-orange', 'text-white');
                btn.classList.add('bg-gray-800', 'text-gray-300');
            });
            if (isNaN(ratio)) {
                document.getElementById('ratio-free').classList.remove('bg-gray-800', 'text-gray-300');
                document.getElementById('ratio-free').classList.add('bg-burnt-orange', 'text-white');
            } else if (ratio === 1) {
                document.getElementById('ratio-1-1').classList.remove('bg-gray-800', 'text-gray-300');
                document.getElementById('ratio-1-1').classList.add('bg-burnt-orange', 'text-white');
            } else if (ratio > 1.5) {
                document.getElementById('ratio-16-9').classList.remove('bg-gray-800', 'text-gray-300');
                document.getElementById('ratio-16-9').classList.add('bg-burnt-orange', 'text-white');
            } else {
                document.getElementById('ratio-4-3').classList.remove('bg-gray-800', 'text-gray-300');
                document.getElementById('ratio-4-3').classList.add('bg-burnt-orange', 'text-white');
            }
        }

        function submitCroppedImage(alignment) {
            if (!activeCropper) return;

            const uploadSpinner = document.getElementById('cropper-upload-spinner');
            if (uploadSpinner) {
                uploadSpinner.classList.remove('hidden');
            }

            activeCropper.getCroppedCanvas({
                maxWidth: 1600,
                maxHeight: 1600,
                imageSmoothingQuality: 'high'
            }).toBlob(function (blob) {
                const formData = new FormData();
                formData.append('image', blob, 'cropped_image.jpg');
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("posts.uploadImage") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (uploadSpinner) {
                        uploadSpinner.classList.add('hidden');
                    }

                    if (data.location) {
                        let styleAttr = '';
                        let classAttr = 'rounded-xl shadow-lg border border-gray-850 ';

                        if (alignment === 'left') {
                            styleAttr = 'float: left; margin: 0 16px 12px 0; max-width: 45%; height: auto;';
                            classAttr += 'kathaingo-img-left';
                        } else if (alignment === 'right') {
                            styleAttr = 'float: right; margin: 0 0 12px 16px; max-width: 45%; height: auto;';
                            classAttr += 'kathaingo-img-right';
                        } else if (alignment === 'center') {
                            styleAttr = 'display: block; margin: 24px auto; max-width: 80%; height: auto;';
                            classAttr += 'kathaingo-img-center';
                        } else {
                            styleAttr = 'display: block; margin: 24px auto; max-width: 100%; height: auto;';
                            classAttr += 'kathaingo-img-full';
                        }

                        // Insert into editor
                        if (typeof tinymce !== 'undefined' && tinymce.get('content')) {
                            tinymce.get('content').insertContent(`<img src="${data.location}" style="${styleAttr}" class="${classAttr}" alt="Post Image">`);
                        }
                        
                        // Close Modal
                        closeCropperModal();
                    } else {
                        alert('Upload failed: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(err => {
                    if (uploadSpinner) {
                        uploadSpinner.classList.add('hidden');
                    }
                    alert('Upload failed: ' + err);
                });
            }, 'image/jpeg', 0.9);
        }
    </script>
    <!-- 1. PREVIEW ARTICLE MODAL -->
    <div x-show="showPreviewModal" x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/85 backdrop-blur-sm p-4 overflow-y-auto" x-cloak>
        <div x-show="showPreviewModal" x-transition.scale class="bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full flex flex-col max-h-[90vh] overflow-hidden transform transition-all duration-300">
            <!-- Header -->
            <div class="px-6 py-4 bg-gray-950 border-b border-gray-850 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-semibold bg-burnt-orange/20 border border-burnt-orange/30 text-burnt-orange rounded-full">Preview View</span>
                    <h3 class="text-lg font-bold text-white">பதிவின் முன்பார்வை (Article Preview)</h3>
                </div>
                <button type="button" @click="showPreviewModal = false" class="text-gray-400 hover:text-white transition cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 md:p-8 overflow-y-auto bg-slate-gray min-h-[50vh] text-gray-300 leading-relaxed">
                <div class="max-w-3xl mx-auto">
                    <!-- Category Breadcrumbs -->
                    <div class="flex items-center gap-2 text-xs text-burnt-orange font-semibold mb-4 bg-gray-950/40 border border-gray-850/50 rounded-full px-4 py-1.5 w-fit">
                        <span>Home</span>
                        <svg class="w-3 h-3 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span x-text="previewData.category || 'Uncategorized (வகைப்படுத்தப்படவில்லை)'"></span>
                    </div>

                    <!-- Title -->
                    <h1 class="text-2xl md:text-4xl font-black text-white mb-6 leading-tight" x-text="previewData.title || 'Untitled Post (தலைப்பற்ற பதிவு)'"></h1>

                    <!-- Author & Info -->
                    <div class="flex items-center gap-3 text-gray-400 text-sm mb-8 border-b border-gray-800 pb-4">
                        <div class="w-9 h-9 rounded-full bg-burnt-orange/20 border border-burnt-orange flex items-center justify-center font-bold text-burnt-orange" x-text="(previewData.author || 'A').substring(0,1)"></div>
                        <div>
                            <span class="font-semibold text-gray-200" x-text="previewData.author"></span>
                            <span class="mx-2 text-gray-600">•</span>
                            <span x-text="previewData.readTime + ' min read (நிமிட வாசிப்பு)'"></span>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <template x-if="previewData.imageSrc">
                        <div class="w-full rounded-2xl overflow-hidden shadow-2xl mb-8 bg-gray-800 border border-gray-800">
                            <img :src="previewData.imageSrc" alt="Featured Image" class="w-full max-h-[450px] object-cover">
                        </div>
                    </template>

                    <!-- Excerpt -->
                    <template x-if="previewData.excerpt">
                        <p class="text-lg font-bold text-burnt-orange leading-relaxed mb-6 border-l-4 border-burnt-orange pl-4" x-text="previewData.excerpt"></p>
                    </template>

                    <!-- Content body -->
                    <div class="prose prose-invert prose-lg max-w-none text-gray-300 leading-relaxed bg-gray-900/30 border border-gray-800/40 rounded-3xl p-6 md:p-8 shadow-xl" x-html="previewData.content || '<p class=\'text-gray-500 italic\'>பதிவின் உள்ளடக்கம் காலியாக உள்ளது (No content written yet).</p>'"></div>

                    <!-- Tags -->
                    <template x-if="previewData.tags && previewData.tags.length > 0">
                        <div class="flex items-center gap-2 mt-8 flex-wrap border-t border-gray-800 pt-4">
                            <span class="text-xs text-gray-500 uppercase font-bold">Tags:</span>
                            <template x-for="tag in previewData.tags" :key="tag">
                                <span class="bg-burnt-orange hover:bg-orange-600 text-white text-xs font-semibold px-2.5 py-1 rounded transition" x-text="tag"></span>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-950 border-t border-gray-850 flex items-center justify-end">
                <button type="button" @click="showPreviewModal = false" class="px-5 py-2 bg-gray-850 hover:bg-gray-800 text-white font-semibold rounded-xl text-xs transition border border-gray-850 cursor-pointer">
                    Back to Edit (மீண்டும் எழுதவும்)
                </button>
            </div>
        </div>
    </div>


    <!-- 2. REVIEW ARTICLE REPORT MODAL -->
    <div x-show="showReviewModal" x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/85 backdrop-blur-sm p-4 overflow-y-auto" x-cloak>
        <div x-show="showReviewModal" x-transition.scale class="bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-w-3xl w-full flex flex-col max-h-[90vh] overflow-hidden transform transition-all duration-300">
            <!-- Header -->
            <div class="px-6 py-4 bg-gray-950 border-b border-gray-850 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs font-semibold bg-burnt-orange/20 border border-burnt-orange/30 text-burnt-orange rounded-full">Quality Scanner</span>
                    <h3 class="text-lg font-bold text-white">பதிவின் மதிப்பாய்வு அறிக்கை (Article Review Report)</h3>
                </div>
                <button type="button" @click="showReviewModal = false" class="text-gray-400 hover:text-white transition cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <!-- Body -->
            <div class="p-6 overflow-y-auto bg-gray-950/40 text-gray-300">
                <template x-if="reviewReport">
                    <div>
                        <!-- Overall Readiness Indicator Banner -->
                        <div class="p-4 rounded-xl border mb-6 flex items-center justify-between shadow-md"
                            :class="{
                                'bg-emerald-950/20 border-emerald-500/30 text-emerald-400': reviewReport.readiness === 'Reviewed',
                                'bg-amber-950/20 border-amber-500/30 text-amber-400': reviewReport.readiness === 'Needs Attention',
                                'bg-rose-950/20 border-rose-500/30 text-rose-400': reviewReport.readiness === 'Major Issues'
                            }">
                            <div class="flex items-center gap-3">
                                <!-- Status Icons -->
                                <template x-if="reviewReport.readiness === 'Reviewed'">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                                <template x-if="reviewReport.readiness === 'Needs Attention'">
                                    <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </template>
                                <template x-if="reviewReport.readiness === 'Major Issues'">
                                    <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </template>
                                
                                <div>
                                    <h4 class="font-bold text-sm" x-text="
                                        reviewReport.readiness === 'Reviewed' ? 'மிகவும் நன்று! பதிவு தயாராக உள்ளது. (Looks Good!)' : 
                                        (reviewReport.readiness === 'Needs Attention' ? 'கவனம் தேவை: சில பரிந்துரைகள் உள்ளன. (Needs Attention)' : 'முக்கிய திருத்தங்கள் தேவை! (Major Issues Detected)')
                                    "></h4>
                                    <p class="text-xs mt-0.5 opacity-85" x-text="
                                        reviewReport.readiness === 'Reviewed' ? 'முக்கிய பிழைகள் எதுவும் இல்லை. தாராளமாக சமர்ப்பிக்கலாம்!' : 
                                        (reviewReport.readiness === 'Needs Attention' ? 'பதிவை மேலும் மேம்படுத்த சில திருத்தங்கள் பரிந்துரைக்கப்படுகின்றன.' : 'பதிவைச் சமர்ப்பிக்கும் முன் சிவப்பு நிற எச்சரிக்கைகளை சரிசெய்யவும்.')
                                    "></p>
                                </div>
                            </div>
                            <!-- Counter badges -->
                            <div class="flex gap-2">
                                <span class="px-2.5 py-1 rounded bg-rose-500/20 text-rose-400 border border-rose-500/20 text-xs font-bold" x-show="reviewReport.redCount > 0" x-text="reviewReport.redCount + ' பிழைகள் (Errors)'"></span>
                                <span class="px-2.5 py-1 rounded bg-amber-500/20 text-amber-400 border border-amber-500/20 text-xs font-bold" x-show="reviewReport.yellowCount > 0" x-text="reviewReport.yellowCount + ' பரிந்துரைகள் (Warnings)'"></span>
                            </div>
                        </div>

                        <!-- Readability Index Block -->
                        <div class="mb-6 p-4 rounded-xl bg-gray-900 border border-gray-800 flex items-center justify-between text-sm">
                            <span class="text-gray-400">வாசிப்புத் தன்மை (Readability Index):</span>
                            <span class="font-semibold text-burnt-orange" x-text="reviewReport.readability"></span>
                        </div>

                        <!-- Checklist list -->
                        <h4 class="font-bold text-sm text-gray-400 mb-3 border-b border-gray-800 pb-2">மதிப்பீட்டுப் புள்ளிகள் (Evaluation Items)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <template x-for="item in reviewReport.checklist" :key="item.id">
                                <div class="p-3 bg-gray-900 border border-gray-800 rounded-xl flex items-start gap-3">
                                    <!-- Indicator circle -->
                                    <div class="mt-0.5">
                                        <template x-if="item.status === 'green'">
                                            <span class="text-emerald-500 flex items-center justify-center bg-emerald-500/10 border border-emerald-500/25 rounded-full p-0.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </span>
                                        </template>
                                        <template x-if="item.status === 'yellow'">
                                            <span class="text-amber-500 flex items-center justify-center bg-emerald-500/10 border border-amber-500/25 rounded-full p-0.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/></svg>
                                            </span>
                                        </template>
                                        <template x-if="item.status === 'red'">
                                            <span class="text-rose-500 flex items-center justify-center bg-rose-500/10 border border-rose-500/25 rounded-full p-0.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </span>
                                        </template>
                                    </div>
                                    
                                    <div>
                                        <h5 class="text-xs font-bold text-white" x-text="item.label"></h5>
                                        <p class="text-xs text-gray-400 mt-1" x-text="item.message"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-950 border-t border-gray-850 flex items-center justify-between">
                <p class="text-xs text-gray-500">Kathaingo Content Review System v2.0</p>
                <button type="button" @click="showReviewModal = false" class="px-5 py-2 bg-burnt-orange hover:bg-orange-700 text-white font-semibold rounded-xl text-xs transition cursor-pointer select-none">
                    Close (அறிக்கையை மூடு)
                </button>
            </div>
        </div>
    </div>


    <!-- 3. SOFT WARNING BEFORE SUBMIT MODAL -->
    <div x-show="showWarningModal" x-transition.opacity class="fixed inset-0 z-[9999] flex items-center justify-center bg-slate-950/85 backdrop-blur-sm p-4" x-cloak>
        <div x-show="showWarningModal" x-transition.scale class="bg-gray-900 border border-gray-800 rounded-2xl shadow-2xl max-w-md w-full flex flex-col overflow-hidden transform transition-all duration-300">
            <!-- Header -->
            <div class="px-5 py-4 bg-gray-950 border-b border-gray-850 flex items-center gap-2 text-rose-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <h3 class="text-base font-bold text-white">சமர்ப்பிப்பு உறுதிப்படுத்தல் (Submission Alert)</h3>
            </div>
            
            <!-- Body -->
            <div class="p-6 text-sm text-gray-300 leading-relaxed bg-gray-950/20">
                <!-- Case A: Unreviewed -->
                <template x-if="warningModalType === 'unreviewed'">
                    <div>
                        <p class="font-semibold text-amber-400">இந்த பதிவை இன்னும் Review செய்யவில்லை.</p>
                        <p class="mt-2 text-gray-400">பதிவை சமர்ப்பிப்பதற்கு முன் ஏதேனும் எழுத்துப் பிழைகள் அல்லது வடிவமைப்பு விடுபடல்கள் உள்ளதா என்று தரம் சரிபார்க்க விரும்புகிறீர்களா?</p>
                    </div>
                </template>

                <!-- Case B: Major Issues -->
                <template x-if="warningModalType === 'major_issues'">
                    <div>
                        <p class="font-semibold text-rose-400">இந்த பதிவில் சில முக்கியமான திருத்தங்கள் தேவைப்படலாம்.</p>
                        <p class="mt-2 text-gray-400 font-medium">மதிப்பாய்வு முடிவுகளின்படி சில கட்டாய விவரங்கள் அல்லது இலக்கண/எழுத்துப் பிழைகள் சிவப்பு நிறத்தில் குறிக்கப்பட்டுள்ளன. முதலில் திருத்தங்களைச் சரிபார்க்க விரும்புகிறீர்களா?</p>
                    </div>
                </template>
            </div>

            <!-- Footer -->
            <div class="px-5 py-3.5 bg-gray-950 border-t border-gray-850 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-2.5">
                <button type="button" @click="reviewNow()"
                    class="px-4 py-2 bg-burnt-orange hover:bg-orange-700 text-white font-semibold rounded-lg text-xs transition cursor-pointer text-center select-none">
                    Review Now (மதிீடு செய்)
                </button>
                <button type="button" @click="submitAnyway()"
                    class="px-4 py-2 border border-gray-800 hover:border-gray-700 bg-transparent text-gray-400 hover:text-white font-semibold rounded-lg text-xs transition cursor-pointer text-center select-none">
                    Submit Anyway (அப்படியே சமர்ப்பி)
                </button>
            </div>
        </div>
    </div>
</x-app-layout>