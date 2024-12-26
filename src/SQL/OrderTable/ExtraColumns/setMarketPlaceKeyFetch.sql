SELECT
    DISTINCT marketplace_id
FROM
    iwa_marketplace_orders_line_items
WHERE
    marketplace_id IS NOT NULL