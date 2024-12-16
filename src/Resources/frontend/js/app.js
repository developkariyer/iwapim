import '../css/app.css'; // Include custom CSS
import $ from 'jquery';

$(document).ready(function () {
    const searchBox = $('#search-box');
    const categoryList = $('#category-list');
    let cachedData = []; // To store the JSON data in memory

    // Fetch categories once on page load
    fetch('/ozon/category-tree', { cache: 'no-store' })
        .then(response => response.json())
        .then(data => {
            cachedData = data; // Cache the data
            renderCategories(cachedData); // Render initially
        })
        .catch(err => {
            console.error('Failed to load categories:', err);
        });

    // Render categories into the list
    function renderCategories(categories) {
        categoryList.html(renderItems(categories)); // Render items and populate the list
    }

    // Recursive function to render items
    function renderItems(items) {
        if (!Array.isArray(items)) {
            return ''; // If items is not an array, return an empty string
        }

        return items
            .map(item => {
                if (item.category_name) {
                    // Render parent category (unselectable)
                    return `
                        <li class="parent">
                            ${item.category_name}
                            <ul>${renderItems(item.children || [])}</ul>
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
                return ''; // Fallback for unexpected data
            })
            .filter(Boolean) // Remove undefined or null values
            .join(''); // Join valid strings to build the HTML
    }

    // Filter categories on search input
    searchBox.on('input', function () {
        const filter = searchBox.val().toLowerCase();
        const filteredData = filterCategories(cachedData, filter); // Filter the cached data
        renderCategories(filteredData);
    });

    // Recursive function to filter categories and their children
    function filterCategories(categories, filter) {
        return categories
            .map(category => {
                let match = false;

                // Check if the parent matches the filter
                if (category.category_name && category.category_name.toLowerCase().includes(filter)) {
                    match = true;
                }

                // Check if children match the filter
                const filteredChildren = category.children ? filterCategories(category.children, filter) : [];

                if (filteredChildren.length > 0) {
                    match = true; // Include parent if children match
                }

                // Return matched parents and children
                if (match) {
                    return {
                        ...category,
                        children: filteredChildren,
                    };
                }

                return null; // Exclude non-matching categories
            })
            .filter(Boolean); // Remove null values
    }

    // Handle category selection using event delegation
    categoryList.on('click', '.child', function () {
        const selectedId = $(this).data('id');
        alert(`Selected category ID: ${selectedId}`); // Replace with your logic
    });
});
