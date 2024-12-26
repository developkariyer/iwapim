UPDATE iwa_marketplace_orders_line_items
SET marketplace_key = :marketplaceKey
WHERE marketplace_id = :marketplaceId;