<?php //弹出位置?>
function note_popup_list(){
   var note_obj = document.getElementById("note_hide_content");
   var tmp_top = document.body.scrollTop | document.documentElement.scrollTop; 
   note_obj.style.top = tmp_top+document.documentElement.clientHeight-$("#note_hide_content").height()+"px"; 
   setTimeout(function(){note_popup_list();},50);
}
<?php //检查存在的功能 ?>
function check_exists_function(funcName){
  try{
    if(typeof(eval(funcName)) == "function") {
      return true;
    }
  }catch(e){
    return false;
  }
}

<?php //显示菜单?>
function showmenu(elmnt)
{
  document.getElementById(elmnt).style.visibility="visible"
}

<?php //隐藏菜单 ?>
function hidemenu(elmnt)
{
  document.getElementById(elmnt).style.visibility="hidden"
}

<?php //切换标题菜单 ?>
function toggle_header_menu(elmnt)
{
  if (document.getElementById(elmnt).style.visibility == 'visible') {
    document.getElementById(elmnt).style.visibility="hidden";
  } else {
    document.getElementById(elmnt).style.visibility="visible";

    switch (elmnt) {
      case 'tutorials':
        document.getElementById('headerorder').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        break;
      case 'managermenu':
        document.getElementById('headerorder').style.visibility="hidden";
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        break; 
      case 'redirecturl':
        document.getElementById('headerorder').style.visibility="hidden";
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        break;
      case 'headerorder':
        document.getElementById('tutorials').style.visibility="hidden";
        document.getElementById('redirecturl').style.visibility="hidden";
        document.getElementById('managermenu').style.visibility="hidden";
        break;
    }
  }
}

<?php //跳转到更改密码 ?>
function goto_changepwd(id){
  document.getElementById(id).action="<?php echo FILENAME_CHANGEPWD;?>";
  document.getElementById(id).submit();
  return false; 
}

