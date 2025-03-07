INSERT INTO iwa_marketplace_returns_line_items (
    marketplace_type, marketplace_id, iwasku, parent_identifier, product_type, variant_name, parent_name, created_at,
    return_id, order_id, variant_id, product_price_USD, quantity, variant_title, return_status, reason, main_reason,
    customer_first_name, customer_last_name, customer_email
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.item.iwasku')) AS iwasku,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.item.parent_identifier')) AS parent_identifier,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.item.product_type')) AS product_type,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.item.variantName')) AS variant_name,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.item.parentName')) AS parent_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.returnByDate')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.returnOrderId')) AS return_id,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.purchaseOrderId')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.item.sku')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.unitPrice.currencyAmount')) AS product_price_USD,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.refundedQty')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.item.productName')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.currentRefundStatus')) AS return_status,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.returnReason')) AS reason,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.returnDescription')) AS main_reason,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerName.firstName')) AS customer_first_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerName.lastName')) AS customer_last_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerName.customerEmailId')) AS customer_email
FROM
    iwa_marketplace_returns
    CROSS JOIN JSON_TABLE(json, '$.returnOrderLines[*]' COLUMNS ( value JSON PATH '$' )) AS return_item
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
    variant_title =    VALUES(variant_title),
    return_status = VALUES(return_status),
    reason = VALUES(reason),
    main_reason = VALUES(main_reason),
    customer_comment = VALUES(customer_comment),
    reason_code = VALUES(reason_code),
    customer_first_name = VALUES(customer_first_name),
    customer_last_name = VALUES(customer_last_name)