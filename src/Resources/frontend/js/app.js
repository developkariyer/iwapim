import './styles/app.css'; // Include your custom CSS
import $ from 'jquery';

$(document).ready(function () {
    const searchBox = $('#search-box');
    const categoryList = $('#category-list');

    // Fetch categories from backend
    fetch('/ozon/category-tree')
        .then(response => response.json())
        .then(data => {
            renderCategories(data); // Render categories on load
        });

    // Render categories into the list
    function renderCategories(categories, filter = '') {
        const renderItems = (items) => {
            return items
                .filter(item => item.category_name?.toLowerCase().includes(filter) || item.type_name?.toLowerCase().includes(filter))
                .map(item => {
                    if (item.category_name) {
                        // Render parent category
                        return `<li class="parent">${item.category_name}</li>` +
                            (item.children ? renderItems(item.children).join('') : '');
                    }
                    if (item.type_name) {
                        // Render selectable type
                        return `<li data-id="${item.type_id}" class="child">${item.type_name}</li>`;
                    }
                    return '';
                });
        };

        categoryList.html(renderItems(categories).join(''));
    }

    // Filter categories on search
    searchBox.on('input', function () {
        const filter = searchBox.val().toLowerCase();
        fetch('/ozon/category-tree') // Fetch full list again
            .then(response => response.json())
            .then(data => {
                renderCategories(data, filter);
            });
    });

    // Handle category selection
    categoryList.on('click', '.child', function () {
        const selectedId = $(this).data('id');
        alert('Selected category ID: ' + selectedId); // Replace with your logic
    });
});
