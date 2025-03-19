INSERT INTO iwa_marketplace_orders_line_items (
    marketplace_type, marketplace_id, created_at, closed_at, order_id, product_id, variant_id, price, currency, quantity, variant_title,
    shipping_province, shipping_city, shipping_country_code, fulfillments_status, tracking_company, fulfillments_status_control, customer_first_name,
    customer_last_name, customer_email
)
SELECT
    :marketplaceType,
    :marketPlaceId,
    FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderDate')) / 1000) AS created_at,
    FROM_UNIXTIME(JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.statusDate')) / 1000)AS closed_at,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.purchaseOrderId')) AS order_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) AS product_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) AS variant_id,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.charges.charge[0].chargeAmount.amount')) AS price,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.charges.charge[0].chargeAmount.currency')) AS currency,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.orderLineQuantity.amount')) AS quantity,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.productName')) AS variant_title,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.state')) AS shipping_province,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.city')) AS shipping_city,
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.country')) AS shipping_country_code,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.orderLineStatuses.orderLineStatus[0].status')) AS fulfillments_status,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.orderLineStatuses.orderLineStatus[0].trackingInfo.carrierName.carrier')) AS tracking_company,
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.orderLineStatuses.orderLineStatus[0].cancellationReason')) AS fulfillments_status_control,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.name')), ' ', LENGTH(JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.name'))) -
    LENGTH(REPLACE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.name')), ' ', ''))) AS customer_first_name,
    SUBSTRING_INDEX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.shippingInfo.postalAddress.name')), ' ', -1) AS customer_last_name
    JSON_UNQUOTE(JSON_EXTRACT(json, '$.customerEmailId')) AS customer_email
FROM
    iwa_marketplace_orders
        CROSS JOIN JSON_TABLE(json, '$.orderLines.orderLine[*]' COLUMNS ( value JSON PATH '$' )) AS line_item
WHERE
    JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) IS NOT NULL
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) != 'null'
    AND JSON_UNQUOTE(JSON_EXTRACT(line_item.value, '$.item.sku')) != ''
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
    shipping_country_code = VALUES(shipping_country_code),
    fulfillments_status = VALUES(fulfillments_status),
    tracking_company = VALUES(tracking_company),
    fulfillments_status_control = VALUES(fulfillments_status_control),
    customer_first_name = VALUES(customer_first_name),
    customer_last_name = VALUES(customer_last_name),
    customer_email = VALUES(customer_email);