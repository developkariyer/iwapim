SELECT
    DISTINCT marketplace_id
FROM
    iwa_marketplace_returns_line_items
WHERE
    marketplace_id IS NOT NULL