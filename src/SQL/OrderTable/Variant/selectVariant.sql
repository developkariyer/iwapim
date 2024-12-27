SELECT
    DISTINCT variant_id
FROM
    iwa_marketplace_orders_line_items
WHERE
    marketplace_type = :marketplaceType;