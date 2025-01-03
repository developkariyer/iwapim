SELECT COALESCE(DATE_FORMAT(MAX(json_extract(json, '$.orderPlacedDateTime')), '%Y-%m-%d'), DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 MONTH), '%Y-%m-%d')) as lastUpdatedAt
FROM iwa_marketplace_orders
WHERE marketplace_id = :marketplace_id;