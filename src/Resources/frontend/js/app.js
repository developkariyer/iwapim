import 'select2/dist/css/select2.css';
import $ from 'jquery';
import 'select2';

$(document).ready(function () {
    $('#category-select').select2({
        placeholder: "Search or select a category...",
        width: '100%',
        ajax: {
            url: '/ozon/category-tree', // Your API endpoint
            dataType: 'json',
            processResults: function (data) {
                // Recursive function to transform nested categories
                function transformCategories(categories) {
                    return categories.map(category => {
                        const node = {
                            text: category.category_name || category.type_name, // Show name
                            id: category.type_id || null, // Only set `id` for selectable items
                            children: []
                        };

                        // If children exist, process them recursively
                        if (category.children && category.children.length > 0) {
                            node.children = transformCategories(category.children);
                        }

                        // If this node has an `id` (type_name), it's selectable
                        if (!node.id) {
                            node.disabled = true; // Disable unselectable parents
                        }

                        return node;
                    });
                }

                // Transform the top-level categories
                const results = transformCategories(data);

                return {
                    results: results
                };
            },
        },
        templateResult: formatResult,
    });

    // Format display to distinguish unselectable parents
    function formatResult(data) {
        if (data.children) {
            return $('<span><strong>' + data.text + '</strong></span>'); // Bold for unselectable parents
        }
        return $('<span>' + data.text + '</span>'); // Normal for selectable items
    }
});
