-- Step 1: Create a temporary table for inventory summary
DROP TABLE IF EXISTS iwa_amazon_inventory_summary_temp;

CREATE TABLE iwa_amazon_inventory_summary_temp AS
SELECT iwasku,
       asin,
       CASE
           WHEN warehouse = 'DE' THEN 'EU'
           ELSE warehouse
           END                                                                                                        AS warehouse,
       GROUP_CONCAT(
               CONCAT(
                       JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.fnSku')),
                       '=>',
                       JSON_UNQUOTE(JSON_EXTRACT(json_data, '$.sellerSku'))
               ) SEPARATOR ' | '
       )                                                                                                              AS sku_list,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.totalQuantity'), 0))                                                     AS total_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.reservedQuantity.fcProcessingQuantity'),
                  0))                                                                                                 AS fc_processing_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.reservedQuantity.totalReservedQuantity'),
                  0))                                                                                                 AS total_reserved_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.reservedQuantity.pendingCustomerOrderQuantity'),
                  0))                                                                                                 AS pending_customer_order_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.reservedQuantity.pendingTransshipmentQuantity'),
                  0))                                                                                                 AS pending_transshipment_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.fulfillableQuantity'),
                  0))                                                                                                 AS fulfillable_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.researchingQuantity.totalResearchingQuantity'),
                  0))                                                                                                 AS total_researching_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.futureSupplyQuantity.futureSupplyBuyableQuantity'),
                  0))                                                                                                 AS future_supply_buyable_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.futureSupplyQuantity.reservedFutureSupplyQuantity'),
                  0))                                                                                                 AS reserved_future_supply_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.unfulfillableQuantity.expiredQuantity'),
                  0))                                                                                                 AS expired_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.unfulfillableQuantity.defectiveQuantity'),
                  0))                                                                                                 AS defective_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.unfulfillableQuantity.carrierDamagedQuantity'),
                  0))                                                                                                 AS carrier_damaged_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.unfulfillableQuantity.customerDamagedQuantity'),
                  0))                                                                                                 AS customer_damaged_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.unfulfillableQuantity.warehouseDamagedQuantity'),
                  0))                                                                                                 AS warehouse_damaged_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.unfulfillableQuantity.distributorDamagedQuantity'),
                  0))                                                                                                 AS distributor_damaged_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.unfulfillableQuantity.totalUnfulfillableQuantity'),
                  0))                                                                                                 AS total_unfulfillable_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.inboundShippedQuantity'),
                  0))                                                                                                 AS inbound_shipped_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.inboundWorkingQuantity'),
                  0))                                                                                                 AS inbound_working_quantity,
       SUM(IFNULL(JSON_EXTRACT(json_data, '$.inventoryDetails.inboundReceivingQuantity'),
                  0))                                                                                                 AS inbound_receiving_quantity
FROM iwa_inventory
WHERE inventory_type = 'AMAZON_FBA'
  AND item_condition = 'NewItem'
GROUP BY iwasku, asin, warehouse;
-- AND total_quantity > 0

-- Step 2: Drop the existing table and rename the temporary table
DROP TABLE IF EXISTS iwa_amazon_inventory_summary;
RENAME
TABLE iwa_amazon_inventory_summary_temp TO iwa_amazon_inventory_summary;
