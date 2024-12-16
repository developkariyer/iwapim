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
        const renderItems = (items) => {
            return items.map(item => {
                if (item.category_name) {
                    // Parent category (unselectable)
                    return `
                        <li class="parent" data-filtered="true">
                            ${item.category_name}
                            <ul>${renderItems(item.children || []).join('')}</ul>
                        </li>
                    `;
                }
                if (item.type_name) {
                    // Selectable type
                    return `
                        <li class="child" data-filtered="true" data-id="${item.type_id}">
                            ${item.type_name}
                        </li>
                    `;
                }
                return '';
            }).join('');
        };

        categoryList.html(renderItems(categories));
    }

    // Filter categories on search input
    searchBox.on('input', function () {
        const filter = searchBox.val().toLowerCase();
        filterCategories(filter);
    });

    // Update visibility without deleting DOM nodes
    function filterCategories(filter) {
        categoryList.find('li').each(function () {
            const text = $(this).text().toLowerCase();
            const isVisible = text.includes(filter);

            if (isVisible) {
                $(this).show();
                $(this).attr('data-filtered', 'true');
            } else {
                $(this).hide();
                $(this).attr('data-filtered', 'false');
            }
        });
    }

    // Handle category selection
    categoryList.on('click', '.child', function () {
        if ($(this).attr('data-filtered') === 'true') {
            const selectedId = $(this).data('id');
            alert(`Selected category ID: ${selectedId}`);
        }
    });
});
