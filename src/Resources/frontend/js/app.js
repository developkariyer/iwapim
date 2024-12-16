import 'select2/dist/css/select2.css';
import $ from 'jquery';
import 'select2';

$(document).ready(function () {
    $('#category-select').select2({
        placeholder: "Search or select a category...",
        width: '100%',
        ajax: {
            url: '/api/categories', // Your API endpoint
            dataType: 'json',
            processResults: function (data) {
                // Convert nested categories to Select2 format
                function transformCategories(categories) {
                    return categories.map(category => ({
                        id: category.id,
                        text: category.name,
                        children: category.children ? transformCategories(category.children) : null
                    }));
                }

                // Transform the top-level categories
                const results = transformCategories(data);

                return {
                    results: results
                };
            },
        },
    });
});
