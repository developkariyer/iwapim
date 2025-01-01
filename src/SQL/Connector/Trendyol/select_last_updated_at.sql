SELECT COALESCE(DATE_FORMAT(FROM_UNIXTIME(MAX(json_extract(json, '$.lastModifiedDate') / 1000)), '%Y-%m-%d'),DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH), '%Y-%m-%d')) AS lastUpdatedAt
FROM iwa_marketplace_orders
WHERE marketplace_id = :marketplace_id
LIMIT 1;