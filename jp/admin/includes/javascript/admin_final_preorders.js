$(document).ready(function() {
  //listen keyup
  $(document).keydown(function(event) {
    if (event.which == 27) {
      //esc 
      if (typeof($('#alert_div_submit').val()) != 'undefined'){
          clear_confirm_div();
      }
    }
    if (event.which == 13) {
      //ENTER 
      if (typeof($('#alert_div_submit').val()) != 'undefined'){
        $('#alert_div_submit').trigger('click');
      }
    }
  });
});
function avg_div_checkbox(){
  document.getElementById('alert_div_id').checked=!document.getElementById('alert_div_id').checked
}
function confirm_div(str){
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
  element_boder.style.cssText = 'margin: 0 auto; line-height: 1.4em;width:500px;;background-color: rgb(255,255,255)';
  ok_input_html =  '<input type="button" id="alert_div_submit" onclick=\'save_div_action()\' value="'+js_final_text_ok+'">';
  clear_input_html = '<input type="button" onclick="clear_confirm_div()" value="'+js_final_text_clear+'">';
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
function save_div_action(){
  if(document.getElementById("alert_div_id").checked){
    clear_confirm_div();
    check_mail_product_status(js_final_oid, js_final_npermission);
  }else{
    clear_confirm_div();
  }
}

function clear_confirm_div(){
  var em_close=document.getElementById("element_ground_close");
  em_close.parentNode.removeChild(em_close);
  var em_close=document.getElementById("element_close");
  em_close.parentNode.removeChild(em_close);
}
function confirm_div_init(hidden_list_str,price_list_str,num_list_str,ot_total_value,payment_method){
  $.ajax({
    url: 'ajax_preorders.php?action=check_preorder_products_avg',
    type: 'POST',
    dataType: 'text',
    data: 'language_id='+js_final_languages_id+'&site_id='+js_final_site_id+'&products_list_str='+hidden_list_str+'&price_list_str='+price_list_str+'&num_list_str='+num_list_str+'&ot_total_value='+ot_total_value+'&payment_method='+payment_method,
    async: false,
    success: function (msg_info) {
      if (msg_info != '') {
        confirm_div(msg_info);
      } else {
        check_mail_product_status(js_final_oid, js_final_npermission);
      } 
    }
  }); 
}
var session_orders_id = js_final_oid;
var session_site_id = js_final_site_id;
//put right value into session
function orders_session(type,value){
  
  $.ajax({
    type: "POST",
    data: 'orders_session_type='+type+'&orders_session_value='+value+'&orders_id='+session_orders_id,
    async:false,
    url: 'ajax_preorders.php?action=orders_session',
    success: function(msg) {
      
    }
  });
}
//check form is ok
function submit_order_check(products_id,op_id){
  var _end = $("#status").val();
  if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
  }else{
    if(confirm(js_final_title_changed)){
    }else{
      return false;
    }
  }
  
  var qty = document.getElementById('update_products_new_qty_'+op_id).value;
  var ensure_date = document.getElementById('date_ensure_deadline').value; 
  ensure_date = ensure_date.replace(/(^\s*)|(\s*$)/g, ""); 
  var payment_str = '';
  if (document.getElementsByName('payment_method')[0]) {
    payment_str = document.getElementsByName('payment_method')[0].value; 
  }
  var is_cu_single = 1;
  var start_num = $('#button_add_id').val(); 
  var is_cu_str = ''; 
  for (var s_num = start_num; s_num > 0; s_num--) {
    if (document.getElementsByName('update_totals['+s_num+'][class]')[0]) {
      if (document.getElementsByName('update_totals['+s_num+'][class]')[0].value == 'ot_custom') {
        if((document.getElementsByName('update_totals['+s_num+'][title]')[0].value == '' && document.getElementsByName('update_totals['+s_num+'][value]')[0].value != '') || (document.getElementsByName('update_totals['+s_num+'][title]')[0].value != '' && document.getElementsByName('update_totals['+s_num+'][value]')[0].value == '')){
        is_cu_str += document.getElementsByName('update_totals['+s_num+'][title]')[0].value + document.getElementsByName('update_totals['+s_num+'][value]')[0].value; 
        }
      }
    }
  }
  is_cu_str = is_cu_str.replace(/^\s+|\s+$/g,"");  
  if (is_cu_str != '') {
    is_cu_single = 0;
  }
  $.ajax({
    type:'POST',
    data:"c_comments="+$('#c_comments').val()+"&o_id="+js_final_oid+"&ensure_date="+ensure_date+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end+'&c_payment='+payment_str+'&c_name_info='+document.getElementsByName('update_customer_name')[0].value+'&c_mail_info='+document.getElementsByName('update_customer_email_address')[0].value+'&is_customized_fee='+is_cu_single,
    async:false,
    url:'ajax_preorders.php?action=check_edit_preorder_variable_data',
    success: function(msg_info) {
      if (msg_info != '') {
        alert(msg_info); 
      } else {
        $.ajax({
          dataType: 'text',
          url: 'ajax_orders_weight.php?action=edit_new_preorder',
          data: 'qty='+qty+'&products_id='+products_id, 
          type:'POST',
          async: false,
          success: function(data) {
            var reg_info = new RegExp("update_products\\[[0-9]+\\]\\[p_price\\]"); 
            var find_input_name = ''; 
            var price_list_str = '';
            var hidden_list_str = '';
            var num_list_str = '';
            var ot_total_value = $("#ot_total_id").html();
            var payment_method = $("select[name='payment_method']").val();
            $('#preorder_list').find('input').each(function() {
              if ($(this).attr('type') == 'text') {
                find_input_name = $(this).attr('name'); 
                if (reg_info.test(find_input_name)) {
                  price_list_str += ($(this).val() == '' ? 0 : $(this).val())+'|||'; 
                  hidden_list_str += $(this).next().val()+'|||'; 
                  num_list_str += $(this).parent().prev().prev().prev().prev().find('input[type=text]').val()+'|||'; 
                }
              }
            }); 
            if(data != ''){
              if(confirm(data)){
                if (price_list_str != '') {
                  price_list_str = price_list_str.substr(0, price_list_str.length-3);
                  hidden_list_str = hidden_list_str.substr(0, hidden_list_str.length-3);
                  num_list_str = num_list_str.substr(0, num_list_str.length-3);
                  $.ajax({
                    url: 'ajax_preorders.php?action=check_preorder_products_profit', 
                    type: 'POST',
                    dataType: 'text',
                    data: 'products_list_str='+hidden_list_str+'&price_list_str='+price_list_str+'&num_list_str='+num_list_str,
                    async: false,
                    success: function (msg_info) {
                      if (msg_info != '') {
                        if (confirm(msg_info)) {
                          confirm_div_init(hidden_list_str,price_list_str,num_list_str,ot_total_value,payment_method);
                        }
                      } else {
                        confirm_div_init(hidden_list_str,price_list_str,num_list_str,ot_total_value,payment_method);
                      }
                    }
                  }); 
                } else {
                  confirm_div_init(hidden_list_str,price_list_str,num_list_str,ot_total_value,payment_method);
                }
              }
            }else{
              if (price_list_str != '') {
                price_list_str = price_list_str.substr(0, price_list_str.length-3);
                hidden_list_str = hidden_list_str.substr(0, hidden_list_str.length-3);
                num_list_str = num_list_str.substr(0, num_list_str.length-3);
                $.ajax({
                  url: 'ajax_preorders.php?action=check_preorder_products_profit', 
                  type: 'POST',
                  dataType: 'text',
                  data: 'products_list_str='+hidden_list_str+'&price_list_str='+price_list_str+'&num_list_str='+num_list_str,
                  async: false,
                  success: function (msg_info) {
                    if (msg_info != '') {
                      if (confirm(msg_info)) {
                        confirm_div_init(hidden_list_str,price_list_str,num_list_str,ot_total_value,payment_method);
                      }
                    } else {
                      confirm_div_init(hidden_list_str,price_list_str,num_list_str,ot_total_value,payment_method);
                    }
                  }
                });
              } else {
                confirm_div_init(hidden_list_str,price_list_str,num_list_str,ot_total_value,payment_method);
              }
            }
          }
        });
      }
    }
  });
}
  //hide pay method info
  function hidden_payment(){
     var idx = document.edit_order.elements["payment_method"].selectedIndex;
     var CI =  document.edit_order.elements["payment_method"].options[idx].value;
     $(".rowHide").hide();
     $(".rowHide").find("input").attr("disabled","true");
     $(".rowHide_"+CI).show();
     $(".rowHide_"+CI).find("input").removeAttr("disabled"); 
     price_total();
     recalc_preorder_price(js_final_oid, js_final_id_str, "1", js_final_info_str);
  }

   //plus or minus sign
   function sign(num){

    var sign = '<select id="sign_'+num+'" name="sign_value_'+num+'" onchange="price_total(\''+js_final_money_symbol+'\');orders_session(\'sign_'+num+'\',this.value);">';
    sign += '<option value="1">+</option>';
    sign += '<option value="0">-</option>';
    sign += '</select>';
    return sign;
  }
  //add inputbox
  function add_option(){
    var add_num = $("#button_add_id").val();
    add_num = parseInt(add_num);
    orders_session('customers_add_num',add_num);
    $("#button_add_id").val(add_num+1);
    add_num++;
    var add_str = '';

    add_str += '<tr><td class="smallText" align="left">&nbsp;</td>'
            +'<td class="smallText" align="right"><input style="text-align:right;" value="" size="'+$("#text_len").val()+'" name="update_totals['+add_num+'][title]" onblur="orders_session(\'customers_total_'+add_num+'\',this.value);">:'
            +'</td><td class="smallText" align="right">'+sign(add_num)+'<input style="text-align:right;" id="update_totals_'+add_num+'" value="" size="6" onkeyup="clearNewLibNum(this);price_total(\''+js_final_money_symbol+'\');recalc_preorder_price(\''+js_final_oid+'\', \''+js_final_id_str+'\', \'0\', \''+js_final_info_str+'\');" onchange="clearNewLibNum(this);price_total(\''+js_final_money_symbol+'\');recalc_preorder_price(\''+js_final_oid+'\', \''+js_final_id_str+'\', \'0\', \''+js_final_info_str+'\');" name="update_totals['+add_num+'][value]">'+js_final_money_symbol+'<input type="hidden" name="update_totals['+add_num+'][class]" value="ot_custom"><input type="hidden" name="update_totals['+add_num+'][total_id]" value="0"></td>'
            +'<td><b><img height="17" width="1" border="0" alt="" src="images/pixel_trans.gif"></b></td></tr>';

    $("#handle_fee_id").parent().parent().before(add_str);
  } 
$(document).ready(function(){
  hidden_payment();
  $("select[name='payment_method']").change(function(){
    hidden_payment();
  });
  $("#update_ensure_year").change(function(){
    var date_value = document.getElementById("update_ensure_year").value;
    orders_session('update_ensure_year',date_value);
  });
  $("#update_ensure_month").change(function(){
    var date_value = document.getElementById("update_ensure_month").value;
    orders_session('update_ensure_month',date_value);
  });
  $("#update_ensure_day").change(function(){
    var date_value = document.getElementById("update_ensure_day").value;
    orders_session('update_ensure_day',date_value);
  }); 
  $("select[name='status']").change(function(){
    var s_status = document.getElementsByName("status")[0].value;
    orders_session('status',s_status);
    var title = document.getElementsByName("title")[0].value;
    orders_session('title',title);
    var comments = document.getElementsByName("comments")[0].value;
    orders_session('comments',comments);
  }); 
  $("input[name='title']").blur(function(){
    var title = document.getElementsByName("title")[0].value;
    orders_session('title',title);
  });
  $("textarea[name='comments']").blur(function(){
    var comments = document.getElementsByName("comments")[0].value;
    orders_session('comments',comments);
  });
  $("textarea[name='comments_text']").blur(function(){
    var comments_text = document.getElementsByName("comments_text")[0].value;
    orders_session('comments_text',comments_text);
  });
  $("input[name='notify']").click(function(){
    var notify = document.getElementsByName("notify")[0].checked;
    notify = notify == true ? 1 : 0;
    orders_session('notify',notify);
  });
  $("input[name='notify_comments']").click(function(){
    var notify_comments = document.getElementsByName("notify_comments")[0].checked;
    notify_comments = notify_comments == true ? 1 : 0;
    orders_session('notify_comments',notify_comments);
  });
  $("input[name='update_customer_name']").blur(function(){
    var update_customer_name = document.getElementsByName("update_customer_name")[0].value;
    orders_session('update_customer_name',update_customer_name);
  });
  $("input[name='update_customer_email_address']").blur(function(){
    var update_customer_email_address = document.getElementsByName("update_customer_email_address")[0].value;
    orders_session('update_customer_email_address',update_customer_email_address);
  }); 
});
$(document).ready(function() {
   var se_status = document.getElementById('status').value;  
  $.ajax({
    url:'ajax_preorders.php?action=get_nyuuka',
    data: 'sid='+se_status, 
    type:'POST',
    dataType: 'text', 
    async: false,
    success: function(data) {
      document.getElementById('isruhe').value = data; 
    }
  });
});
//check is fill in deadline
function check_mail_product_status(pid, c_permission)
{
   var direct_single = false; 
   var select_status = document.getElementById('status').value;  
   var isruhe_value = document.getElementById('isruhe').value;  
   var ensure_date = document.getElementById('date_ensure_deadline').value; 
   ensure_date = ensure_date.replace(/(^\s*)|(\s*$)/g, ""); 
   document.getElementById("h_deadline").value = document.getElementById("date_ensure_deadline").value; 
   
   if (!direct_single) { 
   $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
data: 'current_page_name='+js_final_self, 
async : false,
success: function(data) {
var tmp_msg_arr = data.split('|||'); 
var pwd_arr = tmp_msg_arr[1].split(',');
var flag_tmp = true;
$(".once_pwd").each(function(index) {
  var input_name = $(this).attr('name');
  var input_val = $(this).val();
  var op_id  = input_name.replace(/[^0-9]/ig," ").replace(/(^\s*)|(\s*$)/g, "");;
  var tmp_str = "input[name=op_id_"+op_id+"]";
  var percent = 0;
  $.ajax({
    url: 'ajax_preorders.php?action=getpercent',
    data: 'cid='+op_id,
    type: 'POST',
    dataType: 'text',
    async : false,
    success: function(_data) {
      percent = _data/100;  
    }
    });
  var final_val = $(tmp_str).val();
  if(input_val > Math.abs(final_val*(1+percent))||input_val < Math.abs(final_val*(1-percent))){
    if(percent!=0){
      flag_tmp=false;
    }
  }
  });
  if (c_permission == 31) {
    $("input[name=update_viladate]").val('');
    _flag = true;
  } else {
    if(!flag_tmp){
      if (tmp_msg_arr[0] == '0') {
        $("input[name=update_viladate]").val('');
        _flag = true; 
      } else {
        var pwd =  window.prompt(js_once_pwd+"\r\n","");
        if (in_array(pwd,pwd_arr)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+pwd+'&url_redirect_str='+encodeURIComponent(document.forms.edit_order.action),
            async: false,
            success: function(msg_info) {
              $("input[name=update_viladate]").val(pwd);
              _flag = true; 
            }
          });
        } else {
          alert(js_once_wrong);
          $("input[name=update_viladate]").val('_false');
          $("input[name=x]").val('43');
          $("input[name=y]").val('12');
          return false;
        }
        
      }
    }else{
      if (tmp_msg_arr[0] == '0') {
        $("input[name=update_viladate]").val('');
        _flag = true;
      } else {
        var pwd =  window.prompt(js_once_pwd+"\r\n","");
        if (in_array(pwd,pwd_arr)) {
          $.ajax({
            url: 'ajax_orders.php?action=record_pwd_log',   
            type: 'POST',
            dataType: 'text',
            data: 'current_pwd='+pwd+'&url_redirect_str='+encodeURIComponent(document.forms.edit_order.action),
            async: false,
            success: function(msg_info) {
              $("input[name=update_viladate]").val(pwd);
              _flag = true; 
            }
          });
        } else {
          alert(js_once_wrong);
          $("input[name=update_viladate]").val('_false');
          $("input[name=x]").val('43');
          $("input[name=y]").val('12');
          return false;
        }
      }
    }
  }
}
});

   if (!direct_single&&_flag) {
     document.edit_order.submit(); 
   }
  }
}
//change preorders status
function check_prestatus() {
  var s_value = document.getElementById('status').value;
  $.ajax({
    url:'ajax_preorders.php?action=get_nyuuka',
    data: 'sid='+s_value, 
    type:'POST',
    dataType: 'text', 
    async: false,
    success: function(data) {
      document.getElementById('isruhe').value = data; 
    }
  });

  $.ajax({
    dataType: 'text',
    url: 'ajax_preorders.php?action=get_mail',
    data: 'sid='+s_value, 
    type:'POST',
    async: false,
    success: function(msg) {
      document.edit_order.comments.value = msg;
    }
  });
  
  $.ajax({
    dataType: 'text',
    url: 'ajax_preorders.php?action=get_mail',
    data: 'sid='+s_value+'&type=1', 
    type:'POST',
    async: false,
    success: function(t_msg) {
      document.edit_order.title.value = t_msg;
    }
  });
}  
//print out currency with format
function fmoney(s)
{
   s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(0) + "";
    var l = s.split(".")[0].split("").reverse();
     var t = '';
      for(i = 0; i < l.length; i ++ ){
            t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");
              }
       return t.split("").reverse().join("");
}
//count preorders price info
function recalc_preorder_price(oid, opd, o_str, op_str)
{
  var op_array = op_str.split('|||');
  var p_op_info = 0; 
  var op_price_str = '';
  for (var i=0; i<op_array.length; i++) {
    if (op_array[i] != '') {
      if(o_str == 'true' || document.getElementById('belong_to_option')){
        p_op_info += parseInt(document.getElementsByName('new_update_products_op_price['+op_array[i]+']')[0].value); 
        op_price_str += parseInt(document.getElementsByName('new_update_products_op_price['+op_array[i]+']')[0].value)+'|||';
      }else{
        p_op_info += parseInt(document.getElementsByName('update_products['+opd+'][attributes]['+op_array[i]+'][price]')[0].value); 
        op_price_str += parseInt(document.getElementsByName('update_products['+opd+'][attributes]['+op_array[i]+'][price]')[0].value)+'|||';
      }
    }
  } 

  var update_total_temp;
  var update_total_num = 0;
  var add_num = $("#button_add_id").val();
  var total_str = '';
  var total_price_str = '';
  for(var i = 1;i <= add_num;i++){
   
     if(document.getElementById('update_totals_'+i)){
        update_total_temp = document.getElementById('update_totals_'+i).value; 
        update_total_temp_value = update_total_temp;
        if(update_total_temp == '' || update_total_temp == '-'){update_total_temp = 0;}
        if($("#sign_"+i).val() == '0'){

          update_total_temp = 0-update_total_temp;  
        }
        update_total_temp = parseInt(update_total_temp);
        update_total_num += update_total_temp;
        total_str += i+'|||';
        total_price_str += update_total_temp_value+'|||';
     }
  }
  pro_num = document.getElementById('update_products_new_qty_'+opd).value;
  p_price = document.getElementsByName('update_products['+opd+'][p_price]')[0].value;
  p_final_price = document.getElementsByName('update_products['+opd+'][final_price]')[0].value;
  var payment_method = document.getElementsByName('payment_method')[0].value;
  $.ajax({
    type: "POST",
    data:'oid='+oid+'&opd='+opd+'&o_str='+o_str+'&op_price='+p_op_info+'&p_num='+pro_num+'&p_price='+p_price+'&p_final_price='+p_final_price+'&op_str='+op_str+'&op_price_str='+op_price_str+'&total_str='+total_str+'&total_price_str='+total_price_str+'&payment_method='+payment_method+'&session_site_id='+session_site_id,
    async:false,
    url: 'ajax_preorders.php?action=recalc_price',
    success: function(msg) {
      msg_info = msg.split('|||');
      if(o_str != 3){
        document.getElementsByName('update_products['+opd+'][final_price]')[0].value = msg_info[0];
        document.getElementById('update_products['+opd+'][final_price]').innerHTML = msg_info[7];
      }
      if(o_str != 3){
        document.getElementById('update_products['+opd+'][a_price]').innerHTML = msg_info[1];
      }else{
        document.getElementById('update_products['+opd+'][a_price]').innerHTML = msg_info[4]; 
      }
      if(o_str != 3){
        document.getElementById('update_products['+opd+'][b_price]').innerHTML = msg_info[2];
      }else{
        document.getElementById('update_products['+opd+'][b_price]').innerHTML = msg_info[5]; 
      }
      if(o_str != 3){
        document.getElementById('update_products['+opd+'][c_price]').innerHTML = msg_info[3];
      }else{
        document.getElementById('update_products['+opd+'][c_price]').innerHTML = msg_info[6]; 
      }
      document.getElementById('ot_subtotal_id').innerHTML = document.getElementById('update_products['+opd+'][c_price]').innerHTML;
      document.getElementById('handle_fee_id').innerHTML = msg_info[8]+js_final_money_symbol;
      var opd_str_value = document.getElementById('ot_subtotal_id').innerHTML;
      var opd_str_temp = opd_str_value;
      opd_str_value = opd_str_value.replace(/<.*?>/g,'');
      opd_str_value = opd_str_value.replace(/,/g,'');
      opd_str_value = opd_str_value.replace(js_final_money_symbol,'');
      opd_str_value = parseFloat(opd_str_value);
      var ot_total = 0;
      var handle_fee_id = document.getElementById('handle_fee_id').innerHTML; 
      handle_fee_id = handle_fee_id.replace(/<.*?>/g,'');
      handle_fee_id = handle_fee_id.replace(/,/g,'');
      handle_fee_id = handle_fee_id.replace(js_final_money_symbol,'');
      handle_fee_id = parseInt(handle_fee_id);  
 
      if(opd_str_temp.indexOf('color') > 0){
         
         ot_total = handle_fee_id+update_total_num-opd_str_value;
      }else{
         
         ot_total = opd_str_value+handle_fee_id+update_total_num;
      } 
       
      if(ot_total < 0){
        ot_total = Math.abs(ot_total);
        document.getElementById('ot_total_id').innerHTML = '<font color="#FF0000">'+fmoney(ot_total)+'</font>'+js_final_money_symbol;
      }else{
        document.getElementById('ot_total_id').innerHTML = fmoney(ot_total)+js_final_money_symbol; 
      } 
    }
  });
}
//count price info
function price_total()
{
      var ot_total = '';
      var ot_total_flag = false;
      var ot_subtotal_id = document.getElementById('ot_subtotal_id').innerHTML; 
      if(ot_subtotal_id.indexOf('color') > 0){
        ot_total_flag = true; 
      }
      ot_subtotal_id = ot_subtotal_id.replace(/<.*?>/g,'');
      ot_subtotal_id = ot_subtotal_id.replace(/,/g,'');
      ot_subtotal_id = ot_subtotal_id.replace(js_final_money_symbol,'');
      ot_subtotal_id= parseInt(ot_subtotal_id);
      var handle_fee_id = document.getElementById('handle_fee_id').innerHTML; 
      handle_fee_id = handle_fee_id.replace(/<.*?>/g,'');
      handle_fee_id = handle_fee_id.replace(/,/g,'');
      handle_fee_id = handle_fee_id.replace(js_final_money_symbol,'');
      handle_fee_id = parseInt(handle_fee_id);  
      var update_total_temp;
      var update_total_num = 0;
      var add_num = $("#button_add_id").val();
      for(var i = 1;i <= add_num;i++){
     
        if(document.getElementById('update_totals_'+i)){
          update_total_temp = document.getElementById('update_totals_'+i).value; 
          if(update_total_temp == '' || update_total_temp == '-'){update_total_temp = 0;}
          if($("#sign_"+i).val() == '0'){

            update_total_temp = 0-update_total_temp;  
          }
          update_total_temp = parseInt(update_total_temp);
          update_total_num += update_total_temp;
        }
      }
 
      if(ot_total_flag == false){
        ot_total = ot_subtotal_id+handle_fee_id+update_total_num;
      }else{
        ot_total = handle_fee_id+update_total_num-ot_subtotal_id; 
      }
      if(ot_total < 0){
        ot_total = Math.abs(ot_total);
        document.getElementById('ot_total_id').innerHTML = '<font color="#FF0000">'+fmoney(ot_total)+'</font>'+js_final_money_symbol;
      }else{
        document.getElementById('ot_total_id').innerHTML = fmoney(ot_total)+js_final_money_symbol; 
      } 
}
//popup calendar
function open_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    $('#toggle_open').val('1'); 
    var rules = {
           "all": {
                  "all": {
                           "all": {
                                      "all": "current_s_day",
                                }
                     }
            }};
    if ($("#date_predate").val() != '') {
      if ($("#date_predate").val() == '0000-00-00') {
        date_info_str = js_final_date;  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_predate").val().split('-'); 
      }

    } else {
      date_info_str = js_final_date;  
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
        $("#update_predate_year").val(tmp_show_date_array[0]); 
        $("#update_predate_month").val(tmp_show_date_array[1]); 
        $("#update_predate_day").val(tmp_show_date_array[2]); 
        $("#date_predate").val(tmp_show_date); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
//popup deadline calendar
function open_ensure_calendar()
{
  var is_open = $('#toggle_ensure').val(); 
  if (is_open == 0) {
    $('#toggle_ensure').val('1'); 
    var rules = {
           "all": {
                  "all": {
                           "all": {
                                      "all": "current_s_day",
                                }
                     }
            }};
    if ($("#date_ensure_deadline").val() != '') {
      if ($("#date_ensure_deadline").val() == '0000-00-00') {
        date_info_str = js_final_date;  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_ensure_deadline").val().split('-'); 
      }

    } else {
      date_info_str = js_final_date;  
      date_info = date_info_str.split('-');  
    }
    new_date = new Date(date_info[0], date_info[1]-1, date_info[2]); 
    
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#ecalendar",
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
        $("#update_ensure_year").val(tmp_show_date_array[0]); 
        $("#update_ensure_month").val(tmp_show_date_array[1]); 
        $("#update_ensure_day").val(tmp_show_date_array[2]); 
        $("#date_ensure_deadline").val(tmp_show_date); 
        $('#toggle_ensure').val('0');
        $('#toggle_ensure').next().html('<div id="ecalendar"></div>');
        var year_value = document.getElementById("update_ensure_year").value;
        orders_session('update_ensure_year',year_value);
        var month_value = document.getElementById("update_ensure_month").value;
        orders_session('update_ensure_month',month_value);
        var day_value = document.getElementById("update_ensure_day").value;
        orders_session('update_ensure_day',day_value);
      });
    });
  }
}
//check date
function is_date(dateval)
{
  var arr = new Array();
  if(dateval.indexOf("-") != -1){
    arr = dateval.toString().split("-");
  }else if(dateval.indexOf("/") != -1){
    arr = dateval.toString().split("/");
  }else{
    return false;
  }
  if(arr[0].length==4){
    var date = new Date(arr[0],arr[1]-1,arr[2]);
    if(date.getFullYear()==arr[0] && date.getMonth()==arr[1]-1 && date.getDate()==arr[2]) {
      return true;
    }
  }
  
  if(arr[2].length==4){
    var date = new Date(arr[2],arr[1]-1,arr[0]);
    if(date.getFullYear()==arr[2] && date.getMonth()==arr[1]-1 && date.getDate()==arr[0]) {
      return true;
    }
  }
  
  if(arr[2].length==4){
    var date = new Date(arr[2],arr[0]-1,arr[1]);
    if(date.getFullYear()==arr[2] && date.getMonth()==arr[0]-1 && date.getDate()==arr[1]) {
      return true;
    }
  }
 
  return false;
}

//check change date
function change_predate_date() {
  update_predate_str = $("#update_predate_year").val()+"-"+$("#update_predate_month").val()+"-"+$("#update_predate_day").val(); 
  if (!is_date(update_predate_str)) {
    alert(js_final_error_date); 
  } else {
    $("#date_predate").val(update_predate_str); 
  }
}
//check change deadline date
function change_ensure_date() {
  update_ensure_str = $("#update_ensure_year").val()+"-"+$("#update_ensure_month").val()+"-"+$("#update_ensure_day").val(); 
  if (!is_date(update_ensure_str)) {
    alert(js_final_error_date); 
  } else {
    $("#date_ensure_deadline").val(update_ensure_str); 
  }
}
