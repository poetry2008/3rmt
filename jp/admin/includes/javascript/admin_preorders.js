$(document).ready(function(){ 
  if($(".dataTableContent").find('input|[type=checkbox][checked]').length!=0){
    if(document.sele_act.elements["chk[]"]){
      document.getElementsByName("all_chk")[0].checked = false;
      for(i = 0; i < document.sele_act.elements["chk[]"].length; i++){
        document.sele_act.elements["chk[]"][i].checked = false;
        var tr_id = 'tr_' + document.sele_act.elements["chk[]"][i].value;
        if(document.getElementById(tr_id).className != 'dataTableRowSelected'){
          document.getElementById(tr_id).style.backgroundColor = "";
        }
      }
    }
  }
});
//delete preorders
function confirm_del_preorder_info()
{
if (js_preorders_npermission == 31) {
  document.forms.preorders.submit();
} else {
  $.ajax({
     url: 'ajax_orders.php?action=getallpwd',
     type: 'POST',
     dataType: 'text',
     data: 'current_page_name='+js_preorders_self, 
     async : false,
     success: function(data) {
       var tmp_msg_arr = data.split('|||'); 
       var pwd_list_array = tmp_msg_arr[1].split(',');
       if (tmp_msg_arr[0] == '0') {
         document.forms.preorders.submit();
       } else {
         var input_pwd_str = window.prompt(js_preorders_pwd, ''); 
         if (in_array(input_pwd_str, pwd_list_array)) {
           $.ajax({
             url: 'ajax_orders.php?action=record_pwd_log',   
             type: 'POST',
             dataType: 'text',
             data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.preorders.action),
             async: false,
             success: function(msg_info) {
               document.forms.preorders.submit();
             }
           }); 
         } else {
           alert(js_preorders_error); 
         }
       }
     }
   });
}
}
  //select site or not
  function change_site(site_id,flag,site_list,param_url){  
          var ele = document.getElementById("site_"+site_id);
          $.ajax({
                  dataType: 'text',
                  type:"POST",
                  data:'param_url='+param_url+'&flag='+flag+'&site_list='+site_list+'&site_id='+site_id,
                  async:false, 
                  url: 'ajax_preorders.php?action=select_site',
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
  //wait element hide 
  function read_time(){
    
    $("#wait").hide();
  }
  //tag preorders
  function change_read(oid,user){
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
                  url: 'ajax_preorders.php?action=read_flag',
                  success: function(msg) {
                    if(flag == 0){
                      orders_id.src="images/icons/green_right.gif";
                      orders_id.title=js_preorders_flag_checked;
                      orders_id.alt=js_preorders_flag_checked;
                    }else{
                      orders_id.src="images/icons/gray_right.gif";
                      orders_id.title=js_preorders_flag_uncheck;
                      orders_id.alt=js_preorders_flag_uncheck;
                    }
                    $('body').css('cursor','');
                    setTimeout('read_time()',500);
                  }
               }); 
  }
function del_confirm_payment_time(oid, status_id)
{
  $.ajax({
    url: 'ajax_preorders.php?action=getallpwd',
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_preorders_self, 
    async : false,
    success: function(data) {
      var tmp_msg_arr = data.split('|||'); 
      var pwd_list_array = tmp_msg_arr[1].split(',');
      if (js_preorders_npermission == 31) {
      if (window.confirm(js_preorders_payment_time)) {
        $.ajax({
          type:"POST", 
          url:js_preorders_payment_time_href,
          data:"oID="+oid+"&stid="+status_id, 
          async : false,
          success:function(msg) {
            alert(js_preorders_payment_time_success); 
            window.location.href = window.location.href; 
            window.location.reload; 
          }
        }); 
      }
      } else {
       if (tmp_msg_arr[0] == '0') {
         if (window.confirm(js_preorders_payment_time)) {
          $.ajax({
            type:"POST", 
            url:js_preorders_payment_time_href,
            data:"oID="+oid+"&stid="+status_id, 
            async : false,
            success:function(msg) {
              alert(js_preorders_payment_time_success); 
              window.location.href = window.location.href; 
              window.location.reload; 
            }
          }); 
         }
       } else {
         var input_pwd_str = window.prompt(js_preorders_pwd, ''); 
         if (in_array(input_pwd_str, pwd_list_array)) {
           $.ajax({
             url: 'ajax_orders.php?action=record_pwd_log',   
             type: 'POST',
             dataType: 'text',
             data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(window.location.href),
             async: false,
             success: function(msg_info) {
             if (window.confirm(js_preorders_payment_time)) {
              $.ajax({
                type:"POST", 
                url:js_preorders_payment_time_href,
                data:"oID="+oid+"&stid="+status_id+"&once_pwd="+input_pwd_str, 
                success:function(msg) {
                  alert(js_preorders_payment_time_success); 
                  window.location.href = window.location.href; 
                  window.location.reload; 
                }
              }); 
             }
             }
           }); 
         } else {
           alert(js_preorders_error); 
         }
       }
      }
    }
  });
}
//check send mail status
function check_mail_product_status(pid)
{
   var _end = $("#s_status").val();
   var direct_single = false; 
   if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
   }else{
     if(confirm(js_preorders_title_changed)){
     }else{
       direct_single = true; 
     }
   }

   $.ajax({
    type:"POST",
    data:"c_comments="+$('#c_comments').val()+'&o_id='+pid+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end,
    async:false,
    url:'ajax_preorders.php?action=check_preorder_variable_data',
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
//check null
function check_mail_list_product_status() {
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
  
  $.ajax({
    type:"POST",
    data:"c_comments="+$('#c_comments').val()+'&o_id_list='+o_id_list+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end,
    async:false,
    url:'ajax_preorders.php?action=check_preorder_list_variable_data',
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
//submit form
function check_list_preorder_submit() {
  if (submit_confirm()) {
    check_mail_list_product_status();
  }
}
if(js_preorders_action){
$(function() {
   left_show_height = $('#orders_list_table').height();
   right_show_height = $('#rightinfo').height();
   
   if (right_show_height < left_show_height) {
     $('#rightinfo').css('height', left_show_height);  
   }
});
function resizeRightInfo() {
   left_show_height = $('#orders_list_table').height();
   right_show_height = $('#rightinfo').height();
   
   if (right_show_height <= left_show_height) {
     $('#rightinfo').css('height', left_show_height);  
   }
}
function showRightInfo() {
   left_show_height = $('#orders_list_table').height();
   $('#rightinfo').css('height', left_show_height);  
}
$(window).resize(function() {
  showRightInfo();
});
}
