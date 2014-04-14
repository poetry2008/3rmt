<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');

$is_u_disabled = false;
if ($ocertify->npermission != 31) {
  if (!empty($_SESSION['site_permission'])) {
    $tmp_u_array = explode(',', $_SESSION['site_permission']);
    if (!in_array('0', $tmp_u_array)) {
      $is_u_disabled = true;
    }
  } else {
    $is_u_disabled = true;
  }
}
if(!isset($_GET['sort']) || $_GET['sort'] == ''){
   $manufacturers_str = 'manufacturers_name';
}else if($_GET['sort'] == 'm_name'){
   if($_GET['type'] == 'desc'){
      $manufacturers_str = 'manufacturers_name desc';
      $manufacturers_type = 'asc';
   }else{
      $manufacturers_str = 'manufacturers_name asc';
      $manufacturers_type = 'desc';
   }
}else if($_GET['sort'] == 'last_modified'){
   if($_GET['type'] == 'desc'){
      $manufacturers_str = 'last_modified desc';
      $manufacturers_type = 'asc';
   }else{
      $manufacturers_str = 'last_modified asc';
      $manufacturers_type = 'desc';
   }
}

  if (isset($_GET['action'])) 
  switch ($_GET['action']) {
/*----------------------------------
 case 'insert'  添加制造商
 case 'save'    更新制造商
 case 'deleteconfirm' 删除制造商
 ---------------------------------*/
    case 'insert':
    case 'save':
      $manufacturers_id = tep_db_prepare_input($_GET['mID']);
      $manufacturers_name = tep_db_prepare_input($_POST['manufacturers_name']);

      $sql_data_array = array('manufacturers_name' => $manufacturers_name);


      $manufacturers_image = tep_get_uploaded_file('manufacturers_image');
      $image_directory = tep_get_local_path(tep_get_upload_dir().'manufacturers/');
      $manufacturers_image['size'] = $manufacturers_image['size'] / 1024 / 1024;
      $pic_type_array = array('image/jpeg', 'image/gif', 'image/png', 'image/jpg'); 
      $save_info = false;
      if($manufacturers_image['size'] >= ini_get('upload_max_filesize')
       ||($manufacturers_image['size']==0&&$manufacturers_image['name']!='')
       ||empty($_POST)){
        $_SESSION['error_image'] = TEXT_IMAGE_MAX;
        tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, tep_get_all_get_params(array('page', 'action'))));
        exit;
      }else if ($manufacturers_image['name'] != '') {
        if (!in_array($manufacturers_image['type'], $pic_type_array)) {
          $_SESSION['error_image'] = TEXT_IMAGE_TYPE_WRONG;
          tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, tep_get_all_get_params(array('page', 'action'))));
          exit;
	}else{
          $save_info = true;
	}
      } else {
        $save_info = true;
      }
      if($save_info){
      if ($_GET['action'] == 'insert') {
        $insert_sql_data = array('date_added' => 'now()','last_modified' => 'now()','user_added' => $_POST['user_added'],'user_update' => $_POST['user_update'],'manufacturers_alt' => $_POST['manufacturers_alt']);
        $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
        tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
        $manufacturers_id = tep_db_insert_id();
      } elseif ($_GET['action'] == 'save') {
        $update_sql_data = array('last_modified' => 'now()','user_update' => $_POST['user_update'],'manufacturers_alt' => $_POST['manufacturers_alt']);
        $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
        tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', "manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
      }
      if (is_uploaded_file($manufacturers_image['tmp_name'])) {
        if (!is_writeable($image_directory)) {
          if (is_dir($image_directory)) {
            $messageStack->add_session(sprintf(ERROR_DIRECTORY_NOT_WRITEABLE, $image_directory), 'error');
          } else {
            $messageStack->add_session(sprintf(ERROR_DIRECTORY_DOES_NOT_EXIST, $image_directory), 'error');
          }
        } else {
          tep_db_query("update " . TABLE_MANUFACTURERS . " set manufacturers_image = '" . $manufacturers_image['name'] . "' where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
          tep_copy_uploaded_file($manufacturers_image, $image_directory);
        }
      }

      $languages = tep_get_languages();
      for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $manufacturers_url_array = $_POST['manufacturers_url'];
        $language_id = $languages[$i]['id'];

        $sql_data_array = array('manufacturers_url' => tep_db_prepare_input($manufacturers_url_array[$language_id]));

        if ($_GET['action'] == 'insert') {
          $insert_sql_data = array('manufacturers_id' => $manufacturers_id,
                                   'languages_id' => $language_id);
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
        } elseif ($_GET['action'] == 'save') {
          tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', "manufacturers_id = '" . tep_db_input($manufacturers_id) . "' and languages_id = '" . $language_id . "'");
        }
      }

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('manufacturers');
      }
      tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $manufacturers_id));
      }

      break;
    case 'deleteconfirm':
        if(!empty($_POST['manufacturers_id'])){
           foreach($_POST['manufacturers_id'] as $ge_key => $ge_value){
            tep_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . $ge_value . "'");
            tep_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . $ge_value . "'");
           } 
        }
        $manufacturers_id = tep_db_prepare_input($_GET['mID']);
        $manufacturer_query = tep_db_query("select manufacturers_image from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
        $manufacturer = tep_db_fetch_array($manufacturer_query);
        $image_location = DIR_FS_DOCUMENT_ROOT . DIR_WS_CATALOG_IMAGES . $manufacturer['manufacturers_image'];
        if (file_exists($image_location)) @unlink($image_location);

      tep_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
      tep_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
      if ($_GET['delete_products'] == 'on') {
        $products_query = tep_db_query("select products_id from " . TABLE_PRODUCTS . " where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
        while ($products = tep_db_fetch_array($products_query)) {
          tep_remove_product($products['products_id']);
        }
      } else {
        tep_db_query("update " . TABLE_PRODUCTS . " set manufacturers_id = '' where manufacturers_id = '" . tep_db_input($manufacturers_id) . "'");
      }

      if (USE_CACHE == 'true') {
        tep_reset_cache_block('manufacturers');
      }

      tep_redirect(tep_href_link(FILENAME_MANUFACTURERS, 'page=' . $_GET['page']));
      break;
  }
if(isset($_SESSION['error_image'])&&$_SESSION['error_image']){
  $messageStack->add_session($_SESSION['error_image'], 'error');
  unset($_SESSION['error_image']);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script type="text/javascript">
	var js_del_manufacturers = '<?php echo TEXT_DEL_MANUFACTURERS;?>'; 
	var js_manufacturers_self = '<?php echo $_SERVER['PHP_SELF']?>';
	var js_onetime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>';
	var js_onetime_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
	var js_manufacturers_must_select = '<?php echo TEXT_MANUFACTURERS_MUST_SELECT;?>';
	var js_manufacturers_npermission = '<?php echo $ocertify->npermission;?>';
	var js_del_manufacturers = '<?php echo TEXT_DEL_MANUFACTURERS;?>';
	var js_href_manufacturers = '<?php echo tep_href_link(FILENAME_MANUFACTURERS);?>';

<?php //提交动作?>
function toggle_manufacturers_form(c_permission){
  var manufacturers_name = $("#manufacturers_name").val();
  var url_flag = true;
  <?php
    $languages_list = tep_get_languages();
    for ($i = 0, $n = sizeof($languages_list); $i < $n; $i++) {
      ?>
        if(url_flag){
        url_tmp = '';
        url_tmp = document.getElementById('manufac_url_<?php echo $languages_list[$i]['id'];?>').value;
        if(url_tmp!=''){
          url_flag = checkurl(url_tmp);
        }
        }
      <?php
    }
  ?>
  if(url_flag){
  if(manufacturers_name == ''){
     $("#manufacturers_name_error").html("<?php echo TEXT_PLEASE_MANUFACTURERS_NAME; ?>");   
  }else{
    if (c_permission == 31) {
    document.forms.manufacturers.submit(); 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.forms.manufacturers.submit(); 
        } else {
          $("#button_save").attr('id', 'tmp_button_save');
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.manufacturers.action),
              async: false,
              success: function(msg_info) {
                document.forms.manufacturers.submit(); 
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  }
  }
  }else{
    alert('<?php echo TEXT_URL_EXAMPLE;?>');
  }
}
</script>
<script language="javascript" src="includes/javascript/admin_manufacturers.js"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php 
if($error_image){
echo '<div style="background-color:#FF0000;font-size:12px;padding:2px;">'.$error_image.'</div>';
}
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<input id="show_info_id" type="hidden" name="show_info_id" value="show_manufacturers">
<div id="show_manufacturers" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
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
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
         <?php 
              $site_list_array = array(); 
              $show_site_list_array = array(); 
              $site_list_info_query = tep_db_query("select * from ".TABLE_SITES);    
               
              while ($site_list_info = tep_db_fetch_array($site_list_info_query)) {
                $site_list_array[$site_list_info['id']] = $site_list_info['romaji']; 
                $show_site_list_array[] = $site_list_info['id']; 
              }
              echo tep_show_site_filter(FILENAME_MANUFACTURERS, false, $show_site_list_array); 
         ?>
         <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_manufacturers_list">
          <tr>
           <td>
<?php 
           echo '<input type="hidden" id="sort" value="'.$_GET['sort'].'"><input type="hidden" id="type" value="'.$_GET['type'].'">';
           if($_GET['sort'] == 'm_name'){
               if($_GET['type'] == 'desc'){
                   $m_name = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
               }else{
                   $m_name = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
               }
           }
           if($_GET['sort'] == 'last_modified'){
               if($_GET['type'] == 'desc'){
                   $last_modified = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
               }else{
                   $last_modified = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
               }
           }
           $manufacturers_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
           $notice_box = new notice_box('','',$news_table_params);
           $manufacturers_table_row = array();
           $manufacturers_title_row = array();
           $manufacturers_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" '.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'').'onclick="all_select_manufacturers(\'manufacturers_id[]\');">');
           if(isset($_GET['sort']) && $_GET['sort'] == 'm_name'){
           $manufacturers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_MANUFACTURERS,'page='.$_GET['page'].'&sort=m_name&type='.$manufacturers_type).'">'.TABLE_HEADING_MANUFACTURERS.$m_name.'</a>');
           }else{
           $manufacturers_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link(FILENAME_MANUFACTURERS,'page='.$_GET['page'].'&sort=m_name&type=desc').'">'.TABLE_HEADING_MANUFACTURERS.$m_name.'</a>');
           }
           if(isset($_GET['sort']) && $_GET['sort'] == 'last_modified'){
           $manufacturers_title_row[] = array('params' =>
               'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_MANUFACTURERS,'page='.$_GET['page'].'&sort=last_modified&type='.$manufacturers_type).'">'.TABLE_HEADING_ACTION.$last_modified.'</a>');
           }else{
           $manufacturers_title_row[] = array('params' => 'class="dataTableHeadingContent_order" align="right"','text' => '<a href="'.tep_href_link(FILENAME_MANUFACTURERS,'page='.$_GET['page'].'&sort=last_modified&type=desc').'">'.TABLE_HEADING_ACTION.$last_modified.'</a>');
           }
           $manufacturers_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $manufacturers_title_row);
  $manufacturers_query_raw = "select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified,user_added,user_update from " .  TABLE_MANUFACTURERS . " order by ".$manufacturers_str;
  $manufacturers_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $manufacturers_query_raw, $manufacturers_query_numrows);
  $manufacturers_query = tep_db_query($manufacturers_query_raw);
  $manufacturers_numrows = tep_db_num_rows($manufacturers_query);
  while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
    if (((!isset($_GET['mID']) || !$_GET['mID']) || (@$_GET['mID'] == $manufacturers['manufacturers_id'])) && (!isset($mInfo) || !$mInfo) && (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new')) {
      $manufacturer_products_query = tep_db_query("select count(*) as products_count from " . TABLE_PRODUCTS . " where manufacturers_id = '" . $manufacturers['manufacturers_id'] . "'");
      $manufacturer_products = tep_db_fetch_array($manufacturer_products_query);

      $mInfo_array = tep_array_merge($manufacturers, $manufacturer_products);
      $mInfo = new objectInfo($mInfo_array);
    }

    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
    if ( isset($mInfo) and (is_object($mInfo)) && ($manufacturers['manufacturers_id'] == $mInfo->manufacturers_id) ) {
      $manufacturers_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'"';
    } else {
      $manufacturers_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
    }
    $manufacturers_info = array();
    $manufacturers_checkbox = '<input type="checkbox" name="manufacturers_id[]"'.(isset($is_u_disabled) && $is_u_disabled?'disabled="disabled"':'').' value="'.$manufacturers['manufacturers_id'].'">';
    $manufacturers_info[] = array(
        'params' => 'class="dataTableContent"',
        'text'   => $manufacturers_checkbox
        );
    $manufacturers_info[] = array(
        'params' => 'class="dataTableContent" onclick="document.location.href=\''.tep_href_link(FILENAME_MANUFACTURERS,'mID='.$manufacturers['manufacturers_id']).'\';"',
        'text'   => $manufacturers['manufacturers_name'] 
        );
    $manufacturers_info[] = array(
        'params' => 'class="dataTableContent" align="right"',
        'text'   => '<a href="javascript:void(0)" onclick="show_manufacturers(this,'.$manufacturers['manufacturers_id'].','.$_GET['page'].')">' .  tep_get_signal_pic_info(isset($manufacturers['last_modified']) && $manufacturers['last_modified'] != null?$manufacturers['last_modified']:$manufacturers['date_added']) . '</a>'
        );
    $manufacturers_table_row[] = array('params' => $manufacturers_params, 'text' => $manufacturers_info);
  }
     $manufacturers_form = tep_draw_form('del_manufacturers',FILENAME_MANUFACTURERS, 'page=' . $_GET['page'] . '&mID=' . $mInfo->manufacturers_id . '&action=deleteconfirm');
     $notice_box->get_form($manufacturers_form);
     $notice_box->get_contents($manufacturers_table_row);
     $notice_box->get_eof(tep_eof_hidden());
     echo $notice_box->show_notice();
?>
    </table>
        </td>
          </tr>
            </table>
             <table border="0" width="100%" cellspacing="0" cellpadding="0" class="table_list_box">
                  <tr>
                    <td>
                      <?php
                        if($manufacturers_numrows > 0){
                          if($ocertify->npermission >= 15){
                            echo '<select name="manufacturers_action" onchange="manufacturers_change_action(this.value, \'manufacturers_id[]\');">';
                            echo '<option value="0">'.TEXT_MANUFACTURERS_SELECT_ACTION.'</option>';
                            echo '<option value="1">'.TEXT_MANUFACTURERS_DELETE_ACTION.'</option>';
                            echo '</select>';
                           }
                         }else{
                            echo TEXT_DATA_EMPTY;
                         }
                      ?>
                    </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $manufacturers_split->display_count($manufacturers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_MANUFACTURERS); ?></td>
                    <td class="smallText" align="right">
					<div class="td_box">
					<?php echo $manufacturers_split->display_links($manufacturers_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'action'))); ?></div></td>
                  </tr>
				  <?php
  if (!isset($_GET['action']) || $_GET['action'] != 'new') {
?>
              <tr>
                <td align="right" colspan="2" class="smallText">
                  <div class="td_button">
                     <?php
                     if($is_u_disabled){
                     echo tep_html_element_button(IMAGE_NEW_PROJECT,'disabled="disabled"'); 
                     }else{ 
                     echo '<a href="javascript:void(0)" onclick="show_manufacturers(this,-1,'.$_GET['page'].')">' . tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; 
                     } 
                     ?>
                  </div>
                </td>
              </tr>
<?php
  }
?>
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
