<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Create Child Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.child-categories.store') }}">
                        @csrf

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                            <select id="category_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subcategory -->
                        <div class="mb-4">
                            <label for="subcategory_id"
                                class="block text-gray-700 text-sm font-bold mb-2">Subcategory</label>
                            <select id="subcategory_id" name="subcategory_id"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required disabled>
                                <option value="">Select Category First</option>
                            </select>
                            @error('subcategory_id')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                            <input type="text" name="name" id="name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                            @error('name')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                class="bg-burnt-orange hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Create
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const categories = @json($categories);
        const categorySelect = document.getElementById('category_id');
        const subcategorySelect = document.getElementById('subcategory_id');

        categorySelect.addEventListener('change', function () {
            const categoryId = this.value;
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';

            if (categoryId) {
                const selectedCategory = categories.find(c => c.id == categoryId);
                if (selectedCategory && selectedCategory.subcategories.length > 0) {
                    selectedCategory.subcategories.forEach(sub => {
                        const option = document.createElement('option');
                        option.value = sub.id;
                        option.textContent = sub.name;
                        subcategorySelect.appendChild(option);
                    });
                    subcategorySelect.disabled = false;
                } else {
                    subcategorySelect.innerHTML = '<option value="">No subcategories found</option>';
                    subcategorySelect.disabled = true;
                }
            } else {
                subcategorySelect.innerHTML = '<option value="">Select Category First</option>';
                subcategorySelect.disabled = true;
            }
        });
    </script>
</x-app-layout>