//generate delivery country list
function check(select_value){

  $("#td_"+country_fee_id_one).hide();
  $("#td_"+country_area_id_one).hide();
  $("#td_"+country_city_id_one).hide();
  if(document.getElementById(country_fee_id)){
    var country_fee = document.getElementById(country_fee_id);
    country_fee.options.length = 0;
    var i = 0;
    for(x in weight_arr_check){

      country_fee.options[country_fee.options.length]=new Option(weight_arr_check[x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#td_"+country_fee_id_one).hide();
    }else{

      $("#td_"+country_fee_id_one).show();
    } 
  }
}
//generate delivery area list
function country_check(value,select_value){
   
  if(document.getElementById(country_area_id)){ 
    var country_area = document.getElementById(country_area_id);
    country_area.options.length = 0;
    var i = 0;
    for(x in weight_country_arr[value]){

      country_area.options[country_area.options.length]=new Option(weight_country_arr[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#td_"+country_area_id_one).hide();
    }else{

      $("#td_"+country_area_id_one).show();
    }
  }

}
//generate delivery city list
function country_area_check(value,select_value){
   
  if(document.getElementById(country_city_id)){
    var country_city = document.getElementById(country_city_id);
    country_city.options.length = 0;
    var i = 0;
    for(x in weight_city_arr[value]){

      country_city.options[country_city.options.length]=new Option(weight_city_arr[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#td_"+country_city_id_one).hide();
    }else{
      
      $("#td_"+country_city_id_one).show();
    }
  }

}
//clear address error
function address_clear_error(){
  
    for(x in weight_list_error){
      
      $("#error_"+weight_list_error[x]).html("");
    }

}
//check value is in array
function in_array(value,arr){

  for(vx in arr){
    if(value == arr[vx]){

      return true;
    } 
  }
  return false;
}
//show address property 
function address_option_show(action){
  switch(action){

  case 'new' :
    $("#address_list_id").hide();
    check();
    country_check($('#'+country_fee_id).val());
    country_area_check($('#'+country_area_id).val());
    
  for(x in arr_new){
    if(document.getElementById("ad_"+x)){ 
      var list_options = document.getElementById("ad_"+x);
      list_options.value = arr_new[x];
      list_options.style.color = arr_color[x];
      $("#error_"+x).html('');
      if(weight_address_option){
        if(document.getElementById("l_"+x)){
          if($("#l_"+x).val() == 'true'){
            $("#r_"+x).html("&nbsp;"+weight_text_require);
          }
        }
      }
    }
    }
    break;
  case 'old' :
      if(weight_guest_chk){
        $("#address_list_id").show();
      }else{
       $("#address_list_id").hide(); 
      }
    var orders_billing_address_num = 'true';
  var address_show_list = document.getElementById("address_show_list");

  if(document.getElementById("address_show_list")){
    address_show_list.options.length = 0;
  }

  len = arr_old.length;
  j_num = 0;
  for(i = 0;i < len;i++){
    arr_str = '';
    for(x in arr_old[i]){
        if(in_array(x,arr_name)){
          arr_str += arr_old[i][x];
        }
        if(weight_address_option_new){ 
        if(document.getElementById("l_"+x)){
          if($("#l_"+x).val() == 'true'){
            $("#r_"+x).html("&nbsp;"+weight_text_require);
          }
        }
        }
    }
  if(document.getElementById("address_show_list")){
    if(arr_str != ''){
      ++j_num;
      if(j_num == 1){first_num = i;}

      if(orders_billing_address_num != 'true' && orders_billing_address_num == i){

        var orders_billing_address_str = '（'+weight_billing_address+'）';
      }else{
        var orders_billing_address_str = ''; 
      }
      if(weight_show_list != ''){
        address_show_list.options[address_show_list.options.length]=new Option(arr_str+orders_billing_address_str,i,i==weight_show_list,i==weight_show_list);
      }else{
        if(arr_str == address_str){
          address_show_list.options[address_show_list.options.length]=new Option(arr_str+orders_billing_address_str,i,true,true);
        }else{
          address_show_list.options[address_show_list.options.length]=new Option(arr_str+orders_billing_address_str,i);
        }
      }
    }
  }

  }
    break;
  }
}
//address property list
function address_option_list(value){
  ii = 0;
  for(x in weight_option_arr_list[value]){
   if(document.getElementById("ad_"+x)){
     var list_option = document.getElementById("ad_"+x);
     if(weight_country_fee_id == 'ad_'+x){
      check(weight_option_arr_list[value][x]);
    }else if(weight_country_area_id == 'ad_'+x){
      country_check(document.getElementById(country_fee_id).value,weight_option_[value][x]);
     
    }else if(weight_country_city_id == 'ad_'+x){
      country_area_check(document.getElementById(country_area_id).value,weight_option_arr_list[value][x]);
    }else{
      list_option.style.color = '#000';
      list_option.value = weight_option_arr_list[value][x];   
    }
     
    $("#error_"+x).html('');
    $("#r_"+x).html("&nbsp;"+weight_text_require);
    ii++; 
   }
  }

}
//change address display
function address_show(){
  
  var style = $("#address_show_id").css("display");
  if(style == 'none'){
    $("#address_show_id").show(); 
    $("#address_font").html(js_weight_address_info_hide);
 
  }else{

    $("#address_show_id").hide();
    $("#address_font").html(js_weight_address_info_hide);
  }
}
//address list
function address_list(){

  for(x in js_weight_arr_list){
   if(document.getElementById("ad_"+x)){ 
     var op_list = document.getElementById("ad_"+x);
     if(js_weight_country_fee_id == 'ad_'+x){
      check(js_weight_arr_list[x]);
    }else if(js_weight_country_area_id == 'ad_'+x){
      country_check(document.getElementById(country_fee_id).value,js_weight_arr_list[x]);
     
    }else if(js_weight_country_city_id == 'ad_'+x){
      country_area_check(document.getElementById(country_area_id).value,js_weight_arr_list[x]);
    }else{
      op_list.style.color = '#000';
      $("#ad_"+x).val(js_weight_arr_list[x]);
    }
    
   }
  }
}

if(js_weight_shipping_total > 0){
	$(document).ready(function(){            
  		if(js_weight_address_option_new){
  			address_option_show('old');
  		}
  		if(js_weight_address_option){
  			address_list();
  			address_clear_error();
  		}
	});
}
if(js_doc_weight_add_option_new){
	$(document).ready(function(){
  		$("#address_list_id").hide();
	});
}
if(js_doc_weight_add_option_old){
	$(document).ready(function(){            
  		address_option_show('old');
	});
}
