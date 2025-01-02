SELECT COALESCE(
               DATE_FORMAT(MAX(STR_TO_DATE(JSON_UNQUOTE(JSON_EXTRACT(json, '$.orderModifyDate')), '%d/%m/%Y')), '%Y-%m-%d'),
               DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), '%Y-%m-%d')
       ) AS lastUpdatedAt
FROM iwa_marketplace_orders
WHERE marketplace_id = :marketplace_id;