SELECT
    osp.iwasku,
    osp.name AS product_name,
    osp.productCode,
    osp.productCategory,
    osp.imageUrl,
    osp.variationSize,
    osp.variationColor,
    opr.dest_id AS sticker_id
FROM object_relations_gproduct org
         JOIN object_product osp ON osp.oo_id = org.dest_id
         LEFT JOIN object_relations_product opr ON opr.src_id = osp.oo_id AND opr.type = 'asset' AND opr.fieldname = 'sticker4x6eu'
WHERE org.src_id = :group_id
LIMIT :limit
OFFSET :offset;