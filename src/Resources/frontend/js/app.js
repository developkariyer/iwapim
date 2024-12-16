import $ from 'jquery';
import 'select2/dist/css/select2.css';
import 'select2';

// Initialize Select2
$(document).ready(function () {
    $('#category-select').select2({
        placeholder: "Search or select a category...",
        ajax: {
            url: '/ozon/category-tree', // Replace with your API endpoint
            dataType: 'json',
            delay: 250, // Add a small delay for better performance
            processResults: function (data) {
                // Map the categories to Select2's expected format
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.name,
                        children: item.children || [], // Add children if present
                    })),
                };
            },
        },
    });
});
