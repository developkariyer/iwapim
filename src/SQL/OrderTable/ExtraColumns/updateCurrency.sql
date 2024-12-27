UPDATE iwa_marketplace_orders_line_items
SET currency_rate = :currency_rate
WHERE currency = :currency AND DATE(created_at) = :created_date AND currency_rate IS NULL;