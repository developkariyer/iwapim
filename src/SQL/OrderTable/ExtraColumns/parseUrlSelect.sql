SELECT DISTINCT referring_site
FROM iwa_marketplace_orders_line_items
WHERE
    referring_site IS NOT NULL
    AND referring_site != ''
    AND referring_site != 'null';