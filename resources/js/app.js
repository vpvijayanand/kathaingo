import './bootstrap';
import './tooltip';
import './emoji-picker';


import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

import Sortable from 'sortablejs';

document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('subcategories-table-body');
    if (el) {
        var sortable = Sortable.create(el, {
            animation: 150,
            onEnd: function (evt) {
                var itemEl = evt.item;

                // The toArray method uses data-id by default if configured, strictly we should use getAttribute
                // But Sortable.toArray() works if we set data-id. Let's manualy map to be safe
                var ids = Array.from(el.querySelectorAll('tr[data-id]')).map(function (row) {
                    return row.getAttribute('data-id');
                });

                fetch('/admin/subcategories/reorder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ids: ids })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Order updated');
                        } else {
                            alert('Failed to update order');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while reordering');
                    });
            }
        });
    }
});
