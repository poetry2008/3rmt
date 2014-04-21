//add input form
function input_add(){
      
  var cbox_head = "<div class='add_link'>"+js_oroshi_add_text+":<input type='text' name='set_oroshi[]'></div>"; 
  var cbox  = document.getElementById("oo_input").innerHTML;
  cbox = cbox.replace(/ocid/g,'ocid['+i+']');
  html[i] = cbox_head+cbox;
  var html_text='';
  var o;
  if(i>0){
    for(o=0;o<html.length;o++){
      html_text+=html[o];
    }
  }else{
    html_text=html[0]
  }
  document.getElementById("o_input").innerHTML=html_text;
  if(document.getElementById("change_one")){
    document.getElementById("change_one").innerHTML='';
    document.getElementById("orrshi_id").value='';
  }
  i++;
}
//href cleate_list.php
function jump_oroshi_data(id,oid){
  location.href = 'cleate_list.php?cPath='+id+'&cpath='+id+'&oid='+oid;
}
//jump to cleate_oroshi.php
function resset_cb(){
  location.href="cleate_oroshi.php";
}
var valmethod = true;
//update sort
function notval(){
  valmethod = false;
}
//close config   
function w_close(c_permission, co_type){
  if (co_type == 1) {
    valmethod = false;
    document.getElementById("h_sort").innerHTML = '<input type="hidden" name="sort" value="'+js_oroshi_update_sort+'">';
  }
  var o_error = false; 
  if(valmethod){
  if((!document.getElementById("orrshi_id")||document.getElementsByName('set_oroshi[]')[0])&&html.length==1){
    var j;
    var o_cid;
    var test;
    var o_name = document.getElementsByName('set_oroshi[]');
      o_cid = document.getElementsByName('ocid[0][]');
      if(o_name[0].value == null||o_name[0].value == ''){
        o_error = true; 
        alert(js_oroshi_trade_name);
      }else {
        var ex_name =  document.getElementsByName('exist_name[]');
        var z;
        for(z=0;z<ex_name.length;z++){
          if(ex_name[z].value==o_name[0].value){
             o_error = true; 
             alert(o_name[0].value+js_oroshi_already_exists);
          }
        }
        test=0;
        for (j=0 ;j<o_cid.length; j++){
          if(!o_cid[j].checked){
            test++;
          }
        }
        if (test == j) {
          o_error = true; 
          alert(js_oroshi_game_title);
        }
      }
  }else if(document.getElementById("orrshi_id")){
    var o_cid = document.getElementById("orrshi_id").value;
    if(document.getElementById("name_"+o_cid)){
    var o_name = document.getElementById("name_"+o_cid).value;
    var s_name = document.getElementById("src_name_"+o_cid).value;
    var ocid = document.getElementsByName('ocid[]');
    var test = 0;
    if (o_name == ''||o_name == null){
      o_error = true; 
      alert(js_oroshi_trade_name);
    }else{
        var ex_name =  document.getElementsByName('exist_name[]');
        var z;
        for(z=0;z<ex_name.length;z++){
          if(ex_name[z].value==s_name){
            continue;
          }
          if(ex_name[z].value==o_name){
            o_error = true; 
            alert(o_name+js_oroshi_trade_name);
          }
        }
    for(x=0;x<ocid.length;x++){
      if(!ocid[x].checked){
        test++;
      }
    }
    if (test == x){
       o_error = true; 
       alert(js_oroshi_game_title);
    }
    }
  }
  }
          if(html.length>1){
          var o_name = document.getElementsByName('set_oroshi[]');
          var ex_name =  document.getElementsByName('exist_name[]');
          var le;
          var z;
          var set_name_arr = new Array();
          for(le=0;le<o_name.length;le++){
            if(o_name[le].value != null&&o_name[le].value != ''){
               for(z=0;z<ex_name.length;z++){
                if(ex_name[z].value==o_name[le].value){
                  o_error = true; 
                  alert(o_name[le].value+js_oroshi_already_exists);
                }
               }
            }
            set_name_arr[le] = o_name[le].value;
          }
          var nary=set_name_arr.sort();
          for(var ii=1;ii<nary.length;ii++){
            if (nary[ii-1]!=null||nary[ii-1]!=''){
              continue;
            }
            if (nary[ii-1]==nary[ii]){
              o_error = true; 
              alert(js_oroshi_not_same);
            }
          }
        }
  if(!document.getElementsByName('set_oroshi[]')[0]&&!document.getElementById("orrshi_id")){
    o_error = true; 
    alert(js_oroshi_input_box);
  }
  }
  
  if (o_error == false) {
    if (c_permission == 31) {
      document.forms.o_form.submit(); 
    } else {
      $.ajax({
        url: 'ajax_orders.php?action=getallpwd',   
        type: 'POST',
        dataType: 'text',
        data: 'current_page_name='+js_oroshi_self, 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split('|||'); 
          var pwd_list_array = tmp_msg_arr[1].split(',');
          if (tmp_msg_arr[0] == '0') {
            document.forms.o_form.submit(); 
          } else {
            var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.o_form.action),
                async: false,
                success: function(msg_info) {
                  document.forms.o_form.submit(); 
                }
              }); 
            } else {
              alert(js_onetime_error); 
            }
          }
        }
      });
    }
  }
}
//delete wholesaler name
function del_oroshi(id, c_permission){
  var flg=confirm(js_oroshi_delete);
  if(flg){
    if (c_permission == 31) {
      window.location.href=js_oroshi_ws_admin+'cleate_oroshi.php?action=delete&id='+id;
    } else {
      $.ajax({
        url: 'ajax_orders.php?action=getallpwd',   
        type: 'POST',
        dataType: 'text',
        data: 'current_page_name='+js_oroshi_self, 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split('|||'); 
          var pwd_list_array = tmp_msg_arr[1].split(',');
          if (tmp_msg_arr[0] == '0') {
            window.location.href=js_oroshi_ws_admin+'cleate_oroshi.php?action=delete&id='+id;
          } else {
            var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_oroshi_ws_admin+'cleate_oroshi.php?action=delete&id='+id),
                async: false,
                success: function(msg_info) {
                  window.location.href=js_oroshi_ws_admin+'cleate_oroshi.php?action=delete&id='+id;
                }
              }); 
            } else {
              alert(js_onetime_error); 
            }
          }
        }
      });
    }
  }
}
//edit wholesaler name
function edit_oroshi(id){
  var selectName = 'parent_id_'+id;
  var oroName = 'name_'+id;
  location.href= 'cleate_oroshi.php?action=edit_oroshi&id='+id; 

}
//sort up one level
function ex(id){
  for(exi=1;exi<6;exi++){
    tmp = document.getElementById('tr_'+id+'_'+exi).innerHTML;
    document.getElementById('tr_'+id+'_'+exi).innerHTML = document.getElementById('tr_'+(id-1)+'_'+exi).innerHTML;
    document.getElementById('tr_'+(id-1)+'_'+exi).innerHTML = tmp;
  }
  $('#tr_'+id+'_1>.sort_order_input').val(id);
  $('#tr_'+(id-1)+'_1>.sort_order_input').val(id-1);
}
