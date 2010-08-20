// JavaScript Document
var f_flag = 'off';
var old_color = '';

function all_check(){
  field_on();
  var chk_flag = document.sele_act.all_chk.checked;
  
  if(chk_flag == true){
    
    if(document.sele_act.elements["chk[]"].length == null){
        document.sele_act.elements["chk[]"].checked = true;
        var tr_id = 'tr_' + document.sele_act.elements["chk[]"].value;
        if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
          document.getElementById(tr_id).style.backgroundColor = "#FFCC99";
    }else{
        for(i = 0; i < document.sele_act.elements["chk[]"].length; i++){
          document.sele_act.elements["chk[]"][i].checked = true;
          var tr_id = 'tr_' + document.sele_act.elements["chk[]"][i].value;
          if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
            document.getElementById(tr_id).style.backgroundColor = "#FFCC99";
        }
    }
  }else{
    if(document.sele_act.elements["chk[]"].length == null){
      document.sele_act.elements["chk[]"].checked = false;
      var tr_id = 'tr_' + document.sele_act.elements["chk[]"].value;
      if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
        document.getElementById(tr_id).style.backgroundColor = "#F0F1F1";
    }else{
      for(i = 0; i < document.sele_act.elements["chk[]"].length; i++){
        document.sele_act.elements["chk[]"][i].checked = false;
        var tr_id = 'tr_' + document.sele_act.elements["chk[]"][i].value;
        if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
          document.getElementById(tr_id).style.backgroundColor = "#F0F1F1";
      }
    }
  }
}

function chg_tr_color(aaa){
  // 保持邮件发送框显示
  field_on();
  var c_flag = aaa.checked;
  var tr_id = 'tr_' + aaa.value;
  
  // 如果选中
  if(c_flag == true){
    
    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
        old_color = document.getElementById(tr_id).style.backgroundColor
    }

    // 清空checkbox
    /*
    for(i = 0; i < document.sele_act.elements["chk[]"].length; i++){
      document.sele_act.elements["chk[]"][i].checked = false;
      if (document.getElementById('tr_' + document.sele_act.elements["chk[]"][i].value).className != 'dataTableRowSelected') 
        document.getElementById('tr_' + document.sele_act.elements["chk[]"][i].value).style.backgroundColor = "#F0F1F1";
    }*/

    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
        document.getElementById(tr_id).style.backgroundColor = "#FFCC99";
    }

    // 选中当前checkbox
    //aaa.checked = true;
    
    // 重置订单状态和邮件内容
    //document.sele_act.elements['status'].selectedIndex = 0;
    //mail_text('status','comments','os_title')
  // 如果未选中
  }else{
    if (document.getElementById(tr_id).className != 'dataTableRowSelected') {
        document.getElementById(tr_id).style.backgroundColor = old_color;
    }
    
    //取消当前checkbox
    //aaa.checked = false;
    
    //隐藏邮件框
    //field_off();
  }

}

function chg_td_color(bbb){
  /*
  field_on();
  
  if(document.sele_act.elements["chk[]"].length == null){
    if(document.sele_act.elements["chk[]"].value == bbb){
	  if(document.sele_act.elements["chk[]"].checked == true){
	    document.sele_act.elements["chk[]"].checked = false;
	    var tr_id = 'tr_' + bbb;
	    document.getElementById(tr_id).style.backgroundColor = "#F0F1F1";
      old_color = document.getElementById(tr_id).style.backgroundColor
	  }else{
	    document.sele_act.elements["chk[]"].checked = true;
	    var tr_id = 'tr_' + bbb;
	    document.getElementById(tr_id).style.backgroundColor = "#FFCC99";
      old_color = document.getElementById(tr_id).style.backgroundColor
	  }
	}
  }else{
    for(i = 0; i < document.sele_act.elements["chk[]"].length; i++){	
	  if(document.sele_act.elements["chk[]"][i].value == bbb){
	    if(document.sele_act.elements["chk[]"][i].checked == true){
	      document.sele_act.elements["chk[]"][i].checked = false;
	      var tr_id = 'tr_' + bbb;
	      document.getElementById(tr_id).style.backgroundColor = "#F0F1F1";
        old_color = document.getElementById(tr_id).style.backgroundColor
	    }else{
	      document.sele_act.elements["chk[]"][i].checked = true;
	      var tr_id = 'tr_' + bbb;
	      document.getElementById(tr_id).style.backgroundColor = "#FFCC99";
        old_color = document.getElementById(tr_id).style.backgroundColor
	    }
	  }
    }
  }
  */
}

// 打开邮件框
function field_on(){
  if(f_flag == 'off'){
    f_flag = 'on';
    document.getElementById("select_send").style.display = "block";
  }
}
// 关闭邮件框
function field_off(){
  if(f_flag == 'on'){
    f_flag = 'off';
    document.getElementById("select_send").style.display = "none";
  }
}

function fax_over_color(ele){
  old_color = ele.style.backgroundColor
  ele.style.backgroukdColor = "#ffcc99";
}
function fax_over_color(ele){
  ele.style.backgroukdColor = old_color;
}

/*
function over_color(ccc){
  var tr_id = 'tr_' + ccc;
  old_color = document.getElementById(tr_id).style.backgroundColor
  document.getElementById(tr_id).style.backgroundColor = "#FFFFFF";
}

function out_color(ddd){
  var tr_id = 'tr_' + ddd;
  document.getElementById(tr_id).style.backgroundColor = old_color;
}
*/

