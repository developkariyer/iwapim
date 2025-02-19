INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, order_id, product_id, variant_id, price, currency, quantity, variant_title,
    shipping_province, shipping_city, shipping_country_code, total_price, subtotal_price,
    fulfillments_status, fulfillments_status_control
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.creationDate')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderId')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.legacyItemId')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.legacyVariationId')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.lineItemCost.value')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.lineItemCost.currency')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.title')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.buyer.buyerRegistrationAddress.contactAddress.stateOrProvince')) AS shipping_province,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.buyer.buyerRegistrationAddress.contactAddress.city')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.buyer.buyerRegistrationAddress.contactAddress.countryCode')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.pricingSummary.total.value')) AS total_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.pricingSummary.priceSubtotal.value')) AS subtotal_price,
    JSON_UNQUOTE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderFulfillmentStatus')), NULL) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.cancelStatus.cancelState')) AS fulfillments_status_control
FROM
    iwa_marketplace_orders
        CROSS JOIN JSON_TABLE(json, '$.lineItems[*]' COLUMNS ( value JSON PATH '$' )) AS line_item
WHERE
     JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.legacyVariationId')) IS NOT NULL
     AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.legacyVariationId')) != 'null'
     AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.legacyVariationId')) != ''
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
     shipping_province = VALUES(shipping_province),
     shipping_city = VALUES(shipping_city),
     shipping_company = VALUES(shipping_company),
     shipping_country_code = VALUES(shipping_country_code),
     total_price = VALUES(total_price),
     subtotal_price = VALUES(subtotal_price),
     fulfillments_status = VALUES(fulfillments_status),
     fulfillments_status_control = VALUES(fulfillments_status_control),
