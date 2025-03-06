INSERT INTO iwa_marketplace_returns_line_items (
    marketplace_type, marketplace_id, iwasku, parent_identifier, product_type, variant_name, parent_name, created_at,
    return_id, order_id, variant_id, product_price_USD,	total_price_USD, quantity, variant_title, shipping_country,
    shipping_country_code, return_status, reason, main_reason, customer_comment
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.iwasku')) AS iwasku,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.parent_identifier')) AS parent_identifier,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.product_type')) AS product_type,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.variant_name')) AS variant_name,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.parent_name')) AS parent_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.registrationDateTime')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.returnId')) AS return_id,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderId')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.ean')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.product_price_usd')) AS product_price_USD,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.total_price_usd')) AS total_price_USD,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.expectedQuantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.variant_title')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.shipping_country')) AS shipping_country,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderDetail.shipping_country_code')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(processing_result.value, '$.processingResult')) AS return_status,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.returnReason.detailedReason')) AS reason,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.returnReason.mainReason')) AS main_reason,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.returnReason.customerComments')) AS customer_comment
FROM
    iwa_marketplace_returns
    CROSS JOIN JSON_TABLE(json, '$.returnItems[*]' COLUMNS ( value JSON PATH '$' )) AS return_item
    CROSS JOIN JSON_TABLE(return_item.value, '$.processingResults[*]' COLUMNS ( value JSON PATH '$' )) AS processing_result
WHERE
    marketplace_id = :marketPlaceId
ON DUPLICATE KEY UPDATE
    marketplace_type = VALUES(marketplace_type),
    marketplace_id = VALUES(marketplace_id),
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
    variant_title = VALUES(variant_title),
    shipping_country = VALUES(shipping_country),
    shipping_country_code = VALUES(shipping_country_code),
    return_status = VALUES(return_status),
    reason = VALUES(reason),
    main_reason = VALUES(main_reason),
    customer_comment = VALUES(customer_comment)