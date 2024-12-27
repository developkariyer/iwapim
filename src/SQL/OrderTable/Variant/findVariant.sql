SELECT object_id
FROM iwa_json_store
WHERE
    field_name = 'apiResponseJson'  AND JSON_UNQUOTE(JSON_EXTRACT(json_data, :jsonPath)) = :uniqueId LIMIT 1;