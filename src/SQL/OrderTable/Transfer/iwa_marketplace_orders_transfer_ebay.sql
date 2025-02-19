INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, order_id, product_id, variant_id, price, currency, quantity, variant_title,
    shipping_province, shipping_city, shipping_country_code, total_price, subtotal_price,
    fulfillments_status, fulfillments_status_control
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.creationDate')), NULL) AS created_at,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderId')), NULL) AS order_id,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.legacyItemId')), NULL) AS product_id,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.legacyVariationId')), NULL) AS variant_id,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.lineItemCost.value')), NULL) AS price,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.lineItemCost.currency')), NULL) AS currency,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')), NULL) AS quantity,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.title')), NULL) AS variant_title,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.buyer.buyerRegistrationAddress.contactAddress.stateOrProvince')), NULL) AS shipping_province,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.buyer.buyerRegistrationAddress.contactAddress.city')), NULL) AS shipping_city,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.buyer.buyerRegistrationAddress.contactAddress.countryCode')), NULL) AS shipping_country_code,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.pricingSummary.total.value')), NULL) AS total_price,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.pricingSummary.priceSubtotal.value')), NULL) AS subtotal_price,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderFulfillmentStatus')), NULL) AS fulfillments_status,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.cancelStatus.cancelState')), NULL) AS fulfillments_status_control
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
     fulfillments_status_control = VALUES(fulfillments_status_control)
