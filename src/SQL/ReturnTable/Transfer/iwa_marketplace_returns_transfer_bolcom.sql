INSERT INTO iwa_marketplace_returns_line_items (
    marketplace_type, marketplace_id, iwasku, parent_identifier, product_type,
)
SELECT
    :marketplaceType,
    :marketPlaceId,

FROM
    iwa_marketplace_orders
        CROSS JOIN JSON_TABLE(json, '$.orderItems[*]' COLUMNS ( value JSON PATH '$' )) AS order_item
        CROSS JOIN JSON_TABLE(json, '$.orderDetail.orderItems[*]' COLUMNS ( value JSON PATH '$' )) AS order_item_detail
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) IS NOT NULL
  AND JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) != ''
    AND CAST(JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) AS UNSIGNED) > 0
    AND marketplace_id = :marketPlaceId
ON DUPLICATE KEY UPDATE
                     marketplace_type = VALUES(marketplace_type),
                     marketplace_id = VALUES(marketplace_id),
                     created_at = VALUES(created_at),
                     closed_at = VALUES(closed_at),
                     product_id = VALUES(product_id),
                     variant_id = VALUES(variant_id),
                     price = VALUES(price),
                     quantity = VALUES(quantity),
                     variant_title = VALUES(variant_title),
                     shipping_city = VALUES(shipping_city),
                     shipping_country_code = VALUES(shipping_country_code),
                     fulfillments_status = VALUES(fulfillments_status),
                     fulfillments_status_control = VALUES(fulfillments_status_control);