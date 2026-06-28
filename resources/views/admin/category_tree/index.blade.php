<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-burnt-orange leading-tight">
            {{ __('Category Tree Manager') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-gray-600">Drag and drop items to move them to a new parent. <br>
                    <span class="text-sm italic">Note: You can only move items to a valid parent type (e.g., Subcategory -> Category).</span>
                    </p>

                    <div id="category-tree-container" class="space-y-4">
                        @foreach($categories as $category)
                            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 category-item" data-id="{{ $category->id }}">
                                <h3 class="font-bold text-lg text-gray-800 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-burnt-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    {{ $category->name }}
                                </h3>

                                <ul class="ml-6 mt-2 space-y-2 border-l-2 border-gray-200 pl-4 subcategory-list" data-parent-id="{{ $category->id }}">
                                    @foreach($category->subcategories as $subcategory)
                                        <li class="bg-white p-3 rounded shadow-sm border border-gray-100 subcategory-item cursor-move hover:bg-gray-50" data-id="{{ $subcategory->id }}" data-type="subcategory">
                                            <div class="font-semibold text-gray-700 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                                {{ $subcategory->name }}
                                            </div>

                                            <ul class="ml-6 mt-2 space-y-2 border-l-2 border-gray-100 pl-4 child-category-list min-h-[10px]" data-parent-id="{{ $subcategory->id }}">
                                                @foreach($subcategory->childCategories as $child)
                                                    <li class="bg-gray-50 p-2 rounded border border-gray-200 child-category-item cursor-move hover:bg-white" data-id="{{ $child->id }}" data-type="child_category">
                                                        <div class="text-sm text-gray-600 flex items-center">
                                                            <svg class="w-3 h-3 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                                            {{ $child->name }}
                                                        </div>

                                                        <ul class="ml-6 mt-2 space-y-1 border-l-2 border-gray-200 pl-4 grandchild-category-list min-h-[10px]" data-parent-id="{{ $child->id }}">
                                                            @foreach($child->grandchildCategories as $grandchild)
                                                                <li class="bg-white p-1.5 rounded border border-gray-100 text-xs grandchild-category-item cursor-move hover:bg-gray-50" data-id="{{ $grandchild->id }}" data-type="grandchild_category">
                                                                    <span class="flex items-center text-gray-500">
                                                                        <svg class="w-3 h-3 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path></svg>
                                                                        {{ $grandchild->name }}
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = '{{ csrf_token() }}';

            // Helper to make lists sortable
            function initSortable(selector, groupName, type) {
                document.querySelectorAll(selector).forEach(function (el) {
                    Sortable.create(el, {
                        group: groupName,
                        animation: 150,
                        ghostClass: 'bg-yellow-100',
                        onEnd: function (evt) {
                            const itemEl = evt.item;
                            const newParentEl = evt.to;
                            const oldParentEl = evt.from;

                            // Only act if parent changed
                            if (newParentEl !== oldParentEl) {
                                const itemId = itemEl.getAttribute('data-id');
                                const newParentId = newParentEl.getAttribute('data-parent-id');
                                
                                console.log(`Moving ${type} ${itemId} to parent ${newParentId}`);

                                fetch('{{ route("admin.category-tree.reparent") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    },
                                    body: JSON.stringify({
                                        item_id: itemId,
                                        item_type: type,
                                        new_parent_id: newParentId
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        // Optional: Toast notification
                                        console.log('Moved successfully');
                                    } else {
                                        alert('Error moving item: ' + data.message);
                                        // Revert move (reload page or simplistic revert)
                                        location.reload(); 
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('An error occurred.');
                                    location.reload();
                                });
                            }
                        }
                    });
                });
            }

            // Init Subcategories (Level 2)
            initSortable('.subcategory-list', 'subcategories', 'subcategory');

            // Init Child Categories (Level 3)
            initSortable('.child-category-list', 'child_categories', 'child_category');

            // Init Grandchild Categories (Level 4)
            initSortable('.grandchild-category-list', 'grandchild_categories', 'grandchild_category');
        });
    </script>
</x-app-layout>
