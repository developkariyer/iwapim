INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title,
    total_discount, shipping_city, shipping_country_code, province_code, total_price, fulfillments_status,tracking_company, customer_email)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.PurchaseDate')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.LastUpdateDate')) AS closed_at,
    order_id AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.OrderItemId')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ASIN')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ItemPrice.Amount')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.OrderTotal.CurrencyCode')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.QuantityOrdered')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.Title')) AS variant_title,
    (CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.PromotionDiscount.Amount')) AS DECIMAL(10,2)) +
     CAST(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.PromotionDiscountTax.Amount')) AS DECIMAL(10,2))) AS total_discount,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.ShippingAddress.City')) as shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.ShippingAddress.CountryCode')) AS shipping_country_code,
    CONCAT(
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.ShippingAddress.CountryCode')),
            '-',
            JSON_UNQUOTE(JSON_EXTRACT(json, '$.ShippingAddress.StateOrRegion'))
    ) AS province_code,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.OrderTotal.Amount')) AS total_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.OrderStatus')) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.FulfillmentChannel')) AS tracking_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.BuyerInfo.BuyerEmail')) AS customer_email
FROM
    iwa_marketplace_orders
    CROSS JOIN JSON_TABLE(json, '$.OrderItems[*]' COLUMNS (value JSON PATH '$')) AS line_item
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ASIN')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ASIN')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.ASIN')) != ''
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
     shipping_country_code = VALUES(shipping_country_code),
     total_price = VALUES(total_price),
     fulfillments_status = VALUES(fulfillments_status),
     province_code = VALUES(province_code),
     tracking_company = VALUES(tracking_company),
     customer_email = VALUES(customer_email);