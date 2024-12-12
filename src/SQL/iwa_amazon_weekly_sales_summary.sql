-- Step 1: Create a temporary table for weekly sales
CREATE TABLE iwa_amazon_weekly_sales_summary_temp AS
WITH RECURSIVE
    idx AS (SELECT 0 AS n
            UNION ALL
            SELECT n + 1
            FROM idx
            WHERE n < 99 -- Adjust this limit as needed for maximum array size
    ),
    sales_data AS (SELECT JSON_EXTRACT(o.json, '$.OrderItems[*].ASIN')                                              AS asin_array,
                          JSON_EXTRACT(o.json, '$.OrderItems[*].QuantityShipped')                                   AS quantity_array,
                          REPLACE(REPLACE(JSON_UNQUOTE(JSON_EXTRACT(o.json, '$.PurchaseDate')), 'T', ' '), 'Z',
                                  '')                                                                               AS purchase_date,
                          JSON_UNQUOTE(JSON_EXTRACT(o.json, '$.SalesChannel'))                                      AS sales_channel,
                          o.order_id
                   FROM iwa_marketplace_orders o
                   WHERE REGEXP_LIKE(o.order_id, '^[0-9]{3}-[0-9]{7}-[0-9]{7}$') -- Filter Amazon Order IDs
                     AND JSON_UNQUOTE(JSON_EXTRACT(o.json, '$.OrderStatus')) = 'Shipped'),
    expanded_sales AS (SELECT JSON_UNQUOTE(JSON_EXTRACT(s.asin_array, CONCAT('$[', idx.n, ']')))         AS asin,
                              CAST(JSON_EXTRACT(s.quantity_array, CONCAT('$[', idx.n, ']')) AS UNSIGNED) AS quantity_shipped,
                              CAST(s.purchase_date AS DATETIME)                                          AS purchase_date,
                              s.sales_channel,
                              s.order_id
                       FROM sales_data s
                                JOIN idx
                                     ON idx.n < JSON_LENGTH(s.asin_array)),
    weekly_sales AS (SELECT asin,
                            sales_channel,
                            YEARWEEK(purchase_date, 1) AS week_year, -- ISO Week-Year format
                            SUM(quantity_shipped)      AS total_quantity
                     FROM expanded_sales
                     WHERE purchase_date >= DATE_SUB(CURDATE(), INTERVAL 78 WEEK) -- Limit to the last 78 weeks
                     GROUP BY asin, sales_channel, week_year)
SELECT asin,
       sales_channel,
       week_year,
       total_quantity
FROM weekly_sales;

-- Step 2: Drop the existing table and rename the temporary table
DROP TABLE IF EXISTS iwa_amazon_weekly_sales_summary;
RENAME
TABLE iwa_amazon_weekly_sales_summary_temp TO iwa_amazon_weekly_sales_summary;
