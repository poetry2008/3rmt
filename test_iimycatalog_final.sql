-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2010 年 07 月 29 日 10:19
-- 服务器版本: 5.1.46
-- PHP 版本: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `test_iimycatalog_final`
--

-- --------------------------------------------------------

--
-- 表的结构 `address_book`
--

CREATE TABLE IF NOT EXISTS `address_book` (
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `address_book_id` int(11) NOT NULL DEFAULT '1',
  `entry_gender` char(1) NOT NULL DEFAULT '',
  `entry_company` varchar(32) DEFAULT NULL,
  `entry_firstname` varchar(32) NOT NULL DEFAULT '',
  `entry_lastname` varchar(32) NOT NULL DEFAULT '',
  `entry_firstname_f` varchar(32) NOT NULL DEFAULT '',
  `entry_lastname_f` varchar(32) NOT NULL DEFAULT '',
  `entry_street_address` varchar(64) NOT NULL DEFAULT '',
  `entry_suburb` varchar(32) DEFAULT NULL,
  `entry_postcode` varchar(10) NOT NULL DEFAULT '',
  `entry_city` varchar(32) NOT NULL DEFAULT '',
  `entry_state` varchar(32) DEFAULT NULL,
  `entry_country_id` int(11) NOT NULL DEFAULT '0',
  `entry_telephone` varchar(32) DEFAULT NULL,
  `entry_zone_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customers_id`,`address_book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `address_format`
--

CREATE TABLE IF NOT EXISTS `address_format` (
  `address_format_id` int(11) NOT NULL AUTO_INCREMENT,
  `address_format` varchar(128) NOT NULL DEFAULT '',
  `address_summary` varchar(48) NOT NULL DEFAULT '',
  PRIMARY KEY (`address_format_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- 表的结构 `banners`
--

CREATE TABLE IF NOT EXISTS `banners` (
  `banners_id` int(11) NOT NULL AUTO_INCREMENT,
  `banners_title` varchar(64) NOT NULL DEFAULT '',
  `banners_url` varchar(255) NOT NULL DEFAULT '',
  `banners_image` varchar(64) NOT NULL DEFAULT '',
  `banners_group` varchar(10) NOT NULL DEFAULT '',
  `banners_html_text` text,
  `expires_impressions` int(7) DEFAULT '0',
  `expires_date` datetime DEFAULT NULL,
  `date_scheduled` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_status_change` datetime DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`banners_id`,`site_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- 表的结构 `banners_history`
--

CREATE TABLE IF NOT EXISTS `banners_history` (
  `banners_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `banners_id` int(11) NOT NULL DEFAULT '0',
  `banners_shown` int(5) NOT NULL DEFAULT '0',
  `banners_clicked` int(5) NOT NULL DEFAULT '0',
  `banners_history_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`banners_history_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=72 ;

-- --------------------------------------------------------

--
-- 表的结构 `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `cache_id` varchar(32) NOT NULL DEFAULT '',
  `cache_language_id` tinyint(1) NOT NULL DEFAULT '0',
  `cache_name` varchar(255) NOT NULL DEFAULT '',
  `cache_data` mediumtext NOT NULL,
  `cache_global` tinyint(1) NOT NULL DEFAULT '1',
  `cache_gzip` tinyint(1) NOT NULL DEFAULT '1',
  `cache_method` varchar(20) NOT NULL DEFAULT 'RETURN',
  `cache_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cache_expires` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`cache_id`,`cache_language_id`),
  KEY `cache_id` (`cache_id`),
  KEY `cache_language_id` (`cache_language_id`),
  KEY `cache_global` (`cache_global`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `calendar`
--

CREATE TABLE IF NOT EXISTS `calendar` (
  `cl_id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_ym` varchar(64) NOT NULL DEFAULT '',
  `cl_value` varchar(64) NOT NULL DEFAULT '',
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`cl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=241 ;

-- --------------------------------------------------------

--
-- 表的结构 `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `categories_id` int(11) NOT NULL AUTO_INCREMENT,
  `categories_status` tinyint(1) DEFAULT '0',
  `categories_image` varchar(64) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(3) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`categories_id`),
  KEY `idx_categories_parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=484 ;

-- --------------------------------------------------------

--
-- 表的结构 `categories_description`
--

CREATE TABLE IF NOT EXISTS `categories_description` (
  `categories_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL DEFAULT '1',
  `categories_name` varchar(32) NOT NULL DEFAULT '',
  `seo_name` varchar(32) NOT NULL DEFAULT '',
  `categories_image2` varchar(64) DEFAULT NULL,
  `categories_image3` varchar(64) DEFAULT NULL,
  `categories_meta_text` text NOT NULL,
  `seo_description` text NOT NULL,
  `categories_header_text` text NOT NULL,
  `categories_footer_text` text NOT NULL,
  `text_information` text NOT NULL,
  `meta_keywords` text NOT NULL,
  `meta_description` text NOT NULL,
  PRIMARY KEY (`categories_id`,`site_id`,`language_id`),
  KEY `idx_categories_name` (`categories_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `categories_rss`
--

CREATE TABLE IF NOT EXISTS `categories_rss` (
  `categories_id` int(11) NOT NULL,
  `categories_rss` varchar(255) NOT NULL,
  PRIMARY KEY (`categories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `color`
--

CREATE TABLE IF NOT EXISTS `color` (
  `color_id` int(11) NOT NULL AUTO_INCREMENT,
  `color_tag` varchar(255) NOT NULL DEFAULT '',
  `color_name` varchar(64) NOT NULL DEFAULT '',
  `sort_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`color_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- 表的结构 `color_to_products`
--

CREATE TABLE IF NOT EXISTS `color_to_products` (
  `color_id` int(11) NOT NULL DEFAULT '0',
  `categories_id` int(11) NOT NULL DEFAULT '0',
  `products_id` varchar(255) NOT NULL DEFAULT '',
  `color_image` varchar(64) NOT NULL DEFAULT '',
  `color_to_products_name` varchar(255) NOT NULL DEFAULT '',
  `manufacturers_id` varchar(255) NOT NULL DEFAULT '',
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`cid`),
  KEY `categories_id` (`categories_id`,`products_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;

-- --------------------------------------------------------

--
-- 表的结构 `configuration`
--

CREATE TABLE IF NOT EXISTS `configuration` (
  `configuration_id` int(11) NOT NULL AUTO_INCREMENT,
  `configuration_title` varchar(64) NOT NULL DEFAULT '',
  `configuration_key` varchar(64) NOT NULL DEFAULT '',
  `configuration_value` text NOT NULL,
  `configuration_description` varchar(255) NOT NULL DEFAULT '',
  `configuration_group_id` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(5) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `use_function` varchar(255) DEFAULT NULL,
  `set_function` varchar(255) DEFAULT NULL,
  `site_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`configuration_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1146 ;

-- --------------------------------------------------------

--
-- 表的结构 `configuration_group`
--

CREATE TABLE IF NOT EXISTS `configuration_group` (
  `configuration_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `configuration_group_title` varchar(64) NOT NULL DEFAULT '',
  `configuration_group_description` varchar(255) NOT NULL DEFAULT '',
  `sort_order` int(5) DEFAULT NULL,
  `visible` int(1) DEFAULT '1',
  PRIMARY KEY (`configuration_group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2028 ;

-- --------------------------------------------------------

--
-- 表的结构 `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `contents_id` int(11) NOT NULL AUTO_INCREMENT,
  `contents_title` text NOT NULL,
  `type` int(64) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `default_contents` text NOT NULL,
  `fixed_contents` text NOT NULL,
  `lastupdate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `site_id` datetime NOT NULL,
  PRIMARY KEY (`contents_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `countries_id` int(11) NOT NULL AUTO_INCREMENT,
  `countries_name` varchar(64) NOT NULL DEFAULT '',
  `countries_iso_code_2` char(2) NOT NULL DEFAULT '',
  `countries_iso_code_3` char(3) NOT NULL DEFAULT '',
  `address_format_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`countries_id`),
  KEY `IDX_COUNTRIES_NAME` (`countries_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=240 ;

-- --------------------------------------------------------

--
-- 表的结构 `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
  `currencies_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL DEFAULT '',
  `code` char(3) NOT NULL DEFAULT '',
  `symbol_left` varchar(12) DEFAULT NULL,
  `symbol_right` varchar(12) DEFAULT NULL,
  `decimal_point` char(1) DEFAULT NULL,
  `thousands_point` char(1) DEFAULT NULL,
  `decimal_places` char(1) DEFAULT NULL,
  `value` float(13,8) DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`currencies_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- 表的结构 `customers`
--

CREATE TABLE IF NOT EXISTS `customers` (
  `customers_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_gender` char(1) NOT NULL DEFAULT '',
  `customers_firstname` varchar(32) NOT NULL DEFAULT '',
  `customers_lastname` varchar(32) NOT NULL DEFAULT '',
  `customers_firstname_f` varchar(255) NOT NULL DEFAULT '',
  `customers_lastname_f` varchar(255) NOT NULL DEFAULT '',
  `customers_dob` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `customers_email_address` varchar(96) NOT NULL DEFAULT '',
  `customers_default_address_id` int(5) NOT NULL DEFAULT '1',
  `customers_telephone` varchar(32) NOT NULL DEFAULT '',
  `customers_fax` varchar(255) DEFAULT NULL,
  `customers_password` varchar(40) NOT NULL DEFAULT '',
  `customers_newsletter` char(1) DEFAULT NULL,
  `point` int(11) DEFAULT '0',
  `site_id` int(11) DEFAULT NULL,
  `customers_guest_chk` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customers_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33527 ;

-- --------------------------------------------------------

--
-- 表的结构 `customers_basket`
--

CREATE TABLE IF NOT EXISTS `customers_basket` (
  `customers_basket_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `products_id` tinytext NOT NULL,
  `customers_basket_quantity` int(2) NOT NULL DEFAULT '0',
  `final_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `customers_basket_date_added` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`customers_basket_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9757 ;

-- --------------------------------------------------------

--
-- 表的结构 `customers_basket_attributes`
--

CREATE TABLE IF NOT EXISTS `customers_basket_attributes` (
  `customers_basket_attributes_id` int(11) NOT NULL AUTO_INCREMENT,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `products_id` tinytext NOT NULL,
  `products_options_id` int(11) NOT NULL DEFAULT '0',
  `products_options_value_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`customers_basket_attributes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9515 ;

-- --------------------------------------------------------

--
-- 表的结构 `customers_info`
--

CREATE TABLE IF NOT EXISTS `customers_info` (
  `customers_info_id` int(11) NOT NULL DEFAULT '0',
  `customers_info_date_of_last_logon` datetime DEFAULT NULL,
  `customers_info_number_of_logons` int(5) DEFAULT NULL,
  `customers_info_date_account_created` datetime DEFAULT NULL,
  `customers_info_date_account_last_modified` datetime DEFAULT NULL,
  `global_product_notifications` int(1) DEFAULT '0',
  PRIMARY KEY (`customers_info_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='one has one 会员信息';

-- --------------------------------------------------------

--
-- 表的结构 `faq_categories`
--

CREATE TABLE IF NOT EXISTS `faq_categories` (
  `c_id` int(8) NOT NULL AUTO_INCREMENT,
  `g_id` int(8) NOT NULL,
  `c_order` int(8) NOT NULL DEFAULT '0',
  `category` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`c_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=82 ;

-- --------------------------------------------------------

--
-- 表的结构 `faq_questions`
--

CREATE TABLE IF NOT EXISTS `faq_questions` (
  `q_id` int(8) NOT NULL AUTO_INCREMENT,
  `c_id` int(8) NOT NULL DEFAULT '0',
  `q_order` int(8) NOT NULL DEFAULT '0',
  `question` varchar(255) NOT NULL DEFAULT '',
  `answer` text NOT NULL,
  PRIMARY KEY (`q_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=379 ;

-- --------------------------------------------------------

--
-- 表的结构 `geo_zones`
--

CREATE TABLE IF NOT EXISTS `geo_zones` (
  `geo_zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `geo_zone_name` varchar(32) NOT NULL DEFAULT '',
  `geo_zone_description` varchar(255) NOT NULL DEFAULT '',
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`geo_zone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `image_documents`
--

CREATE TABLE IF NOT EXISTS `image_documents` (
  `documents_id` int(11) NOT NULL AUTO_INCREMENT,
  `documents_name` varchar(64) NOT NULL DEFAULT '',
  `documents_size` int(11) NOT NULL DEFAULT '0',
  `documents_title` varchar(64) NOT NULL,
  `document_types_id` int(11) NOT NULL,
  `documents_visible` set('True','False') NOT NULL DEFAULT 'True',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `last_updated` datetime DEFAULT NULL,
  KEY `documents_id` (`documents_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `image_document_types`
--

CREATE TABLE IF NOT EXISTS `image_document_types` (
  `document_types_id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(64) NOT NULL DEFAULT '',
  `type_description` varchar(64) NOT NULL,
  `type_visible` set('True','False') NOT NULL DEFAULT 'True',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  KEY `documents_id` (`document_types_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- 表的结构 `information_page`
--

CREATE TABLE IF NOT EXISTS `information_page` (
  `pID` int(11) NOT NULL AUTO_INCREMENT,
  `navbar_title` text NOT NULL,
  `heading_title` text NOT NULL,
  `text_information` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `sort_id` int(3) DEFAULT NULL,
  `romaji` varchar(255) NOT NULL,
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`pID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=43 ;

-- --------------------------------------------------------

--
-- 表的结构 `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `languages_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL DEFAULT '',
  `code` char(2) NOT NULL DEFAULT '',
  `image` varchar(64) DEFAULT NULL,
  `directory` varchar(32) DEFAULT NULL,
  `sort_order` int(3) DEFAULT NULL,
  PRIMARY KEY (`languages_id`),
  KEY `IDX_LANGUAGES_NAME` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- 表的结构 `latest_news`
--

CREATE TABLE IF NOT EXISTS `latest_news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `headline` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `news_image` varchar(64) DEFAULT NULL,
  `news_image_description` text NOT NULL,
  `site_id` int(11) NOT NULL,
  `isfirst` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`news_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=645 ;

-- --------------------------------------------------------

--
-- 表的结构 `login`
--

CREATE TABLE IF NOT EXISTS `login` (
  `sessionid` varchar(32) NOT NULL DEFAULT '',
  `logintime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastaccesstime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `account` varchar(16) DEFAULT NULL,
  `loginstatus` char(1) DEFAULT NULL,
  `logoutstatus` char(1) DEFAULT NULL,
  `address` int(11) DEFAULT NULL,
  PRIMARY KEY (`sessionid`),
  KEY `login_logintime_index` (`logintime`),
  KEY `login_lastaccesstime_index` (`lastaccesstime`),
  KEY `login_loginstatus_index` (`loginstatus`),
  KEY `login_logoutstatus_index` (`logoutstatus`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `mail_magazine`
--

CREATE TABLE IF NOT EXISTS `mail_magazine` (
  `mag_id` int(11) NOT NULL AUTO_INCREMENT,
  `mag_email` varchar(128) NOT NULL DEFAULT '',
  `mag_name` varchar(128) NOT NULL DEFAULT '',
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`mag_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- 表的结构 `manufacturers`
--

CREATE TABLE IF NOT EXISTS `manufacturers` (
  `manufacturers_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturers_name` varchar(32) NOT NULL DEFAULT '',
  `manufacturers_image` varchar(64) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  PRIMARY KEY (`manufacturers_id`),
  KEY `IDX_MANUFACTURERS_NAME` (`manufacturers_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- 表的结构 `manufacturers_info`
--

CREATE TABLE IF NOT EXISTS `manufacturers_info` (
  `manufacturers_id` int(11) NOT NULL DEFAULT '0',
  `languages_id` int(11) NOT NULL DEFAULT '0',
  `manufacturers_url` varchar(255) NOT NULL DEFAULT '',
  `url_clicked` int(5) NOT NULL DEFAULT '0',
  `date_last_click` datetime DEFAULT NULL,
  PRIMARY KEY (`manufacturers_id`,`languages_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `newsletters`
--

CREATE TABLE IF NOT EXISTS `newsletters` (
  `newsletters_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `module` varchar(255) NOT NULL DEFAULT '',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_sent` datetime DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `locked` int(1) DEFAULT '0',
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`newsletters_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `orders_id` char(32) NOT NULL DEFAULT '',
  `site_id` int(11) NOT NULL,
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `customers_name` varchar(64) NOT NULL DEFAULT '',
  `customers_name_f` varchar(64) NOT NULL DEFAULT '',
  `customers_company` varchar(32) DEFAULT NULL,
  `customers_street_address` varchar(64) NOT NULL DEFAULT '',
  `customers_suburb` varchar(32) DEFAULT NULL,
  `customers_city` varchar(32) NOT NULL DEFAULT '',
  `customers_postcode` varchar(10) NOT NULL DEFAULT '',
  `customers_state` varchar(32) DEFAULT NULL,
  `customers_country` varchar(32) NOT NULL DEFAULT '',
  `customers_telephone` varchar(32) NOT NULL DEFAULT '',
  `customers_email_address` varchar(96) NOT NULL DEFAULT '',
  `customers_address_format_id` int(5) NOT NULL DEFAULT '0',
  `delivery_name` varchar(64) NOT NULL DEFAULT '',
  `delivery_name_f` varchar(64) NOT NULL DEFAULT '',
  `delivery_company` varchar(32) DEFAULT NULL,
  `delivery_street_address` varchar(64) NOT NULL DEFAULT '',
  `delivery_suburb` varchar(32) DEFAULT NULL,
  `delivery_city` varchar(32) NOT NULL DEFAULT '',
  `delivery_postcode` varchar(10) NOT NULL DEFAULT '',
  `delivery_state` varchar(32) DEFAULT NULL,
  `delivery_country` varchar(32) NOT NULL DEFAULT '',
  `delivery_telephone` varchar(32) DEFAULT NULL,
  `delivery_address_format_id` int(5) NOT NULL DEFAULT '0',
  `billing_name` varchar(64) NOT NULL DEFAULT '',
  `billing_name_f` varchar(64) NOT NULL DEFAULT '',
  `billing_company` varchar(32) DEFAULT NULL,
  `billing_street_address` varchar(64) NOT NULL DEFAULT '',
  `billing_suburb` varchar(32) DEFAULT NULL,
  `billing_city` varchar(32) NOT NULL DEFAULT '',
  `billing_postcode` varchar(10) NOT NULL DEFAULT '',
  `billing_state` varchar(32) DEFAULT NULL,
  `billing_country` varchar(32) NOT NULL DEFAULT '',
  `billing_telephone` varchar(32) DEFAULT NULL,
  `billing_address_format_id` int(5) NOT NULL DEFAULT '0',
  `payment_method` varchar(32) NOT NULL DEFAULT '',
  `cc_type` varchar(20) DEFAULT NULL,
  `cc_owner` varchar(64) DEFAULT NULL,
  `cc_number` varchar(32) DEFAULT NULL,
  `cc_expires` varchar(4) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `date_purchased` datetime DEFAULT NULL,
  `orders_status` int(5) NOT NULL DEFAULT '0',
  `orders_date_finished` datetime DEFAULT NULL,
  `currency` char(3) DEFAULT NULL,
  `currency_value` decimal(14,6) DEFAULT NULL,
  `torihiki_Bahamut` varchar(255) NOT NULL DEFAULT '',
  `torihiki_houhou` varchar(255) NOT NULL DEFAULT '',
  `torihiki_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `code_fee` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) DEFAULT NULL,
  `orders_status_name` varchar(60) DEFAULT NULL,
  `orders_status_image` varchar(200) DEFAULT NULL,
  `finished` int(11) DEFAULT NULL,
  PRIMARY KEY (`orders_id`),
  KEY `torihiki_date` (`torihiki_date`),
  KEY `orders_id` (`orders_id`,`torihiki_date`),
  KEY `orders_id_4` (`orders_id`,`site_id`,`customers_id`,`customers_name`,`payment_method`,`last_modified`,`date_purchased`,`currency`,`currency_value`,`torihiki_date`),
  KEY `orders_id_5` (`orders_id`,`site_id`),
  KEY `orders_status` (`orders_status`),
  KEY `payment_method` (`payment_method`),
  KEY `orders_id_7` (`orders_id`,`site_id`,`orders_status`),
  KEY `orders_id_9` (`orders_id`,`payment_method`,`orders_status`),
  KEY `payment_method_2` (`payment_method`,`orders_status`),
  KEY `language_id` (`language_id`),
  KEY `finished` (`finished`),
  KEY `orders_status_2` (`orders_status`,`language_id`),
  KEY `date_purchased` (`date_purchased`),
  KEY `orders_id_2` (`orders_id`,`site_id`,`date_purchased`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `orders_mail`
--

CREATE TABLE IF NOT EXISTS `orders_mail` (
  `orders_status_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL DEFAULT '0',
  `orders_status_title` varchar(128) NOT NULL DEFAULT '',
  `orders_status_mail` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `orders_products`
--

CREATE TABLE IF NOT EXISTS `orders_products` (
  `orders_products_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` varchar(32) NOT NULL DEFAULT '0',
  `products_id` int(11) NOT NULL DEFAULT '0',
  `products_model` varchar(18) DEFAULT NULL,
  `products_name` varchar(64) NOT NULL DEFAULT '',
  `products_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `final_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `products_tax` decimal(7,4) NOT NULL DEFAULT '0.0000',
  `products_quantity` int(2) NOT NULL DEFAULT '0',
  `products_character` varchar(128) NOT NULL DEFAULT '',
  `site_id` int(11) NOT NULL,
  PRIMARY KEY (`orders_products_id`),
  KEY `orders_id` (`orders_id`,`products_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=94784 ;

-- --------------------------------------------------------

--
-- 表的结构 `orders_products_attributes`
--

CREATE TABLE IF NOT EXISTS `orders_products_attributes` (
  `orders_products_attributes_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` varchar(32) NOT NULL DEFAULT '0',
  `orders_products_id` int(11) NOT NULL DEFAULT '0',
  `products_options` varchar(32) NOT NULL DEFAULT '',
  `products_options_values` varchar(64) NOT NULL DEFAULT '',
  `options_values_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `price_prefix` char(1) NOT NULL DEFAULT '',
  `attributes_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orders_products_attributes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=93580 ;

-- --------------------------------------------------------

--
-- 表的结构 `orders_products_download`
--

CREATE TABLE IF NOT EXISTS `orders_products_download` (
  `orders_products_download_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` varchar(32) NOT NULL DEFAULT '0',
  `orders_products_id` int(11) NOT NULL DEFAULT '0',
  `orders_products_filename` varchar(255) NOT NULL DEFAULT '',
  `download_maxdays` int(2) NOT NULL DEFAULT '0',
  `download_count` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orders_products_download_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `orders_status`
--

CREATE TABLE IF NOT EXISTS `orders_status` (
  `orders_status_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '1',
  `orders_status_name` varchar(32) NOT NULL DEFAULT '',
  `finished` tinyint(4) NOT NULL DEFAULT '0',
  `orders_status_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`orders_status_id`,`language_id`),
  KEY `idx_orders_status_name` (`orders_status_name`),
  KEY `finished` (`finished`),
  KEY `orders_status_id` (`orders_status_id`,`language_id`,`finished`),
  KEY `language_id` (`language_id`,`finished`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 触发器 `orders_status`
--
DROP TRIGGER IF EXISTS `ps_ordersstatus_updated`;
DELIMITER //
CREATE TRIGGER `ps_ordersstatus_updated` AFTER UPDATE ON `orders_status`
 FOR EACH ROW begin 
declare sl_id int;
declare sosname varchar(60);
declare sosimg varchar(60);
declare sosfinish int;
select s.language_id,s.orders_status_name,s.orders_status_image ,s.finished into sl_id ,sosname,sosimg,sosfinish 
from 
orders_status AS s 
where s.orders_status_id = OLD.orders_status_id; 
update orders set language_id = sl_id,orders_status_image = sosimg,finished = sosfinish ,orders_status_name = sosname
where orders_status = OLD.orders_status_id;
end
//
DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `orders_status_history`
--

CREATE TABLE IF NOT EXISTS `orders_status_history` (
  `orders_status_history_id` int(11) NOT NULL AUTO_INCREMENT,
  `orders_id` varchar(32) NOT NULL DEFAULT '0',
  `orders_status_id` int(5) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `customer_notified` int(1) DEFAULT '0',
  `comments` text,
  PRIMARY KEY (`orders_status_history_id`),
  KEY `orders_id` (`orders_id`),
  KEY `orders_status_id` (`orders_status_id`),
  KEY `orders_id_2` (`orders_id`,`orders_status_id`),
  KEY `orders_status_history_id` (`orders_status_history_id`,`orders_id`,`orders_status_id`),
  KEY `orders_status_history_id_2` (`orders_status_history_id`,`orders_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=313027 ;

-- --------------------------------------------------------

--
-- 表的结构 `orders_total`
--

CREATE TABLE IF NOT EXISTS `orders_total` (
  `orders_total_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orders_id` char(32) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `text` varchar(255) NOT NULL DEFAULT '',
  `value` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `class` varchar(32) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`orders_total_id`),
  KEY `orders_id` (`orders_id`),
  KEY `orders_id_2` (`orders_id`,`class`),
  KEY `class` (`class`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=274872 ;

-- --------------------------------------------------------

--
-- 表的结构 `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `userid` char(16) NOT NULL DEFAULT '',
  `permission` int(11) DEFAULT '0',
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `present_applicant`
--

CREATE TABLE IF NOT EXISTS `present_applicant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `family_name` varchar(128) NOT NULL DEFAULT '',
  `first_name` varchar(128) NOT NULL DEFAULT '',
  `mail` varchar(64) NOT NULL DEFAULT '',
  `postcode` varchar(10) NOT NULL DEFAULT '',
  `prefectures` varchar(32) NOT NULL DEFAULT '',
  `cities` varchar(64) NOT NULL DEFAULT '',
  `address1` varchar(128) NOT NULL DEFAULT '',
  `address2` varchar(128) NOT NULL DEFAULT '',
  `phone` varchar(32) NOT NULL DEFAULT '',
  `tourokubi` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `present_goods`
--

CREATE TABLE IF NOT EXISTS `present_goods` (
  `goods_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `html_check` tinyint(1) NOT NULL DEFAULT '0',
  `title` text NOT NULL,
  `image` varchar(64) NOT NULL DEFAULT '',
  `text` text NOT NULL,
  `site_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `limit_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`goods_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- 表的结构 `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `products_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_quantity` int(4) NOT NULL DEFAULT '0',
  `products_model` varchar(18) DEFAULT NULL,
  `products_image` varchar(64) DEFAULT NULL,
  `products_image2` varchar(64) DEFAULT NULL,
  `products_image3` varchar(64) DEFAULT NULL,
  `products_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `products_price_offset` varchar(15) DEFAULT NULL,
  `products_date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `products_last_modified` datetime DEFAULT NULL,
  `products_date_available` datetime DEFAULT NULL,
  `products_weight` decimal(5,2) NOT NULL DEFAULT '0.00',
  `products_status` tinyint(1) NOT NULL DEFAULT '0',
  `products_tax_class_id` int(11) NOT NULL DEFAULT '0',
  `manufacturers_id` int(11) DEFAULT NULL,
  `products_ordered` int(11) NOT NULL DEFAULT '0',
  `products_bflag` tinyint(1) NOT NULL DEFAULT '0',
  `products_cflag` tinyint(1) NOT NULL DEFAULT '0',
  `products_small_sum` text NOT NULL,
  `option_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`products_id`),
  KEY `idx_products_date_added` (`products_date_added`),
  KEY `idx_products_status` (`products_status`),
  KEY `products_quantity` (`products_quantity`),
  KEY `manufacturers_id` (`manufacturers_id`),
  KEY `products_model` (`products_model`),
  KEY `products_status` (`products_status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33644 ;

-- --------------------------------------------------------

--
-- 表的结构 `products_attributes`
--

CREATE TABLE IF NOT EXISTS `products_attributes` (
  `products_attributes_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `options_id` int(11) NOT NULL DEFAULT '0',
  `options_values_id` int(11) NOT NULL DEFAULT '0',
  `options_values_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `price_prefix` char(1) NOT NULL DEFAULT '',
  `products_stock` varchar(255) NOT NULL,
  `products_at_quantity` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`products_attributes_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=261374 ;

-- --------------------------------------------------------

--
-- 表的结构 `products_attributes_download`
--

CREATE TABLE IF NOT EXISTS `products_attributes_download` (
  `products_attributes_id` int(11) NOT NULL DEFAULT '0',
  `products_attributes_filename` varchar(255) NOT NULL DEFAULT '',
  `products_attributes_maxdays` int(2) DEFAULT '0',
  `products_attributes_maxcount` int(2) DEFAULT '0',
  PRIMARY KEY (`products_attributes_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `products_description`
--

CREATE TABLE IF NOT EXISTS `products_description` (
  `products_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL DEFAULT '1',
  `products_name` varchar(64) NOT NULL DEFAULT '',
  `products_description` text,
  `site_id` int(11) NOT NULL,
  `products_attention_1` text,
  `products_attention_2` text,
  `products_attention_3` text,
  `products_attention_4` text,
  `products_attention_5` text,
  `products_url` varchar(255) DEFAULT NULL,
  `products_viewed` int(5) DEFAULT '0',
  PRIMARY KEY (`products_id`,`language_id`,`site_id`),
  KEY `products_name` (`products_name`),
  FULLTEXT KEY `products_description` (`products_description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=33644 ;

-- --------------------------------------------------------

--
-- 表的结构 `products_notifications`
--

CREATE TABLE IF NOT EXISTS `products_notifications` (
  `products_id` int(11) NOT NULL DEFAULT '0',
  `customers_id` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`products_id`,`customers_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `products_options`
--

CREATE TABLE IF NOT EXISTS `products_options` (
  `products_options_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '1',
  `products_options_name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`products_options_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `products_options_values`
--

CREATE TABLE IF NOT EXISTS `products_options_values` (
  `products_options_values_id` int(11) NOT NULL DEFAULT '0',
  `language_id` int(11) NOT NULL DEFAULT '1',
  `products_options_values_name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`products_options_values_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `products_options_values_to_products_options`
--

CREATE TABLE IF NOT EXISTS `products_options_values_to_products_options` (
  `products_options_values_to_products_options_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_options_id` int(11) NOT NULL DEFAULT '0',
  `products_options_values_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`products_options_values_to_products_options_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=296 ;

-- --------------------------------------------------------

--
-- 表的结构 `products_to_categories`
--

CREATE TABLE IF NOT EXISTS `products_to_categories` (
  `products_id` int(11) NOT NULL DEFAULT '0',
  `categories_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`products_id`,`categories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `products_to_image_documents`
--

CREATE TABLE IF NOT EXISTS `products_to_image_documents` (
  `products_to_documents_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `documents_id` int(11) NOT NULL DEFAULT '0',
  `last_updated` datetime NOT NULL,
  UNIQUE KEY `id` (`products_to_documents_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `products_to_tags`
--

CREATE TABLE IF NOT EXISTS `products_to_tags` (
  `products_id` int(11) NOT NULL,
  `tags_id` int(11) NOT NULL,
  PRIMARY KEY (`products_id`,`tags_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `product_dougyousya_order`
--

CREATE TABLE IF NOT EXISTS `product_dougyousya_order` (
  `product_id` int(11) NOT NULL,
  `order_value` int(11) NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `reviews`
--

CREATE TABLE IF NOT EXISTS `reviews` (
  `reviews_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `customers_id` int(11) DEFAULT NULL,
  `customers_name` varchar(64) NOT NULL DEFAULT '',
  `reviews_rating` int(1) DEFAULT NULL,
  `date_added` datetime DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `reviews_read` int(5) NOT NULL DEFAULT '0',
  `site_id` int(11) NOT NULL,
  `reviews_status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reviews_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3720 ;

-- --------------------------------------------------------

--
-- 表的结构 `reviews_description`
--

CREATE TABLE IF NOT EXISTS `reviews_description` (
  `reviews_id` int(11) NOT NULL DEFAULT '0',
  `languages_id` int(11) NOT NULL DEFAULT '0',
  `reviews_text` text NOT NULL,
  PRIMARY KEY (`reviews_id`,`languages_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `sesskey` varchar(32) NOT NULL DEFAULT '',
  `expiry` int(11) unsigned NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`sesskey`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `set_auto_calc`
--

CREATE TABLE IF NOT EXISTS `set_auto_calc` (
  `parent_id` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `bairitu` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `keisan` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `hikaku` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `shisoku` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `percent` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- 表的结构 `set_comments`
--

CREATE TABLE IF NOT EXISTS `set_comments` (
  `categories_id` int(11) NOT NULL,
  `author` varchar(255) NOT NULL,
  `rule` text NOT NULL,
  `comment` text NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`categories_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `set_dougyousya_categories`
--

CREATE TABLE IF NOT EXISTS `set_dougyousya_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dougyousya_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dougyousya_id` (`dougyousya_id`,`categories_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100 ;

-- --------------------------------------------------------

--
-- 表的结构 `set_dougyousya_history`
--

CREATE TABLE IF NOT EXISTS `set_dougyousya_history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `categories_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `dougyosya_kakaku` int(11) NOT NULL,
  `radio_chk` int(11) NOT NULL,
  `last_date` datetime NOT NULL,
  `dougyousya_id` int(11) NOT NULL,
  PRIMARY KEY (`history_id`),
  KEY `last_date` (`last_date`),
  KEY `dougyousya_id` (`dougyousya_id`),
  KEY `products_id` (`products_id`),
  KEY `categories_id` (`categories_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=209 ;

-- --------------------------------------------------------

--
-- 表的结构 `set_dougyousya_names`
--

CREATE TABLE IF NOT EXISTS `set_dougyousya_names` (
  `dougyousya_id` int(11) NOT NULL AUTO_INCREMENT,
  `dougyousya_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dougyousya_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- 表的结构 `set_menu_list`
--

CREATE TABLE IF NOT EXISTS `set_menu_list` (
  `set_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `categories_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `kakuukosuu` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `kakaku` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `last_modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`set_list_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=626 ;

-- --------------------------------------------------------

--
-- 表的结构 `set_oroshi_categories`
--

CREATE TABLE IF NOT EXISTS `set_oroshi_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `oroshi_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `oroshi_id` (`oroshi_id`,`categories_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=102 ;

-- --------------------------------------------------------

--
-- 表的结构 `set_oroshi_datas`
--

CREATE TABLE IF NOT EXISTS `set_oroshi_datas` (
  `list_id` int(11) NOT NULL AUTO_INCREMENT,
  `oroshi_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `datas` text COLLATE utf8_unicode_ci NOT NULL,
  `set_date` datetime NOT NULL,
  PRIMARY KEY (`list_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=282 ;

-- --------------------------------------------------------

--
-- 表的结构 `set_oroshi_names`
--

CREATE TABLE IF NOT EXISTS `set_oroshi_names` (
  `oroshi_id` int(11) NOT NULL AUTO_INCREMENT,
  `oroshi_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`oroshi_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- 表的结构 `set_products_dougyousya`
--

CREATE TABLE IF NOT EXISTS `set_products_dougyousya` (
  `product_id` int(11) NOT NULL,
  `dougyousya_id` int(11) NOT NULL,
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `sites`
--

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `romaji` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL,
  `url` varchar(200) NOT NULL,
  `order_num` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`romaji`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- 表的结构 `specials`
--

CREATE TABLE IF NOT EXISTS `specials` (
  `specials_id` int(11) NOT NULL AUTO_INCREMENT,
  `products_id` int(11) NOT NULL DEFAULT '0',
  `specials_new_products_price` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `specials_date_added` datetime DEFAULT NULL,
  `specials_last_modified` datetime DEFAULT NULL,
  `expires_date` datetime DEFAULT NULL,
  `date_status_change` datetime DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`specials_id`),
  KEY `idx_products_id` (`products_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=32433 ;

-- --------------------------------------------------------

--
-- 表的结构 `sql_log`
--

CREATE TABLE IF NOT EXISTS `sql_log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `time` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `group` int(11) NOT NULL,
  `project` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(2048) COLLATE utf8_unicode_ci NOT NULL,
  `file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `param` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `tags_id` int(11) NOT NULL AUTO_INCREMENT,
  `tags_name` varchar(255) NOT NULL,
  `tags_images` varchar(255) NOT NULL,
  `tags_checked` tinyint(4) NOT NULL,
  `tags_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tags_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `tax_class`
--

CREATE TABLE IF NOT EXISTS `tax_class` (
  `tax_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_class_title` varchar(32) NOT NULL DEFAULT '',
  `tax_class_description` varchar(255) NOT NULL DEFAULT '',
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tax_class_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `tax_rates`
--

CREATE TABLE IF NOT EXISTS `tax_rates` (
  `tax_rates_id` int(11) NOT NULL AUTO_INCREMENT,
  `tax_zone_id` int(11) NOT NULL DEFAULT '0',
  `tax_class_id` int(11) NOT NULL DEFAULT '0',
  `tax_priority` int(5) DEFAULT '1',
  `tax_rate` decimal(7,4) NOT NULL DEFAULT '0.0000',
  `tax_description` varchar(255) NOT NULL DEFAULT '',
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`tax_rates_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `userid` varchar(16) NOT NULL DEFAULT '',
  `password` varchar(64) NOT NULL DEFAULT '',
  `name` varchar(64) DEFAULT NULL,
  `email` varchar(96) DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `whos_online`
--

CREATE TABLE IF NOT EXISTS `whos_online` (
  `customer_id` int(11) DEFAULT NULL,
  `full_name` varchar(64) NOT NULL DEFAULT '',
  `session_id` varchar(128) NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `time_entry` varchar(14) NOT NULL DEFAULT '',
  `time_last_click` varchar(14) NOT NULL DEFAULT '',
  `site_id` int(11) NOT NULL,
  `last_page_url` varchar(64) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `zones`
--

CREATE TABLE IF NOT EXISTS `zones` (
  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_country_id` int(11) NOT NULL DEFAULT '0',
  `zone_code` varchar(32) NOT NULL DEFAULT '',
  `zone_name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`zone_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=229 ;

-- --------------------------------------------------------

--
-- 表的结构 `zones_to_geo_zones`
--

CREATE TABLE IF NOT EXISTS `zones_to_geo_zones` (
  `association_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_country_id` int(11) NOT NULL DEFAULT '0',
  `zone_id` int(11) DEFAULT NULL,
  `geo_zone_id` int(11) DEFAULT NULL,
  `last_modified` datetime DEFAULT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`association_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;
