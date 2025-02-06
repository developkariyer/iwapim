SELECT COALESCE(MAX(json_extract(json, '$.updated_at')), '2000-01-01T00:00:00Z') AS lastUpdatedAt
FROM iwa_marketplace_orders
WHERE marketplace_id = :marketplace_id;