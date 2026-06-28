<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Manage Hero Images') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                            role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Upload Form -->
                    <div class="mb-8 p-6 bg-gray-50 border border-gray-200 rounded-xl shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Upload New Image</h3>
                        <form id="uploadForm" action="{{ route('admin.hero-images.store') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <input type="hidden" name="cropped_image" id="croppedImageData">
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Image (Max 5MB)</label>
                                <input type="file" id="imageInput" name="image" required accept="image/*" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2.5 file:px-5
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-burnt-orange file:text-white
                                    hover:file:bg-orange-600 file:cursor-pointer
                                " />
                            </div>

                            <!-- Cropper Wrapper -->
                            <div id="cropperWrapper" class="hidden mt-4 bg-gray-100 rounded-xl p-4 border border-gray-300">
                                <span class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Adjust Crop Area (Ideal Aspect Ratio: 16:7)</span>
                                <div class="max-h-[450px] overflow-hidden rounded-lg">
                                    <img id="imageToCrop" src="" class="max-w-full block" />
                                </div>
                                <!-- Directional and Zoom Adjustment Buttons -->
                                <div class="flex flex-wrap gap-2 mt-4 justify-center">
                                    <button type="button" id="btnMoveLeft" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">← Left</button>
                                    <button type="button" id="btnMoveRight" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">→ Right</button>
                                    <button type="button" id="btnMoveUp" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">↑ Up</button>
                                    <button type="button" id="btnMoveDown" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">↓ Down</button>
                                    <button type="button" id="btnZoomIn" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">＋ Zoom In</button>
                                    <button type="button" id="btnZoomOut" class="px-3 py-1.5 bg-gray-800 hover:bg-gray-700 text-white rounded text-sm transition">－ Zoom Out</button>
                                    <button type="button" id="btnReset" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition">Reset</button>
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" id="submitBtn"
                                    class="px-6 py-2.5 bg-burnt-orange hover:bg-orange-600 text-white font-bold rounded-full transition transform hover:scale-105 shadow">
                                    Upload & Crop Image
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Images Grid -->
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Hero Images Sequence (Drag & Drop to Sort)</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="heroImagesGrid">
                        @forelse($heroImages as $image)
                            <div class="relative group bg-white border border-gray-200 rounded-2xl overflow-hidden shadow cursor-move transition-all hover:shadow-md" data-id="{{ $image->id }}">
                                <img src="{{ $image->image_path }}" alt="Hero Image" class="w-full h-48 object-cover">
                                <div
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <form action="{{ route('admin.hero-images.destroy', $image->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this image?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="bg-red-600 text-white p-3 rounded-full hover:bg-red-700 transition transform hover:scale-110 shadow"
                                            title="Delete Image">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div
                                    class="order-badge absolute top-3 left-3 bg-black/65 backdrop-blur border border-gray-800 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow">
                                    Order: {{ $image->order }}
                                </div>
                                <!-- Grab handle indicator icon -->
                                <div class="absolute top-3 right-3 bg-black/65 backdrop-blur border border-gray-800 text-white p-1.5 rounded-full shadow pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    </svg>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-16 text-gray-500 bg-gray-55 border border-gray-200 rounded-2xl">
                                <p class="text-lg mb-2 text-gray-700">No hero images uploaded yet.</p>
                                <p class="text-sm text-gray-500">Upload images above to build your homepage hero slider.</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Cropper.js & SortableJS Library Integration -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Cropper Setup
            const imageInput = document.getElementById('imageInput');
            const imageToCrop = document.getElementById('imageToCrop');
            const cropperWrapper = document.getElementById('cropperWrapper');
            const croppedImageData = document.getElementById('croppedImageData');
            const uploadForm = document.getElementById('uploadForm');
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
                            aspectRatio: 16 / 7, // Wide banner aspect ratio
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

            uploadForm.addEventListener('submit', function (e) {
                if (cropper) {
                    e.preventDefault();
                    // Crop canvas
                    const canvas = cropper.getCroppedCanvas({
                        width: 1920,
                        height: 840,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high'
                    });
                    
                    // Get base64 string
                    const dataUrl = canvas.toDataURL('image/jpeg', 1.0);
                    croppedImageData.value = dataUrl;
                    
                    // Clear file input to avoid sending duplicate raw file payload
                    const dataTransfer = new DataTransfer();
                    imageInput.files = dataTransfer.files;
                    
                    // Submit form
                    uploadForm.submit();
                }
            });

            // Drag and Drop Sorting Setup
            const grid = document.getElementById('heroImagesGrid');
            if (grid && grid.querySelector('[data-id]')) {
                new Sortable(grid, {
                    animation: 150,
                    ghostClass: 'opacity-40',
                    onEnd: function () {
                        const ids = Array.from(grid.querySelectorAll('[data-id]')).map(el => el.dataset.id);
                        
                        fetch('{{ route("admin.hero-images.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: ids })
                       })
                       .then(response => response.json())
                       .then(data => {
                           if (data.success) {
                               // Update order badges
                               grid.querySelectorAll('[data-id]').forEach((el, index) => {
                                   const badge = el.querySelector('.order-badge');
                                   if (badge) {
                                       badge.textContent = 'Order: ' + (index + 1);
                                   }
                               });
                               showNotification(data.message || 'Order updated successfully.', 'success');
                           } else {
                               showNotification(data.message || 'Failed to update order.', 'error');
                           }
                       })
                       .catch(error => {
                           console.error('Error updating order:', error);
                           showNotification('An error occurred while updating order.', 'error');
                       });
                   }
                });
            }

            // Dynamic Notifications Utility
            function showNotification(message, type = 'success') {
                const container = document.getElementById('notification-container') || createNotificationContainer();
                const notification = document.createElement('div');
                notification.className = `p-4 rounded-xl border shadow-xl flex items-center gap-3 text-sm transition-all duration-500 transform translate-y-2 opacity-0 ${
                    type === 'success' 
                        ? 'bg-green-100 border-green-400 text-green-700' 
                        : 'bg-red-100 border-red-400 text-red-700'
                }`;
                notification.innerHTML = `
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' 
                            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' 
                            : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>'}
                    </svg>
                    <span>${message}</span>
                `;
                container.appendChild(notification);
                
                // Animate in
                setTimeout(() => {
                    notification.classList.remove('translate-y-2', 'opacity-0');
                }, 10);
                
                // Animate out
                setTimeout(() => {
                    notification.classList.add('translate-y-2', 'opacity-0');
                    setTimeout(() => {
                       notification.remove();
                    }, 500);
                }, 3000);
            }

            function createNotificationContainer() {
                const container = document.createElement('div');
                container.id = 'notification-container';
                container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full';
                document.body.appendChild(container);
                return container;
            }
        });
    </script>
</x-app-layout>