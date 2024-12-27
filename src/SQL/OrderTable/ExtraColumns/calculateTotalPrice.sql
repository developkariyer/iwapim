UPDATE iwa_marketplace_orders_line_items AS t1
    INNER JOIN (
    SELECT
    order_id,
    SUM(price) AS total_price
    FROM iwa_marketplace_orders_line_items
    WHERE marketplace_type = :marketplaceType
    GROUP BY order_id
    ) AS t2
ON t1.order_id = t2.order_id
    SET t1.total_price = t2.total_price
WHERE t1.marketplace_type = :marketplaceType;