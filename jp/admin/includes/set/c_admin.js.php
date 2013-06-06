var zaiko_input_obj=document.getElementsByName("zaiko[]");<?php //虚拟 ?>
var target_input_obj=document.getElementsByName("TARGET_INPUT[]");<?php//同行 ?>
var price_obj=document.getElementsByName("price[]");<?php//特价 ?>
var error_msg='';
<?php //点击确认跳转到指定页面 ?>
function confirmg(question,url) {
  var x = confirm(question);
  if (x) {
    window.location = url;
  }
}
<?php //全部更新 ?>
function all_update(c_permission){
  check_error();
  if (error_msg != '') {
    alert(error_msg);
    error_msg = '';
  }
  var flg=confirm("<?php echo JS_TEXT_C_ADMIN_IS_UPDATE;?>");
  if(flg){
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
            var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
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
              alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
            }
          }
        }
      });
    }
  }else{
    document.myForm1.flg_up.value=0;
    alert("<?php echo JS_TEXT_C_ADMIN_UPDATE_CLEAR;?>");
  }
}
<?php //检查 radio ?>
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
<?php //隐藏wait ID ?>
function read_space_time()
{
  $('#wait').hide(); 
}
<?php //表单提交 ?>
function cleat_set(url){
  window.document.myForm1.action = url;
  window.document.myForm1.method = "POST"; 
  window.document.myForm1.submit();
}
<?php //跳转到list_display.php页 ?>
function list_display(path,cid,fullpath){
  location.href="list_display.php?cpath="+path+"&cid="+cid+'&fullpath='+fullpath;
}
<?php //更新数量 ?>
function update_quantity(pid){
  nquantity = $('#real_pro_num').val();
  if (nquantity && false == /^\d+$/.test(nquantity)) {
    alert('<?php echo JS_TEXT_C_ADMIN_INPUT_INFO;?>');
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
          $('#quantity_'+pid).html(res_tmp_arr[1]);
          $('#h_edit_p_'+pid).parent().next().next().next().next().find('a').html(data_tmp_array[1]); 
          setTimeout(function(){$('body').css('cursor', '');$('#wait').hide();$('#show_popup_info').css('display', 'none');}, 500);
        }
    });
  }
}
<?php //更新虚拟数量 ?>
function update_virtual_quantity(pid){
  nquantity = $('#virtual_pro_num').val();
  if (nquantity && false == /^\d+$/.test(nquantity)) {
    alert('<?php echo JS_TEXT_C_ADMIN_INPUT_INFO;?>');
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
          $('#h_edit_p_'+pid).parent().next().next().next().next().find('a').html(data_tmp_array[1]); 
          setTimeout(function(){$('body').css('cursor', '');$('#wait').hide();$('#show_popup_info').css('display', 'none');}, 500);
        }
    });
  }
}
<?php //失去焦点时触发事件 ?>
function event_onblur(i){
}
<?php //变化时触发事件?>
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
<?php //过滤字符串 ?>
function SBC2DBC(str) {
  var arr = new Array('０','１','２','３','４','５','６','７','８','９');
  for(i in arr) {
    str = str.replace(eval("/"+arr[i]+"/g"),i);
  }
  str = str.replace(/[^\d\.]/gi,'');
  return str;
}
<?php//读取计算设定 ?>
function set_money(num,warning, single_type){
  if (warning ==undefined)
    {
        warning = true;
    }
  var n=num;
  var radio_cnt=document.getElementsByName("chk["+n+"]");
  if(radio_cnt.length == 0){

    var tar_ipt = document.getElementById("target_"+n+"_0").innerHTML;<?php//同行 ?>

  }else{
    for(var i=0;i < radio_cnt.length;i++){
      if (single_type == '1') {
        if(radio_cnt[i].checked == true){
          var tar_ipt = document.getElementById("target_"+n+"_"+i).innerHTML;<?php//同行 ?>
        }
      } else {
        if (document.getElementById("radio_"+n+"_"+i)) {
          var tar_ipt = document.getElementById("radio_"+n+"_"+i).value;<?php//同行 ?>
        }
      }
    } 
  } 
  var increase_input_obj=$(".INCREASE_INPUT");<?php//工商业者 x 倍数 ?>
  var ins_ipt=increase_input_obj[n].innerHTML; 

  var set_m=0;                       <?php //在网站输入框里设置值 变量初始化 ?>

  if(parseInt(ins_ipt) < parseInt(tar_ipt)){
      
    var ins_anser = ( parseInt(ins_ipt) / parseInt(tar_ipt) ) * 100;
    ins_anser = 100 - ins_anser;
    if(calc.percent != '' && parseInt(ins_anser) >= calc.percent){
        if (warning){
          error_msg = calc.percent+"<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>\n";
        }
    }
    var kei = calc.keisan;<?php //数字 ?>
    var shisoku = calc.shisoku;<?php //运算符 ?>

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
          error_msg = calc.percent+"<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>\n";
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
    
  <?php 
  //价格的判定
  //现在的价格和预计更新的价格相比较
  //如果一致的话，文字蓝色显示，不一致的话，文字红色显示
  ?>
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
<?php //加载AJAX ?>
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
<?php //履历  ?>
function history(url,cpath,cid,action){
  var url=url+"?cpath="+cpath+"&cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800,scrollbars=yes");
}
<?php //商品名称履历 ?> 
function oro_history(url,cid,action){
  var url=url+"?cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800,scrollbars=yes");
}
<?php //同行名称履历 ?>
function dougyousya_history(url,cpath,cid,action,did,fullpath){
  var url=url+"?cPath="+cpath+"&cid="+cid+"&did="+did+"&action="+action+"&fullpath="+fullpath;
  location.href=url;
}
<?php //通过onload事件计算 ?>
function onload_keisan(warning, single_type){

  var trader_input_obj=$(".TRADER_INPUT");<?php //工商业者?>
  var increase_input_obj=$(".INCREASE_INPUT");<?php //工商业者?>
  for(var i=0;i< trader_input_obj.length;i++){
      set_money(i,warning,single_type);<?php //特価価格設定?>
  }
}
<?php //检查错误 ?>
function check_error(){

      var trader_input_obj=$(".TRADER_INPUT");<?php //工商业者?>
      var this_price=document.getElementsByName("pprice[]");
      var bflag=document.getElementsByName("bflag[]");
      var focus_id = '';
      var price_error = '<?php echo JS_TEXT_C_ADMIN_ERROR_PRICE;?>';

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
                error_msg = calc.percent+"<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>\n";
                $('#price_input_'+(i+1)).css('border-color','red');
                if (focus_id == '') {
                    focus_id = '#price_input_'+(i+1);
                }
            }
          } else {
            if( ((old_price - new_price) / new_price) * 100 >= calc.percent ) {
                error_msg = calc.percent+"<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>\n";
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
<?php //设置新的价格 ?>
function set_new_price(pid, cnt) {
  default_price = $('#h_edit_p_'+pid).html(); 
  nquantity = $('#new_confirm_price').val();
  c_ele = $('#h_edit_p_'+pid).parent(); 
  if (nquantity && false == /^\d+$/.test(nquantity)) {
    alert('<?php echo JS_TEXT_C_ADMIN_INPUT_INFO;?>');
    return false;
  }
  if (nquantity !== '' && nquantity !== null) {
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
  }
}
