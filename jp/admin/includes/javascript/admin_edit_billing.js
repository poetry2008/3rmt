//generate order address country list
function billing_check(select_value){

  $("#billing_td_"+billing_country_fee_id_one).hide();
  $("#billing_td_"+billing_country_area_id_one).hide();
  $("#billing_td_"+billing_country_city_id_one).hide();
  if(document.getElementById(billing_country_fee_id)){
    var billing_country_fee = document.getElementById(billing_country_fee_id);
    billing_country_fee.options.length = 0;
    var i = 0;
    for(x in billing_arr_check){

      billing_country_fee.options[billing_country_fee.options.length]=new Option(billing_arr_check[x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#billing_td_"+billing_country_fee_id_one).hide();
    }else{

      $("#billing_td_"+billing_country_fee_id_one).show();
    } 
  }
}
//generate order address area list
function billing_country_check(value,select_value){
  if(document.getElementById(billing_country_area_id)){ 
    var billing_country_area = document.getElementById(billing_country_area_id);
    billing_country_area.options.length = 0;
    var i = 0;
    for(x in billing_arr_country_check[value]){

      billing_country_area.options[billing_country_area.options.length]=new Option(billing_arr_country_check[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#billing_td_"+billing_country_area_id_one).hide();
    }else{

      $("#billing_td_"+billing_country_area_id_one).show();
    }
  }

}
//generate order address city list
function billing_country_area_check(value,select_value){
  if(document.getElementById(billing_country_city_id)){
    var billing_country_city = document.getElementById(billing_country_city_id);
    billing_country_city.options.length = 0;
    var i = 0;
    for(x in billing_arr_city[value]){

      billing_country_city.options[billing_country_city.options.length]=new Option(billing_arr_city[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i ==  0){

      $("#billing_td_"+billing_country_city_id_one).hide();
    }else{
      
      $("#billing_td_"+billing_country_city_id_one).show();
    }
  }

}
//clear orders address error
function billing_address_clear_error(){
    for(x in billing_list_error){
      
      $("#billing_error_"+billing_list_error[x]).html("");
    }
}
//check value is in array
function billing_in_array(value,arr){

  for(vx in arr){
    if(value == arr[vx]){

      return true;
    } 
  }
  return false;
}
//show bill orders address property
function billing_address_option_show(action){
  switch(action){

  case 'new' :
    $("#billing_address_list_id").hide();
    billing_check();
    billing_country_check($('#'+billing_country_fee_id).val());
    billing_country_area_check($('#'+billing_country_area_id).val());
  for(x in billing_arr_new){
    if(document.getElementById("billing_"+x)){ 
      var billing_list_options = document.getElementById("billing_"+x);
      billing_list_options.value = billing_arr_new[x];
      billing_list_options.style.color = billing_arr_color[x];
      $("#billing_error_"+x).html('');
      if(js_ed_orders_post_address){
        if(document.getElementById("billing_l_"+x)){
          if($("#billing_l_"+x).val() == 'true'){
            $("#billing_r_"+x).html("&nbsp;"+js_ed_orders_require);
          }
        }
      }
    }
    }
    break;
  case 'old' :
      if(js_ed_orders_guest_chk){
        $("#billing_address_list_id").show();
      }else{
       $("#billing_address_list_id").hide(); 
      }
    var billing_address_num = 'true';
  var billing_address_show_list = document.getElementById("billing_address_show_list");
  if(document.getElementById("billing_address_show_list")){
    billing_address_show_list.options.length = 0;
  }

  len = billing_arr_old.length;
  j_num = 0;
  for(i = 0;i < len;i++){
    billing_arr_str = '';
    for(x in billing_arr_old[i]){
        if(billing_in_array(x,billing_arr_name)){
          billing_arr_str += billing_arr_old[i][x];
        }
        if(js_ed_orders_address_option_new){ 
        if(document.getElementById("billing_l_"+x)){
          if($("#billing_l_"+x).val() == 'true'){
            $("#billing_r_"+x).html("&nbsp;"+js_ed_orders_require);
          }
        }
        }
    }
  if(document.getElementById("billing_address_show_list")){
    if(billing_arr_str != ''){
      ++j_num;
      if(j_num == 1){billing_first_num = i;}

      if(billing_address_num != 'true' && billing_address_num == i){

        var billing_address_string = '（'+js_ed_orders_billing_address+'）';
      }else{
        var billing_address_string = ''; 
      }
      if(js_ed_orders_post_show_list != ''){
        billing_address_show_list.options[billing_address_show_list.options.length]=new Option(billing_arr_str+billing_address_string,i,i==js_ed_orders_post_show_list,i==js_ed_orders_post_show_list);
      }else{
        if(billing_arr_str == billing_address_str){
          billing_address_show_list.options[billing_address_show_list.options.length]=new Option(billing_arr_str+billing_address_string,i,true,true);
        }else{
          billing_address_show_list.options[billing_address_show_list.options.length]=new Option(billing_arr_str+billing_address_string,i);
        }
      }
    }
  }

  }
    break;
  }
}
//billing address property list
function billing_address_option_list(value){
  ii = 0;
  for(x in billing_arr_list[value]){
   if(document.getElementById("billing_"+x)){
     var billing_list_option = document.getElementById("billing_"+x);
     if(js_ed_orders_billing_fee_id == 'billing_'+x){
      billing_check(billing_arr_list[value][x]);
    }else if(js_ed_orders_billing_area_id == 'billing_'+x){
      billing_country_check(document.getElementById(billing_country_fee_id).value,billing_arr_list[value][x]);
     
    }else if(js_ed_orders_billing_city_id == 'billing_'+x){
      billing_country_area_check(document.getElementById(billing_country_area_id).value,billing_arr_list[value][x]);
    }else{
      billing_list_option.style.color = '#000';
      billing_list_option.value = billing_arr_list[value][x];   
    }
     
    $("#billing_error_"+x).html('');
    $("#billing_r_"+x).html("&nbsp;"+js_ed_orders_require);
    ii++; 
   }
  }

}
