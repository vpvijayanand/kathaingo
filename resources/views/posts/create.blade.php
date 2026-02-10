<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Create New Post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Title')" />
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Content with Rich Text Editor -->
                        <div class="mb-4">
                            <x-input-label for="content" :value="__('Content')" />
                            <textarea id="content" name="content" class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm" rows="15" required>{{ old('content') }}</textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>

                        <!-- Featured Image Upload -->
                        <div class="mb-4">
                            <x-input-label for="featured_image" :value="__('Featured Image')" />
                            <input type="file" id="featured_image" name="featured_image" accept="image/*" class="block mt-1 w-full text-gray-900 border border-gray-300 rounded-md shadow-sm focus:border-burnt-orange focus:ring-burnt-orange" />
                            <p class="mt-1 text-sm text-gray-500">Upload a featured image for your post (JPG, PNG, GIF - Max 2MB)</p>
                            <x-input-error :messages="$errors->get('featured_image')" class="mt-2" />
                        </div>

                        <!-- Image URL (Alternative) -->
                        <div class="mb-4">
                            <x-input-label for="image" :value="__('Image URL (Alternative)')" />
                            <x-text-input id="image" class="block mt-1 w-full" type="url" name="image" :value="old('image')" placeholder="https://example.com/image.jpg" />
                            <p class="mt-1 text-sm text-gray-500">Or provide an external image URL</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        <!-- Video URL (YouTube/Facebook) -->
                        <div class="mb-4">
                            <x-input-label for="video_url" :value="__('Video URL (YouTube/Facebook)')" />
                            <x-text-input id="video_url" class="block mt-1 w-full" type="url" name="video_url" :value="old('video_url')" placeholder="https://www.youtube.com/watch?v=..." />
                            <p class="mt-1 text-sm text-gray-500">Paste a YouTube or Facebook video URL to embed in your post</p>
                            <x-input-error :messages="$errors->get('video_url')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div class="mb-4">
                            <x-input-label for="category_id" :value="__('Category (Optional)')" />
                            <select id="category_id" name="category_id" class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Subcategory -->
                        <div class="mb-4">
                            <x-input-label for="subcategory_id" :value="__('Subcategory (Optional)')" />
                            <select id="subcategory_id" name="subcategory_id" class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm">
                                <option value="">Select a subcategory</option>
                                @foreach($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}" {{ old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>{{ $subcategory->category->name }} → {{ $subcategory->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('subcategory_id')" class="mt-2" />
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full bg-white text-gray-900 border-gray-300 focus:border-burnt-orange focus:ring-burnt-orange rounded-md shadow-sm">
                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button class="ml-4 bg-burnt-orange hover:bg-orange-700">
                                {{ __('Create Post') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TinyMCE Script -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                'forecolor backcolor | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | link image media | removeformat code | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            
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
</x-app-layout>
