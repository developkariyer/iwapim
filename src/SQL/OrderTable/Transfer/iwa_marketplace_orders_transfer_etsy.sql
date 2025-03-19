INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title,
    total_discount, shipping_city, shipping_country_code, total_price, subtotal_price, fulfillments_status, tracking_company, customer_first_name,
    customer_last_name, customer_email)
SELECT
    :marketplaceType,
    :marketPlaceId,
    FROM_UNIXTIME(CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.created_timestamp')) AS UNSIGNED)) AS created_at,
    FROM_UNIXTIME(CAST(JSON_UNQUOTE(JSON_EXTRACT(json, '$.updated_timestamp')) AS UNSIGNED)) AS closed_at,
    order_id AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.listing_id')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.amount')) / JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.divisor')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.price.currency_code')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.title')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.discount_amt.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.discount_amt.divisor'))  AS total_discount,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.city')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.country_iso')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.grandtotal.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.grandtotal.divisor'))  AS total_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal.amount')) / JSON_UNQUOTE(JSON_EXTRACT(json, '$.subtotal.divisor'))  AS subtotal_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.status')) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(shipments.value, '$.carrier_name')) AS tracking_company,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.name')), ' ', LENGTH(JSON_UNQUOTE(JSON_EXTRACT(json, '$.name'))) -
    LENGTH(REPLACE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.name')), ' ', ''))) AS customer_first_name,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.name')), ' ', -1) AS customer_last_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.buyer_email')) AS customer_email
FROM
    iwa_marketplace_orders
        CROSS JOIN JSON_TABLE(json, '$.transactions[*]' COLUMNS (
                    value JSON PATH '$'
                )) AS line_item
        CROSS JOIN JSON_TABLE(json, '$.shipments[*]' COLUMNS (
                    value JSON PATH '$'
                )) AS shipments
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.product_id')) != ''
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
    shipping_city = VALUES(shipping_city),
    shipping_country_code = VALUES(shipping_country_code),
    total_price = VALUES(total_price),
    subtotal_price = VALUES(subtotal_price),
    fulfillments_status = VALUES(fulfillments_status),
    tracking_company = VALUES(tracking_company),
    customer_first_name = VALUES(customer_first_name),
    customer_last_name = VALUES(customer_last_name),
    customer_email = VALUES(customer_email);