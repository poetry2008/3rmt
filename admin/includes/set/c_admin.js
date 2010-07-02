var zaiko_input_obj=document.getElementsByName("zaiko[]");//架空
var target_input_obj=document.getElementsByName("TARGET_INPUT[]");//同業者
var price_obj=document.getElementsByName("price[]");//特別価格
var error_msg='';
//var old_color='';

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
  } //else {
      var flg=confirm("特価価格を更新します");
      if(flg){
        document.myForm1.flg_up.value=1;
        window.document.myForm1.submit();
      }else{
        document.myForm1.flg_up.value=0;
        alert("更新をキャンセルしました");
      }
  //}
}

function chek_radio(cnt){
  //var radio_cnt=document.getElementsByName("chk_"+cnt+"[]");
  var radio_cnt=document.getElementsByName("chk["+cnt+"]");
  for(var i=0;i < radio_cnt.length;i++){
    if(radio_cnt[i].checked == true){
      //document.getElementById("radiochk"+cnt+"_"+i).value = 1;
      //document.getElementById("target_"+cnt+"_"+i).disabled = false;
      if(document.getElementById("target_"+cnt+"_"+i).innerHTML != ''){
        set_money(cnt);//特価価格設定
      }
    }else{
      //document.getElementById("radiochk"+cnt+"_"+i).value = 0;
      //document.getElementById("target_"+cnt+"_"+i).disabled =true;
    }
  }   
}

function cleat_set(url,w,h){
  var set_url=url;
  var set_width=w;
  var set_height=h;
  window.open(set_url,'aaa',"width="+set_width+",height="+set_height);
  //window.open(url,'aaa','width=100%,height=100%');
  window.document.myForm1.action = set_url;
  window.document.myForm1.target = "aaa"; 
  window.document.myForm1.method = "POST"; 
  window.document.myForm1.submit();
}

function list_display(path,cid){

  var set_url="list_display.php?cpath="+path+"&cid="+cid;
  window.open(set_url,'bbbb',"width=1000,height=500,scrollbars=yes");

  //location.href="list_display.php?cpath="+path+"&cid="+cid;
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
            if( confirm(calc.percent+"%の差額があります。再設定してください") ) {
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
  var this_price=document.getElementsByName("this_price[]");
  var old_price = this_price[i-1].value;
  var new_price = $('#price_input_'+i).val();
  if (old_price != new_price) {
    $('#price_input_'+i).css('color','red');
  }else {
    $('#price_input_'+i).css('color','blue');
  }
}

//計算設定読み込み
function set_money(num,warning){
    if (warning ==undefined)
    {
        warning = true;
    }
  var n=num;
  var radio_cnt=document.getElementsByName("chk["+n+"]");
    
  if(radio_cnt.length == 0){

    var tar_ipt = document.getElementById("target_"+n+"_0").innerHTML;//同業者

  }else{
    for(var i=0;i < radio_cnt.length;i++){
      if(radio_cnt[i].checked == true){
        var tar_ipt = document.getElementById("target_"+n+"_"+i).innerHTML;//同業者

      }
    } 
  } 
  var increase_input_obj=$(".INCREASE_INPUT");//業者 x 倍数
  var ins_ipt=increase_input_obj[n].innerHTML; //


  var set_m=0;                       //サイト入力フォームに値を設置変数初期化

  if(parseInt(ins_ipt) <= parseInt(tar_ipt)){
      
    var ins_anser = ( parseInt(ins_ipt) / parseInt(tar_ipt) ) * 100;
    ins_anser = 100 - ins_anser;
    if(calc.percent != '' && parseInt(ins_anser) >= calc.percent){
        if (warning){
          error_msg += calc.percent+"%の差額があります。再設定してください\n";
        }
    }
    var kei = calc.keisan;//数字
    var shisoku = calc.shisoku;//演算子

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
          error_msg += calc.percent+"%の差額があります。再設定してください\n";
        }
    }
    set_m=ins_ipt;
    set_m=Math.ceil(set_m);
  }
  if (set_m < 0) {
    set_m = 0;
  }
  if(typeof(tar_ipt) == 'undefined' || ins_ipt == 0)return;
  //var price_n = n + 1;
  //var price_obj=document.getElementById("price_input_"+ price_n);//サイトインプット
  var this_price=document.getElementsByName("pprice[]");

  price_obj[n].value=parseInt(set_m);
    
  //価格の判定
  //現在の価格と更新予定の価格を比較
  //一致しているなら文字の色を青、不一致なら赤にする
  if(parseInt(this_price[n].value)==parseInt(set_m)){
    price_obj[n].style.color="blue";
  }else{
    price_obj[n].style.color="red";
  }
}

var calc;
function ajaxLoad(path){
    var send_url="set_ajax.php?action=ajax&cPath="+path;
  $.ajax({
    url: send_url,
        success: function(data) {
        calc = eval('('+data+')');
            onload_keisan(false);
      }
    });
}
//var spprice=document.getElementsByName("pprice[]");

function history(url,cpath,cid,action){
  var url=url+"?cpath="+cpath+"&cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800");
}

function oro_history(url,cid,action){
  var url=url+"?cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800,scrollbars=yes");
}

function dougyousya_history(url,cpath,cid,action,did){
  var url=url+"?cPath="+cpath+"&cid="+cid+"&did="+did+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800");
}

function onload_keisan(warning){

  var trader_input_obj=$(".TRADER_INPUT");//業者
  var increase_input_obj=$(".INCREASE_INPUT");//業者
  for(var i=0;i< trader_input_obj.length;i++){
//    var trader_price=var_calc(trader_input_obj[i].innerHTML);
  //    alert(trader_price);
    //increase_input_obj[i].innerHTML=trader_price;
      set_money(i,warning);//特価価格設定
  }
}
function check_error(){

      var trader_input_obj=$(".TRADER_INPUT");//業者
      var this_price=document.getElementsByName("pprice[]");
      var bflag=document.getElementsByName("bflag[]");
      var focus_id = '';
      var price_error = '価格設定はエラーが出ます。再設定してください。';

      for(var i=0;i< trader_input_obj.length;i++){
          $('#price_input_'+(i+1)).css('border-color','');
          $('#price_error_'+(i+1)).html('');
          $('#offset_input_'+(i+1)).css('border-color','');
          $('#offset_error_'+(i+1)).html('');
          
          var old_price = this_price[i].value;
          var new_price = $('#price_input_'+(i+1)).val();
          //alert(old_price + '|' + new_price);
          if (calc.percent != '' && calc.percent != 0 && calc.percent != null) {
          if (new_price > old_price) {
            if( ((new_price - old_price) / old_price) * 100 >= calc.percent ) {
                error_msg = calc.percent+"%の差額があります。再設定してください\n";
                //error_msg = price_error;
                $('#price_input_'+(i+1)).css('border-color','red');
                //$('#price_error_'+(i+1)).html('<img src="images/icons/error_1.gif" title="'+calc.percent+'%の差額があります。再設定してください">');
                if (focus_id == '') {
                    focus_id = '#price_input_'+(i+1);
                }
            }
          } else {
            if( ((old_price - new_price) / new_price) * 100 >= calc.percent ) {
                error_msg = calc.percent+"%の差額があります。再設定してください\n";
                //error_msg = price_error;
                $('#price_input_'+(i+1)).css('border-color','red');
                //$('#price_error_'+(i+1)).html('<img src="images/icons/error_1.gif" title="'+calc.percent+'%の差額があります。再設定してください">');
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
              $('#offset_error_'+(i+1)).append('<img src="images/icons/error_2.gif" title="特別価格が通常価格より低くなりました">');
              if (focus_id == '') {
                  focus_id = '#price_input_'+(i+1);
              }
            }
          } else {
            //alert(parseFloat($('#offset_input_' + (i+1)).val()));
            if (parseFloat($('#offset_input_' + (i+1)).val()) < 0) {
              error_msg = price_error;
              $('#offset_input_'+(i+1)).css('border-color','red');
              $('#offset_error_'+(i+1)).append('<img src="images/icons/error_3.gif" title="特別価格が通常価格より高くなりました">');
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
