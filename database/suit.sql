/*
Navicat MySQL Data Transfer

Source Server         : aa
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : suit

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2017-12-17 18:47:28
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for address
-- ----------------------------
DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '关联的用户ID',
  `phone` varchar(15) NOT NULL COMMENT '收货人联系电话',
  `name` varchar(30) NOT NULL COMMENT '收货人姓名',
  `province` varchar(40) NOT NULL COMMENT '省',
  `city` varchar(40) DEFAULT NULL,
  `area` varchar(40) DEFAULT NULL COMMENT '区',
  `detail` varchar(250) DEFAULT NULL COMMENT '详细地址',
  `store_num` varchar(30) DEFAULT '' COMMENT '门牌号',
  `area_code` varchar(30) NOT NULL DEFAULT '0' COMMENT '对应每日优鲜的城市code',
  `type` varchar(20) NOT NULL COMMENT '地址类型  住宅，公司，学校，其他',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效 0无效 1有效',
  `ct` datetime NOT NULL COMMENT '创建时间',
  `ut` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='收货地址表';

-- ----------------------------
-- Records of address
-- ----------------------------

-- ----------------------------
-- Table structure for adm_user
-- ----------------------------
DROP TABLE IF EXISTS `adm_user`;
CREATE TABLE `adm_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `phone` varchar(30) NOT NULL DEFAULT '' COMMENT '手机号',
  `pass` varchar(80) NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` varchar(30) NOT NULL DEFAULT '' COMMENT '昵称',
  `integral` double(8,2) NOT NULL DEFAULT '0.00' COMMENT '积分数',
  `user_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '用户类型',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='后台用户表';

-- ----------------------------
-- Records of adm_user
-- ----------------------------
INSERT INTO `adm_user` VALUES ('1', 'root', '05707b58fb1e31ece2e981e98b327668', '管理员', '0.00', '1', '1', '2017-10-25 11:12:54', '2017-12-17 15:20:01');
INSERT INTO `adm_user` VALUES ('2', 'admin', '14e126c4abfbb4b4b9553bb44cee3b16', '管理员2', '0.00', '1', '1', '2017-10-25 11:12:54', '2017-10-25 11:13:33');

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku_id` varchar(30) NOT NULL COMMENT '每日优鲜对应的商品sku_id',
  `name` varchar(80) NOT NULL COMMENT '商品名称',
  `images` varchar(120) NOT NULL COMMENT '商品图片',
  `stock` int(11) unsigned NOT NULL COMMENT '库存数',
  `stock_use` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '剩余库存',
  `price` float NOT NULL COMMENT '市场价格',
  `helps` int(11) NOT NULL COMMENT '免费领需助力数',
  `sorts` int(11) NOT NULL DEFAULT '1' COMMENT '商品权重，越大越靠前',
  `description` varchar(200) DEFAULT '' COMMENT '商品描述',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效 0无效 1有效 ',
  `ut` datetime NOT NULL COMMENT '修改时间',
  `ct` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`,`sku_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='商品表';

-- ----------------------------
-- Records of goods
-- ----------------------------

-- ----------------------------
-- Table structure for groups
-- ----------------------------
DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(30) NOT NULL COMMENT '微信群ID',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1',
  `ct` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of groups
-- ----------------------------

-- ----------------------------
-- Table structure for group_qrcode
-- ----------------------------
DROP TABLE IF EXISTS `group_qrcode`;
CREATE TABLE `group_qrcode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `qrcode_url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '二维码地址',
  `ct` datetime NOT NULL COMMENT '创建时间',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效 0无效 1有效',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='群二维码表';

-- ----------------------------
-- Records of group_qrcode
-- ----------------------------

-- ----------------------------
-- Table structure for orders
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unionid` varchar(32) NOT NULL COMMENT '用户的唯一标识unionid',
  `goods_id` int(11) NOT NULL COMMENT '订单关联的商品ID',
  `order_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '订单状态  1进行中 2自动取消 3完成',
  `helps` int(11) NOT NULL COMMENT '该商品所需助力数（生成订单时记录）',
  `address_id` int(11) NOT NULL COMMENT '订单关联收货地址ID',
  `share_url` varchar(200) NOT NULL COMMENT '分享出去的url',
  `form_id` varchar(130) DEFAULT NULL COMMENT '小程序发送消息的formid',
  `is_receive` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否领取 0未领取  1已领取',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效 0无效 1有效',
  `ct` datetime NOT NULL COMMENT '创建时间',
  `ut` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`),
  KEY `unionid` (`unionid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='订单表';

-- ----------------------------
-- Records of orders
-- ----------------------------

-- ----------------------------
-- Table structure for order_helps
-- ----------------------------
DROP TABLE IF EXISTS `order_helps`;
CREATE TABLE `order_helps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT '关联订单表主键ID',
  `unionid_help` varchar(40) NOT NULL COMMENT '助力人的unionid',
  `group_id` varchar(30) NOT NULL DEFAULT '' COMMENT '群ID',
  `is_valid` int(11) NOT NULL DEFAULT '0' COMMENT '助力是否有效 0无效 1有效',
  `ct` datetime NOT NULL COMMENT '创建时间',
  `ut` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='订单助力表';

-- ----------------------------
-- Records of order_helps
-- ----------------------------

-- ----------------------------
-- Table structure for reserve
-- ----------------------------
DROP TABLE IF EXISTS `reserve`;
CREATE TABLE `reserve` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `sex` tinyint(4) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `remark` text,
  `sign` tinyint(255) DEFAULT NULL COMMENT '标记是否联系',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of reserve
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(30) DEFAULT NULL COMMENT '手机号',
  `nickname` varchar(150) DEFAULT NULL,
  `nickname_bak` varchar(80) DEFAULT NULL COMMENT '备用昵称',
  `openid` varchar(40) NOT NULL,
  `unionid` varchar(40) NOT NULL,
  `gender` tinyint(4) DEFAULT '1' COMMENT '性别: 0,没有 1男 2女',
  `province` varchar(40) DEFAULT NULL,
  `city` varchar(40) NOT NULL,
  `avatar_url` varchar(200) DEFAULT NULL COMMENT '头像url',
  `is_valid` tinyint(4) DEFAULT '1',
  `is_check` tinyint(4) DEFAULT '0' COMMENT '是否验证 0未验证 1已验证	',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `unionid` (`unionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of users
-- ----------------------------
