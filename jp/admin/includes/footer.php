<?php
/*
   $Id$
 */

?>
<?php echo tep_draw_form('changepwd', FILENAME_CHANGEPWD,'','post','
    id=\'changepwd_form\'');
echo tep_draw_hidden_field("execute_password",TEXT_ECECUTE_PASSWORD_USER);
echo tep_draw_hidden_field("userslist",$ocertify->auth_user);
echo "</form>";
?>
<script language="JavaScript" type="text/javascript">
function note_popup_list(){
   var note_obj = document.getElementById("note_hide_content");
   var tmp_top = document.body.scrollTop | document.documentElement.scrollTop; 
   note_obj.style.top = tmp_top+document.documentElement.clientHeight-$("#note_hide_content").height()+"px"; 
   setTimeout(function(){note_popup_list();},50);
}
$(function() {
  note_popup_list();
});
</script>
<script type="text/javascript" src="includes/javascript/split_page.js"></script>
<script type="text/javascript">
<?php //更改新的URL?>
function redirect_new_url(new_object)
{
  var url_str = $(new_object).parent().attr('href');
  window.location.href = url_str;
}
</script>
<?php
if($_SESSION['user_permission'] == 15 ){
  ?>
  <table class="bottom_content" border="0" cellpadding="0" cellspacing="0">
  <tr> 
  <?php 
    $sql_check = "select * from ".TABLE_PWD_CHECK." where
    page_name='".$_SERVER['PHP_SELF']."'";
    $query_check = tep_db_query($sql_check);
    $arr_check = array();
    while($row_check = tep_db_fetch_array($query_check)){
      $arr_check[] = $row_check['check_value'];
    }
    if(empty($arr_check)){
  ?>
  <td width="80%"><input type='checkbox' name='pwd_check' value='staff' class="bottom_input"></td><td><span>Staff&nbsp;</span></td>
  <td><input type='checkbox' name='pwd_check' value='chief' class="bottom_input"></td><td><span>Chief&nbsp;</span></td>
  <td><input type='checkbox' name='pwd_check' value='admin' checked="checked" class="bottom_input"></td><td><span>Admin&nbsp;</span></td>
  <td><input type='checkbox' name='pwd_check' value='onetime' class="bottom_input"></td><td nowrap><div class="bottom_text"><?php echo 
    TEXT_FOOTER_ONE_TIME;?>&nbsp;</div></td>
  <td><input type='button' onclick="save_once_pwd_checkbox()" value="<?php echo
    PRIVILEGE_SET_TEXT;?>" class="bottom_input_button"></td>
  <?php }else{ 
    if(in_array('staff',$arr_check)){
      echo "<td width='80%'><input type='checkbox' name='pwd_check' value='staff' checked='checked'
        class='bottom_input'></td><td><span>Staff&nbsp;</span></td>";
    }else{
      echo "<td width='80%'><input type='checkbox' name='pwd_check' value='staff' class='bottom_input'
        ></td><td><span>Staff&nbsp;</span></td>";
    }
    if(in_array('chief',$arr_check)){
      echo "<td><input type='checkbox' name='pwd_check' value='chief' class='bottom_input'
        checked='checked'></td><td><span>Chief&nbsp;</span></td>";
    }else{
      echo "<td><input type='checkbox' name='pwd_check' value='chief' class='bottom_input'
        ></td><td><span>Chief&nbsp;</span></td>";
    }
    if(in_array('admin',$arr_check)){
      echo "<td><input type='checkbox' name='pwd_check' value='admin' class='bottom_input'
        checked='checked'></td><td><span>Admin&nbsp;</span></td>";
    }else{
      echo "<td><input type='checkbox' name='pwd_check' value='admin' class='bottom_input'
        ></td><td><span>Admin&nbsp;</span></td>";
    }
    if(in_array('onetime',$arr_check)){
      echo "<td><input type='checkbox' name='pwd_check' value='onetime' class='bottom_input'
        checked='checked'></td><td nowrap><div class='bottom_text'>".TEXT_FOOTER_ONE_TIME."&nbsp;</div></td>";
    }else{
      echo "<td><input type='checkbox' name='pwd_check' value='onetime' class='bottom_input'
        ></td><td nowrap><span>".TEXT_FOOTER_ONE_TIME."&nbsp;</span></td>";
    }
  echo "<td><input type='button' class='bottom_input_button' onclick='save_once_pwd_checkbox()'
    value='".PRIVILEGE_SET_TEXT."'></td>";
  }
  ?>
  </tr> 
  </table>
  <script language='javascript' >
  <?php //保存密码复选框 ?>
    function save_once_pwd_checkbox(){
      var check_str = '';
      $("input|[name=pwd_check]").each(function(){
          if($(this).attr('checked')){
            check_str += $(this).val()+',';
          }
      });
      check_str = check_str.substring(0, check_str.lastIndexOf(','));
      $.ajax({
        url: 'ajax_orders.php?action=pwd_check_save',
        data: 'check_str='+check_str+'&page_name=<?php echo $_SERVER['PHP_SELF'];?>',
        type: 'POST',
        dataType: 'text',
        async : false,
        success: function(data) {
          if(data == "noall"){
            alert("<?php echo TEXT_ONE_TIME_CONFIRM;?>");
          }else if(data == "noadmin"){
            alert("<?php echo TEXT_ONE_TIME_ADMIN_CONFIRM;?>");
          }else if(data == "true"){
            alert("<?php echo TEXT_ONE_TIME_CONFIG_SAVE;?>");
          }else{
            alert("<?php echo TEXT_ONE_TIME_ERROR;?>");
          }
        }
      });
    }
  </script>
    <?
}
echo '<div class="footer_copyright">';
echo sprintf(TEXT_SITE_COPYRIGHT,date('Y'));
echo '</div>';
$page_name = $_SERVER['PHP_SELF'];
if($_SESSION['last_page']!= $page_name){
    unset($_SESSION[$_SESSION['last_page']]);
    $_SESSION['last_page'] = $page_name;
}


// 显示SQL执行记录
if (STORE_DB_TRANSACTIONS == 'true' && false) {?>
<?php
//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log
?>

  <div id="debug_info">
    <pre>
    <?php if(isset($logger)){
      foreach ($logger->queries as $qk => $qv) {
        echo '[' . $logger->times[$qk] . ']' . $qk . "\t=>\t" . $qv."\n";
      }
    }
  print_r($_SESSION);
  ?>
    </pre>
    </div>
    <?php }?>
<div style="position:absolute;right:0;z-index:20000;" id="note_hide_content">
<?php
if($mode_flag){
  $note_hide_query = tep_db_query("select * from notes where (belong='".$belong."' or belong='".$mode_belong_value."') and (attribute='1' or (attribute='0' and author='".$ocertify->auth_user."')) and is_show = '0' order by id desc");
}else{
  $note_hide_query = tep_db_query("select * from notes where belong='".$belong."' and (attribute='1' or (attribute='0' and author='".$ocertify->auth_user."')) and is_show = '0' order by id desc");
}
echo '<ul class="note_hide_list">'; 
while ($note_hide_list = tep_db_fetch_array($note_hide_query)) {
  echo '<li>';
  echo '<a href="javascript:void(0);" onclick="note_revert_window(this, \''.$note_hide_list['id'].'\');"><img src="images/icons/note_'.$note_hide_list['color'].'_window.gif" title="'.$note_hide_list['title'].'" alt="'.$note_hide_list['title'].'"></a>'; 
  echo '</li>';
}
echo '</ul>'; 
?>
</div>
