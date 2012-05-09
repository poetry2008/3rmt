ALTER  TABLE `configuration`  ADD INDEX ( `site_id` );
ALTER  TABLE `configuration`  ADD INDEX ( `configuration_key` ) ;



ALTER  TABLE `customers` ADD INDEX ( `customers_id` , `customers_firstname` , `customers_lastname` ) ;
ALTER  TABLE `customers` ADD INDEX ( `customers_id` , `customers_firstname` , `customers_lastname` , `site_id` ) ;
ALTER  TABLE `customers` ADD INDEX ( `customers_id` , `site_id` ) ;
ALTER  TABLE `customers` ADD INDEX ( `site_id` ) ;

ALTER  TABLE `oa_formvalue` ADD INDEX ( `item_id` );

ALTER  TABLE `orders` ADD `telecom_unknow` TINYINT( 4 ) NOT NULL DEFAULT '1' AFTER `telecom_sendid`;

ALTER  TABLE `orders` ADD INDEX ( `customers_name` , `customers_name_f` , `billing_street_address`  , `billing_telephone` ) ;

ALTER  TABLE `orders` ADD INDEX ( `orders_id` , `flag_qaf` ) ;
ALTER  TABLE `orders` ADD INDEX ( `orders_status` , `flag_qaf` ) ;
ALTER  TABLE `orders` ADD INDEX ( `orders_status` , `language_id` , `flag_qaf` ) ;
ALTER  TABLE `orders` ADD INDEX ( `orders_id` , `orders_status` , `flag_qaf` ) ;


ALTER  TABLE `orders_products`  ADD INDEX ( `orders_id` , `products_name` ) ;
ALTER  TABLE `orders_products`  ADD INDEX ( `products_name` ) ;
ALTER  TABLE `orders_products`  ADD INDEX ( `torihiki_date` ) ;


ALTER  TABLE `orders_questions`  ADD `q_14_3` TINYINT NULL AFTER `q_14_2`;

ALTER  TABLE `orders_status_history`  ADD FULLTEXT (
`comments`
);

ALTER  TABLE `orders_total` ADD INDEX ( `orders_id` , `title` ) ;


ALTER  TABLE `preorders` ADD `bank_info` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER  TABLE `products_description`  ADD `preorder_status` SMALLINT NOT NULL DEFAULT '0';



ALTER  TABLE `reviews` ADD INDEX ( `customers_name` ) ;
ALTER  TABLE `reviews` ADD INDEX ( `reviews_rating` ) ;
ALTER  TABLE `reviews` ADD INDEX ( `date_added` ) ;
ALTER  TABLE `reviews` ADD INDEX ( `site_id` ) ;
ALTER  TABLE `reviews` ADD INDEX ( `reviews_status` ) ;
ALTER  TABLE `reviews` ADD INDEX ( `products_id` ) ;


ALTER  TABLE `reviews_description`  ADD FULLTEXT (
`reviews_text`
);






INSERT INTO `configuration` (`configuration_id`, `configuration_title`, `configuration_key`, `configuration_value`, `configuration_description`, `configuration_group_id`, `sort_order`, `last_modified`, `date_added`, `use_function`, `set_function`, `site_id`) VALUES
(NULL, '予約注文', 'MODULE_PAYMENT_BUYING_PREORDER_SHOW', 'True', '予約注文で銀行振込(買い取り)を表示します', 6, 1, NULL, '2011-12-05 17:11:53', NULL, 'tep_cfg_select_option(array(''True'', ''False''), ', 0),
(NULL, '予約注文', 'MODULE_PAYMENT_BUYINGPOINT_PREORDER_SHOW', 'True', '予約注文でポイント(買い取り)を表示します', 6, 1, NULL, '2011-12-05 17:13:19', NULL, 'tep_cfg_select_option(array(''True'', ''False''), ', 0),
(NULL, '予約注文', 'MODULE_PAYMENT_FETCH_GOOD_PREORDER_SHOW', 'True', '予約注文で来店支払いを表示します', 6, 1, NULL, '2011-12-05 17:13:28', NULL, 'tep_cfg_select_option(array(''True'', ''False''), ', 0);





update `products_description` set `preorder_status` = '1'  where `products_id` in (select `products_id` from `products` where `products_bflag` = '0');
