import 'select2/dist/css/select2.css';
import $ from 'jquery';
import 'select2';

// Initialize Select2
$(document).ready(function () {
    $('#category-select').select2({
        placeholder: "Search or select a category...",
        ajax: {
            url: '/api/categories', // Backend API endpoint for category options
            dataType: 'json',
            processResults: (data) => {
                return {
                    results: data.map((item) => ({
                        id: item.id,
                        text: item.name,
                        children: item.children || [],
                    })),
                };
            },
        },
    });
});
