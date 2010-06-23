  function confirmg(question,url) {
  var x = confirm(question);
  if (x) {
    window.location = url;
  }
}
  
function mess(){
  //if(document.getElementById('pp').value == "" || document.getElementById('pp').value < 1){
  //alert("価格情報を入力して下さい");
  //document.getElementById('pp').focus();
  //return false;
  //}
}

var zaiko_input_obj=document.getElementsByName("zaiko[]");//架空
var trader_input_obj=document.getElementsByName("TRADER_INPUT[]");//業者
var increase_input_obj=document.getElementsByName("INCREASE_INPUT");//倍率
var target_input_obj=document.getElementsByName("TARGET_INPUT[]");//同業者
var price_obj=document.getElementsByName("price[]");//特別価格

function ctrl_keydown(evt,id_val,num,i,j){ //id_ver=ID　num＝現在の番号　i＝同業者番号 j=同業者数

  var n=parseInt(num)-1;
  var n2=parseInt(trader_input_obj.length);//フォームの数
  var a=parseInt(1);
  var b=parseInt(2);//特価フォーム専用
  var id=id_val;
  e = (evt)?evt:((window.event)?window.event:'');
  keychar = e.keyCode?e.keyCode:e.which;
  switch(keychar) { 
  case 13://enter
    n2 -=a;//フォームの数-１
    if((id == "TRADER_INPUT")&&(n < n2)){
      n +=a;
      trader_input_obj[n].focus();
    }else if((id == "zaiko")&&(n < n2)){
      n += a;
      zaiko_input_obj[n].focus();
    }else if((id == "INCREASE_INPUT")&&(n < n2)){
      n += a;
      increase_input_obj[n].focus();  
    }else if((id == "TARGET_INPUT")&&(n < n2)){
      n += a;
      document.getElementById("target_"+n+"_"+i).focus();
    }else if((id == "price_input_")&&(n < n2)){
      n += a;
      price_obj[n].focus();
      //document.getElementById("price_input_"+n).focus();
    }
        
    break;
  case 37:　//キーボードの十字キーの←
      if(id == "TRADER_INPUT"){
        zaiko_input_obj[n].focus();
      }else if(id == "INCREASE_INPUT"){
        trader_input_obj[n].focus();
      }else if(id == "TARGET_INPUT"){
        var k= j-1;
        if(j != 0 && 0 != k && i !=0 ){
          i--;
          document.getElementById("target_"+n+"_"+i).focus();
        }else{
          increase_input_obj[n].focus();
        }
      }else if(id == "price_input_"){
        i--;
        document.getElementById("target_"+n+"_"+i).focus();
      }
    break;
  case 38:　//キーボードの十字キーの↑
      if((id == "TRADER_INPUT")&&(n != 0)){
        n -= a;
      }else if((id == "zaiko")&&(n != 0)){
          
        n -= a;
        zaiko_input_obj[n].focus();
          
      }else if((id == "INCREASE_INPUT")&&(n != 0)){
          
        n -= a;
        increase_input_obj[n].focus();  
        
      }else if((id == "TARGET_INPUT")&&(n != 0)){
        
        n -= a;
        document.getElementById("target_"+n+"_"+i).focus();
        
      }else if((id == "price_input_")&&(n != 0)){
        //document.getElementById("price_input_"+n).focus();
        n -= a;
        price_obj[n].focus();
      }
       
    break;
  case 39:　//キーボードの十字キーの→

      if(id == "zaiko"){
        
        trader_input_obj[n].focus();
        
      }else if(id == "TRADER_INPUT"){
        
        increase_input_obj[n].focus();  
              
      }else if(id == "INCREASE_INPUT"){
        
        document.getElementById("target_"+n+"_0").focus();
            
      }else if(id == "TARGET_INPUT"){
        var k= j-1;
        if(j != 0 && i != k){
          i++;
          document.getElementById("target_"+n+"_"+i).focus();
        }else{
          
          price_obj[n].focus();
          //document.getElementById("price_input_"+n).focus();
        }
          
          
      }
    break;
  case 40: 　//キーボードの十字キーの↓
      n2 -=a;//フォームの数-１
    if((id == "TRADER_INPUT")&&(n < n2)){
        
      n +=a;
          
      trader_input_obj[n].focus();
          
    }else if((id == "zaiko")&&(n < n2)){
          
      n += a;
      zaiko_input_obj[n].focus();
          
    }else if((id == "INCREASE_INPUT")&&(n < n2)){
          
      n += a;
      increase_input_obj[n].focus();  
        
    }else if((id == "TARGET_INPUT")&&(n < n2)){
        
      n += a;
      document.getElementById("target_"+n+"_"+i).focus();
          
    }else if((id == "price_input_")&&(n < n2)){
      n +=a;
      price_obj[n].focus();
      //document.getElementById("price_input_"+n).focus();
    }
    break;
  }
      
}


//個別特価価格更新処理
/*
  function single_update(cPath,pID,products_price,cnt,d_cnt){
  var n=cnt;
  var d_n=cnt-1;
  var p_price=products_price;
  //var price_obj=document.getElementById("price_input_"+n).value;
  var s_price=document.getElementById("price_input_"+n).value;
  var zaiko=document.getElementById("zaiko_"+n).value;
  if(zaiko =="在庫切れ" || zaiko==""){
  zaiko=0;
  }
  var flg=confirm("特価価格を更新します");
  if(flg){
  location.href="categories.php?cPath="+cPath+"&pID="+pID+"&action=single_update&products_special_price="+s_price+"&products_price="+p_price+"&products_quantity="+zaiko;
  }else{
  alert("更新をキャンセルしました");
  }
  }
*/
function all_update(){
  var flg=confirm("特価価格を更新します");
  if(flg){
    document.myForm1.flg_up.value=1;
    window.document.myForm1.submit();
  }else{
    document.myForm1.flg_up.value=0;
    alert("更新をキャンセルしました");
  }
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
  window.open(set_url,'bbbb',"width=1000,height=500");

  //location.href="list_display.php?cpath="+path+"&cid="+cid;
}
  
function event_onblur(num){   
  var n=num-1;                          //フォーム識別番号
    
  var trader_price=var_calc(trader_input_obj[n].value);
  increase_input_obj[n].value=trader_price;
  set_money(n);//特価価格設定
    
}

function onload_keisan(){

  for(var i=0;i<trader_input_obj.length;i++){
    var trader_price=var_calc(trader_input_obj[i].value);
    increase_input_obj[i].value=trader_price;
    set_money(i);//特価価格設定
  }
}

function var_calc(val){
  //val=業者/価格の値

  var bai = calc.bairitu
    var price=val*bai;                      


  var anser=Math.floor(price);  //切捨て
  
    
  return anser;
}


//計算設定読み込み


function set_money(num){
  var n=num;
  var radio_cnt=document.getElementsByName("chk["+n+"]");
    
  if(radio_cnt.length == 0){

    var tar_ipt = document.getElementById("target_"+n+"_0").innerHTML;//同業者

  }else{
    for(var i=0;i < radio_cnt.length;i++){
      if(radio_cnt[i].checked == true){
        var tar_ipt = document.getElementById("target_"+n+"_"+i).innerHTML;//同業者


//        var tar_ipt = document.getElementById("target_"+n+"_"+i).innerHTML;//同業者

      }
    } 
  } 
//  var ins_ipt=increase_input_obj[n].value;//倍率
    var ins_ipt=increase_input_obj[n].innerHTML;//倍率


  var set_m="";                       //サイト入力フォームに値を設置変数初期化

  if(parseInt(ins_ipt) <= parseInt(tar_ipt)){
    var ins_anser = ( parseInt(ins_ipt) / parseInt(tar_ipt) ) * 100;
    ins_anser = 100 - ins_anser;
    if(parseInt(ins_anser) >= 20){
      alert("20%の差額があります。再設定してください");
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
    if(parseInt(ins_anser) >= 20){
      alert("20%の差額があります。再設定してください");
    }
    set_m=ins_ipt;
    set_m=Math.ceil(set_m);
  }
  var price_n = n + 1;
  //var price_obj=document.getElementById("price_input_"+ price_n);//サイトインプット
  var this_price=document.getElementsByName("this_price[]");
  price_obj[n].value=String(set_m);
    
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
            onload_keisan();
      }
    });
}
var spprice=document.getElementsByName("pprice[]");

function history(url,cpath,cid,action){
  var url=url+"?cpath="+cpath+"&cid="+cid+"&action="+action;
  window.open(url,'ccc',"width=1000,height=800");
}
