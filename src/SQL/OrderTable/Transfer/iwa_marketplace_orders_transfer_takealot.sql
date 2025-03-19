INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, order_id, product_id, variant_id, price,
    currency, quantity, variant_title, fulfillments_status, tracking_company, customer_first_name,
    customer_last_name
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    DATE_FORMAT(STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.order_date')), '%d %b %Y %H:%i:%s'), '%Y-%m-%d %H:%i:%s') AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.order_id')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.order_item_id')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.selling_price')) AS price,
    'ZAR' AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.product_title')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.sale_status')) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.dc')) AS tracking_company,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.customer')), ' ', LENGTH(JSON_UNQUOTE(JSON_EXTRACT(json, '$.customer'))) -
                                                                     LENGTH(REPLACE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.customer')), ' ', ''))) AS customer_first_name,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.customer')), ' ', -1) AS customer_last_name
FROM
    iwa_marketplace_orders
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) != ''
    AND CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.tsin')) AS UNSIGNED) > 0
    AND marketplace_id = :marketPlaceId
ON DUPLICATE KEY UPDATE
    marketplace_type = VALUES(marketplace_type),
    marketplace_id = VALUES(marketplace_id),
    created_at = VALUES(created_at),
    product_id = VALUES(product_id),
    variant_id = VALUES(variant_id),
    price = VALUES(price),
    currency = VALUES(currency),
    quantity = VALUES(quantity),
    variant_title = VALUES(variant_title),
    fulfillments_status = VALUES(fulfillments_status),
    tracking_company = VALUES(tracking_company)
    customer_first_name = VALUES(customer_first_name),
    customer_last_name = VALUES(customer_last_name);