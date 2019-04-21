-- ----------------------------
-- 上传后分组的图片
-- ----------------------------
DROP TABLE IF EXISTS `ztb_upload_image_manage`;
CREATE TABLE `ztb_upload_image_manage`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'url',
  `cate_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分类id',
  PRIMARY KEY (`id`) USING BTREE,
) ENGINE = InnoDB AUTO_INCREMENT = 48 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;


-- ----------------------------
-- 图片的分类
-- ----------------------------
DROP TABLE IF EXISTS `ztb_image_cate_manage`;
CREATE TABLE `ztb_image_cate_manage`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  PRIMARY KEY (`id`) USING BTREE,
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Compact;

INSERT INTO `ztb_image_cate_manage` VALUES (1,'默认分组');

