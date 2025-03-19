INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity,
    variant_title,  shipping_city, shipping_country_code, fulfillments_status, fulfillments_status_control, customer_first_name,
    customer_last_name, customer_email
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderPlacedDateTime')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderPlacedDateTime')) AS closed_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderId')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.ean')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.bolProductId')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.unitPrice')) AS price,
    'EUR' AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.product.title')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.city')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.countryCode')) AS shipping_country_code,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(order_item.value, '$.fulfilmentStatus')), NULL) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(order_item_detail.value, '$.cancellationRequest')) AS fulfillments_status_control,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.firstName')) AS customer_first_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.surname')) AS customer_last_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDetail.shipmentDetails.email')) AS customer_email
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
    fulfillments_status_control = VALUES(fulfillments_status_control),
    customer_first_name = VALUES(customer_first_name),
    customer_last_name = VALUES(customer_last_name),
    customer_email = VALUES(customer_email);