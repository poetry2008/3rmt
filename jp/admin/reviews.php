<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  $sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id)){
    $site_arr = $userslist['site_permission']; 
  }
  $site_permission = editPermission($site_arr, $_GET['site_id'],true);
  if(!$site_permission){
    $str_disabled = ' disabled="disabled" ';
  }else{
    $str_disabled = '';
  }

  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
    $sql_site_where = ' site_id in ('.str_replace('-', ',', $_GET['site_id']).')'; 
    $sql_list_site_where = ' r.site_id in ('.str_replace('-', ',', $_GET['site_id']).')'; 
    $tmp_site_list_array = explode('-', $_GET['site_id']);
  } else {
    $sql_site_where = ' site_id in ('.tep_get_setting_site_info(FILENAME_REVIEWS).')'; 
    $sql_list_site_where = ' r.site_id in ('.tep_get_setting_site_info(FILENAME_REVIEWS).')'; 
    $tmp_site_list_array = explode('-', tep_get_setting_site_info(FILENAME_REVIEWS));
  }
  
  $tmp_list_or_str = ''; 
  foreach ($tmp_site_list_array as $or_key => $or_value) {
    $tmp_list_or_str .= "pd.site_id = '".$or_value."' or "; 
  }
  $tmp_list_or_str = substr($tmp_list_or_str, 0, -3);
  
  if(isset($_GET['site_id'])&&$_GET['site_id']==''){
    $_GET['site_id'] = str_replace(',','-',tep_get_setting_site_info(FILENAME_REVIEWS));
  }
  

  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
/*------------------------------------
 case 'new_preview'  添加评论
 case 'setflag'      设置标志
 case 'update'       更新评论 
 case 'deleteconfirm' 确认删除评论
 -----------------------------------*/
      case 'new_preview':
        $sql_array = array(
          'reviews_id' => 'null',
          'products_id' => $_POST['products_id'],
          'customers_id' => '0',
          'customers_name' => $_POST['customers_name'] ? $_POST['customers_name'] : TEXT_NO_NAME,
          'reviews_rating' => $_POST['reviews_rating'],
          'date_added' => $_POST['year'].'-'.$_POST['m'].'-'.$_POST['d'].' '.$_POST['h'].':'.$_POST['i'].':'.$_POST['s'],
          'last_modified' => '',
          'reviews_read' => '0',
          'site_id' => $_POST['insert_site_id'],
          'reviews_status' => $_POST['reviews_status'],
        );
        tep_db_perform(TABLE_REVIEWS, $sql_array);
        $insert_id = tep_db_insert_id();
        $sql_description_array = array(
          'reviews_id' => $insert_id,
          'languages_id' => $languages_id,
          'reviews_text' => $_POST['reviews_text']
        );
        tep_db_perform(TABLE_REVIEWS_DESCRIPTION, $sql_description_array);
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath='.$_POST['cPath'].(trim($_POST['search'])?'&search='.trim($_POST['search']):'')));
        break;
      case 'setflag':
        $site_id = isset($_GET['action_sid']) ? $_GET['action_sid'] :0;
        forward401Unless(editPermission($site_arr, $site_id,true));
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['pID']) {
            $pID = (int)$_GET['pID'];
            $flag = (int)$_GET['flag'];
            tep_db_query("UPDATE ".TABLE_REVIEWS." 
                SET reviews_status = '".$flag."',
                user_update='".$_SESSION['user_name']."',
                last_modified=now()
                WHERE reviews_id = '".$pID."'");
          }
        }
        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' .  $_GET['page'].'&site_id='.$_GET['site_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'')));
        break;
      case 'update':
        if($_POST['hidden_select'] == $_POST['hidden_products_name']){
             $add_products = 0;  
        }else{
             $add_products = 1;  
        } 
        $reviews_id     = tep_db_prepare_input($_GET['rID']);
        $site_id=tep_get_rev_sid_by_id($reviews_id);
        if(!$site_id['site_id']){
          $site_id['site_id'] = $_GET['action_sid'];
        }
        if($_POST['action_type']=='insert'){
          $site_id['site_id'] = $_POST['insert_site_id'];
        }
        forward401Unless(editPermission($site_arr, $site_id['site_id'],true));
        $reviews_rating = tep_db_prepare_input($_POST['reviews_rating']);
        $last_modified  = tep_db_prepare_input($_POST['last_modified']);
        $reviews_text   = tep_db_prepare_input($_POST['reviews_text']);
        $reviews_status = tep_db_prepare_input($_POST['reviews_status']);
        $date_added     = $_POST['year'].'-'.$_POST['m'].'-'.$_POST['d'].' '.$_POST['h'].':'.$_POST['i'].':00';
        $customers_name = $_POST['customers_name'] ? $_POST['customers_name'] : TEXT_NO_NAME;
        $add_product_products_id = $_POST['add_product_products_id'];
       if($add_products == 1){
        if(isset($_POST['action_type'])&&$_POST['action_type']=='insert'){
          $sql_array = array(
            'reviews_id' => 'null',
            'products_id' => $add_product_products_id,
            'customers_id' => '0',
            'customers_name' => $_POST['customers_name'] ? $_POST['customers_name'] : TEXT_NO_NAME,
            'reviews_rating' => $_POST['reviews_rating'],
            'date_added' => $_POST['year'].'-'.$_POST['m'].'-'.$_POST['d'].' '.$_POST['h'].':'.$_POST['i'].':'.$_POST['s'],
            'last_modified' => 'now()',
            'reviews_read' => '0',
            'site_id' => $_POST['insert_site_id'],
            'reviews_status' => $_POST['reviews_status'],
            'user_added'  => $_SESSION['user_name'],
            'user_update' => $_SESSION['user_name'],
          );
          tep_db_perform(TABLE_REVIEWS, $sql_array);
          $insert_id = tep_db_insert_id();
          $sql_description_array = array(
            'reviews_id' => $insert_id,
            'languages_id' => $languages_id,
            'reviews_text' => $_POST['reviews_text']
          );
          tep_db_perform(TABLE_REVIEWS_DESCRIPTION, $sql_description_array);
          tep_db_query(" delete from " . TABLE_REVIEWS . " where reviews_id = '" . tep_db_input($reviews_id) . "'");
          tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . tep_db_input($reviews_id) . "'");
        }else{
          tep_db_query("
            update " . TABLE_REVIEWS . " 
            set products_id = '".$add_product_products_id."',
                reviews_rating = '" . tep_db_input($reviews_rating) . "', 
                last_modified = now(), 
	        user_update = '".$_SESSION['user_name']."',
                reviews_status = '".$reviews_status."',
                date_added = '".$date_added."',
                customers_name = '".$customers_name."'
            where reviews_id = '" . tep_db_input($reviews_id) . "'");
        
          tep_db_query("
              update " . TABLE_REVIEWS_DESCRIPTION . " 
              set reviews_text = '" . tep_db_input($reviews_text) . "' 
              where reviews_id = '" . tep_db_input($reviews_id) . "'");
        }
        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] .  '&site_id='.$_POST['site_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'').(isset($reviews_id)?'&rID='.$reviews_id:'')));
        break;
       } 
        tep_db_query("
            update " . TABLE_REVIEWS . " 
            set reviews_rating = '" . tep_db_input($reviews_rating) . "', 
                last_modified = now(), 
	        user_update = '".$_SESSION['user_name']."',
                reviews_status = '".$reviews_status."',
                date_added = '".$date_added."',
                customers_name = '".$customers_name."'
            where reviews_id = '" . tep_db_input($reviews_id) . "'");
        
        tep_db_query("
            update " . TABLE_REVIEWS_DESCRIPTION . " 
            set reviews_text = '" . tep_db_input($reviews_text) . "' 
            where reviews_id = '" . tep_db_input($reviews_id) . "'");

        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] .  '&site_id='.$_POST['site_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'').(isset($reviews_id)?'&rID='.$reviews_id:'')));
        break;
      case 'deleteconfirm':
        if($ocertify->npermission >= 15){
        if (!empty($_POST['review_id'])) {
                   foreach ($_POST['review_id'] as $ge_key => $ge_value) {
                   tep_db_query(" delete from " . TABLE_REVIEWS . " where reviews_id = '" . $ge_value . "'");
                   tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $ge_value . "'");
                   }
                   tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'')));
        }
        $reviews_id = tep_db_prepare_input($_GET['rID']);
        tep_db_query(" delete from " . TABLE_REVIEWS . " where reviews_id = '" . tep_db_input($reviews_id) . "'");
        tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . tep_db_input($reviews_id) . "'");
        }
        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'')));
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css?v=<?php echo $back_rand_info?>">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js&v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js&v=<?php echo $back_rand_info?>"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript" >
	var js_site_id = '<?php echo $_GET['site_id'];?>';
	var js_del_review = '<?php echo TEXT_DEL_REVIEW;?>';
	var js_reviews_self = '<?php echo $_SERVER['PHP_SELF']?>';
	var js_onetime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>';
	var js_onetime_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
	var js_review_must_select = '<?php echo TEXT_REVIEW_MUST_SELECT;?>';
	var js_review_min_length = '<?php echo REVIEW_TEXT_MIN_LENGTH;?>';
	var js_notice_totalnum_error = '<?php echo REVIEWS_NOTICE_TOTALNUM_ERROR;?>';
	var js_reviews_npermission = '<?php echo $ocertify->npermission;?>';
</script>
<script language="javascript" src="includes/javascript/admin_reviews.js?v=<?php echo $back_rand_info?>"></script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != '' && $belong_temp_array[0][0] != 'action=delete'){
  preg_match_all('/rID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0].'|||'.$belong_temp_array[0][0];
  }else{

    $belong = $href_url;
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js?v=<?php echo $back_rand_info?>"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header -->
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<input type="hidden" id="show_info_id" value="show_text_reviews" name="show_info_id">
<?php if(isset($_GET['r_sort'])&&$_GET['r_sort']){?>
<input type="hidden" id="back_sort" value="<?php echo $_GET['r_sort'];?>" name="back_sort">
<?php } ?>
<?php if(isset($_GET['r_sort_type'])&&$_GET['r_sort_type']){?>
<input type="hidden" id="back_sort_type" value="<?php echo $_GET['r_sort_type'];?>" name="back_sort_type">
<?php } ?>
<div id="show_text_reviews" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" height="40"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right">
            <?php 
            if(isset($_GET['site_id'])&&$_GET['site_id']!=''){
              $search_sid ='?site_id='.$_GET['site_id'];
            }else{
              $search_sid ='';
            }
            ?>
            <form method="GET" action="reviews.php<?php echo $search_sid;?>"> 
            <input type="text" value="<?php echo isset($_GET['product_name'])?trim($_GET['product_name']):'';?>" id="keyword" name="product_name" size="40">&nbsp;&nbsp;<input type="submit" value="<?php echo IMAGE_SEARCH;?>"> 
            <input type="hidden" name="site_id" value="<?php echo $_GET['site_id'];?>">
            </form>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
        <?php echo tep_show_site_filter(FILENAME_REVIEWS,false,array(0));?>
        <table id="show_text_list" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
                   <?php
                    $reviews_table_site_str = '';  
                    $reviews_table_products_name_str = '';  
                    $reviews_table_rating_str = '';  
                    $reviews_table_date_added_str = '';  
                    $reviews_table_status_str = '';  
                    $reviews_table_date_edit_str = '';  
                    $reviews_order_sort_name = ' date_added'; 
                    $reviews_order_sort = 'desc';
                    if (isset($_GET['r_sort'])) {
                      if ($_GET['r_sort_type'] == 'asc') {
                        $type_str = '<font color="#facb9c">'.TEXT_SORT_ASC.'</font><font color="#c0c0c0">'.TEXT_SORT_DESC.'</font>'; 
                        $tmp_type_str = 'desc'; 
                      } else {
                        $type_str = '<font color="#c0c0c0">'.TEXT_SORT_ASC.'</font><font color="#facb9c">'.TEXT_SORT_DESC.'</font>'; 
                        $tmp_type_str = 'asc'; 
                      }
                    }
                    $reviews_order_help_sotr = ' reviews_id';
                    switch ($_GET['r_sort']) {
                      case 'r_site';
                        $reviews_table_site_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_site&r_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_SITE.$type_str.'</a>';  
                        $reviews_table_products_name_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_name&r_sort_type=desc').'">'.TABLE_HEADING_PRODUCTS.'</a>';  
                        $reviews_table_rating_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_rate&r_sort_type=desc').'">'.TABLE_HEADING_RATING.'</a>';  
                        $reviews_table_date_added_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_added&r_sort_type=desc').'">'.TABLE_HEADING_DATE_ADDED.'</a>';  
                        $reviews_table_status_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_status&r_sort_type=desc').'">'.TABLE_HEADING_STATUS.'</a>';  
                        $reviews_table_date_edit_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_update&r_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>';  
                        $reviews_order_sort_name = ' romaji'; 
                        break;
                      case 'r_name';
                        $reviews_table_site_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_site&r_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>';  
                        $reviews_table_products_name_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_name&r_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_PRODUCTS.$type_str.'</a>';  
                        $reviews_table_rating_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_rate&r_sort_type=desc').'">'.TABLE_HEADING_RATING.'</a>';  
                        $reviews_table_date_added_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_added&r_sort_type=desc').'">'.TABLE_HEADING_DATE_ADDED.'</a>';  
                        $reviews_table_status_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_status&r_sort_type=desc').'">'.TABLE_HEADING_STATUS.'</a>';  
                        $reviews_table_date_edit_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_update&r_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>';  
                        $reviews_order_sort_name = ' products_name'; 
                        break;
                      case 'r_rate';
                        $reviews_table_site_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_site&r_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>';  
                        $reviews_table_products_name_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_name&r_sort_type=desc').'">'.TABLE_HEADING_PRODUCTS.'</a>';  
                        $reviews_table_rating_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_rate&r_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_RATING.$type_str.'</a>';  
                        $reviews_table_date_added_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_added&r_sort_type=desc').'">'.TABLE_HEADING_DATE_ADDED.'</a>';  
                        $reviews_table_status_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_status&r_sort_type=desc').'">'.TABLE_HEADING_STATUS.'</a>';  
                        $reviews_table_date_edit_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_update&r_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>';  
                        $reviews_order_sort_name = ' reviews_rating'; 
                        break;
                      case 'r_added';
                        $reviews_table_site_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_site&r_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>';  
                        $reviews_table_products_name_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_name&r_sort_type=desc').'">'.TABLE_HEADING_PRODUCTS.'</a>';  
                        $reviews_table_rating_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_rate&r_sort_type=desc').'">'.TABLE_HEADING_RATING.'</a>';  
                        $reviews_table_date_added_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_added&r_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_DATE_ADDED.$type_str.'</a>';  
                        $reviews_table_status_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_status&r_sort_type=desc').'">'.TABLE_HEADING_STATUS.'</a>';  
                        $reviews_table_date_edit_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_update&r_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>';  
                        $reviews_order_sort_name = ' date_added'; 
                        break;
                      case 'r_status';
                        $reviews_table_site_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_site&r_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>';  
                        $reviews_table_products_name_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_name&r_sort_type=desc').'">'.TABLE_HEADING_PRODUCTS.'</a>';  
                        $reviews_table_rating_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_rate&r_sort_type=desc').'">'.TABLE_HEADING_RATING.'</a>';  
                        $reviews_table_date_added_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_added&r_sort_type=desc').'">'.TABLE_HEADING_DATE_ADDED.'</a>';  
                        $reviews_table_status_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_status&r_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_STATUS.$type_str.'</a>';  
                        $reviews_table_date_edit_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_update&r_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>';  
                        $reviews_order_sort_name = ' reviews_status'; 
                        break;
                      case 'r_update';
                        $reviews_table_site_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_site&r_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>';  
                        $reviews_table_products_name_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_name&r_sort_type=desc').'">'.TABLE_HEADING_PRODUCTS.'</a>';  
                        $reviews_table_rating_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_rate&r_sort_type=desc').'">'.TABLE_HEADING_RATING.'</a>';  
                        $reviews_table_date_added_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_added&r_sort_type=desc').'">'.TABLE_HEADING_DATE_ADDED.'</a>';  
                        $reviews_table_status_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_status&r_sort_type=desc').'">'.TABLE_HEADING_STATUS.'</a>';  
                        $reviews_table_date_edit_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_update&r_sort_type='.$tmp_type_str).'">'.TABLE_HEADING_ACTION.$type_str.'</a>';  
                        $reviews_order_sort_name = ' last_modified'; 
                        break;
                    }
                    if (isset($_GET['r_sort_type'])) {
                      if ($_GET['r_sort_type'] == 'asc') {
                        $reviews_order_sort = 'asc'; 
                      } else {
                        $reviews_order_sort = 'desc'; 
                      }
                    }
                    $reviews_order_sql = $reviews_order_sort_name.' '.$reviews_order_sort.' , '.$reviews_order_help_sotr.' '.$reviews_order_sort; 
                   
                    if (!isset($_GET['r_sort_type'])) {
                      $reviews_table_site_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_site&r_sort_type=desc').'">'.TABLE_HEADING_SITE.'</a>';  
                      $reviews_table_products_name_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_name&r_sort_type=desc').'">'.TABLE_HEADING_PRODUCTS.'</a>';  
                      $reviews_table_rating_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_rate&r_sort_type=desc').'">'.TABLE_HEADING_RATING.'</a>';  
                      $reviews_table_date_added_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_added&r_sort_type=desc').'">'.TABLE_HEADING_DATE_ADDED.'</a>';  
                      $reviews_table_status_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_status&r_sort_type=desc').'">'.TABLE_HEADING_STATUS.'</a>';  
                      $reviews_table_date_edit_str = '<a href="'.tep_href_link(FILENAME_REVIEWS, tep_get_all_get_params(array('action', 'r_sort_type', 'r_sort', 'site_id')).'r_sort=r_update&r_sort_type=desc').'">'.TABLE_HEADING_ACTION.'</a>';  
                    }
                    
                    $review_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
                    $notice_box = new notice_box('','',$review_table_params);
                    $review_table_row = array();
                    $review_title_row = array();
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '<input type="checkbox" name="all_check" onclick="all_select_review(\'review_id[]\');">' );
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $reviews_table_site_str);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent_order"', 'text' => $reviews_table_products_name_str);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"', 'text' => $reviews_table_rating_str);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"', 'text' => $reviews_table_date_added_str);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="center"', 'text' => $reviews_table_status_str);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"', 'text' => $reviews_table_date_edit_str);
                    $review_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $review_title_row);
    if(isset($_GET['product_name']) && $_GET['product_name']){
       $p_list_arr = array();
       $p_list_arr_site = array();
       if(isset($_GET['site_id'])&&$_GET['site_id']){
         $p_list_arr_site_sql = "select products_id,products_name from ".
           TABLE_PRODUCTS_DESCRIPTION." where 
           products_name like '%".trim($_GET['product_name'])."%' 
           and ".$sql_site_where;
         $p_list_arr_site_query = tep_db_query($p_list_arr_site_sql);
         while($p_list_arr_site_res = tep_db_fetch_array($p_list_arr_site_query)){
           $p_list_arr[] = $p_list_arr_site_res['products_id'];
           $p_list_arr_site[$p_list_arr_site_res['products_id']] =
           $p_list_arr_site_res['products_name'];
         }
       }
       if(isset($_GET['site_id'])&&$_GET['site_id']){
         $p_list_arr_sql = "SELECT products_id FROM ".TABLE_PRODUCTS_DESCRIPTION." 
           WHERE site_id = 0 
           and products_name like '%".trim($_GET['product_name'])."%'
           and products_id not in 
           (select products_id FROM ".TABLE_PRODUCTS_DESCRIPTION." 
            where ".$sql_site_where.")";
       }else{
         $p_list_arr_sql = "select products_id from ".
           TABLE_PRODUCTS_DESCRIPTION." where 
           products_name like '%".trim($_GET['product_name'])."%' 
           and site_id = 0";
       }
         $p_list_arr_query = tep_db_query($p_list_arr_sql);
         while($p_list_arr_res = tep_db_fetch_array($p_list_arr_query)){
           if(!in_array($p_list_arr_res['products_id'],$p_list_arr)){
             $p_list_arr[] = $p_list_arr_res['products_id'];
             $p_list_arr_site[$p_list_arr_res['products_id']] =
             $p_list_arr_res['products_name'];
           }
         }
         $where_str = ' and r.products_id in ('.implode(',',$p_list_arr).') ';
    }
    $reviews_query_raw = "
      select * from (select r.reviews_id, 
             r.products_id, 
             r.date_added, 
             r.last_modified, 
	     r.user_added,
	     r.user_update,
             r.site_id,
             r.reviews_rating, 
             r.reviews_status,
             s.romaji,
             pd.products_name,
             s.name as site_name
     from " . TABLE_REVIEWS . " r, ".TABLE_SITES." s, ".TABLE_PRODUCTS." p, ".TABLE_PRODUCTS_DESCRIPTION." pd 
     where (pd.site_id = r.site_id or pd.site_id = '0') and r.site_id = s.id
       and p.products_id = r.products_id
       and p.products_id = pd.products_id
       and pd.language_id = '".$languages_id."'
       and " . $sql_list_site_where . "".$where_str."
     ) p group by reviews_id order by ".$reviews_order_sql;
    
    $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $reviews_query_raw, $reviews_query_numrows);
    $reviews_query = tep_db_query($reviews_query_raw);
    $rows = 0;
    while ($reviews = tep_db_fetch_array($reviews_query)) {
      $self_link = false;
      $rows++;
      if ( ((!isset($_GET['rID']) || !$_GET['rID']) || ($_GET['rID'] == $reviews['reviews_id'])) && (!isset($rInfo) || !$rInfo) ) {
        $reviews_text_query = tep_db_query("
            select r.reviews_read, 
                   r.customers_name, 
                   r.site_id,
                   length(rd.reviews_text) as reviews_text_size 
            from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd 
            where r.reviews_id = '" . $reviews['reviews_id'] . "' 
              and r.reviews_id = rd.reviews_id");
        $reviews_text = tep_db_fetch_array($reviews_text_query);

        $img_array =
          tep_products_images($reviews['products_id'],$reviews['site_id']);
        
        $products_name_query = tep_db_query("
            select products_name 
            from " . TABLE_PRODUCTS_DESCRIPTION . " 
            where products_id = '" . $reviews['products_id'] . "' 
              and site_id ='0'
              and language_id = '" . $languages_id . "'");
        $products_name = tep_db_fetch_array($products_name_query);

        $reviews_average_query = tep_db_query("
            select (avg(reviews_rating) / 5 * 100) as average_rating 
            from " . TABLE_REVIEWS . " 
            where products_id = '" . $reviews['products_id'] . "'
          ");
        $reviews_average = tep_db_fetch_array($reviews_average_query);

        $review_info = tep_array_merge($reviews_text, $reviews_average, $products_name);
        $rInfo_array = tep_array_merge($reviews, $review_info, $products_image);
        $rInfo = new objectInfo($rInfo_array);
      }

      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( (isset($rInfo) && is_object($rInfo)) && ($reviews['reviews_id'] == $rInfo->reviews_id) ) {
        if(!($rows==1&&!isset($_GET['rID']))){
          $self_link = true;
          $nowColor = 'dataTableRowSelected';
        }else{
          if($rows==1){
            $self_link = true;
            $nowColor = 'dataTableRowSelected';
          }
        }
      }
      $review_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
      $review_info = array();
      $site_array = explode(',',$site_arr);
      if(in_array($reviews['site_id'],$site_array)){
          $reviews_checkbox = '<input type="checkbox" name="review_id[]" value="'.$reviews['reviews_id'].'">';
      }else{
          $reviews_checkbox = '<input disabled="disabled" type="checkbox" name="review_id[]" value="'.$reviews['reviews_id'].'">';
      }
      if(!$self_link){
        $td_review_params = ' onclick="document.location.href=\'' .tep_href_link(FILENAME_REVIEWS, 'page=' .  $_GET['page'].'&site_id='.$_GET['site_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'')) .(isset($reviews['reviews_id'])?'&rID='.$reviews['reviews_id']:''). '\'"';
      }
      $review_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $reviews_checkbox 
      );
 
      $review_info[] = array(
          'params' => 'class="dataTableContent"'.$td_review_params,
          'text'   => ''.$reviews['romaji']
      );
      $review_info[] = array(
          'params' => 'class="dataTableContent"'.$td_review_params,
          'text'   =>
          tep_get_products_name($reviews['products_id'],$languages_id,$reviews['site_id'],true)
      );
      $action_sid_str = '&action_sid='.$reviews['site_id'];
      if ($reviews['reviews_status'] == '1') {
        if(in_array($reviews['site_id'],$site_array)){
          $review_image = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="toggle_reviews_action(\'' . tep_href_link(FILENAME_REVIEWS, 'action=setflag&flag=0&action_sid='.$reviews['site_id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'&page=' . (isset($_GET['page'])?$_GET['page']:'') . '&pID=' .  $reviews['reviews_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT) . '</a>';
        } else {
          $review_image = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT);
        }
      } else {
        if(in_array($reviews['site_id'],$site_array)){
          $review_image = '<a href="javascript:void(0);" onclick="toggle_reviews_action(\'' . tep_href_link(FILENAME_REVIEWS, 'action=setflag&flag=1&action_sid='.$reviews['site_id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'&page=' . (isset($_GET['page'])?$_GET['page']:'') . '&pID=' .  $reviews['reviews_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
        } else {
          $review_image = tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
        }
      }
      $review_info[] = array(
          'params' => 'class="dataTableContent" align="center"'.$td_review_params,
          'text'   => $reviews['reviews_rating'] 
      );
       $review_info[] = array(
          'params' => 'class="dataTableContent" align="center"'.$td_review_params,
          'text'   =>  tep_date_short($reviews['date_added']) . ' ' .date('H:i:s', strtotime($reviews['date_added'])) 
      );
      $review_info[] = array(
          'params' => 'class="dataTableContent" align="center"',
          'text'   => ''.$review_image 
      );
      if(empty($_GET['site_id'])){ $_GET['site_id'] = ''; } 
      $review_date_info = (tep_not_null($reviews['last_modified']) && ($reviews['last_modified'] != '0000-00-00 00:00:00'))?$reviews['last_modified']:$reviews['date_added'];
      $review_info[] = array(
          'params' => 'class="dataTableContent" align="right"',
          'text'   => '<a href="javascript:void(0);" onclick="show_text_reviews(this,\''.$_GET['page'].'\',\''.$reviews['reviews_id'].'\',\''.$_GET['site_id'].'\',\''.$reviews['site_id'].'\', \''.(isset($_GET['r_sort'])?$_GET['r_sort']:'').'\', \''.(isset($_GET['r_sort_type'])?$_GET['r_sort_type']:'').'\')">'.  tep_get_signal_pic_info($review_date_info).'</a>'
      );
    $review_table_row[] = array('params' => $review_params, 'text' => $review_info);
    }
    $review_form = tep_draw_form('del_review', FILENAME_REVIEWS, 'page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&action=deleteconfirm'.(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'').(isset($_GET['r_sort'])?'&r_sort='.$_GET['r_sort']:'').(isset($_GET['r_sort_type'])?'&r_sort_type='.$_GET['r_sort_type']:''));
    $notice_box->get_form($review_form);
    $notice_box->get_contents($review_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
?>
            </table>
		<table border="0" width="100%" cellspacing="0" cellpadding="0" class="table_list_box">
                  <tr>
                    <td colspan="2">
                      <?php 
                      if($ocertify->npermission >= 15){
                      ?>
                      <select name="reviews_action" onchange="review_change_action(this.value, 'review_id[]');">
                        <option value="0"><?php echo TEXT_REVIEWS_SELECT_ACTION;?></option> 
                        <option value="1"><?php echo TEXT_REVIEWS_DELETE_ACTION;?></option> 
                      </select>
                    <?php }?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $reviews_split->display_count($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                    <td class="smallText" align="right">
					<div class="td_box"><?php echo $reviews_split->display_links($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'rID'))); ?></div></td>
                  </tr>
                  <tr>
                    <td class="smallText" align="right" colspan="2">
                     <div class="td_button">   
                      <button type="button" onclick="show_text_reviews(this,'<?php echo $_GET['page']; ?>','0','<?php echo $_GET['site_id'];?>','','<?php echo (isset($_GET['r_sort'])?$_GET['r_sort']:'');?>', '<?php echo (isset($_GET['r_sort_type'])?$_GET['r_sort_type']:'');?>')"><?php echo IMAGE_NEW_PROJECT;?></button>
                      </div>
                    </td>
                  </tr>
                </table>
			</td>
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
