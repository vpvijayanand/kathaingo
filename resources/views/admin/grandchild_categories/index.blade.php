<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
                {{ __('Grandchild Categories (Level 4)') }}
            </h2>
            <a href="{{ route('admin.grandchild-categories.create') }}"
                class="bg-burnt-orange hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                Add Grandchild Category
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Filter -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('admin.grandchild-categories.index') }}"
                            class="flex flex-wrap items-center gap-4">
                            <!-- Category Filter -->
                            <div class="w-full md:w-1/4">
                                <select name="category_id" onchange="this.form.submit()"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-burnt-orange focus:border-burnt-orange block w-full p-2.5">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Subcategory Filter -->
                            <div class="w-full md:w-1/4">
                                <select name="subcategory_id" onchange="this.form.submit()"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-burnt-orange focus:border-burnt-orange block w-full p-2.5"
                                    {{ !request('category_id') ? 'disabled' : '' }}>
                                    <option value="">All Subcategories</option>
                                    @if(request('category_id'))
                                        @php
                                            $selectedCategory = $categories->firstWhere('id', request('category_id'));
                                        @endphp
                                        @if($selectedCategory)
                                            @foreach($selectedCategory->subcategories as $subcategory)
                                                <option value="{{ $subcategory->id }}" {{ request('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                                    {{ $subcategory->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                            </div>

                            <!-- Child Category Filter -->
                            <div class="w-full md:w-1/4">
                                <select name="child_category_id" onchange="this.form.submit()"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-burnt-orange focus:border-burnt-orange block w-full p-2.5"
                                    {{ !request('subcategory_id') ? 'disabled' : '' }}>
                                    <option value="">All Child Categories</option>
                                    @if(request('subcategory_id') && isset($selectedCategory))
                                        @php
                                            $selectedSubcategory = $selectedCategory->subcategories->firstWhere('id', request('subcategory_id'));
                                        @endphp
                                        @if($selectedSubcategory)
                                            @foreach($selectedSubcategory->childCategories as $child)
                                                <option value="{{ $child->id }}" {{ request('child_category_id') == $child->id ? 'selected' : '' }}>
                                                    {{ $child->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                            </div>

                            @if(request('category_id'))
                                <a href="{{ route('admin.grandchild-categories.index') }}"
                                    class="text-gray-500 hover:text-gray-700 underline text-sm">Clear Filters</a>
                            @endif
                        </form>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subcategory</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Child Category</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Grandchild Category</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Slug</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-grandchild-categories" class="bg-white divide-y divide-gray-200">
                            @foreach($grandchildCategories as $grandchild)
                                <tr data-id="{{ $grandchild->id }}" class="cursor-move">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $grandchild->childCategory->subcategory->category->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $grandchild->childCategory->subcategory->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $grandchild->childCategory->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $grandchild->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $grandchild->slug }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.grandchild-categories.edit', $grandchild->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <form action="{{ route('admin.grandchild-categories.destroy', $grandchild->id) }}"
                                            method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var el = document.getElementById('sortable-grandchild-categories');
            var sortable = Sortable.create(el, {
                animation: 150,
                onEnd: function (evt) {
                    var order = [];
                    el.querySelectorAll('tr').forEach(function (row) {
                        order.push(row.getAttribute('data-id'));
                    });

                    fetch('{{ route("admin.grandchild-categories.reorder") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order: order })
                    });
                }
            });
        });
    </script>
</x-app-layout>