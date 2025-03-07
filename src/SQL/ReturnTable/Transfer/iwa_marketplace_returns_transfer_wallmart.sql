INSERT INTO iwa_marketplace_returns_line_items (
    marketplace_type, marketplace_id, iwasku, parent_identifier, product_type, variant_name, parent_name, created_at,
    return_id, order_id, variant_id, variant_title, return_status, reason, main_reason, customer_comment, reason_code,
    customer_first_name, customer_last_name
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.item.iwasku')) AS iwasku,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderLine.parent_identifier')) AS parent_identifier,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderLine.product_type')) AS product_type,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderLine.variant_name')) AS variant_name,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderLine.parent_name')) AS parent_name,
    FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.claimDate')) / 1000) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.id')) AS return_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderNumber')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderLine.barcode')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(return_item.value, '$.orderLine.variant_name')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(status.value, '$.claimItemStatus.name')) AS return_status,
    JSON_UNQUOTE(JSON_EXTRACT(status.value, '$.trendyolClaimItemReason.name')) AS reason,
    JSON_UNQUOTE(JSON_EXTRACT(status.value, '$.trendyolClaimItemReason.externalReasonId')) AS main_reason,
    JSON_UNQUOTE(JSON_EXTRACT(status.value, '$.customerClaimItemReason.name')) AS customer_comment,
    JSON_UNQUOTE(JSON_EXTRACT(status.value, '$.trendyolClaimItemReason.code')) AS reason_code,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerFirstName')) AS customer_first_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerLastName')) AS customer_last_name
FROM
    iwa_marketplace_returns
        CROSS JOIN JSON_TABLE(json, '$.returnOrderLines[*]' COLUMNS ( value JSON PATH '$' )) AS return_item
        CROSS JOIN JSON_TABLE(return_item.value, '$.claimItems[*]' COLUMNS ( value JSON PATH '$' )) AS status
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