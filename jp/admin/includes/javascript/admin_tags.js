$(document).ready(function() {
  if(document.getElementsByName("select_edit_tags")[0]){
    document.getElementsByName("select_edit_tags")[0].value = 0;
  }
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if ($('#show_popup_info').css('display') != 'none') {
        close_tags_info(); 
      }
    }
    if (event.which == 13) {
      //ENTER 
      if ($('#show_popup_info').css('display') != 'none') {  
        if($("#show_popup_info").find('input:submit').first().val()){
          $("#button_save").trigger("click"); 
        }else{
          $("#button_save").trigger("click"); 
        }
      } 
    }
    if (event.ctrlKey && event.which == 37) {
      //Ctrl+left 
      if ($('#show_popup_info').css('display') != 'none') {
        if ($("#tags_prev")) {
          $("#tags_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right 
      if ($('#show_popup_info').css('display') != 'none') {
        if ($("#tags_next")) {
          $("#tags_next").trigger("click");
        }
      } 
    }
  });    
});
window.onresize = resize_option_page;
function resize_option_page()
{
  if ($(".box_warp").height() < $(".compatible").height()) {
    $(".box_warp").height($(".compatible").height()); 
  }
}

//sort item from item manage page
function change_sort_tags(sort_type)
{
  url = 'tags.php?'+js_preg_sort+'&sort=' +sort_type;
  window.location.href = url;
}

//forward manage page according different methods to do different action
function select_type_changed_products(value)
{
  switch(value){
 
  case '1':
    setting_products_to_tags('tags_list_id[]');
    break;
  case '2': 
    delete_select_products_to_tags('tags_list_id[]');
    break;
  }
}

//forward manage page item of tags, check the checkbox
function setting_products_to_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  if (sel_num == 1) {
    document.edit_tags.action = js_filename_tags+'?action=setting_products_to_tags';
    document.edit_tags.submit(); 
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    document.getElementsByName("select_edit_tags")[1].value = 0;
    alert(js_text_tags_must_select); 
  }
}

//delete confirm with cross products
function delete_select_products_to_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm(js_delete_tags_confirm)) {
      if (js_npermission == 31) {
      document.edit_tags.action = js_select_products_to_tags_href;
      document.edit_tags.submit(); 
      } else {
      $.ajax({
        url: 'ajax_orders.php?action=getallpwd',   
        type: 'POST',
        dataType: 'text',
        data: 'current_page_name='+js_tags_self, 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split('|||'); 
          var pwd_list_array = tmp_msg_arr[1].split(',');
          if (tmp_msg_arr[0] == '0') {
            document.edit_tags.action = js_select_products_to_tags_href;
            document.edit_tags.submit(); 
          } else {
            $("#button_save").attr('id', 'tmp_button_save');
            var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_select_products_to_tags_href),
                async: false,
                success: function(msg_info) {
                  document.edit_tags.action = js_select_products_to_tags_href;
                  document.edit_tags.submit(); 
                }
              }); 
            } else {
              alert(js_onetime_error); 
              setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
            }
          }
        }
      });
      }
    }else{

      document.getElementsByName("select_edit_tags")[0].value = 0;
      document.getElementsByName("select_edit_tags")[1].value = 0;
    } 
  }else{
    document.getElementsByName("select_edit_tags")[0].value = 0;
    document.getElementsByName("select_edit_tags")[1].value = 0;
    alert(js_text_tags_must_select); 
  }
}
//when submit check the data integrity
function create_tags_submit(r_type){

  var error = false;
  var tags_name = document.getElementsByName("tags_name")[0];
  var tags_name_value = tags_name.value;
  tags_name_value = tags_name_value.replace(/\s/g,"");
  if(tags_name_value == ''){

    error = true; 
    $("#tags_name_error").html('&nbsp;<font color="#FF0000">'+js_text_tags_must_input+'</font>');
  }else{
    if(!document.getElementsByName("tags_id")[0]){
      var tags_images = document.getElementsByName("tags_images")[0];
      tags_images = tags_images.value;
      tags_images = tags_images.split('\\');
      tags_images = tags_images.pop();
      if(tags_images != ''){
      $.ajax({
        url: 'ajax.php?action=check_file_exists',      
        data: 'table='+js_table_tags+'&field=tags_images&dir=tags/&file='+tags_images,
        type: 'POST',
        dataType: 'text',
        async:false,
        success: function (data) {
          if(parseInt(data) > 0){
 
            if(confirm(js_check_file_exists)){

              error = false; 
            }else{
              error = true; 
            }   
          }else{
            error = false; 
          }
        }
      }); 
      }else{
        error = false; 
      }
    }
  }

  if (error == false) {
    if (r_type == 1) {
      return true; 
    }
    if (js_npermission == 31) {
      document.tags_form.submit(); 
    } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_tags_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.tags_form.submit(); 
        } else {
          $("#button_save").attr('id', 'tmp_button_save');
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.tags_form.action),
              async: false,
              success: function(msg_info) {
                document.tags_form.submit(); 
              }
            }); 
          } else {
            alert(js_onetime_error); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
          }
        }
      }
    });
    }
  } else {
    return false; 
  }
}
//handle submit type 
function edit_tags_submit(action){
  var otag_single = false;
  if(action == 'deleteconfirm'){
    if(confirm(js_info_delete_intro)){
      var tags_images_id = $("#tags_images_id").val();
      tags_images = tags_images_id;
      $.ajax({
        url: 'ajax.php?action=check_file_exists',      
        data: 'table='+js_table_tags+'&field=tags_images&dir=tags/&file='+tags_images,
        type: 'POST',
        dataType: 'text',
        async:false,
        success: function (data) {
          if(parseInt(data) > 1){
            if(confirm(js_check_file_delete)){

              document.tags_form.action = js_all_href_filename_tags+'?action='+action;
              if (js_npermission == 31) {
              document.tags_form.submit();
              } else {
              $.ajax({
                url: 'ajax_orders.php?action=getallpwd',   
                type: 'POST',
                dataType: 'text',
                data: 'current_page_name='+js_tags_self, 
                async: false,
                success: function(msg) {
                  var tmp_msg_arr = msg.split('|||'); 
                  var pwd_list_array = tmp_msg_arr[1].split(',');
                  if (tmp_msg_arr[0] == '0') {
                    document.tags_form.submit();
                  } else {
                    $("#button_save").attr('id', 'tmp_button_save');
                    var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                    if (in_array(input_pwd_str, pwd_list_array)) {
                      $.ajax({
                        url: 'ajax_orders.php?action=record_pwd_log',   
                        type: 'POST',
                        dataType: 'text',
                        data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.tags_form.action),
                        async: false,
                        success: function(msg_info) {
                          document.tags_form.submit();
                        }
                      }); 
                    } else {
                      alert(js_onetime_error); 
                      setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
                    }
                  }
                }
              });
              }
            } 
          }else{
            otag_single = true;
            document.tags_form.action = js_all_href_filename_tags+'?action='+action;
          }
        }
      }); 
    }
  }else{
    if(create_tags_submit(1)){
      var tags_images = document.getElementsByName("tags_images")[0];
      var delete_image = '';
      if(document.getElementsByName("delete_image")[0]){
        delete_image = document.getElementsByName("delete_image")[0];
        delete_image = delete_image.checked;
      }
      var tags_images_id = $("#tags_images_id").val();
      tags_images = tags_images.value;
      tags_images = tags_images.split('\\');
      tags_images = tags_images.pop();
      if(delete_image == true){tags_images = tags_images_id;}
      if(tags_images != '' || delete_image == true){
      $.ajax({
        url: 'ajax.php?action=check_file_exists',      
        data: 'table='+js_table_tags+'&field=tags_images&dir=tags/&file='+tags_images,
        type: 'POST',
        dataType: 'text',
        async:false,
        success: function (data) {
          if(parseInt(data) > 0){

           if(delete_image == false){
            if(confirm(js_check_file_exists)){
              document.tags_form.action = js_all_href_filename_tags+'?action='+action;
              if (js_npermission == 31) {
              document.tags_form.submit();
              } else {
              $.ajax({
                url: 'ajax_orders.php?action=getallpwd',   
                type: 'POST',
                dataType: 'text',
                data: 'current_page_name='+js_tags_self, 
                async: false,
                success: function(msg) {
                  var tmp_msg_arr = msg.split('|||'); 
                  var pwd_list_array = tmp_msg_arr[1].split(',');
                  if (tmp_msg_arr[0] == '0') {
                    document.tags_form.submit();
                  } else {
                    $("#button_save").attr('id', 'tmp_button_save');
                    var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                    if (in_array(input_pwd_str, pwd_list_array)) {
                      $.ajax({
                        url: 'ajax_orders.php?action=record_pwd_log',   
                        type: 'POST',
                        dataType: 'text',
                        data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.tags_form.action),
                        async: false,
                        success: function(msg_info) {
                          document.tags_form.submit();
                        }
                      }); 
                    } else {
                      alert(js_onetime_error); 
                      setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
                    }
                  }
                }
              });
              }
            }
           }else{
            if(parseInt(data) > 1){
            if(confirm(js_check_file_delete)){
              document.tags_form.action = js_all_href_filename_tags+'?action='+action;
              if (js_npermission == 31) {
              document.tags_form.submit();
              } else {
              $.ajax({
                url: 'ajax_orders.php?action=getallpwd',   
                type: 'POST',
                dataType: 'text',
                data: 'current_page_name='+js_tags_self, 
                async: false,
                success: function(msg) {
                  var tmp_msg_arr = msg.split('|||'); 
                  var pwd_list_array = tmp_msg_arr[1].split(',');
                  if (tmp_msg_arr[0] == '0') {
                    document.tags_form.submit();
                  } else {
                    $("#button_save").attr('id', 'tmp_button_save');
                    var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                    if (in_array(input_pwd_str, pwd_list_array)) {
                      $.ajax({
                        url: 'ajax_orders.php?action=record_pwd_log',   
                        type: 'POST',
                        dataType: 'text',
                        data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.tags_form.action),
                        async: false,
                        success: function(msg_info) {
                          document.tags_form.submit();
                        }
                      }); 
                    } else {
                      alert(js_onetime_error); 
                      setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
                    }
                  }
                }
              });
              }
            }  
            }else{
              otag_single = true;
              document.tags_form.action = js_all_href_filename_tags+'?action='+action;
            }
           }
          }else{
            otag_single = true;
            document.tags_form.action = js_all_href_filename_tags+'?action='+action;
          }
        }
      }); 
      }else{
        otag_single = true;
        document.tags_form.action = js_all_href_filename_tags+'?action='+action;
      }
    }
  }
  if (otag_single == true) {
    if (js_npermission == 31) {
    document.tags_form.submit();
    } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_tags_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.tags_form.submit();
        } else {
          $("#button_save").attr('id', 'tmp_button_save');
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.tags_form.action),
              async: false,
              success: function(msg_info) {
                document.tags_form.submit();
              }
            }); 
          } else {
            alert(js_onetime_error); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
          }
        }
      }
    });
    }
  }
}
//checkbox action
function all_select_tags(tags_list_id)
{
  var check_flag = document.edit_tags.all_check.checked;
  if (document.edit_tags.elements[tags_list_id]) {
    if (document.edit_tags.elements[tags_list_id].length == null) {
      if (check_flag == true) {
        document.edit_tags.elements[tags_list_id].checked = true;
      } else {
        document.edit_tags.elements[tags_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
        if (check_flag == true) {
          document.edit_tags.elements[tags_list_id][i].checked = true;
        } else {
          document.edit_tags.elements[tags_list_id][i].checked = false;
        }
      }
    }
  }
}

//when delect tag and relevance, confirm of checkbox selected
function delete_select_tags(tags_list_id, c_permission)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    if (confirm(js_tags_delete_confirm)) {
      var tags_images = '';
      var tags_images_flag = false;
      for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
        if (document.edit_tags.elements[tags_list_id][i].checked == true) {

          if(document.edit_tags.elements["tags_list_images[]"][i].value != ''){
            tags_images = document.edit_tags.elements["tags_list_images[]"][i].value;
            $.ajax({
            url: 'ajax.php?action=check_file_exists',      
            data: 'table='+js_table_tags+'&field=tags_images&dir=tags/&file='+tags_images,
            type: 'POST',
            dataType: 'text',
            async:false,
            success: function (data) {
            if(parseInt(data) > 1){
              tags_images_flag = true;
            }
          }
          });
          }
        }
        if(tags_images_flag == true){break;}
      } 
      if(tags_images_flag == true){

        if(confirm(js_check_file_delete)){
           if (c_permission == 31) {
             document.edit_tags.action = js_all_href_filename_tags+'?action=delete_tags';
             document.edit_tags.submit(); 
           } else {
             $.ajax({
               url: 'ajax_orders.php?action=getallpwd',   
               type: 'POST',
               dataType: 'text',
               data: 'current_page_name='+js_tags_self, 
               async: false,
               success: function(msg) {
                var tmp_msg_arr = msg.split('|||'); 
                var pwd_list_array = tmp_msg_arr[1].split(',');
                if (tmp_msg_arr[0] == '0') {
                  document.edit_tags.action = js_all_href_filename_tags+'?action=delete_tags';
                  document.edit_tags.submit(); 
                } else {
                  $("#button_save").attr('id', 'tmp_button_save');
                  var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    $.ajax({
                      url: 'ajax_orders.php?action=record_pwd_log',   
                      type: 'POST',
                      dataType: 'text',
                      data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_all_href_filename_tags+'?action=delete_tags'),
                      async: false,
                      success: function(msg_info) {
                        document.edit_tags.action = js_all_href_filename_tags+'?action=delete_tags';
                        document.edit_tags.submit(); 
                      }
                    }); 
                  } else {
                    document.getElementsByName("select_edit_tags")[0].value = 0; 
                    alert(js_onetime_error); 
                    setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
                  }
                }
               }
             });
           }
        }
      }else{
        if (c_permission == 31) {
          document.edit_tags.action = js_all_href_filename_tags+'?action=delete_tags';
          document.edit_tags.submit(); 
        } else {
          $.ajax({
            url: 'ajax_orders.php?action=getallpwd',   
            type: 'POST',
            dataType: 'text',
            data: 'current_page_name='+js_tags_self, 
            async: false,
            success: function(msg) {
              var tmp_msg_arr = msg.split('|||'); 
              var pwd_list_array = tmp_msg_arr[1].split(',');
              if (tmp_msg_arr[0] == '0') {
                document.edit_tags.action = js_all_href_filename_tags+'?action=delete_tags';
                document.edit_tags.submit(); 
              } else {
                $("#button_save").attr('id', 'tmp_button_save');
                var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
                if (in_array(input_pwd_str, pwd_list_array)) {
                  $.ajax({
                    url: 'ajax_orders.php?action=record_pwd_log',   
                    type: 'POST',
                    dataType: 'text',
                    data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_all_href_filename_tags+'?action=delete_tags'),
                    async: false,
                    success: function(msg_info) {
                      document.edit_tags.action = js_all_href_filename_tags+'?action=delete_tags';
                      document.edit_tags.submit(); 
                    }
                  }); 
                } else {
                  document.getElementsByName("select_edit_tags")[0].value = 0; 
                  alert(js_onetime_error); 
                  setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
                }
              }
            }
          });
        }
      } 
    }else{
      document.getElementsByName("select_edit_tags")[0].value = 0; 
    }
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    alert(js_text_tags_must_select); 
  }
}
//when delete tag relevance, check the confirm of checkbox
function delete_select_products_tags(tags_list_id, c_permission)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    if (confirm(js_tags_products_delete_confirm)) {
      if (c_permission == 31) {
        document.edit_tags.action = js_all_href_filename_tags+'?action=delete_products_tags';
        document.edit_tags.submit(); 
      } else {
        $.ajax({
          url: 'ajax_orders.php?action=getallpwd',   
          type: 'POST',
          dataType: 'text',
          data: 'current_page_name='+js_tags_self, 
          async: false,
          success: function(msg) {
            var tmp_msg_arr = msg.split('|||'); 
            var pwd_list_array = tmp_msg_arr[1].split(',');
            if (tmp_msg_arr[0] == '0') {
              document.edit_tags.action = js_all_href_filename_tags+'?action=delete_products_tags';
              document.edit_tags.submit(); 
            } else {
              $("#button_save").attr('id', 'tmp_button_save');
              var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(js_all_href_filename_tags+'?action=delete_products_tags'),
                  async: false,
                  success: function(msg_info) {
                    document.edit_tags.action = js_all_href_filename_tags+'?action=delete_products_tags';
                    document.edit_tags.submit(); 
                  }
                }); 
              } else {
                document.getElementsByName("select_edit_tags")[0].value = 0;
                alert(js_onetime_error); 
                setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
              }
            }
          }
        });
      }
    }else{

      document.getElementsByName("select_edit_tags")[0].value = 0;
    }
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    alert(js_text_tags_must_select); 
  }
}
//when tags relevance item, check the checkbos is selected
function setting_products_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    document.edit_tags.action = js_all_href_filename_tags+'?action=setting_products_tags';
    document.edit_tags.submit(); 
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    alert(js_text_tags_must_select); 
  }
}

//aimed at different method to handle different action
function select_type_changed(value)
{
  switch(value){

  case '1':
    delete_select_tags('tags_list_id[]', js_npermission); 
    break;
  case '2':
    delete_select_products_tags('tags_list_id[]', js_npermission); 
    break;
  case '3':
    setting_products_tags('tags_list_id[]'); 
    break;
  }
}

//close confirm
function close_tags_info()
{
  $('#show_popup_info').html(''); 
  $('#show_popup_info').css('display', 'none'); 
}
//text tags info
function show_tags_info(ele, tid, param_str)
{
  ele = ele.parentNode;
  param_str = decodeURIComponent(param_str);
  param_str = param_str.replace(/&/g,'|||');
  $.ajax({
    url: 'ajax.php?action=edit_tags',      
    data: 'tags_id='+tid+'&param_str='+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (ele.offsetTop+$('#tags_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#tags_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#tags_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if (ele.offsetTop+$('#tags_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#tags_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#tags_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      }
      $('#show_popup_info').show(); 
    }
  }); 
}
//new tags info
function create_tags_info(ele)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax.php?action=create_tags',      
    data: '',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  }); 
}
//edit tags previous and next info
function show_link_tags_info(tid, param_str)
{
  param_str = decodeURIComponent(param_str);
  param_str = param_str.replace(/&/g,'|||');
  $.ajax({
    url: 'ajax.php?action=edit_tags',      
    data: 'tags_id='+tid+'&param_str='+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  });  
}
//check product selected
function products_tags_submit(){

  var submit_flag = false;
  $(".products_checkbox").each(function(){

    if($(this).attr('checked') == 'checked'){
    
      submit_flag = true; 
    }
  }); 
  if(submit_flag == true){
    if (js_npermission == 31) {
    document.products_to_tags.submit();
    } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_tags_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.products_to_tags.submit();
        } else {
          $("#button_save").attr('id', 'tmp_button_save');
          var input_pwd_str = window.prompt(js_onetime_pwd, ''); 
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.products_to_tags.action),
              async: false,
              success: function(msg_info) {
                document.products_to_tags.submit();
              }
            }); 
          } else {
            alert(js_onetime_error); 
            setTimeOut($("#tmp_button_save").attr('id', 'button_save'), 1);
          }
        }
      }
    });
    }
  }else{

    alert(js_products_tags_check);
  }
}
//checkbox all select action
function all_select_products(tags_list_id)
{
  var check_flag = document.products_to_tags.all_check.checked;
  if (document.products_to_tags.elements[tags_list_id]) {
    if (document.products_to_tags.elements[tags_list_id].length == null) {
      if (check_flag == true) {
        document.products_to_tags.elements[tags_list_id].checked = true;
      } else {
        document.products_to_tags.elements[tags_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.products_to_tags.elements[tags_list_id].length; i++) {
        if (check_flag == true) {
          document.products_to_tags.elements[tags_list_id][i].checked = true;
        } else {
          document.products_to_tags.elements[tags_list_id][i].checked = false;
        }
      }
    }
  }
}
//button of categories ID
function switch_categories(cid){
  if ($('#d_'+cid).css('display') == 'block') {
    $('#d_'+cid).css('display', 'none');
  } else {
    $('#d_'+cid).css('display', 'block');
  }
}
//check all
function check_all(cid){
  if ($('#categories_'+cid).attr('checked')) {
    $('#d_'+cid+' input[type=checkbox]').attr('checked','checked');
  } else {
    $('#d_'+cid+' input[type=checkbox]').removeAttr('checked');
  }
}
