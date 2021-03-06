/******************************************************************************/
/*                            Time select box                            /
/******************************************************************************/
function selectDate(start_time,end_time,value,start_time_old,end_time_old,now_time,start_time_exit,end_time_exit,exit_time){
       if(now_time != value){
      
          if(exit_time == value){
             
            start_time = start_time_exit;
            end_time = end_time_exit;
          }else{
            start_time = start_time_old;
            end_time = end_time_old;
          }
        } 
        $("#date_show_id").show();
        $("#hour_show_error").hide();
        var array_start = Array();
        array_start = start_time.split('||'); 

        //new
        html_str = '<table width="100%" border="0" cellspacing="2" cellpadding="2" id="group_list_box"><tr>';
        for(j=0;j<24;j++){
          flag = false;
          for(x in array_start){
          
            if(array_start[x] == j){
           
              flag = true;
              break;
            }
          } 
          if(flag == true){
            html_str += '<td id="hour'+j+'" bgcolor="#cccccc" style="color:#000000;cursor:pointer;" align="center" onclick="if((document.getElementById(\'shipping_list_min\').style.display == \'table-row\' && this.style.backgroundColor == \'rgb(56, 56, 56)\') || (document.getElementById(\'shipping_list_min\').style.display == \'block\' && this.style.backgroundColor == \'#383838\')){check_out('+j+');}else{this.style.background=\'#383838\';selectHour(\''+start_time+'\',\''+end_time+'\','+j+',\'\',this);}">'+j+'<input type="hidden" id="h_c_'+j+'" value="0"></td>';
          }else{
            html_str += '<td id="hour'+j+'" bgcolor="#f1f0ef" style="color:#cccccc;" align="center">'+j+'</td>';
          }
          if((j+1) % 6 == 0){
          
            html_str += '</tr><tr>';
          }
        }
        
        html_str += '</tr></table>';
        $("#shipping_list_show").html('');
        if(value != ''){
          $("#shipping_list").show();
        }else{
          $("#shipping_list").hide();
          $("#date_show_id").hide();
        }
        $("#shipping_list_show").html(html_str);

        $("#shipping_list_show_min").html('');
        $("#shipping_list_min").hide();
}




/******************************************************************************/
/*                            Minutes select box                               /
/******************************************************************************/
function selectHour(start_time,end_time,hour,min_num,ele){
        $("#hour_error").html("");
        $("#hour_show_error").hide();
        if(hour != ''){
          hour = parseInt(hour); 
          document.getElementById("hour"+hour).style.color="#ffffff";
          document.getElementById("hour"+hour).style.textDecoration ="underline";
          $("#hour"+hour).css("background-color","#383838");
        }
        var array_start = new Array();
        array_start = start_time.split('||'); 
        var array_end = new Array();
        array_end = end_time.split('||');
         
        //Integerized

        for(h = 0;h < 24;h++){
          flag = false;
          for(x in array_start){
          
            if(array_start[x] == h){
              flag = true; 
            }
          } 
          if(flag == true && h != hour){
              $("#hour"+h).css("background-color","#cccccc"); 
              document.getElementById("hour"+h).style.color="#000000";
              document.getElementById("hour"+h).style.textDecoration ="";
          }
        }
        
        html_str = '';
        html_str = '<table width="100%" border="0" cellspacing="0" cellpadding="2"><tr><input type="hidden" name="hour" value="'+hour+'">';
        
        

        var string = '';
        var start_hour_num = '';
        var start_min_num = '';
        var end_hour_num = '';
        var end_min_num = '';
        var h_c_value = $("#h_c_"+hour).val();
        for(n in array_start){
          
            if(hour == array_start[n]){
            
              arr_time_d = array_end[n].split('|');
              for(m in arr_time_d){
                arr_time_t = arr_time_d[m].split(',');
                if(m == h_c_value && min_num == ''){
                    checked = ' checked';
                    arr_time_temp_1 = arr_time_t[0].split(':');
                    arr_time_temp_2 = arr_time_t[1].split(':');
                    start_hour_num = arr_time_temp_1[0];
                    start_min_num = arr_time_temp_1[1];
                    end_hour_num = arr_time_temp_2[0];
                    end_min_num = arr_time_temp_2[1]; 
                }else{
                    if(min_num != '' && min_num == m){
                      checked = ' checked';
                      arr_time_temp_1 = arr_time_t[0].split(':');
                      arr_time_temp_2 = arr_time_t[1].split(':');
                      start_hour_num = arr_time_temp_1[0];
                      start_min_num = arr_time_temp_1[1];
                      end_hour_num = arr_time_temp_2[0];
                      end_min_num = arr_time_temp_2[1];
                    }else{
                       
                      checked = ''; 
                    }
                }
                for(k in arr_time_t){
                
                  arr_time_m = arr_time_t[k].split(':');
             
                  if(k != arr_time_t.length-1){
                    string +=  '<div class="time_radio"><input type="radio" id="m'+m+'" name="min" value="'+m+'"'+checked+' onclick="change_time('+m+',\''+array_end[n]+'\','+array_start[n]+');"></div><div class="time_label"><label for="m'+m+'"><a href="javascript:void(0);"onclick="change_new_time('+m+',\''+array_end[n]+'\','+array_start[n]+');" >'+arr_time_m[0]+'時'+arr_time_m[1]+'分から';
                  }else{
                    string +=  arr_time_m[0]+'時'+arr_time_m[1]+'分</a></label></div>'; 
                  }
                }
                if(m % 2 == 1){
                  string += '<br>';
                }
              }
            }
        }
         
          html_str += '<td><input type="hidden" id="start_hour" name="start_hour" value="'+start_hour_num+'"><input type="hidden" id="start_min" name="start_min" value="'+start_min_num+'"><input type="hidden" id="end_hour"name="end_hour" value="'+end_hour_num+'"><input type="hidden" id="end_min" name="end_min" value="'+end_min_num+'"><div id="shipping_time_id" class="shipping_time_reorder">'+string+'</div></td>';
          

          html_str += '</tr><tr></table>'; 

        $("#shipping_list_show_min").html('');
        $("#shipping_list_show_min").html(html_str);
        $("#shipping_list_min").show();
        
        var temp_value = 0;
        if(hour < 6){
          if(navigator.userAgent.indexOf("MSIE 9.0")>0) {
              temp_value = -98;
          } else {
            if(navigator.userAgent.indexOf("MSIE")>0) {
              temp_value = -98;
            } else {
              temp_value = -96;
            }
          }
        }else if(hour >= 6 && hour <= 11){
        
          if(navigator.userAgent.indexOf("MSIE 9.0")>0) {
              temp_value = -76;
          } else {
            if(navigator.userAgent.indexOf("MSIE")>0) {
              temp_value = -76;
            } else {
              temp_value = -74;
            }
          }
        }else if(hour >= 12 && hour <= 17){
        
          if(navigator.userAgent.indexOf("MSIE 9.0")>0) {
            temp_value = -54;
          } else {
          if(navigator.userAgent.indexOf("MSIE")>0) {
            temp_value = -54;
          } else {
            temp_value = -52;
          }
          }
        }else{
          if(navigator.userAgent.indexOf("MSIE 9.0")>0) {
            temp_value = -32;
          } else {
          if(navigator.userAgent.indexOf("MSIE")>0) {
            temp_value = -32;
          } else {
            temp_value = -30;
          }
          }
        }
        $('#shipping_time_id').css('top', temp_value).show();
        if(typeof(ele) != "object"){
        
          $("#ele_id").val(ele);
        }else{
          
          $("#ele_id").val(temp_value);
        }
}


function change_time(value,end_time,h_num){
              var start_hour_num = new Array();
              var start_min_num = new Array();
              $("#h_c_"+h_num).val(value);
              arr_time_d = end_time.split('|');
              for(m in arr_time_d){
                 if(m == value){
                    arr_time_t = arr_time_d[m].split(',');
                    for(x in arr_time_t){
                      arr_time_temp = arr_time_t[x].split(':');
                      if(x == 0){
                        document.getElementById("start_hour").value = arr_time_temp[0];  
                        document.getElementById("start_min").value = arr_time_temp[1];
                      }
                      if(x == 1){
                        document.getElementById("end_hour").value = arr_time_temp[0];  
                        document.getElementById("end_min").value = arr_time_temp[1];
                      }
                    }
                }
              }
}

function change_new_time(value,end_time,h_num){
  var radio_list = document.getElementsByName("min");
  $("#h_c_"+h_num).val(value);
  for (var i=0; i<radio_list.length; i++) {
      document.getElementById('m'+value).checked=false;   
  } 
  for (var i=0; i<radio_list.length; i++) {
    if (radio_list[i].id == 'm'+value) {
      document.getElementById('m'+value).checked=true;   
    } 
  }
  var start_hour_num = new Array();
              var start_min_num = new Array();
              arr_time_d = end_time.split('|');
              for(m in arr_time_d){
                 if(m == value){
                    arr_time_t = arr_time_d[m].split(',');
                    for(x in arr_time_t){
                      arr_time_temp = arr_time_t[x].split(':');
                      if(x == 0){
                        document.getElementById("start_hour").value = arr_time_temp[0];  
                        document.getElementById("start_min").value = arr_time_temp[1];
                      }
                      if(x == 1){
                        document.getElementById("end_hour").value = arr_time_temp[0];  
                        document.getElementById("end_min").value = arr_time_temp[1];
                      }
                    }
                }
              }
}
function check_out(num){
  var style = $("#shipping_time_id").css("display");
  if(style != 'none' && document.getElementById('shipping_time_id')){
      $("#shipping_time_id").hide();
      var shipping_list_min = document.getElementById("shipping_list_min");
      var hour = document.getElementById("hour"+num);
      shipping_list_min.style.display = 'none';
      hour.style.backgroundColor = '#cccccc';
      document.getElementById("hour"+num).style.color="#000000";
      document.getElementById("hour"+num).style.textDecoration ="";
  }
}
