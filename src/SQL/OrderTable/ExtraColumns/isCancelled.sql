UPDATE iwa_marketplace_orders_line_items
SET is_canceled =
    CASE
          WHEN marketplace_type = 'Shopify' AND fulfillments_status_control = 'null' THEN 'not_cancelled'
          WHEN marketplace_type = 'Shopify' AND fulfillments_status_control != 'null' THEN 'cancelled'
          WHEN marketplace_type = 'Trendyol' AND fulfillments_status = 'Cancelled' THEN 'cancelled'
          WHEN marketplace_type = 'Trendyol' AND fulfillments_status != 'Cancelled' THEN 'not_cancelled'
          WHEN marketplace_type = 'Bol.com' AND fulfillments_status_control = 'true' THEN 'cancelled'
          WHEN marketplace_type = 'Bol.com' AND fulfillments_status_control != 'true' THEN 'not_cancelled'
          WHEN marketplace_type = 'Etsy' AND fulfillments_status = 'Canceled' THEN 'cancelled'
          WHEN marketplace_type = 'Etsy' AND fulfillments_status != 'Canceled' THEN 'not_cancelled'
          WHEN marketplace_type = 'Amazon' AND fulfillments_status = 'Canceled' THEN 'cancelled'
          WHEN marketplace_type = 'Amazon' AND fulfillments_status != 'Canceled' THEN 'not_cancelled'
          WHEN marketplace_type = 'Wallmart' AND fulfillments_status_control = 'null' THEN 'cancelled'
          WHEN marketplace_type = 'Wallmart' AND fulfillments_status_control != 'null' THEN 'not_cancelled'
          WHEN marketplace_type = 'Ciceksepeti' AND fulfillments_status_control = 'null' THEN 'not_cancelled'
          WHEN marketplace_type = 'Ciceksepeti' AND fulfillments_status_control != 'null' THEN 'cancelled'
          WHEN marketplace_type = 'Takealot' AND fulfillments_status = 'Returned' OR fulfillments_status = 'Cancelled by Customer'  THEN 'cancelled'
          WHEN marketplace_type = 'Takealot' AND fulfillments_status = 'Returned' OR fulfillments_status = 'Cancelled by Customer' THEN 'not_cancelled'
          WHEN marketplace_type = 'Wayfair' AND fulfillments_status = 'true' THEN 'cancelled'
          WHEN marketplace_type = 'Wayfair' AND fulfillments_status = 'false' THEN 'not_cancelled'
    END;