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
    o.id, -- Product ID
    o.parentId AS parent, -- Parent ID
    p.iwasku, -- Internal SKU
    p.imageUrl, -- Product image URL
    p.productIdentifier, -- Product group identifier
    p.name, -- Product name
    p.eanGtin, -- Barcode/GTIN
    p.variationSize, -- Size variation
    p.variationColor, -- Color variation
    p.wisersellId, -- Wisersell identifier
    p.productCategory, -- Product category
    p.description, -- Product description
    p.productDimension1, -- Product dimension 1
    p.productDimension2, -- Product dimension 2
    p.productDimension3, -- Product dimension 3
    p.productWeight, -- Product weight
    p.packageDimension1, -- Package dimension 1
    p.packageDimension2, -- Package dimension 2
    p.packageDimension3, -- Package dimension 3
    p.packageWeight -- Package weight
FROM 
    objects o
INNER JOIN 
    object_query_product p ON o.id = p.oo_id
WHERE 
    o.className = 'Product' AND o.published = true;

UPDATE iwa_assistant_product p
SET children = (
    SELECT 
        JSON_ARRAYAGG(o.id) -- Aggregate child IDs into a JSON array
    FROM 
        objects o
    WHERE 
        o.parentId = p.id -- Match parent ID
        AND o.className = 'Product' -- Ensure we're only dealing with products
        AND o.published = 1 -- Include only published products
    GROUP BY o.parentId
)
WHERE 
    EXISTS (
        SELECT 1
        FROM objects o
        WHERE 
            o.parentId = p.id -- Ensure there are child rows
            AND o.className = 'Product'
            AND o.published = 1
    );

UPDATE iwa_assistant_product p
SET listingItems = (
    SELECT 
        JSON_ARRAYAGG(r.dest_id) -- Aggregate linked listing IDs into a JSON array
    FROM 
        object_relations_product r
    WHERE 
        r.src_id = p.id -- Match product ID
        AND r.fieldname = 'listingItems' -- Only consider 'listingItems' relationships
)
WHERE 
    EXISTS (
        SELECT 1
        FROM object_relations_product r
        WHERE 
            r.src_id = p.id -- Ensure there are linked listings
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
    o.id, -- Listing ID
    v.title, -- Listing title
    v.imageUrl, -- Listing image URL
    v.urlLink, -- URL link to the marketplace listing
    v.lastUpdate, -- Last update timestamp
    v.salePrice, -- Sale price
    v.saleCurrency, -- Sale currency
    v.uniqueMarketplaceId, -- Unique identifier for the marketplace
    v.quantity, -- Quantity available
    v.wisersellVariantCode, -- Wisersell variant code
    v.last7Orders, -- Orders in the last 7 days
    v.last30Orders, -- Orders in the last 30 days
    v.totalOrders -- Total number of orders
FROM 
    objects o
INNER JOIN 
    object_query_varyantproduct v ON o.id = v.oo_id
WHERE 
    o.className = 'VariantProduct' AND o.published = true;

UPDATE iwa_assistant_listing l
SET mainProduct = (
    SELECT 
        r.src_id -- Find the product ID linked to this listing
    FROM 
        object_relations_product r
    WHERE 
        r.dest_id = l.id -- Match listing ID
        AND r.fieldname = 'listingItems' -- Only consider 'listingItems' relationships
    LIMIT 1 -- Ensure a single product ID
)
WHERE 
    EXISTS (
        SELECT 1
        FROM object_relations_product r
        WHERE 
            r.dest_id = l.id -- Ensure there is a linked product
            AND r.fieldname = 'listingItems'
    );

UPDATE iwa_assistant_listing l
SET marketplace = (
    SELECT 
        r.dest_id -- Marketplace ID linked to the listing
    FROM 
        object_relations_varyantproduct r
    WHERE 
        r.src_id = l.id -- Match listing ID
        AND r.fieldname = 'marketplace' -- Only consider 'marketplace' relationships
    LIMIT 1 -- Ensure a single marketplace ID
)
WHERE 
    EXISTS (
        SELECT 1
        FROM object_relations_varyantproduct r
        WHERE 
            r.src_id = l.id -- Ensure there is a linked marketplace
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
    o.id, -- Marketplace ID
    o.key,
    m.marketplaceType, -- Type of marketplace
    m.wisersellStoreId -- Wisersell store ID
FROM 
    objects o
INNER JOIN 
    object_query_marketplace m ON o.id = m.oo_id
WHERE 
    o.className = 'Marketplace' AND published = true; -- Only include rows classified as 'Marketplace'

UPDATE iwa_assistant_marketplace m
SET listings = (
    SELECT 
        JSON_ARRAYAGG(r.src_id) -- Aggregate listing IDs into a JSON array
    FROM 
        object_relations_varyantproduct r
    WHERE 
        r.dest_id = m.id -- Match marketplace ID
        AND r.fieldname = 'marketplace' -- Only consider 'marketplace' relationships
)
WHERE 
    EXISTS (
        SELECT 1
        FROM object_relations_varyantproduct r
        WHERE 
            r.dest_id = m.id -- Ensure there are linked listings
            AND r.fieldname = 'marketplace'
    );
