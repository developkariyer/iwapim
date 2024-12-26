UPDATE iwa_marketplace_orders_line_items
SET completion_day = DATEDIFF(DATE(closed_at), DATE(created_at))
WHERE DATE(closed_at) IS NOT NULL;