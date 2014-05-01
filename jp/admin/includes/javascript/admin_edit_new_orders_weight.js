//clear address error info
function address_clear_error(){
  
    for(x in list_error){
      
      $("#error_"+list_error[x]).html("");
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
function address_option_show(action){
  switch(action){

  case 'new' :
    $("#address_list_id").hide();
    check();
    country_check($("#"+country_fee_id).val());
    country_area_check($("#"+country_area_id).val());
    
  for(x in arr_new){
    if(document.getElementById("ad_"+x)){ 
      var list_options = document.getElementById("ad_"+x);
      list_options.value = arr_new[x];
      list_options.style.color = arr_color[x];
      $("#error_"+x).html('');
      if(weight_option_status_old){
      if(document.getElementById("l_"+x)){
          if($("#l_"+x).val() == 'true'){
            $("#r_"+x).html("&nbsp;"+js_ne_orders_text_require);
          }
      } 
      }
    }
    }
    break;
  case 'old' :
    $("#address_list_id").show();
  var address_show_list = document.getElementById("address_show_list");

  address_show_list.options.length = 0;

  len = arr_old.length;
  j_num = 0;
  for(i = 0;i < len;i++){
    arr_str = '';
    for(x in arr_old[i]){
        if(in_array(x,arr_name)){
          arr_str += arr_old[i][x];
        }
          if(weight_option_status_new){
        if(document.getElementById("l_"+x)){
          if($("#l_"+x).val() == 'true'){
            $("#r_"+x).html("&nbsp;"+js_ne_orders_text_require);
          }
        }
         }
    }
    if(arr_str != ''){
      if(arr_str==address_select){
              address_first_num = i;
      }
      ++j_num;
      if(j_num == 1){first_num = i;}
        if(billing_address_num != '' && billing_address_num == i){

          var billing_address_str = '（'+js_ne_orders_weight_billing_add+'）';
        }else{
          var billing_address_str = ''; 
        }
        if(js_ne_orders_weight_add_show_list != ''){
          address_show_list.options[address_show_list.options.length]=new Option(arr_str+billing_address_str,i,i==js_ne_orders_weight_add_show_list,i==js_ne_orders_weight_add_show_list);
        }else{
          if(arr_str == address_str){
            address_show_list.options[address_show_list.options.length]=new Option(arr_str+billing_address_str,i,true,true);
          }else{
            address_show_list.options[address_show_list.options.length]=new Option(arr_str+billing_address_str,i,arr_str==address_select,arr_str==address_select); 
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
  for(x in arr_list[value]){
   if(document.getElementById("ad_"+x)){
     var list_option = document.getElementById("ad_"+x);
     if(js_ne_country_fee_id == 'ad_'+x){
      check(arr_list[value][x]);
    }else if(js_ne_country_area_id == 'ad_'+x){
      country_check(document.getElementById(country_fee_id).value,arr_list[value][x]);
     
    }else if(js_ne_country_city_id == 'ad_'+x){
      country_area_check(document.getElementById(country_area_id).value,arr_list[value][x]);
    }else{
      list_option.style.color = '#000';
      list_option.value = arr_list[value][x];      
    }
     
    $("#error_"+x).html('');
    ii++; 
   }
  }

}
//generate delivery country list
function check(select_value){
  
  $("#td_"+country_fee_id_one).hide();
  $("#td_"+country_area_id_one).hide();
  $("#td_"+country_city_id_one).hide();
   
  if(document.getElementById(country_fee_id)){
    var country_fee = document.getElementById(country_fee_id);
    country_fee.options.length = 0;
    var i = 0;
    for(x in js_arr_country){

      country_fee.options[country_fee.options.length]=new Option(js_arr_country[x], x,x==select_value,x==select_value);
      i++;
    }

    if(i == 0){

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
    for(x in js_arr_area[value]){

      country_area.options[country_area.options.length]=new Option(js_arr_area[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i == 0){

      $("#td_"+country_area_id_one).hide();
    }else{

      $("#td_"+country_area_id_one).show();
    }
  }

}
//generate delivery area list
function country_area_check(value,select_value){
   
  if(document.getElementById(country_city_id)){
    var country_city = document.getElementById(country_city_id);
    country_city.options.length = 0;
    var i = 0;
    for(x in js_arr_city[value]){

      country_city.options[country_city.options.length]=new Option(js_arr_city[value][x], x,x==select_value,x==select_value);
      i++;
    }

    if(i == 0){

      $("#td_"+country_city_id_one).hide();
    }else{

      $("#td_"+country_city_id_one).show();
    }
  }

}
function address_show_list(){
     
    for(x in address_list){
     if(document.getElementById("ad_"+x)){ 
       var address_id = document.getElementById("ad_"+x);
    if(js_show_list_country == 'ad_'+x){
      check(address_list[x]);
    }else if(js_show_list_area == 'ad_'+x){
      country_check(document.getElementById(country_fee_id).value,address_list[x]);
     
    }else if(js_show_list_city == 'ad_'+x){
      country_area_check(document.getElementById(country_area_id).value,address_list[x]);
    }else{
      $("#ad_"+x).val(address_list[x]);
      address_id.style.color = '#000';
    }
      
     }
    }

  
  }
//change display address
  function address_show(){
    var style = $("#address_show_id").css("display");
  if(style == 'none'){
    
    $("#address_show_id").show(); 
    $("#address_font").html(js_ne_orders_info_hide);
  }else{
    
    $("#address_show_id").hide();
    $("#address_font").html(js_ne_orders_info_show);
  }
  }

$(document).ready(function(){            
     
   var address_show_list = document.getElementById("address_show_list");
   if(address_show_list){
      if(js_ready_option){
     address_option_show('old');
     }
     if(js_ready_option_action){
       address_clear_error();
     }
   }
});

if(js_ready_option_new){
$(document).ready(function(){            
  $("#address_list_id").hide();
});
}

if(js_ready_option_old){
$(document).ready(function(){            
  address_option_show('old');
});
}
