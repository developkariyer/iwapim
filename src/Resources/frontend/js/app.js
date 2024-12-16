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
                // Transform the data to make category_name unselectable
                function transformCategories(categories) {
                    return categories.map(category => ({
                        text: category.category_name, // Unselectable parent
                        children: [
                            ...(category.children ? transformCategories(category.children) : []),
                            ...(category.children || []).map(child => ({
                                id: child.type_id, // Selectable type_id
                                text: child.type_name // Selectable type_name
                            }))
                        ]
                    }));
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
            return $('<span><strong>' + data.text + '</strong></span>'); // Bold for parent categories
        }
        return $('<span>' + data.text + '</span>'); // Normal for selectable items
    }
});
