import '../css/app.css'; // Include custom CSS
import $ from 'jquery';

$(document).ready(function () {
    const searchBox = $('#search-box');
    const categoryList = $('#category-list');
    let cachedData = []; // To store the JSON data in memory

    // Fetch categories once on page load
    fetch('/ozon/category-tree')
        .then(response => response.json())
        .then(data => {
            cachedData = data; // Cache the data
            renderCategories(cachedData); // Render initially
        })
        .catch(err => {
            console.error('Failed to load categories:', err);
        });

    // Render categories into the list
    function renderCategories(categories, filter = '') {
        // Recursive rendering function
        const renderItems = (items) => {
            return items
                .filter(item =>
                    // Filter by category_name or type_name
                    item.category_name?.toLowerCase().includes(filter) ||
                    item.type_name?.toLowerCase().includes(filter)
                )
                .map(item => {
                    if (item.category_name) {
                        // Render parent category (unselectable)
                        return `
                            <li class="parent">
                                ${item.category_name}
                                <ul>${renderItems(item.children || []).join('')}</ul>
                            </li>
                        `;
                    }
                    if (item.type_name) {
                        // Render selectable type
                        return `
                            <li class="child" data-id="${item.type_id}">
                                ${item.type_name}
                            </li>
                        `;
                    }
                    return '';
                });
        };

        // Clear and re-render the list
        categoryList.html(renderItems(categories).join(''));
    }

    // Filter categories locally on search input
    searchBox.on('input', function () {
        const filter = searchBox.val().toLowerCase();
        renderCategories(cachedData, filter); // Filter and re-render
    });

    // Handle category selection
    categoryList.on('click', '.child', function () {
        const selectedId = $(this).data('id');
        alert(`Selected category ID: ${selectedId}`); // Replace with your logic
    });
});
