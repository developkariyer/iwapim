UPDATE iwa_marketplace_orders_line_items
SET iwasku = :iwasku, parent_identifier  = :identifier, product_type = :productType, variant_name = :variantName, parent_name = :parentName
WHERE variant_id = :uniqueMarketplaceId AND marketplace_type= :marketplaceType;