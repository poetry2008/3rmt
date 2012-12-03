var zaiko_input_obj=document.getElementsByName("zaiko[]");<?php //架空 ?>
var target_input_obj=document.getElementsByName("TARGET_INPUT[]");<?php//同業者 ?>
var price_obj=document.getElementsByName("price[]");<?php//特別価格 ?>
var error_msg='';

function confirmg(question,url) {
  var x = confirm(question);
  if (x) {
    window.location = url;
  }
}

function all_update(){
  check_error();
  if (error_msg != '') {
    alert(error_msg);
    error_msg = '';
  }
  var flg=confirm("<?php echo JS_TEXT_C_ADMIN_IS_UPDATE;?>");
  if(flg){
    document.myForm1.flg_up.value=1;
    window.document.myForm1.submit();
  }else{
    document.myForm1.flg_up.value=0;
    alert("<?php echo JS_TEXT_C_ADMIN_UPDATE_CLEAR;?>");
  }
}

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

function read_space_time()
{
  $('#wait').hide(); 
}

function cleat_set(url){
  window.document.myForm1.action = url;
  window.document.myForm1.method = "POST"; 
  window.document.myForm1.submit();
}

function list_display(path,cid,fullpath){
  location.href="list_display.php?cpath="+path+"&cid="+cid+'&fullpath='+fullpath;
}

function update_quantity(pid){
  oquantity = $('#quantity_'+pid).html();
  pname = $('#products_name_'+pid).html();
  nquantity = prompt(pname+"\n<?php echo JS_TEXT_C_ADMIN_REAL_QUANTITY_INFO_START;?>"+oquantity+"\n<?php echo JS_TEXT_C_ADMIN_REAL_QUANTITY_INFO_END;?>", oquantity);
  //alert(nquantity);
  if (nquantity && false == /^\d+$/.test(nquantity)) {
    alert('<?php echo JS_TEXT_C_ADMIN_INPUT_INFO;?>');
    return false;
  }
  if (nquantity !== '' && nquantity !== null) {
  var send_url="set_quantity.php?pid="+pid+"&quantity="+nquantity;
  $.ajax({
    beforeSend: function(){$('body').css('cursor', 'wait');$('#wait').show();}, 
    url: send_url,
        success: function(data) {
            $('#quantity_'+pid).html(data)
            $('body').css('cursor', ''); 
            $('#wait').hide(); 
      }
    });
  }
}

function update_virtual_quantity(pid){
  oquantity = $('#virtual_quantity_'+pid).html();
  pname = $('#products_name_'+pid).html();
  nquantity = prompt(pname+"\n<?php echo JS_TEXT_C_ADMIN_VIRTUAL_QUANTITY_INFO_START;?>"+oquantity+"\n<?php echo JS_TEXT_C_ADMIN_VIRTUAL_QUANTITY_INFO_END;?>", oquantity);
  //alert(nquantity);
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
            $('#virtual_quantity_'+pid).html(data)
            $('body').css('cursor', ''); 
            $('#wait').hide(); 
      }
    });
  }
}

function event_onblur(i){
  /*
  var this_price=document.getElementsByName("this_price[]");

  $('#price_input_'+i).css('border-color','');
  var old_price = this_price[i-1].value;
  var new_price = $('#price_input_'+i).val();
  if (calc.percent != '' && calc.percent != 0 && calc.percent != null) {
      if (new_price > old_price) {
        if( ((new_price - old_price) / old_price) * 100 >= calc.percent ) {
            $('#price_input_'+i).css('border-color','red');
            if( confirm(calc.percent+"<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>") ) {
                setTimeout(function(){$('#price_input_'+i).focus()}, 100);
            }
        }
      }
  }*/
  /*if (old_price != new_price) {
    $('#price_input_'+i).css('color','red');
  }*/
}

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
function SBC2DBC(str) {
  var arr = new Array('０','１','２','３','４','５','６','７','８','９');
  for(i in arr) {
    str = str.replace(eval("/"+arr[i]+"/g"),i);
  }
  str = str.replace(/[^\d\.]/gi,'');
  return str;
}
<?php//計算設定読み込み ?>
function set_money(num,warning, single_type){
  if (warning ==undefined)
    {
        warning = true;
    }
  var n=num;
  var radio_cnt=document.getElementsByName("chk["+n+"]");
  if(radio_cnt.length == 0){

    var tar_ipt = document.getElementById("target_"+n+"_0").innerHTML;<?php//同業者 ?>

  }else{
    for(var i=0;i < radio_cnt.length;i++){
      if (single_type == '1') {
        if(radio_cnt[i].checked == true){
          var tar_ipt = document.getElementById("target_"+n+"_"+i).innerHTML;<?php//同業者 ?>
        }
      } else {
        if (document.getElementById("radio_"+n+"_"+i)) {
          var tar_ipt = document.getElementById("radio_"+n+"_"+i).value;<?php//同業者 ?>
        }
      }
    } 
  } 
  var increase_input_obj=$(".INCREASE_INPUT");<?php//業者 x 倍数 ?>
  var ins_ipt=increase_input_obj[n].innerHTML; 

  var set_m=0;                       <?php //サイト入力フォームに値を設置変数初期化 ?>

  if(parseInt(ins_ipt) < parseInt(tar_ipt)){
      
    var ins_anser = ( parseInt(ins_ipt) / parseInt(tar_ipt) ) * 100;
    ins_anser = 100 - ins_anser;
    if(calc.percent != '' && parseInt(ins_anser) >= calc.percent){
        if (warning){
          error_msg = calc.percent+"<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>\n";
        }
    }
    var kei = calc.keisan;<?php //数字 ?>
    var shisoku = calc.shisoku;<?php //演算子 ?>

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
  //if(typeof(tar_ipt) == 'undefined')return;
  //var price_n = n + 1;
  //var price_obj=document.getElementById("price_input_"+ price_n);<?php //サイトインプット?>
  var this_price=document.getElementsByName("pprice[]");
 
  if (single_type == '1') {
    price_obj[n].value=parseInt(set_m);
  }
    
  <?php 
  //価格の判定
  //現在の価格と更新予定の価格を比較
  //一致しているなら文字の色を青、不一致なら赤にする
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
//var spprice=document.getElementsByName("pprice[]");

function history(url,cpath,cid,action){
  var url=url+"?cpath="+cpath+"&cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800,scrollbars=yes");
}

function oro_history(url,cid,action){
  var url=url+"?cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800,scrollbars=yes");
}

function dougyousya_history(url,cpath,cid,action,did,fullpath){
  var url=url+"?cPath="+cpath+"&cid="+cid+"&did="+did+"&action="+action+"&fullpath="+fullpath;
  location.href=url;
  //window.open(url,'ccc',"width=1000,height=800,scrollbars=yes");
}

function onload_keisan(warning, single_type){

  var trader_input_obj=$(".TRADER_INPUT");<?php //業者?>
  var increase_input_obj=$(".INCREASE_INPUT");<?php //業者?>
  for(var i=0;i< trader_input_obj.length;i++){
      set_money(i,warning,single_type);<?php //特価価格設定?>
  }
}
function check_error(){

      var trader_input_obj=$(".TRADER_INPUT");<?php //業者?>
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
          //alert(old_price + '|' + new_price);
          if (calc.percent != '' && calc.percent != 0 && calc.percent != null) {
          if (new_price > old_price) {
            if( ((new_price - old_price) / old_price) * 100 >= calc.percent ) {
                error_msg = calc.percent+"<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>\n";
                //error_msg = price_error;
                $('#price_input_'+(i+1)).css('border-color','red');
                //$('#price_error_'+(i+1)).html('<img src="images/icons/error_1.gif" title="'+calc.percent+'<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>">');
                if (focus_id == '') {
                    focus_id = '#price_input_'+(i+1);
                }
            }
          } else {
            if( ((old_price - new_price) / new_price) * 100 >= calc.percent ) {
                error_msg = calc.percent+"<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>\n";
                //error_msg = price_error;
                $('#price_input_'+(i+1)).css('border-color','red');
                //$('#price_error_'+(i+1)).html('<img src="images/icons/error_1.gif" title="'+calc.percent+'<?php echo JS_TEXT_C_ADMIN_RESET_DIFFERENCE;?>">');
                if (focus_id == '') {
                    focus_id = '#price_input_'+(i+1);
                }
            }
          }
          }
          /*
          if (bflag[i].value == 1) {
            if (parseFloat($('#offset_input_' + (i+1)).val()) > 0) {
              error_msg = price_error;
              $('#offset_input_'+(i+1)).css('border-color','red');
              $('#offset_error_'+(i+1)).append('<img src="images/icons/error_2.gif" title="<?php echo JS_TEXT_C_ADMIN_SPECIAL_PRICE;?>">');
              if (focus_id == '') {
                  focus_id = '#price_input_'+(i+1);
              }
            }
          } else {
            //alert(parseFloat($('#offset_input_' + (i+1)).val()));
            if (parseFloat($('#offset_input_' + (i+1)).val()) < 0) {
              error_msg = price_error;
              $('#offset_input_'+(i+1)).css('border-color','red');
              $('#offset_error_'+(i+1)).append('<img src="images/icons/error_3.gif" title="<?php echo JS_TEXT_C_ADMIN_SPECIAL_PRICE_MORE;?>">');
              if (focus_id == '') {
                  focus_id = '#price_input_'+(i+1);
              }
            }
          }*/
      }
      if (focus_id != '') {
        $(focus_id).focus();
      }
}

function set_new_price(c_ele, pid, cnt) {
  pname = $('#products_name_'+pid).html();
  default_price = $('#h_edit_p_'+pid).html(); 
  nquantity = prompt(pname+"\n"+"<?php echo TEXT_POPUP_WINDOW_SHOW.': ';?>"+default_price+"\n"+"<?php echo TEXT_POPUP_WINDOW_EDIT;?>", default_price);
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
        set_money(cnt, false, '1'); 
        $('body').css('cursor', '');
        setTimeout('read_space_time()', 500);
      }
    }); 
  }
}
