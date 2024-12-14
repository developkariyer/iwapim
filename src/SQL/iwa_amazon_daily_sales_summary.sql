SELECT "Step 1: Create temp_sales_data temporary table"
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

SELECT "Step 2: Create temp_expanded_sales temporary table"
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

SELECT "Step 3: Create temp_daily_sales temporary table"
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
    1 AS data_source -- 1 indicates gathered data
FROM
    temp_expanded_sales
WHERE
    purchase_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) -- Past 2 years
GROUP BY
    asin, sales_channel, sale_date;

SELECT "Step 4: Generate the full date range in temp_all_dates"
DROP TEMPORARY TABLE IF EXISTS temp_all_dates;
CREATE TEMPORARY TABLE temp_all_dates (
    generated_date DATE
) AS
WITH RECURSIVE seq AS (
    SELECT 0 AS n
    UNION ALL
    SELECT n + 1
    FROM seq
    WHERE n < DATEDIFF(DATE_SUB(CURDATE(), INTERVAL 1 DAY), DATE_SUB(CURDATE(), INTERVAL 2 YEAR))
)
SELECT
    DATE_SUB(CURDATE(), INTERVAL 2 YEAR) + INTERVAL n DAY AS generated_date
FROM
    seq;

SELECT "Step 5: Create temp_expanded_asin_sales_channel"
DROP TEMPORARY TABLE IF EXISTS temp_expanded_asin_sales_channel;
CREATE TEMPORARY TABLE temp_expanded_asin_sales_channel (
    asin VARCHAR(50),
    sales_channel VARCHAR(50)
) AS
SELECT DISTINCT asin, sales_channel
FROM temp_daily_sales;

SELECT "Step 6: Combine all data into iwa_amazon_daily_sales_summary_temp"
DROP TABLE IF EXISTS iwa_amazon_daily_sales_summary_temp;
CREATE TABLE iwa_amazon_daily_sales_summary_temp (
    asin VARCHAR(50),
    iwasku VARCHAR(50),
    sales_channel VARCHAR(50),
    sale_date DATE,
    total_quantity DECIMAL(10,4),
    data_source TINYINT
) AS
SELECT
    a.asin,
    COALESCE((SELECT regvalue FROM iwa_registry WHERE regtype = 'asin-to-iwasku' AND regkey = a.asin), a.asin) AS iwasku,
    a.sales_channel,
    b.generated_date AS sale_date,
    IFNULL(d.total_quantity, 0) AS total_quantity,
    1 AS data_source -- 0 indicates forecasted or missing data
FROM
    temp_expanded_asin_sales_channel a
        CROSS JOIN
    temp_all_dates b
        LEFT JOIN
    temp_daily_sales d
    ON
        a.asin = d.asin
            AND a.sales_channel = d.sales_channel
            AND b.generated_date = d.sale_date;

DROP TEMPORARY TABLE IF EXISTS temp_calendar;
CREATE TEMPORARY TABLE temp_calendar AS
SELECT date AS sale_date
FROM iwa_static_dates
WHERE date >= (SELECT MIN(sale_date) FROM temp_daily_sales)
  AND date <= (SELECT MAX(sale_date) FROM temp_daily_sales);

SELECT "Step 7: Insert aggregated data for 'all' sales channels"
INSERT INTO iwa_amazon_daily_sales_summary_temp (asin, iwasku, sales_channel, sale_date, total_quantity, data_source)
SELECT
    tds.asin AS asin,
    COALESCE((SELECT regvalue FROM iwa_registry WHERE regtype = 'asin-to-iwasku' AND regkey = tds.asin), tds.asin) AS iwasku,
    'all' AS sales_channel,
    cal.sale_date AS sale_date,
    COALESCE(SUM(tds.total_quantity), 0) AS total_quantity,
    1 AS data_source
FROM
    temp_calendar cal
        LEFT JOIN
    temp_daily_sales tds
    ON
        cal.sale_date = tds.sale_date
GROUP BY
    tds.asin, cal.sale_date
    ON DUPLICATE KEY UPDATE
         total_quantity = VALUES(total_quantity);

SELECT "Step 8: Insert aggregated data for 'Amazon.eu' sales channels"
INSERT INTO iwa_amazon_daily_sales_summary_temp (asin, iwasku, sales_channel, sale_date, total_quantity, data_source)
SELECT
    tds.asin AS asin,
    COALESCE((SELECT regvalue FROM iwa_registry WHERE regtype = 'asin-to-iwasku' AND regkey = tds.asin), tds.asin) AS iwasku,
    'Amazon.eu' AS sales_channel,
    temp_calendar.sale_date,
    COALESCE(SUM(tds.total_quantity), 0) AS total_quantity,
    1 AS data_source
FROM
    temp_calendar
        LEFT JOIN
    temp_daily_sales tds
    ON
        temp_calendar.sale_date = tds.sale_date
            AND tds.sales_channel IN (
                                      'Amazon.de',
                                      'Amazon.fr',
                                      'Amazon.it',
                                      'Amazon.es',
                                      'Amazon.nl',
                                      'Amazon.be',
                                      'Amazon.ie',
                                      'Amazon.se',
                                      'Amazon.pl',
                                      'Amazon.cz',
                                      'Amazon.at',
                                      'Amazon.hu'
            )
GROUP BY
    tds.asin, temp_calendar.sale_date
    ON DUPLICATE KEY UPDATE
        total_quantity = VALUES(total_quantity);


SELECT "Step 9: Drop the existing table and rename the temporary table";
DROP TABLE IF EXISTS iwa_amazon_daily_sales_summary;
RENAME TABLE iwa_amazon_daily_sales_summary_temp TO iwa_amazon_daily_sales_summary;

SELECT "Step 10: Add indexes for faster querying";
CREATE INDEX idx_iwasku ON iwa_amazon_daily_sales_summary (iwasku);
CREATE INDEX idx_sales_channel ON iwa_amazon_daily_sales_summary (sales_channel);
ALTER TABLE iwa_amazon_daily_sales_summary ADD UNIQUE KEY (asin, sales_channel, sale_date);
