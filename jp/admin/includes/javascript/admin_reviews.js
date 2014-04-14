$(function() {
       function format(group) {
           return group.name;
       }
       $("#keyword").autocomplete('ajax_create_order.php?action=search_product_name&site_id='+js_site_id, {
                      multipleSeparator: '',
                      dataType: "json",
                      parse: function(data) {
                      return $.map(data, function(row) {
                      return {
                             data: row,
                             value: row.name,
                             result: row.name
                             }
                      });
                      },
                      formatItem: function(item) {
                          return format(item);
                        }
                      }).result(function(e, item) {
                      });
    });
    function all_select_review(review_str){
        var check_flag = document.del_review.all_check.checked;
          if (document.del_review.elements[review_str]) {
           if (document.del_review.elements[review_str].length == null){
             if (check_flag == true) {
                  document.del_review.elements[review_str].checked = true;
             } else {
                  document.del_review.elements[review_str].checked = false;
             }
            } else {
                for (i = 0; i < document.del_review.elements[review_str].length; i++){
                  if(!document.del_review.elements[review_str][i].disabled){
                   if (check_flag == true) {
                       document.del_review.elements[review_str][i].checked = true;
                    } else {
                       document.del_review.elements[review_str][i].checked = false;
                    }
                  }
                }
             }
           }
         }
   function delete_select_review(review_str, c_permission){
      sel_num = 0;
      if (document.del_review.elements[review_str].length == null) {
          if (document.del_review.elements[review_str].checked == true) {
              sel_num = 1;
          }
       } else {
          for (i = 0; i < document.del_review.elements[review_str].length; i++) {
                  if (document.del_review.elements[review_str][i].checked == true) {
                      sel_num = 1;
                      break;
                   }
           }
       }
       if (sel_num == 1) {
           if (confirm(js_del_review)) {
             if (c_permission == 31) {
               document.forms.del_review.submit(); 
             } else {
               $.ajax({
                 url: 'ajax_orders.php?action=getallpwd',   
                 type: 'POST',
                 dataType: 'text',
                 data: 'current_page_name='+js_reviews_self, 
                 async: false,
                 success: function(msg) {
                   var tmp_msg_arr = msg.split('|||'); 
                   var pwd_list_array = tmp_msg_arr[1].split(',');
                   if (tmp_msg_arr[0] == '0') {
                     document.forms.del_review.submit(); 
                   } else {
                     var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                     if (in_array(input_pwd_str, pwd_list_array)) {
                       $.ajax({
                         url: 'ajax_orders.php?action=record_pwd_log',   
                         type: 'POST',
                         dataType: 'text',
                         data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.del_review.action),
                         async: false,
                         success: function(msg_info) {
                           document.forms.del_review.submit(); 
                         }
                       }); 
                     } else {
                       document.getElementsByName('reviews_action')[0].value = 0; 
                       alert(js_onetime_error); 
                     }
                   }
                 }
               });
             }
           } else {
              document.getElementsByName('reviews_action')[0].value = 0; 
           }
        } else {
            document.getElementsByName('reviews_action')[0].value = 0; 
            alert(js_review_must_select); 
           }
    }
    function change_hidden_select(ele){
       set_rpid(ele);
       document.getElementById("hidden_select").value=ele.options[ele.selectedIndex].value;
    }
    function change_review_products_id(ele,page,rID,site_id){
      set_rcid(ele);
      var review_products_id_info = document.getElementById('review_products_id').value;
      var site_id_name = document.getElementById('site_id').value;
      site_id = site_id_name;
      var show_site_id = 0;
      if(document.getElementById('add_site_id')){
        show_site_id = document.getElementById('add_site_id').value;
      }
      refresh(rID,page,review_products_id_info,site_id,show_site_id);
     }
    function refresh(rID,page,review_products_id_info,site_id,show_site_id){
         var product_name = document.getElementById('keyword').value;
         var con_cname = $('#customers_name').val();
         var con_text = $('#reviews_text').val();
         set_default_value();
         $.ajax({
               url: "ajax.php?&action=edit_reviews&validate=true",
               data: {rID:rID,page:page,review_products_id_info:review_products_id_info,site_id:site_id,product_name:product_name,add_site_id:show_site_id},
               async:false,
               success: function(data){
                  $("#show_text_reviews").html(data);
                }
            });
        $('#customers_name').val(con_cname);
        $('#reviews_text').val(con_text);
     
    }
function check_review_submit(rID,page){
          var show_site_id = 0;
          if(document.getElementById('add_site_id')){
            show_site_id = document.getElementById('add_site_id').value;
          }
          var site_id = document.getElementById('site_id').value;
          var add_id = document.getElementById('add_product_products_id').value;
          var customers_name = document.getElementById('customers_name').value;
          var product_name = document.getElementById('keyword').value;
          var con_cname = $('#customers_name').val();
          var con_text = $('#reviews_text').val();
          set_default_value();
          $.ajax({
               url: "ajax.php?&action=edit_reviews&validate=true",
               data: {rID:rID,page:page,site_id:site_id,add_id:add_id,customers_name:customers_name,product_name:product_name,add_site_id:show_site_id},
               async:false,
               success: function(data){
                  $("#show_text_reviews").html(data);
                }
            });
         $('#customers_name').val(con_cname);
         $('#reviews_text').val(con_text);
       //  if(document.getElementById('back_sort').value!=''&&document.getElementById('back_sort_type').value!=''){
         //  document.forms.review.action = document.forms.review.action+'&r_sort='+document.getElementById('back_sort').value+'&r_sort_type='+document.getElementById('back_sort_type').value;
        // }
         if(rID!=''){
           document.forms.review.action = document.forms.review.action+'&rID='+rID;
         }

         if (document.getElementById('add_product_products_id').value != 0) {
           if (document.getElementById('reviews_text').value.length < js_review_min_length) {
             alert(js_notice_totalnum_error);
           } else {
             if (js_reviews_npermission == 31) {
               document.forms.review.submit();
             } else {
              $.ajax({
                url: 'ajax_orders.php?action=getallpwd',   
                type: 'POST',
                dataType: 'text',
                data: 'current_page_name='+js_reviews_self, 
                async: false,
                success: function(msg) {
                   var tmp_msg_arr = msg.split('|||'); 
                   var pwd_list_array = tmp_msg_arr[1].split(',');
                   if (tmp_msg_arr[0] == '0') {
                     document.forms.review.submit();
                   } else {
                     $('#button_save').attr('id', 'tmp_button_save'); 
                     var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                     if (in_array(input_pwd_str, pwd_list_array)) {
                       $.ajax({
                         url: 'ajax_orders.php?action=record_pwd_log',   
                         type: 'POST',
                         dataType: 'text',
                         data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.review.action),
                         async: false,
                         success: function(msg_info) {
                           document.forms.review.submit();
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
    }
 function check_review(){
        if (document.getElementById('reviews_text').value.length < js_review_min_length) {
            alert(js_notice_totalnum_error);
            return false;
        } else {
            return true;
        }
    }
$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc
      if ($('#show_text_reviews').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           //ENTER
        if ($('#show_text_reviews').css('display') != 'none') {
               $("#button_save").trigger("click");
            }
        }

     if (event.ctrlKey && event.which == 37) {
      //Ctrl+left
      if ($('#show_text_reviews').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right
      if ($('#show_text_reviews').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

function show_text_reviews(ele,page,rID,site_id,action_sid,sort_name,sort_type){
 var product_name = document.getElementById('keyword').value;
 $.ajax({
 url: 'ajax.php?&action=edit_reviews',
 data: {page:page,rID:rID,site_id:site_id,product_name:product_name,action_sid:action_sid,r_sort:sort_name,r_sort_type:sort_type} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_text_reviews").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(rID != 0){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_text_reviews').height()){
offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_text_reviews').height()) > $('.box_warp').height())&&($('#show_text_reviews').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_text_reviews').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_text_reviews').height()) > $('.box_warp').height())&&($('#show_text_reviews').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_text_reviews').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_text_reviews').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_text_reviews').height()) > $('.box_warp').height())&&($('#show_text_reviews').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_text_reviews').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_text_reviews').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_text_reviews').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(rID == 0){
  var show_text_list = $('#show_text_list').offset();
$('#show_text_reviews').css('top',show_text_list.top);
}
$('#show_text_reviews').css('z-index','1');
$('#show_text_reviews').css('left',leftset);
$('#show_text_reviews').css('display', 'block');
word_count();
  }
  }); 
}
function word_count(){
      document.getElementById('count_box').innerHTML = document.getElementById('reviews_text').value.length;
}
function hidden_info_box(){
$('#show_text_reviews').css('display','none');
}
function set_rstatus(_this){
  $("#r_status").val(_this.value);
}
function set_rating(_this){
  $("#r_rating").val(_this.value);
}
function set_ryear(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_year").val(value);
}
function set_rmonth(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_month").val(value);
}
function set_rday(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_day").val(value);
}
function set_rhour(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_hour").val(value);
}
function set_rminute(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_minute").val(value);
}
function set_rcid(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_cid").val(value);
  $("#r_pid").val('0');
}
function set_rpid(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_pid").val(value);
  if(value){
    $("#p_error").css('display','none');
  }
}
function set_default_value(){
  var df_status = $("#r_status").val();
  var df_rating = $("#r_rating").val();
  var df_year = $("#r_year").val();
  var df_m = $("#r_month").val();
  var df_d = $("#r_day").val();
  var df_h = $("#r_hour").val();
  var df_i = $("#r_minute").val();
  var df_cid = $("#r_cid").val();
  var df_pid = $("#r_pid").val();
  $.ajax({
    url: "ajax.php?&action=edit_reviews&default_value=save",
    data:{df_status:df_status,df_rating:df_rating,df_year:df_year,df_m:df_m,df_d:df_d,df_h:df_h,df_i:df_i,df_cid:df_cid,df_pid:df_pid},
    async:false,
    success: function(data){
    }
  });
}
//choose action
function review_change_action(r_value, r_str) {
  if (r_value == '1') {
    delete_select_review(r_str, js_reviews_npermission);
  }
}
//action herf
function toggle_reviews_action(reviews_url_str) 
{
    if (js_reviews_npermission == 31) {
  window.location.href = reviews_url_str;  
    } else {
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_reviews_self, 
    async: false,
    success: function(msg) {
       var tmp_msg_arr = msg.split('|||'); 
       var pwd_list_array = tmp_msg_arr[1].split(',');
       if (tmp_msg_arr[0] == '0') {
         window.location.href = reviews_url_str;  
       } else {
         if ($('#button_save')) {
           $('#button_save').attr('id', 'tmp_button_save'); 
         }
         var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
         if (in_array(input_pwd_str, pwd_list_array)) {
           $.ajax({
             url: 'ajax_orders.php?action=record_pwd_log',   
             type: 'POST',
             dataType: 'text',
             data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(reviews_url_str),
             async: false,
             success: function(msg_info) {
               window.location.href = reviews_url_str;  
             }
           }); 
         } else {
           alert(js_onetime_error); 
           if ($('#tmp_button_save')) {
             setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1); 
           }
         }
       }
     }
  });
    }
}

//delete action url
function delete_reviews_action(reviews_url_str)
{
    if (js_reviews_npermission == 31) {
  if (confirm(js_del_review)) {
    window.location.href = reviews_url_str;
  }
    } else {
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',
    type: 'POST',
    dataType: 'text',
    data: 'current_page_name='+js_reviews_self,
    async: false,
    success: function(msg) {
       var tmp_msg_arr = msg.split('|||');
       var pwd_list_array = tmp_msg_arr[1].split(',');
       if (confirm(js_del_review)) {
         if (tmp_msg_arr[0] == '0') {
           window.location.href = reviews_url_str;
         } else {
           if ($('#button_save')) {
             $('#button_save').attr('id', 'tmp_button_save');
           }
           var input_pwd_str = window.prompt(js_onetime_pwd, '');
           if (in_array(input_pwd_str, pwd_list_array)) {
             $.ajax({
               url: 'ajax_orders.php?action=record_pwd_log',
               type: 'POST',
               dataType: 'text',
               data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(reviews_url_str),
               async: false,
               success: function(msg_info) {
                 window.location.href = reviews_url_str;
               }
             });
           } else {
             alert(js_onetime_error);
             if ($('#tmp_button_save')) {
               setTimeOut($('#tmp_button_save').attr('id', 'button_save'), 1);
             }
           }
         }
       }    
         }
  });
    }
}
