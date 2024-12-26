UPDATE iwa_marketplace_orders_line_items
SET has_discount =
CASE
    WHEN total_discount IS NOT NULL AND total_discount <> 0.00 THEN TRUE
    ELSE FALSE
END;