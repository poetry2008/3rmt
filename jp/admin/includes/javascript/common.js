//popup position
function note_popup_list(){
   var note_obj = document.getElementById("note_hide_content");
   var tmp_top = document.body.scrollTop | document.documentElement.scrollTop; 
   note_obj.style.top = tmp_top+document.documentElement.clientHeight-$("#note_hide_content").height()+"px"; 
   setTimeout(function(){note_popup_list();},50);
}
//check function whether exists
function check_exists_function(funcName){
  try{
    if(typeof(eval(funcName)) == "function") {
      return true;
    }
  }catch(e){
    return false;
  }
}

//show menu
function showmenu(elmnt)
{
  document.getElementById(elmnt).style.visibility="visible";
}

//hide menu
function hidemenu(elmnt)
{
  document.getElementById(elmnt).style.visibility="hidden";
}

//switch header menu
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

//jump change password page
function goto_changepwd(id, action_page){
  document.getElementById(id).action=action_page;
  document.getElementById(id).submit();
  return false; 
}

