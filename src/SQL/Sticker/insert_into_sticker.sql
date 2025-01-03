INSERT INTO iwa_stickers (group_id, iwasku)
VALUES (:group_id, :iwasku);
ON DUPLICATE KEY UPDATE
     group_id = VALUES(group_id),
     iwasku = VALUES(iwasku);