SELECT DISTINCT shipping_province
FROM iwa_marketplace_orders_line_items
WHERE
    shipping_province IS NOT NULL
    AND shipping_province != ''
    AND shipping_province != 'null';