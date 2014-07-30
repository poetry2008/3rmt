<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id_sql)){
    $site_arr = $userslist['site_permission']; 
  }
  $site_array = explode(',',$site_arr);
 
  if(isset($_GET['action']) && $_GET['action'] != ''){
    switch($_GET['action']){
    /* -----------------------------------------------------
       case 'new_group' 新建group 
       case 'update_group' 更新group 
       case 'setflag' 切换组的状态 
       case 'delete_group' 删除组及其子组 
       case 'delete_select_group' 删除选中的组及其子组 
    ------------------------------------------------------*/
      case 'new_group':
        if(trim($_POST['group_name']) != ''){
	  if($_GET['id'] == '-1' && is_numeric($_GET['parent_id'])){
	    if(!empty($_POST['users_list'])){
	      $all_users_id = implode('|||',$_POST['users_list'] );
	    }else{
	      $all_users_id = '';
	    }

	    if(!empty($_POST['managers_list'])){
	      $all_managers_id = implode('|||',$_POST['managers_list'] );
	    }else{
	      $all_managers_id = '';
	    }

            //终始日
            $end_date = $_POST['end_date'];
            $start_date = $_POST['start_date'];
            $date_array = array();
            foreach($end_date as $end_date_key=>$end_date_value){

              $date_array[] = $end_date_value.'-'.$start_date[$end_date_key];
            }
            $date_str = implode('|||',$date_array);
	    $group_sql_array = array('name' => $_POST['group_name'],
				     'parent_id' => $_GET['parent_id'],
				     'all_users_id' => $all_users_id,
				     'all_managers_id' => $all_managers_id,
                                     'create_time' => 'now()',
                                     'create_user' => $_SESSION['user_name'],
                                     'group_contents' => tep_db_prepare_input($_POST['group_contents']), 
                                     'currency_type' => tep_db_prepare_input($_POST['currency_type']), 
                                     'begin_end_date' => $date_str, 
                                     'cycle_flag' => tep_db_prepare_input($_POST['cycle_flag']), 
                                     'begin_end_hour' => tep_db_prepare_input($_POST['start_end_hour']), 
                                     'order_sort' => tep_db_prepare_input($_POST['order_sort']) 
			            );
            tep_db_perform(TABLE_GROUPS, $group_sql_array);	
            $insert_group_id = tep_db_insert_id();
            //计算工资的标题、公式
            $object_title = tep_db_prepare_input($_POST['object_title']);
            $object_contents = tep_db_prepare_input($_POST['object_contents']);

            foreach($object_title as $object_title_key=>$object_title_value){

             if(trim($object_title_value) != '' && trim($object_contents[$object_title_key]) != ''){ 
               $object_sql_array = array('group_id' => $insert_group_id,
				     'project_id' => 0,
				     'title' => $object_title_value,
				     'contents' => $object_contents[$object_title_key] 
			            );
               tep_db_perform(TABLE_WAGE_SETTLEMENT, $object_sql_array);
             }
            }

            $formula_title = tep_db_prepare_input($_POST['formula_title']);
            $formula_contents = tep_db_prepare_input($_POST['formula_contents']);
            $formula_value = tep_db_prepare_input($_POST['formula_value']);

            foreach($formula_title as $formula_title_key=>$formula_title_value){

             if(trim($formula_title_value) != '' && trim($formula_contents[$formula_title_key]) != ''){ 
               $formula_sql_array = array('group_id' => $insert_group_id,
				     'project_id' => 1,
				     'title' => $formula_title_value,
				     'contents' => $formula_contents[$formula_title_key], 
				     'project_value' => $formula_value[$formula_title_key] 
			            );
               tep_db_perform(TABLE_WAGE_SETTLEMENT, $formula_sql_array);
             }
            } 
	  }
        }
        tep_redirect(tep_href_link(FILENAME_GROUPS,'id='.$_GET['parent_id']));
        break;
      case 'update_group':
        if($_POST['group_name'] != ''){

          $group_id = $_GET['id'];
          if(!empty($_POST['users_list'])){
	    $all_users_id = implode('|||',$_POST['users_list'] );
	  }else{
	    $all_users_id = '';
	  }


	    if(!empty($_POST['managers_list'])){
	      $all_managers_id = implode('|||',$_POST['managers_list'] );
	    }else{
	      $all_managers_id = '';
	    }

          //终始日
          $end_date = $_POST['end_date'];
          $start_date = $_POST['start_date'];
          $date_array = array();
          foreach($end_date as $end_date_key=>$end_date_value){

            $date_array[] = $end_date_value.'-'.$start_date[$end_date_key];
          }
          $date_str = implode('|||',$date_array);
	  $group_sql_array = array('name' => $_POST['group_name'],
				   'all_users_id' => $all_users_id,
				   'all_managers_id' => $all_managers_id,
                                   'update_time' => 'now()',
                                   'update_user' => $_SESSION['user_name'],
                                   'group_contents' => tep_db_prepare_input($_POST['group_contents']), 
                                   'currency_type' => tep_db_prepare_input($_POST['currency_type']), 
                                   'begin_end_date' => $date_str, 
                                   'cycle_flag' => tep_db_prepare_input($_POST['cycle_flag']), 
                                   'begin_end_hour' => tep_db_prepare_input($_POST['start_end_hour']), 
                                   'order_sort' => tep_db_prepare_input($_POST['order_sort']) 
			           );
          tep_db_perform(TABLE_GROUPS, $group_sql_array, 'update', 'id='.$group_id);
          //计算工资的标题、公式
          tep_db_query("delete from ".TABLE_WAGE_SETTLEMENT." where group_id='".$group_id."'");
          $object_title = tep_db_prepare_input($_POST['object_title']);
          $object_contents = tep_db_prepare_input($_POST['object_contents']);

          foreach($object_title as $object_title_key=>$object_title_value){

           if(trim($object_title_value) != '' && trim($object_contents[$object_title_key]) != ''){ 
             $object_sql_array = array('group_id' => $group_id,
				     'project_id' => 0,
				     'title' => $object_title_value,
				     'contents' => $object_contents[$object_title_key] 
			            );
             tep_db_perform(TABLE_WAGE_SETTLEMENT, $object_sql_array);
           }
          }

          $formula_title = tep_db_prepare_input($_POST['formula_title']);
          $formula_contents = tep_db_prepare_input($_POST['formula_contents']);
          $formula_value = tep_db_prepare_input($_POST['formula_value']);

          foreach($formula_title as $formula_title_key=>$formula_title_value){

            if(trim($formula_title_value) != '' && trim($formula_contents[$formula_title_key]) != ''){
              $formula_sql_array = array('group_id' => $group_id,
				     'project_id' => 1,
				     'title' => $formula_title_value,
				     'contents' => $formula_contents[$formula_title_key], 
				     'project_value' => $formula_value[$formula_title_key] 
			            );
              tep_db_perform(TABLE_WAGE_SETTLEMENT, $formula_sql_array);
            }
          }
        }
        tep_redirect(tep_href_link(FILENAME_GROUPS,'id='.$_GET['parent_id']));
        break;
      case 'setflag':

        $flag = $_GET['flag'];
        $group_id = $_GET['group_id'];

        tep_db_query("update ".TABLE_GROUPS." set group_status='".$flag."' where id='".$group_id."'");
        //获取当前组包含的子组
        group_id_list($group_id,$group_status_id_list);
        $group_status_str = implode(',',$group_status_id_list);
        tep_db_query("update ".TABLE_GROUPS." set group_status='".$flag."' where id in (".$group_status_str.")");
        tep_redirect(tep_href_link(FILENAME_GROUPS,'id='.$_GET['parent_id']));
        break;
      case 'delete_group':
      
        $group_id = $_GET['group_id'];
        $parent_id = $_GET['parent_id'];

        $group_id_list[] = $group_id;
        
        group_id_list($group_id,$group_id_list);

        tep_db_query("delete from ".TABLE_GROUPS." where id in (".implode(',',$group_id_list).")");

        tep_redirect(tep_href_link(FILENAME_GROUPS,'id='.$_GET['parent_id']));
        break;
      case 'delete_select_group':

        $group_id_array = $_POST['group_id'];

        foreach($group_id_array as $group_value){
          $group_id_list = array($group_value);
          group_id_list($group_value,$group_id_list);
          tep_db_query("delete from ".TABLE_GROUPS." where id in (".implode(',',$group_id_list).")");
        }

        tep_redirect(tep_href_link(FILENAME_GROUPS,'id='.$_GET['parent_id']));
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo GROUP_SETUP; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript">
  var group_name_must = '<?php echo TEXT_GROUP_MUST_INPUT;?>';
  var delete_group_confirm = '<?php echo TEXT_GROUP_DELETE;?>';
  var user_permission = '<?php echo $ocertify->npermission;?>';
  var group_select_delete = '<?php echo TEXT_GROUP_EDIT_CONFIRM;?>';
  var must_select_group = '<?php echo TEXT_GROUP_EDIT_MUST_SELECT;?>';
  var ontime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>'; 
  var ontime_pwd_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
</script>
<script language="javascript" src="includes/javascript/admin_groups.js?v=<?php echo $back_rand_info?>"></script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=new_group/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != ''){
  preg_match_all('/id=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0];
  }else{

    $belong = $href_url.'?'.$belong_temp_array[0][0];
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<input type="hidden" name="show_info_id" value="show_latest_news" id="show_info_id">
<div style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;" id="show_latest_news"></div>
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
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
	        <td class="pageHeading">
			<?php echo GROUP_SETUP;?>
		</td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_text_list">
          <tr>
            <td valign="top">
<?php
	$form_str = tep_draw_form('edit_group', FILENAME_GROUPS,'action=delete_group&group_sort='.$_GET['group_sort'].'&group_sort_type='.$_GET['group_sort_type'].'&page='.$_GET['page'], 'post', 'enctype="multipart/form-data" onSubmit="return false;"');
	$group_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
	$notice_box = new notice_box('','',$group_table_params);
	$group_table_row = array();
	$group_title_row = array();
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_group(\'group_id[]\');">');
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.GROUP_NAME.'</a>');
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.GROUP_STAFF_NUM.'</a>');
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.GROUP_STATUS.'</a>');
	$group_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.GROUP_OPT.'</a>');
	$group_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $group_title_row);
	if($_GET['id'] == '' || !is_numeric($_GET['id'])){
		$group_id = 0;
	}else{
		$group_id = $_GET['id'];
	}
	$latest_group_query_raw = ' select *
                from '.TABLE_GROUPS.' where parent_id = "'.$group_id.'"';
	$latest_group_split = new splitPageResults($group_page, MAX_DISPLAY_SEARCH_RESULTS, $latest_group_query_raw, $latest_group_query_numrows);
	$latest_group_query = tep_db_query($latest_group_query_raw);
        $all_group_array = array();
        if(tep_db_num_rows($latest_group_query) == 0){
          $group_data_row[] = array('align' => 'left','params' => 'colspan="7" nowrap="nowrap"', 'text' => '<font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>');
                    
          $group_table_row[] = array('params' => '', 'text' => $group_data_row);  
        } 
	while ($latest_group = tep_db_fetch_array($latest_group_query)) {
		$even = 'dataTableSecondRow';
        	$odd  = 'dataTableRow';
        	if (isset($nowColor) && $nowColor == $odd) {
                	$nowColor = $even;
        	} else {
                	$nowColor = $odd;
        	}
		$group_params = 'id="'.$latest_group['id'].'" class="'.$nowColor.'" onclick="" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
		$group_info = array();
		$group_checkbox = '<input type="checkbox" name="group_id[]" value="'.$latest_group['id'].'">';
		$group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => $group_checkbox
        	);
		$group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => '<a  href="'.FILENAME_GROUPS.'?id='.$latest_group['id'].'"><img src="images/icons/folder.gif" border="0">&nbsp'.$latest_group['name'].'</a>'
                );
                //统计员工数量
                $all_users_array = array();
                if(trim($latest_group['all_users_id']) != ''){
                  $all_users_array = explode('|||',$latest_group['all_users_id']);
                }
                $group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => count($all_users_array)
                );
                //组状态切换
                if ($latest_group['group_status'] == '1') {
                  if(in_array(0,$site_array)){
                    $latest_group_status = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;<a href="javascript:void(0);" onclick="toggle_group_action(\'' . tep_href_link(FILENAME_GROUPS, 'page='.$_GET['page'].'&action=setflag&flag=0&parent_id='.$latest_group['parent_id'].'&group_id=' .  $latest_group['id'].(isset($_GET['group_sort'])?'&group_sort='.$_GET['group_sort']:'').(isset($_GET['group_sort_type'])?'&group_sort_type='.$_GET['group_sort_type']:'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT) . '</a>';
                  } else {
                    $latest_group_status = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT);
                  }
                } else {
                  if(in_array(0,$site_array)){
                    $latest_group_status = '<a href="javascript:void(0);" onclick="toggle_group_action(\'' . tep_href_link(FILENAME_GROUPS, 'page='.$_GET['page'].'&action=setflag&flag=1&parent_id='.$latest_group['parent_id'].'&group_id=' .  $latest_group['id'].(isset($_GET['group_sort'])?'&group_sort='.$_GET['group_sort']:'').(isset($_GET['group_sort_type'])?'&group_sort_type='.$_GET['group_sort_type']:'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
                  } else {
                    $latest_group_status = tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
                  }
                }  
		$group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => $latest_group_status
        	);
		$group_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => '<a href="javascript:void(0)" onclick="group_ajax(this,\''.$latest_group['id'].'\',\''.$group_id.'\',\''.$latest_group['name'].'\')">'.tep_get_signal_pic_info(date('Y-m-d H:i:s',strtotime(($latest_group['update_time'] != '' && $latest_group['update_time'] != '0000-00-00 00:00:00' ? $latest_group['update_time'] : $latest_group['create_time'])))).'</a>'
        	);
		$group_table_row[] = array('params' => $group_params, 'text' => $group_info);
		$all_group_array[] = $latest_group;
	}
	$notice_box->get_form($form_str);
	$notice_box->get_contents($group_table_row);
	$notice_box->get_eof(tep_eof_hidden());
	echo $notice_box->show_notice();
?>	
	    </td>
            </tr>
            </table>
<br>
		    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:-10px;">
                    <tr>                 
                    <td valign="top" class="smallText">
                    <?php 
                    echo '<select name="group_action" onchange="group_change_action(this.value, \'group_id[]\','.$ocertify->npermission.',\''.$_GET['id'].'\');">';
                    echo '<option value="0">'.TEXT_CONTENTS_SELECT_ACTION.'</option>';   
                    echo '<option value="1">'.TEXT_CONTENTS_DELETE_ACTION.'</option>';
                    echo '</select>';
                    ?> 
                    </td>
                    <td align="right" class="smallText">
                    </td>
                    </tr>

                  <tr>
                    <td class="smallText" valign="top"><?php echo $latest_group_split->display_count($latest_group_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $group_page, TEXT_DISPLAY_NUMBER_OF_LATEST_NEWS); ?></td>
                    <td class="smallText" align="right"><div class="td_box"><?php echo $latest_group_split->display_links($latest_group_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $group_page, tep_get_all_get_params(array('page', 'info', 'x', 'y', 'id'))); ?></div></td>
                  </tr>
                     <tr><td></td><td align="right">
                     <div class="td_button"><?php
                     if(isset($_GET['id']) && $_GET['id'] != 0){
                       //获取父组的ID
                       $parent_query = tep_db_query("select parent_id from ".TABLE_GROUPS." where id='".$_GET['id']."'");
                       $parent_array = tep_db_fetch_array($parent_query);
                       tep_db_free_result($parent_query);
                       echo '&nbsp;<a href="javascript:void(0)" onclick="javascript:location.href=\''.FILENAME_GROUPS.'?id='.$parent_array['parent_id'].'\';">'.tep_html_element_button(IMAGE_BACK).'</a>';
                     }
                      //通过site_id判断是否允许新建
                     if (in_array(0,$site_array)) {
                       echo '&nbsp;<a href="javascript:void(0)" onclick="group_ajax(this,\'-1\',\''.$group_id.'\',\'\')">' .tep_html_element_button(TEXT_GROUP_CREATE) . '</a>';
                     }else{
                       echo '&nbsp;' .tep_html_element_button(TEXT_GROUP_CREATE,'disabled="disabled"');
                     } 
                      ?>
                    </div>
                     </td></tr>
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
