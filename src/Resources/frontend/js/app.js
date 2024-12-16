import '../css/app.css'; // Link the CSS file
import $ from 'jquery';

$(document).ready(function () {
    const searchBox = $('#search-box');
    const categoryList = $('#category-list');

    // Fetch categories from backend
    fetch('/ozon/category-tree')
        .then(response => response.json())
        .then(data => renderCategories(data));

    // Recursive function to render the category list
    function renderCategories(categories, filter = '') {
        const renderItems = (items) => {
            return items
                .filter(item =>
                    item.category_name?.toLowerCase().includes(filter) ||
                    item.type_name?.toLowerCase().includes(filter)
                )
                .map(item => {
                    if (item.category_name) {
                        return `<li class="parent">${item.category_name}</li>` +
                            (item.children ? renderItems(item.children).join('') : '');
                    }
                    if (item.type_name) {
                        return `<li data-id="${item.type_id}" class="child">${item.type_name}</li>`;
                    }
                }).join('');
        };

        categoryList.html(renderItems(categories));
    }

    // Filter logic
    searchBox.on('input', function () {
        const filter = searchBox.val().toLowerCase();
        fetch('/ozon/category-tree')
            .then(response => response.json())
            .then(data => renderCategories(data, filter));
    });

    // Handle selection
    categoryList.on('click', '.child', function () {
        alert(`Selected ID: ${$(this).data('id')}`);
    });
});
