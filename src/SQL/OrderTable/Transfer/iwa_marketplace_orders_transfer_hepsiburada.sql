INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, total_discount,
    shipping_city, shipping_country_code, total_price, fulfillments_status, tracking_company
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.detail.createdDate')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.DeliveredDate')) AS closed_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.OrderNumber')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.barcode')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.sku')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.unitPrice.amount')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.unitPrice.currency')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.name')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.hbDiscount.totalPrice.amount')) AS total_discount,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.shippingAddress.city')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.shippingAddress.countryCode')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.totalPrice')) AS total_price,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.status')) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.cargoCompanyModel.name')) AS tracking_company
FROM
    iwa_marketplace_orders
    CROSS JOIN JSON_TABLE(json, '$.detail.items[*]' COLUMNS ( value JSON PATH '$' )) AS line_item
WHERE
    marketplace_id = :marketPlaceId
ON DUPLICATE KEY UPDATE
    marketplace_type = VALUES(marketplace_type),
    marketplace_id = VALUES(marketplace_id),
    created_at = VALUES(created_at),
    closed_at = VALUES(closed_at),
    product_id = VALUES(product_id),
    variant_id = VALUES(variant_id),
    price = VALUES(price),
    currency = VALUES(currency),
    quantity = VALUES(quantity),
    variant_title = VALUES(variant_title),
    total_discount = VALUES(total_discount),
    shipping_city = VALUES(shipping_city),
    shipping_country_code = VALUES(shipping_country_code),
    total_price = VALUES(total_price),
    fulfillments_status = VALUES(fulfillments_status),
    tracking_company = VALUES(tracking_company),