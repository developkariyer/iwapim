UPDATE iwa_marketplace_orders_line_items
SET shipping_country = :shipping_country
WHERE shipping_country_code = :shipping_country_code