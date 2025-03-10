INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, order_id, product_id, variant_id, price, currency, quantity,
    shipping_city, shipping_province, fulfillments_status, tracking_company)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.poDate')) AS created_at,
    order_id AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.partNumber')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.partNumber')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price')) AS price,
    'USD' AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerCity')) as shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerState')) AS shipping_province,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.isCancelled')) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.carrierCode')) AS tracking_company
FROM
    iwa_marketplace_orders
        CROSS JOIN JSON_TABLE(json, '$.products[*]' COLUMNS (value JSON PATH '$')) AS line_item
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.partNumber')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.partNumber')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.partNumber')) != ''
    AND marketplace_id = :marketPlaceId
ON DUPLICATE KEY UPDATE
    marketplace_type = VALUES(marketplace_type),
    marketplace_id = VALUES(marketplace_id),
    created_at = VALUES(created_at),
    product_id = VALUES(product_id),
    variant_id = VALUES(variant_id),
    price = VALUES(price),
    quantity = VALUES(quantity),
    shipping_city = VALUES(shipping_city),
    shipping_province = VALUES(shipping_province),
    fulfillments_status = VALUES(fulfillments_status),
    tracking_company = VALUES(tracking_company);