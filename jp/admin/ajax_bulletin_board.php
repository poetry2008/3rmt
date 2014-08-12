<?php
  require('includes/application_top.php');
if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']){
		/*----------------------------------------------
		  case 'chang_collect_status' 更改收藏状态
		  case 'new_bulletin' 显示新建bulletin的页面
		  case 'create_bulletin' 生成bulletin
		  --------------------------------------------*/
		case 'change_collect_status':
			$id=$_POST['id'];
			$collect=$_POST['collect'];
			$sql="update bulletin_board set collect=$collect where id=$id";
			if(!tep_db_query($sql))print "0".mysql_error()."sql:$sql";
			break;
	case 'new_bulletin':
	 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.'bulletin_board.php');
	 include(DIR_FS_ADMIN.'classes/notice_box.php');
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $page_str = '<a onclick="hidden_info_box('.($_GET['bulletin_sta'] == 'drafts' && $_GET['latest_bulletin_id'] > 0 ? '1' : ($_GET['latest_bulletin_id'] < 0 ? '2' : '3')).');" href="javascript:void(0);">X</a>';
	$heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	$heading[] = array('text' => TEXT_CREATE_BULLETIN);
	$form_str = tep_draw_form('new_bulletin_board', 'ajax_bulletin_board.php','action=create_bulletin','post','enctype="multipart/form-data" id="form1" '); 
	 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);
 
	 $bulletin_content_table = array();
	 $bulletin_content_row_from = array();
	 $bulletin_content_row_from[] = array('params'=>'width="20%"','text'=>TEXT_TITLE);
	 $bulletin_content_row_from[] = array('params'=>'width="100%" style="color:#FF0000;"','text'=>'<input type="text" name="title" id="bulletin_title" size=100>*');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_from);
	 $bulletin_content_row_manager = array();
	 $bulletin_content_row_manager [] = array('text'=>TEXT_MANAGER);
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
	 $bulletin_content_row_allow [] =array('text'=>'TO');
	 $bulletin_content_row_allow [] =array('text'=>'<table width="100%"><tr><td><input type="radio" value="all" onclick="select_allow(this,0)" name="select_all" id="select_all_radio">ALL</td><td><input type="radio" values="group" name="select_group" id="select_group_radio"  onclick="select_allow(this,1)">'.TEXT_GROUP_SELECT.'</td><td><input type="radio" value="id" onclick="select_allow(this,2)" name="select_id" id="select_id_radio">'.TEXT_SELECT_ID.'</td></tr></table>');
	 $bulletin_content_row_allow [] =array('text'=>'');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_allow);
	 $users_list_html='';
	 foreach($user_name as $name){
		 $users_list_html.='<div value='.$name.' onclick="checkbox_event(this,event)" style="cursor: pointer; -moz-user-select: none; background: none repeat scroll 0% 0% rgb(255, 255, 255); color: black;">
			 <input type="checkbox" hidden="" name="all_staff" value="'.$name.'">
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
		<td align="center" width="45%">'.TEXT_STAFF.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="user_add" width="100%" style="overflow-y:scroll;height:105px;"></div></td>
		<td align="center" style="vertical-align:middle;">
			<button type="button" onclick="add_select_user(1)">&lt&lt'.ADD_STAFF.'</button><br>
			<button type="button" onclick="delete_select_user(1)">'.DELETE_STAFF.'&gt&gt</button>
		</td>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div width="100%" id="user_delete_to" style="overflow-y:scroll;height:105px;">'.$users_list_html.'</div></td>
	</tr>
</table></div>
<div width="100%" id="select_group" style="display:none;"><table width="100%">
	<tr>
		<td align="center" width="45%">'.TEXT_TO_BODY.'</td>
		<td align="center" width="10%"></td>
		<td align="center" width="45%">'.TEXT_STAFF.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="group_add" width="100%" style="overflow-y:scroll;height:105px;"></div></td>
		<td align="center" style="vertical-align:middle;">
			<button type="button" onclick="add_select_user(0)">&lt&lt'.ADD_STAFF.'</button><br>
			<button type="button" onclick="delete_select_user(0)">'.DELETE_STAFF.'&gt&gt</button>
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
 if($_GET['latest_bulletin_id']>0){
	$sql_message_content = tep_db_query('select * from bulletin where id = "'.$_GET['latest_bulletin_id'].'"');
	$sql_message_content_res = tep_db_fetch_array($sql_message_content);
        $bulletin_text_area = '<textarea id="bulletin_text" style="overflow-y:hidden;width:100%;height:163px;" disabled="disabled" name="contents">'.$sql_message_content_res['content'].'</textarea><input type="hidden" name="drafts_contents" value="'.$sql_message_content_res['content'].'">';
 }else{
 	$bulletin_text_area =  '<textarea style="resize:vertical; width:100%;" class="textarea_width" rows="10" id="current_contents" name="content"></textarea>';
 }
	 $bulletin_content_row_text[] = array('text'=> $bulletin_text_area);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_text);
	 $bulletin_content_row_must_write = array();
	 $bulletin_content_row_must_write[] = array('text'=> '');
	 $bulletin_content_row_must_write[] = array('text'=> '<div id="bulletin_must_write" style="display: none;"><span style="color:#ff0000;">'.CONTENT_MUST_WRITE.'</span></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_must_write);
	 $bulletin_content_row_addfile = array();
     $bulletin_content_row_addfile[] = array('text'=> TEXT_ADDFILE);
     $bulletin_content_row_addfile[] = array('text'=> '<div id="bulletin_file_boder"><input type="file" id="bulletin_file" name="bulletin_file[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\'bulletin_file\')">'.DELETE_STAFF.'</a>&nbsp;&nbsp;<a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="add_email_file(\'bulletin_file\')">'.BUTTON_ADD_TEXT.'</a></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_addfile);
	 $bulletin_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="reset" value="'.TEXT_RESET.'"><input type="submit"   value="'.TEXT_SUBMIT.'">'.$bulletin_buttons);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_submit);
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($bulletin_content_table); 
	 echo $notice_box->show_notice();
	 break;

	case 'create_bulletin':
	 $id_raw=tep_db_query("select id from bulletin_board ");
	 while($id_row=tep_db_fetch_array($id_raw)){
		 if($id<$id_row['id'])$id=$id_row['id'];
	 }
	 $id+=1;
	 $author=$ocertify->auth_user;
	 $content=$_POST['content'];
	 $collect=0;
	 $allow="";
	 if($_POST['select_all'])$allow="ALL";
	 if($_POST['select_group'])$allow="group:";
	 if($_POST['select_id'])$allow="id:";
	foreach($_POST['selected_staff'] as $value){
		if(strlen($allow)>6)$allow.=",".$value;
		else $allow.=$value;
	}
	 $manager=$_POST['manager'];
	 $mark="";
	 foreach($_POST['pic_icon'] as $icon){
		 if(strlen($mark)){
			 $mark.=",".$icon;
		 }else{
			 $mark.=$icon;
		 }
	 }
	 $reply_number=0;
	 $title=$_POST['title'];
	 $file_path="";
	 $index=0;
	 foreach($_FILES['bulletin_file']['name'] as $fk => $fv){
		 $file_name=explode('.',$_FILES['bulletin_file']['name'][$fk]);
		 $file_name=$file_name[1];
		 $file_name=str_replace("/[\w]+\.$/",".",$file_name);
		 if(strlen($file_path)!=0)$file_path.="|||";
		 $file_name=date("Ymdhisa_").$author.".".$file_name;
		 $file_path.=$file_name;
	   	 move_uploaded_file($_FILES['bulletin_file']["tmp_name"][$fk],"upload/bulletin_board/".$file_name); 
	 }
	 $bulletin_sql="insert into bulletin_board values($id,'$author','$content',now(),'$allow','$manager','$mark',$collect,$reply_number,now(),'$title','$file_path')";
		tep_db_query($bulletin_sql);
	 break;



	//修改bulletin
	case 'edit_bulletin':
	 include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.$language.'/'.'bulletin_board.php');
	 include(DIR_FS_ADMIN.'classes/notice_box.php');
	 $notice_box = new notice_box('popup_order_title', 'popup_order_info');
	 $page_str = '<a onclick="hidden_info_box('.($_GET['bulletin_sta'] == 'drafts' && $_GET['latest_bulletin_id'] > 0 ? '1' : ($_GET['latest_bulletin_id'] < 0 ? '2' : '3')).');" href="javascript:void(0);">X</a>';
	$heading[] = array('params' => 'width="22"', 'text' => '<img width="16" height="16" alt="'.IMAGE_ICON_INFO.'" src="images/icon_info.gif">');
	$heading[] = array('text' => TEXT_CREATE_BULLETIN);
	$form_str = tep_draw_form('new_bulletin_board', 'ajax_bulletin_board.php','action=create_bulletin','post','enctype="multipart/form-data" id="form1" '); 
	 $heading[] = array('align' => 'right', 'text' => '<span id="next_prev"></span>&nbsp&nbsp'.$page_str);

     //bulletin infomation
	 $bulletin_id=$_POST['bulletin_id'];
	 $bulletin_sql="select * from bulletin_board where id=$bulletin_id";
	 $bulletin_raw=tep_db_query($bulletin_sql);
	 $bulletin_info=tep_db_fetch_array($bulletin_raw);


	 $bulletin_content_table = array();
	 $bulletin_content_row_from = array();
	 $bulletin_content_row_from[] = array('params'=>'width="20%"','text'=>TEXT_TITLE);
	 $bulletin_content_row_from[] = array('params'=>'width="100%" style="color:#FF0000;"','text'=>'<input type="text" name="title" value="'.$bulletin_info["title"].'" id="bulletin_title" size=100>*');
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
	 $bulletin_content_row_allow [] =array('text'=>'<table width="100%"><tr><td><input type="radio" value="all" onclick="select_allow(this,0)" name="select_all" id="select_all_radio">ALL</td><td><input type="radio" values="group" name="select_group" id="select_group_radio"  onclick="select_allow(this,1)">'.TEXT_GROUP_SELECT.'</td><td><input type="radio" value="id" onclick="select_allow(this,2)" name="select_id" id="select_id_radio">'.TEXT_SELECT_ID.'</td></tr></table>');
	 $bulletin_content_row_allow [] =array('text'=>'');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_allow);
	 $users_list_html='';
	 $users_select_html='';
	 foreach($user_name as $name){
		 $users_list_html.='<div value='.$name.' onclick="checkbox_event(this,event)" style="cursor: pointer; -moz-user-select: none; background: none repeat scroll 0% 0% rgb(255, 255, 255); color: black;">
			 <input type="checkbox" hidden="" name="all_staff" value="'.$name.'">
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
		<td align="center" width="45%">'.TEXT_STAFF.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="user_add" width="100%" style="overflow-y:scroll;height:105px;"></div></td>
		<td align="center" style="vertical-align:middle;">
			<button type="button" onclick="add_select_user(1)">&lt&lt'.ADD_STAFF.'</button><br>
			<button type="button" onclick="delete_select_user(1)">'.DELETE_STAFF.'&gt&gt</button>
		</td>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div width="100%" id="user_delete_to" style="overflow-y:scroll;height:105px;">'.$users_list_html.'</div></td>
	</tr>
</table></div>
<div width="100%" id="select_group" style="display:none;"><table width="100%">
	<tr>
		<td align="center" width="45%">'.TEXT_TO_BODY.'</td>
		<td align="center" width="10%"></td>
		<td align="center" width="45%">'.TEXT_STAFF.'</td>
	</tr>
	<tr>
		<td style="background:#FFF;border:1px #E0E0E0 solid;"><div id="group_add" width="100%" style="overflow-y:scroll;height:105px;"></div></td>
		<td align="center" style="vertical-align:middle;">
			<button type="button" onclick="add_select_user(0)">&lt&lt'.ADD_STAFF.'</button><br>
			<button type="button" onclick="delete_select_user(0)">'.DELETE_STAFF.'&gt&gt</button>
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
 	$bulletin_text_area =  '<textarea style="resize:vertical; width:100%;" class="textarea_width" rows="10" id="current_contents" name="content">'.$bulletin_info["content"].'</textarea>';
	 $bulletin_content_row_text[] = array('text'=> $bulletin_text_area);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_text);
	 $bulletin_content_row_must_write = array();
	 $bulletin_content_row_must_write[] = array('text'=> '');
	 $bulletin_content_row_must_write[] = array('text'=> '<div id="bulletin_must_write" style="display: none;"><span style="color:#ff0000;">'.CONTENT_MUST_WRITE.'</span></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_must_write);
	 $bulletin_content_row_addfile = array();
     $bulletin_content_row_addfile[] = array('text'=> TEXT_ADDFILE);
     $bulletin_content_row_addfile[] = array('text'=> '<div id="bulletin_file_boder"><input type="file" id="bulletin_file" name="bulletin_file[]"><a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="file_cancel(\'bulletin_file\')">'.DELETE_STAFF.'</a>&nbsp;&nbsp;<a style="color:#0000FF;text-decoration:underline;" href="javascript:void(0)" onclick="add_email_file(\'bulletin_file\')">'.BUTTON_ADD_TEXT.'</a></div>');
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_addfile);
	 $bulletin_content_row_submit[] = array('params' => 'colspan="2" align="center"','text'=> '<input type="reset" value="'.TEXT_RESET.'"><input type="submit"   value="'.TEXT_SUBMIT.'">'.$bulletin_buttons);
	 $bulletin_content_table[] = array('text'=> $bulletin_content_row_submit);
	 $notice_box->get_heading($heading);
	 $notice_box->get_form($form_str);
	 $notice_box->get_contents($bulletin_content_table); 
	 echo $notice_box->show_notice();
	 break;


	}
}
