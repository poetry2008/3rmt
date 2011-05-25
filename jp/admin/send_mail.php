#!/usr/bin/env php
<?php 
//define('ROOT_DIR','/home/.sites/22/site13/vhosts/jp/admin');
define('ROOT_DIR','/home/szn/project/3rmt/jp/admin');
require(ROOT_DIR.'/includes/configure.php');



define('DEFAULT_EMAIL_FROM','sznforwork@yahoo.co.jp');
define('DEFAULT_POINT_MAIL_TITLE','point test');
define('ONE_DAY_SECOND',60*60*24);

define('SLEEP_SECOND',3);
define('SEND_ROWS',2);


// link db
$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_select_db(DB_DATABASE);

//get config value function
function get_configuration_by_site_id($key, $site_id = '0',$table_name='') {
  $config = mysql_fetch_array(mysql_query("select * from ".$table_name." where configuration_key='".$key."' and site_id='".$site_id."'"));
  if ($config) {
    return $config['configuration_value'];
  } else {
    return false;
  }
}


  // read template from point mail
  $template_sql = "select * from point_mail"; 
  $template_query = mysql_query($template_sql);
  $template_arr = array();
  while($template_row = mysql_fetch_array($template_query)){
    $template_arr[] = array('mail_date' => $template_row['mail_date'],
                          'mail_title' =>  $template_row['mail_title'],
                          'template' => $template_row['description']);

  }

  // grep point by config
  $customer_sql = "SELECT 
    o.customers_name AS customer_name,
    o.customers_email_address AS customer_email,
    c.point AS point, 
    o.site_id AS site_id, 
    o.date_purchased AS point_date,
    con.configuration_value AS config_date
      FROM orders o, customers c, configuration con
      WHERE if( con.configuration_value = '0', DATE_ADD( o.date_purchased, INTERVAL 1
            DAY ) > now( ) , DATE_ADD( o.date_purchased, INTERVAL con.configuration_value
              DAY ) > now( ) )
      AND o.customers_id = c.customers_id
      AND if( con.site_id = o.site_id, con.site_id = o.site_id, con.site_id =0 )
      AND con.configuration_key = 'MODULE_ORDER_TOTAL_POINT_LIMIT'
      GROUP BY o.customers_id
      ORDER BY o.date_purchased DESC";

  $customer_query = mysql_query($customer_sql);
  //var_dump($customer_sql);
  // replace str to value for email template
  $sum_user = 0;
  while($customer_info = mysql_fetch_array($customer_query)){
    foreach($template_arr as $template_row){
      $value = $template_row['mail_date'];
      $email_template = $template_row['template'];
      $title = $template_row['mail_title'];
      if(!isset($title)||$title == ''){
        $title = DEFAULT_POINT_MAIL_TITLE;
      }
      //get time 
      $last_login = strtotime($customer_info['point_date']);
      $year = substr($customer_info['point_date'],0,4);
      $mon = substr($customer_info['point_date'],5,2);
      $day = substr($customer_info['point_date'],8,2);
      $out_time = mktime(0,0,0,$mon,$day+$customer_info['config_date'],$year);
      if($last_login < ($out_time-$value*ONE_DAY_SECOND)&&$last_login >
          ($out_time-($value+1)*ONE_DAY_SECOND)){
        //replace ${} to true value
        $show_email_template = str_replace(
            array('${NAME}','${POINT}','${POINT_DATE}','${SITE_NAME}'),
            array($customer_info['customer_name'],
              $customer_info['point'],$value,
              get_configuration_by_site_id('STORE_NAME',
                $customer_info['site_id'],'configuration')),
            $email_template);
        $title = str_replace(
            array('${NAME}','${POINT}','${POINT_DATE}','${SITE_NAME}'),
            array($customer_info['customer_name'],
              $customer_info['point'],$value,
              get_configuration_by_site_id('STORE_NAME',
                $customer_info['site_id'],'configuration')),
            $title);
        $sum_user++;
        $to = $customer_info['customer_email'];
        $message = $show_email_template;
        $subject = "=?UTF-8?B?".
          base64_encode($title)."?=";
        $headers = 'Content-type: text/plain; charset=utf-8' . "\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit\r\n";  
        $From_Mail = DEFAULT_EMAIL_FROM;
        if(get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',
              $customer_info['site_id'],'configuration')){
          $From_Mail = get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',
              $customer_info['site_id'],'configuration');
        }
        $headers .= 'From: '.$From_Mail. "\r\n";
        
        // out put test
        var_dump($From_Mail);
        var_dump($title);
        var_dump($to);
        var_dump($message);
        var_dump(mail($to, $subject, $message, $headers)); 
        //send mail 
        mail($to, $subject, $message, $headers);
        if(($sum_user%SEND_ROWS)==0){
          sleep(SLEEP_SECOND);
        }
      }
    }
    /*
       test for out put 
       var_dump($show_email_template."\r\n".$customer_info['point_date']);
     */
  }
