var zaiko_input_obj=document.getElementsByName("zaiko[]"); //virtual
var target_input_obj=document.getElementsByName("TARGET_INPUT[]");//Peer
var price_obj=document.getElementsByName("price[]");//sale
var error_msg='';
//click to jump specify page
function confirmg(question,url) {
  var x = confirm(question);
  if (x) {
    window.location = url;
  }
}
//all update
function all_update(c_permission){
  check_error();
  if (error_msg != '') {
    alert(error_msg);
    error_msg = '';
  }
  var product_price_list = '';
  var product_id_list = '';
  if (document.myForm1.elements['price[]']) {
    if (document.myForm1.elements['price[]'].length == null) {
      product_price_list = document.myForm1.elements['price[]'].value; 
    } else {
      for (var pu = 0; pu < document.myForm1.elements['price[]'].length; pu++) {
        product_price_list += document.myForm1.elements['price[]'][pu].value+'|||'; 
      }
    }
  }
  if (document.myForm1.elements['hidden_products_id[]']) {
    if (document.myForm1.elements['hidden_products_id[]'].length == null) {
      product_id_list = document.myForm1.elements['hidden_products_id[]'].value; 
    } else {
      for (var pm = 0; pm < document.myForm1.elements['hidden_products_id[]'].length; pm++) {
        product_id_list += document.myForm1.elements['hidden_products_id[]'][pm].value+'|||'; 
      }
    }
  }
  if (product_price_list != '') {
    product_price_list = product_price_list.substr(0, product_price_list.length-3); 
  }
  
  if (product_id_list != '') {
    product_id_list = product_id_list.substr(0, product_id_list.length-3); 
  }
  
  $.ajax({
    url: 'ajax_orders.php?action=check_list_products_profit',   
    type: 'POST',
    dataType: 'text',
    data: 'product_id_list='+product_id_list+'&product_price_list='+product_price_list, 
    async: false,
    success: function (msg_info) {
      if (msg_info != '') {
        document.myForm1.flg_up.value=0;
        alert(msg_info); 
      } else {

        $.ajax({
          url: 'ajax_orders.php?action=check_list_products_avg',   
          type: 'POST',
          dataType: 'text',
          data: 'product_id_list='+product_id_list+'&product_price_list='+product_price_list, 
          async: false,
          success: function (msg_avg) {
            if(msg_avg!=''){
              confirm_div(msg_avg,'','',c_permission,'');
            }else{
              save_permission(c_permission);
            }
          }
        });
      }
    }
  });
}
function save_permission(c_permission){
  if (confirm(c_admin_is_update)) {
    document.myForm1.flg_up.value=1;
    if (c_permission == 31) {
      window.document.myForm1.submit();
    } else {
      $.ajax({
        url: 'ajax_orders.php?action=getallpwd',   
        type: 'POST',
        dataType: 'text',
        data: 'current_page_name='+document.getElementById("hidden_page_info").value, 
        async: false,
        success: function(msg) {
          var tmp_msg_arr = msg.split('|||'); 
          var pwd_list_array = tmp_msg_arr[1].split(',');
          if (tmp_msg_arr[0] == '0') {
            window.document.myForm1.submit();
          } else {
            var input_pwd_str = window.prompt(c_admin_onetime_pwd, ''); 
            if (in_array(input_pwd_str, pwd_list_array)) {
              $.ajax({
                url: 'ajax_orders.php?action=record_pwd_log',   
                type: 'POST',
                dataType: 'text',
                data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.myForm1.action),
                async: false,
                success: function(msg_info) {
                  window.document.myForm1.submit();
                }
              }); 
            } else {
              alert(c_admin_onetime_error); 
            }
          }
        }
      });
    }
  } else {
    document.myForm1.flg_up.value=0;
    alert(c_admin_update_clear);
  }
}



//check radio
function chek_radio(cnt){
  var radio_cnt=document.getElementsByName("chk["+cnt+"]");
  var proid = document.getElementsByName("proid[]");
  for(var i=0;i < radio_cnt.length;i++){
    if(radio_cnt[i].checked == true){
      if(document.getElementById("target_"+cnt+"_"+i).innerHTML != ''){
        set_money(cnt, false, '1'); 
        $.ajax({
          type:'POST', 
          beforeSend: function(){$('body').css('cursor', 'wait');$('#wait').show();}, 
          dataType:'text',
          async:false, 
          url: 'set_ajax_dougyousya.php?products_id='+proid[cnt].value+'&dougyousya_id='+$('#radio_'+cnt+"_"+i).val(),
          success: function(msg) {
            $('body').css('cursor', '');
            setTimeout('read_space_time()', 500);
          } 
        });
      }
    }
  }   
}
//hide wait ID 
function read_space_time()
{
  $('#wait').hide(); 
}
//form submit
function cleat_set(url){
  window.document.myForm1.action = url;
  window.document.myForm1.method = "POST"; 
  window.document.myForm1.submit();
}
//jump to list_display.php
function list_display(path,cid,fullpath){
  location.href="list_display.php?cpath="+path+"&cid="+cid+'&fullpath='+fullpath;
}
//update number
function update_quantity(pid){
  nquantity = $('#real_pro_num').val();
  if (nquantity && false == /^\d+$/.test(nquantity)) {
    alert(c_admin_input_info);
    return false;
  }
  if (nquantity !== '' && nquantity !== null) {
    var is_radices = 0;
    if($('#is_radices')){
      is_radices = $('#is_radices').val();
    }
    if(is_radices == 1 ){
      var send_url="set_quantity.php?pid="+pid+"&quantity="+nquantity+"&is_radices=1";
    }else{
      var send_url="set_quantity.php?pid="+pid+"&quantity="+nquantity;
    }
    $.ajax({
      beforeSend: function(){$('body').css('cursor', 'wait');$('#wait').show();}, 
      url: send_url,
        success: function(data) {
          data_tmp_array = data.split('|||'); 
          var res_tmp_arr = data_tmp_array[0].split('<<<')
          $('#quantity_real_'+pid).html(res_tmp_arr[0]);
          for(var i=1;i<=c_admin_sites_num;i++){
            $('#edit_quantity_real_'+pid+'_'+i).html(res_tmp_arr[0]);
          }
          $('#quantity_'+pid).html(res_tmp_arr[1]);
          $('#h_edit_p_'+pid).parent().next().next().next().next().find('a').html(data_tmp_array[1]); 
          setTimeout(function(){$('body').css('cursor', '');$('#wait').hide();$('#show_popup_info').css('display', 'none');}, 500);
        }
    });
  }
}
//update number of virtual
function update_virtual_quantity(pid){
  nquantity = $('#virtual_pro_num').val();
  if (nquantity && false == /^\d+$/.test(nquantity)) {
    alert(c_admin_input_info);
    return false;
  }
  if (nquantity !== '' && nquantity !== null) {
    var send_url="set_quantity.php?pid="+pid+"&virtual_quantity="+nquantity;
    $.ajax({
      beforeSend: function(){$('body').css('cursor', 'wait');$('#wait').show();}, 
      url: send_url,
        success: function(data) {
          data_tmp_array = data.split('|||'); 
          $('#virtual_quantity_'+pid).html(data_tmp_array[0]);
          for(var i=1;i<=c_admin_sites_num;i++){
            $('#edit_virtual_quantity_'+pid+'_'+i).html(data_tmp_array[0]);
          }
          $('#h_edit_p_'+pid).parent().next().next().next().next().find('a').html(data_tmp_array[1]); 
          setTimeout(function(){$('body').css('cursor', '');$('#wait').hide();$('#show_popup_info').css('display', 'none');}, 500);
        }
    });
  }
}
//lost focus event action
function event_onblur(i){
}
//change event action
function event_onchange(i){
  var this_price=document.getElementsByName("pprice[]");
  $('#price_input_'+i).val(SBC2DBC($('#price_input_'+i).val()));
  var old_price = this_price[i-1].value;
  var new_price = $('#price_input_'+i).val();
  if (old_price != new_price) {
    $('#price_input_'+i).css('color','red');
  }else {
    $('#price_input_'+i).css('color','blue');
  }
}
//string filter
function SBC2DBC(str) {
  var arr = new Array('０','１','２','３','４','５','６','７','８','９');
  for(i in arr) {
    str = str.replace(eval("/"+arr[i]+"/g"),i);
  }
  str = str.replace(/[^\d\.]/gi,'');
  return str;
}
//read count config
function set_money(num,warning, single_type){
  if (warning ==undefined)
    {
        warning = true;
    }
  var n=num;
  var radio_cnt=document.getElementsByName("chk["+n+"]");
  if(radio_cnt.length == 0){

    var tar_ipt = document.getElementById("target_"+n+"_0").innerHTML; //peer

  }else{
    for(var i=0;i < radio_cnt.length;i++){
      if (single_type == '1') {
        if(radio_cnt[i].checked == true){
          var tar_ipt = document.getElementById("target_"+n+"_"+i).innerHTML; //peer
        }
      } else {
        if (document.getElementById("radio_"+n+"_"+i)) {
          var tar_ipt = document.getElementById("radio_"+n+"_"+i).value; //peer
        }
      }
    } 
  } 
  var increase_input_obj=$(".INCREASE_INPUT"); //industrial businesser x multiple
  var ins_ipt=increase_input_obj[n].innerHTML; 

  var set_m=0;                       //inputbox variable initialize

  if(parseInt(ins_ipt) < parseInt(tar_ipt)){
      
    var ins_anser = ( parseInt(ins_ipt) / parseInt(tar_ipt) ) * 100;
    ins_anser = 100 - ins_anser;
    if(calc.percent != '' && parseInt(ins_anser) >= calc.percent){
        if (warning){
          error_msg = calc.percent+c_admin_reset_difference+"\n";
        }
    }
    var kei = calc.keisan;  //number
    var shisoku = calc.shisoku;  //operator

    if(shisoku == "+"){
      set_m = parseInt(tar_ipt) + parseInt(kei);
    }else{
      set_m = parseInt(tar_ipt) - parseInt(kei);
    }
  }else{
    var ins_anser = ( parseInt(tar_ipt) / parseInt(ins_ipt)) * 100;
    ins_anser = 100 - ins_anser;
    if(calc.percent != '' && parseInt(ins_anser) >= calc.percent){
        if (warning){
          error_msg = calc.percent+c_admin_reset_difference+"\n";
        }
    }
    set_m=ins_ipt;
    set_m=Math.ceil(set_m);
  }
  if (set_m < 0) {
    set_m = 0;
  }
  if (single_type == '0') {
    current_tmp_pid = document.getElementsByName('hide_price[]')[n].value; 
  }
  if(typeof(tar_ipt) == 'undefined' || ins_ipt == 0) {
    if (single_type == '1') {
      price_obj[n].style.color="red";
    } else {
      document.getElementById('show_price_'+current_tmp_pid).style.color="red"; 
    }
    return;
  }
  var this_price=document.getElementsByName("pprice[]");
 
  if (single_type == '1') {
    price_obj[n].value=parseInt(set_m);
  }
    
  
  //judge price
  //compare current price and anticipated price
  //if it same character display blue otherwise character display red
  
  if(parseInt(document.getElementsByName("pprice[]")[n].value)==parseInt(set_m)){
    if (single_type == '1') {
      price_obj[n].style.color="blue";
    } else {
      document.getElementById('show_price_'+current_tmp_pid).innerHTML = '<font color="blue">'+parseInt(set_m)+'</font>'; 
    }
  }else{
    if (single_type == '1') {
      price_obj[n].style.color="red";
    } else {
      document.getElementById('show_price_'+current_tmp_pid).innerHTML = '<font color="red">'+parseInt(set_m)+'</font>'; 
    }
  }
}

var calc;
//load ajax
function ajaxLoad(path, single_type){
    var send_url="set_ajax.php?action=ajax&cPath="+path;
  $.ajax({
    url: send_url,
        success: function(data) {
        calc = eval('('+data+')');
            onload_keisan(false, single_type);
      }
    });
}
//record
function history(url,cpath,cid,action){
  var url=url+"?cpath="+cpath+"&cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800,scrollbars=yes");
}
//item name record
function oro_history(url,cid,action){
  var url=url+"?cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800,scrollbars=yes");
}
//peer name record
function dougyousya_history(url,cpath,cid,action,did,fullpath){
  var url=url+"?cPath="+cpath+"&cid="+cid+"&did="+did+"&action="+action+"&fullpath="+fullpath;
  location.href=url;
}
//count through with onload event
function onload_keisan(warning, single_type){

  var trader_input_obj=$(".TRADER_INPUT");  //industrial businesser
  var increase_input_obj=$(".INCREASE_INPUT");  //industrial businesser
  for(var i=0;i< trader_input_obj.length;i++){
      set_money(i,warning,single_type);  //sale price set
  }
}
//check error
function check_error(){

      var trader_input_obj=$(".TRADER_INPUT");  //industrial businesser
      var this_price=document.getElementsByName("pprice[]");
      var bflag=document.getElementsByName("bflag[]");
      var focus_id = '';
      var price_error = c_admin_error_price;

      for(var i=0;i< trader_input_obj.length;i++){
          $('#price_input_'+(i+1)).css('border-color','');
          $('#price_error_'+(i+1)).html('');
          $('#offset_input_'+(i+1)).css('border-color','');
          $('#offset_error_'+(i+1)).html('');
          
          var old_price = parseInt(this_price[i].value);
          var new_price = parseInt($('#price_input_'+(i+1)).val());
          if (calc.percent != '' && calc.percent != 0 && calc.percent != null) {
          if (new_price > old_price) {
            if( ((new_price - old_price) / old_price) * 100 >= calc.percent ) {
                error_msg = calc.percent+c_admin_reset_difference+"\n";
                $('#price_input_'+(i+1)).css('border-color','red');
                if (focus_id == '') {
                    focus_id = '#price_input_'+(i+1);
                }
            }
          } else {
            if( ((old_price - new_price) / new_price) * 100 >= calc.percent ) {
                error_msg = calc.percent+c_admin_reset_difference+"\n";
                $('#price_input_'+(i+1)).css('border-color','red');
                if (focus_id == '') {
                    focus_id = '#price_input_'+(i+1);
                }
            }
          }
          }
      }
      if (focus_id != '') {
        $(focus_id).focus();
      }
}
//set new price
function set_new_price(pid, cnt) {
  default_price = $('#h_edit_p_'+pid).html(); 
  nquantity = $('#new_confirm_price').val();
  c_ele = $('#h_edit_p_'+pid).parent(); 
  if (nquantity && false == /^\d+$/.test(nquantity)) {
    alert(c_admin_input_info);
    return false;
  }
  if (nquantity !== '' && nquantity !== null) {
    $.ajax({
      type:'POST', 
      async:false, 
      url: 'ajax_orders.php?action=check_products_profit',
      dataType:'text',
      data:'products_id='+pid+"&new_price="+nquantity,
      success: function(msg_info) {
        if (msg_info != '') {
          $("#new_price_button").attr("id", 'tmp_new_price_button'); 
          alert(msg_info);
          setTimeout('$("#tmp_new_price_button").attr("id", "new_price_button")', 100);
        } else {
    $.ajax({
      type:'POST', 
      async:false, 
      url: 'ajax_orders.php?action=check_products_avg',
      dataType:'text',
      data:'products_id='+pid+"&new_price="+nquantity,
      success: function(msg_avg) {
        var save_flag = false;
        if(msg_avg!=''){
          confirm_div(msg_avg,cnt,pid,'','');
        }else{
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
              for(var i=1;i<=c_admin_sites_num;i++){
              
                $("#edit_p_"+pid+"_"+i).html(msg_array[0]);
                $("#show_price_"+pid+"_"+i).html(msg_array[1]);
              }
              set_money(cnt, false, '1'); 
              setTimeout(function(){$('body').css('cursor', '');$('#wait').hide();$('#show_popup_info').css('display', 'none');}, 500);
            }
          });

        }
       }
      });
	location = location;
        }
      }
    }); 
  }
}
