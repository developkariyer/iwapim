INSERT INTO iwa_stickers (group_id, iwasku, product_code, category, product_name, product_attributes ,image_link, sticker_link)
VALUES (:group_id, :iwasku, :product_code, :category, :product_name,  :attributes, :image_link, :sticker_link);
ON DUPLICATE KEY UPDATE
     group_id = VALUES(group_id),
     product_code = VALUES(product_code),
     category = VALUES(category),
     product_name = VALUES(product_name),
     attributes = VALUES(attributes),
     image_link = VALUES(image_link),
     sticker_link = VALUES(sticker_link);