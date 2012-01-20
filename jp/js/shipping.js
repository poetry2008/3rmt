var shipping_torihiki;
var torihiki_radio_div;
var old_time_object = new Array();
function format_time(str){
  if(str >=10){
    return str+"";
  }else{
    return "0"+str;
  }
}
//下面的方法 需要添加 参数 才能适用于多个配送
function show_address_book(pid){
  //显示 地址本
  address_list = window.document.getElementById('address_book_list'+pid);
  address_list.style.display = 'block';
}
function show_shipping_method(pid){
  shipping_list_t = window.document.getElementById('shipping_list'+pid);
  shipping_list_t.style.display = 'block';
}
function create_address_book(){
  //跳转到 创建地址页
  window.location.href = 'checkout_shipping_address.php';
}
function show_torihiki_time(_this,radio_name,pid){
  shipping_torihiki_t = window.document.getElementById('shipping_torihiki'+pid);
  if(_this.value != ''){
  shipping_torihiki_t.style.display = 'block';
  //通过隐藏 获得 工作时间 和 开始时间
  var work_time = window.document.getElementById('shipping_work_time'+pid).value;
  var start_time = window.document.getElementById('shipping_start_time'+pid).value;
  work_time_arr = work_time.split('-');
  work_start = work_time_arr[0];
  work_end = work_time_arr[1];
  //工作 开始时间
  work_start_arr = work_start.split(':');
  work_start_hour = work_start_arr[0];
  work_start_mim = work_start_arr[1];
  //工作 结束时间
  work_end_arr = work_end.split(':');
  work_end_hour = work_end_arr[0];
  work_end_mim = work_end_arr[1];
  //通过 时间戳 获得可以配送的开始时间
  work_datetime = new Date(parseInt(start_time) * 1000);
  start_hour = work_datetime.getHours();
  start_mim = work_datetime.getMinutes();
  //获得 现在的时间
  now_datetime = new Date();
  now_hour = now_datetime.getHours();
  now_mim = now_datetime.getMinutes();

  select_torihiki_date = _this.value;
  select_torihiki_date_arr = select_torihiki_date.split('-');
  select_year = select_torihiki_date_arr[0];
  select_mon = select_torihiki_date_arr[1];
  select_day = select_torihiki_date_arr[2];
  now_year = parseInt(now_datetime.getFullYear());
  now_mon = parseInt(now_datetime.getMonth())+1;
  now_day = parseInt(now_datetime.getDate());
  // date_flag 选择时间  大于 现在时间是1 等于是2 其他是0
  date_flag=0;
  if(select_year > now_year){
    date_flag=1;
  }else if(select_year == now_year){
    if(select_mon > now_mon){
      date_flag=1;
    }else if(select_mon == now_mon){
      if(select_day > now_day){
        date_flag=1;
      }else if(select_day == now_day){
        date_flag=2;
      }else{
      }
    }else{
    }
  }else{
  }
  if(date_flag == 1){
    s_hour = work_start_hour;
    s_mim = work_start_mim;
  }else if(date_flag == 2){
  //通过 配送的开始时间 和现在时间 确定 显示时间的开始
    //根据结束时间判断
  sub_time = start_mim - work_end_mim;
  if(start_hour > work_end_hour){
    s_hour = work_start_hour;
    s_mim = work_start_mim;
  }else if(start_hour == work_end_hour){
    if(sub_time > 15){
      s_hour = start_hour;
      s_mim = sub_time+(15-sub_time%15);
    }else{
      s_hour = start_hour;
      s_mim = start_mim;
    }
  }else{
    if(sub_time+(15-sub_time%15)==60){
      s_hour = now_hour+1;
      s_mim = 0;
    }else{
      s_hour = now_hour;
      s_mim = sub_time+(15-sub_time%15);
    }
  }

  //根据开始时间判断
  sub_time = start_mim - now_mim;
  if(start_hour > now_hour){
    s_hour = start_hour;
    s_mim = start_mim;
  }else if(start_hour == now_hour){
    if(sub_time > 15){
      s_hour = start_hour;
      s_mim = sub_time+(15-sub_time%15);
    }else{
      s_hour = start_hour;
      s_mim = start_mim;
    }
  }else{
    if(sub_time+(15-sub_time%15)==60){
      s_hour = now_hour+1;
      s_mim = 0;
    }else{
      s_hour = now_hour;
      s_mim = sub_time+(15-sub_time%15);
    }
  }
  
  }else {
    alert("<?php echo TEXT_SELECT_TORIHIKI_ERROR?>");
  }
  torihiki_time_hour_str  = '<ul>';
  for(h_i=0;h_i<24;h_i++){
    if(h_i>=s_hour&&h_i<=work_end_hour){
      if(date_flag == 2&&h_i==now_hour){
        s_mim = 0;
      }
      torihiki_time_hour_str += "<li class='hour_list"+pid+"' id='hour_list"+pid+h_i+"' ";
      torihiki_time_hour_str += " onclick='show_torihiki_time_info(";
      torihiki_time_hour_str += "\""+radio_name+"\",\""+pid+"\","+h_i+","+work_end_hour;
      torihiki_time_hour_str += ","+work_end_mim+","+s_mim+")' ";
      torihiki_time_hour_str += " onDblClick='un_show_torihiki_time_info(";
      torihiki_time_hour_str += "\""+pid+"\")' ";
      torihiki_time_hour_str += " >"+h_i+"</li>";
    }else{
      torihiki_time_hour_str += "<li style='background:#f1f0ef;color:#ccc' >"+h_i+"</li>";
    }
    if((h_i+1)%6 == 0){
     torihiki_time_hour_str  += '</ul><ul>';
    }
  }
  torihiki_time_hour_str  += '</ul>';
  }else{
    torihiki_time_hour_str = '';
    shipping_torihiki_t.style.display = 'none';
  }
  torihiki_radio_hour_div = window.document.getElementById('shipping_torihiki_radio_hour'+pid);
  torihiki_radio_hour_div.innerHTML = torihiki_time_hour_str;

}
function show_torihiki_time_info(radio_name,pid,s_hour,work_end_hour,work_end_mim,s_mim){
    one_hour_check = window.document.getElementById('hour_list'+pid+s_hour);
    /*
    old_index = pid+"_"+s_hour;
    if(old_time_object.length == 0){
      old_time_object[old_index] = one_hour_check; 
      old_time_object['length'] = old_index;
    }else{
      old_time_object[old_index].style.background = '#ccc';
      old_time_object[old_index].style.color = '##f1f0ef';
      old_time_object[old_index] = one_hour_check; 
      old_time_object['length'] = old_index;
    }
    */
   if(!old_time_object[pid]){
      old_time_object[pid] = one_hour_check; 
    }else{
      old_time_object[pid].style.background = '#ccc';
      old_time_object[pid].style.color = '#000';
      old_time_object[pid] = one_hour_check; 
    }
    one_hour_check.style.background = '#FFE6E6';
    one_hour_check.style.color = '#000';
    show_row = 0;
    torihiki_time_str = "<ul>";
    row_num = 0;
    if(s_hour == work_end_hour){
      end_mim = work_end_mim;
    }else{
      end_mim = 60;
    }
    for(s_mim;s_mim<end_mim;){
      row_num++;
      if(show_row == 0 && s_mim!=0){
        if(s_mim<15){
          s_mim = 15;
          torihiki_time_str += "<li></li>";
        }else if(s_mim<30){
          s_mim = 30;
          torihiki_time_str += "<li></li>";
          torihiki_time_str += "<li></li>";
        }else if(s_mim<45){
          s_mim = 45;
          torihiki_time_str += "<li></li>";
          torihiki_time_str += "<li></li>";
          torihiki_time_str += "<li></li>";
        }else if(s_mim>=45){
          s_mim = 0;
          break;
        }
      }
      s_start = s_mim;
      s_mim+=14;
      e_start = s_mim;
      torihiki_time_str += "<li>";
      torihiki_time_str += "<input type='radio' name='"+radio_name+"' value='";
      torihiki_time_str += s_hour+":"+format_time(s_start)+"-";
      torihiki_time_str += s_hour+":"+format_time(e_start)+"'";
      torihiki_time_str += " >&nbsp;&nbsp;";        
      torihiki_time_str += s_hour+"時"+format_time(s_start)+"分";
      torihiki_time_str += " ～ ";
      torihiki_time_str += s_hour+"時"+format_time(e_start)+"分";
      torihiki_time_str += "</li>";
      show_row ++;
      s_mim++;
      if(row_num%2==0){
        torihiki_time_str += "</ul><ul>";
      }
    }
  torihiki_time_str += "</ul>";
  torihiki_radio_time_div = window.document.getElementById('shipping_torihiki_radio_time'+pid);
  torihiki_radio_time_div.innerHTML = torihiki_time_str;
}
function un_show_torihiki_time_info(pid){
  old_time_object[pid].style.background = '#ccc';
  old_time_object[pid].style.color = '#000';
  old_time_object[pid] = null; 
  torihiki_radio_time_div = window.document.getElementById('shipping_torihiki_radio_time'+pid);
  torihiki_radio_time_div.innerHTML = "";
}
function set_torihiki_date(shipping_code,work_time,start_time,pid){
  //设置 可用 取引日期
  var show_select = window.document.getElementById('shipping_torihiki_date_select'+pid);
  var show_length = show_select.options.length;
  var from_select = window.document.getElementById(shipping_code+pid);
  show_select.options.length = 1;
  for(i=0;i<from_select.length;i++){
    show_select.options.add(new Option(from_select.options[i].text,from_select.options[i].value));
  }
  window.document.getElementById('shipping_work_time'+pid).value = work_time;
  window.document.getElementById('shipping_start_time'+pid).value = start_time;

  s_torihiki = window.document.getElementById('shipping_torihiki'+pid);
  s_torihiki.style.display = 'none';
  torihiki_info_list_t = window.document.getElementById('torihiki_info_list'+pid);
  torihiki_info_list_t.style.display = 'block';
}
function clear_all_radio_checked(){
  $("input[type=radio]").each(function(){
      $(this).attr('checked',false);
  });
}
