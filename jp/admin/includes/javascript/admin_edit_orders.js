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
  element_boder.style.cssText = 'margin: 0 auto; line-height: 1.4em;width:500px;background-color: rgb(255,255,255)';
  ok_input_html =  '<input type="button" id="alert_div_submit" onclick=\'save_div_action()\' value="'+js_ed_orders_text_ok+'">';
  clear_input_html = '<input type="button" onclick="clear_confirm_div()" value="'+js_ed_orders_text_clear+'">';
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
    edit_order_weight();
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
//check delivery time
function date_time(){
    var fetch_year = document.getElementById('fetch_year').value; 
    var fetch_month = document.getElementById('fetch_month').value;
    var fetch_day = document.getElementById('fetch_day').value;
    var date_time = parseInt(js_ed_orders_date_time);
    var date_hour = parseInt(js_ed_orders_date_hour);
    var date_time_value = parseInt(fetch_year+fetch_month+fetch_day);
    var start_hour = document.getElementById('hour').value;
    var start_min = document.getElementById('min').value;
    var end_min = document.getElementById('min_1').value;
    var start_hour_str = parseInt(start_hour+start_min+end_min);
    if(date_time_value < date_time || (date_time_value == date_time && start_hour_str < date_hour)){
      if(confirm(js_ed_orders_date_error)){
        return true;
      }else{
        return false; 
      }
    }
    return true;
}
//check order item number
function products_num_check(orders_products_list_id,products_name,products_list_id){

    var _end = $("#mail_title_status").val();
    if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
    }else{
      if(confirm(js_ed_orders_title_changed)){
      }else{
        return false;
      }
    }
    var products_error = true;
    var products_array = new Array();
    products_array = orders_products_list_id.split('|||');
    var products_list_str = '';
    var products_temp;
    for(var x in products_array){
      products_temp = $("#update_products_new_qty_"+products_array[x]).val(); 
      products_list_str += products_temp+'|||';
    }
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
      data:"c_comments="+$('#c_comments').val()+"&o_id="+js_ed_orders_oid+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end+'&c_payment='+payment_str+'&c_name_info='+document.getElementsByName("update_customer_name")[0].value+'&c_mail_info='+document.getElementsByName("update_customer_email_address")[0].value+'&c_comment_info='+document.getElementsByName("comments_text")[0].value+'&is_customized_fee='+is_cu_single,
      async: false,
      url:'ajax_orders.php?action=check_edit_order_variable_data',
      success: function(msg_info) {
        if (msg_info != '') {
          products_error = false;
          alert(msg_info); 
        } else {
          $.ajax({
            type: "POST",
            data: 'products_list_id='+products_list_id+'&products_list_str='+products_list_str+'&products_name='+products_name+'&orders_products_list_id='+orders_products_list_id+'&products_diff=1',
            async:false,
            url: 'ajax_orders.php?action=products_num',
            success: function(msg) {
              if(msg != ''){
                if(confirm(msg+"\n\n"+js_ed_orders_product_num)){
                  products_error = true;
                }else{
                  products_error = false;
                }
              }else{  
                products_error = true;
              }         
            }
          });   
        }
      }
    });  
    return products_error;
}
//check order item weight 
function submit_check_con(){
  var find_input_name = ''; 
  var reg_info = new RegExp("update_products\\[[0-9]+\\]\\[p_price\\]"); 
  var reg_new_info = new RegExp("update_products\\[o_[0-9]+\\]\\[p_price\\]"); 
  var reg_num_info = new RegExp("update_products\\[[0-9]+\\]\\[qty\\]"); 
  var next_find_input_name = ''; 
  var price_list_str = '';
  var hidden_list_str = '';
  var num_list_str = ''; 
  $('#ctable').find('input').each(function() {
    if ($(this).attr('type') == 'text') {
      find_input_name = $(this).attr('name'); 
      if (reg_info.test(find_input_name)) {
        price_list_str += $(this).val()+'|||'; 
        hidden_list_str += $(this).next().val()+'|||'; 
        num_list_str += $(this).parent().prev().prev().prev().prev().find('input[type=text]').val()+'|||';
      }
      if (reg_new_info.test(find_input_name)) {
        price_list_str += $(this).val()+'|||'; 
        hidden_list_str += 'o_'+$(this).next().val()+'|||'; 
        num_list_str += $(this).parent().prev().prev().prev().prev().find('input[type=text]').val()+'|||';
      }
    }
  });
  if (price_list_str != '') {
    price_list_str = price_list_str.substr(0, price_list_str.length-3);
    hidden_list_str = hidden_list_str.substr(0, hidden_list_str.length-3);
    num_list_str = num_list_str.substr(0, num_list_str.length-3);
    $.ajax({
      url: 'ajax_orders.php?action=check_order_products_profit',
      type: 'POST',
      dataType: 'text',
      data: 'products_list_str='+hidden_list_str+'&price_list_str='+price_list_str+'&num_list_str='+num_list_str,
      async: false,
      success: function (msg_info) {
        if (msg_info != '') {
          if (confirm(msg_info)) {
            confirm_div_init(hidden_list_str,price_list_str,num_list_str);
          } 
        } else {
          confirm_div_init(hidden_list_str,price_list_str,num_list_str);
        } 
      }
    }); 
  } else {
    edit_order_weight();
  }
}
function confirm_div_init(hidden_list_str,price_list_str,num_list_str){
  $.ajax({
    url: 'ajax_orders.php?action=check_order_products_avg',
    type: 'POST',
    dataType: 'text',
    data: 'language_id='+js_ed_orders_languages_id+'&site_id='+js_ed_orders_site_id+'&products_list_str='+hidden_list_str+'&price_list_str='+price_list_str+'&num_list_str='+num_list_str,
    async: false,
    success: function (msg_info) {
      if (msg_info != '') {
        confirm_div(msg_info);
      } else {
        edit_order_weight();
      } 
    }
  }); 
}
function edit_order_weight(){
   var options = {
     url: 'ajax_orders_weight.php?action=edit_orders&oID='+js_ed_orders_oid,
     type:  'POST',
     success: function(data) {
       if(data != ''){
         if(confirm(data)){
           submitChk(js_ed_orders_npermission); 
         }
       }else{
         submitChk(js_ed_orders_npermission); 
       } 
     }
   };
   $('#edit_order_id').ajaxSubmit(options);
}
//plus or minus sign
function sign(num){

  var sign = '<select id="sign_'+num+'" name="sign_value_'+num+'" onchange="price_total(\''+js_ed_orders_money_symbol+'\');orders_session(\'sign_'+num+'\',this.value);">';
  sign += '<option value="1">+</option>';
  sign += '<option value="0">-</option>';
  sign += '</select>';
  return sign;
}
//add inputbox
function add_option(ele){
    var add_num = $("#button_add_id").val();
    add_num = parseInt(add_num);
    orders_session('orders_totals',add_num+1);
    $("#button_add_id").val(add_num+1);
    add_num++; 
    var add_str = '';

    add_str += '<tr><td class="smallText" align="left">&nbsp;</td>'
      +'<td class="smallText" align="right" style="min-width:188px;"><input style="text-align:right;" value="" size="'+$("#text_len").val()+'" name="update_totals['+add_num+'][title]" onkeyup="price_total(\''+js_ed_orders_money_symbol+'\');">:'
            +'</td><td class="smallText" align="right">'+sign(add_num)+'<input style="text-align:right;" id="update_total_'+add_num+'" value="" size="6" onkeyup="clearNewLibNum(this);price_total(\''+js_ed_orders_money_symbol+'\');" name="update_totals['+add_num+'][value]"><input type="hidden" name="update_totals['+add_num+'][class]" value="ot_custom"><input type="hidden" name="update_totals['+add_num+'][total_id]" value="0">'+js_ed_orders_money_symbol+'</td>'
            +'<td><b><img height="17" width="1" border="0" alt="" src="images/pixel_trans.gif"></b></td></tr>';

    $("#point_id").parent().parent().before(add_str);
  }
//generate delivery begining time hours list
function check_hour(value){
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var min = document.getElementById('min');
  var min_value = min.value;
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var min_end = document.getElementById('min_end');
  var min_end_value = min_end.value;


  if(parseInt(value) >= parseInt(hour_1.value)){ 
    hour_1.options.length = 0;
    value = parseInt(value);
    for(h_i = value;h_i <= 23;h_i++){
      h_i_str = h_i < 10 ? '0'+h_i : h_i;
      hour_1.options[hour_1.options.length]=new Option(h_i_str,h_i_str,h_i_str==value); 
    }

    if(parseInt(min_end_value) < parseInt(min_value)){
      min_end.options.length = 0;
      min_value = parseInt(min_value);
      for(m_i = min_value;m_i <= 5;m_i++){
        min_end.options[min_end.options.length]=new Option(m_i,m_i,m_i==min_value); 
      }
    }

    if(parseInt(min_end_1_value) < parseInt(min_1_value)){
      min_end_1.options.length = 0;
      min_1_value = parseInt(min_1_value);
      for(m_i_1 = min_1_value;m_i_1 <= 9;m_i_1++){
        min_end_1.options[min_end_1.options.length]=new Option(m_i_1,m_i_1,m_i_1==min_1_value); 
      }
    }
  }else{

    hour_1.options.length = 0;
    value = parseInt(value);
    for(h_i = value;h_i <= 23;h_i++){
      h_i_str = h_i < 10 ? '0'+h_i : h_i;
      hour_1.options[hour_1.options.length]=new Option(h_i_str,h_i_str,h_i_str==hour_1_value); 
    }
    min_end.options.length = 0;
    min_value = parseInt(min_value);
    for(m_i = 0;m_i <= 5;m_i++){
      min_end.options[min_end.options.length]=new Option(m_i,m_i,m_i==min_end_value); 
    }

    min_end_1.options.length = 0;
    min_1_value = parseInt(min_1_value);
    for(m_i_1 = 0;m_i_1 <= 9;m_i_1++){
      min_end_1.options[min_end_1.options.length]=new Option(m_i_1,m_i_1,m_i_1==min_end_1_value); 
    } 
  }
}
//generate delivery begining time tens minutes list
function check_min(value){
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var min_end = document.getElementById('min_end');
  var min_end_value = min_end.value;
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;
   
  if(parseInt(value) >= parseInt(min_end_value) && parseInt(hour.value) >= parseInt(hour_1.value)){ 
    min_end.options.length = 0;
    value = parseInt(value);
    for(mi_i = value;mi_i <= 5;mi_i++){
      min_end.options[min_end.options.length]=new Option(mi_i,mi_i,mi_i==value); 
    }
    min_end_1.options.length = 0;
    for(mi_i_end = min_1_value;mi_i_end <= 9;mi_i_end++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i_end,mi_i_end,mi_i_end==min_end_1_value); 
    }
  }else if(parseInt(value) <  parseInt(min_end_value) && parseInt(hour.value) >= parseInt(hour_1.value)){
   min_end.options.length = 0;
    value = parseInt(value);
    for(mi_i = value;mi_i <= 5;mi_i++){
      min_end.options[min_end.options.length]=new Option(mi_i,mi_i,mi_i==min_end_value); 
    }
    min_end_1.options.length = 0;
    for(mi_i_end = 0;mi_i_end <= 9;mi_i_end++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i_end,mi_i_end,mi_i_end==min_end_1_value); 
    }
  }
}
//generate delivery begining time units of minutes list
function check_min_1(value){
  var min = document.getElementById('min');
  var min_value = min.value;
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var min_end = document.getElementById('min_end');
  var min_end_value = min_end.value;
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;
   
  if(parseInt(value) >= parseInt(min_end_1_value) && parseInt(hour.value) >= parseInt(hour_1.value) && parseInt(min.value) >= parseInt(min_end.value)){ 
    min_end_1.options.length = 0;
    value = parseInt(value);
    for(mi_i = value;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==value); 
    }
  }else if(parseInt(value) < parseInt(min_end_1_value) && parseInt(hour.value) >= parseInt(hour_1.value) && parseInt(min.value) >= parseInt(min_end.value)){
   min_end_1.options.length = 0;
    value = parseInt(value);
    for(mi_i = value;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
    }

  }
}
//generate delivery begining time hours list
function check_hour_1(value){
  var min = document.getElementById('min');
  var min_value = min.value;
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value;
  var min_end = document.getElementById('min_end');
  var min_end_value = min_end.value;
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;

  
  if(hour_value == value){ 
    min_end.options.length = 0;
    min_value = parseInt(min_value);
    for(mi_i = min_value;mi_i <= 5;mi_i++){
      min_end.options[min_end.options.length]=new Option(mi_i,mi_i,mi_i==min_1_value); 
    }
    if(min_end_value <= min_value ){
      min_end_1.options.length = 0;
      min_1_value = parseInt(min_1_value);
      for(mi_i = min_1_value;mi_i <= 9;mi_i++){
        min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
      }
    }else{
      min_end_1.options.length = 0;
      min_1_value = parseInt(min_1_value);
      for(mi_i = 0;mi_i <= 9;mi_i++){
        min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
      } 
    }
  }else{

    min_end.options.length = 0;
    min_value = parseInt(min_value);
    for(mi_i = 0;mi_i <= 5;mi_i++){
      min_end.options[min_end.options.length]=new Option(mi_i,mi_i,mi_i==min_1_value); 
    }
    min_end_1.options.length = 0;
    min_1_value = parseInt(min_1_value);
    for(mi_i = 0;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
    }
    
  }
}
//generate delivery begining time tens of hours list
function check_end_min(value){
  var min = document.getElementById('min');
  var min_value = min.value;
  var min_1 = document.getElementById('min_1');
  var min_1_value = min_1.value; 
  var min_end_1 = document.getElementById('min_end_1');
  var min_end_1_value = min_end_1.value;
  var hour = document.getElementById('hour');
  var hour_value = hour.value;
  var hour_1 = document.getElementById('hour_1');
  var hour_1_value = hour_1.value;
  
  if(parseInt(value) == parseInt(min_value) && parseInt(hour.value) == parseInt(hour_1.value)){ 
    min_end_1.options.length = 0;
    min_1_value = parseInt(min_1_value);
    for(mi_i = min_1_value;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
    }
  }else{
    min_end_1.options.length = 0;
    for(mi_i = 0;mi_i <= 9;mi_i++){
      min_end_1.options[min_end_1.options.length]=new Option(mi_i,mi_i,mi_i==min_end_1_value); 
    }    
  }
}
//change bill delivery address show
function billing_address_show(){
  
  var style = $("#billing_address_show_id").css("display");
  if(style == 'none'){
    $("#billing_address_show_id").show(); 
    $("#billing_address_font").html(js_ed_orders_add_info_hide);
 
  }else{

    $("#billing_address_show_id").hide();
    $("#billing_address_font").html(js_ed_orders_add_info_show);
  }
}
//bills delivery address list
function  billing_address_list(){

  for(x in edit_orders_arr_list){
   if(document.getElementById("billing_"+x)){ 
     var op_list = document.getElementById("billing_"+x);
     if(edit_orders_country_fee_id == 'billing_'+x){
      billing_check(edit_orders_arr_list[x]);
    }else if(edit_orders_country_area_id == 'billing_'+x){
      billing_country_check(document.getElementById(billing_country_fee_id).value,edit_orders_arr_list[x]);
     
    }else if(edit_orders_country_city_id == 'billing_'+x){
      billing_country_area_check(document.getElementById(billing_country_area_id).value,edit_orders_arr_list[x]);
    }else{
      op_list.style.color = '#000';
      $("#billing_"+x).val(edit_orders_arr_list[x]);
    }
    
   }
  }
}
//hide pay method additional information
  function hidden_payment(num){
   if(document.edit_order){
     var idx = document.edit_order.elements["payment_method"].selectedIndex;
     var CI = document.edit_order.elements["payment_method"].options[idx].value;
     $(".rowHide").hide();
     $(".rowHide").find("input").attr("disabled","true");
     $(".rowHide_"+CI).show();
     $(".rowHide_"+CI).find("input").removeAttr("disabled");
     if(CI == js_ed_orders_pay_type_str){
       $("#handle_fee_id").html(js_ed_orders_handle_money_symbol);
     }else{
       $("#handle_fee_id").html(0+js_ed_orders_money_symbol); 
     }
     if(num == 0){
       price_total(js_ed_orders_money_symbol);
     }
   }
  }
//open calendar

function open_calendar()
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
        date_info_str = js_ed_orders_calendar_time;  
        date_info = date_info_str.split('-');  
      } else {
        date_info = $("#date_orders").val().split('-'); 
      }
    } else {
                      //mm-dd-yyyy || mm/dd/yyyy
      date_info_str = js_ed_orders_calendar_time;  
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
        $("#fetch_year").val(tmp_show_date_array[0]); 
        $("#fetch_month").val(tmp_show_date_array[1]); 
        $("#fetch_day").val(tmp_show_date_array[2]); 
        $("#date_orders").val(tmp_show_date); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
//check date is right
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
//copy the date of delivery time to hide field
function change_fetch_date() {
  fetch_date_str = $("#fetch_year").val()+"-"+$("#fetch_month").val()+"-"+$("#fetch_day").val(); 
  if (!is_date(fetch_date_str)) {
    alert(js_ed_orders_input_right_date); 
  } else {
    $("#date_orders").val(fetch_date_str); 
  }
}
