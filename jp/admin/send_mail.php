#!/usr/bin/env php
<?php 
require('includes/configure.php');
require('includes/functions/database.php');
require('includes/functions/general.php');
// link db
tep_db_connect();
// read config for point mail info
$email_template_sql = "select * from `configuration` 
  where `configuration_key` = 'POINT_EMAIL_TEMPLATE' limit 1 ";
$email_template_query = tep_db_query($email_template_sql);
if($email_template_row = tep_db_fetch_array($email_template_query)){
  $email_template = $email_template_row['configuration_value'];
}
$email_dates_sql = "select * from `configuration` 
  where `configuration_key` = 'POINT_EMAIL_DATE' limit 1 ";
$email_dates_query = tep_db_query($email_dates_sql);
if($email_dates_row = tep_db_fetch_array($email_dates_query)){
  $email_date_arr = explode(',',$email_dates_row['configuration_value']);
}
if(!isset($email_template)||!$email_template||!isset($email_date_arr)||!$email_date_arr){
  $sql_insert = "INSERT INTO `configuration` (`configuration_id`,
    `configuration_title`, `configuration_key`, `configuration_value`,
    `configuration_description`, `configuration_group_id`, `sort_order`,
    `last_modified`, `date_added`, `use_function`, `set_function`, `site_id`) VALUES
      (null, 'POINT邮件模板', 'POINT_EMAIL_TEMPLATE', 'name = ${NAME}\r\npoint =
       ${POINT}\r\npoint_date = ${POINT_DATE}\r\nsite_name = ${SITE_NAME}''',
       'POINT_EMAIL_TEMPLATE_DESCRIPTION', 901, NULL, '2011-05-19 16:30:31',
       '0000-00-00 00:00:00', NULL, 'tep_cfg_textarea(', 0),
       (null, 'POINT邮件发送时间', 'POINT_EMAIL_DATE', '1,20,30', 'POINT邮件发送时间
        请使用'',''分隔', 901, NULL, '2011-05-13 14:31:18', '0000-00-00 00:00:00',
        NULL, NULL, 0);
       ";
       tep_db_query($sql_insert);
}

// grep point by config
foreach($email_date_arr as $value){
$customer_sql = "SELECT ci.customers_info_date_of_last_logon as point_date,
                 c.point as point , c.site_id as site_id ,
                 c.customers_firstname as firstname,
                 c.customers_lastname as lastname,
                 con.configuration_value as config_date 
                 FROM customers_info ci, customers c
                 ,configuration con 
                 WHERE (ci.customers_info_date_of_last_logon between 
                    DATE_SUB(now(),INTERVAL ".intval($value+1)." DAY )
                  and 
                    DATE_SUB(now(),INTERVAL ".intval($value)." DAY )
                  )
                 AND DATE_ADD(ci.customers_info_date_of_last_logon,INTERVAL 
                     con.configuration_value DAY) > now( )
                 AND ci.customers_info_id = c.customers_id 
                 AND if(con.site_id = c.site_id,con.site_id = c.site_id,con.site_id=0) 
                 AND con.configuration_key = 'MODULE_ORDER_TOTAL_POINT_LIMIT'";
$customer_query = tep_db_query($customer_sql);
while($customer_info = tep_db_fetch_array($customer_query)){
  $email_template = str_replace(
      array('${NAME}','${POINT}','${POINT_DATE}','${SITE_NAME}'),
      array($customer_info['lastname']." ".$customer_info['firstname'],
        $customer_info['point'],$value,
        get_configuration_by_site_id('STORE_NAME',
          $customer_info['site_id'],'configuration')),
      $email_template);
  var_dump($email_template);
}
}
