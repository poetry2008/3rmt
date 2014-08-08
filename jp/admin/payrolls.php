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
  $site_id_array = explode(',',$site_arr);
 
  if(isset($_GET['action']) && $_GET['action'] != ''){
    switch($_GET['action']){
    /* -----------------------------------------------------
      case 'edit_users_wage' 编辑员工工资  
      case 'update_show_user'保存用户记录
      case 'save_user_wage'保存用户工资
    ------------------------------------------------------*/
      case 'edit_users_wage':

        $users_wage = tep_db_prepare_input($_POST['users_wage']); 
        $save_date = tep_db_prepare_input($_POST['save_date']);

        foreach($users_wage as $users_wage_key=>$users_wage_value){

          foreach($users_wage_value as $key=>$value){
            $exist_query = tep_db_query("select id from ".TABLE_USER_WAGE." where wage_id='".$users_wage_key."' and user_id='".$key."' and save_date='".$save_date."'");
            if(tep_db_num_rows($exist_query) > 0){
              tep_db_query("update ".TABLE_USER_WAGE." set wage_value='".$value."',update_date=now() where wage_id='".$users_wage_key."' and user_id='".$key."' and save_date='".$save_date."'");
            }else{
              tep_db_query("insert into ".TABLE_USER_WAGE."(id,wage_id,user_id,wage_value,save_date,update_date) value(NULL,".$users_wage_key.",'".$key."','".$value."','".date('Y-m-d',time())."',now())");
            }
          }
        }
        tep_redirect(tep_href_link(FILENAME_PAYROLLS,''));
        break; 
      case 'update_show_user':
        //获取选中的组
        $show_group = tep_db_prepare_input($_GET['show_group']);
        //获取选中的员工
        $show_group_user_list = tep_db_prepare_input($_GET['show_group_user_list']);
        $show_user_array = array();
        if(USER_WAGE_SETTING != ''){

          $show_user_array = unserialize(USER_WAGE_SETTING); 
          $show_user_array[$ocertify->auth_user] = array('group'=>$show_group,'user'=>implode(',',$show_group_user_list));
        }else{

          $show_user_array[$ocertify->auth_user] = array('group'=>$show_group,'user'=>implode(',',$show_group_user_list));   
        }
        $show_user_str = serialize($show_user_array);
        tep_db_query("update ".TABLE_CONFIGURATION." set configuration_value='".$show_user_str."' where configuration_key='USER_WAGE_SETTING'");
        break;
      case 'save_user_wage':
        $user_wage_list = tep_db_prepare_input($_POST['user_wage_list']);
        $user_id = tep_db_prepare_input($_POST['user_id']);
        $save_date = tep_db_prepare_input($_POST['save_date']);
        $user_wage = tep_db_prepare_input($_POST['user_wage']);
        $user_wage_start_date = tep_db_prepare_input($_POST['user_wage_start_date']);
        $user_wage_end_date = tep_db_prepare_input($_POST['user_wage_end_date']);
        $wage_contents = tep_db_prepare_input($_POST['wage_contents']);

        if($user_wage_list != ''){
          $user_wage_list_array = array();
          $user_wage_query = tep_db_query("select id,wage_id from ".TABLE_USER_WAGE_INFO." where id in (".$user_wage_list.")"); 
          while($user_wage_array = tep_db_fetch_array($user_wage_query)){

            $user_wage_list_array[$user_wage_array['wage_id']] = $user_wage_array['id'];
          }
          tep_db_free_result($user_wage_query);

          foreach($user_wage as $user_wage_key=>$user_wage_value){

            tep_db_query("update ".TABLE_USER_WAGE_INFO." set wage_value='".$user_wage_value."',start_date='".$user_wage_start_date[$user_wage_key]."',end_date='".$user_wage_end_date[$user_wage_key]."',contents='".$wage_contents."',update_date=now() where id='".$user_wage_list_array[$user_wage_key]."'");
          }
        }else{
         
          foreach($user_wage as $user_wage_key=>$user_wage_value){

            tep_db_query("insert into ".TABLE_USER_WAGE_INFO."(id,wage_id,user_id,wage_value,start_date,end_date,contents,update_date) values(NULL,".$user_wage_key.",'".$user_id."','".$user_wage_value."','".$user_wage_start_date[$user_wage_key]."','".$user_wage_end_date[$user_wage_key]."','".$wage_contents."',now())");
          } 
        }
        tep_redirect(tep_href_link(FILENAME_PAYROLLS,''));
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_HEAD_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js?v=<?php echo $back_rand_info?>"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript">
  var js_cale_date = '<?php echo date('Y-m-d', time())?>'; 
  var user_permission = '<?php echo $ocertify->npermission;?>';
  var user_select_send_mail = '<?php echo TEXT_USER_SEND_MAIL_CONFIRM;?>';
  var must_select_user = '<?php echo TEXT_USER_EDIT_MUST_SELECT;?>';
  var ontime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>'; 
  var ontime_pwd_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
</script>
<script language="javascript" src="includes/javascript/admin_payrolls.js?v=<?php echo $back_rand_info?>"></script>
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
<style type="text/css">
a.dpicker {
width: 16px;
height: 18px;
border: none;
color: #fff;
padding: 0;
margin:0;
overflow: hidden;
display:block;
cursor: pointer;
background: url(./includes/calendar.png) no-repeat;
}
#new_yui3 {
  margin-left:-168px;
  *margin-left:-28px;
  margin-left:-170px\9;
position: absolute;
          z-index:200px;
          margin-top:15px;
}
@media screen and (-webkit-min-device-pixel-ratio:0) {
#new_yui3{
position: absolute;
          z-index:200px;
          margin-top:17px;
}
}
.yui3-skin-sam img,.yui3-skin-sam input,.date_box{ float:left;}
.yui3-skin-sam .redtext {
color:#0066CC;
}
</style>
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
<input type="hidden" name="show_info_id" value="show_user_wage" id="show_info_id">
<div style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;" id="show_user_wage"></div>
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
			<?php echo TEXT_HEAD_TITLE;?>
		</td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr> 
      <tr><td>
        <form action="<?php echo tep_href_link(FILENAME_PAYROLLS);?>" method="get">
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
        <td class="smallText" width="100" height="25"><?php echo TEXT_SHOW_CONTENTS_TITLE;?></td>
        <td>
          <input type="hidden" name="action" value="update_show_user">
          <input type="checkbox" name="currency_type[]" id="currency_type_jpy" value="0"<?php echo in_array(0,$_GET['currency_type']) ? ' checked' : '';?> style="padding-left:0;margin-left:0;"><label for="currency_type_jpy"><?php echo TEXT_PAYROLLS_CURRENCY_TYPE_JPY;?></label>
          <input type="checkbox" name="currency_type[]" id="currency_type_rmb" value="1"<?php echo in_array(1,$_GET['currency_type']) ? ' checked' : '';?>><label for="currency_type_rmb"><?php echo TEXT_PAYROLLS_CURRENCY_TYPE_RMB;?></label>
          <input type="checkbox" name="currency_type[]" id="currency_type_usd" value="2"<?php echo in_array(2,$_GET['currency_type']) ? ' checked' : '';?>><label for="currency_type_usd"><?php echo TEXT_PAYROLLS_CURRENCY_TYPE_USD;?></label>
          <input type="checkbox" name="currency_type[]" id="currency_type_vnd" value="3"<?php echo in_array(3,$_GET['currency_type']) ? ' checked' : '';?>><label for="currency_type_vnd"><?php echo TEXT_PAYROLLS_CURRENCY_TYPE_VND;?></label>
        </td>
        </tr> 
        <tr>
        <td class="smallText" width="100" height="25">&nbsp;</td>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr><td width="90">
          <input type="radio" name="select_mode" id="select_mode_date" value="date"<?php echo $_GET['select_mode'] != 'contents' ? ' checked': '';?> style="padding-left:0;margin-left:0;"><label for="select_mode_date"><?php echo TEXT_PAYROLLS_DATE_SELECT;?></label>
          </td><td width="150">
          <div class="yui3-skin-sam yui3-g">
                <?php echo  tep_draw_input_field('wage_date',($_GET['select_mode'] == 'date' ? $_GET['select_date'] : date('Y-m-d',time())),'class="readonly" disabled');?>
<input id="date_orders" type="hidden" name='select_date' size='15' value='<?php echo $_GET['select_mode'] == 'date' ? $_GET['select_date'] : date('Y-m-d',time());?>'>
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
                <div class="yui3-u" id="new_yui3">
                <div id="mycalendar"></div> 
                </div>
          </div>
          </td><td>
          <input type="radio" name="select_mode" id="select_mode_contents" value="contents"<?php echo $_GET['select_mode'] == 'contents' ? ' checked': '';?>><label for="select_mode_contents"><?php echo TEXT_PAYROLLS_CONTENTS_SELECT;?></label> 
<?php
//获取已存在数据的日期列表
$user_wage_query = tep_db_query("select * from ".TABLE_USER_WAGE." group by save_date order by save_date");
?>
          <select name="old_wage_date">
<?php
if(tep_db_num_rows($user_wage_query) > 0){
  while($user_wage_array = tep_db_fetch_array($user_wage_query)){
?>
  <option value="<?php echo date('Y-m-d',strtotime($user_wage_array['save_date']));?>"<?php echo ($_GET['select_mode'] == 'contents' && $_GET['old_wage_date'] == date('Y-m-d',strtotime($user_wage_array['save_date'])) ? ' selected' : '');?>><?php echo date('Y-m-d',strtotime($user_wage_array['save_date']));?></option>
<?php
  }
  tep_db_free_result($user_wage_query);
}else{
  echo '<option value="'.date('Y-m-d',time()).'">'.date('Y-m-d',time()).'</option>';
}
?>
          </select>
          </td>
          </tr></table>
        </td>
        </tr>
        <tr>
        <td class="smallText" width="100" height="25">&nbsp;</td>
        <td>
        <?php
        //通过组选择员工
        $group_list = tep_get_group_tree();
        //要显示的员工
        $show_group_user = array();
        //选中的员工
        $show_select_group_user = array();
        //获取记录
        if(USER_WAGE_SETTING != ''){

          $user_wage_array = unserialize(USER_WAGE_SETTING);
          $show_group_id = $user_wage_array[$ocertify->auth_user]['group'];
          $show_select_group_user = explode(',',$user_wage_array[$ocertify->auth_user]['user']);
        }else{

          $show_group_id = 0;
        }
        //默认选中的组
        $show_group_id = isset($_GET['show_group']) && $_GET['show_group'] != '' ? $_GET['show_group'] : $show_group_id;
        //默认选中的员工
        $show_select_group_user = isset($_GET['show_group_user_list']) && $_GET['show_group_user_list'] != '' ? $_GET['show_group_user_list'] : $show_select_group_user;


        if($show_group_id==0){
          $user_sql = "select * from ".TABLE_USERS." where status='1'";
          $user_query = tep_db_query($user_sql);
          while($user_row = tep_db_fetch_array($user_query)){
            $show_group_user[] = $user_row['userid'];
            if(USER_WAGE_SETTING == ''){
              $show_select_group_user[] = $user_row['userid'];
            }
          }
        } else {
          $user_sql = "select * from ".TABLE_GROUPS." where id='".$show_group_id."'";
          $user_query = tep_db_query($user_sql);
          if($user_row = tep_db_fetch_array($user_query)){
            $show_group_user = explode('|||',$user_row['all_users_id']);
          }
        }
   
        $group_str = '';
        $group_str .= '<table border="0" cellspacing="0" cellpadding="0" width="100%">';
        $group_str .= '<tr >';
        $group_str .= '<td width="15%" align="left">';
        $group_str .= TEXT_GROUP_SELECT;
        $group_str .= '</td>';
        $group_str .= '<td colspan="2" align="left">';
        $group_str .= '<select name="show_group" onchange="change_user_list(this)">';
        $group_str .= '<option value="0" ';
        if($show_group_id==0){
          $group_str .= ' selected ';
        }
        $group_str .= ' >'.TEXT_ALL_GROUP.'</option>';
        foreach($group_list as $group){
          $group_str .= '<option value="'.$group['id'].'"';
          if($show_group_id == $group['id']){
            $group_str .= ' selected ';
          }
          $group_str .= '>'.$group['text'].'</oprion>';
        }
        $group_str .= '</select>';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        $group_str .= '<tr>';
        $group_str .= '<td valign="top">';
        $group_str .= TEXT_GROUP_USER_LIST;
        $group_str .= '</td>';
        $group_str .= '<td align="left">';
        $group_str .= '<div id="show_user_list">';
        foreach($show_group_user as $show_list_uid){
          if($show_list_uid!=''){
			$tep_array= tep_get_user_info($show_list_uid);
			$uname_arr[] = $tep_array['name'];

          }
        }
	$group_user_list = array_combine($show_group_user,$uname_arr);
	asort($group_user_list);

	foreach($group_user_list as $key=>$val) {
          $group_str .= '<input type="checkbox" name="show_group_user_list[]" id="'.$key.'"';
          if(in_array($key,$show_select_group_user)){
            $group_str .= ' checked="checked" ';
          }
          $group_str .= ' value="'.$key.'" >';
          $group_str .=  '<label for="'.$key.'">'.$val.'</label>';
          $group_str .= '&nbsp;&nbsp;&nbsp;';
	}

        $group_str .= '</div>';
        $group_str .= '</td>';
        $group_str .= '<td align="right">';
        $group_str .= '<input type="submit" value="'.TEXT_UPDATE.'">';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        $group_str .= '</table>';
        echo $group_str;
        ?>
        </td>
        </tr>
        </table> 
        </form>
      </td></tr>
      <tr>
        <td>
        <table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_text_list">
          <tr>
            <td valign="top">
<?php
        $site_query = tep_db_query("select id from ".TABLE_SITES);
        $site_list_array = array();
        while($site_array = tep_db_fetch_array($site_query)){

          $site_list_array[] = $site_array['id'];
        }
        tep_db_free_result($site_query);
        echo tep_show_site_filter(FILENAME_PAYROLLS,false,$site_list_array);
        //默认保存日期
        $default_date = date('Y-m-d');
        $default_date = $_GET['select_mode'] == 'date' ? $_GET['select_date'] : ($_GET['select_mode'] == 'contents' ? $_GET['old_wage_date'] : $default_date);
	$form_str = tep_draw_form('edit_users_wage', FILENAME_PAYROLLS,'action=edit_users_wage&page='.$_GET['page'], 'post', 'onSubmit="return false;"');
	$wage_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
	$notice_box = new notice_box('','',$wage_table_params);
	$wage_table_row = array();
	$wage_title_row = array();
	$wage_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox" name="all_check" onclick="all_select_user(\'user_id[]\');"><input type="hidden" name="save_date" value="'.$default_date.'">');
        $wage_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.TEXT_PAYROLLS_NAME.'</a>');
        //获取组对应的工资项目
        $groups_id = isset($_GET['show_group']) && $_GET['show_group'] != '' ? $_GET['show_group'] : $show_group_id;
        $groups_users_id = array();
        if($groups_id != 0){
          $groups_wage_query = tep_db_query("select * from ".TABLE_WAGE_SETTLEMENT." where group_id='".$groups_id."' order by id");
          while($groups_wage_array = tep_db_fetch_array($groups_wage_query)){
            $wage_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.$groups_wage_array['title'].'</a>');
            $groups_users_id[] = array('id'=>$groups_wage_array['id'],'value'=>($groups_wage_array['project_id'] == 0 ? $groups_wage_array['contents'] : $groups_wage_array['project_value']),'project_id'=>$groups_wage_array['project_id']);
          }
          tep_db_free_result($groups_wage_query);
          $group_id = $groups_id;
        }else{
          $groups_wage_query = tep_db_query("select * from ".TABLE_WAGE_SETTLEMENT." group by title order by id");
          while($groups_wage_array = tep_db_fetch_array($groups_wage_query)){
            $wage_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.$groups_wage_array['title'].'</a>');
            $groups_users_id[] = array('id'=>$groups_wage_array['id'],'value'=>($groups_wage_array['project_id'] == 0 ? $groups_wage_array['contents'] : $groups_wage_array['project_value']),'project_id'=>$groups_wage_array['project_id']);
            $group_id = $groups_wage_array['group_id'];
          }
          tep_db_free_result($groups_wage_query);
        }
	$wage_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="javascript:void(0)">'.TEXT_PAYROLLS_OPTION.'</a>');
	$wage_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $wage_title_row);
	if($_GET['id'] == '' || !is_numeric($_GET['id'])){
		$wage_id = 0;
	}else{
		$wage_id = $_GET['id'];
	}
	
        $show_group_user_list = isset($_GET['show_group_user_list']) && $_GET['show_group_user_list'] != '' ? $_GET['show_group_user_list'] : $show_select_group_user;
        $show_group_user_list = array_filter($show_group_user_list);
        if(empty($show_group_user_list)){
          $group_data_row[] = array('align' => 'left','params' => 'colspan="7" nowrap="nowrap"', 'text' => '<font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>');
                    
          $wage_table_row[] = array('params' => '', 'text' => $group_data_row);  
        } 
        //获取所有员工的相关信息
        $all_users_info = array();
        $all_users_query = tep_db_query("select userid,name from ".TABLE_USERS);
        while($all_users_array = tep_db_fetch_array($all_users_query)){

          $all_users_info[$all_users_array['userid']] = $all_users_array['name'];
        }
        tep_db_free_result($all_users_query); 
	foreach($show_group_user_list as $users_value) {
		$even = 'dataTableSecondRow';
        	$odd  = 'dataTableRow';
        	if (isset($nowColor) && $nowColor == $odd) {
                	$nowColor = $even;
        	} else {
                	$nowColor = $odd;
        	}
		$user_params = 'id="wage_'.$users_wage['id'].'" class="'.$nowColor.'" onclick="" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
		$user_info = array();
		$group_checkbox = '<input type="checkbox" name="user_id[]" value="'.$users_value.'">';
		$user_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => $group_checkbox
        	);
		$user_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => $all_users_info[$users_value] 
                ); 
                //用户项目的ID
                $user_project_id_array = array();
                $update_time = '';
                foreach($groups_users_id as $wage_id){
                  //根据工资ID与用户ID，来获取对应的值
                  $user_wage_value_query = tep_db_query("select id,wage_value,start_date,end_date,update_date from ".TABLE_USER_WAGE_INFO." where wage_id='".$wage_id['id']."' and user_id='".$users_value."'");
                  if(tep_db_num_rows($user_wage_value_query) > 0){
                    $user_wage_value_array = tep_db_fetch_array($user_wage_value_query);
                    tep_db_free_result($user_wage_value_query);
                    //判断工资的有效期
                    $now_date = date('Y-m-d',strtotime($default_date));
                    if($user_wage_value_array['start_date'] != '' && $user_wage_value_array['end_date'] != ''){
                      if($now_date >= $user_wage_value_array['start_date'] && $now_date <= $user_wage_value_array['end_date']){
                        $user_wage_val = $user_wage_value_array['wage_value'];
                      }else{
                       
                        $user_wage_val = 0;
                      }
                    }else if($user_wage_value_array['start_date'] != ''){

                      if($now_date >= $user_wage_value_array['start_date']){

                        $user_wage_val = $user_wage_value_array['wage_value'];
                      }else{
                        $user_wage_val = 0; 
                      }
                    }else if($user_wage_value_array['end_date'] != ''){

                      if($now_date <= $user_wage_value_array['end_date']){

                        $user_wage_val = $user_wage_value_array['wage_value'];
                      }else{
                        $user_wage_val = 0; 
                      } 
                    }else{
                        $user_wage_val = $user_wage_value_array['wage_value']; 
                    }
                    if($update_time == ''){

                      $update_time = $user_wage_value_array['update_date'];
                    }
                    if($wage_id['project_id'] == 0){
                      $user_project_id_array[] = $user_wage_value_array['id'];
                    }
                  }

                  //根据工资ID与用户ID及日期，来获取对应的值
                  $user_wage_value_query = tep_db_query("select id,wage_value,update_date from ".TABLE_USER_WAGE." where wage_id='".$wage_id['id']."' and user_id='".$users_value."' and save_date='".$default_date."'");
                  $user_wage_val = '';
                  if(tep_db_num_rows($user_wage_value_query) > 0){
                    $user_wage_value_array = tep_db_fetch_array($user_wage_value_query);
                    tep_db_free_result($user_wage_value_query);
                    $user_wage_val = $user_wage_value_array['wage_value'];
                    if($update_time == ''){

                      $update_time = $user_wage_value_array['update_date'];
                    }
                    
                  }

                  $user_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => '<input type="text" name="users_wage['.$wage_id['id'].']['.$users_value.']" value="'.($user_wage_val != '' ? $user_wage_val :tep_user_wage($wage_id['value'],$users_value,$default_date,$group_id)).'">' 
                  );  
                }
                $user_project_id_array = array_filter($user_project_id_array);
		$user_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => '<a href="javascript:void(0)" onclick="show_user_wage(this,\''.$users_value.'\',\''.$all_users_info[$users_value].'\',\''.$groups_id.'\',\''.implode(',',$user_project_id_array).'\',\''.$default_date.'\')">'.tep_get_signal_pic_info(date('Y-m-d H:i:s',strtotime(($update_time != '' && $update_time != '0000-00-00 00:00:00' ? $update_time : $update_time)))).'</a>'
        	);
		$wage_table_row[] = array('params' => $user_params, 'text' => $user_info);
        }
        if(!empty($_GET['currency_type'])){

          $currency_type_array = array(TEXT_PAYROLLS_CURRENCY_TYPE_JPY,TEXT_PAYROLLS_CURRENCY_TYPE_RMB,TEXT_PAYROLLS_CURRENCY_TYPE_USD,TEXT_PAYROLLS_CURRENCY_TYPE_VND); 
          $i = 0;
          foreach($_GET['currency_type'] as $currency_type_value){
            $user_info = array();
            $user_info[] = array(
                	         'params' => '',
                	         'text'   => $i == 0 ? TEXT_PAYROLLS_TOTAL : ''  
                                );
            $user_info[] = array(
                	        'params' => '',
                	        'text'   => $currency_type_array[$currency_type_value] 
                                );
            foreach($groups_users_id as $wage_id){
              $user_info[] = array(
                	           'params' => '',
                	           'text'   => '<input type="text" name="users_wage_total['.$wage_id['id'].']">' 
                                  ); 
            } 
            $user_info[] = array(
                	         'params' => '',
                	         'text'   => ''  
                                );
            $wage_table_row[] = array('params' => '', 'text' => $user_info);  
            $i++;
          }
        }
	$notice_box->get_form($form_str);
	$notice_box->get_contents($wage_table_row);
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
                    echo '<select name="user_action" onchange="user_change_action(this.value, \'user_id[]\','.$ocertify->npermission.');">';
                    echo '<option value="0">'.TEXT_CONTENTS_SELECT_ACTION.'</option>';   
                    echo '<option value="1">'.TEXT_PAYROLLS_SEND_MAIL.'</option>';
                    echo '</select>';
                    ?> 
                    </td>
                    <td align="right" class="smallText">
                    </td>
                    </tr> 
                    <tr><td></td><td align="right">
                     <div class="td_button"><?php 
                     //通过site_id判断是否允许新建
                     if (in_array(0,$site_id_array)) {
                       echo '<a href="javascript:void(0)" onclick="">' .tep_html_element_button(TEXT_PAYROLLS_EXPORT) . '</a>';
                       echo '&nbsp;<a href="javascript:void(0)" onclick="">' .tep_html_element_button(TEXT_PAYROLLS_PRINT) . '</a>';
                       echo '&nbsp;<a href="javascript:void(0)" onclick="">' .tep_html_element_button(TEXT_PAYROLLS_RESET) . '</a>';
                       echo '&nbsp;<a href="javascript:void(0)" onclick="">' .tep_html_element_button(IMAGE_SAVE,'onclick="save_user_wage();"') . '</a>';
                     }else{
                       echo tep_html_element_button(TEXT_PAYROLLS_EXPORT,'disabled="disabled"').'&nbsp;' ;
                       echo tep_html_element_button(TEXT_PAYROLLS_PRINT,'disabled="disabled"').'&nbsp;' ;
                       echo tep_html_element_button(TEXT_PAYROLLS_RESET,'disabled="disabled"').'&nbsp;' ;
                       echo tep_html_element_button(IMAGE_SAVE,'disabled="disabled"');
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
