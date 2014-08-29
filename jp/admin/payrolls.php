<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  //管理员可管理的组
  $admin_group_list_array = array();
  $admin_user_list_array = array();
  $admin_group_query = tep_db_query("select id,all_users_id,payrolls_admin from ".TABLE_GROUPS);
  while($admin_group_array = tep_db_fetch_array($admin_group_query)){

   if(trim($admin_group_array['payrolls_admin']) != ''){

     $payrolls_admin_array = explode('|||',$admin_group_array['payrolls_admin']);

        if(in_array($ocertify->auth_user,$payrolls_admin_array)){

            $admin_group_list_array[] = $admin_group_array['id']; 
            if(trim($admin_group_array['all_users_id']) != ''){
              $admin_user_list_temp = explode('|||',$admin_group_array['all_users_id']);
              foreach($admin_user_list_temp as $admin_user_list_value){
                $admin_user_list_array[] = $admin_user_list_value; 
              }
            }
        }
    }
  }
  tep_db_free_result($admin_group_query);
  $admin_user_list_array = array_unique($admin_user_list_array);

  if(empty($admin_group_list_array) && $ocertify->npermission != 31){

    one_time_pwd_forward401($page_name, (!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:tep_href_link(FILENAME_DEFAULT)), $one_time_arr);
  }

  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  $sites_id_sql = tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id_sql)){
    $site_arr = $userslist['site_permission']; 
  }
  $site_id_array = explode(',',$site_arr);
 
  if(isset($_GET['action']) && $_GET['action'] != ''){
    switch($_GET['action']){
    /* -----------------------------------------------------
      case 'edit_users_payroll' 编辑员工工资  
      case 'update_show_user'保存用户记录
      case 'save_user_payroll' 编辑员工工资的设置
      case 'reset_user_payroll' 重置员工工资
      case 'again_computing' 重新计算工资 
      case 'send_mail' 给员工发信 
    ------------------------------------------------------*/
      case 'edit_users_payroll':

        $users_payroll = tep_db_prepare_input($_POST['users_payroll']); 
        $save_date = tep_db_prepare_input($_POST['save_date']);
        $group_id = tep_db_prepare_input($_POST['group_id']);

        $user_payroll_date = tep_start_end_date($group_id,$save_date);

        $save_date = $user_payroll_date['end_date'];

        foreach($users_payroll as $users_payroll_key=>$users_payroll_value){

          foreach($users_payroll_value as $key=>$value){
            $exist_query = tep_db_query("select id from ".TABLE_USER_PAYROLL." where payroll_id='".$users_payroll_key."' and user_id='".$key."' and save_date='".$save_date."'");
            if(tep_db_num_rows($exist_query) > 0){
              tep_db_query("update ".TABLE_USER_PAYROLL." set payroll_value='".$value."',update_date=now() where payroll_id='".$users_payroll_key."' and user_id='".$key."' and save_date='".$save_date."'");
            }else{
              tep_db_query("insert into ".TABLE_USER_PAYROLL."(id,payroll_id,user_id,payroll_value,save_date,update_date) value(NULL,".$users_payroll_key.",'".$key."','".$value."','".$save_date."',now())");
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
        //选择的日期


        $selected_date = tep_db_prepare_input($_GET['select_date']);
        
        $show_user_array = array();
        if(USER_PAYROLL_SETTING != ''){

          $show_user_array = unserialize(USER_PAYROLL_SETTING);  
          $show_user_array[$ocertify->auth_user]['group'] = $show_group;
          $show_user_array[$ocertify->auth_user]['user'] = implode(',',$show_group_user_list);
          $show_user_array[$ocertify->auth_user]['date'] = $selected_date;
        }else{

        
          $show_user_array[$ocertify->auth_user] = array('group'=>$show_group,
                                                       'user'=>implode(',',$show_group_user_list),
                                                       'date'=>$selected_date
                                                     );
        }
        $show_user_array[$ocertify->auth_user]['select_user'][$show_group] = implode(',',$show_group_user_list);

        $show_user_str = serialize($show_user_array);
        tep_db_query("update ".TABLE_CONFIGURATION." set configuration_value='".$show_user_str."' where configuration_key='USER_PAYROLL_SETTING'");
        break;
      case 'save_user_payroll':
        $user_payroll_list = tep_db_prepare_input($_POST['user_payroll_list']);
        $user_id = tep_db_prepare_input($_POST['user_id']);
        $save_date = tep_db_prepare_input($_POST['save_date']);
        $user_payroll = tep_db_prepare_input($_POST['user_payroll']);
        $user_payroll_start_date = tep_db_prepare_input($_POST['user_payroll_start_date']);
        $user_payroll_end_date = tep_db_prepare_input($_POST['user_payroll_end_date']);
        $payroll_contents = tep_db_prepare_input($_POST['payroll_contents']);

        $user_info = tep_get_user_info($ocertify->auth_user);
        $user=$user_info['name'];
        if($user_payroll_list != ''){
          $user_payroll_list_array = array();
          $user_payroll_query = tep_db_query("select id,payroll_id from ".TABLE_USER_PAYROLL_INFO." where id in (".$user_payroll_list.")"); 
          while($user_payroll_array = tep_db_fetch_array($user_payroll_query)){

            $user_payroll_list_array[$user_payroll_array['payroll_id']] = $user_payroll_array['id'];
          }
          tep_db_free_result($user_payroll_query);

          foreach($user_payroll as $user_payroll_key=>$user_payroll_value){

            if(in_array($user_payroll_key,array_keys($user_payroll_list_array))){
              tep_db_query("update ".TABLE_USER_PAYROLL_INFO." set payroll_value='".$user_payroll_value."',start_date='".$user_payroll_start_date[$user_payroll_key]."',end_date='".$user_payroll_end_date[$user_payroll_key]."',contents='".$payroll_contents."',update_user='".$user."',update_date=now() where id='".$user_payroll_list_array[$user_payroll_key]."'");
            }else{
              tep_db_query("insert into ".TABLE_USER_PAYROLL_INFO."(id,payroll_id,user_id,payroll_value,start_date,end_date,contents,create_user,create_date) values(NULL,".$user_payroll_key.",'".$user_id."','".$user_payroll_value."','".$user_payroll_start_date[$user_payroll_key]."','".$user_payroll_end_date[$user_payroll_key]."','".$payroll_contents."','".$user."',now())");
            }
          }
        }else{
         
          foreach($user_payroll as $user_payroll_key=>$user_payroll_value){

            tep_db_query("insert into ".TABLE_USER_PAYROLL_INFO."(id,payroll_id,user_id,payroll_value,start_date,end_date,contents,create_user,create_date) values(NULL,".$user_payroll_key.",'".$user_id."','".$user_payroll_value."','".$user_payroll_start_date[$user_payroll_key]."','".$user_payroll_end_date[$user_payroll_key]."','".$payroll_contents."','".$user."',now())");
          } 
        }
        tep_redirect(tep_href_link(FILENAME_PAYROLLS,''));
        break;
        case 'reset_user_payroll':

        $users_payroll = tep_db_prepare_input($_POST['users_payroll']); 
        $save_date = tep_db_prepare_input($_POST['save_date']);
        $group_id = tep_db_prepare_input($_POST['group_id']);

        $user_payroll_date = tep_start_end_date($group_id,$save_date);

        $save_date = $user_payroll_date['end_date'];

        foreach($users_payroll as $users_payroll_key=>$users_payroll_value){

          foreach($users_payroll_value as $key=>$value){
            $exist_query = tep_db_query("select id from ".TABLE_USER_PAYROLL." where payroll_id='".$users_payroll_key."' and user_id='".$key."' and save_date='".$save_date."'");
            if(tep_db_num_rows($exist_query) > 0){
              tep_db_query("delete from ".TABLE_USER_PAYROLL." where payroll_id='".$users_payroll_key."' and user_id='".$key."' and save_date='".$save_date."'");
            }
          }
        }
        tep_redirect(tep_href_link(FILENAME_PAYROLLS,''));
        break;
        case 'again_computing':
          $users_payroll = tep_db_prepare_input($_POST['users_payroll']);
          $hidden_users_payroll = tep_db_prepare_input($_POST['hidden_users_payroll']);
          $pam_users_payroll = tep_db_prepare_input($_POST['pam_users_payroll']);
          $formula_users_payroll = tep_db_prepare_input($_POST['formula_users_payroll']);
          $save_date = tep_db_prepare_input($_POST['save_date']);
          $group_id = tep_db_prepare_input($_POST['group_id']);

          $pam_array = array();
          foreach($users_payroll as $key=>$value){

            foreach($value as $key_k=>$value_v){
              if($value_v != $hidden_users_payroll[$key][$key_k]){

                $pam_array[$key_k][$pam_users_payroll[$key][$key_k]] = $value_v;
              }
            }
          }

          $replace_pam_value = array();
          foreach($formula_users_payroll as $keys=>$values){

            foreach($values as $keys_k=>$values_v){
              if(!empty($pam_array[$keys_k])){
                $pam_key_array = array_keys($pam_array[$keys_k]);

                if(!in_array($pam_users_payroll[$keys][$keys_k],$pam_key_array)){
                  $replace_pam_value[$keys][$keys_k] = tep_user_payroll($values_v,$keys_k,$save_date,$group_id,$pam_array[$keys_k]);  
                }else{
                  $replace_pam_value[$keys][$keys_k] = $users_payroll[$keys][$keys_k];
                }
              }else{
                  $replace_pam_value[$keys][$keys_k] = $users_payroll[$keys][$keys_k];
              }
            }
          }
          break;
        case 'send_mail':
          $user_id = $_POST['user_id'];
          $users_payroll = $_POST['users_payroll'];
          $payroll_title = $_POST['payroll_title'];
          $payroll_date = tep_db_prepare_input($_POST['save_date']);
          $group_id = tep_db_prepare_input($_POST['group_id']);

          $payroll_email = tep_get_mail_templates('PAYROLL_MAIL_TEMPLATES','0'); 
          $mode_array = array('${USER_NAME}','${CONTENTS}','${PERIOD}');

          //管理者信息
          $admin_info = tep_get_user_info($ocertify->auth_user);

          foreach($user_id as $user_value){

            $payroll_str = '';
            foreach($users_payroll as $payroll_key=>$payroll_value){

              $payroll_str .= $payroll_title[$payroll_key].' '.$payroll_value[$user_value]."\r\n";
            }
            $user_info = tep_get_user_info($user_value);
            $period_date = tep_start_end_date($group_id,$payroll_date);
            $email_title = tep_replace_mail_templates($payroll_email['title'],$user_info['email'],$user_info['name']);
            $email_text = tep_replace_mail_templates($payroll_email['contents'],$user_info['email'],$user_info['name']);
            $replace_array = array($user_info['name'],$payroll_str,$period_date['start_date'].'～'.$period_date['end_date']);
            $email_title = str_replace($mode_array,$replace_array,$email_title);  
            $email_text = str_replace($mode_array,$replace_array,$email_text);

            tep_mail($user_info['name'], $user_info['email'], $email_title, $email_text, $admin_info['name'], $admin_info['email'],0);
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
  var js_ed_orders_input_right_date = '<?php echo ERROR_INPUT_RIGHT_DATE;?>';
  var submit_url = '<?php echo tep_href_link('payrolls_csv_exe.php','csv_exe=true', 'SSL');?>';
  var user_export_confirm = '<?php echo TEXT_USER_EXPORT_CONFIRM;?>';
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
<input type="hidden" name="show_info_id" value="show_user_payroll" id="show_info_id">
<div style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;" id="show_user_payroll"></div>
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
      <?php
      //表示内容获取默认选项
      $show_select_type = 'date';
      $show_select_date = date('Y-m-d',time());
      if(USER_PAYROLL_SETTING != ''){

        $user_payroll_array = unserialize(USER_PAYROLL_SETTING);
        if(isset($user_payroll_array[$ocertify->auth_user])){
          $show_select_date = $user_payroll_array[$ocertify->auth_user]['date'];
        }
      }

      
      $show_selected_date = $show_select_date;

      $default_year = date('Y',strtotime($show_selected_date));
      $default_month = date('m',strtotime($show_selected_date));
      $default_day = date('d',strtotime($show_selected_date));
      ?>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr> 
          <td width="160"><input type="hidden" name="action" value="update_show_user">
    <select id="fetch_year" onchange="change_fetch_date();">
    <?php
      $default_fetch_year = isset($_GET['select_date']) && $_GET['select_date'] ? date('Y',strtotime($_GET['select_date'])) : $default_year; 
      for ($f_num = 2006; $f_num <= 2050; $f_num++) {
	if($default_fetch_year == $f_num || $f_num >= date('Y',time())){
        	echo '<option value="'.$f_num.'"'.(($default_fetch_year == $f_num)?' selected':'').'>'.$f_num.'</option>'; 
      	}	
      }
    ?>
    </select>
    <select id="fetch_month" onchange="change_fetch_date();">
    <?php
      for ($f_num = 1; $f_num <= 12; $f_num++) {
        $default_fetch_month = isset($_GET['select_date']) && $_GET['select_date'] ? date('m',strtotime($_GET['select_date'])) : $default_month; 
        $tmp_fetch_month = sprintf('%02d', $f_num); 
        echo '<option value="'.$tmp_fetch_month.'"'.(($default_fetch_month == $tmp_fetch_month)?' selected':'').'>'.$tmp_fetch_month.'</option>'; 
      }
    ?>
    </select>
    <select id="fetch_day" onchange="change_fetch_date();">
    <?php
      for ($f_num = 1; $f_num <= 31; $f_num++) {
        $default_fetch_day = isset($_GET['select_date']) && $_GET['select_date'] ? date('d',strtotime($_GET['select_date'])) : $default_day; 
        $tmp_fetch_day = sprintf('%02d', $f_num); 
        echo '<option value="'.$tmp_fetch_day.'"'.(($default_fetch_day == $tmp_fetch_day)?' selected':'').'>'.$tmp_fetch_day.'</option>'; 
      }
    ?>
    </select>
          </td><td align="left"> 
          <div class="yui3-skin-sam yui3-g">
<input id="date_orders" type="hidden" name='select_date' size='15' value='<?php echo isset($_GET['select_date']) ? $_GET['select_date'] : $show_selected_date;?>'>
                <div class="date_box">
                <a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a> 
                </div>
                <input type="hidden" id="date_order" name="update_date" value="">
                <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
                <div class="yui3-u" id="new_yui3">
                <div id="mycalendar"></div> 
                </div>
          </div>
          </td>
          </tr></table>
        </td>
        </tr>
        <tr>
        <?php
        //通过组选择员工
        $group_list = tep_get_group_tree();
        //要显示的员工
        $show_group_user = array();
        //选中的员工
        $show_select_group_user = array();
        //获取记录
        $default_select_flag = false;
        if(USER_PAYROLL_SETTING != ''){

          $user_payroll_array = unserialize(USER_PAYROLL_SETTING);
          if(isset($user_payroll_array[$ocertify->auth_user])){
            $show_group_id = $user_payroll_array[$ocertify->auth_user]['group'];
            $show_select_group_user = explode(',',$user_payroll_array[$ocertify->auth_user]['user']);
          }else{
            $show_group_id = $group_list[0]['id'];
            $default_select_flag = true; 
          }
        }else{

          $show_group_id = $group_list[0]['id'];
          $default_select_flag = true;
        }

        //默认选中的组
        $show_group_id = isset($_GET['show_group']) && $_GET['show_group'] != '' ? $_GET['show_group'] : $show_group_id;
        
        if(!in_array($show_group_id,$admin_group_list_array) && $ocertify->npermission != 31){

          $show_group_id = 0;
        }

        $user_sql = "select * from ".TABLE_GROUPS." where id='".$show_group_id."'";
        $user_query = tep_db_query($user_sql);
        if($user_row = tep_db_fetch_array($user_query)){
          $show_group_user = explode('|||',$user_row['all_users_id']);
          $currency_type = $user_row['currency_type'];
        }

        if($default_select_flag == true){

          $show_select_group_user = $show_group_user;
        }
 
        //默认选中的员工
        $show_select_group_user = isset($_GET['show_group_user_list']) && $_GET['show_group_user_list'] != '' ? $_GET['show_group_user_list'] : $show_select_group_user;
  
        $group_str = '';
        $group_str .= '<td class="smallText" width="100" height="25">';
        $group_str .= TEXT_GROUP_SELECT;
        $group_str .= '</td>';
        $group_str .= '<td align="left">';
        $group_str .= '<select name="show_group" onchange="change_user_list(this)">';
       
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
        $group_str .= '<td class="smallText" width="100" height="25" valign="top">';
        $group_str .= TEXT_GROUP_USER_LIST;
        $group_str .= '</td>';
        $group_str .= '<td align="left">';
        $group_str .= '<div id="show_user_list">';
        foreach($show_group_user as $show_list_uid){
          if($show_list_uid!=''){
			$tep_array= tep_get_user_info($show_list_uid);
			$uname_arr[] = array('status'=>$tep_array['status'],'name'=>$tep_array['name']);

          }
        }
	$group_user_list = array_combine($show_group_user,$uname_arr);
        asort($group_user_list);
        $able_user_array = array();

        foreach($group_user_list as $key=>$val) {
          if($val['status'] == 1){
            $group_str .= '<input type="checkbox" name="show_group_user_list[]" id="'.$key.'"';
            if(in_array($key,$show_select_group_user) || (!isset($_GET) && $default_select_flag == true)){
              $group_str .= ' checked="checked" ';
              $able_user_array[] = $key;
            }
            $group_str .= ' value="'.$key.'" >';
            $group_str .=  '<label for="'.$key.'">'.$val['name'].'</label>';
            $group_str .= '&nbsp;&nbsp;&nbsp;';
          }
	}

        $group_str .= '</div>';
        $group_str .= '<div style="float:right;">';
        $group_str .= '<input type="submit" value="'.TEXT_UPDATE.'">';
        $group_str .= '</div>';
        $group_str .= '</td>';
        $group_str .= '</tr>';
        echo $group_str;
        ?>
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
        $default_date = $show_select_date;
        $default_date = isset($_GET['select_date']) ? $_GET['select_date'] : $default_date;
	$form_str = tep_draw_form('edit_users_payroll', FILENAME_PAYROLLS,'action=edit_users_payroll&page='.$_GET['page'], 'post', 'onSubmit="return false;"');
	$payroll_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
	$notice_box = new notice_box('','',$payroll_table_params);
	$payroll_table_row = array();
	$payroll_title_row = array();
	$payroll_title_row[] = array('params' => 'class="dataTableHeadingContent" width="5%"','text' => '<input type="checkbox" name="all_check" onclick="all_select_user(\'user_id[]\');"><input type="hidden" name="save_date" value="'.$default_date.'">');
        $payroll_title_row[] = array('params' => 'class="dataTableHeadingContent_order" style="width:10%;" id="td_name"','text' => '<a href="javascript:payrolls_sort(\'name\',1,\''.TEXT_PAYROLLS_NAME.'\',\''.TEXT_SORT_ASC.'\',\''.TEXT_SORT_DESC.'\',\'\');">'.TEXT_PAYROLLS_NAME.'</a>');
        //获取组对应的工资项目
        $group_id = '';
        $group_id = isset($_GET['show_group']) && $_GET['show_group'] != '' ? $_GET['show_group'] : $show_group_id;
        $groups_users_id = array();
        $groups_payroll_query = tep_db_query("select * from ".TABLE_PAYROLL_SETTLEMENT." where group_id='".$group_id."' order by sort");
        while($groups_payroll_array = tep_db_fetch_array($groups_payroll_query)){
          $payroll_title_row[] = array('params' => 'class="dataTableHeadingContent_order" id="td_title_'.$groups_payroll_array['id'].'"','text' => '<a href="javascript:payrolls_sort(\'title\',1,\''.$groups_payroll_array['title'].'\',\''.TEXT_SORT_ASC.'\',\''.TEXT_SORT_DESC.'\','.$groups_payroll_array['id'].');">'.$groups_payroll_array['title'].'</a><input type="hidden" name="payroll_title['.$groups_payroll_array['id'].']" value="'.$groups_payroll_array['title'].'">');
          $groups_users_id[] = array('id'=>$groups_payroll_array['id'],'value'=>($groups_payroll_array['project_id'] == 0 ? $groups_payroll_array['contents'] : $groups_payroll_array['project_value']),'project_id'=>$groups_payroll_array['project_id'],'pam'=>$groups_payroll_array['contents']);
        }
        tep_db_free_result($groups_payroll_query);
        
	$payroll_title_row[] = array('params' => 'class="dataTableHeadingContent_order" style="width:10%;" id="td_time"','text' => '<input type="hidden" name="group_id" value="'.$group_id.'"><a href="javascript:payrolls_sort(\'time\',1,\''.TEXT_PAYROLLS_OPTION.'\',\''.TEXT_SORT_ASC.'\',\''.TEXT_SORT_DESC.'\',\'\');">'.TEXT_PAYROLLS_OPTION.'</a>');
	$payroll_table_row[] = array('params' => 'class="dataTableHeadingRow" id="tr_index"','text' => $payroll_title_row);
	if($_GET['id'] == '' || !is_numeric($_GET['id'])){
		$payroll_id = 0;
	}else{
		$payroll_id = $_GET['id'];
	}
	
        $show_group_user_list = isset($_GET['show_group_user_list']) && $_GET['show_group_user_list'] != '' ? $_GET['show_group_user_list'] : $show_select_group_user;
        $show_group_user_list = array_filter($show_group_user_list);
        //判断当前员工列表中的员工是否可用
        foreach($show_group_user_list as $k_user=>$v_user){

          if(!in_array($v_user,$able_user_array)){

            unset($show_group_user_list[$k_user]);
          }
        }

        if($ocertify->npermission != 31){
          foreach($show_group_user_list as $show_group_user_key=>$show_group_user_value){

            if(!in_array($show_group_user_value,$admin_user_list_array)){

              unset($show_group_user_list[$show_group_user_key]);
            }
          }
        }

        if(empty($show_group_user_list)){
          $group_data_row[] = array('align' => 'left','params' => 'colspan="7" nowrap="nowrap"', 'text' => '<font color="red"><b>'.TEXT_DATA_IS_EMPTY.'</b></font>');
                    
          $payroll_table_row[] = array('params' => '', 'text' => $group_data_row);  
        } 
        //获取所有员工的相关信息
        $all_users_info = array();
        $all_users_query = tep_db_query("select userid,name from ".TABLE_USERS);
        while($all_users_array = tep_db_fetch_array($all_users_query)){

          $all_users_info[$all_users_array['userid']] = $all_users_array['name'];
        }
        tep_db_free_result($all_users_query); 
        $user_payroll_value = array();
	foreach($show_group_user_list as $users_value) {
		$even = 'dataTableSecondRow';
        	$odd  = 'dataTableRow';
        	if (isset($nowColor) && $nowColor == $odd) {
                	$nowColor = $even;
        	} else {
                	$nowColor = $odd;
        	}
		$user_params = 'id="payroll_'.$users_value.'" class="'.$nowColor.'" onclick="" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
		$user_info = array();
		$group_checkbox = '<input type="checkbox" name="user_id[]" value="'.$users_value.'">';
		$user_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => $group_checkbox
        	);
		$user_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => $all_users_info[$users_value].'<input type="hidden" name="user_name[]" value="'.$all_users_info[$users_value].'">' 
                ); 
                //用户项目的ID
                $user_project_id_array = array();
                $update_time = '';
                foreach($groups_users_id as $payroll_id){
                  //根据工资ID与用户ID，来获取对应的值
                  $user_payroll_value_query = tep_db_query("select id,payroll_value,start_date,end_date,update_date from ".TABLE_USER_PAYROLL_INFO." where payroll_id='".$payroll_id['id']."' and user_id='".$users_value."'");
                  if(tep_db_num_rows($user_payroll_value_query) > 0){
                    $user_payroll_value_array = tep_db_fetch_array($user_payroll_value_query);
                    tep_db_free_result($user_payroll_value_query);
                    //判断工资的有效期
                    $payroll_date = tep_start_end_date($group_id,$default_date);
                    if($user_payroll_value_array['start_date'] != '' && $user_payroll_value_array['end_date'] != ''){
                      if($payroll_date['start_date'] >= $user_payroll_value_array['start_date'] && $payroll_date['end_start'] <= $user_payroll_value_array['end_date']){
                        $user_payroll_val = $user_payroll_value_array['payroll_value'];
                      }else{
                       
                        $user_payroll_val = 0;
                      }
                    }else if($user_payroll_value_array['start_date'] != ''){

                      if($payroll_date['start_date'] >= $user_payroll_value_array['start_date']){

                        $user_payroll_val = $user_payroll_value_array['payroll_value'];
                      }else{
                        $user_payroll_val = 0; 
                      }
                    }else if($user_payroll_value_array['end_date'] != ''){

                      if($payroll_date['end_date'] <= $user_payroll_value_array['end_date']){

                        $user_payroll_val = $user_payroll_value_array['payroll_value'];
                      }else{
                        $user_payroll_val = 0; 
                      } 
                    }else{
                        $user_payroll_val = $user_payroll_value_array['payroll_value']; 
                    }
                    if($update_time == ''){

                      $update_time = $user_payroll_value_array['update_date'];
                    }
                    if($payroll_id['project_id'] == 0){
                      $user_project_id_array[] = $user_payroll_value_array['id'];
                    }
                  }

                  //根据工资ID与用户ID及日期，来获取对应的值
                  $user_payroll_value_query = tep_db_query("select id,payroll_value,update_date from ".TABLE_USER_PAYROLL." where payroll_id='".$payroll_id['id']."' and user_id='".$users_value."' and save_date='".$payroll_date['end_date']."'");
                  $user_payroll_val = '';
                  if(tep_db_num_rows($user_payroll_value_query) > 0){
                    $user_payroll_value_array = tep_db_fetch_array($user_payroll_value_query);
                    tep_db_free_result($user_payroll_value_query);
                    $user_payroll_val = $user_payroll_value_array['payroll_value'];
                    if($update_time == ''){

                      $update_time = $user_payroll_value_array['update_date'];
                    }
                    
                  }

                  $payroll_value = $user_payroll_val != '' && !isset($_GET['reset']) ? $user_payroll_val :tep_user_payroll($payroll_id['value'],$users_value,$default_date,$group_id,array(),$error_pam_array);
                  if($user_payroll_val != '' && !isset($_GET['reset'])){
                    $error_pam_temp = tep_param_error($payroll_id['value'],$group_id);

                    foreach($error_pam_temp as $pam_value){

                      $error_pam_array[] =$pam_value ;
                    }
                  }
                  if($_GET['action'] == 'again_computing' && isset($_POST['users_payroll']) && !empty($replace_pam_value)){

                    $payroll_value = $replace_pam_value[$payroll_id['id']][$users_value];
                  }
                  $user_payroll_value[$payroll_id['id']] += $payroll_value;
                  $user_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => '<input type="text" name="users_payroll['.$payroll_id['id'].']['.$users_value.']" value="'.$payroll_value.'" style="width:80%;" onblur="if(this.value==\'\'){this.value=0;}"><input type="hidden" name="hidden_users_payroll['.$payroll_id['id'].']['.$users_value.']" value="'.$payroll_value.'"><input type="hidden" name="pam_users_payroll['.$payroll_id['id'].']['.$users_value.']" value="'.$payroll_id['pam'].'"><input type="hidden" name="formula_users_payroll['.$payroll_id['id'].']['.$users_value.']" value="'.$payroll_id['value'].'">' 
                  );  
                }
                $user_project_id_array = array_filter($user_project_id_array);
		$user_info[] = array(
                	'params' => 'class="dataTableContent"',
                	'text'   => '<a href="javascript:void(0)" onclick="show_user_payroll(this,\''.$users_value.'\',\''.$all_users_info[$users_value].'\',\''.$group_id.'\',\''.implode(',',$user_project_id_array).'\',\''.$default_date.'\',\''.$group_id.'\')">'.tep_get_signal_pic_info(date('Y-m-d H:i:s',strtotime(($update_time != '' && $update_time != '0000-00-00 00:00:00' ? $update_time : $update_time)))).'</a><input type="hidden" name="payrolls_time[]" value="'.$update_time.'">'
        	);
		$payroll_table_row[] = array('params' => $user_params, 'text' => $user_info);
        }
        if(isset($currency_type)){

          $currency_type_array = array(TEXT_PAYROLLS_CURRENCY_TYPE_JPY,TEXT_PAYROLLS_CURRENCY_TYPE_RMB,TEXT_PAYROLLS_CURRENCY_TYPE_USD,TEXT_PAYROLLS_CURRENCY_TYPE_VND); 
          $i = 0;
          $user_info = array();
          $user_info[] = array(
               	         'params' => '',
               	         'text'   => $i == 0 ? TEXT_PAYROLLS_TOTAL : ''  
                              );
          $user_info[] = array(
               	        'params' => '',
               	        'text'   => $currency_type_array[$currency_type].'<input type="hidden" name="currency_type_str" value="'.$currency_type_array[$currency_type].'">' 
                               );
          foreach($groups_users_id as $payroll_id){
            $user_info[] = array(
                	           'params' => '',
                	           'text'   => '<input type="text" style="width:80%;" disabled name="users_payroll_total['.$payroll_id['id'].']" value="'.$user_payroll_value[$payroll_id['id']].'">' 
                                 ); 
          } 
          $user_info[] = array(
               	         'params' => '',
               	         'text'   => ''  
                               );
          $payroll_table_row[] = array('params' => 'id="payrolls_total"', 'text' => $user_info);  
          $i++;
        }
	$notice_box->get_form($form_str);
	$notice_box->get_contents($payroll_table_row);
	$notice_box->get_eof(tep_eof_hidden());
	echo $notice_box->show_notice();
?>	
	    </td>
            </tr>
            </table>
<br>
		    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:-10px;">
                    <tr>                 
                    <td valign="top" class="smallText" colspan="2">
                    <?php 
                    echo '<select name="user_action" onchange="user_change_action(this.value, \'user_id[]\','.$ocertify->npermission.');">';
                    echo '<option value="0">'.TEXT_CONTENTS_SELECT_ACTION.'</option>';   
                    echo '<option value="1">'.TEXT_PAYROLLS_SEND_MAIL.'</option>';
                    echo '<option value="2">'.TEXT_PAYROLLS_EXPORT.'</option>';
                    echo '<option value="3">'.TEXT_PAYROLLS_PRINT.'</option>';
                    echo '</select>';
                    $error_pam_array = array_unique($error_pam_array);
                    if(!empty($error_pam_array)){
                      $error_string = '';
                      $error_string_array = array();
                      foreach($error_pam_array as $error_value){
                        $error_array = array();
                        $payroll_title_query = tep_db_query("select title from ".TABLE_PAYROLL_SETTLEMENT." where group_id='".$group_id."' and project_value like '%".$error_value."%'");
                        while($payroll_title_array = tep_db_fetch_array($payroll_title_query)){

                          $error_array[] = TEXT_ERROR_LEFT.$payroll_title_array['title'].TEXT_ERROR_RIGHT; 
                        }
                        tep_db_free_result($payroll_title_query);
                        $error_string_array[] = implode(TEXT_ERROR_TITLE_LINK,$error_array).TEXT_ERROR_LINK.$error_value;
                      }
                      echo '<br>'.sprintf(TEXT_PAYROLLS_ERROR_PAM,implode('、',$error_string_array));
                    }
                    ?> 
                    </td>
                    </tr> 
                    <tr><td></td><td align="right">
                     <div class="td_button"><?php 
                     //通过site_id判断是否允许新建
                     if (in_array(0,$site_id_array)) {
                       echo '<a href="javascript:void(0)">' .tep_html_element_button(TEXT_PAYROLLS_AGAIN_COMPUTING,'onclick="again_computing();"') . '</a>';
                       echo '&nbsp;<a href="javascript:void(0)">' .tep_html_element_button(TEXT_PAYROLLS_RESET,'onclick="reset_user_payroll(\''.tep_get_all_get_params(array('reset')).'\');"') . '</a>';
                       echo '&nbsp;<a href="javascript:void(0)">' .tep_html_element_button(IMAGE_SAVE,'onclick="save_user_payroll();"') . '</a>';
                     }else{
                       echo tep_html_element_button(TEXT_PAYROLLS_AGAIN_COMPUTING,'disabled="disabled"').'&nbsp;' ;
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
