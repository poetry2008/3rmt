function inventory_operations(num){

  var inventory_contents_value;
  if(num == 1){

    inventory_contents_value = $("#max_inventory").val();
  }else{

    inventory_contents_value = $("#min_inventory").val();
  }
  inventory_contents_value = inventory_contents_value.replace(/\+/g,'<<<');

  $.ajax({
    url: 'ajax_orders.php?action=inventory_operations',   
    type: 'POST',
    dataType: 'text',
    data: 'inventory_contents='+inventory_contents_value+'&pid='+get_pid+'&site_id='+get_site_id, 
    async: false,
    success: function(msg) {

      if(num == 1){

        $("#max_inventory_contents").val(msg);
      }else{

        $("#min_inventory_contents").val(msg);
      }
    }
  });
}
function avg_div_checkbox(){
  document.getElementById('alert_div_id').checked=!document.getElementById('alert_div_id').checked
}
function confirm_div(str,cnt,pid,c_permission,c_type){
  var ClassName = "thumbviewbox";
  var allheight = document.body.scrollHeight;
  //ground div 
  var element_ground = document.createElement('div');
  element_ground.setAttribute('class',ClassName);
  element_ground.setAttribute('id','element_ground_close');
  element_ground.style.cssText = 'position: absolute; top: 0px; left: 0; z-index: 150;background-color: rgb(0, 0, 0); opacity: 0.01; width:100%; height: '+allheight+'px;';
  element_ground.style.filter="alpha(opacity=1)";
  // text str 
  var element_boder = document.createElement('div');
  element_boder.setAttribute('class',ClassName);
  element_boder.setAttribute('id','element_boder_close');
  element_boder.style.cssText = 'margin: 0 auto; line-height: 1.4em;width:500px;background-color: rgb(255,255,255)';
  ok_input_html =  '<input type="button" id="alert_div_submit" onclick=\'save_div_action(\"'+cnt+'\",\"'+pid+'\",\"'+c_permission+'\",\"'+c_type+'\")\' value="'+div_text+'">';
  clear_input_html = '<input type="button" onclick="clear_confirm_div()" value="'+div_text_clear+'">';
  alert_div_html = '<div style="padding:10px;text-align:left">'+str+'</div>';
  alert_div_html = alert_div_html+'<div style="text-align:center">'+ok_input_html+'&nbsp;&nbsp;'+clear_input_html+'</div>'
  element_boder.innerHTML = '<div style="padding:10px;text-align:left">'+alert_div_html+'</div>';

  //center div 
  var element = document.createElement('div');
  element.appendChild(element_boder);
  element.setAttribute('class',ClassName);
  element.setAttribute('id','element_close');
  element.style.cssText = 'width:100%;position:fixed;z-index:151;text-align:center;line-height:0;top:25%';


// add div 
  document.body.appendChild(element_ground);
  document.body.appendChild(element);
  var Apdiv=document.getElementById("alert_div_id");
  Apdiv.focus();
}
function save_div_action(cnt,pid,c_permission,c_type){
  if(document.getElementById("alert_div_id").checked){
  if(pid!=''){
    nquantity = $('#new_confirm_price').val();
    $.ajax({
      type:'POST', 
      dataType:'text',
      beforeSend: function(){$('body').css('cursor', 'wait');$('#wait').show();}, 
      data:'products_id='+pid+"&new_price="+nquantity, 
      async:false, 
      url: 'ajax_orders.php?action=set_new_price',
      success: function(msg) {
        msg_array = msg.split('|||'); 
        $(c_ele).html(msg_array[0]); 
        $(c_ele).next().next().next().find('input[name="pprice[]"]').eq(0).val(msg_array[1]); 
        $(c_ele).next().find('input[name="price[]"]').eq(0).val(msg_array[1]);  
        $(c_ele).next().next().next().next().find('a').html(msg_array[3]);  
        set_money(cnt, false, '1'); 
        setTimeout(function(){$('body').css('cursor', '');$('#wait').hide();$('#show_popup_info').css('display', 'none');}, 500);
      }
    });
    clear_confirm_div()
  }else if(c_permission!=''&&c_type!=''){
    toggle_category_form(c_permission, c_type);
  }else if(pid==''&&c_permission==''&&c_type==''){
    document.forms.new_product.submit();
  }else if(pid==''&&cnt==''&&c_type==''){
    clear_confirm_div();
    save_permission(c_permission)
  }
  }else{
    var em_close=document.getElementById("element_ground_close");
    em_close.parentNode.removeChild(em_close);
    var em_close=document.getElementById("element_close");
    em_close.parentNode.removeChild(em_close);
  }
}

function clear_confirm_div(){
  var em_close=document.getElementById("element_ground_close");
  em_close.parentNode.removeChild(em_close);
  var em_close=document.getElementById("element_close");
  em_close.parentNode.removeChild(em_close);
}
// JS get file name
function getFileName(path){
  var pos1 = path.lastIndexOf('/');
  var pos2 = path.lastIndexOf('\\');
  var pos  = Math.max(pos1, pos2)
  if( pos<0 ){
    return path;
  }else{
    return path.substring(pos+1);
  }
}
// assignment textbox before picture
function change_image_text(_this,change_name){
  var image_name = getFileName(_this.value);
  $.ajax({
    url: 'ajax_orders.php?action=has_pimage',   
    type: 'POST',
    dataType: 'text',
    data: 'image_name='+image_name+'&site_id='+js_site_id, 
    async: false,
    success: function(msg) {
      msg_arr = msg.split('|||'); 
      if (msg_arr[1] == '1') {
        if(msg_arr[0]=='true'){
          if(confirm(image_name+' '+overwrite_image)){
            $("input[name="+change_name+"]").val(image_name);
            $("#overwrite").val('yes');
          }else{
            _this.value='';
          }
        }else if(msg_arr[0]=='false'){
          $("input[name="+change_name+"]").val(image_name);
        }
      } else {
        alert(read_text); 
      }
    }
  });
}
// cancel all of corresponding information about picture 
function clear_image(file_name,input_name){
  var image_name = $("input[name="+input_name+"]").val();
  var f_name = $("input[name="+file_name+"]").val();
  var pid = $("input[name=hidd_pid]").val();
  $.ajax({
    url: 'ajax_orders.php?action=has_pimage',   
    type: 'POST',
    dataType: 'text',
    data: 'image_value='+image_name+'&col_name='+file_name+'&pid='+pid+'&site_id='+js_site_id, 
    async: false,
    success: function(msg) {
      msg_arr = msg.split('|||'); 
      if (msg_arr[1] == '1') {
        if(msg_arr[0]=='true'){
          $.ajax({
            url: 'ajax_orders.php?action=change_pimage',   
            type: 'POST',
            dataType: 'text',
            data: 'col_name='+file_name+'&pid='+pid+'&site_id='+js_site_id, 
            async: false,
            success: function(msg) {
            }
          });
          
          $("input[name="+file_name+"]").val('');
          $("input[name="+input_name+"]").val('');
        }else if(msg_arr[0]=='false'){
          confirmg(image_name+' '+del_confirm,clear_image_href_link+'&file='+image_name+'&cl='+file_name);
        }
      } else {
        alert(read_text); 
      }
    }
  });
}
// assignment textbox before categories picture
function change_c_image_text(_this,change_name){
  var image_name = getFileName(_this.value);
  $.ajax({
    url: 'ajax_orders.php?action=has_cimage',   
    type: 'POST',
    dataType: 'text',
    data: 'image_name='+image_name+'&site_id='+js_site_id, 
    async: false,
    success: function(msg) {
      msg_arr = msg.split('|||'); 
      if (msg_arr[1] == '1') {
        if(msg_arr[0]=='true'){
          if(confirm(image_name+' '+overwrite_image)){
            $("input[name="+change_name+"]").val(image_name);
            $("#c_overwrite").val('yes');
          }else{
            _this.value='';
          }
        }else if(msg_arr[0]=='false'){
          $("input[name="+change_name+"]").val(image_name);
        }
      } else {
        alert(read_text); 
      }
      
    }
  });
}
// cancel all of corresponding information about categories picture
function clear_c_image(file_name,input_name){
  var image_name = $("input[name="+input_name+"]").val();
  var f_name = $("input[name="+file_name+"]").val();
  var e_cid = $("input[name=hide_cid]").val();
  $.ajax({
    url: 'ajax_orders.php?action=has_cimage',   
    type: 'POST',
    dataType: 'text',
    data: 'image_value='+image_name+'&col_name='+file_name+'&e_cid='+e_cid+'&site_id='+js_site_id, 
    async: false,
    success: function(msg) {
      msg_arr = msg.split('|||'); 
      if (msg_arr[1] == '1') {
        if(msg_arr[0]=='true'){
          $.ajax({
            url: 'ajax_orders.php?action=change_cimage',   
            type: 'POST',
            dataType: 'text',
            data: 'col_name='+file_name+'&e_cid='+e_cid+'&site_id='+js_site_id, 
            async: false,
            success: function(msg) {
            }
          });
          
          $("input[name="+file_name+"]").val('');
          $("input[name="+input_name+"]").val('');
        }else if(msg_arr[0]=='false'){
          confirmg(image_name+' '+del_confirm,clear_c_image_href_link+'&file='+image_name+'&cl='+file_name);
        }
      } else {
        alert(read_text); 
      }
    }
  });
}
function remove_event_focus()
{
  if ($('#show_popup_info').css('display') == 'block') {
    c_submit_single = false;
  } 
}
// Restore the focus
function recover_event_focus()
{
  c_submit_single = true;
}
// hidden or displayed recommend commodities correlate option
function cattags_show(num){

  if(num == 0){

    $("#cattags_list").hide();
    $("#cattags_title").hide();
    $("#cattags_contents").hide();
  }else{
    $("#cattags_list").show();
    $("#cattags_title").show();
    $("#cattags_contents").show();
  }
}

// checkbox clear action
function all_reset_tags(tags_list_id)
{ 
  if (document.edit_tags.elements[tags_list_id]) {
    if (document.edit_tags.elements[tags_list_id].length == null) {
        document.edit_tags.elements[tags_list_id].checked = false;
    } else {
      for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
        document.edit_tags.elements[tags_list_id][i].checked = false;
      }
    }
  }
}

// check all checkbox action
function all_select_tags(tags_list_id)
{
  if(document.edit_tags.all_check){
    var check_flag = document.edit_tags.all_check.checked;
  }else{

    var check_flag = true;
  }
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

//when commodity associated Dom tag, ajax data
function edit_products_tags_check(tags_list_id)
{ 
  var options = {
    url: 'categories.php?action=edit_products_tags',
    type:  'POST',
    success: function() {
        $("#show_popup_info").hide();
    }
  };
  $('#edit_tags_id').ajaxSubmit(options);
  return false; 
}

//when delete catagories and item, judgement the checkbox is selected and has deleted prompt dialog
function delete_select_products(categories_list_id,products_list_id,c_permission)
{ 
  var sel_num = 0;
  if(document.myForm1.elements[categories_list_id]){
    if (document.myForm1.elements[categories_list_id].length == null) {
      if (document.myForm1.elements[categories_list_id].checked == true) {
        sel_num = 1;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[categories_list_id].length; i++) {
        if (document.myForm1.elements[categories_list_id][i].checked == true) {
          sel_num = 1;
          break;
        }
      }
    }
  }

  var sel_num_products = 0;
  if(document.myForm1.elements[products_list_id]){ 
    if (document.myForm1.elements[products_list_id].length == null) {
      if (document.myForm1.elements[products_list_id].checked == true) {
        sel_num_products = 1;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[products_list_id].length; i++) {
        if (document.myForm1.elements[products_list_id][i].checked == true) {
          sel_num_products = 1;
          break;
        }
      }
    }
  }
   if (sel_num+sel_num_products >= 1) {
    if (confirm(text_del_confirm)) {
      if (c_permission == 31) {
        document.myForm1.action = href_categories+'?action=delete_select_categories_products&'+js_query_string;
        document.myForm1.submit(); 
      } else {
        $.ajax({
          url: 'ajax_orders.php?action=getallpwd',   
          type: 'POST',
          dataType: 'text',
          data: 'current_page_name='+js_sever_self, 
          async: false,
          success: function(msg) {
            var tmp_msg_arr = msg.split('|||'); 
            var pwd_list_array = tmp_msg_arr[1].split(',');
            if (tmp_msg_arr[0] == '0') {
              document.myForm1.action = href_categories+'?action=delete_select_categories_products&'+js_query_string;
              document.myForm1.submit(); 
            } else {
              var input_pwd_str = window.prompt(onetime_pwd, ''); 
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(href_categories+'?action=delete_select_categories_products&'+js_query_string),
                  async: false,
                  success: function(msg_info) {
                    document.myForm1.action = href_categories+'?action=delete_select_categories_products&'+js_query_string;
                    document.myForm1.submit(); 
                  }
                }); 
              } else {
                document.getElementsByName("products_to_tags")[0].value = 0;
                alert(onetime_error); 
              }
            }
          }
        });
      }
    }else{
      document.getElementsByName("products_to_tags")[0].value = 0;
    }
  } else {
    document.getElementsByName("products_to_tags")[0].value = 0;
    alert(must_select); 
  }
}
//when associate tags, judgement the checkbox is selected
function select_products_to_tags(categories_list_id,products_list_id)
{ 
  var sel_num = 0;
  if(document.myForm1.elements[categories_list_id]){
    if (document.myForm1.elements[categories_list_id].length == null) {
      if (document.myForm1.elements[categories_list_id].checked == true) {
        sel_num = 1;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[categories_list_id].length; i++) {
        if (document.myForm1.elements[categories_list_id][i].checked == true) {
          sel_num = 1;
          break;
        }
      }
    }
  }

  var sel_num_products = 0;
  if(document.myForm1.elements[products_list_id]){ 
    if (document.myForm1.elements[products_list_id].length == null) {
      if (document.myForm1.elements[products_list_id].checked == true) {
        sel_num_products = 1;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[products_list_id].length; i++) {
        if (document.myForm1.elements[products_list_id][i].checked == true) {
          sel_num_products = 1;
          break;
        }
      }
    }
  }
  
  if (sel_num+sel_num_products >= 1) {
    document.myForm1.action = filename_tags+'?action=products_to_tags'+is_query_string;
    document.myForm1.submit(); 
  } else {
    document.getElementsByName("products_to_tags")[0].value = 0;
    alert(must_select); 
  }
}
//handle of check action
function products_tags_change(value){

  if(value == '1'){

    select_products_to_tags('categories_id_list[]','products_id_list[]'); 
  }
  if(value == '2'){

    delete_select_products('categories_id_list[]','products_id_list[]', js_npermission);
  }
}
//checkbox all selected action
function all_products_check(products_list_id)
{
  var check_flag = document.myForm1.all_check.checked;
  if (document.myForm1.elements[products_list_id]) {
    if (document.myForm1.elements[products_list_id].length == null) {
      if (check_flag == true) {
        document.myForm1.elements[products_list_id].checked = true;
      } else {
        document.myForm1.elements[products_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.myForm1.elements[products_list_id].length; i++) {
        if (check_flag == true) {
          document.myForm1.elements[products_list_id][i].checked = true;
        } else {
          document.myForm1.elements[products_list_id][i].checked = false;
        }
      }
    }
  }
}
window.onresize = resizepage;
//function of browser zoom window
function resizepage(){
  if($(".box_warp").height() < $(".compatible").height()){
    $(".box_warp").height($(".compatible").height());
  }
}
$(document).ready(function(){
    if(document.getElementsByName("select_edit_tags")[0]){
      document.getElementsByName("select_edit_tags")[0].value = 0;
    }
    if(document.getElementsByName("select_edit_tags")[1]){
      document.getElementsByName("select_edit_tags")[1].value = 0;
    }
    if(document.getElementsByName("products_to_tags")[0]){
      document.getElementsByName("products_to_tags")[0].value = 0;
    }
    if($(".box_warp").height() < $(".compatible").height()){
      $(".box_warp").height($(".compatible").height()+100);
    }
    $(".udlr").udlr(); 
    ajaxLoad(js_cpath, js_isempty_site_id); 
    }); 
//set picture info
function set_image_alt_and_title(_this,pid,limit_time_info,limit_flag){
  $.ajax({
type:'POST',
dataType: 'text',
url: 'categories.php?action=get_last_order_date',
data: 'pid='+pid+'&limit_time='+limit_time_info+'&single='+limit_flag,
async:false,
success: function(text) {
  if((text.indexOf('</body>')>0)&&(text.indexOf('</html>')>0)){
    alert(timeout_relogin);
    window.location.reload();
  }
  $(_this).attr('alt',text+ale_text);
  $(_this).attr('title',text+ale_text);
}
});
}
//associate item dropdown list
function relate_products1(cid,rid){
  $.ajax({
dataType: 'text',
url: 'categories.php?action=get_products&cid='+cid+'&rid='+rid,
success: function(text) {
$('#relate_products').html(text);
}
});
}
//click to jump url
function confirmg(question,url) {
  var x = confirm(question);
  if (x) {
    window.location = url;
  }
}
//check categories name and romaji
function cmess(pid, cid, site_id, c_permission, ca_type) {
  var ca_error = false; 
  if (document.getElementById('cname').value == "") {
    ca_error = true; 
    alert(name_is_not_null); 
  }

  if (document.getElementById('cromaji').value == "") {
    ca_error = true; 
    alert(romaji_not_null); 
  }

  flag1 = c_is_set_romaji(pid,cid,site_id);
  flag2 = c_is_set_error_char(true); 
  
  if (ca_error == false) {
    if(flag1&&flag2){
      if (c_permission == 31) {
        if (ca_type == 0) {
          document.forms.newcategory.submit(); 
        } else if (ca_type == 1) {
          document.forms.editcategory.submit(); 
        }
      } else {
        $.ajax({
          url: 'ajax_orders.php?action=getallpwd',   
          type: 'POST',
          dataType: 'text',
          data: 'current_page_name='+js_sever_self, 
          async: false,
          success: function(msg) {
            var tmp_msg_arr = msg.split('|||'); 
            var pwd_list_array = tmp_msg_arr[1].split(',');
            if (tmp_msg_arr[0] == '0') {
              if (ca_type == 0) {
                document.forms.newcategory.submit(); 
              } else if (ca_type == 1) {
                document.forms.editcategory.submit(); 
              }
            } else {
              var input_pwd_str = window.prompt(onetime_pwd, ''); 
              var form_action_str = ''; 
              if (ca_type == 0) {
                form_action_str = document.forms.newcategory.action; 
              } else if (ca_type == 1) {
                form_action_str = document.forms.editcategory.action; 
              }
              if (in_array(input_pwd_str, pwd_list_array)) {
                $.ajax({
                  url: 'ajax_orders.php?action=record_pwd_log',   
                  type: 'POST',
                  dataType: 'text',
                  data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(form_action_str),
                  async: false,
                  success: function(msg_info) {
                    if (ca_type == 0) {
                      document.forms.newcategory.submit(); 
                    } else if (ca_type == 1) {
                      document.forms.editcategory.submit(); 
                    }
                  }
                }); 
              } else {
                alert(onetime_error); 
              }
            }
          }
        });
      }
    }
  } 
  
}
//check item name and romaji is null
function mess(){
  if (document.getElementById('pname').value == "") {
    alert(product_name_is_not_null); 
    return false; 
  }

  if (document.getElementById('promaji').value == "") {
    alert(romaji_not_null); 
    return false; 
  }
}
//check price
function check_price(new_id,old_price,percent){
  $('#'+new_id).css('border-color','');
  new_price = Math.abs($('#'+new_id).val());
  old_price = Math.abs(old_price);
  if (percent != '' && percent != 0 && percent != null) {
    if (new_price > old_price) {
      if( ((new_price - old_price) / old_price) * 100 >= percent ) {
        error_msg = percent+js_chae_error+"\n";
      }
    } else {
      if( ((old_price - new_price) / new_price) * 100 >= percent ) {
        error_msg = percent+js_chae_error+"\n";
      }
    }
  }

  if (error_msg != '') {
    alert(error_msg);
    error_msg = '';
  }

  if(confirm(js_update_notice)){
    return true;
  }else{
    alert(js_update_error);
    $('#'+new_id).css('border-color','red');
    $('#'+new_id).focus();
    return false;
  }
}
//count price
function calculate_price(){
  if (parseInt($('#pp').val()) != 0) {
    $('#a_1').html(Math.ceil(5000/$('#pp').val()));
    if ($('#a_1').html()%10 != 0) {
      if ($('#a_1').html()%10 < 5) {
        $('#a_2').html(Math.floor($('#a_1').html()/10)*10+5);
      } else {
        $('#a_2').html('');
      }
      $('#a_3').html(Math.floor($('#a_1').html()/10)*10+10);
    } else {
      $('#a_2').html('');
      $('#a_3').html('');
    }

    $('#b_1').html(Math.ceil(10000/$('#pp').val()));
    if ($('#b_1').html()%10 != 0) {
      if ($('#b_1').html()%10 < 5) {
        $('#b_2').html(Math.floor($('#b_1').html()/10)*10+5);
      } else {
        $('#b_2').html('');
      }
      $('#b_3').html(Math.floor($('#b_1').html()/10)*10+10);
    } else {
      $('#b_2').html('');
      $('#b_3').html('');
    }
  } else {
    $('#a_1').html('');
    $('#a_2').html('');
    $('#a_3').html('');
    $('#b_1').html('');
    $('#b_2').html('');
    $('#b_3').html('');
  }
}
//assignment element value if id equal qt
function change_qt(ele){
  qt = ele.innerHTML;
  if (qt) {
    $('#qt').val(qt);
  }
}
//open remind of item 
function get_cart_products(){
  tagstr = '';

  $(".carttags").each(function(){
      start  = $(this).attr('name').indexOf('[') + 1;
      end    = $(this).attr('name').indexOf(']');
      if(this.checked)
      tagstr += '&tags_id[]='+$(this).attr('name').substr(start, end-start);
      });
  if (tagstr != '')
    window.open("categories.php?action=get_cart_products&products_id="+js_get_pid+"&buyflag="+$("input[@type=radio][name=products_cart_buyflag][checked]").val()+tagstr, '','toolbar=0,location=0,directories=0,status=1,menubar=0,scrollbars=yes,resizable=yes,width=300');
}
//open and close categories tree 
function display(){
  offset = $(".pageHeading").offset();
  var categories_tree = document.getElementById('categories_tree'); 
  if(categories_tree.style.display == 'none' || categories_tree.style.display == ''){
    categories_tree.style.top = offset.top + 'px';
    categories_tree.style.display = 'block';
  }else{
    categories_tree.style.display = 'none';
  }
}
//clear inputbox attribute value
function clear_option()
{
  document.getElementById('option_keyword').value = '';
}
//auto search team
$(function() {
    function format(group) {
    return group.name;
    }
    $("#option_keyword").autocomplete('ajax_orders.php?action=search_group', {
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
//jump to specified group 
function handle_option()
{
  var option_value = document.getElementById('option_keyword').value;
  if (option_value != '') {
    $.ajax({
type:'POST',
dataType: 'text',
url: 'ajax_orders.php?action=handle_option',
data:'keyword='+option_value,
async:false,
success: function(msg) {
open_url = 'option.php?keyword='+option_value+"&search=2";     
window.open(open_url, 'newwindow', ''); 
}
});  
} 
}
//set position of popups
function info_box_set(ele, current_belong){
  
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_popup_info').height()){
offset = ele.offsetTop+$("#products_list_table").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#products_list_table").position().top-1)) {
offset = ele.offsetTop+$("#products_list_table").position().top-1-$('#show_popup_info').height()+head_top;
} else {
offset = ele.offsetTop+$("#products_list_table").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#products_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#products_list_table").position().top-1)) {
    offset = ele.offsetTop+$("#products_list_table").position().top-1-$('#show_popup_info').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#products_list_table").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#products_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_popup_info').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_popup_info').height()) > $('.box_warp').height())&&($('#show_popup_info').height()<ele.offsetTop+parseInt(head_top)-$("#products_list_table").position().top-1)) {
      offset = ele.offsetTop+$("#products_list_table").position().top-1-$('#show_popup_info').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#products_list_table").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#products_list_table').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#products_list_table").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_popup_info').css('top',offset);
}
box_warp_height = box_warp_height + $('#show_popup_info').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
}
$('#show_popup_info').css('z-index', z_index);
$('#show_popup_info').css('left',leftset);
}
//hide pupup page
function hidden_info_box(){
  $('#show_popup_info').css('display','none');
}

var ele_tags_obj = '';
//popup tag info
function edit_products_tags(ele,tags_id_list,type,url,pid){
  $.ajax({
    url: 'ajax.php?action=edit_products_tags',
    data: 'tags_id_list='+tags_id_list+'&type='+type+'&url='+url+'&pid='+pid,
    type: 'POST', 
    dataType: 'text',  
    async:false,
    success: function(text) {
      ele_obj = $(ele).offset();
      ele_tags_obj = ele;
      $('#show_popup_info').html(text);
      $('#show_popup_info').css('top',ele_obj.top+$(ele).height());
      $('#show_popup_info').css('left',ele_obj.left);
      $('#show_popup_info').css('z-index', 1);
      $('#show_popup_info').css('display','block');
    }
  });
}
//pupup item info
function show_product_info(pid,ele){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_info_box&pID='+pid+'&site_id='+show_product_info_url,
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
if(ele!=''){
info_box_set(ele, js_belong);
}
$('#show_popup_info').css('display','block');
}
});
}
//pupup item move page
function show_product_move(pid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_move_box&pID='+pid+'&site_id='+show_product_move_url,
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
//popup item copy or link page
function show_product_copy(type,pid){
  var type_str;
  if(type == 'copy'){

    type_str = 'product_copy_to_box';
  }else{
    
    type_str = 'product_link_to_box'; 
  }
  $.ajax({
dataType: 'text',
url: 'ajax.php?action='+type_str+'&pID='+pid+'&site_id='+show_product_copy_url,
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
//popup item delete page
function show_product_delete(pid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_delete_box&pID='+pid+'&site_id='+show_product_copy_url,
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
//popup item delete description
function show_product_description_delete(pid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?action=product_description_delete_box&pID='+pid+'&site_id='+show_product_description_delete_url,
success: function(text) {
//show_p_info 
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
//popup categories info
function show_category_info(cid,ele){
  $.ajax({
dataType: 'text',
url: 'ajax.php?'+show_category_info_url+'action=show_category_info&current_cid='+cid,
success: function(text) {
$('#show_popup_info').html(text);
if(ele!=''){
info_box_set(ele, js_belong);
}
$('#show_popup_info').css('display','block');
}
});
}
//popup move categroies page
function move_category_id(cid){
  $.ajax({
dataType: 'text',
url: 'ajax.php?'+move_category_id_url+'&action=move_category&current_cid='+cid,
success: function(text) {
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
//popup delete categories page
function delete_category_info(cid, del_type){
  $.ajax({
dataType: 'text',
url: 'ajax.php?'+delete_category_info_url+'&action=delete_category&current_cid='+cid+'&del_type'+del_type,
success: function(text) {
$('#show_popup_info').html(text);
$('#show_popup_info').css('display','block');
}
});
}
//popup update item price/item in/inventory 
function show_update_info(ele, pid, update_type, cnt_num) {
  if (update_type == '0') {
    origin_quantity = $('#quantity_real_'+pid).html(); 
    url_str = 'ajax.php?action=update_real_quantity'; 
    data_str = 'pid='+pid+"&origin_num="+origin_quantity;
  } else if (update_type == '1') {
    origin_quantity = $('#virtual_quantity_'+pid).html(); 
    url_str = 'ajax.php?action=update_virtual_quantity';
    data_str = 'pid='+pid+"&origin_num="+origin_quantity;
  } else if (update_type == '2') {
    origin_quantity = $('#quantity_'+pid).html(); 
    url_str = 'ajax.php?action=update_quantity'; 
    data_str = 'pid='+pid+"&origin_num="+origin_quantity;
  } else {
    origin_price = $('#h_edit_p_'+pid).html(); 
    url_str = 'ajax.php?action=set_new_price'; 
    data_str = 'pid='+pid+"&origin_price="+origin_price+'&cnt_num='+cnt_num;
  }
  $.ajax({
type:'POST',
dataType:'text',
data:data_str, 
async:false, 
url: url_str,
success: function(text) {
$('#show_popup_info').html(text);
if(ele!=''){
info_box_set(ele, js_belong);
}
$('#show_popup_info').css('display','block');
if(update_type == '0'){
$('#real_pro_num').select().focus();
}else if (update_type == '1') {
$('#virtual_pro_num').select().focus();
} else if (update_type == '2') {
$('#real_pro_num').select().focus();
} else {
$('#new_confirm_price').select().focus();
}
}
});
}
//popup calendar
function open_new_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    //mm-dd-yyyy || mm/dd/yyyy
    $('#toggle_open').val('1'); 

    var rules = {
      "all": {
        "all": {
          "all": {
            "all": "current_s_day",
          }
        }
      }};
    if ($("#date_orders").val() != '') {
      if ($("#date_orders").val() == '0000-00-00') {
        date_info_str =  js_cale_date;  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_orders").val().split('-'); 
      }
    } else {
      //mm-dd-yyyy || mm/dd/yyyy
      date_info_str = js_cale_date;  
      date_info = date_info_str.split('-');  
    }
    new_date = new Date(date_info[0], date_info[1]-1, date_info[2]); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
contentBox: "#mycalendar",
width:'170px',
date: new_date

}).render();
        if (rules != '') {
        month_tmp = date_info[1].substr(0, 1);
        if (month_tmp == '0') {
        month_tmp = date_info[1].substr(1);
        month_tmp = month_tmp-1;
        } else {
        month_tmp = date_info[1]-1; 
        }
        day_tmp = date_info[2].substr(0, 1);

        if (day_tmp == '0') {
        day_tmp = date_info[2].substr(1);
        } else {
        day_tmp = date_info[2];   
        }
        data_tmp_str = date_info[0]+'-'+month_tmp+'-'+day_tmp;
        calendar.set("customRenderer", {
rules: rules,
filterFunction: function (date, node, rules) {
cmp_tmp_str = date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate();
if (cmp_tmp_str == data_tmp_str) {
node.addClass("redtext"); 
}
}
});
}
var dtdate = Y.DataType.Date;
calendar.on("selectionChange", function (ev) {
    var newDate = ev.newSelection[0];
    tmp_show_date = dtdate.format(newDate); 
    tmp_show_date_array = tmp_show_date.split('-');
    $(".cal-TextBox").val(tmp_show_date); 
    $("#date_orders").val(tmp_show_date); 
    $('#toggle_open').val('0');
    $('#toggle_open').next().html('<div id="mycalendar"></div>');
    });
});
}
}
$(document).ready(function() {
  //listen keyup
  $(document).keyup(function(event) {
    if (event.which == 27) {
      //esc 
      if (typeof($('#alert_div_submit').val()) != 'undefined'){
          clear_confirm_div();
      }else{
        if ($('#show_popup_info').css('display') != 'none') {
          hidden_info_box(); 
        }
      }
    }
    if (event.which == 13) {
      //ENTER 
      if (typeof($('#alert_div_submit').val()) != 'undefined'){
        $('#alert_div_submit').trigger('click');
      }else{
      if ($('#show_popup_info').css('display') != 'none') {
        tmp_click_str = $("#show_popup_info").find('input:button').first().attr('onclick'); 
        tmp_click_symbol = '0'; 
        if (tmp_click_str.indexOf('update_virtual_quantity') >= 0) {
          tmp_click_symbol = '1'; 
        } else if (tmp_click_str.indexOf('update_quantity') >= 0) {
          tmp_click_symbol = '1'; 
        } 
         
        tmp_click_str = $("#show_popup_info").find('input:button').first().attr('id'); 
         
        if (tmp_click_str == 'new_price_button') {
          $("#new_price_button").trigger("click");
        } else {
          if (c_submit_single) {
            if (tmp_click_symbol == '1') {
              $("#show_popup_info").find('input:button').first().trigger("click"); 
            }else{
              if(ele_tags_obj != ''){
                 $("#show_popup_info").find('input:button').first().trigger("click"); 
              } 
              if ($("#button_save_product")) {
                $("#button_save_product").trigger("click");
              }
            }
          }
        }
        
      } 
    }
    }
    if (event.ctrlKey && event.which == 37) {
      //Ctrl+left 
      if ($('#show_popup_info').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      //Ctrl+right
      if ($('#show_popup_info').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});
//submit action
function toggle_category_form(c_permission, cf_type)
{
  if (c_permission == 31) {
    if (cf_type == 0) {
      document.forms.delete_category.submit(); 
    } else if (cf_type == 1) {
      //handle double-clicking submit 
      if($("#check_submit_flag").val() == '0'){
        document.forms.insert_product.submit(); 
        $("#check_submit_flag").val('1');
      }
    } else if (cf_type == 2) {
      document.forms.update_product.submit(); 
    } else if (cf_type == 3) {
      document.forms.simple_update_product.submit(); 
    } else if (cf_type == 4) {
      document.forms.delete_product.submit(); 
    } else if (cf_type == 5) {
      document.forms.move_category.submit(); 
    } else if (cf_type == 6) {
      document.forms.move_products.submit(); 
    } else if (cf_type == 7) {
      document.forms.copy_to.submit(); 
    } else if (cf_type == 8) {
      document.forms.simple_update.submit(); 
    }
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name='+js_sever_self, 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          if (cf_type == 0) {
            document.forms.delete_category.submit(); 
          } else if (cf_type == 1) {
            //handle double-submit
            if($("#check_submit_flag").val() == '0'){
              document.forms.insert_product.submit(); 
              $("#check_submit_flag").val('1');
            }
          } else if (cf_type == 2) {
            document.forms.update_product.submit(); 
          } else if (cf_type == 3) {
            document.forms.simple_update_product.submit(); 
          } else if (cf_type == 4) {
            document.forms.delete_product.submit(); 
          } else if (cf_type == 5) {
            document.forms.move_category.submit(); 
          } else if (cf_type == 6) {
            document.forms.move_products.submit(); 
          } else if (cf_type == 7) {
            document.forms.copy_to.submit(); 
          } else if (cf_type == 8) {
            document.forms.simple_update.submit(); 
          }
        } else {
          var input_pwd_str = window.prompt(onetime_pwd, ''); 
          var form_action_str = ''; 
          if (cf_type == 0) {
            form_action_str = document.forms.delete_category.action; 
          } else if (cf_type == 1) {
            form_action_str = document.forms.insert_product.action; 
          } else if (cf_type == 2) {
            form_action_str = document.forms.update_product.action; 
          } else if (cf_type == 3) {
            form_action_str = document.forms.simple_update_product.action; 
          } else if (cf_type == 4) {
            form_action_str = document.forms.delete_product.action; 
          } else if (cf_type == 5) {
            form_action_str = document.forms.move_category.action; 
          } else if (cf_type == 6) {
            form_action_str = document.forms.move_products.action; 
          } else if (cf_type == 7) {
            form_action_str = document.forms.copy_to.action; 
          } else if (cf_type == 8) {
            form_action_str = document.forms.simple_update.action; 
          }
          if (in_array(input_pwd_str, pwd_list_array)) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(form_action_str),
              async: false,
              success: function(msg_info) {
                if (cf_type == 0) {
                  document.forms.delete_category.submit(); 
                } else if (cf_type == 1) {
                  //handle double-submit
                  if($("#check_submit_flag").val() == '0'){
                    document.forms.insert_product.submit(); 
                    $("#check_submit_flag").val('1');
                  }
                } else if (cf_type == 2) {
                  document.forms.update_product.submit(); 
                } else if (cf_type == 3) {
                  document.forms.simple_update_product.submit(); 
                } else if (cf_type == 4) {
                  document.forms.delete_product.submit(); 
                } else if (cf_type == 5) {
                  document.forms.move_category.submit(); 
                } else if (cf_type == 6) {
                  document.forms.move_products.submit(); 
                } else if (cf_type == 7) {
                  document.forms.copy_to.submit(); 
                } else if (cf_type == 8) {
                  document.forms.simple_update.submit(); 
                }
              }
            }); 
          } else {
            alert(onetime_error); 
          }
        }
      }
    });
  }
}
//check item price 
function check_single_product_price(pid_info, c_permission, c_type) {
  var new_price_value = $('#pp').val(); 
  var relate_new_price_value = '0'; 
  if (typeof($('#r_price').val()) != 'undefined') {
    relate_new_price_value = $('#r_price').val(); 
  }
  $.ajax({
    type: 'POST',
    async: false,
    url: 'ajax_orders.php?action=check_single_products_profit',
    dataType: 'text',
    data: 'products_id='+pid_info+'&new_price='+new_price_value+'&relate_new_price='+relate_new_price_value,
    success:function(msg_info) {
      if (msg_info != '') {
        $("#button_save_product").attr("id", 'tmp_button_save_product');
        alert(msg_info);
        setTimeout('$("#tmp_button_save_product").attr("id", "button_save_product")', 100); 
      } else {
        relate_value = $('input:[name=relate_products_id]').val(); 
        if(typeof($('#relate_qtr').val())!='undefined'){
          r_quantity = $('#relate_qtr').val();
        }else{
          r_quantity = 0;
        }
        if(typeof($('#product_qtr').val())!='undefined'){
          p_quantity = $('#product_qtr').val();
        }else{
          p_quantity = 0;
        }
        if(typeof($('#product_radices').val())!='undefined'){
          p_radices = $('#product_radices').val();
        }else{
          p_radices = 0;
        }
        if(typeof($('#relate_radices').val())!='undefined'){
          r_radices = $('#relate_radices').val();
        }else{
          r_radices = 0;
        }
        if(typeof(relate_value)!='undefined'){
        $.ajax({
          type: 'POST',
          async: false,
          url: 'ajax_orders.php?action=check_single_products_avg',
          dataType: 'text',
          data: 'products_id='+pid_info+'&new_price='+new_price_value+'&relate_new_price='+relate_new_price_value+'&relate_id='+relate_value+'&p_quantity='+p_quantity+'&r_quantity='+r_quantity+'&p_radices='+p_radices+'&r_radices='+r_radices,
          success:function(msg_avg){
            if(msg_avg != ''){
              confirm_div(msg_avg,'','',c_permission,c_type)
            }else{
              toggle_category_form(c_permission, c_type); 
            }
          }
        });
        }else{
          toggle_category_form(c_permission, c_type); 
        }
      }
    }
  }); 
}
//check out edit item price 
function check_edit_product_profit() {
  var new_price_value = $('#pp').val(); 
  var flag_type = $('input:radio:checked[name=products_bflag]').val(); 
  var relate_value = $('#relate_info').val(); 
  var num_value = $('#products_attention_1_3').val(); 
  if (relate_value != '0') {
    $.ajax({
      type: 'POST',
      async: false,
      url: 'ajax_orders.php?action=check_category_to_products_profit',
      dataType: 'text',
      data: 'product_flag='+flag_type+'&new_price='+new_price_value+'&p_relate_id='+relate_value+'&num_value='+num_value,
      success:function(msg_info) {
        if (msg_info != '') {
          alert(msg_info); 
        } else {
          new_product_quantity = $('#products_real_quantity').val();
          products_name = $('#pname').val();
          $.ajax({
            type: 'POST',
            async: false,
            url: 'ajax_orders.php?action=check_category_to_products_avg',
            dataType: 'text',
            data: 'products_name='+products_name+'&new_price='+new_price_value+'&product_quantity='+new_product_quantity+'&p_relate_id='+relate_value+'&p_radices='+num_value,
            success:function(msg_avg){
              if(msg_avg != ''){
                confirm_div(msg_avg,'','','','')
              }else{
                document.forms.new_product.submit(); 
              }
            }
          });
        }
      }
    });
  } else {
    document.forms.new_product.submit(); 
  }
}