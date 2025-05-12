INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, total_discount,
    shipping_city, shipping_company, shipping_country_code,total_price, subtotal_price, fulfillments_status, tracking_company, customer_first_name,
    customer_last_name, customer_email)
SELECT
    :marketplaceType,
    :marketPlaceId,
    FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDate')) / 1000) AS created_at,
    FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.lastModifiedDate')) / 1000) AS closed_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderNumber')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.id')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.currencyCode')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productName')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalDiscount')) AS total_discount,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipmentAddress.city')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.commercial')) AS shipping_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipmentAddress.countryCode')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalPrice')) AS total_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalPrice')) AS subtotal_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.status')) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.cargoProviderName')) AS tracking_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerFirstName')) AS customer_first_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerLastName')) AS customer_last_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerEmail')) AS customer_email
FROM
    iwa_marketplace_orders
    CROSS JOIN JSON_TABLE(json, '$.lines[*]' COLUMNS (
                value JSON PATH '$'
            )) AS line_item
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) != ''
    AND CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.productCode')) AS UNSIGNED) > 0
    AND marketplace_id = :marketPlaceId
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
    shipping_company = VALUES(shipping_company),
    shipping_country_code = VALUES(shipping_country_code),
    total_price = VALUES(total_price),
    subtotal_price = VALUES(subtotal_price),
    fulfillments_status = VALUES(fulfillments_status),
    tracking_company = VALUES(tracking_company),
    customer_first_name = VALUES(customer_first_name),
    customer_last_name = VALUES(customer_last_name),
    customer_email = VALUES(customer_email);