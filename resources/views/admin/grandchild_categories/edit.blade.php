<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Edit Grandchild Category') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST"
                        action="{{ route('admin.grandchild-categories.update', $grandchildCategory->id) }}">
                        @csrf
                        @method('PUT')

                        <div x-data="categorySelect()">
                            <!-- Category -->
                            <div class="mb-4">
                                <label for="category_id"
                                    class="block text-gray-700 text-sm font-bold mb-2">Category</label>
                                <select id="category_id" x-model="selectedCategory" @change="updateSubcategories"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Category</option>
                                    <template x-for="category in categories" :key="category.id">
                                        <option :value="category.id" x-text="category.name"
                                            :selected="category.id == selectedCategory"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Subcategory -->
                            <div class="mb-4">
                                <label for="subcategory_id"
                                    class="block text-gray-700 text-sm font-bold mb-2">Subcategory</label>
                                <select id="subcategory_id" x-model="selectedSubcategory"
                                    @change="updateChildCategories"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Subcategory</option>
                                    <template x-for="subcategory in subcategories" :key="subcategory.id">
                                        <option :value="subcategory.id" x-text="subcategory.name"
                                            :selected="subcategory.id == selectedSubcategory"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Child Category -->
                            <div class="mb-4">
                                <label for="child_category_id" class="block text-gray-700 text-sm font-bold mb-2">Child
                                    Category</label>
                                <select id="child_category_id" name="child_category_id" x-model="selectedChildCategory"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    required>
                                    <option value="">Select Child Category</option>
                                    <template x-for="child in childCategories" :key="child.id">
                                        <option :value="child.id" x-text="child.name"
                                            :selected="child.id == selectedChildCategory"></option>
                                    </template>
                                </select>
                                @error('child_category_id')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                            <input type="text" name="name" id="name" value="{{ $grandchildCategory->name }}"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>
                            @error('name')
                                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit"
                                class="bg-burnt-orange hover:bg-orange-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function categorySelect() {
            return {
                categories: @json($categories),
                subcategories: [],
                childCategories: [],
                selectedCategory: '{{ $grandchildCategory->childCategory->subcategory->category_id }}',
                selectedSubcategory: '{{ $grandchildCategory->childCategory->subcategory_id }}',
                selectedChildCategory: '{{ $grandchildCategory->child_category_id }}',

                init() {
                    this.updateSubcategories();
                    this.updateChildCategories();
                },

                updateSubcategories() {
                    const category = this.categories.find(c => c.id == this.selectedCategory);
                    this.subcategories = category ? category.subcategories : [];

                    // On manual change, clear selection
                    if (this.selectedCategory != '{{ $grandchildCategory->childCategory->subcategory->category_id }}') {
                        if (!this.subcategories.find(s => s.id == this.selectedSubcategory)) {
                            this.selectedSubcategory = '';
                            this.childCategories = [];
                        }
                    }
                },

                updateChildCategories() {
                    const subcategory = this.subcategories.find(s => s.id == this.selectedSubcategory);
                    this.childCategories = subcategory ? subcategory.child_categories : [];

                    if (!this.childCategories.find(c => c.id == this.selectedChildCategory)) {
                        this.selectedChildCategory = '';
                    }
                }
            }
        }
    </script>
</x-app-layout>