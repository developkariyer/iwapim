INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, total_discount,
    shipping_country, shipping_province, shipping_city, shipping_company, shipping_country_code, total_price, subtotal_price,
    fulfillments_status, tracking_company, fulfillments_status_control, referring_site, landing_site
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.created_at')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.closed_at')) AS closed_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.id')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.currency')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.name')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.current_total_discounts')) AS total_discount,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.country')) AS shipping_country,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.province')) AS shipping_province,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.city')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.company')) AS shipping_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shipping_address.country_code')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.current_total_price')) AS total_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.current_subtotal_price')) AS subtotal_price,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.status')), NULL) AS fulfillments_status,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(fulfillments.value, '$.tracking_company')), NULL) AS tracking_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.cancelled_at')) AS fulfillments_status_control,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.referring_site')) AS referring_site,
    COALESCE(LEFT(JSON_UNQUOTE(JSON_EXTRACT(json, '$.landing_site')), 255), NULL) AS landing_site
FROM
    iwa_marketplace_orders
        CROSS JOIN JSON_TABLE(json, '$.line_items[*]' COLUMNS ( value JSON PATH '$' )) AS line_item
        LEFT JOIN JSON_TABLE(json, '$.fulfillments[*]' COLUMNS ( value JSON PATH '$' )) AS fulfillments ON TRUE
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) != ''
    AND CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant_id')) AS UNSIGNED) > 0
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
    shipping_country = VALUES(shipping_country),
    shipping_province = VALUES(shipping_province),
    shipping_city = VALUES(shipping_city),
    shipping_company = VALUES(shipping_company),
    shipping_country_code = VALUES(shipping_country_code),
    total_price = VALUES(total_price),
    subtotal_price = VALUES(subtotal_price),
    fulfillments_status = VALUES(fulfillments_status),
    tracking_company = VALUES(tracking_company),
    fulfillments_status_control = VALUES(fulfillments_status_control),
    referring_site = VALUES(referring_site),
    landing_site = VALUES(landing_site);