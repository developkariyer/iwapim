INSERT INTO iwa_marketplace_returns_line_items (
    marketplace_type, marketplace_id, marketplace_key, iwasku, parent_identifier, product_type, variant_name, parent_name, created_at,
    return_id, order_id, variant_id, product_price_USD, total_price_USD, quantity, variant_title, return_status, customer_first_name, customer_last_name
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.marketplace_key')) AS marketplace_key
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.iwasku')) AS iwasku,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.parent_identifier')) AS parent_identifier,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.product_type')) AS product_type,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.variant_name')) AS variant_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.parent_name')) AS parent_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.created_at')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.id')) AS return_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.order_id')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.json.tsin')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.product_price_usd')) AS product_price_USD,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.total_price_usd')) AS total_price_USD,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.variant_title')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.json.sale_status')) AS return_status,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.json.customer')), ' ', 1) AS customer_first_name,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.json.customer')), ' ', -1) AS customer_last_name
FROM
    iwa_marketplace_returns
WHERE
    marketplace_id = :marketPlaceId
ON DUPLICATE KEY UPDATE
    marketplace_type = VALUES(marketplace_type),
    marketplace_id = VALUES(marketplace_id),
    marketplace_key = VALUES(marketplace_key),
    iwasku = VALUES(iwasku),
    parent_identifier = VALUES(parent_identifier),
    product_type = VALUES(product_type),
    variant_name = VALUES(variant_name),
    parent_name = VALUES(parent_name),
    created_at = VALUES(created_at),
    return_id = VALUES(return_id),
    order_id = VALUES(order_id),
    variant_id = VALUES(variant_id),
    product_price_USD = VALUES(product_price_USD),
    total_price_USD = VALUES(total_price_USD),
    quantity = VALUES(quantity),
    variant_title =    VALUES(variant_title),
    return_status = VALUES(return_status),
    customer_first_name = VALUES(customer_first_name),
    customer_last_name = VALUES(customer_last_name)