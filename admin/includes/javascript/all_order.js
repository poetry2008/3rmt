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
  field_on();
  var c_flag = aaa.checked;
  var tr_id = 'tr_' + aaa.value;
  
  if (document.getElementById(tr_id).className != 'dataTableRowSelected') 
  if(c_flag == true){
    old_color = document.getElementById(tr_id).style.backgroundColor
    document.getElementById(tr_id).style.backgroundColor = "#FFCC99";
  }else{
    document.getElementById(tr_id).style.backgroundColor = old_color;
    //document.getElementById(tr_id).style.backgroundColor = "#F0F1F1";
    //old_color = document.getElementById(tr_id).style.backgroundColor
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

function field_on(){
  if(f_flag == 'off'){
    f_flag = 'on';
    document.getElementById("select_send").style.display = "block";
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

