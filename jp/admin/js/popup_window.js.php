//popup window
var temp_id = '';
function popup_window(ele,type,title,list){ 
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if($(".box_warp").offset()){
    box_warp = $(".box_warp").offset();
    box_warp_top = box_warp.top;
    box_warp_left = box_warp.left;
  }
  var html_str = '';
  var date=new Date();
  var tid = '';
  var ele_next = '';
  var ele_obj = '';
  tid = date.getTime();
  ele_obj = $(ele).offset(); 
  $(ele).attr('id','popup_window_value_'+tid);
  $(ele).next("input:first").attr('id','popup_window_hidden_value_'+tid);
  temp_id = tid;
  var default_value = $("#popup_window_value_"+tid).html();
  default_value = default_value.replace(/<u>/i,"");
  default_value = default_value.replace(/<\/u>/i,"");
  default_value = default_value == '<?php echo TEXT_UNSET_DATA;?>' ? '' : default_value;
  html_str += popup_window_head(title);
  switch(type){
    case 'textarea': 
      html_str += popup_window_body(default_value,popup_window_textarea(default_value,tid),tid);
      break;
    case 'text': 
      html_str += popup_window_body(default_value,popup_window_text(default_value,tid),tid);
      break; 
    case 'radio': 
      html_str += popup_window_body(default_value,popup_window_radio(list,default_value,tid),tid);
      break;
    case 'select': 
      html_str += popup_window_body(default_value,popup_window_select(list,default_value,tid),tid);
      break;
  }
  $("#popup_window").html(html_str);
  $("#popup_window").css('top',ele_obj.top+$(ele).height()-box_warp_top);
  $("#popup_window").css('left',ele_obj.left-box_warp_left); 
  $("#popup_window").show(); 
}

//head html
function popup_window_head(title){
  var html_str = '';
  html_str += '<table width="100%" cellspacing="0" cellpadding="2" border="0" class="popup_window_top">';
  html_str += '<tr>';
  html_str += '<td width="22"><img width="16" height="16" border="0" title=" <?php echo IMAGE_ICON_INFO;?> " alt="<?php echo IMAGE_ICON_INFO;?>" src="images/icon_info.gif">&nbsp;</td>';
  html_str += '<td align="left">'+title+'</td>';
  html_str += '<td align="right"><a onclick="close_window();" href="javascript:void(0);">X</a></td>';
  html_str += '</tr>';
  html_str += '</table>';
  return html_str;
}

//body html
function popup_window_body(default_value,type_html,id){ 
  var html_str = '';
  html_str += '<table width="100%" cellspacing="0" cellpadding="2" border="0" class="popup_window_body">';
  html_str += '<tr><td colspan="2">&nbsp;</td></tr>';
  html_str += '<tr>';
  html_str += '<td width="55"valign="top"><?php echo TEXT_POPUP_WINDOW_SHOW;?></td>';
  html_str += '<td align="left">'+default_value+'</td>';
  html_str += '</tr>';
  html_str += '<tr>';
  html_str += '<td width="55" valign="top"><?php echo TEXT_POPUP_WINDOW_EDIT;?></td>';
  html_str += '<td align="left">'+type_html+'</td>';
  html_str += '</tr>';
  html_str += '<tr>';
  html_str += '<td align="center" colspan="2"><input type="button" value="<?php echo IMAGE_SAVE;?>" onclick="popup_window_save(\''+id+'\');"></td>';
  html_str += '</tr>';  
  html_str += '</table>';
  return html_str;
}

//textarea
function popup_window_textarea(default_value,id){
  var html_str = '';
  html_str += '<textarea cols="30" rows="5" id="default_value_'+id+'">'+default_value+'</textarea>';
  return html_str;
}

//text
function popup_window_text(default_value,id){
  var html_str = '';
  html_str += '<input type="text" value="'+default_value+'" id="default_value_'+id+'">';
  return html_str;
}

//radio
function popup_window_radio(list,default_value,id){
  var html_str = '';
  var selected = '';
  var list_array = new Array();
  list_array = list.split("|||>>>");
  for(x in list_array){
    if(list_array[x] == default_value){
      selected = ' checked="true"';
    }else{
      selected = '';
    }
    if(default_value == '' && x == 0){
      selected = ' checked="true"'; 
    }
    html_str += '<input type="radio" name="radio_value" value="'+list_array[x]+'" onclick="popup_window_default_value(\''+id+'\',this.value);"'+selected+'>&nbsp;'+list_array[x]+'<br>';
  }
  default_value = default_value == '' ? list_array[0] : default_value;
  html_str += '<input type="hidden" id="default_value_'+id+'" value="'+default_value+'">';
  return html_str;
}

//select
function popup_window_select(list,default_value,id){
  var html_str = '';
  var selected = '';
  var list_array = new Array();
  list_array = list.split("|||>>>");
  html_str += '<select onchange="popup_window_default_value(\''+id+'\',this.value);">';
  for(x in list_array){
    if(list_array[x] == default_value){
      selected = ' selected="true"';
    }else{
      selected = '';
    }
    html_str += '<option value="'+list_array[x]+'"'+selected+'>'+list_array[x]+'</option>';
  }
  html_str += '</select>';
  default_value = default_value == '' ? list_array[0] : default_value;
  html_str += '<input type="hidden" id="default_value_'+id+'" value="'+default_value+'">';
  return html_str;
}

//save
function popup_window_save(id){
  var default_value = $("#default_value_"+id).val() == '' ? '<?php echo TEXT_UNSET_DATA;?>' : $("#default_value_"+id).val();
  $("#popup_window_value_"+id).html('<u>'+default_value+'</u>');
  $("#popup_window_hidden_value_"+id).val($("#default_value_"+id).val());
  $("#popup_window").hide();
}

//close
function close_window(){
  $("#popup_window").hide();
}

//default_value
function popup_window_default_value(id,value){
  $("#default_value_"+id).val(value);
}

window.onresize = resizepage;

function resizepage(){
  var ele_obj = '';
  var box_warp = '';
  var box_warp_top = 0;
  var box_warp_left = 0;
  if(temp_id != ''){
    if($(".box_warp").offset()){
      box_warp = $(".box_warp").offset();
      box_warp_top = box_warp.top;
      box_warp_left = box_warp.left;
    }
    ele_obj = $("#popup_window_value_"+temp_id).offset();
    $("#popup_window").css('top',ele_obj.top+$("#popup_window_value_"+temp_id).height()-box_warp_top);
    $("#popup_window").css('left',ele_obj.left-box_warp_left);
  }
}
