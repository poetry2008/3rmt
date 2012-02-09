<?php
/*
	JP、GM共通ファイル
*/
?>

<script language="javascript"><!--

var submitted = false;

function check_form() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  if (submitted == true) {
    alert("<?php echo JS_ERROR_SUBMITTED; ?>");
    return false;
  }

  var first_name = document.account_edit.firstname.value;
  var last_name = document.account_edit.lastname.value;
  var email_address = document.account_edit.email_address.value;

  if (document.account_edit.elements['firstname'].type != "hidden") {
    if (first_name == '' || first_name.length < <?php echo ENTRY_FIRST_NAME_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_FIRST_NAME; ?>";
      error = 1;
    }
  }

  if (document.account_edit.elements['lastname'].type != "hidden") {
    if (last_name == '' || last_name.length < <?php echo ENTRY_LAST_NAME_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_LAST_NAME; ?>";
      error = 1;
    }
  }

  if (document.account_edit.elements['email_address'].type != "hidden") {
    if (email_address == '' || email_address.length < <?php echo ENTRY_EMAIL_ADDRESS_MIN_LENGTH; ?>) {
      error_message = error_message + "<?php echo JS_EMAIL_ADDRESS; ?>";
      error = 1;
    }
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    submitted = true;
    return true;
  }
}
// show shipping date time 
/*
var shipping_torihiki;
var torihiki_radio_div;
function format_time(str){
  if(str >=10){
    return str+"";
  }else{
    return "0"+str;
  }
}
//下面的方法 需要添加 参数 才能适用于多个配送
function show_address_book(){
  //显示 地址本
  address_list = window.document.getElementById('address_book_list');
  address_list.style.display = 'block';
}
function show_shipping_method(){
  shipping_list = window.document.getElementById('shipping_list');
  shipping_list.style.display = 'block';
}
function create_address_book(){
  //跳转到 创建地址页

}
function show_torihiki_time(_this,radio_name){
  shipping_torihiki = window.document.getElementById('shipping_torihiki');
  if(_this.value != ''){
  shipping_torihiki.style.display = 'block';
  //通过隐藏 获得 工作时间 和 开始时间
  var work_time = window.document.getElementById('shipping_work_time').value;
  var start_time = window.document.getElementById('shipping_start_time').value;
  torihiki_time_str = get_torihiki_time_list(work_time,start_time,_this.value,radio_name,'');
  }else{
    torihiki_time_str = '';
    shipping_torihiki.style.display = 'none';
  }
  torihiki_radio_div = window.document.getElementById('shipping_torihiki_radio');
  torihiki_radio_div.innerHTML = torihiki_time_str;
  
}
function set_torihiki_date(shipping_code,work_time,start_time){
  //设置 可用 取引日期
  var show_select = window.document.getElementById('shipping_torihiki_date_select');
  var show_length = show_select.options.length;
  var from_select = window.document.getElementById(shipping_code);
  show_select.options.length = 1;
  for(i=0;i<from_select.length;i++){
    show_select.options.add(new Option(from_select.options[i].text,from_select.options[i].value));
  }
  window.document.getElementById('shipping_work_time').value = work_time;
  window.document.getElementById('shipping_start_time').value = start_time;

  s_torihiki = window.document.getElementById('shipping_torihiki');
  s_torihiki.style.display = 'none';
  torihiki_info_list = window.document.getElementById('torihiki_info_list');
  torihiki_info_list.style.display = 'block';
}

function get_torihiki_time_list(work_time,start_time,torihiki_date,radio_name,check_time){
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

  select_torihiki_date = torihiki_date; 
  select_torihiki_date_arr = select_torihiki_date.split('-');
  select_year = parseInt(select_torihiki_date_arr[0]);
  select_mon = parseInt(select_torihiki_date_arr[1]);
  select_day = parseInt(select_torihiki_date_arr[2]);
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
  show_row = 0;
  torihiki_time_str = "<ul>";
  row_num = 0;
  for(s_hour;s_hour<=work_end_hour;s_hour++){
    if(s_hour == work_end_hour){
      end_mim = work_end_mim;
    }else{
      end_mim = 60;
    }
    for(s_mim;s_mim<end_mim;){
      row_num++;
      if(show_row == 0){
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
      if(check_time == s_hour+":"+format_time(s_start)+"-"+s_hour+":"+format_time(e_start)){
        torihiki_time_str += " checked='true' ";
      }
      torihiki_time_str += " >&nbsp;&nbsp;";        
      torihiki_time_str += s_hour+"時"+format_time(s_start)+"分";
      torihiki_time_str += " ～ ";
      torihiki_time_str += s_hour+"時"+format_time(e_start)+"分";
      torihiki_time_str += "</li>";
      show_row ++;
      s_mim++;
      if(row_num%4==1){
        torihiki_time_str += "</ul><ul>";
      }
    }
    s_mim=0;
  }
  torihiki_time_str += "</ul>";
  return torihiki_time_str;
}
*/
//--></script>
