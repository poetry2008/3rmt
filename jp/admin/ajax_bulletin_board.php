<?php
  require('includes/application_top.php');
  include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.'bulletin_board.php');
  require(DIR_FS_ADMIN . '/classes/notice_box.php');
if (isset($_GET['action']) and $_GET['action']) {
  switch ($_GET['action']){
		/*----------------------------------------------
		  case 'chang_collect_status' �����ղ�״̬
		  case 'new_bulletin' ��ʾ�½�bulletin��ҳ��
		  case 'edit_bulletin' �޸�bulletin����
		  case 'new_bulletin_reply' ��ʾ�½��ظ�����
		  case 'edit_bulletin_reply'   ��ʾ�����ظ�����
		  --------------------------------------------*/
	case 'change_collect_status':
		$id=$_POST['id'];
		$collect='';
		if($_POST['collect_type']!='show_reply'){
			$collect_info=tep_db_fetch_array(tep_db_query("select collect from ".TABLE_BULLETIN_BOARD." where id=$id"));
			$collect_user=explode(",",$collect_info['collect']);
			if(in_array($_POST['user_id'],$collect_user)){
				foreach($collect_user as $user){
					if($user==$_POST['user_id']||$user=='')continue;
					$collect.=",".$user;
				}
			}else{
				$collect=$collect_info['collect'].",".$_POST['user_id'];
			}
			$collect=substr($collect,1);
			$sql="update ".TABLE_BULLETIN_BOARD." set collect='$collect' where id=$id";
		}
		else {
			$collect_info=tep_db_fetch_array(tep_db_query("select collect from ".TABLE_BULLETIN_BOARD_REPLY." where id=$id"));
			$collect_user=explode(",",$collect_info['collect']);
			if(in_array($_POST['user_id'],$collect_user)){
				foreach($collect_user as $user){
					if($user==$_POST['user_id']||$user=='')continue;
					$collect.=",".$user;
				}
			}else{
				$collect=$collect_info['collect'].",".$_POST['user_id'];
			}
			 $collect=substr($collect,1);
			$sql="update ".TABLE_BULLETIN_BOARD_REPLY." set collect='$collect' where id=$id";
		}
		if(!tep_db_query($sql))print "sql:$sql";
		break;
	case 'new_bulletin':
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $page_str = '<a onclick="hidden_info_box('.($_GET['bulletin_sta'] == 'drafts' && $_GET['latest_bulletin_id'] > 0 ? '1' : ($_GET['latest_bulletin_id'] < 0 ? '2' : '3')).');" href="javascript:void(0);">X</a>';
	$heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	$heading[] = array('text' => TEXT_CREATE_BULLETIN);
	$form_str = tep_draw_form('new_bulletin_board', 'bulletin_board.php','action=create_bulletin&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type'].'','post','enctype="multipart/form-data" onsubmit="return check_value(0)" id="form_create_bulletin"'); 
	 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
 
	 $bulletin_content_table = array();
	 $bulletin_content_row_from = array();
	 $bulletin_content_row_from[] = array('params'=>'width="20%"','text'=>TEXT_TITLE);
	 $bulletin_content_row_from[] = array('params'=>'width="70%" style="color:#FF0000;"','text'=>'<input type="text" name="title" id="bulletin_title" size=80> * '.TEXT_MUST_WRITE.'<br /><div id="popup_title" style="display:none;color:#FF0000;">'.TEXT_WARNING_EMPTY.'</div>');
	 $bulletin_content_row_from[] = array('params'=>'width="10%"','text'=>'');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_from);
	 $bulletin_content_row_manager = array();
	 $bulletin_content_row_manager [] = array('text'=>"From(".TEXT_MANAGER.")");
	 $sql_for_all_users = 'select userid, name,email from users where status=1 order by name asc';
	 $sql_for_all_users_query = tep_db_query($sql_for_all_users);
	 $user_name=array();
	 while($row=tep_db_fetch_array($sql_for_all_users_query)){
		 $user_name[]=$row['userid'];
	 }
	 $option_html='<option >----</option>';
	 foreach($user_name as $name){
		 $option_html.='<option name="manager" value='.$name.'>'.$name.'</option>';
	 }
	 $bulletin_content_row_manager [] = array('text'=>'<select name="manager" value ="----">'.$option_html.'</select>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_manager);
	 
	 //To
	 $bulletin_content_row_allow = array();
	 $bulletin_content_row_allow [] =array('text'=>'To');
	 $bulletin_content_row_allow [] =array('text'=>'<table width="100%"><tr><td><input type="radio" value="all" onclick="select_allow(this,0)" checked="true" name="select_all" id="select_all_radio">ALL</td><td><input type="radio" values="group" name="select_group" id="select_group_radio"  onclick="select_allow(this,1)">'.TEXT_GROUP_SELECT.'</td><td><input type="radio" value="id" onclick="select_allow(this,2)" name="select_id" id="select_id_radio">'.TEXT_SELECT_ID.'</td></tr></table>');
	 $bulletin_content_row_allow [] =array('text'=>'');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_allow);
	 $users_list_html='';
	 foreach($user_name as $name){
		 $users_list_html.='<div value='.$name.' onclick="checkbox_event(this,event)" style="cursor: pointer; -moz-user-select: none; background: none repeat scroll 0% 0% rgb(255, 255, 255); color: black;">
			 <input type="hidden" name="all_staff" value="'.$name.'">
			 '.$name.'
			 </div>';
	 }
	 $group_row=tep_db_query("select name from groups");
	 $group_list_html="";
	 while($row=tep_db_fetch_array($group_row)){
		 $group_list_html.='<div value='.$row["name"].' onclick="checkbox_event(this,event)" style="cursor: pointer; -moz-user-select: none; background: none repeat scroll 0% 0% rgb(255, 255, 255); color: black;">
			 <input type="checkbox" hidden="" name="all_staff" value="'.$row["name"].'">
			 '.$row["name"].'
			 </div>';
	 }
	 $bulletin_content_row_choose = array();
	 $bulletin_content_row_choose [] =  array('text'=> '');
	 $bulletin_content_row_choose [] =  array('text'=> '<div width="100%" id="select_user" style="display:none;"><table width="100%">
	<tr>
		<td align="center" width="45%">'.TEXT_TO_BODY.'</td>
		<td align="center" width="10%"></td>
		<td align="center" width="45%">'.TEXT_STAFF_LIST.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="user_add" width="100%" style="overflow-y:scroll;height:105px;"></div></td>
		<td  board="0" align="center" style="vertical-align:middle;">
			<div style="background:#CCCCCC;" width="100%" onclick="add_select_user(1)">&lt'.ADD_STAFF.'</div>
			<div style="background:#CCCCCC;" width="100%" onclick="delete_select_user(1)">'.DELETE_STAFF.'&gt</div>
		</td>
		<td board="0" style="background:#FFF;border:1px #E0E0E0 solid;"><div width="100%" id="user_delete_to" style="overflow-y:scroll;height:105px;">'.$users_list_html.'</div></td>
	</tr>
</table></div>
<div width="100%" id="select_group" style="display:none;"><table width="100%">
	<tr>
		<td align="center" width="45%">'.TEXT_TO_BODY.'</td>
		<td align="center" width="10%"></td>
		<td align="center" width="45%">'.TEXT_STAFF_LIST.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="group_add" width="100%" style="overflow-y:scroll;height:105px;"></div></td>
		<td align="center" style="vertical-align:middle;">
			<div style="background:#CCCCCC;" width="100%" onclick="add_select_user(0)">&lt'.ADD_STAFF.'</div>
			<div style="background:#CCCCCC;" width="100%" onclick="delete_select_user(0)">'.DELETE_STAFF.'&gt</div>
		</td>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div width="100%" id="group_delete_to" style="overflow-y:scroll;height:105px;">'.$group_list_html.'</div></td>
	</tr>
</table></div>');
	$bulletin_content_table[] = array('text'=> $bulletin_content_row_choose);
	$mark_array = explode(',',$_GET['mark']);
	$pic_list_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." order by sort_order asc"); 
	 $users_icon = '<ul class="table_img_list" style="width:100%">'; 
    while ($pic_list_res = tep_db_fetch_array($pic_list_raw)) {
		 $users_icon .= '<li><input type="checkbox" name="pic_icon[]" value="'.$pic_list_res['id'].'"'.(in_array($pic_list_res['id'],$mark_array) ? ' checked="checked"' : '').' style="padding-left:0;margin-left:0;"><img src="images/icon_list/'.$pic_list_res['pic_name'].'" alt="'.$pic_list_res['pic_alt'].'" title="'.$pic_list_res['pic_alt'].'"></li>'; 
   }
	$users_icon .= '</ul>';
	$bulletin_content_row_mark = array();
	$bulletin_content_row_mark[] = array('text'=> TEXT_MARK);
	$bulletin_content_row_mark[] = array('text'=> $users_icon.'<input type="hidden" id="old_mark_str" value="'.$_GET['mark'].'">');
	$bulletin_content_table[] = array('text'=> $bulletin_content_row_mark);
	$bulletin_content_row_text = array();
	$bulletin_content_row_text[] = array('text'=> TEXT_CONTENT);
 	$bulletin_text_area =  '<textarea style="resize:vertical; width:100%;" class="textarea_width" rows="10" id="current_contents" name="content"></textarea><div id="popup_content" style="display:none;color:#FF0000;">'.TEXT_WARNING_EMPTY.'</div>';
	 $bulletin_content_row_text[] = array('text'=> $bulletin_text_area);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_text);
	 $bulletin_content_row_must_write = array();
	 $bulletin_content_row_must_write[] = array('text'=> '');
	 $bulletin_content_row_must_write[] = array('text'=> '<div id="bulletin_must_write" style="display: none;"><span style="color:#ff0000;"> '.CONTENT_MUST_WRITE.'</span></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_must_write);
	 $bulletin_content_row_addfile = array();
     $bulletin_content_row_addfile[] = array('text'=> TEXT_ADDFILE);
     $bulletin_content_row_addfile[] = array('text'=> '<div id="bulletin_file_boder"><input type="file" id="bulletin_file" name="bulletin_file[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\'bulletin_file\')">'.DELETE_STAFF.'</a>&nbsp;&nbsp;<a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="add_email_file(\'bulletin_file\')">'.BUTTON_ADD_TEXT.'</a></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_addfile);
	 $bulletin_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="submit"   value="'.TEXT_SUBMIT.'"><input type="reset" value="'.TEXT_RESET.'">'.$bulletin_buttons);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_submit);
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($bulletin_content_table); 
	 echo $notice_box->show_notice();
	 break;

	//�޸�bulletin
	case 'edit_bulletin':
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $bulletin_id=$_POST['bulletin_id'];
	 $page_str = '<a onclick="hidden_info_box('.($_GET['bulletin_sta'] == 'drafts' && $_GET['latest_bulletin_id'] > 0 ? '1' : ($_GET['latest_bulletin_id'] < 0 ? '2' : '3')).');" href="javascript:void(0);">X</a>';
	$heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	$heading[] = array('text' => TEXT_EDIT_BULLETIN);
	$group_raw=tep_db_fetch_array(tep_db_query("select name from ".TABLE_GROUPS." where (all_managers_id='$ocertify->auth_user' or all_managers_id like '$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user|||%' or all_managers_id like '%|||$ocertify->auth_user') limit 1"));
	$group_name=$group_raw['name'];
	$page_raw=(tep_db_fetch_array(tep_db_query("select count(id) num from ".TABLE_BULLETIN_BOARD." where id>=$bulletin_id ".($ocertify->npermission==31?"":"and (allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))"))));
	$page=ceil($page_raw['num']/MAX_DISPLAY_SEARCH_RESULTS);
	$next_raw=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD." where id<'$bulletin_id' ".($ocertify->npermission==31?"":"and (allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))")." order by id desc limit 1"));
	$next=$next_raw['id'];
	$last_raw=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD." where id>'$bulletin_id' ".($ocertify->npermission==31?"":"and (allow='all' or (allow like 'id:%' and( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))")."  order by id asc limit 1"));
	$last=$last_raw['id'];
	$max_id=0;
	$min_id=100000000;
	$limit_str=(($page-1)*MAX_DISPLAY_SEARCH_RESULTS).','.MAX_DISPLAY_SEARCH_RESULTS;
	$max_min_raw=tep_db_query("select * from ".TABLE_BULLETIN_BOARD."  ".($ocertify->npermission==31?"":"where (allow='all' or (allow like 'id:%' and ( allow like '%:$ocertify->auth_user,%' or allow like '%:$ocertify->auth_user' or allow like '%,$ocertify->auth_user,%' or allow like '%,$ocertify->auth_user') ) or (allow like 'group:%' and (allow like '%:$group_name,%' or allow like '%:$group_name' or allow like '%,$group_name,%' or allow like '%,$group_name')))")." order by id desc limit ".$limit_str);
	while($row=tep_db_fetch_array($max_min_raw)){
		if($row['id']>$max_id)$max_id=$row['id'];
		if($row['id']<$min_id)$min_id=$row['id'];
	}
	if($next<$min_id)$turn_html='<a href="javascript:void(0)" onclick="show_link_bulletin_info('.$last.')">'.TEXT_LAST.'</a>';
	else if($last>$max_id||$last==''){
		$turn_html='<a href="javascript:void(0)" onclick="show_link_bulletin_info('.$next.')">'.TEXT_NEXT.'</a>';
	}else{
		$turn_html='<a href="javascript:void(0)" onclick="show_link_bulletin_info('.$last.')">'.TEXT_LAST.'</a><a href="javascript:void(0)" onclick="show_link_bulletin_info('.$next.')">'.TEXT_NEXT.'</a>';
	}
	 $form_str = tep_draw_form('new_bulletin_board', 'bulletin_board.php','action=update_bulletin&bulletin_id='.$bulletin_id.'&order_sort='.$_GET['order_sort'].'&order_type='.$_GET['order_type'].'&page='.$_GET['page'],'post','enctype="multipart/form-data" id="form1"'); 
	 $heading[] = array('align' => 'right', 'text' => $turn_html.'<span id="next_prev"></span>&nbsp&nbsp'.$page_str);

     //bulletin infomation
	 $bulletin_sql="select * from bulletin_board where id=$bulletin_id";
	 $bulletin_raw=tep_db_query($bulletin_sql);
	 $bulletin_info=tep_db_fetch_array($bulletin_raw);

	 $users_select=explode(':',$bulletin_info['allow']);
	 $select_type=$users_select[0];
	 $id_show="none";
	 $group_show="none";
	 switch($select_type){
		 case 'all':
			echo '<script>document.getElementById("select_all_radio").checked=true;</script>';
			 break;
		 case 'group':
			echo '<script>document.getElementById("select_group_radio").checked=true;</script>';
			 $group_show="inline";
			 break;
		 case 'id':
			 $id_show="inline";
			echo '<script>document.getElementById("select_id_radio").checked=true;</script>';
			 break;
	 }
	 $user=$ocertify->auth_user;
	 $bulletin_content_table = array();
	 $bulletin_content_row_from = array();
	 $bulletin_content_row_from[] = array('params'=>'width="20%"','text'=>TEXT_TITLE);
	 $bulletin_content_row_from[] = array('params'=>'width="100%" style="color:#FF0000;"','text'=>'<input type="text" name="title" value="'.$bulletin_info["title"].'" id="bulletin_title" size="80" '.(($ocertify->npermission>=15||$user==$bulletin_info['author']||$user==$bulletin_info['manager'])?"":'disabled="disabled"').'> * '.TEXT_MUST_WRITE);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_from);
	 $bulletin_content_row_manager = array();
	 $bulletin_content_row_manager [] = array('text'=>TEXT_MANAGER);
	 $sql_for_all_users = 'select userid, name,email from users where status=1 order by name asc';
	 $sql_for_all_users_query = tep_db_query($sql_for_all_users);
	 $user_name=array();
	 while($row=tep_db_fetch_array($sql_for_all_users_query)){
		 $user_name[]=$row['userid'];
	 }
	 $option_html='<option name="manager" value="'.$bulletin_info["manager"].'">'.$bulletin_info["manager"].'</option>';
	 foreach($user_name as $name){
		 if($name==$bulletin_info['manager'])continue;
		 $option_html.='<option name="manager" value='.$name.'>'.$name.'</option>';
	 }
	 $bulletin_content_row_manager [] = array('text'=>'<select name="manager" value ="">'.$option_html.'</select>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_manager);
	 
	 //To
	 $bulletin_content_row_allow = array();
	 $bulletin_content_row_allow [] =array('text'=>'TO');
	 $bulletin_content_row_allow [] =array('text'=>'<table width="100%"><tr><td><input type="radio" value="all" onclick="select_allow(this,0)" name="select_all" id="select_all_radio" >ALL</td><td><input type="radio" values="group" name="select_group" id="select_group_radio"  onclick="select_allow(this,1)" >'.TEXT_GROUP_SELECT.'</td><td><input type="radio" value="id" onclick="select_allow(this,2)" name="select_id" id="select_id_radio">'.TEXT_SELECT_ID.'</td></tr></table>');
	 $bulletin_content_row_allow [] =array('text'=>'');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_allow);
	 $users_list_html='';
	 $users_select_html='';
	 $users_select=explode(',',$users_select[1]);
	 foreach($user_name as $name){
		 if(in_array($name,$users_select))$users_select_html.='<div value='.$name.' onclick="checkbox_event(this,event)" style="cursor: pointer; -moz-user-select: none; background: none repeat scroll 0% 0% rgb(255, 255, 255); color: black;">
			 <input type="hidden"  name="selected_staff[]" value="'.$name.'">
			 '.$name.'
			 </div>';
		 else $users_list_html.='<div value='.$name.' onclick="checkbox_event(this,event)" style="cursor: pointer; -moz-user-select: none; background: none repeat scroll 0% 0% rgb(255, 255, 255); color: black;">
			 <input type="checkbox" hidden="" name="all_staff" value="'.$name.'">
			 '.$name.'
			 </div>';
	 }
	 $group_row=tep_db_query("select name from groups");
	 $group_select_html="";
	 $group_list_html="";
	 while($row=tep_db_fetch_array($group_row)){
		 if(in_array($row['name'],$users_select)) $group_select_html.='<div value='.$row["name"].' onclick="checkbox_event(this,event)" style="cursor: pointer; -moz-user-select: none; background: none repeat scroll 0% 0% rgb(255, 255, 255); color: black;">
			 <input type="hidden" name="selected_staff[]" value="'.$row["name"].'">
			 '.$row["name"].'
			 </div>';
		 else $group_list_html.='<div value='.$row["name"].' onclick="checkbox_event(this,event)" style="cursor: pointer; -moz-user-select: none; background: none repeat scroll 0% 0% rgb(255, 255, 255); color: black;">
			 <input type="checkbox" hidden="" name="all_staff" value="'.$row["name"].'">
			 '.$row["name"].'
			 </div>';
	 }
	 $bulletin_content_row_choose = array();
	 $bulletin_content_row_choose [] =  array('text'=> '');
	 $bulletin_content_row_choose [] =  array('text'=> '<div width="100%" id="select_user" style="display:'.$id_show.';"><table width="100%">
	<tr>
		<td align="center" width="45%">'.TEXT_TO_BODY.'</td>
		<td align="center" width="10%"></td>
		<td align="center" width="45%">'.TEXT_STAFF_LIST.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="user_add" width="100%" style="overflow-y:scroll;height:105px;">'.$users_select_html.'</div></td>
		<td align="center" style="vertical-align:middle;">
			<div style="background:#CCCCCC;" width="100%" onclick="add_select_user(1)">&lt'.ADD_STAFF.'</div>
			<div style="background:#CCCCCC;" width="100%" onclick="delete_select_user(1)">'.DELETE_STAFF.'&gt</div>
		</td>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div width="100%" id="user_delete_to" style="overflow-y:scroll;height:105px;">'.$users_list_html.'</div></td>
	</tr>
</table></div>
<div width="100%" id="select_group" style="display:'.$group_show.';"><table width="100%">
	<tr>
		<td align="center" width="45%">'.TEXT_TO_BODY.'</td>
		<td align="center" width="10%"></td>
		<td align="center" width="45%">'.TEXT_STAFF_LIST.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="group_add" width="100%" style="overflow-y:scroll;height:105px;">'.$group_select_html.'</div></td>
		<td align="center" style="vertical-align:middle;">
			<div style="background:#CCCCCC;" width="100%" onclick="add_select_user(0)">&lt'.ADD_STAFF.'</div>
			<div style="background:#CCCCCC;" width="100%" onclick="delete_select_user(0)">'.DELETE_STAFF.'&gt</div>
		</td>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div width="100%" id="group_delete_to" style="overflow-y:scroll;height:105px;">'.$group_list_html.'</div></td>
	</tr>
</table></div>');
	$bulletin_content_table[] = array('text'=> $bulletin_content_row_choose);
	$mark_array = explode(',',$bulletin_info['mark']);
	$pic_list_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." order by sort_order asc"); 
    $users_icon = '<ul class="table_img_list" style="width:100%">'; 
    while ($pic_list_res = tep_db_fetch_array($pic_list_raw)) {
     $users_icon .= '<li><input type="checkbox" name="pic_icon[]" value="'.$pic_list_res['id'].'"'.(in_array($pic_list_res['id'],$mark_array) ? ' checked="checked"' : '').' style="padding-left:0;margin-left:0;"><img src="images/icon_list/'.$pic_list_res['pic_name'].'" alt="'.$pic_list_res['pic_alt'].'" title="'.$pic_list_res['pic_alt'].'"></li>'; 
   }
	$users_icon .= '</ul>';
	$bulletin_content_row_mark = array();
	$bulletin_content_row_mark[] = array('text'=> TEXT_MARK);
	$bulletin_content_row_mark[] = array('text'=> $users_icon.'<input type="hidden" id="old_mark_str" value="'.$_GET['mark'].'">');
	$bulletin_content_table[] = array('text'=> $bulletin_content_row_mark);
	$bulletin_content_row_text = array();
	$bulletin_content_row_text[] = array('text'=> TEXT_CONTENT);
	$user=$ocertify->auth_user;
 	$bulletin_text_area =  '<textarea style="resize:vertical; width:100%;" class="textarea_width" rows="10" id="current_contents" '.(($ocertify->npermission>=15||$user==$bulletin_info['author']||$user==$bulletin_info['manager'])?"":'disabled="disabled"').'   name="content">'.$bulletin_info["content"].'</textarea>';
	 $bulletin_content_row_text[] = array('text'=> $bulletin_text_area);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_text);
	 $file_download_url='<div>';
	 $index=1;
	 foreach(explode('|||',$bulletin_info['file_path']) as $value){
		 if($value=='')continue;
		 $file_download_url.='&nbsp;<div id="delete_file_'.$index.'" style="float:left;"><a href="'.PATH_BULLETIN_BOARD_UPLOAD.$value.'" style="text-decoration:underline;color:#0000FF;" >'.$value.'</a>';
		 if($user==$bulletin_info['manager']||$ocertify->npermission==31)$file_download_url.='<a href="javascript:void(0)" onclick="delete_file(\'delete_file_'.$index.'\',\''.$value.'\')" style="text-decoration:underline;color:#0000FF;">&nbsp;X&nbsp;</a>';
		$index++;
		$file_download_url.='&nbsp;&nbsp;&nbsp;</div>';
	 }
	 $file_download_url.='</div>';
	 $bulletin_content_row_download = array();
	 $bulletin_content_row_download[] = array('text'=> TEXT_FILE_DOWNLOAD_URL);
	 $bulletin_content_row_download[] = array('text'=> $file_download_url);
	 if($file_download_url)$bulletin_content_table[] = array('text'=> $bulletin_content_row_download);
	 $bulletin_content_row_addfile = array();
     $bulletin_content_row_addfile[] = array('text'=> TEXT_ADDFILE);
     $bulletin_content_row_addfile[] = array('text'=> '<div id="bulletin_file_boder"><input type="file" id="bulletin_file" name="bulletin_file[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\'bulletin_file\')">'.DELETE_STAFF.'</a>&nbsp;&nbsp;<a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="add_email_file(\'bulletin_file\')">'.BUTTON_ADD_TEXT.'</a></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_addfile);
	 $bulletin_content_row_author=array();
	 $bulletin_content_row_author[] = array('text'=>TEXT_AUTHOR."    ".$bulletin_info['author']);
	 $bulletin_content_row_author[] = array('text'=>TEXT_DONE_TIME.'    '.$bulletin_info['time']);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_author);

	 $bulletin_content_row_update=array();
	 $bulletin_content_row_update[] = array('text'=>TEXT_UPDATE_AUTHOR."    ".$bulletin_info['update_author']);
	 $bulletin_content_row_update[] = array('text'=>TEXT_UPDATE_TIME.'    '.$bulletin_info['update_time']);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_update);
	 if($ocertify->npermission>=15||$user==$bulletin_info['manager']||$user==$bulletin_info['author'])$delete_button_html='<input type="button" value="'.TEXT_RESET.'"onclick="delete_bulletin('.$bulletin_info["id"].',0)">';
	 $bulletin_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="submit" '.(($ocertify->npermission>=15||$user==$bulletin_info['author']||$user==$bulletin_info['manager'])?"":'disabled="disabled"').'  value="'.TEXT_SUBMIT.'">'.$delete_button_html);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_submit);
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($bulletin_content_table); 
	 echo $notice_box->show_notice();
	 break;

	case 'new_bulletin_reply':
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $bulletin_id=$_POST['bulletin_id'];
	 $page_str = '<a onclick="hidden_info_box('.($_GET['bulletin_sta'] == 'drafts' && $_GET['latest_bulletin_id'] > 0 ? '1' : ($_GET['latest_bulletin_id'] < 0 ? '2' : '3')).');" href="javascript:void(0);">X</a>';
	$heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	$heading[] = array('text' => TEXT_CREATE_BULLETIN_ERPLY);
	$form_str = tep_draw_form('new_bulletin_board', 'bulletin_board.php','action=create_bulletin_reply&bulletin_id='.$_POST["bulletin_id"].'','post','enctype="multipart/form-data" id="form1" onsubmit="return check_value(1)"'); 
	 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
 
	 $bulletin_content_table = array();
	//���
	 $mark_array = explode(',',$_GET['mark']);
	 $pic_list_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." order by sort_order asc"); 
	 $users_icon = '<ul class="table_img_list" style="width:100%">'; 
	 while ($pic_list_res = tep_db_fetch_array($pic_list_raw)) {
		 $users_icon .= '<li><input type="checkbox" name="pic_icon[]" value="'.$pic_list_res['id'].'"'.(in_array($pic_list_res['id'],$mark_array) ? ' checked="checked"' : '').' style="padding-left:0;margin-left:0;"><img src="images/icon_list/'.$pic_list_res['pic_name'].'" alt="'.$pic_list_res['pic_alt'].'" title="'.$pic_list_res['pic_alt'].'"></li>'; 
   }
	 $users_icon .= '</ul>';
	 $bulletin_content_row_mark = array();
	 $bulletin_content_row_mark[] = array('params'=>'width="20%"','text'=> TEXT_MARK);
	 $bulletin_content_row_mark[] = array('text'=> $users_icon.'<input type="hidden" id="old_mark_str" value="'.$_GET['mark'].'">');
	 $bulletin_content_row_mark[] = array('params'=>'width="10%"','text'=> ' ');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_mark);

    
	 $bulletin_content_row_text[] = array('text'=> TEXT_CONTENT_REPLY);
 	 $bulletin_text_area =  '<textarea style="resize:vertical; width:100%;" class="textarea_width" rows="10" id="current_contents" name="content"></textarea> <div id="popup_content" style    ="display:none;color:#FF0000;">'.TEXT_WARNING_EMPTY.'</div>';
	 $bulletin_content_row_text[] = array('text'=> $bulletin_text_area);
	 $bulletin_content_row_text[] = array('params'=>'style="color:#FF0000;"','text'=> '&nbsp;*'.TEXT_MUST_WRITE);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_text);
	 $bulletin_content_row_must_write = array();
	 $bulletin_content_row_must_write[] = array('text'=> '');
	 $bulletin_content_row_must_write[] = array('text'=> '<div id="bulletin_must_write" style="display: none;"><span style="color:#ff0000;">'.CONTENT_MUST_WRITE.'</span></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_must_write);
	 $bulletin_content_row_addfile = array();
     $bulletin_content_row_addfile[] = array('text'=> TEXT_ADDFILE);
     $bulletin_content_row_addfile[] = array('text'=> '<div id="bulletin_file_boder"><input type="file" id="bulletin_file" name="bulletin_file[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\'bulletin_file\')">'.DELETE_STAFF.'</a>&nbsp;&nbsp;<a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="add_email_file(\'bulletin_file\')">'.BUTTON_ADD_TEXT.'</a></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_addfile);
	 $bulletin_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="submit"   value="'.TEXT_SUBMIT.'"><input type="reset" value="'.TEXT_RESET.'">'.$bulletin_buttons);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_submit);
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($bulletin_content_table); 
	 echo $notice_box->show_notice();
	 break;

	case 'edit_bulletin_reply':
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
     $bulletin_info=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id=".$_POST['id'].""));
	 $page_str = '<a onclick="hidden_info_box('.($_GET['bulletin_sta'] == 'drafts' && $_GET['latest_bulletin_id'] > 0 ? '1' : ($_GET['latest_bulletin_id'] < 0 ? '2' : '3')).');" href="javascript:void(0);">X</a>';
	 $heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	 $heading[] = array('text' => TEXT_EDIT_BULLETIN_ERPLY);
	 $id=$bulletin_info['id'];
	 $bulletin_id=$bulletin_info['bulletin_id'];
	 $turn_html='';
	$next_raw=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id<$id  and bulletin_id=$bulletin_id order by id desc limit 1"));
	$next=$next_raw['id'];
	$last_raw=tep_db_fetch_array(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id>$id  and bulletin_id=$bulletin_id order by id asc limit 1"));
	$last=$last_raw['id'];
	if(tep_db_num_rows(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id<='$id' and bulletin_id=$bulletin_id"))==1)$turn_html='<a href="javascript:void(0)" onclick="show_link_reply_info('.$last.','.$bulletin_id.')">'.TEXT_LAST.'</a>';
	else if(tep_db_num_rows(tep_db_query("select * from ".TABLE_BULLETIN_BOARD_REPLY." where id>='$id' and bulletin_id=$bulletin_id"))==1){
		$turn_html='<a href="javascript:void(0)" onclick="show_link_reply_info('.$next.','.$bulletin_id.')">'.TEXT_NEXT.'</a>';
	}else{
		$turn_html='<a href="javascript:void(0)" onclick="show_link_reply_info('.$last.','.$bulletin_id.')">'.TEXT_LAST.'</a><a href="javascript:void(0)" onclick="show_link_reply_info('.$next.','.$bulletin_id.')">'.TEXT_NEXT.'</a>';
	}
	 $form_str = tep_draw_form('new_bulletin_board', 'bulletin_board.php','action=update_bulletin_reply&id='.$_POST["id"].'&bulletin_id='.$bulletin_info['bulletin_id'],'post','enctype="multipart/form-data" id="form1" '); 
	 $heading[] = array('align' => 'right', 'text' => $turn_html.'<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
	 $old_content=$bulletin_info['content'];
	 $bulletin_content_table = array();
	 $bulletin_content_row_text[] = array('text'=> TEXT_CONTENT_REPLY);
 	 $bulletin_text_area =  '<textarea style="resize:vertical;  background:#CCCCCC; width:100%;" class="textarea_width"  rows="10" id="current_contents" name="old_content" readonly="readonly">'.$old_content.'</textarea>';
	 $bulletin_content_row_text[] = array('text'=> $bulletin_text_area);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_text);
	 $mark_array = explode(',',$bulletin_info['mark']);
	 $pic_list_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." order by sort_order asc"); 
	 $users_icon = '<ul class="table_img_list" style="width:100%">'; 
	 while ($pic_list_res = tep_db_fetch_array($pic_list_raw)) {
		 $users_icon .= '<li><input type="checkbox" name="pic_icon[]" value="'.$pic_list_res['id'].'"'.(in_array($pic_list_res['id'],$mark_array) ? ' checked="checked"' : '').' style="padding-left:0;margin-left:0;"><img src="images/icon_list/'.$pic_list_res['pic_name'].'" alt="'.$pic_list_res['pic_alt'].'" title="'.$pic_list_res['pic_alt'].'"></li>'; 
   }
	 $users_icon .= '</ul>';

	 $bulletin_content_row_mark = array();
	 $bulletin_content_row_mark[] = array('params'=>'width="20%"','text'=> TEXT_MARK);
	 $bulletin_content_row_mark[] = array('text'=> $users_icon.'<input type="hidden" id="old_mark_str" value="'.$bulletin_info['mark'].'">');
	 $bulletin_content_row_mark[] = array('params'=>'width="10%"','text'=>' ');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_mark);
	
	 $new_bulletin_content_row_text[] = array('text'=> TEXT_CONTENT_REPLY_LAST);
 	$new_bulletin_text_area ='<textarea style="resize:vertical; width:100%;" class="textarea_width" rows="10" id="current_contents" name="content"></textarea>';
	 $new_bulletin_content_row_text[] = array('text'=> $new_bulletin_text_area);
	 $new_bulletin_content_row_text[] = array('params'=>'style="color:#FF0000;"','text'=> '&nbsp;*'.TEXT_MUST_WRITE);
	 $bulletin_content_table[] = array('text'=> $new_bulletin_content_row_text);
	 $file_download_url='';
	 $index=1;
	 $user=$ocertify->auth_user;
	 foreach(explode('|||',$bulletin_info['file_path']) as $value){
		 if($value=='')continue;
		 $file_download_url.='<div id="delete_file_'.$index.'"><a href="'.PATH_BULLETIN_BOARD_UPLOAD.$value.'" style="text-decoration:underline;color:#0000FF;" >'.$value.'</a>';
		 if($user==$bulletin_info['manager']||$ocertify->npermission==31)$file_download_url.='<a href="javascript:void(0)" onclick="delete_file(\'delete_file_'.$index.'\',\''.$value.'\')">&nbsp;X&nbsp</a>';
		 $file_download_url.='</div>';
		$index++;
	 }
	 $bulletin_content_row_download = array();
	 $bulletin_content_row_download[] = array('text'=> TEXT_FILE_DOWNLOAD_URL);
	 $bulletin_content_row_download[] = array('text'=> $file_download_url);
	 if($file_download_url)$bulletin_content_table[] = array('text'=> $bulletin_content_row_download);
	 $bulletin_content_row_addfile = array();
     $bulletin_content_row_addfile[] = array('text'=> TEXT_ADDFILE);
     $bulletin_content_row_addfile[] = array('text'=> '<div id="bulletin_file_boder"><input type="file" id="bulletin_file" name="bulletin_file[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\'bulletin_file\')">'.DELETE_STAFF.'</a>&nbsp;&nbsp;<a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="add_email_file(\'bulletin_file\')">'.BUTTON_ADD_TEXT.'</a></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_addfile);
	 $bulletin_content_row_author=array();
	 $bulletin_content_row_author[] = array('text'=>TEXT_AUTHOR."    ".$bulletin_info['author']);
	 $bulletin_content_row_author[] = array('text'=>TEXT_DONE_TIME.'    '.$bulletin_info['time']);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_author);

	 $bulletin_content_row_update=array();
	 $bulletin_content_row_update[] = array('text'=>TEXT_UPDATE_AUTHOR."    ".$bulletin_info['update_author']);
	 $bulletin_content_row_update[] = array('text'=>TEXT_UPDATE_TIME.'    '.$bulletin_info['update_time']);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_update);
	 if($ocertify->npermission>=15||$user==$bulletin_info['author'])$delete_button_html='<input type="button" value="'.TEXT_RESET.'"onclick="delete_bulletin('.$bulletin_info["id"].',\'show_reply\')">';
	 $bulletin_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="submit" '.(($ocertify->npermission>=15||$user==$bulletin_info['author'])?"":'disabled="disabled"').'  value="'.TEXT_SUBMIT.'">'.$delete_button_html);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_submit);
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($bulletin_content_table); 
	 echo $notice_box->show_notice();
	 break;
	}
}