UPDATE iwa_marketplace_orders_line_items
SET referring_site_domain = :referringSiteDomain
WHERE referring_site = :referringSite;