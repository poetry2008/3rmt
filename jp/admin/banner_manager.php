<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  $banner_extension = tep_banner_image_extension();
  if(isset($_GET['site_id'])&&$_GET['site_id']!='') {
     $sql_site_where = 's.id in ('.str_replace('-', ',', $_GET['site_id']).')';
     $show_list_array = explode('-',$_GET['site_id']);
  }else {
     $show_list_str = tep_get_setting_site_info(FILENAME_BANNER_MANAGER);
     $sql_site_where = 's.id in ('.$show_list_str.')';
     $show_list_array = explode(',',$show_list_str);
  }
  $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
      while($userslist= tep_db_fetch_array($sites_id_sql)){
            $site_arr = $userslist['site_permission'];
      }
               if(!isset($_GET['type']) || $_GET['type'] == ''){
                         $_GET['type'] = 'asc';
                }
                if($present_type == ''){
                   $present_type = 'asc';
                }
                if(!isset($_GET['sort']) || $_GET['sort'] == ''){
                     $banner_str = ' banners_id desc';
                }else if($_GET['sort'] == 'site_name'){
                     if($_GET['type'] == 'desc'){
                        $banner_str = 'romaji desc';
                        $banner_type = 'asc';
                      }else{
                        $banner_str = 'romaji asc';
                        $banner_type = 'desc';
                      }
                }else if($_GET['sort'] == 'title'){
                     if($_GET['type'] == 'desc'){
                        $banner_str = 'banners_title desc';
                        $banner_type = 'asc';
                      }else{
                        $banner_str = 'banners_title asc';
                        $banner_type = 'desc';
                      }
                }else if($_GET['sort'] == 'banners_group'){
                     if($_GET['type'] == 'desc'){
                        $banner_str = 'banners_group desc';
                        $banner_type = 'asc';
                      }else{
                        $banner_str = 'banners_group asc';
                        $banner_type = 'desc';
                      }
                }else if($_GET['sort'] == 'banners_shown'){
                     if($_GET['type'] == 'desc'){
                        $banner_str = 'banners_shown desc';
                        $banner_type = 'asc';
                      }else{
                        $banner_str = 'banners_shown asc';
                        $banner_type = 'desc';
                      }
                }else if($_GET['sort'] == 'status'){
                     if($_GET['type'] == 'desc'){
                        $banner_str = 'status desc';
                        $banner_type = 'asc';
                      }else{
                        $banner_str = 'status asc';
                        $banner_type = 'desc';
                      }
                }else if($_GET['sort'] == 'date_update'){
                     if($_GET['type'] == 'desc'){
                        $banner_str = 'date_update desc';
                        $banner_type = 'asc';
                      }else{
                        $banner_str = 'date_update asc';
                        $banner_type = 'desc';
                      }
                }
             if($_GET['sort'] == 'site_name'){
                if($_GET['type'] == 'desc'){
                    $banner_site_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                }else{
                    $banner_site_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                }
             }
             if($_GET['sort'] == 'title'){
                if($_GET['type'] == 'desc'){
                    $banner_title = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                }else{
                    $banner_title = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                }
             }
             if($_GET['sort'] == 'banners_group'){
                if($_GET['type'] == 'desc'){
                    $banner_group = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                }else{
                    $banner_group = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                }
             }
             if($_GET['sort'] == 'banners_shown'){
                if($_GET['type'] == 'desc'){
                    $banner_shown = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                }else{
                    $banner_shown = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                }
             }
             if($_GET['sort'] == 'status'){
                if($_GET['type'] == 'desc'){
                    $banner_status = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                }else{
                    $banner_status = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                }
             }
             if($_GET['sort'] == 'date_update'){
                if($_GET['type'] == 'desc'){
                    $banner_date_update = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                }else{
                    $banner_date_update = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                }
             }
  $site_array = explode(',',$site_arr);
  if (isset($_GET['action']) && $_GET['action']) {
   switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'setflag' 设置banner状态   
   case 'insert' 创建banner     
   case 'update' 更新banner     
   case 'deleteconfirm' 删除banner      
------------------------------------------------------*/
      case 'setflag':
        $banner_exists_raw = tep_db_query("select * from ".TABLE_BANNERS." where banners_id = '".(int)$_GET['bID']."' and site_id = '".$_GET['site_id']."'");        
        $banner_exists = tep_db_fetch_array($banner_exists_raw);
        if ($banner_exists) {
          if (($_GET['flag'] == '0') || ($_GET['flag'] == '1')) {
            if($_GET['flag'] == '1'){
            tep_db_query("update  ".TABLE_BANNERS." set expires_impressions = NULL,date_status_change = NULL,status = '".$_GET['flag']."', date_scheduled = '".$banner_exists['date_scheduled']."',user_update = '".$_SESSION['user_name']."',date_update = now() where banners_id = '".(int)$_GET['bID']."' and site_id = '".$_GET['site_id']."'");
            }else{
            tep_db_query("update  ".TABLE_BANNERS." set date_status_change = now(),status = '".$_GET['flag']."', date_scheduled = '".$banner_exists['date_scheduled']."',user_update = '".$_SESSION['user_name']."',date_update = now() where banners_id = '".(int)$_GET['bID']."' and site_id = '".$_GET['site_id']."'");
            }
            $messageStack->add_session(SUCCESS_BANNER_STATUS_UPDATED, 'success');
          } else {
            $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
          }
        } else {
          $messageStack->add_session(ERROR_UNKNOWN_STATUS_FLAG, 'error');
        }
        tep_redirect(tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&sort='.$_GET['sort'].'&type='.$_GET['type'].'&bID=' . $_GET['bID'] .  (isset($_GET['lsite_id'])?('&site_id='.$_GET['lsite_id']):'')));
        break;
      case 'insert':
        if (empty($site_id)) {
          $messageStack->add(SITE_ID_NOT_NULL, 'error');
          $banner_error = true;
        }
      case 'update':
        $site_id              = tep_db_prepare_input($_POST['site_id']);

        $banners_id           = tep_db_prepare_input($_POST['banners_id']);
        $banners_title        = tep_db_prepare_input($_POST['banners_title']);
        $banners_url          = tep_db_prepare_input($_POST['banners_url']);
        $new_banners_group    = tep_db_prepare_input($_POST['new_banners_group']);
        $banners_group        = (empty($new_banners_group)) ? tep_db_prepare_input($_POST['banners_group']) : $new_banners_group;
        $html_text            = tep_db_prepare_input($_POST['html_text']);
        $banners_image        = tep_get_uploaded_file('banners_image');
        $banners_image_local  = tep_db_prepare_input($_POST['banners_image_local']);
        $banners_image_target = tep_db_prepare_input($_POST['banners_image_target']);
        $db_image_location    = '';

        $banners = tep_get_banner($banners_id);
        $image_directory      = tep_get_local_path(tep_get_upload_dir(isset($banners['site_id']) ? $banners['site_id']: $site_id ) . $banners_image_target);

        $banner_error = false;
        if (empty($banners_title)) {
          $messageStack->add(ERROR_BANNER_TITLE_REQUIRED, 'error');
          $banner_error = true;
        }
        if (empty($banners_group)) {
          $messageStack->add(ERROR_BANNER_GROUP_REQUIRED, 'error');
          $banner_error = true;
        }
        if ( (isset($banners_image)) && ($banners_image['name'] != 'none') && (is_uploaded_file($banners_image['tmp_name'])) ) {
          $store_image = false;
          if (!is_writeable($image_directory)) {
            if (is_dir($image_directory)) {
              $messageStack->add(sprintf(ERROR_IMAGE_DIRECTORY_NOT_WRITEABLE, $image_directory), 'error');
            } else {
              $messageStack->add(sprintf(ERROR_IMAGE_DIRECTORY_DOES_NOT_EXIST, $image_directory), 'error');
            }
            $banner_error = true;
          } else {
            $store_image = true;
          }
        }

        if (!$banner_error) {
          if ( (empty($html_text)) && ($store_image == true) ) {
            tep_copy_uploaded_file($banners_image, $image_directory);
          }
          $db_image_location = $banners_image['name'];
          if($db_image_location == ''){
             $db_image_location = $_POST['banners_image_local'];
          }
          $sql_data_array = array('banners_title'     => $banners_title,
                                  'banners_url'       => $banners_url,
                                  'banners_image'     => $db_image_location,
                                  'banners_group'     => $banners_group,
				  'banners_html_text' => $html_text,
				  'user_update' => $_SESSION['user_name'],
				  'date_update' => date('Y-m-d H:i:s',time()),
                                  'banners_show_type' => $_POST['banner_show_type']
			  );
          if ($_GET['action'] == 'insert') {
            $insert_sql_data = array('date_added' => date('Y-m-d H:i:s',time()),
		                     'user_added' => $_SESSION['user_name'],
                                      'status' => '1',
                                      'site_id' => $site_id,
                                      'banners_show_type' => $_POST['banner_show_type'],
                                      'date_scheduled' => $_POST['date_scheduled'],
                                      'expires_date'   => $_POST['expires_date']
                                     );
            $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
            tep_db_perform(TABLE_BANNERS, $sql_data_array);
            $banners_id = tep_db_insert_id();
            $messageStack->add_session(SUCCESS_BANNER_INSERTED, 'success');
          } elseif ($_GET['action'] == 'update') {
            tep_db_perform(TABLE_BANNERS, $sql_data_array, 'update', 'banners_id =
                \'' . $banners_id . '\' and site_id =\''.$site_id.'\'');
            $messageStack->add_session(SUCCESS_BANNER_UPDATED, 'success');
          }

            $expires_date = tep_db_prepare_input($_POST['expires_date']);
          if (isset($_POST['expires_date']) && $_POST['expires_date'] && $_POST['expires_date'] != ' ') {
            list($day, $month, $year) = explode('/', $expires_date);

            $expires_date = $year .
                            ((strlen($month) == 1) ? '0' . $month : $month) .
                            ((strlen($day) == 1) ? '0' . $day : $day);
            tep_db_query(" update " . TABLE_BANNERS . " set expires_date = '" . tep_db_input($expires_date) . "', expires_impressions = null where banners_id = '" . $banners_id . "' and site_id = '" .$site_id."' ");
          } elseif (isset($_POST['impressions']) && $_POST['impressions']) {
            $impressions = tep_db_prepare_input($_POST['impressions']);
            tep_db_query(" update " . TABLE_BANNERS . " set expires_impressions = '" . tep_db_input($impressions) . "', expires_date = '".tep_db_input($expires_date)."' where banners_id = '" . $banners_id . "' and site_id = '" .$site_id."' ");    
          }else if($_POST['expires_date'] == ''){
            tep_db_query(" update " . TABLE_BANNERS . " set date_scheduled = '".$_POST['date_scheduled']."',expires_date = '" . tep_db_input($expires_date) . "' where banners_id = '" . $banners_id . "' and site_id = '" .$site_id."' ");
          }

          if ($_POST['date_scheduled'] != ' ') {
            $date_scheduled = tep_db_prepare_input($_POST['date_scheduled']);
            list($day, $month, $year) = explode('/', $date_scheduled);

            $date_scheduled = $year .
                              ((strlen($month) == 1) ? '0' . $month : $month) .
                              ((strlen($day) == 1) ? '0' . $day : $day);

            tep_db_query("
                update " . TABLE_BANNERS . " 
                set status = '0', 
                    date_scheduled = '" . tep_db_input($date_scheduled) . "' 
                where banners_id = '" . $banners_id . "'
                and site_id = '" .$site_id."'
                ");
          }
          tep_redirect(tep_href_link(FILENAME_BANNER_MANAGER, 'page=' .  $_GET['page'] . '&bID=' . $banners_id .  (isset($_GET['lsite_id'])?('&site_id='.$_GET['lsite_id']):'').($_GET['sort']?'&sort='.$_GET['sort']:'').($_GET['type']?'&type='.$_GET['type']:'')));
        } else {
          $_GET['action'] = 'new';
        }
        break;
      case 'deleteconfirm':
        $banners_id   = tep_db_prepare_input($_GET['bID']);
        $delete_image = tep_db_prepare_input($_POST['delete_image']);
        if(!empty($_POST['banner_id'])){
         foreach($_POST['banner_id'] as $ge_key => $ge_value){
          $banner_query = tep_db_query(" select * from " . TABLE_BANNERS . " where banners_id = '" .$ge_value. "' ");
          $banner = tep_db_fetch_array($banner_query);
          if (is_file(tep_get_upload_dir($banner['site_id']). $banner['banners_image'])) {
            if (is_writeable(DIR_FS_CATALOG_IMAGES . $banner['banners_image'])) {
              unlink(DIR_FS_CATALOG_IMAGES . $banner['banners_image']);
            } else {
              $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
            }
          } else {
            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
          }

        tep_db_query("delete from " . TABLE_BANNERS . " where banners_id = '" .  $ge_value . "'");
        tep_db_query("delete from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $ge_value . "'");
          } 
        }
          $banner_query = tep_db_query(" select * from " . TABLE_BANNERS . " where banners_id = '" . tep_db_input($banners_id) . "' ");
          $banner = tep_db_fetch_array($banner_query);
          if (is_file(tep_get_upload_dir($banner['site_id']). $banner['banners_image'])) {
            if (is_writeable(DIR_FS_CATALOG_IMAGES . $banner['banners_image'])) {
              unlink(DIR_FS_CATALOG_IMAGES . $banner['banners_image']);
            } else {
              $messageStack->add_session(ERROR_IMAGE_IS_NOT_WRITEABLE, 'error');
            }
          } else {
            $messageStack->add_session(ERROR_IMAGE_DOES_NOT_EXIST, 'error');
          }
        tep_db_query("delete from " . TABLE_BANNERS . " where banners_id = '" . tep_db_input($banners_id) . "'");
        tep_db_query("delete from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . tep_db_input($banners_id) . "'");

        if ( (function_exists('imagecreate')) && ($banner_extension) ) {
          if (is_file(DIR_WS_IMAGES . 'graphs/banner_infobox-' . $banners_id . '.' . $banner_extension)) {
            if (is_writeable(DIR_WS_IMAGES . 'graphs/banner_infobox-' . $banners_id . '.' . $banner_extension)) {
              unlink(DIR_WS_IMAGES . 'graphs/banner_infobox-' . $banners_id . '.' . $banner_extension);
            }
          }

          if (is_file(DIR_WS_IMAGES . 'graphs/banner_yearly-' . $banners_id . '.' . $banner_extension)) {
            if (is_writeable(DIR_WS_IMAGES . 'graphs/banner_yearly-' . $banners_id . '.' . $banner_extension)) {
              unlink(DIR_WS_IMAGES . 'graphs/banner_yearly-' . $banners_id . '.' . $banner_extension);
            }
          }

          if (is_file(DIR_WS_IMAGES . 'graphs/banner_monthly-' . $banners_id . '.' . $banner_extension)) {
            if (is_writeable(DIR_WS_IMAGES . 'graphs/banner_monthly-' . $banners_id . '.' . $banner_extension)) {
              unlink(DIR_WS_IMAGES . 'graphs/banner_monthly-' . $banners_id . '.' . $banner_extension);
            }
          }

          if (is_file(DIR_WS_IMAGES . 'graphs/banner_daily-' . $banners_id . '.' . $banner_extension)) {
            if (is_writeable(DIR_WS_IMAGES . 'graphs/banner_daily-' . $banners_id . '.' . $banner_extension)) {
              unlink(DIR_WS_IMAGES . 'graphs/banner_daily-' . $banners_id . '.' . $banner_extension);
            }
          }
        }
        $messageStack->add_session(SUCCESS_BANNER_REMOVED, 'success');
        tep_redirect(tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').($_GET['sort']?'&sort='.$_GET['sort']:'').($_GET['type']?'&type='.$_GET['type']:'')));
        break;
    }
  }

// check if the graphs directory exists
  $dir_ok = false;
  if ( (function_exists('imagecreate')) && ($banner_extension) ) {
    if (is_dir(DIR_WS_IMAGES . 'graphs')) {
      if (is_writeable(DIR_WS_IMAGES . 'graphs')) {
        $dir_ok = true;
      } else {
        $messageStack->add(ERROR_GRAPHS_DIRECTORY_NOT_WRITEABLE, 'error');
      }
    } else {
      $messageStack->add(ERROR_GRAPHS_DIRECTORY_DOES_NOT_EXIST, 'error');
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
<script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js&v=<?php echo $back_rand_info?>"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript">
	var js_banner_manager_home = '<?php echo tep_href_link(FILENAME_BANNER_MANAGER);?>';
	var js_banner_manager_statistics = '<?php echo tep_href_link(FILENAME_BANNER_STATISTICS);?>'; 
	var js_banner_manager_del_news = '<?php echo TEXT_DEL_NEWS;?>';
	var js_banner_manager_self = '<?php echo $_SERVER['PHP_SELF']?>';
	var js_onetime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>';
	var js_onetime_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
	var js_banner_must_select = '<?php echo TEXT_NEWS_MUST_SELECT;?>';
	var js_banner_manager_npermission = '<?php echo $ocertify->npermission;?>';
	var js_banner_manager_sql_where = '<?php echo $sql_site_where;?>';
	var js_banner_manager_str = '<?php echo $banner_str;?>';
	var js_banner_manager_site_id = '<?php echo $_GET['site_id'];?>';
	var js_banner_manager_sort = '<?php echo $_GET['sort'];?>';
	var js_banner_manager_type = '<?php echo $_GET['type'];?>';
	var js_banner_manager_title_error = "<?php echo BANNER_TITLE_ERROR;?>";
	var js_banner_manager_group_error = "<?php echo BANNER_GROUP_ERROR;?>";
</script>
<script language="javascript" src="includes/javascript/admin_banner_manager.js?v=<?php echo $back_rand_info?>"></script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=new/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/bID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0];
  }else{

    $belong = $href_url.'?'.$belong_temp_array[0][0];
  }
}else{
  if(preg_match_all('/action=insert/',$belong,$belong_temp_array)){
    $belong = $href_url.'?action=new';
  }else{
    $belong = $href_url;
  }
}
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<div id="spiffycalendar" class="text"></div>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<input id="show_info_id" type="hidden" value="show_banner" name="show_info_id">
<div id="show_banner" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
  <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
        <?php tep_show_site_filter(FILENAME_BANNER_MANAGER,'false',array(0));?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_banner_list">
          <tr>
            <td valign="top">
            <?php 
             $banner_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
             $notice_box = new notice_box('','',$banner_table_params);
             $banner_table_row = array();
             $banner_title_row = array();
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_banner(\'banner_id[]\');">');
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '');
             if(isset($_GET['sort']) && $_GET['sort'] == 'site_name'){
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=site_name'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type='.$banner_type).'">'.TABLE_HEADING_SITE.$banner_site_name.'</a>');
             }else{
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order" nowrap','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=site_name'.($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type=desc').'">'.TABLE_HEADING_SITE.$banner_site_name.'</a>');
             }
             if(isset($_GET['sort']) && $_GET['sort'] == 'title'){
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order" colspan="3"','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=title'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type='.$banner_type).'">'.TABLE_HEADING_BANNERS.$banner_title.'</a>');
             }else{
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order" colspan="3"','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=title'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type=desc').'">'.TABLE_HEADING_BANNERS.$banner_title.'</a>');
             }
             if(isset($_GET['sort']) && $_GET['sort'] == 'banners_group'){
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=banners_group'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type='.$banner_type).'">'.TABLE_HEADING_GROUPS.$banner_group.'</a>');
             }else{
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=banners_group'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type=desc').'">'.TABLE_HEADING_GROUPS.$banner_group.'</a>');
             }
             if(isset($_GET['sort']) && $_GET['sort'] == 'banners_shown'){
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=banners_shown'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type='.$banner_type).'">'.TABLE_HEADING_STATISTICS.$banner_shown.'</a>');
             }else{
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => 
          '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=banners_shown'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type=desc').'">'.TABLE_HEADING_STATISTICS.$banner_shown.'</a>');
             }
             if(isset($_GET['sort']) && $_GET['sort'] == 'status'){
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' =>
             '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=status'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type='.$banner_type).'">'.TABLE_HEADING_STATUS.$banner_status.'</a>');
             }else{
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=status'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type=desc').'">'.TABLE_HEADING_STATUS.$banner_status.'</a>');
             }
             if(isset($_GET['sort']) && $_GET['sort'] == 'date_update'){
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53px"','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=date_update'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type='.$banner_type).'">'.TABLE_HEADING_ACTION.$banner_date_update.'</a>');
             }else{
             $banner_title_row[] = array('params' => 'class="dataTableHeadingContent_order" width="53px"','text' => '<a href="'.tep_href_link(FILENAME_BANNER_MANAGER,'sort=date_update'.  ($_GET['site_id']?'&site_id='.$_GET['site_id']:'').($_GET['bID']?'&bID='.$_GET['bID']:'').'&type=desc').'">'.TABLE_HEADING_ACTION.$banner_date_update.'</a>');
             }
             $banner_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $banner_title_row);
    $banners_query_raw = "
      select sum(h.banners_shown) as banners_shown,
             sum(h.banners_clicked) as banners_clicked,
             b.banners_id, 
             b.banners_title, 
             b.banners_image, 
             b.banners_group, 
             b.status, 
             b.expires_date, 
             b.expires_impressions, 
             b.date_status_change, 
             b.date_scheduled, 
             b.date_added,
	     b.user_added,
	     b.user_update,
	     b.date_update,
             b.banners_show_type,
             b.site_id,
             s.romaji,
             s.name as site_name
      from " . TABLE_BANNERS . " b left join  " . TABLE_BANNERS_HISTORY . " h on b.banners_id = h.banners_id , ".TABLE_SITES." s 
      where s.id = b.site_id and 
        " . $sql_site_where . "  group by b.banners_id 
      order by ".$banner_str;
    $banners_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $banners_query_raw, $banners_query_numrows);
    $banners_query = tep_db_query($banners_query_raw);
    $banner_num = tep_db_num_rows($banners_query);
        $sql_check = "select * from ".TABLE_PWD_CHECK." where page_name='/admin/banner_statistics.php'";
        $query_check = tep_db_query($sql_check);
        $arr_check = array();
        while($row_check = tep_db_fetch_array($query_check)){
             $arr_check[] = $row_check['check_value'];
        }
      if($ocertify->npermission == 7){
          $permissions = 'staff'; 
      }else if($ocertify->npermission == 15){
          $permissions = 'admin';
      }else if($ocertify->npermission == 10){
          $permissions = 'chief';
      }
   while ($banners = tep_db_fetch_array($banners_query)) {
      $banners_shown = ($banners['banners_shown'] != '') ? $banners['banners_shown'] : '0';
      $banners_clicked = ($banners['banners_clicked'] != '') ? $banners['banners_clicked'] : '0';
      
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      
      if ($banners['banners_id'] == $_GET['bID']) {
        $banner_params =  'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\';"' . "\n";
      } else {
        $banner_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\';" onmouseout="this.className=\''.$nowColor.'\'"' . "\n";
      }
      $banner_param_str = 'onclick="document.location.href=\'' .  tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' .$banners['banners_id'].  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').($_GET['sort']?'&sort='.$_GET['sort']:'').($_GET['type']?'&type='.$_GET['type']:'')) . '\'"';
      $banner_info = array();
      if(in_array($banners['site_id'],$site_array)){
          $banner_checkbox = '<input type="checkbox" name="banner_id[]" value="'.$banners['banners_id'].'">';
      }else{
          $banner_checkbox = '<input disabled="disabled" type="checkbox" name="banner_id[]" value="'.$banners['banners_id'].'">';
      }
      $banner_info[] = array(
          'params' => 'class="dataTableContent" width="1%"',
          'text'   => $banner_checkbox
          );
      $banner_info[] = array(
          'params' => 'class="dataTableContent" width="3%"'.$banner_param_str,
          'text'   => ''
          );
      $banner_info[] = array(
          'params' => 'class="dataTableContent"'.$banner_param_str,
          'text'   => $banners['romaji']
          );
      if(in_array($banners['site_id'],$site_array)){
       if($ocertify->npermission == 31){
       $banner_images = '<a href="' . tep_href_link(FILENAME_BANNER_STATISTICS, 'page=' .  $_GET['page'] . '&bID=' . $banners['banners_id'] .  (isset($banners['site_id'])?('&site_id='.$banners['site_id']):'')) . '">' . tep_image(DIR_WS_ICONS . 'statistics.gif', ICON_STATISTICS) .  '</a>';
       }else{
       if(in_array($permissions,$arr_check)){
       $banner_images = '<a href="' . tep_href_link(FILENAME_BANNER_STATISTICS, 'page=' .  $_GET['page'] . '&bID=' . $banners['banners_id'] .  (isset($banners['site_id'])?('&site_id='.$banners['site_id']):'')) . '">' . tep_image(DIR_WS_ICONS . 'statistics.gif', ICON_STATISTICS) .  '</a>';
      }else if(in_array('onetime',$arr_check)){
       $banner_images = '<a href="javascript:void(0)" onclick="onetime_images('.$ocertify->npermission.','.$_GET['page'].','.$banners['banners_id'].','.$banners['site_id'].')" >' . tep_image(DIR_WS_ICONS . 'statistics.gif', ICON_STATISTICS) .  '</a>';
      }else{
      $banner_images = tep_image(DIR_WS_ICONS . 'statistics.gif', ICON_STATISTICS);
      }
       }
      }else{
      $banner_images = tep_image(DIR_WS_ICONS . 'statistics.gif', ICON_STATISTICS);
      }
      $banner_info[] = array(
          'params' => 'class="dataTableContent" width="1%"',
          'text'   => $banner_images
          );
      $banner_info[] = array(
          'params' => 'class="dataTableContent" width="22px" align="center"',
          'text'   => '&nbsp;<a href="javascript:popupImageWindow(\'' . FILENAME_POPUP_IMAGE . '?banner=' . $banners['banners_id'] .  '&site_id='.$banners['site_id'].'\')">' . tep_image(DIR_WS_IMAGES . 'icon_popup.gif', 'View Banner') . '</a>&nbsp;'
          );
      $banner_info[] = array(
          'params' => 'class="dataTableContent"'.$banner_param_str,
          'text'   => $banners['banners_title']
          );
      $banner_info[] = array(
          'params' => 'class="dataTableContent"'.$banner_param_str,
          'text'   => $banners['banners_group']
          );
      $banner_info[] = array(
          'params' => 'class="dataTableContent"'.$banner_param_str,
          'text'   => $banners_shown . ' / ' . $banners_clicked
          );
      if ($banners['status'] == '1') {
      if(in_array($banners['site_id'],$site_array)){
        $banner_status =  tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', 'Active') .  '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="toggle_banner_action(\'' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $banners['banners_id'] .  '&action=setflag&flag=0&sort='.$_GET['sort'].'&type='.$_GET['type'] .  (isset($banners['site_id'])?('&site_id='.$banners['site_id']):'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', 'Set Inactive') . '</a>';
      }else{
        $banner_status =  tep_image(DIR_WS_IMAGES . 'icon_status_green.gif',
            'Active') .  '&nbsp;&nbsp;'. tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', 'Set Inactive');
      }
      } else {
      if(in_array($banners['site_id'],$site_array)){
        $banner_status =  '<a href="javascript:void(0);" onclick="toggle_banner_action(\'' .  tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' .  $banners['banners_id'] .  '&action=setflag&flag=1&sort='.$_GET['sort'].'&type='.$_GET['type'] .  (isset($banners['site_id'])?('&site_id='.$banners['site_id']):'').(isset($_GET['site_id']) && $_GET['site_id']?'&lsite_id='.$_GET['site_id']:'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', 'Set Active') . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', 'Inactive');
      }else{
        $banner_status =  tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', 'Set Active') . '&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', 'Inactive');
      }
      }
      $banner_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $banner_status
          );
      $banner_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => ' <a href="javascript:void(0)" onclick="show_banner(this,'.$banners['banners_id'].','.$_GET['page'].','.$banners['site_id'].');check_radio('.$banners['banners_show_type'].')">'.tep_get_signal_pic_info(isset($banners['date_update']) && $banners['date_update']?$banners['date_update']:$banners['date_added']).'</a>'
          );
      $banner_table_row[] = array('params' => $banner_params, 'text' => $banner_info);
    }
      $banner_form = tep_draw_form('del_banner',FILENAME_BANNER_MANAGER,'action=deleteconfirm&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&sort='.$_GET['sort'].'&type='.$_GET['type']);
      $notice_box->get_form($banner_form);
      $notice_box->get_contents($banner_table_row);
      $notice_box->get_eof(tep_eof_hidden());
      echo $notice_box->show_notice();
?>
	<table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
           <tr>
             <td>
               <?php
               //删除勾选触发事件
                 if($banner_num > 0){
                   if($ocertify->npermission >= 15){
                        echo '<select name="banner_action" onchange="banner_change_action(this.value, \'banner_id[]\');">';
                        echo '<option value="0">'.TEXT_REVIEWS_SELECT_ACTION.'</option>';
                        echo '<option value="1">'.TEXT_REVIEWS_DELETE_ACTION.'</option>';
                        echo '</select>';
                    }
                 }else{
                        echo TEXT_DATA_EMPTY;
                 }
                ?>
              </td>
            </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $banners_split->display_count($banners_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_BANNERS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $banners_split->display_links($banners_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'bID'))); ?></div></td>
                  </tr>
                  <tr>
                    <td align="right" colspan="2"><div class="td_button">
                    <?php echo '<a href="javascript:void(0)" onclick="show_banner(this,-1,'.$_GET['page'].',-1);check_radio(0)">' . tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; ?>
                    </div></td>
                  </tr>
                </table>
			</td>
<?php
  $heading = array();
  $contents = array();
  switch (isset($_GET['action'])?$_GET['action']:null) {
/* -----------------------------------------------------
   case 'delete' 右侧删除页面 
   default 右侧默认页面     
------------------------------------------------------*/
    case 'delete':
      $heading[] = array('text' => $bInfo->banners_title);

      $contents = array('form' => tep_draw_form('banners', FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] . '&bID=' . $bInfo->banners_id . '&action=deleteconfirm' . (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br>' . $bInfo->banners_title);
      if ($bInfo->banners_image) $contents[] = array('text' => '<br>' . tep_draw_checkbox_field('delete_image', 'on', true) . ' ' . TEXT_INFO_DELETE_IMAGE);
      $contents[] = array('align' => 'center', 'text' => '<br><a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_DELETE, 'onclick="check_banner_form(1);"') .
          '</a>&nbsp;<a href="' . tep_href_link(FILENAME_BANNER_MANAGER, 'page=' . $_GET['page'] .  '&bID=' . $_GET['bID'] .  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
      break;
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
