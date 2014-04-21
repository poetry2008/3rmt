<?php
/*
  $Id$
*/

  //顾客信息检索页面
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  require('includes/step-by-step/new_application_top.php');
  //获取显示哪些网站的数据
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
     $sql_site_where = ' in ('.str_replace('-', ',', $_GET['site_id']).') and ';
  } else {
     $show_list_str = tep_get_setting_site_info(FILENAME_CUSTOMERS_INFO);
     $sql_site_where = ' in ('.$show_list_str.') and ';
  }
  if(isset($_GET['site_id'])&&$_GET['site_id']==''){
     $_GET['site_id'] = str_replace(',','-',tep_get_setting_site_info(FILENAME_CUSTOMERS_INFO));
  } 
  //根据各条件来查询相应的顾客信息
  if (isset($_GET['action']) && $_GET['action'] == 'search') {

    //获取各条件的值
    $keyword_1 = tep_db_input(tep_db_prepare_input($_GET['keywords_1']));    
    $condition = tep_db_prepare_input($_GET['condition']); 
    $keyword_2 = tep_db_input(tep_db_prepare_input($_GET['keywords_2']));    
    $search_object = tep_db_prepare_input($_GET['search_object']);    
    $search_condition = tep_db_prepare_input($_GET['search_condition']);    
    $search_range = tep_db_prepare_input($_GET['search_range']);    

    //是否忽略空格、换行
    if(in_array('customers_blank',$search_condition)){

      $keyword_1 = preg_replace('/\s/','',$keyword_1);
      $keyword_2 = preg_replace('/\s/','',$keyword_2);
    }

    //当未选择时，默认全选
    if(empty($search_object)){

      $search_object = array('customers_name','customers_email','customers_other');
    }
    if(empty($search_range)){

      $search_range = array('customers_customers','customers_orders','customers_preorders','customers_telecom_unknow'); 
    }


    //关键字
    $keyword_str = '';
    $keyword_query_str = '';
    //生成SQL语句
    /*
     * 参数 #CASE 用于替换区分大小写
     * 参数 #BLANK_F 用于替换忽略空格、换行的前缀
     * 参数 #BLANK_E 用于替换忽略空格、换行的后缀
     * 参数 #CHAR 用于区分全角、半角
     * 参数 #CHAR_F 用于转换编码的前缀 
     * 参数 #CHAR_E 用于转换编码的后缀 
     */
    $keyword_array = array('customers_customers'=>array('customers_name'=>"#CASE#BLANK_Fc.customers_firstname#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fc.customers_lastname#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fc.customers_firstname_f#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fc.customers_lastname_f#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fconcat(c.customers_lastname,' ',c.customers_firstname)#BLANK_E #CHAR_Clike '%#KEYWORD%'",
                                                        'customers_email'=>"#CASE#BLANK_Fc.customers_email_address#BLANK_E #CHAR_Clike '%#KEYWORD%'",
                                                        'customers_other'=>"#CASE#BLANK_Fc.customers_fax#BLANK_E #CHAR_Clike '%#KEYWORD%'",  
                                                  ),
                         'customers_orders'=>array('customers_name'=>"#CASE#BLANK_Fo.customers_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.customers_name_f#BLANK_E #CHAR_Clike '%#KEYWORD%'",
                                                    'customers_email'=>"#CASE#BLANK_Fo.customers_email_address#BLANK_E #CHAR_Clike '%#KEYWORD%'", 
                                                    'customers_other'=>"#CASE#BLANK_Fo.orders_id#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_F#CHAR_Fo.customers_id#CHAR_E#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.billing_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.payment_method#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_status_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_ip#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_host_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_user_agent#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_comment#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_screen_resolution#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_color_depth#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_flash_version#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_http_accept_language#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.telecom_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.telecom_tel#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.telecom_money#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.telecom_email#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_ref#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_ref_keywords#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fc.customers_fax#BLANK_E #CHAR_Clike '%#KEYWORD%'",
                                                  ), 
                         'customers_preorders'=>array('customers_name'=>"#CASE#BLANK_Fp.customers_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.customers_name_f#BLANK_E #CHAR_Clike '%#KEYWORD%'",
                                                    'customers_email'=>"#CASE#BLANK_Fp.customers_email_address#BLANK_E #CHAR_Clike '%#KEYWORD%'", 
                                                    'customers_other'=>"#CASE#BLANK_Fp.orders_id#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_F#CHAR_Fp.customers_id#CHAR_E#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.billing_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.payment_method#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_status_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_ip#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_host_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_user_agent#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_comment#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_screen_resolution#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_color_depth#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_flash_version#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_http_accept_language#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.telecom_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.telecom_tel#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.telecom_money#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.telecom_email#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_ref#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fp.orders_ref_keywords#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fc.customers_fax#BLANK_E #CHAR_Clike '%#KEYWORD%'",
                                                  ),
                         'customers_telecom_unknow'=>array('customers_name'=>"#CASE#BLANK_Fo.customers_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.customers_name_f#BLANK_E #CHAR_Clike '%#KEYWORD%'",
                                                    'customers_email'=>"#CASE#BLANK_Fo.customers_email_address#BLANK_E #CHAR_Clike '%#KEYWORD%'", 
                                                    'customers_other'=>"#CASE#BLANK_Fo.orders_id#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_F#CHAR_Fo.customers_id#CHAR_E#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.billing_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.payment_method#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_status_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_ip#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_host_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_user_agent#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_comment#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_screen_resolution#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_color_depth#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_flash_version#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.orders_http_accept_language#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.telecom_name#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.telecom_tel#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.telecom_money#BLANK_E #CHAR_Clike '%#KEYWORD%' or #CASE#BLANK_Fo.telecom_email#BLANK_E #CHAR_Clike '%#KEYWORD%'",
                                                  ),
                                                );

    //查询表
    $table_array = array('customers_customers'=>"select c.site_id site_id,1 info_type,c.customers_id info_id,concat(c.customers_lastname,' ',c.customers_firstname) info_name,c.customers_email_address info_email,ci.customers_info_date_account_created info_time from ".TABLE_CUSTOMERS." c left join ".TABLE_CUSTOMERS_INFO." ci on c.customers_id=ci.customers_info_id where c.site_id".$sql_site_where,
                         'customers_orders'=>"select o.site_id,2 info_type,concat(o.orders_id,'') info_id,o.customers_name info_name,o.customers_email_address info_email,o.date_purchased info_time from ".TABLE_ORDERS." o left join ".TABLE_CUSTOMERS." c on o.customers_id=c.customers_id where o.site_id".$sql_site_where, 
                         'customers_preorders'=>"select p.site_id,3 info_type,concat(p.orders_id,'') info_id,p.customers_name info_name,p.customers_email_address info_email,p.date_purchased info_time from ".TABLE_PREORDERS." p left join ".TABLE_CUSTOMERS." c on p.customers_id=c.customers_id where p.site_id".$sql_site_where,
                         'customers_telecom_unknow'=>"select o.site_id site_id,4 info_type,t.id info_id,o.customers_name info_name,o.customers_email_address info_email,o.date_purchased info_time from telecom_unknow t left join ".TABLE_ORDERS." o on t.option=o.telecom_option where o.telecom_option!='' and t.option!='' and o.site_id".$sql_site_where,
                       );
    //生成查询SQL条件
    $obj_lenght = count($search_object)-1;
    $range_lenght = count($search_range )-1;
    foreach($search_range as $range_key=>$range_value){
      $keyword_query_str .= $table_array[$range_value];
      $keyword_str = '';
      foreach($search_object as $obj_key=>$obj_value){

        if($keyword_array[$range_value][$obj_value] != ''){
          if($obj_key < $obj_lenght){
            $keyword_str .= $keyword_array[$range_value][$obj_value].' or ';
          }else{
            $keyword_str .= $keyword_array[$range_value][$obj_value];
          }
        }

      }
      if(trim($keyword_1) != ''){

        $keyword_str_1 = str_replace('#KEYWORD',$keyword_1,$keyword_str);
      }
      if(trim($keyword_2) != ''){

        $keyword_str_2 = str_replace('#KEYWORD',$keyword_2,$keyword_str);
      }
      if($condition == 0){

        if($keyword_str_2 != ''){
          $keyword_query_str .= '(('.$keyword_str_1.') or ('.$keyword_str_2.'))';
        }else{
          $keyword_query_str .= '('.$keyword_str_1.')';
        }
      }else{
        if($keyword_str_2 != ''){
          $keyword_query_str .= '(('.$keyword_str_1.') and ('.$keyword_str_2.'))';
        }else{
          $keyword_query_str .= '('.$keyword_str_1.')'; 
        }
      }
      if($range_key < $range_lenght){
        $keyword_query_str .= ' union ';
      }
    } 

    //是否区别大小写
    if(in_array('customers_case',$search_condition)){

      $keyword_query_str = str_replace('#CASE','',$keyword_query_str);
    }else{

      $keyword_query_str = str_replace('#CASE','binary ',$keyword_query_str);
    } 

    //是否区别全角、半角
    if(in_array('customers_character',$search_condition)){

      $keyword_query_str = str_replace('#CHAR_F','convert(',$keyword_query_str);
      $keyword_query_str = str_replace('#CHAR_E',' using utf8)',$keyword_query_str);
      $keyword_query_str = str_replace('#CHAR_C','collate utf8_unicode_ci ',$keyword_query_str);
    }else{

      $keyword_query_str = str_replace('#CHAR_F','',$keyword_query_str);
      $keyword_query_str = str_replace('#CHAR_E','',$keyword_query_str);
      $keyword_query_str = str_replace('#CHAR_C','',$keyword_query_str);
    }

    //是否忽略空格、换行
    if(in_array('customers_blank',$search_condition)){

      $keyword_query_str = str_replace('#BLANK_F','replace(replace(replace(',$keyword_query_str);
      $keyword_query_str = str_replace('#BLANK_E',", '\\r\\n', '' ),' ',''),'　','')",$keyword_query_str);
    }else{

      $keyword_query_str = str_replace('#BLANK_F','',$keyword_query_str);
      $keyword_query_str = str_replace('#BLANK_E','',$keyword_query_str);
    }
    //echo $keyword_query_str;
    //exit;
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php echo HEADING_TITLE; ?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/all_page.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script type="text/javascript">
<?php //验证是否输入关键字?>
function submit_check(){
  var keyword = $("#search_keyword").val();
  keyword = keyword.replace(/\s/g,'');

  if(keyword == ''){
    $("#search_error").html("<?php echo TEXT_ERROR_NULL;?>");
    $("#search_keyword").val('');
    $("#search_keyword").focus();
    return false;
  }

  return true;
}
<?php //选中不区别全角半角的同时选中不区别大小写?>
function select_case(){

  if($("#info_character").attr("checked")){
    $("#info_case").attr("checked","checked");
  }
}
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=edit/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/cID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0];
  }else{

    $belong = $href_url;
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<?php if (isset($_GET['eof']) && $_GET['eof'] == 'error') { ?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="show_error_message()">
<div id="popup_info">
<div class="popup_img"><img onclick="close_error_message()" src="images/close_error_message.gif" alt="close"></div>
<span><?php echo TEXT_EOF_ERROR_MSG;?></span>
</div>
<div id="popup_box"></div>
<? } else {?>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
<?php }?>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top" id="categories_right_td"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2" >
      <tr>
        <td>
          <?php echo tep_draw_form('search', FILENAME_CUSTOMERS_INFO,'', 'get','onsubmit="return submit_check();"'); ?>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" height="35" colspan="2"><?php echo HEADING_TITLE; ?><input type="hidden" name="action" value="search"></td>
          </tr>
          <tr>
          <td class="smallText" height="25" valign="top"><?php echo CUSTOMERS_SEARCH_FRONT_TEXT;?></td><td valign="top"><input type="text" value="<?php echo $_GET['keywords_1'];?>" size="40" style="width:310px;" id="search_keyword" name="keywords_1"><span id="search_error"><?php echo TEXT_FIELD_REQUIRED;?></span><div style="float:right;margin:0 auto; padding:0;"><input type="submit" value="<?php echo IMAGE_SEARCH;?>"></div></td> 
          </tr>
          <tr>
          <td class="smallText" height="25" valign="top"><?php echo CUSTOMERS_SEARCH_CONDITION_TEXT;?></td><td valign="top"><input type="radio" value="0" id="condition_or" name="condition" style="padding-left:0;margin-left:0;"<?php echo !isset($_GET['condition']) || $_GET['condition'] == '0' ? ' checked="checked"' : '';?>><label for="condition_or"><?php echo CUSTOMERS_SEARCH_OR_TEXT;?></label><input type="radio" value="1" id="condition_and" name="condition"<?php echo $_GET['condition'] == '1' ? ' checked="checked"' : '';?>><label for="condition_and"><?php echo CUSTOMERS_SEARCH_AND_TEXT;?></label></td> 
          </tr>
          <tr>
          <td class="smallText" height="25" valign="top"><?php echo CUSTOMERS_SEARCH_END_TEXT;?></td><td valign="top"><input type="text" value="<?php echo $_GET['keywords_2'];?>" size="40" style="width:310px;" name="keywords_2"></td> 
          </tr>
          <tr>
          <td class="smallText" height="60" valign="top"><?php echo CUSTOMERS_SEARCH_OPTION_TEXT;?></td><td valign="top">
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr><td width="25%" height="20">
          <input type="checkbox" value="customers_name" id="info_name" name="search_object[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_name',$_GET['search_object']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_name"><?php echo CUSTOMERS_SEARCH_OPTION_NAME;?></label>
          </td><td width="22%">
          <input type="checkbox" value="customers_email" id="info_email" name="search_object[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_email',$_GET['search_object']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_email"><?php echo CUSTOMERS_SEARCH_OPTION_MAIL;?></label>
          </td><td width="20%">
          <input type="checkbox" value="customers_other" id="info_other" name="search_object[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_other',$_GET['search_object']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_other"><?php echo CUSTOMERS_SEARCH_OTHER_TEXT;?></label>
          </td>
          <td>&nbsp;</td>
          </tr><tr>
          <td height="20">
          <input type="checkbox" value="customers_case" id="info_case" name="search_condition[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_case',$_GET['search_condition']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_case"><?php echo CUSTOMERS_SEARCH_TYPE_TEXT;?></label>
          </td><td>
          <input type="checkbox" value="customers_character" id="info_character" name="search_condition[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_character',$_GET['search_condition']) || !isset($_GET['action']) ? ' checked="checked"' : '';?> onclick="select_case();"><label for="info_character"><?php echo CUSTOMERS_SEARCH_CHARACTER_TEXT;?></label>
          </td><td>
          <input type="checkbox" value="customers_blank" id="info_blank" name="search_condition[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_blank',$_GET['search_condition']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_blank"><?php echo CUSTOMERS_SEARCH_BLANK_TEXT;?></label>
          </td>
          <td>&nbsp;</td>
          </tr><tr>
          <td height="20">
          <input type="checkbox" value="customers_customers" id="info_customers" name="search_range[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_customers',$_GET['search_range']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_customers"><?php echo CUSTOMERS_HEADING_CUSTOMERS_TITLE;?></label>
          </td>   
          <td>
          <input type="checkbox" value="customers_orders" id="info_orders" name="search_range[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_orders',$_GET['search_range']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_orders"><?php echo CUSTOMERS_HEADING_ORDER_TITLE;?></label>
          </td><td>
          <input type="checkbox" value="customers_preorders" id="info_preorders" name="search_range[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_preorders',$_GET['search_range']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_preorders"><?php echo CUSTOMERS_HEADING_PREORDER_TITLE;?></label>
          </td><td>
          <input type="checkbox" value="customers_telecom_unknow" id="info_telecom_unknow" name="search_range[]" style="padding-left:0;margin-left:0;"<?php echo in_array('customers_telecom_unknow',$_GET['search_range']) || !isset($_GET['action']) ? ' checked="checked"' : '';?>><label for="info_telecom_unknow"><?php echo CUSTOMERS_HEADING_TELECOM_UNKNOW_TITLE;?></label>
          </td>
          </tr>
          </table>
          </td> 
          </tr>
        </table></form></td>
      </tr>
      <tr><td>
      <tr><td>
        <?php tep_show_site_filter(FILENAME_CUSTOMERS_INFO,false,array(0));?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_customers_list">
          <tr>
            <td valign="top">
             <input type="hidden" id="search" value="<?php echo $_GET['search'];?>">
             <?php
              //排序处理
              $customers_order_sort_name = ''; 
              $customers_order_sort = ''; 
              
              if (isset($_GET['customers_sort'])) {
                if ($_GET['customers_sort_type'] == 'asc') {
                  $type_str = '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>'; 
                  $tmp_type_str = 'desc'; 
                } else {
                  $type_str = '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>'; 
                  $tmp_type_str = 'asc'; 
                }
                switch ($_GET['customers_sort']) {
                  //网站排序
                  case 'site_id':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_SITE.$type_str.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_type&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_id&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_ORDERS_ID.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_name&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_NAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_email&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_MAIL.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_order_sort_name = ' s.romaji'; 
                    break;
                  //数据来源排序
                  case 'info_type':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_type&customers_sort_type='.$tmp_type_str).'">'.HEADING_TITLE_SEARCH_TYPE.$type_str.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_id&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_ORDERS_ID.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_name&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_NAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_email&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_MAIL.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_order_sort_name = ' info_type'; 
                    break;
                  //数据ID排序
                  case 'info_id':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_type&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_id&customers_sort_type='.$tmp_type_str).'">'.HEADING_TITLE_SEARCH_ORDERS_ID.$type_str.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_name&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_NAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_email&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_MAIL.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_order_sort_name = ' info_id'; 
                    break;
                  //顾客名排序
                  case 'customers_name':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_type&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_id&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_ORDERS_ID.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_name&customers_sort_type='.$tmp_type_str).'">'.CUSTOMERS_SEARCH_OPTION_NAME.$type_str.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_email&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_MAIL.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_order_sort_name = ' info_name'; 
                    break;
                  //顾客EMAIL排序
                  case 'customers_email':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_type&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_id&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_ORDERS_ID.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_name&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_NAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_email&customers_sort_type='.$tmp_type_str).'">'.CUSTOMERS_SEARCH_OPTION_MAIL.$type_str.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
                    $customers_order_sort_name = ' info_email'; 
                    break; 
                  //作成时间排序
                  case 'update_at':
                    $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                    $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_type&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_TYPE.'</a>'; 
                    $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_id&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_ORDERS_ID.'</a>'; 
                    $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_name&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_NAME.'</a>'; 
                    $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_email&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_MAIL.'</a>'; 
                    $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_ACTION.$type_str.'</a>'; 
                    $customers_order_sort_name = ' info_time'; 
                    break;
                }
              }
              if (isset($_GET['customers_sort_type'])) {
                if ($_GET['customers_sort_type'] == 'asc') {
                  $customers_order_sort = 'asc'; 
                } else {
                  $customers_order_sort = 'desc'; 
                }
              }

              if($customers_order_sort_name != '' && $customers_order_sort!=''){ 
                $customers_order_sql = 'order by '.$customers_order_sort_name.' '.$customers_order_sort; 
              }else{
                $customers_order_sql = ''; 
              }
              if (!isset($_GET['customers_sort_type'])) {
                $customers_table_id_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=site_id&customers_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>'; 
                $customers_table_type_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_type&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_TYPE.'</a>'; 
                $customers_table_exit_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=info_id&customers_sort_type=desc').'">'.HEADING_TITLE_SEARCH_ORDERS_ID.'</a>'; 
                $customers_table_lastname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_name&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_NAME.'</a>'; 
                $customers_table_firstname_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=customers_email&customers_sort_type=desc').'">'.CUSTOMERS_SEARCH_OPTION_MAIL.'</a>'; 
                $customers_table_update_str = '<a href="'.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid', 'customers_sort', 'customers_sort_type')).'customers_sort=update_at&customers_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>'; 
              }
              //生成查询结果列表
              $customers_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
              $notice_box = new notice_box('','',$news_table_params);
              $customers_table_row = array();
              $customers_title_row = array();
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_customers(\'customers_id[]\');" disabled="disabled">'); 
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_id_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_type_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_exit_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_lastname_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => $customers_table_firstname_str);
              $customers_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53"','text' => $customers_table_update_str);
              $customers_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $customers_title_row);               
  if($keyword_query_str != ''){           
    if($customers_order_sql !=''){
      $customers_query_raw = 'select * from ('.$keyword_query_str.') info left join '.TABLE_SITES.' s on info.site_id=s.id '.$customers_order_sql;
    }else{
      $customers_query_raw = $keyword_query_str; 
    }
    $customers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $customers_query_raw, $customers_query_numrows);
    $customers_query = tep_db_query($customers_query_raw);
    $customers_numrows = tep_db_num_rows($customers_query);
    //搜索的类别
    $info_type_array = array('1'=>CUSTOMERS_HEADING_CUSTOMERS_TITLE.HEADING_TITLE_SEARCH_INFO,
                             '2'=>CUSTOMERS_HEADING_ORDER_TITLE.HEADING_TITLE_SEARCH_INFO,  
                             '3'=>CUSTOMERS_HEADING_PREORDER_TITLE.HEADING_TITLE_SEARCH_INFO,
                             '4'=>CUSTOMERS_HEADING_TELECOM_UNKNOW_TITLE.HEADING_TITLE_SEARCH_INFO,
                           ); 
    //跳转页面URL处理
    $info_url_array = array('1'=>FILENAME_CUSTOMERS.'?search=#ID',
                            '2'=>FILENAME_ORDERS.'?keywords=#ID&search_type=orders_id',
                            '3'=>FILENAME_PREORDERS.'?keywords=#ID&search_type=orders_id',
                            '4'=>'telecom_unknow.php?keywords=#ID&search_type=tid',
                           );
    while ($customers = tep_db_fetch_array($customers_query)) {
      if (
          ((!isset($_GET['cID']) || !$_GET['cID']) || (@$_GET['cID'] == $customers['info_id'])) 
          && (!isset($cInfo) || !$cInfo)
        ) {
        
        $cInfo = new objectInfo($customers);
      } 

    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
      if ($_GET['current_cuid'] == $customers['info_id']) {
        $customers_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
      } else {
        $customers_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
      }
      $customers_info = array();
      
      $customers_checkbox = '<input disabled="disabled" type="checkbox" name="customers_id[]" value="'.$customers['info_id'].'"><input type="hidden" name="customers_site_id_list[]" value="'.$customers['site_id'].'">';
      $customers_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $customers_checkbox 
          );
      $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid')).'current_cuid='.$customers['info_id']).'\';"',
           'text'   => tep_get_site_romaji_by_id($customers['site_id'])
         ); 
      $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid')).'current_cuid='.$customers['info_id']).'\';"',
           'text'   => $info_type_array[$customers['info_type']]  
          ); 
      $customers_info[] = array(
           'params' => 'class="dataTableContent"',
           'text'   => '<a target="_black" href="'.str_replace('#ID',$customers['info_id'],$info_url_array[$customers['info_type']]).'"><u>'.$customers['info_id'].'</u></a>'
          );
       $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid')).'current_cuid='.$customers['info_id']).'\';"',
           'text'   => htmlspecialchars(html_entity_decode($customers['info_name'])) 
          );
        $customers_info[] = array(
           'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_CUSTOMERS_INFO, tep_get_all_get_params(array('current_cuid')).'current_cuid='.$customers['info_id']).'\';"',
           'text'   => $customers['info_email'] 
          ); 
       $customers_info[] = array(
           'params' => 'class="dataTableContent"',
           'text'   => '<a href="javascript:void(0)">'
            .
            tep_image(DIR_WS_ICONS.'info_gray.gif', tep_datetime_short(isset($customers['info_time']) && $customers['info_time'] != null?$customers['info_time']:$customers['date_account_created'])) . '</a>'
          );
       $customers_table_row[] = array('params' => $customers_params, 'text' => $customers_info);
    }
  }
    $news_form = '';
    $notice_box->get_form($news_form);
    $notice_box->get_contents($customers_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
    //生成分页列表
    if($keyword_query_str != ''){
?>
              <tr>
                <td colspan="6"><table border="0" width="100%" cellspacing="3" cellpadding="0" class="table_list_box">
                  <tr>
                    <td>
                     <?php 
                      if($customers_numrows > 0){
                           echo ''; 
                      }else{
                           echo TEXT_DATA_EMPTY;
                      }
                   ?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $customers_split->display_count($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS_INFO); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $customers_split->display_links($customers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID', 'current_cuid'))); ?></div></td>
                  </tr>
                </table></td>
              </tr>
<?php
    }
?>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
