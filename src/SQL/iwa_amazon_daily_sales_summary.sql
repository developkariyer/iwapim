SET GLOBAL innodb_parallel_read_threads = 8;

SELECT "Step 1: Create temp_sales_data temporary table";
DROP TEMPORARY TABLE IF EXISTS temp_sales_data;
CREATE TEMPORARY TABLE temp_sales_data (
    asin_array JSON,
    quantity_array JSON,
    purchase_date DATETIME,
    sales_channel VARCHAR(50),
    order_id VARCHAR(50)
) AS
SELECT
    JSON_EXTRACT(o.json, '$.OrderItems[*].ASIN') AS asin_array,
    JSON_EXTRACT(o.json, '$.OrderItems[*].QuantityShipped') AS quantity_array,
    REPLACE(REPLACE(JSON_UNQUOTE(JSON_EXTRACT(o.json, '$.PurchaseDate')), 'T', ' '), 'Z', '') AS purchase_date,
    JSON_UNQUOTE(JSON_EXTRACT(o.json, '$.SalesChannel')) AS sales_channel,
    o.order_id
FROM
    iwa_marketplace_orders o
WHERE
    REGEXP_LIKE(o.order_id, '^[0-9]{3}-[0-9]{7}-[0-9]{7}$') -- Validate Amazon Order ID format
  AND JSON_UNQUOTE(JSON_EXTRACT(o.json, '$.OrderStatus')) = 'Shipped';

SELECT "Step 2: Create temp_expanded_sales temporary table";
DROP TEMPORARY TABLE IF EXISTS temp_expanded_sales;
CREATE TEMPORARY TABLE temp_expanded_sales (
    asin VARCHAR(50),
    quantity_shipped INT,
    purchase_date DATE,
    sales_channel VARCHAR(50),
    order_id VARCHAR(50)
) AS
WITH RECURSIVE idx AS (
    SELECT 0 AS n
    UNION ALL
    SELECT n + 1
    FROM idx
    WHERE n < 99
)
SELECT
    JSON_UNQUOTE(JSON_EXTRACT(s.asin_array, CONCAT('$[', idx.n, ']'))) AS asin,
    CAST(JSON_EXTRACT(s.quantity_array, CONCAT('$[', idx.n, ']')) AS UNSIGNED) AS quantity_shipped,
    CAST(s.purchase_date AS DATE) AS purchase_date,
    s.sales_channel,
    s.order_id
FROM
    temp_sales_data s
        JOIN idx
             ON idx.n < JSON_LENGTH(s.asin_array);

SELECT "Step 3: Create temp_daily_sales temporary table";
DROP TEMPORARY TABLE IF EXISTS temp_daily_sales;
CREATE TEMPORARY TABLE temp_daily_sales (
    asin VARCHAR(50),
    sales_channel VARCHAR(50),
    sale_date DATE,
    total_quantity DECIMAL(10, 4),
    data_source TINYINT
) AS
SELECT
    asin,
    sales_channel,
    DATE(purchase_date) AS sale_date,
    SUM(quantity_shipped) AS total_quantity,
    1 AS data_source
FROM
    temp_expanded_sales
GROUP BY
    asin, sales_channel, sale_date;

SELECT "Step 4.0: Compute global maximum sale date";
SET @global_max_date = (SELECT MAX(sale_date) FROM temp_daily_sales);

SELECT "Step 4.1: Create temporary table for date range per ASIN and sales channel";
DROP TEMPORARY TABLE IF EXISTS temp_date_range;
CREATE TEMPORARY TABLE temp_date_range AS
SELECT
    asin,
    sales_channel,
    MIN(sale_date) AS min_date,
    @global_max_date AS max_date
FROM temp_daily_sales
GROUP BY asin, sales_channel;

SELECT "Step 4.2: Create asin_sales_channel_date table";
DROP TEMPORARY TABLE IF EXISTS temp_asin_sales_channel_date;
CREATE TEMPORARY TABLE temp_asin_sales_channel_date AS
SELECT
    dr.asin,
    dr.sales_channel,
    d.date AS sale_date
FROM
    temp_date_range dr
        CROSS JOIN iwa_static_dates d
WHERE d.date BETWEEN dr.min_date AND dr.max_date;

SELECT "Step 5: Populate asin_sales_channel_date with total_quantity and iwasku";
DROP TABLE IF EXISTS iwa_amazon_daily_sales_summary_temp;
CREATE TABLE iwa_amazon_daily_sales_summary_temp (
                                                     asin VARCHAR(50),
                                                     iwasku VARCHAR(50),
                                                     sales_channel VARCHAR(50),
                                                     sale_date DATE,
                                                     total_quantity DECIMAL(10, 4),
                                                     data_source TINYINT
) AS
SELECT
    tascd.asin,
    COALESCE((SELECT regvalue FROM iwa_registry WHERE regtype = 'asin-to-iwasku' AND regkey = tascd.asin), tascd.asin) AS iwasku,
    tascd.sales_channel,
    tascd.sale_date,
    COALESCE(td.total_quantity, 0) AS total_quantity,
    1 AS data_source
FROM
    temp_asin_sales_channel_date tascd
        LEFT JOIN temp_daily_sales td
                  ON tascd.asin = td.asin
                      AND tascd.sales_channel = td.sales_channel
                      AND tascd.sale_date = td.sale_date;

SELECT "Step 6.1: Insert aggregated 'all' rows";
-- Insert 'all' rows grouped by iwasku
INSERT INTO iwa_amazon_daily_sales_summary_temp (asin, iwasku, sales_channel, sale_date, total_quantity, data_source)
SELECT
    iwasku AS asin,
    iwasku,
    'all' AS sales_channel,
    sale_date,
    SUM(total_quantity) AS total_quantity,
    1 AS data_source
FROM
    iwa_amazon_daily_sales_summary_temp
GROUP BY
    iwasku, sale_date;

SELECT "Step 6.2: Insert aggregated 'Amazon.eu' rows";
-- Insert 'Amazon.eu' rows grouped by iwasku
INSERT INTO iwa_amazon_daily_sales_summary_temp (asin, iwasku, sales_channel, sale_date, total_quantity, data_source)
SELECT
    iwasku AS asin, -- Use iwasku as asin for 'Amazon.eu' sales_channel
    iwasku,
    'Amazon.eu' AS sales_channel,
    sale_date,
    SUM(total_quantity) AS total_quantity,
    1 AS data_source
FROM
    iwa_amazon_daily_sales_summary_temp
WHERE sales_channel IN (
                        'Amazon.de', 'Amazon.fr', 'Amazon.it', 'Amazon.es', 'Amazon.nl',
                        'Amazon.be', 'Amazon.ie', 'Amazon.se', 'Amazon.pl', 'Amazon.cz',
                        'Amazon.at', 'Amazon.hu'
    )
GROUP BY
    iwasku, sale_date;

SELECT "Step 7.1: Add iwasku index to improve query performance";
CREATE INDEX idx_iwasku ON iwa_amazon_daily_sales_summary_temp (iwasku);
SELECT "Step 7.2: Add sales_channel index to improve query performance";
CREATE INDEX idx_sales_channel ON iwa_amazon_daily_sales_summary_temp (sales_channel);
SELECT "Step 7.3: Add sale_date index to improve query performance";
CREATE INDEX idx_sale_date ON iwa_amazon_daily_sales_summary_temp (sale_date);
SELECT "Step 7.4: Add unique index to improve query performance";
ALTER TABLE iwa_amazon_daily_sales_summary_temp ADD UNIQUE KEY (asin, sales_channel, sale_date);

SELECT "Step 8: Replace original table with the updated one";
DROP TABLE IF EXISTS iwa_amazon_daily_sales_summary;
RENAME TABLE iwa_amazon_daily_sales_summary_temp TO iwa_amazon_daily_sales_summary;
