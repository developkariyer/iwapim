import 'select2/dist/css/select2.css';
import $ from 'jquery';
import 'select2';

$(document).ready(function () {
    $('#category-select').select2({
        placeholder: "Search or select a category...",
        width: '100%',
        minimumInputLength: 3, // Wait for at least 3 characters
        ajax: {
            url: '/ozon/category-tree',
            dataType: 'json',
            processResults: function (data) {
                function transformCategories(categories) {
                    return categories.map(category => {
                        if (category.category_name) {
                            return {
                                text: category.category_name, // Parent name
                                disabled: true, // Unselectable parent
                                children: category.children ? transformCategories(category.children) : [],
                            };
                        }

                        if (category.type_name) {
                            return {
                                id: category.type_id, // Selectable id
                                text: category.type_name, // Selectable text
                            };
                        }

                        return null; // Handle edge cases
                    }).filter(Boolean); // Remove null entries
                }

                const results = transformCategories(data);

                return {
                    results: results
                };
            },
        },
        templateResult: formatResult,
    });

    function formatResult(data) {
        if (data.children) {
            return $('<span><strong>' + data.text + '</strong></span>'); // Bold for unselectable parents
        }
        return $('<span>' + data.text + '</span>'); // Normal for selectable items
    }
});
