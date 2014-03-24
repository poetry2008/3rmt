//window resize
function resizepage(){
  if($(".note_head").val()== ""&&$("#orders_list_table").width()< 714){
    $(".box_warp").css('height',$('.compatible').height());
  }
}
window.onresize = resizepage;
//submit form
function check_list_order_submit(notice_mail_str) {
  if (submit_confirm()) {
    confrim_list_mail_title(notice_mail_str);
  }
}

//wait hide
function read_time(){
  $("#wait").hide();
}

//current_time equal pay time
function q_3_2(){
  if ($('#q_3_1').attr('checked') == true){
    if ($('#q_3_2_m').val() == '' || $('#q_3_2_m').val() == '') {
      $('#q_3_2_m').val(new Date().getMonth()+1);
      $('#q_3_2_d').val(new Date().getDate());
    }
  }
}

//current_time equal pay time
function q_4_3(){
  if ($('#q_4_2').attr('checked') == true){
    if ($('#q_4_3_m').val() == '' || $('#q_4_3_m').val() == '') {
      $('#q_4_3_m').val(new Date().getMonth()+1);
      $('#q_4_3_d').val(new Date().getDate());
    }
  }
}

//update orders_comment_flag value
function validate_comment(){
  var o_comment = $('textarea|[name=orders_comment]');
  if(o_comment.val()){
    return true;
  }else{
    o_comment_flag = $('input|[name=orders_comment_flag]');
    o_comment_flag.val('true');
    return true;
  }
}

//show manual all content
function manual_show(action, manual_info, show_manual_info){

  switch(action){

  case 'top':
    $("#manual_top_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_top_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'top\', \''+show_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  case 'top_categories':
    $("#manual_top_categories_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_top_categories_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'top_categories\', \''+show_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  case 'categories':
    $("#manual_categories_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_categories_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'categories\', \''+show_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  case 'categories_children':
    $("#manual_categories_children_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_categories_children_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'categories_children\', \''+show_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  case 'products':
    $("#manual_products_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_products_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'products\', \''+show_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  }   
}
//show manual some content
function manual_hide(action, manual_info, hide_manual_info){

  switch(action){

  case 'top':
    $("#manual_top_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_top_all").html('<a href="javascript:void(0);" onclick="manual_show(\'top\', \''+hide_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  case 'top_categories':
    $("#manual_top_categories_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_top_categories_all").html('<a href="javascript:void(0);" onclick="manual_show(\'top_categories\', \''+hide_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  case 'categories':
    $("#manual_categories_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_categories_all").html('<a href="javascript:void(0);" onclick="manual_show(\'categories\', \''+hide_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  case 'categories_children':
    $("#manual_categories_children_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_categories_children_all").html('<a href="javascript:void(0);" onclick="manual_show(\'categories_children\', \''+hide_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  case 'products':
    $("#manual_products_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_products_all").html('<a href="javascript:void(0);" onclick="manual_show(\'products\', \''+hide_manual_info+'\', \''+manual_info+'\');"><u>'+manual_info+'</u></a>');
    break;
  }  
}

//confirm mail title
function confrim_list_mail_title(notice_mail_str){
  var _end = $("#mail_title_status").val();
  var o_id_list = ''; 
  var direct_single = false;
  if (document.sele_act.elements['chk[]']) {
    if (document.sele_act.elements['chk[]'].length == null) {
      if (document.sele_act.elements['chk[]'].checked == true) {
        o_id_list += document.sele_act.elements['chk[]'].value+','; 
      }
    } else {
      for (var i = 0; i < document.sele_act.elements['chk[]'].length; i++) {
        if (document.sele_act.elements['chk[]'][i].checked == true) {
          o_id_list += document.sele_act.elements['chk[]'][i].value+','; 
        }
      }
    }
  }
  if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
  }else{
    if(confirm(notice_mail_str)){
    } else {
      direct_single = true;
    }
  }
  
  $.ajax({
    type:"POST",
    data:"c_comments="+$('#c_comments').val()+'&o_id_list='+o_id_list+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end,
    async:false,
    url:'ajax_orders.php?action=check_order_list_variable_data',
    success: function(msg) {
      if (msg != '') {
        if (direct_single == false) {
          alert(msg); 
        } 
      } else {
        if (direct_single == false) {
          document.forms.sele_act.submit(); 
        }
      } 
    }
  }); 
}

//select or unselect site
function change_site(site_id,flag,site_list,param_url){  
  var ele = document.getElementById("site_"+site_id);
  $.ajax({
    dataType: 'text',
    type:"POST",
    data:'param_url='+param_url+'&flag='+flag+'&site_list='+site_list+'&site_id='+site_id,
    async:false, 
    url: 'ajax_orders.php?action=select_site',
    success: function(data) {
      if (data != '') {
        if (ele.className == 'site_filter_selected') {
          ele.className='';
        } else {
          ele.className='site_filter_selected';
        }
        window.location.href = data; 
     }
   }
  });
}

//add order symbol
function change_read(oid,user,notice_check_flag,notice_uncheck_flag){
  var orders_id = document.getElementById("oid_"+oid); 
  var orders_id_src = orders_id.src;
  var orders_id_src_array = new Array();
  var flag = 0;
  orders_id_src_array = orders_id_src.split("/"); 
  if(orders_id_src_array[orders_id_src_array.length-1] == 'green_right.gif'){
    flag = 1;
  }
  $.ajax({
    type: "POST",
    data: 'oid='+oid+'&user='+user+'&flag='+flag,
    beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
    async:false,
    url: 'ajax_orders.php?action=read_flag',
    success: function(msg) {
      if(flag == 0){
        orders_id.src="images/icons/green_right.gif";
        orders_id.title=notice_check_flag;
        orders_id.alt=notice_check_flag;
      }else{
        orders_id.src="images/icons/gray_right.gif";
        orders_id.title=notice_uncheck_flag;
        orders_id.alt=notice_uncheck_flag;
      }
      $('body').css('cursor','');
      setTimeout('read_time()',500);
    }
  }); 
}

//confirm mail title
function confrim_mail_title(oid_info, notice_mail_str){
  var _end = $("#mail_title_status").val();
  var direct_single = false;
  if(send_mail){
  if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
  }else{
    if(confirm(notice_mail_str)){
    } else {
      direct_single = true;
    }
  }
  
  $.ajax({
    type:"POST",
    data:"c_comments="+$('#c_comments').val()+'&o_id='+oid_info+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end,
    async:false,
    url:'ajax_orders.php?action=check_order_variable_data',
    success: function(msg) {
      if (msg != '') {
        if (direct_single == false) {
          alert(msg); 
        } 
      } else {
        if (direct_single == false) {
          document.forms.sele_act.submit(); 
        }
      } 
    }
  }); 
  }else{
    $.ajax({
      dataType: 'text',
      async:false,
      url: 'ajax_orders.php?action=edit_order_send_mail&oid='+oid_info+'&o_status='+_end,
      success: function(msg) {
        document.getElementById('edit_order_send_mail').innerHTML=msg;
        send_mail = true;
        if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
        }else{
          if(confirm(notice_mail_str)){
          } else {
            direct_single = true;
          }
        }
        
        $.ajax({
          type:"POST",
          data:"c_comments="+$('#c_comments').val()+'&o_id='+oid_info+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end,
          async:false,
          url:'ajax_orders.php?action=check_order_variable_data',
          success: function(msg) {
            if (msg != '') {
              if (direct_single == false) {
                alert(msg); 
              } 
            } else {
              if (direct_single == false) {
                document.forms.sele_act.submit(); 
              }
            } 
          }
        }); 
      }
    });
  }
}

//delete order status
function del_confirm_payment_time(oid, status_id, notice_del_confirm, notice_del_success, notice_pwd, notice_pwd_error, c_permission)
{
  $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
data: 'current_page_name='+tmp_other_str, 
async : false,
success: function(data) {
var tmp_msg_arr = data.split('|||'); 
var pwd_list_array = tmp_msg_arr[1].split(',');
if (c_permission == '31') {
if (window.confirm(notice_del_confirm)) {
$.ajax({
type:"POST", 
url:"handle_payment_time.php",
data:"oID="+oid+"&stid="+status_id, 
success:function(msg) {
alert(notice_del_success); 
window.location.href = window.location.href; 
window.location.reload; 
}
}); 
}
} else {
if (tmp_msg_arr[0] == '0') {
if (window.confirm(notice_del_confirm)) {
$.ajax({
type:"POST", 
url:"handle_payment_time.php",
data:"oID="+oid+"&stid="+status_id, 
success:function(msg) {
alert(notice_del_success); 
window.location.href = window.location.href; 
window.location.reload; 
}
}); 
}
} else {
 var input_pwd_str = window.prompt(notice_pwd, ''); 
 if (in_array(input_pwd_str, pwd_list_array)) {
   $.ajax({
     url: 'ajax_orders.php?action=record_pwd_log',   
     type: 'POST',
     dataType: 'text',
     data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(window.location.href),
     async: false,
     success: function(msg_info) {
      if (window.confirm(notice_del_confirm)) {
      $.ajax({
      type:"POST", 
      url:"handle_payment_time.php",
      data:"oID="+oid+"&stid="+status_id+"&once_pwd="+input_pwd_str, 
      success:function(msg) {
      alert(notice_del_success); 
      window.location.href = window.location.href; 
      window.location.reload; 
      }
      }); 
      }
     }
   }); 
 } else {
   alert(notice_pwd_error); 
 }
}
}
}
});
}

//delete order
function confirm_del_order_info(notice_pwd, notice_pwd_error, c_permission)
{
if (c_permission == '31') {
  document.forms.orders.submit();
} else {
  $.ajax({
     url: 'ajax_orders.php?action=getallpwd',
     type: 'POST',
     dataType: 'text',
     data: 'current_page_name='+tmp_other_str, 
     async : false,
     success: function(data) {
       var tmp_msg_arr = data.split('|||'); 
       var pwd_list_array = tmp_msg_arr[1].split(',');
       if (tmp_msg_arr[0] == '0') {
         document.forms.orders.submit();
       } else {
         var input_pwd_str = window.prompt(notice_pwd, ''); 
         if (in_array(input_pwd_str, pwd_list_array)) {
           $.ajax({
             url: 'ajax_orders.php?action=record_pwd_log',   
             type: 'POST',
             dataType: 'text',
             data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.orders.action),
             async: false,
             success: function(msg_info) {
               document.forms.orders.submit();
             }
           }); 
         } else {
           alert(notice_pwd_error); 
         }
       }
     }
   });
}
}
