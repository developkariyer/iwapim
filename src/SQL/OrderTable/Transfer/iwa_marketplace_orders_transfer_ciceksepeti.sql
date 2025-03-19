INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title, total_discount,
    shipping_city, shipping_country_code, shipping_company, total_price, fulfillments_status, tracking_company, fulfillments_status_control, customer_first_name,
    customer_last_name, customer_email
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    STR_TO_DATE(CONCAT(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderCreateDate')), ' ', JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderCreateTime'))),'%d/%m/%Y %H:%i:%s'
    ) AS created_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderModifyDate')) AS closed_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderId')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.productId')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.itemPrice')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.currency')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.quantity')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.name')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.discount')) AS total_discount,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverCity')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverCountryCode')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverCompanyName')) AS shipping_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.totalPrice')) AS total_price,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderProductStatus')) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.cargoCompany')) AS tracking_company,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.cancellationResult')) AS fulfillments_status_control,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverName')), ' ', LENGTH(JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverName'))) -
    LENGTH(REPLACE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverName')), ' ', ''))) AS customer_first_name,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.receiverName')), ' ', -1) AS customer_last_name,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.invoiceEmail')) AS customer_email
FROM
    iwa_marketplace_orders
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(json, '$.productCode')) != ''
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
    shipping_company = VALUES(shipping_company),
    shipping_country_code = VALUES(shipping_country_code),
    total_price = VALUES(total_price),
    subtotal_price = VALUES(subtotal_price),
    fulfillments_status = VALUES(fulfillments_status),
    tracking_company = VALUES(tracking_company),
    fulfillments_status_control = VALUES(fulfillments_status_control),
    customer_first_name = VALUES(customer_first_name),
    customer_last_name = VALUES(customer_last_name),
    customer_email = VALUES(customer_email);