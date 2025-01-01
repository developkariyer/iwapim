SELECT COALESCE(DATE_FORMAT(FROM_UNIXTIME(MAX(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderLines.orderLine[0].statusDate')) / 1000)), '%Y-%m-%d'),DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 180 DAY), '%Y-%m-%d')) AS lastUpdatedAt
FROM iwa_marketplace_orders
WHERE marketplace_id = :marketplace_id
LIMIT 1