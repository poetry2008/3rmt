#!/usr/bin/env php
<?php 
set_time_limit(0);
//file patch
define('ROOT_DIR','/home/.sites/28/site1/web/admin');
require(ROOT_DIR.'/includes/configure.php');
// default sleep second
define('SLEEP_SECOND',3);//以秒为单位设置
// default send row to sleep
define('SEND_ROWS',2);
// debug module flag
define('POINT_DEBUG_MODULE_FLAG','Off'); // On or Off
// default log file name
define('LOG_FILE_NAME','cron___point_alert_send_mail_to_customer.log');

if(POINT_DEBUG_MODULE_FLAG=='On'){
  $log_str = '';
}
$send_row = 0;
// link db
$link = mysql_connect(DB_SERVER,DB_SERVER_USERNAME,DB_SERVER_PASSWORD);
mysql_query('set names utf8');
mysql_select_db(DB_DATABASE);

//get config value function
function get_configuration_by_site_id($key, $site_id = '0',$table_name='') {
  if(!$site_id||!isset($site_id)){
    $site_id = '0';
  }
  $config = mysql_fetch_array(mysql_query("select * from ".$table_name." where
        configuration_key='".$key."' and (site_id='".$site_id."' or site_id = '0')
        order by site_id desc"));
  if ($config) {
    return $config['configuration_value'];
  } else {
    return false;
  }
}

//get point and date_purchased
function get_customer_info_by_site_id_email($site_id,$email){
  $sql = "select c.point as point ,o.date_purchased as point_date
    from orders o,customers c
    where o.customers_id = c.customers_id
    and c.site_id = '".$site_id."' 
    and c.customers_email_address = '".$email."' 
    order by o.date_purchased DESC limit 1";
  $query = mysql_query($sql);
  if($query){
    return mysql_fetch_array($query);
  }else{
    return false;
  }

}

//get url by site id 
function get_url_by_site_id($site_id) {
  $site = mysql_fetch_array(mysql_query("select * from sites where
        id='".$site_id."'"));
  if ($site) {
    return $site['url'];
  } else {
    return false;
  }
}

// read template from point mail
$template_sql = "select * from point_mail"; 
$template_query = mysql_query($template_sql);
$template_arr = array();
while($template_row = mysql_fetch_array($template_query)){
  $point_mail_query = mysql_query("select title,contents from mail_templates where flag='POINT_NOTIFY_MAIL_TEMPLATES_".$template_row['id']."'");
  $point_mail_array = mysql_fetch_array($point_mail_query);
  mysql_free_result($point_mail_query);
  $template_arr[] = array('mail_date' => $template_row['mail_date'],
      'mail_title' =>  $point_mail_array['title'],
      'template' => $point_mail_array['contents']);

}

// grep customers by config
$customer_sql = "SELECT 
distinct
o.customers_name AS customer_name,
  o.customers_email_address AS customer_email,
  o.site_id AS site_id, 
  con.configuration_value AS config_date
  FROM orders o, customers c, configuration con
  WHERE if( con.configuration_value = '0', DATE_ADD( o.date_purchased, INTERVAL 1
        DAY ) > now( ) , DATE_ADD( o.date_purchased, INTERVAL con.configuration_value
          DAY ) > now( ) )
  AND o.customers_id = c.customers_id
  AND c.point > 0 
  AND c.customers_guest_chk = 0 
  AND if( con.site_id = o.site_id, con.site_id = o.site_id, con.site_id =0 )
  AND con.configuration_key = 'MODULE_ORDER_TOTAL_POINT_LIMIT'
  ORDER BY o.date_purchased DESC";

  $customer_query = mysql_query($customer_sql);
  // replace str to value for email template
  $sum_user = 0;
  while($customer_info = mysql_fetch_array($customer_query)){
    foreach($template_arr as $template_row){
      $value = $template_row['mail_date'];
      $email_template = $template_row['template'];
      $title = $template_row['mail_title'];
      if(!isset($title)||$title == ''){

        echo "Title ERROR \n";
        exit;
      }
      //get time 
      $customer_info_arr =
        get_customer_info_by_site_id_email($customer_info['site_id'],
            $customer_info['customer_email']);
      $last_login = date('Y-m-d',time()); 
      $now_time = mktime(0,0,0,
          substr($last_login,5,2),
          substr($last_login,8,2),
          substr($last_login,0,4));
      $year = substr($customer_info_arr['point_date'],0,4);
      $mon = substr($customer_info_arr['point_date'],5,2);
      $day = substr($customer_info_arr['point_date'],8,2);
      $out_time = mktime(0,0,0,$mon,$day+$customer_info['config_date'],$year);
      if(($out_time>$now_time)&&($customer_info['config_date']>=$value)&&
          intval(($out_time-$now_time)/86400)==$value+1){
        $point_out_date = date('Y年m月d日',$out_time-86400);
        $show_email_template = str_replace(
            array('${USER_NAME}','${USER_MAIL}','${POINT}','${VALID_DAY}','${SITE_NAME}','${LAST_VILID_DATE}'
              ,'${SITE_URL}','${SUPPORT_MAIL}'),
            array($customer_info['customer_name'],
              $customer_info['customer_email'],
              $customer_info_arr['point'],$value,
              get_configuration_by_site_id('STORE_NAME',
                $customer_info['site_id'],'configuration'),
              $point_out_date,
              get_url_by_site_id($customer_info['site_id']),
              get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS',
                $customer_info['site_id'],'configuration')
              ),
            $email_template);
        $title = str_replace(
            array('${USER_NAME}','${USER_MAIL}','${POINT}','${VALID_DAY}','${SITE_NAME}','${LAST_VILID_DATE}'
              ,'${SITE_URL}','${SUPPORT_MAIL}'),
            array($customer_info['customer_name'],
              $customer_info['customer_email'],
              $customer_info_arr['point'],$value,
              get_configuration_by_site_id('STORE_NAME',
                $customer_info['site_id'],'configuration'),
              $point_out_date,
              get_url_by_site_id($customer_info['site_id']),
              get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS',
                $customer_info['site_id'],'configuration')
              ),
            $title);
        $sum_user++;
        $to = '"=?UTF-8?B?'.base64_encode($customer_info['customer_name']).'?=" <'.$customer_info['customer_email'].'>'. "\r\n";
        $message = $show_email_template;
        $subject = "=?UTF-8?B?".base64_encode($title)."?=";
        $headers = 'MIME-Version: 1.0'."\r\n";
        $headers .= "X-Mailer: iimy Mailer\r\n";
        $headers .= 'Content-type: text/plain; charset=utf-8' . "\r\n";
        $headers .= "Content-Transfer-Encoding: 7bit\r\n";

        if(get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',
              $customer_info['site_id'],'configuration')){
          $From_Mail = get_configuration_by_site_id('STORE_OWNER_EMAIL_ADDRESS',
              $customer_info['site_id'],'configuration');
        }else{
          echo "MailAddress ERROR \n";
          exit;
        }
        $headers .= 'From: "=?UTF-8?B?'.base64_encode(get_configuration_by_site_id('STORE_NAME',$customer_info['site_id'],'configuration')).'?=" <'.$From_Mail.'>'. "\r\n";

        $parameter = '-f'.$From_Mail;
        $send_row++;
        //替换通用邮件模板参数 
        $site_name = get_configuration_by_site_id('STORE_NAME',$customer_info['site_id'],'configuration');
        $http_server = get_configuration_by_site_id('HTTP_SERVER',$customer_info['site_id'],'configuration');
        $company_name = get_configuration_by_site_id('COMPANY_NAME',$customer_info['site_id'],'configuration');
        $company_address = get_configuration_by_site_id('STORE_NAME_ADDRESS',$customer_info['site_id'],'configuration');
        $company_tel = get_configuration_by_site_id('STORE_NAME_TEL',$customer_info['site_id'],'configuration');
        $support_email_address = get_configuration_by_site_id('SUPPORT_EMAIL_ADDRESS',$customer_info['site_id'],'configuration');
        $email_footer = get_configuration_by_site_id('C_EMAIL_FOOTER',$customer_info['site_id'],'configuration');
            
        $mode_array = array(
                '${SITE_NAME}', 
                '${SITE_URL}', 
                '${COMPANY_NAME}', 
                '${COMPANY_ADDRESS}', 
                '${COMPANY_TEL}', 
                '${SUPPORT_MAIL}', 
                '${STAFF_MAIL}', 
                '${STAFF_NAME}', 
                '${SIGNATURE}', 
                '${USER_MAIL}',
                '${USER_NAME}',
                '${USER_INFO}', 
                '${YEAR}', 
                '${MONTH}', 
                '${DAY}', 
                '${HTTPS_SERVER}'
                ); 
        $replace_array = array(
                $site_name,
                $http_server,
                $company_name,
                $company_address,
                $company_tel,
                $support_email_address,
                '',
                '',
                $email_footer,
                $customer_info['customer_email'],
                $customer_info['customer_name'],
                '',
                date('Y'),
                date('m'),
                date('d'),
                ''
              );
        $message = str_replace($mode_array,$replace_array,$message);
        if(POINT_DEBUG_MODULE_FLAG != 'On'){
          mail($to, $subject, $message, $headers,$parameter);
          if(($sum_user%SEND_ROWS)==0){
            sleep(SLEEP_SECOND);
          }
        }else{
          $log_str .= "\n";
          $log_str .= "Subject: ".$title."\n";
          $log_str .= 'MIME-Version: 1.0'."\n";
          $log_str .= "X-Mailer: iimy Mailer\n";
          $log_str .= 'Content-type: text/plain; charset=utf-8' . "\n";
          $log_str .= "Content-Transfer-Encoding: 7bit\n";
          $log_str .= 'From: "'.get_configuration_by_site_id('STORE_NAME',$customer_info['site_id'],'configuration').'" <'.$From_Mail.'>'."\n";
          $log_str .= "To: ".'"'.$customer_info['customer_name'].'" <'.$customer_info['customer_email'].'>'. "\n";
          $log_str .= "Return-Path: <".$From_Mail.">\n";
          $log_str .= "message: \n";
          $log_str .= str_replace("\r\n","\n",$message);
          $log_str .= "\n";
          $log_str .= "==============================================";
          $log_str .= "\n";
          if($send_row == 1){
            $point_mail_address_query = mysql_query("select configuration_value from configuration where configuration_key='POINT_NOTIFY_EMAIL_ADDRESS' and site_id='0'");
            $point_mail_address_array = mysql_fetch_array($point_mail_address_query);
            mysql_free_result($point_mail_address_query);
            $to = '"=?UTF-8?B?'.base64_encode($customer_info['customer_name']).'?=" <'.$point_mail_address_array['configuration_value'].'>'. "\r\n";
            mail($to, $subject, $message, $headers,$parameter);
          }
        }
        echo "SEND: ".$send_row." mail \n";
      }
    }
  }
if(POINT_DEBUG_MODULE_FLAG == 'On'){
  $fp = fopen(ROOT_DIR.'/log/'.LOG_FILE_NAME,'w');
  $head = "SEND: ".$send_row." mail \n\n";
  fwrite($fp,$head.$log_str); 
  fclose($fp);
}

echo "Finish \n";


?>
