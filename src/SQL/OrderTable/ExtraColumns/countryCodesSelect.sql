SELECT DISTINCT shipping_country_code
FROM iwa_marketplace_orders_line_items
WHERE
    shipping_country_code IS NOT NULL
    AND shipping_country_code != ''
    AND shipping_country_code != 'null'