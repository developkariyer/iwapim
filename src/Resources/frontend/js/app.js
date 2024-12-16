import 'select2/dist/css/select2.css'; // Select2 styles
import $ from 'jquery'; // jQuery
import 'select2'; // Select2 library

$(document).ready(function () {
    $('#category-select').select2({
        placeholder: "Search or select a category...",
        width: '100%',
        ajax: {
            url: '/ozon/category-tree', // Your API endpoint
            dataType: 'json',
            processResults: function (data) {
                // Map API results to Select2 format
                return {
                    results: data
                };
            },
        },
    });
});
