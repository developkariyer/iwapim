UPDATE iwa_marketplace_orders_line_items
SET product_price_usd = :productPriceUsd, total_price_usd = :totalPriceUsd, subtotal_price_usd = :subtotalPriceUsd
WHERE id = :id;