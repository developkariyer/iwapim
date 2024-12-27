SELECT id, currency, price, total_price, subtotal_price, DATE(created_at) as created_date
FROM iwa_marketplace_orders_line_items
WHERE (product_price_usd IS NULL OR total_price_usd IS NULL OR total_price_usd = 0) AND currency IS NOT NULL;