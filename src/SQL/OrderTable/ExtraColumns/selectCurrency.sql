SELECT DISTINCT currency, DATE(created_at) as created_date
FROM iwa_marketplace_orders_line_items
WHERE currency is not null  AND currency_rate=0.00