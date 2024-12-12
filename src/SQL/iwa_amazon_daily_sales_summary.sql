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
            CAST(s.purchase_date AS DATETIME) AS purchase_date,
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
    SUM(quantity_shipped) AS total_quantity
    FROM
    expanded_sales
    WHERE
    purchase_date >= DATE_SUB(CURDATE(), INTERVAL 80 WEEK) -- Past 80 weeks
    GROUP BY
    asin, sales_channel, sale_date
    )
SELECT
    asin,
    sales_channel,
    sale_date,
    total_quantity
FROM
    daily_sales;

-- Step 2: Drop the existing table and rename the temporary table
DROP TABLE IF EXISTS iwa_amazon_daily_sales_summary;
RENAME TABLE iwa_amazon_daily_sales_summary_temp TO iwa_amazon_daily_sales_summary;
