TRUNCATE TABLE iwa_assistant_product;

INSERT INTO iwa_assistant_product (
    id,
    parent,
    iwasku,
    imageUrl,
    productIdentifier,
    name,
    eanGtin,
    variationSize,
    variationColor,
    wisersellId,
    productCategory,
    description,
    productDimension1,
    productDimension2,
    productDimension3,
    productWeight,
    packageDimension1,
    packageDimension2,
    packageDimension3,
    packageWeight
)

SELECT
    o.id,
    o.parentId,
    p.iwasku,
    p.imageUrl,
    p.productIdentifier,
    p.name,
    p.eanGtin,
    p.variationSize,
    p.variationColor,
    p.wisersellId,
    p.productCategory,
    p.description,
    p.productDimension1,
    p.productDimension2,
    p.productDimension3,
    p.productWeight,
    p.packageDimension1,
    p.packageDimension2,
    p.packageDimension3,
    p.packageWeight
FROM 
    objects o
INNER JOIN 
    object_query_product p ON o.id = p.oo_id
WHERE 
    o.className = 'Product' AND o.published = true;

UPDATE iwa_assistant_product p
SET children = (
    SELECT 
        JSON_ARRAYAGG(o.id)
    FROM 
        objects o
    WHERE 
        o.parentId = p.id
        AND o.className = 'Product'
        AND o.published = 1
    GROUP BY o.parentId
)
WHERE 
    EXISTS (
        SELECT 1
        FROM objects o
        WHERE 
            o.parentId = p.id
            AND o.className = 'Product'
            AND o.published = 1
    );

UPDATE iwa_assistant_product p
SET listingItems = (
    SELECT 
        JSON_ARRAYAGG(r.dest_id)
    FROM 
        object_relations_product r
    WHERE 
        r.src_id = p.id
        AND r.fieldname = 'listingItems'
)
WHERE 
    EXISTS (
        SELECT 1
        FROM object_relations_product r
        WHERE 
            r.src_id = p.id
            AND r.fieldname = 'listingItems'
    );

TRUNCATE TABLE iwa_assistant_listing;

INSERT INTO iwa_assistant_listing (
    id,
    title,
    imageUrl,
    urlLink,
    lastUpdate,
    salePrice,
    saleCurrency,
    uniqueMarketplaceId,
    quantity,
    wisersellVariantCode,
    last7Orders,
    last30Orders,
    totalOrders
)
SELECT 
    o.id,
    v.title,
    v.imageUrl,
    v.urlLink,
    v.lastUpdate,
    v.salePrice,
    v.saleCurrency,
    v.uniqueMarketplaceId,
    v.quantity,
    v.wisersellVariantCode,
    v.last7Orders,
    v.last30Orders,
    v.totalOrders
FROM 
    objects o
INNER JOIN 
    object_query_varyantproduct v ON o.id = v.oo_id
WHERE 
    o.className = 'VariantProduct' AND o.published = true;

UPDATE iwa_assistant_listing l
SET mainProduct = (
    SELECT 
        r.src_id
    FROM 
        object_relations_product r
    WHERE 
        r.dest_id = l.id
        AND r.fieldname = 'listingItems'
    LIMIT 1
)
WHERE 
    EXISTS (
        SELECT 1
        FROM object_relations_product r
        WHERE 
            r.dest_id = l.id
            AND r.fieldname = 'listingItems'
    );

UPDATE iwa_assistant_listing l
SET marketplace = (
    SELECT 
        r.dest_id
    FROM 
        object_relations_varyantproduct r
    WHERE 
        r.src_id = l.id
        AND r.fieldname = 'marketplace'
    LIMIT 1
)
WHERE 
    EXISTS (
        SELECT 1
        FROM object_relations_varyantproduct r
        WHERE 
            r.src_id = l.id
            AND r.fieldname = 'marketplace'
    );

TRUNCATE TABLE iwa_assistant_marketplace;

INSERT INTO iwa_assistant_marketplace (
    id,
    marketplaceName,
    marketplaceType,
    wisersellStoreId
)
SELECT 
    o.id,
    o.key,
    m.marketplaceType,
    m.wisersellStoreId
FROM 
    objects o
INNER JOIN 
    object_query_marketplace m ON o.id = m.oo_id
WHERE 
    o.className = 'Marketplace' AND published = true;

UPDATE iwa_assistant_marketplace m
SET listings = (
    SELECT 
        JSON_ARRAYAGG(r.src_id)
    FROM 
        object_relations_varyantproduct r
    WHERE 
        r.dest_id = m.id
        AND r.fieldname = 'marketplace'
)
WHERE 
    EXISTS (
        SELECT 1
        FROM object_relations_varyantproduct r
        WHERE 
            r.dest_id = m.id
            AND r.fieldname = 'marketplace'
    );
