-- phpMyAdmin SQL Dump
-- version 4.7.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: 2017-12-17 01:47:16
-- 服务器版本： 5.6.20-log
-- PHP Version: 7.0.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pintuan`
--

-- --------------------------------------------------------

--
-- 表的结构 `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
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
  `ut` datetime NOT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收货地址表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `adm_user`
--

CREATE TABLE `adm_user` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '自增ID',
  `phone` varchar(30) NOT NULL DEFAULT '' COMMENT '手机号',
  `pass` varchar(80) NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` varchar(30) NOT NULL DEFAULT '' COMMENT '昵称',
  `integral` double(8,2) NOT NULL DEFAULT '0.00' COMMENT '积分数',
  `user_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '用户类型',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='后台用户表';

--
-- 转存表中的数据 `adm_user`
--

INSERT INTO `adm_user` (`id`, `phone`, `pass`, `nickname`, `integral`, `user_type`, `is_valid`, `created_at`, `updated_at`) VALUES
(1, 'root', '05707b58fb1e31ece2e981e98b327668', '管理员', 0.00, 1, 1, '2017-10-25 03:12:54', '2017-10-26 03:15:49'),
(2, 'admin', '14e126c4abfbb4b4b9553bb44cee3b16', '管理员2', 0.00, 1, 1, '2017-10-25 03:12:54', '2017-10-25 03:13:33');

-- --------------------------------------------------------

--
-- 表的结构 `goods`
--

CREATE TABLE `goods` (
  `id` int(11) NOT NULL,
  `sku_id` varchar(30) NOT NULL COMMENT '每日优鲜对应的商品sku_id',
  `name` varchar(80) NOT NULL COMMENT '商品名称',
  `images` varchar(120) NOT NULL COMMENT '商品图片',
  `stock` int(11) UNSIGNED NOT NULL COMMENT '库存数',
  `stock_use` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '剩余库存',
  `price` float NOT NULL COMMENT '市场价格',
  `helps` int(11) NOT NULL COMMENT '免费领需助力数',
  `sorts` int(11) NOT NULL DEFAULT '1' COMMENT '商品权重，越大越靠前',
  `description` varchar(200) DEFAULT '' COMMENT '商品描述',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效 0无效 1有效 ',
  `ut` datetime NOT NULL COMMENT '修改时间',
  `ct` datetime NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `group` varchar(30) NOT NULL COMMENT '微信群ID',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1',
  `ct` datetime DEFAULT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `group_qrcode`
--

CREATE TABLE `group_qrcode` (
  `id` int(11) NOT NULL,
  `qrcode_url` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '二维码地址',
  `ct` datetime NOT NULL COMMENT '创建时间',
  `is_valid` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否有效 0无效 1有效'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='群二维码表';

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
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
  `ut` datetime NOT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `order_helps`
--

CREATE TABLE `order_helps` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL COMMENT '关联订单表主键ID',
  `unionid_help` varchar(40) NOT NULL COMMENT '助力人的unionid',
  `group_id` varchar(30) NOT NULL DEFAULT '' COMMENT '群ID',
  `is_valid` int(11) NOT NULL DEFAULT '0' COMMENT '助力是否有效 0无效 1有效',
  `ct` datetime NOT NULL COMMENT '创建时间',
  `ut` datetime NOT NULL COMMENT '修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单助力表' ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
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
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `adm_user`
--
ALTER TABLE `adm_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `goods`
--
ALTER TABLE `goods`
  ADD PRIMARY KEY (`id`,`sku_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_qrcode`
--
ALTER TABLE `group_qrcode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unionid` (`unionid`),
  ADD KEY `goods_id` (`goods_id`);

--
-- Indexes for table `order_helps`
--
ALTER TABLE `order_helps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unionid` (`unionid`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1906;

--
-- 使用表AUTO_INCREMENT `adm_user`
--
ALTER TABLE `adm_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `goods`
--
ALTER TABLE `goods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- 使用表AUTO_INCREMENT `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=295;

--
-- 使用表AUTO_INCREMENT `group_qrcode`
--
ALTER TABLE `group_qrcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- 使用表AUTO_INCREMENT `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2539;

--
-- 使用表AUTO_INCREMENT `order_helps`
--
ALTER TABLE `order_helps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1417;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4630;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
