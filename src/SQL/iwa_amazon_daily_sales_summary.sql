-- Step 1: Create a temporary table for daily sales
DROP TABLE IF EXISTS iwa_amazon_daily_sales_summary_temp;

CREATE TABLE iwa_amazon_daily_sales_summary_temp AS
WITH RECURSIVE
    idx AS (
        SELECT 0 AS n
        UNION ALL
        SELECT n + 1
        FROM idx
        WHERE n < 99
    ),
    sales_data AS (
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
          AND JSON_UNQUOTE(JSON_EXTRACT(o.json, '$.OrderStatus')) = 'Shipped'
    ),
    expanded_sales AS (
        SELECT
            JSON_UNQUOTE(JSON_EXTRACT(s.asin_array, CONCAT('$[', idx.n, ']'))) AS asin,
            CAST(JSON_EXTRACT(s.quantity_array, CONCAT('$[', idx.n, ']')) AS UNSIGNED) AS quantity_shipped,
            CAST(s.purchase_date AS DATE) AS purchase_date,
            s.sales_channel,
            s.order_id
        FROM
            sales_data s
                JOIN
            idx
            ON
                idx.n < JSON_LENGTH(s.asin_array)
    ),
    daily_sales AS (
        SELECT
            asin,
            sales_channel,
    DATE(purchase_date) AS sale_date, -- Extract only the date
    SUM(quantity_shipped) AS total_quantity,
    1 AS data_source -- 1 indicates gathered data
    FROM
    expanded_sales
    WHERE
    purchase_date >= DATE_SUB(CURDATE(), INTERVAL 2 YEAR) -- Past 2 years
    GROUP BY
    asin, sales_channel, sale_date
    ),
    date_range AS (
                      SELECT MIN(sale_date) AS start_date, MAX(sale_date) AS end_date
    FROM daily_sales
    ),
    all_dates AS (
                     SELECT start_date + INTERVAL seq DAY AS generated_date
                     FROM date_range, (
                     SELECT @row := @row + 1 AS seq
                     FROM (SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a,
(SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b,
(SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) c
    CROSS JOIN (SELECT @row := -1) init
    ) d
    WHERE start_date + INTERVAL seq DAY <= end_date
    ),
    expanded_asin_sales_channel AS (
                                       SELECT DISTINCT asin, sales_channel
                                       FROM daily_sales
                                   ),
    full_data_set AS (
                         SELECT
                         a.asin,
                         a.sales_channel,
                         b.generated_date AS sale_date,
                         IFNULL(d.total_quantity, 0) AS total_quantity,
    IF(d.total_quantity IS NULL, 0, 1) AS data_source -- 0 indicates forecasted or missing data
    FROM
    expanded_asin_sales_channel a
    CROSS JOIN
    all_dates b
    LEFT JOIN
    daily_sales d
    ON
    a.asin = d.asin
    AND a.sales_channel = d.sales_channel
    AND b.generated_date = d.sale_date
    )
SELECT
    asin,
    sales_channel,
    sale_date,
    total_quantity,
    data_source
FROM
    full_data_set;

-- Step 2: Drop the existing table and rename the temporary table
DROP TABLE IF EXISTS iwa_amazon_daily_sales_summary;
RENAME TABLE iwa_amazon_daily_sales_summary_temp TO iwa_amazon_daily_sales_summary;

-- Step 3: Create indexes for faster querying
ALTER TABLE iwa_amazon_daily_sales_summary
    MODIFY asin VARCHAR(50),
    MODIFY sales_channel VARCHAR(50);

CREATE INDEX idx_asin ON iwa_amazon_daily_sales_summary (asin);
CREATE INDEX idx_sales_channel ON iwa_amazon_daily_sales_summary (sales_channel);
CREATE INDEX idx_sale_date ON iwa_amazon_daily_sales_summary (sale_date);
ALTER TABLE iwa_amazon_daily_sales_summary ADD UNIQUE KEY (asin, sales_channel, sale_date);
