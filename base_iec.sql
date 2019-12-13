-- MySQL dump 10.16  Distrib 10.3.10-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: rm-wz93t84m3dbwc4o47.mysql.rds.aliyuncs.com    Database: iec
-- ------------------------------------------------------
-- Server version	5.7.20-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `area`
--

DROP TABLE IF EXISTS `area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area` (
  `id` int(10) unsigned NOT NULL,
  `pid` int(10) unsigned DEFAULT NULL,
  `node` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` tinyint(4) NOT NULL,
  `lat` double(8,2) NOT NULL,
  `lng` double(8,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `area_lat_lng_index` (`lat`,`lng`),
  KEY `area_pid_index` (`pid`),
  KEY `area_name_index` (`name`),
  KEY `area_level_index` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_bank_income`
--

DROP TABLE IF EXISTS `iec_bank_income`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_bank_income` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '收益名称',
  `day` int(11) NOT NULL DEFAULT '0' COMMENT '收益周期',
  `num` smallint(6) NOT NULL DEFAULT '0' COMMENT '次数',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_bank_log`
--

DROP TABLE IF EXISTS `iec_bank_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_bank_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `type` tinyint(3) NOT NULL COMMENT '类型',
  `has_id` int(11) NOT NULL COMMENT '关联ID',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `money` decimal(20,8) NOT NULL COMMENT '数量',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=458 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_bank_order`
--

DROP TABLE IF EXISTS `iec_bank_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_bank_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `rate` decimal(20,8) NOT NULL COMMENT '利率',
  `amount` decimal(20,8) NOT NULL COMMENT '数量',
  `status` tinyint(3) NOT NULL COMMENT '状态',
  `day` smallint(6) NOT NULL COMMENT '周期天数',
  `endtime` int(11) NOT NULL COMMENT '结束时间',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `symbol` varchar(50) NOT NULL DEFAULT '',
  `supernode_uid` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=411 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_bank_product`
--

DROP TABLE IF EXISTS `iec_bank_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_bank_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '名称',
  `symbol` varchar(255) NOT NULL COMMENT '币种标识',
  `amount` decimal(20,8) NOT NULL COMMENT '数量',
  `rate` decimal(20,8) NOT NULL COMMENT '年利率',
  `min_amount` decimal(20,8) NOT NULL COMMENT '最小数量',
  `max_amount` decimal(20,8) NOT NULL COMMENT '最大数量',
  `income_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '收益类型',
  `income_description` varchar(255) NOT NULL COMMENT '收益描述',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '产品类型',
  `fee` decimal(20,8) NOT NULL COMMENT '费用',
  `fee_explain` varchar(255) NOT NULL COMMENT '费用说明',
  `day` smallint(6) NOT NULL COMMENT '周期天数',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `statime` int(11) NOT NULL COMMENT '开始时间',
  `endtime` int(11) NOT NULL COMMENT '结束时间',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `user_amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `status` tinyint(4) NOT NULL DEFAULT '10',
  `super_rate` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_bank_profit`
--

DROP TABLE IF EXISTS `iec_bank_profit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_bank_profit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `product_id` int(11) NOT NULL COMMENT '产品ID',
  `order_id` int(11) NOT NULL COMMENT '订单ID',
  `amount` decimal(20,8) NOT NULL COMMENT '数量',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `symbol` varchar(50) NOT NULL DEFAULT '',
  `type` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2741 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_currency`
--

DROP TABLE IF EXISTS `iec_currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(32) NOT NULL COMMENT '英文缩写',
  `description` varchar(128) NOT NULL COMMENT '中文描述',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '最后修改时间',
  `status` smallint(3) DEFAULT '10' COMMENT '状态',
  `model` varchar(32) DEFAULT NULL COMMENT '币种模型',
  `icon` varchar(255) DEFAULT NULL COMMENT 'ICON',
  `weight` int(11) DEFAULT '1' COMMENT '排序权重',
  `fee_symbol` varchar(32) DEFAULT NULL COMMENT '手续费币种',
  `fee_withdraw_amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '提现手续费金额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COMMENT='币种列表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_currency_param`
--

DROP TABLE IF EXISTS `iec_currency_param`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_currency_param` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL COMMENT '币种ID',
  `symbol` varchar(32) NOT NULL COMMENT '币种标识',
  `key` varchar(128) NOT NULL COMMENT '配置名',
  `value` text COMMENT '配置值',
  `updated_at` int(11) DEFAULT NULL COMMENT '更改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COMMENT='币种配置';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_currency_price`
--

DROP TABLE IF EXISTS `iec_currency_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_currency_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL COMMENT '币种ID',
  `symbol` varchar(32) NOT NULL COMMENT '币种标识',
  `price` decimal(20,8) DEFAULT '0.00000000' COMMENT '中文描述',
  `updated_at` int(11) DEFAULT NULL COMMENT '最后修改时间',
  `updated_date` int(11) DEFAULT NULL COMMENT '最后修改日期',
  `source` varchar(128) DEFAULT NULL COMMENT '来源标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COMMENT='币种价格';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_deposit`
--

DROP TABLE IF EXISTS `iec_deposit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_deposit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `wallet_id` int(11) DEFAULT NULL COMMENT '钱包ID',
  `symbol` varchar(32) NOT NULL COMMENT '币种标识',
  `amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '金额',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '最后修改时间',
  `status` smallint(3) DEFAULT '10' COMMENT '状态',
  `source` varchar(128) DEFAULT NULL COMMENT '来源标识',
  `txid` varchar(64) DEFAULT NULL COMMENT '交易ID',
  `address_id` int(11) NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `fee` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `fee_symbol` varchar(50) NOT NULL DEFAULT '',
  `remark` text,
  `transaction_hash` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13027 DEFAULT CHARSET=utf8mb4 COMMENT='充值记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_eth_collect`
--

DROP TABLE IF EXISTS `iec_eth_collect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_eth_collect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` varchar(32) NOT NULL COMMENT '币种标识',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `from_address` varchar(255) NOT NULL COMMENT '钱包地址',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` smallint(3) DEFAULT '0' COMMENT '任务状态',
  `gas_cost` decimal(20,8) DEFAULT '0.00000000' COMMENT '归集GAS',
  `gas_amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '归集金额',
  `gas_time` int(11) DEFAULT NULL COMMENT '归集提交时间',
  `gas_tx_hash` varchar(255) DEFAULT NULL COMMENT '归集交易哈希',
  `gas_tx_status` smallint(3) DEFAULT '0' COMMENT '归集交易状态',
  `collect_amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '归集金额',
  `collect_time` int(11) DEFAULT NULL COMMENT '归集提交时间',
  `collect_tx_hash` varchar(255) DEFAULT NULL COMMENT '归集交易哈希',
  `collect_tx_status` smallint(3) DEFAULT '0' COMMENT '归集交易状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_eth_gas`
--

DROP TABLE IF EXISTS `iec_eth_gas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_eth_gas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tx_hash` varchar(255) DEFAULT NULL COMMENT '交易哈希',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `type` int(11) DEFAULT '0' COMMENT '类别',
  `business_sn` varchar(128) DEFAULT NULL COMMENT '业务单号',
  `amount` decimal(20,8) DEFAULT NULL COMMENT '金额',
  `gas_used` varchar(128) DEFAULT NULL COMMENT '实际使用GAS数',
  `gas_price` varchar(128) DEFAULT NULL COMMENT 'GAS单价',
  `desc` varchar(255) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_eth_transaction`
--

DROP TABLE IF EXISTS `iec_eth_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_eth_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `block_number` int(11) DEFAULT NULL COMMENT '区块编号',
  `from_address` varchar(255) DEFAULT NULL COMMENT '转出地址',
  `to_address` varchar(255) DEFAULT NULL COMMENT '接收地址',
  `created_at` int(11) DEFAULT NULL COMMENT '交易时间',
  `transaction_hash` varchar(255) DEFAULT NULL COMMENT '交易哈希',
  `nonce` varchar(64) DEFAULT '0' COMMENT '同交易生效序列',
  `block_hash` varchar(255) DEFAULT NULL COMMENT '块哈希',
  `transaction_index` int(11) DEFAULT NULL COMMENT '交易序列',
  `value` varchar(64) DEFAULT '0' COMMENT '金额',
  `gas` varchar(64) DEFAULT NULL COMMENT '提交GAS',
  `gas_price` varchar(64) DEFAULT NULL COMMENT 'GAS单价',
  `is_error` int(11) DEFAULT NULL COMMENT '是否错误',
  `txreceipt_status` int(11) DEFAULT NULL COMMENT '交易接收状态',
  `input` text COMMENT '交易附加信息',
  `contract_address` varchar(255) DEFAULT NULL COMMENT '合约地址',
  `cumulative_gas_used` varchar(64) DEFAULT NULL COMMENT '累计GAS开销',
  `gas_used` varchar(64) DEFAULT NULL COMMENT 'GAS开销',
  `confirmations` int(11) DEFAULT '0' COMMENT '确认次数',
  `contract_to` varchar(255) DEFAULT NULL COMMENT '合约收款地址',
  `contract_value` varchar(64) DEFAULT NULL COMMENT '合约金额',
  `status` tinyint(3) DEFAULT '0' COMMENT '状态',
  `type` tinyint(3) DEFAULT '0' COMMENT '类型',
  PRIMARY KEY (`id`),
  KEY `index_from` (`from_address`),
  KEY `index_to` (`to_address`)
) ENGINE=InnoDB AUTO_INCREMENT=636 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_friend_moment`
--

DROP TABLE IF EXISTS `iec_friend_moment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_friend_moment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) NOT NULL COMMENT 'userid',
  `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '类型',
  `content` text COMMENT '正文内容',
  `linkid` int(11) NOT NULL DEFAULT '0' COMMENT '链接ID',
  `address` varchar(255) NOT NULL COMMENT '地址',
  `status` tinyint(3) NOT NULL DEFAULT '10' COMMENT '状态',
  `created_at` int(11) NOT NULL,
  `sort` tinyint(4) NOT NULL DEFAULT '0',
  `hot` int(11) NOT NULL DEFAULT '0',
  `like` int(11) NOT NULL DEFAULT '0',
  `reply` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1003 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_friend_moment_like`
--

DROP TABLE IF EXISTS `iec_friend_moment_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_friend_moment_like` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `momentid` int(11) NOT NULL COMMENT 'ID',
  `userid` varchar(255) NOT NULL COMMENT 'userid',
  `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '类型',
  `amount` decimal(20,8) NOT NULL DEFAULT '0.00000000' COMMENT '币赞数量',
  `status` tinyint(3) NOT NULL DEFAULT '10' COMMENT '状态',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1823 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_friend_moment_message`
--

DROP TABLE IF EXISTS `iec_friend_moment_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_friend_moment_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) NOT NULL COMMENT 'userid',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '类型',
  `momentid` int(11) NOT NULL COMMENT '朋友圈ID',
  `moment_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '朋友圈类型',
  `in_userid` varchar(255) NOT NULL COMMENT '用户userid',
  `to_userid` varchar(255) NOT NULL COMMENT '对方userid',
  `is_reply` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否回复',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  `moment_content` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1416 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_friend_moment_reply`
--

DROP TABLE IF EXISTS `iec_friend_moment_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_friend_moment_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `momentid` int(11) NOT NULL COMMENT 'ID',
  `in_userid` varchar(255) NOT NULL COMMENT '回复人userid',
  `to_userid` varchar(255) NOT NULL COMMENT '对象人userid',
  `content` varchar(255) NOT NULL COMMENT '回复内容',
  `status` tinyint(3) NOT NULL DEFAULT '10' COMMENT '状态',
  `created_at` int(11) NOT NULL,
  `is_reply` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=492 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_friend_moment_user`
--

DROP TABLE IF EXISTS `iec_friend_moment_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_friend_moment_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) NOT NULL COMMENT '自己userid',
  `moment_id` int(11) NOT NULL COMMENT '朋友圈ID',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19633 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_gift_money`
--

DROP TABLE IF EXISTS `iec_gift_money`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_gift_money` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) DEFAULT NULL COMMENT '发红包用户ID',
  `amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '金额',
  `amount_left` decimal(20,8) DEFAULT '0.00000000' COMMENT '剩余金额',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `status` smallint(3) DEFAULT '10' COMMENT '状态',
  `type` smallint(3) DEFAULT '10' COMMENT '类型',
  `amount_unit` decimal(20,8) DEFAULT '0.00000000' COMMENT '单个金额',
  `count` int(11) DEFAULT '0' COMMENT '红包个数',
  `expired_at` int(11) DEFAULT NULL COMMENT '过期时间',
  `description` varchar(255) DEFAULT NULL COMMENT '红包祝福语',
  `bind_taker` varchar(255) NOT NULL COMMENT '绑定接收对象',
  `symbol` varchar(32) DEFAULT NULL COMMENT '币种标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1488 DEFAULT CHARSET=utf8mb4 COMMENT='红包';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_gift_money_taker`
--

DROP TABLE IF EXISTS `iec_gift_money_taker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_gift_money_taker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taker_id` int(11) DEFAULT NULL COMMENT '领红包用户ID',
  `created_at` int(11) DEFAULT NULL COMMENT '领取时间',
  `amount` decimal(20,8) DEFAULT NULL COMMENT '领取金额',
  `gift_money_id` int(11) NOT NULL COMMENT '红包ID',
  `reply` varchar(255) DEFAULT NULL COMMENT '领取者回复',
  `reply_time` int(11) DEFAULT NULL COMMENT '回复时间',
  `symbol` varchar(32) DEFAULT NULL COMMENT '币种标识',
  `taken_at` int(11) DEFAULT NULL COMMENT '领取时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33499 DEFAULT CHARSET=utf8mb4 COMMENT='红包领取记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_group`
--

DROP TABLE IF EXISTS `iec_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` varchar(255) NOT NULL COMMENT '群ID',
  `name` varchar(255) NOT NULL COMMENT '群名称',
  `groupimgurl` varchar(255) NOT NULL COMMENT '群头像',
  `sort` tinyint(3) NOT NULL DEFAULT '0' COMMENT '排名',
  `hot` smallint(6) NOT NULL DEFAULT '0' COMMENT '热度',
  `nums` smallint(6) NOT NULL DEFAULT '1' COMMENT '群人数',
  `description` varchar(255) DEFAULT NULL COMMENT '个性说明',
  `status` smallint(6) NOT NULL DEFAULT '1',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `createid` varchar(255) NOT NULL DEFAULT '',
  `max_nums` smallint(6) NOT NULL DEFAULT '3000',
  `is_ban` tinyint(4) NOT NULL DEFAULT '0',
  `is_hot_show` tinyint(1) NOT NULL DEFAULT '1',
  `is_pull` tinyint(1) NOT NULL DEFAULT '1',
  `is_verify` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_group_user`
--

DROP TABLE IF EXISTS `iec_group_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_group_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` varchar(255) NOT NULL DEFAULT '',
  `userid` varchar(255) NOT NULL DEFAULT '' COMMENT '用户userid',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `permission` tinyint(3) NOT NULL DEFAULT '1' COMMENT '权限',
  `msg` tinyint(3) NOT NULL DEFAULT '1' COMMENT '消息免打扰',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态',
  `created_at` int(11) NOT NULL,
  `is_ban` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `index_name` (`groupid`)
) ENGINE=InnoDB AUTO_INCREMENT=14117 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_image`
--

DROP TABLE IF EXISTS `iec_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '类型',
  `origin` int(11) NOT NULL COMMENT '来源ID',
  `url` varchar(255) NOT NULL COMMENT '地址',
  `thumbnail` varchar(255) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '10' COMMENT '状态',
  `created_at` int(11) NOT NULL,
  `width` int(11) NOT NULL DEFAULT '0',
  `height` int(11) NOT NULL DEFAULT '0',
  `userid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1575 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_invitation`
--

DROP TABLE IF EXISTS `iec_invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registerer_id` int(11) DEFAULT NULL COMMENT '注册人ID',
  `inviter_id` int(11) DEFAULT NULL COMMENT '邀请人ID',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `level` int(11) DEFAULT '1' COMMENT '层级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=420700 DEFAULT CHARSET=utf8mb4 COMMENT='注册邀请记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_invite_pool`
--

DROP TABLE IF EXISTS `iec_invite_pool`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_invite_pool` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL COMMENT '币种ID',
  `symbol` varchar(32) NOT NULL COMMENT '币种标识',
  `amount` decimal(28,8) DEFAULT '0.00000000' COMMENT '奖金池金额',
  `amount_left` decimal(28,8) DEFAULT '0.00000000' COMMENT '奖金池剩余',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `expired_at` int(11) DEFAULT NULL COMMENT '过期时间',
  `status` smallint(3) DEFAULT '10' COMMENT '状态',
  `prize` decimal(28,8) DEFAULT '0.00000000' COMMENT '奖金包金额',
  `prize_registerer` int(11) DEFAULT NULL COMMENT '注册人比重',
  `prize_inviter` int(11) DEFAULT NULL COMMENT '邀请人比重',
  `prize_grand_inviter` int(11) DEFAULT NULL COMMENT '父级邀请人比重',
  `prize_grand_grand_inviter` int(11) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(255) NOT NULL DEFAULT '',
  `background` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COMMENT='红包领取记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_invite_pool_log`
--

DROP TABLE IF EXISTS `iec_invite_pool_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_invite_pool_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `pool_id` int(11) NOT NULL COMMENT '糖果ID',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '类型，0:加款',
  `symbol` varchar(50) NOT NULL COMMENT '标示',
  `amount` decimal(20,8) NOT NULL COMMENT '数量',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_invite_reward`
--

DROP TABLE IF EXISTS `iec_invite_reward`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_invite_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invitation_id` int(11) DEFAULT NULL COMMENT '邀请记录ID',
  `level` int(11) DEFAULT '1' COMMENT '层级',
  `currency_id` int(11) NOT NULL COMMENT '币种ID',
  `symbol` varchar(32) NOT NULL COMMENT '币种标识',
  `amount` decimal(20,8) DEFAULT NULL COMMENT '奖励金额',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `user_id_rewarded` int(11) DEFAULT NULL COMMENT '收益人',
  `registerer_id` int(11) DEFAULT NULL COMMENT '注册人ID',
  `registerer_reward` decimal(20,8) DEFAULT NULL COMMENT '注册人得奖',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=598840 DEFAULT CHARSET=utf8mb4 COMMENT='邀请注册奖励记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_message`
--

DROP TABLE IF EXISTS `iec_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `source_id` int(11) NOT NULL COMMENT '来源ID',
  `type` tinyint(3) NOT NULL COMMENT '类型',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `symbol` varchar(50) NOT NULL COMMENT '币种',
  `amount` decimal(20,8) NOT NULL DEFAULT '0.00000000' COMMENT '数量',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33819 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_moment_banner`
--

DROP TABLE IF EXISTS `iec_moment_banner`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_moment_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `url` varchar(255) NOT NULL COMMENT '广告地址',
  `link` varchar(255) NOT NULL COMMENT '链接地址',
  `sort` tinyint(3) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_poster`
--

DROP TABLE IF EXISTS `iec_poster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_poster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '海报名称',
  `url` varchar(255) NOT NULL COMMENT '地址',
  `sort` smallint(6) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) NOT NULL DEFAULT '10' COMMENT '状态',
  `created_at` int(11) NOT NULL,
  `endtime_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_setting`
--

DROP TABLE IF EXISTS `iec_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT '昵称',
  `key` varchar(255) NOT NULL COMMENT '键名',
  `value` text COMMENT '值',
  `group` varchar(255) NOT NULL COMMENT '分组',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_sms`
--

DROP TABLE IF EXISTS `iec_sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '短信类型',
  `phone` char(11) NOT NULL COMMENT '手机号码',
  `code` char(6) NOT NULL COMMENT '验证码',
  `status` smallint(6) NOT NULL COMMENT '返回状态吗',
  `response` varchar(255) NOT NULL COMMENT '返回信息',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=256797 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_start_page`
--

DROP TABLE IF EXISTS `iec_start_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_start_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img` varchar(255) NOT NULL COMMENT '图片',
  `name` varchar(255) NOT NULL COMMENT '广告名称',
  `type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '类型:1安卓/10 IOS',
  `sort` int(11) NOT NULL COMMENT '排序',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态:1启动/10禁用',
  `time` int(11) NOT NULL DEFAULT '3' COMMENT '广告时间',
  `redirecturl` varchar(255) DEFAULT NULL COMMENT '广告url',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_supernode`
--

DROP TABLE IF EXISTS `iec_supernode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_supernode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `status` tinyint(3) NOT NULL DEFAULT '10' COMMENT '状态：10开启/0关闭',
  `lvl` smallint(5) NOT NULL DEFAULT '0' COMMENT '超级节点等级',
  `amount` decimal(20,8) NOT NULL DEFAULT '0.00000000' COMMENT '数量',
  `description` varchar(255) NOT NULL COMMENT '描述',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=158 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_supernode_profit`
--

DROP TABLE IF EXISTS `iec_supernode_profit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_supernode_profit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `in_uid` int(11) NOT NULL COMMENT '购买者用户ID',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `node` tinyint(3) NOT NULL DEFAULT '0' COMMENT '普通节点',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '收益类型',
  `symbol` varchar(50) NOT NULL COMMENT '标志',
  `hasid` int(11) NOT NULL COMMENT '关联ID',
  `amount` decimal(20,8) NOT NULL COMMENT '数量',
  `status` tinyint(3) NOT NULL DEFAULT '10' COMMENT '状态',
  `created_at` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5855 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_transfer`
--

DROP TABLE IF EXISTS `iec_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) DEFAULT NULL COMMENT '转出人ID',
  `receiver_id` int(11) DEFAULT NULL COMMENT '转入人ID',
  `symbol` varchar(32) NOT NULL COMMENT '币种标识',
  `currency_id` int(11) NOT NULL COMMENT '币种ID',
  `amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '金额',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `taken_at` int(11) DEFAULT NULL COMMENT '接收时间',
  `status` smallint(3) DEFAULT '10' COMMENT '状态',
  `description` varchar(255) NOT NULL COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=116599 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_user`
--

DROP TABLE IF EXISTS `iec_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upid` int(11) NOT NULL DEFAULT '0' COMMENT '上级用户ID',
  `area` smallint(6) NOT NULL DEFAULT '86' COMMENT '国家区号',
  `initials` char(1) NOT NULL COMMENT '首字母',
  `username` varchar(255) NOT NULL,
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `iecid` varchar(50) NOT NULL,
  `userid` varchar(255) DEFAULT '' COMMENT 'userID',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '用户头像',
  `access_token` varchar(255) DEFAULT '' COMMENT 'token',
  `auth_key` varchar(32) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL COMMENT '经度',
  `latitude` varchar(255) DEFAULT NULL COMMENT '纬度',
  `sex` tinyint(3) NOT NULL DEFAULT '0' COMMENT '性别',
  `age` smallint(6) NOT NULL DEFAULT '0' COMMENT '年龄',
  `country` varchar(255) DEFAULT NULL COMMENT '国家',
  `province` varchar(255) DEFAULT NULL COMMENT '省',
  `city` varchar(255) DEFAULT NULL COMMENT '城市',
  `status` smallint(6) NOT NULL DEFAULT '10',
  `code` char(6) NOT NULL COMMENT '邀请码',
  `description` varchar(255) DEFAULT NULL COMMENT '个性说明',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `friend` tinyint(4) NOT NULL DEFAULT '0',
  `payment_hash` varchar(255) DEFAULT NULL,
  `is_iec` tinyint(4) NOT NULL DEFAULT '0',
  `is_wallet_protocol` tinyint(4) NOT NULL DEFAULT '0',
  `area_id` int(11) NOT NULL DEFAULT '0',
  `crontab_status` tinyint(4) NOT NULL DEFAULT '0',
  `pool_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `iecid` (`iecid`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `userid` (`userid`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `idxApiToken` (`access_token`,`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=209891 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_user_address`
--

DROP TABLE IF EXISTS `iec_user_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_user_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `name` varchar(64) NOT NULL COMMENT '用户备注',
  `address` varchar(128) NOT NULL COMMENT '钱包地址',
  `created_at` int(11) DEFAULT NULL COMMENT '钱包地址',
  `model` varchar(32) DEFAULT NULL COMMENT '币种模型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=455 DEFAULT CHARSET=utf8mb4 COMMENT='用户地址钱包';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_user_friend`
--

DROP TABLE IF EXISTS `iec_user_friend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_user_friend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `in_userid` varchar(255) NOT NULL COMMENT '用户userid',
  `to_userid` varchar(255) NOT NULL COMMENT '好友userid',
  `status` smallint(6) NOT NULL DEFAULT '1',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8320 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_user_tree`
--

DROP TABLE IF EXISTS `iec_user_tree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_user_tree` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `root` int(11) DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `lvl` smallint(5) NOT NULL,
  `name` varchar(60) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `icon_type` smallint(1) NOT NULL DEFAULT '1',
  `node` int(11) NOT NULL DEFAULT '0',
  `userid` varchar(255) NOT NULL COMMENT '用户userid',
  `uid` int(11) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `selected` tinyint(1) NOT NULL DEFAULT '0',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `readonly` tinyint(1) NOT NULL DEFAULT '0',
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `collapsed` tinyint(1) NOT NULL DEFAULT '0',
  `movable_u` tinyint(1) NOT NULL DEFAULT '1',
  `movable_d` tinyint(1) NOT NULL DEFAULT '1',
  `movable_l` tinyint(1) NOT NULL DEFAULT '1',
  `movable_r` tinyint(1) NOT NULL DEFAULT '1',
  `removable` tinyint(1) NOT NULL DEFAULT '1',
  `removable_all` tinyint(1) NOT NULL DEFAULT '0',
  `child_allowed` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` int(11) NOT NULL DEFAULT '0',
  `node_lvl` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tree_NK1` (`root`),
  KEY `tree_NK2` (`lft`),
  KEY `tree_NK3` (`rgt`),
  KEY `tree_NK4` (`lvl`),
  KEY `tree_NK5` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=189361 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_version`
--

DROP TABLE IF EXISTS `iec_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '类型',
  `num` int(11) NOT NULL COMMENT '版本ID',
  `update` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否强制更新',
  `version` varchar(255) NOT NULL COMMENT '版本号',
  `size` int(11) NOT NULL COMMENT '大小',
  `url` varchar(255) NOT NULL COMMENT '下载URL',
  `content` text,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_wallet`
--

DROP TABLE IF EXISTS `iec_wallet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_wallet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `symbol` varchar(32) DEFAULT NULL COMMENT '币种标识',
  `amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '金额',
  `amount_lock` decimal(20,8) DEFAULT '0.00000000' COMMENT '锁定金额',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间',
  `is_displayed` smallint(3) DEFAULT '10' COMMENT '开放显示',
  `weight` int(11) DEFAULT '1' COMMENT '排序权重',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=726690 DEFAULT CHARSET=utf8mb4 COMMENT='用户账户钱包';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_wallet_address`
--

DROP TABLE IF EXISTS `iec_wallet_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_wallet_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `currency_id` int(11) NOT NULL COMMENT '币种ID',
  `type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '类型',
  `symbol` varchar(32) NOT NULL COMMENT '英文缩写',
  `address` varchar(255) DEFAULT NULL COMMENT '钱包地址',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `model` varchar(32) NOT NULL DEFAULT '',
  `amount` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `passwd` varchar(64) DEFAULT NULL COMMENT '密钥',
  `balance_changed` smallint(3) DEFAULT NULL COMMENT '金额变动',
  `private_key` varchar(255) DEFAULT NULL COMMENT '私钥',
  `pulled_at` int(11) DEFAULT NULL COMMENT '更新时间',
  `lock_collect` smallint(3) DEFAULT '0' COMMENT '归集锁',
  PRIMARY KEY (`id`),
  KEY `userSymbol` (`user_id`,`symbol`)
) ENGINE=InnoDB AUTO_INCREMENT=135875 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_wallet_log`
--

DROP TABLE IF EXISTS `iec_wallet_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_wallet_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wallet_id` int(11) DEFAULT NULL COMMENT '钱包ID',
  `symbol` varchar(32) DEFAULT NULL COMMENT '币种标识',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `type` int(11) DEFAULT NULL COMMENT '类型',
  `amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '金额',
  `balance` decimal(20,8) DEFAULT '0.00000000' COMMENT '帐变前余额',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `remark` text COMMENT '备注',
  `business_sn` varchar(255) DEFAULT NULL COMMENT '业务单号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=864362 DEFAULT CHARSET=utf8mb4 COMMENT='用户账户钱包';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `iec_withdraw`
--

DROP TABLE IF EXISTS `iec_withdraw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `iec_withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  `wallet_id` int(11) DEFAULT NULL COMMENT '钱包ID',
  `address_id` int(11) NOT NULL COMMENT '钱包地址ID',
  `address` varchar(128) NOT NULL COMMENT '钱包地址',
  `symbol` varchar(32) NOT NULL COMMENT '币种标识',
  `amount` decimal(20,8) DEFAULT '0.00000000' COMMENT '金额',
  `fee` decimal(20,8) DEFAULT '0.00000000',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '最后修改时间',
  `status` smallint(3) DEFAULT '10' COMMENT '状态',
  `checker_id` int(11) DEFAULT NULL COMMENT '审核人ID',
  `check_time` int(11) DEFAULT NULL COMMENT '审核时间',
  `source` varchar(128) DEFAULT NULL COMMENT '来源标识',
  `remark` text COMMENT '备注',
  `fee_symbol` varchar(32) DEFAULT NULL COMMENT '手续费币种',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 COMMENT='充值记录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `level` int(11) DEFAULT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `log_time` double DEFAULT NULL,
  `prefix` text COLLATE utf8_unicode_ci,
  `message` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_log_level` (`level`),
  KEY `idx_log_category` (`category`)
) ENGINE=InnoDB AUTO_INCREMENT=129659 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menu`
--

DROP TABLE IF EXISTS `menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `parent` int(11) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `order` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`),
  KEY `parent` (`parent`),
  CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `menu` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-02-14 18:14:07
