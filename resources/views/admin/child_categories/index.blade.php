<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
                {{ __('Manage Child Categories') }}
            </h2>
            <a href="{{ route('admin.child-categories.create') }}"
                class="inline-flex items-center px-4 py-2 bg-burnt-orange border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 focus:bg-orange-600 active:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-burnt-orange focus:ring-offset-2 transition ease-in-out duration-150">
                Create Child Category
            </a>
        </div>
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

                    <!-- Filter -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('admin.child-categories.index') }}"
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

                            @if(request('category_id') || request('subcategory_id'))
                                <a href="{{ route('admin.child-categories.index') }}"
                                    class="text-gray-500 hover:text-gray-700 underline text-sm">Clear Filters</a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Category
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Subcategory
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Slug
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="child-categories-table-body">
                                @forelse($childCategories as $childCategory)
                                    <tr data-id="{{ $childCategory->id }}" class="cursor-move">
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap">
                                                {{ $childCategory->subcategory->category->name }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap">
                                                {{ $childCategory->subcategory->name }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap font-semibold">
                                                {{ $childCategory->name }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-600 whitespace-no-wrap">{{ $childCategory->slug }}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <div class="flex gap-2">
                                                <a href="{{ route('admin.child-categories.edit', $childCategory->id) }}"
                                                    class="text-burnt-orange hover:text-orange-600 font-semibold">Edit</a>
                                                <form
                                                    action="{{ route('admin.child-categories.destroy', $childCategory->id) }}"
                                                    method="POST" onsubmit="return confirm('Delete this child category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-burnt-orange hover:text-orange-600 font-semibold">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center text-gray-500">
                                            No child categories found. Create your first one!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>