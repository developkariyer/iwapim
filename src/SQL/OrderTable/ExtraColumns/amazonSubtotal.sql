UPDATE
    iwa_marketplace_orders_line_items
    JOIN (
    SELECT
    order_id,
    SUM(price) - SUM(total_discount) AS pnet
    FROM
    iwa_marketplace_orders_line_items
    WHERE
    marketplace_type = 'Amazon'
    GROUP BY
    order_id
    ) AS calculated_pnet
ON
    iwa_marketplace_orders_line_items.order_id = calculated_pnet.order_id
    SET
        iwa_marketplace_orders_line_items.subtotal_price = calculated_pnet.pnet;