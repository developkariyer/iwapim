INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, total_discount,
    shipping_country, shipping_province, shipping_city, shipping_company, shipping_country_code, total_price, subtotal_price,
    fulfillments_status, tracking_company, fulfillments_status_control, referring_site, landing_site, customer_first_name,
    customer_last_name, customer_email
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.createdAt')) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.closedAt')) AS closed_at,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.id')), '/', -1) AS order_id,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product.id')), '/', -1) AS product_id,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant.id')), '/', -1) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant.price')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.currencyCode')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.name')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.currentTotalDiscountsSet.shopMoney.amount')) AS total_discount,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingAddress.country')) AS shipping_country,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingAddress.province')) AS shipping_province,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingAddress.city')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingAddress.company')) AS shipping_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingAddress.countryCodeV2')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.currentTotalPriceSet.shopMoney.amount')) AS total_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.currentSubtotalPriceSet.shopMoney.amount')) AS subtotal_price,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.displayFulfillmentStatus')), NULL) AS fulfillments_status,
    NULL AS tracking_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.cancelledAt')) AS fulfillments_status_control,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.referrerUrl')) AS referring_site,
    COALESCE(LEFT(JSON_UNQUOTE(JSON_EXTRACT(json, '$.landingPageUrl')), 255), NULL) AS landing_site,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.customer.firstName')), '') AS customer_first_name,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.customer.lastName')), '') AS customer_last_name,
    COALESCE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.email')), '') AS customer_email
FROM
    iwa_marketplace_orders
    CROSS JOIN JSON_TABLE(json, '$.lineItems.nodes[*]' COLUMNS ( value JSON PATH '$' )) AS line_item
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant.id')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant.id')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.variant.id')) != ''
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
     landing_site = VALUES(landing_site),
     customer_first_name = VALUES(customer_first_name),
     customer_last_name = VALUES(customer_last_name),
     customer_email = VALUES(customer_email);