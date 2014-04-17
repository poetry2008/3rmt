function check_guest(guest_value){
  if(guest_value == 1){
    $("#password_hide").hide(); 
    $("#reset_flag_hide").hide();
    $("#point_hide").hide();
  }else{
    $("#password_hide").show(); 
    $("#reset_flag_hide").show();
    $("#point_hide").show();
  }
  check_is_active =  $("#check_is_active").val();
  if(check_is_active == 1 && guest_value == 1){
    document.getElementById("check_is_active").value = 0;
  }else{
    document.getElementById("check_is_active").value = 1;
  }
}
function check_password(value, c_permission){
 post_email = $("#customers_email_address").val();
 post_site =  $("#customers_site_id").val();
 once_again_password = $("#once_again_password").val();
 check_is_active = $("#check_is_active").val();
 password = $("#password").val();
 customers_email_address_value = $("#customers_email_address_value").val();
 if(customers_email_address_value != post_email){
 $.ajax({
 url: 'ajax.php?action=check_email',
 data: {post_email:post_email,post_site:post_site,password:password,once_again_password:once_again_password} ,
 type: 'POST',
 dataType: 'text',
 async : false,
 success: function(data){
   data_info = data.split(",");
   if(data_info[1] == 1){
     email_error = 'true';
   }else{
     email_error = 'false';
   }
   if(data_info[0] == 1){
     check_email = 'true';
   }else{
     check_email = 'false';
   }
   if(data_info[2] == 1){
     error_password = 'true';
   }else{
     error_password = 'false';
   }
   }
 });
 }else{
 var email_error = 'false';
 var check_email = 'false';
 var error_password = 'false';
 }
 customers_firstname = $("#customers_firstname").val();
 customers_lastname = $("#customers_lastname").val();
 
 var check_error = '';
if(check_is_active == 1){ 
 if(password == '' && once_again_password == ''){
   $("#error_info_o").html(js_customers_error_null); 
   $("#error_info_f").html(js_customers_error_null); 
   check_error = 'true';
 }else{
   $("#error_info_o").html(""); 
   $("#error_info_f").html(""); 
 }
}
 if(customers_firstname == ''){
    $("#customers_firstname_error").html(js_customers_error_null);   
    check_error = 'true';
 }else if(js_customers_strlen_firstname < customers_firstname.length){
    $("#customers_firstname_error").html(js_customers_sprintf_firstname);   
    check_error = 'true';
 }else{
    $("#customers_firstname_error").html("");   
 }
 if(customers_lastname == ''){
    $("#customers_lastname_error").html(js_customers_error_null);
    check_error = 'true';
 }else if(js_customers_strlen_lastname < customers_lastname.length){
    $("#customers_lastname_error").html(js_customers_sprintf_lastname);
    check_error = 'true';
 }else{
    $("#customers_lastname_error").html("");
 }
 if(email_error == 'true' && post_email != ''){
    $("#error_email").html(js_customers_error_email);
    check_error = 'true';
  }else{
    $("#error_email").html("");
  }
 if(check_email == 'true' && post_email != ''){
    $("#check_email").html(js_customers_email_address);
    check_error = 'true';
 }else{
    $("#check_email").html("");
 }
 if(post_email == ''){
   $("#error_email_info").html(js_customers_error_null);    
   check_error = 'true';
 }else{
   $("#error_email_info").html("");    
 }
 if(check_is_active == 1 && password != once_again_password){
    $("#error_info_o").html(js_customers_error_info); 
    document.getElementById("password").value = ""; 
    document.getElementById("once_again_password").value = ""; 
    check_error = 'true';
  }else if(error_password == 'true'){
    $("#error_info_o").html(data_info[3]); 
    document.getElementById("password").value = ""; 
    document.getElementById("once_again_password").value = ""; 
    check_error = 'true';
  }else if(password == ''){
    $("#error_info_o").html(js_customers_error_info); 
  }else{
    $("#error_info").html(""); 
  }
  if(value == 1){
   document.getElementById('check_order').value = 1;
  }else if(value == 0){
   document.getElementById('check_order').value = 0;
  }
  if(check_error != 'true'){
    if (c_permission == 31) {
      document.forms.customers.submit();  
    } else {
      $.ajax({
        url: 'ajax_orders.php?action=getallpwd',   
        type: 'POST',
        dataType: 'text',
        data: 'current_page_name='+js_customers_self, 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split('|||'); 
          var pwd_list_array = tmp_msg_arr[1].split(',');
          if (tmp_msg_arr[0] == '0') {
            document.forms.customers.submit();  
          } else {
            $('#button_save').attr('id', 'tmp_button_save'); 
            var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.customers.action),
                async: false,
                success: function(msg_info) {
                  document.forms.customers.submit();  
                }
              }); 
            } else {
              alert(js_onetime_error); 
              setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
            }
          }
        }
      });
    }
  }

}
function all_select_customers(customers_str){
      var check_flag = document.del_customers.all_check.checked;
          if (document.del_customers.elements[customers_str]) {
            if (document.del_customers.elements[customers_str].length == null){
                if (check_flag == true) {
                  document.del_customers.elements[customers_str].checked = true;
                 } else {
                  document.del_customers.elements[customers_str].checked = false;
                 }
                } else {
            for (i = 0; i < document.del_customers.elements[customers_str].length; i++){
                       if (!document.del_customers.elements[customers_str][i].disabled) { 
                         if (check_flag == true) {
                             document.del_customers.elements[customers_str][i].checked = true;
                         } else {
                             document.del_customers.elements[customers_str][i].checked = false;
                         }
                       }
                       }
                   }
             }
}
function delete_select_customers(customers_str, c_permission){
     sel_num = 0;
     if (document.del_customers.elements[customers_str].length == null) {
         if (document.del_customers.elements[customers_str].checked == true){
               sel_num = 1;
            }
         } else {
         for (i = 0; i < document.del_customers.elements[customers_str].length; i++) {
             if(document.del_customers.elements[customers_str][i].checked == true) {
                   sel_num = 1;
                   break;
                  }
               }
         }
       if (sel_num == 1) {
       //judge checked customers have orders or preorders
         var customers_id_list = '';
         var customers_id_list_all = '';
         for (i = 0; i < document.del_customers.elements[customers_str].length; i++) {
           if(document.del_customers.elements[customers_str][i].checked == true) {
             if(i < document.del_customers.elements[customers_str].length-1){
               customers_id_list += document.del_customers.elements[customers_str][i].value+',';
             }else{
               customers_id_list += document.del_customers.elements[customers_str][i].value; 
             }
           }
           if(i < document.del_customers.elements[customers_str].length-1){
             customers_id_list_all += document.del_customers.elements[customers_str][i].value+',';
           }else{
             customers_id_list_all += document.del_customers.elements[customers_str][i].value; 
           }
         }
         var customers_site_str = 'customers_site_id_list[]';
         var customers_site_id_list = '';
         for (i = 0; i < document.del_customers.elements[customers_site_str].length; i++) {
           if(i < document.del_customers.elements[customers_site_str].length-1){
             customers_site_id_list += document.del_customers.elements[customers_site_str][i].value+',';
           }else{
             customers_site_id_list += document.del_customers.elements[customers_site_str][i].value; 
           }
         }
         var customers_id_flag = false;
         var customers_id_confirm_str = '';
         $.ajax({
              url: 'ajax.php?&action=check_customers',   
              type: 'POST',
              dataType: 'text',
              data: 'customers_id_list='+customers_id_list+'&customers_site_id_list='+customers_site_id_list+'&customers_id_list_all='+customers_id_list_all, 
              async: false,
              success: function(msg) {
                if(msg != ''){
                  customers_id_flag = true;
                  customers_id_confirm_str = msg;
                }
              }
         });

         var customers_id_confirm_flag = false;
         if(customers_id_flag == true){

           if(confirm(js_customers_delete_confirm_info+"\n"+customers_id_confirm_str)){

             if(confirm(js_customers_del_news)){

               customers_id_confirm_flag = true;
             }
           }
         }else{

           if(confirm(js_customers_del_news)){

             customers_id_confirm_flag = true;
           } 
         }
         if (customers_id_confirm_flag) {
           if (c_permission == 31) {
             document.forms.del_customers.submit(); 
           } else {
             $.ajax({
              url: 'ajax_orders.php?action=getallpwd',   
              type: 'POST',
              dataType: 'text',
              data: 'current_page_name='+js_customers_self, 
              async: false,
              success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                  document.forms.del_customers.submit(); 
                } else {
                  var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_customers.action),
                      async: false,
                      success: function(msg_info) {
                        document.forms.del_customers.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName('customers_action')[0].value = 0;
                    alert(js_onetime_error); 
                  }
                }
              }
            });
           }
          }else{
             document.getElementsByName('customers_action')[0].value = 0;
          }
         } else {
            document.getElementsByName('customers_action')[0].value = 0;
             alert(js_customers_must_select); 
          }
}
//choose action
function customers_change_action(r_value, r_str) {
if (r_value == '1') {
   delete_select_customers(r_str, js_customers_npermission);
   }
}
$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_customers').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           //ENTER
        if ($('#show_customers').css('display') != 'none') {
            if (o_submit_single){
                cid = $("#cid").val();
                $("#button_save").trigger("click");
             }
            }
        }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left
      if ($('#show_customers').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right
      if ($('#show_customers').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

function show_customers(ele,cID,page,action_sid,sort_name,sort_type){
 site_id = js_customers_site_id;
 var search = $('#search').val();
 var type = js_customers_type;
 $.ajax({
 url: 'ajax.php?&action=edit_customers',
 data: {cID:cID,page:page,site_id:site_id,search:search,action_sid:action_sid,customers_sort:sort_name,customers_sort_type:sort_type,type:type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_customers").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(cID != -1){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_customers').height()){
offset = ele.offsetTop+$("#show_customers_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_customers').height()) > $('.box_warp').height())&&($('#show_customers').height()<ele.offsetTop+parseInt(head_top)-$("#show_customers_list").position().top-1)) {
offset = ele.offsetTop+$("#show_customers_list").position().top-1-$('#show_customers').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_customers_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_customers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_customers').height()) > $('.box_warp').height())&&($('#show_customers').height()<ele.offsetTop+parseInt(head_top)-$("#show_customers_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_customers_list").position().top-1-$('#show_customers').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_customers_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_customers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_customers').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_customers').height()) > $('.box_warp').height())&&($('#show_customers').height()<ele.offsetTop+parseInt(head_top)-$("#show_customers_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_customers_list").position().top-1-$('#show_customers').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_customers_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_customers_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_customers_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_customers').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_customers').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(cID == -1){
  $('#show_customers').css('top',$('#show_customers_list').offset().top);
}
$('#show_customers').css('z-index','1');
$('#show_customers').css('left',leftset);
$('#show_customers').css('display', 'block');
o_submit_single = true;
  }
  }); 
}
function hidden_info_box(){
   $('#show_customers').css('display','none');
}
function check_radio_status(r_ele)
{
  var s_radio_value = $("#s_radio").val(); 
  var n_radio_value = $(r_ele).val(); 
  
  if (s_radio_value == n_radio_value) {
    $(".table_img_list input[type='radio']").each(function(){
      $(this).attr("checked", false); 
    });
    $("#s_radio").val(''); 
  } else {
    $("#s_radio").val(n_radio_value); 
  } 
}
//execute action
function toggle_customers_action(c_url_str, c_permission)
{
  if (c_permission == 31) {
    window.location.href = c_url_str; 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_customers_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          window.location.href = c_url_str; 
        } else {
          $('#button_save').attr('id', 'tmp_button_save'); 
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(c_url_str),
              async: false,
              success: function(msg_info) {
                window.location.href = c_url_str; 
              }
            }); 
          } else {
            alert(js_onetime_error); 
            setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
          }
        }
      }
    });
  }
}
