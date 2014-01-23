var alert_update_id = '';
$(function() {
   setTimeout(function() {show_head_notice(1)}, 35000);
});

function Sound(source,volume,loop)
{
    this.source=source;
    this.volume=volume;
    this.loop=loop;
    var son;
    this.son=son;
    this.finish=false;
    this.stop=function()
    {
      $("#hidden_mp3").remove();
    }
    this.start=function()
    {
        if(this.finish)return false;
        this.son=document.createElement("embed");
        this.son.setAttribute("src",this.source);
        this.son.setAttribute("hidden","true");
        this.son.setAttribute("volume",this.volume);
        this.son.setAttribute("autostart","true");
        this.son.setAttribute("loop",this.loop);
        $("#hidden_mp3").append(this.son);
    }
    this.remove=function()
    {
      $("#hidden_mp3").remove();
        this.finish=true;
    }
    this.init=function(volume,loop)
    {
        this.finish=false;
        this.volume=volume;
        this.loop=loop;
    }
}

function splay(url){
  $("#hidden_mp3").empty();
  var sou = new Sound(url,0,true);
  sou.start();
}


//handle notice time
function calc_notice_time(leave_time, nid, start_calc, alarm_flag, alarm_date, notice_day_title, notice_hour_title, notice_min_title)
{

  if(alert_update_id == ''){
    alert_update_id = nid; 
  }
  var now_timestamp = Date.parse(new Date());
  
  now_timestamp_str = now_timestamp.toString().substr(0, 10);

  now_timestamp_tmp = parseInt(now_timestamp_str);
  
  leave_time_diff = leave_time - now_timestamp_tmp;
  
  n_day = Math.floor(leave_time_diff / (24*3600)); 
  leave_time_tmp = leave_time_diff % (24*3600);
  leave_time_seconds = leave_time_tmp % 3600;
  n_hour = (leave_time_tmp - leave_time_seconds) / 3600;
  leave_time_minute = leave_time_seconds % 60;
  n_minute = (leave_time_seconds - leave_time_minute) / 60; 
  
  if (n_day < 10) {
    n_show_day = '0'+n_day; 
  } else {
    n_show_day = n_day; 
  }
  
  if (n_hour < 10) {
    n_show_hour = '0'+n_hour; 
  } else {
    n_show_hour = n_hour; 
  }
  
  if (n_minute < 10) {
    n_show_minute = '0'+n_minute; 
  } else {
    n_show_minute = n_minute; 
  }
  
  if (leave_time_diff <= 0) {
    n_show_day = '00'; 
    n_show_hour = '00'; 
    n_show_minute = '00';
    n_day = 0; 
    n_hour = 0;
    n_minute = 0;
  }
  
  if (document.getElementById('leave_time_'+nid)) {
    $.ajax({
      dataType: 'text', 
      url: 'ajax_orders.php?action=check_play_sound',
      success: function(msg) {
        if(alarm_flag == '1'){
          document.getElementById('leave_time_'+nid).innerHTML = alarm_date; 
        }else{ 
          document.getElementById('leave_time_'+nid).innerHTML = n_show_day+notice_day_title+n_show_hour+notice_hour_title+n_show_minute+notice_min_title;
        }
        if ((n_hour == 0) && (n_minute == 0) && (n_day == 0)) {
          document.getElementById('leave_time_'+nid).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background = '#FFB3B5'; 
          var n_node=document.getElementById('head_notice');  
          if (msg == '1') {
            splay('images/notice.mp3');
          }
        }
        setTimeout(function(){calc_notice_time(leave_time, nid, 1, alarm_flag, alarm_date, notice_day_title, notice_hour_title, notice_min_title)}, 5000); 
      }
    });
  } 
}
//show all notice
function expend_all_notice(aid)
{
  if ($('#show_all_notice').css('display') == 'none') {
    $('#show_all_notice').css('display', 'block');
    $.ajax({
      url: 'ajax_notice.php?action=show_all_notice',     
      data: 'aid='+aid, 
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(data) {
        $('#show_all_notice').html(data); 
      }
    });
  } else {
    $('#show_all_notice').css('display', 'none');
    $('#show_all_notice').html(''); 
  }
}
//delete alarm notice
function delete_alarm_notice(nid, e_type)
{
  $.ajax({
      url: 'ajax_notice.php?action=delete_alarm',
      data: 'nid='+nid,
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(data) {
        if($("#notice_id_str").val()){
          var notice_id_str = $("#notice_id_str").val();
        }
        $('#show_all_notice').css('display', 'none');
        $('#show_all_notice').html(''); 
        show_head_notice(0);
        if($("#notice_id_str").val()){
          $("#notice_id_str").remove();
          $('#show_all_notice').append('<input type="hidden" value="'+notice_id_str+'" id="notice_id_str">');
        }
      } 
      });
}
//delete notice
function delete_micro_notice(nid, e_type)
{
  $.ajax({
      url: 'ajax_notice.php?action=delete_micro',
      data: 'nid='+nid,
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(data) {
        $('#show_all_notice').css('display', 'none');
        $('#show_all_notice').html(''); 
        show_head_notice(0);
      } 
      });
}
//show header notice
function show_head_notice(no_type)
{
  $.ajax({
      url: 'ajax_notice.php?action=show_head_notice',
      type: 'POST',
      dataType: 'text',
      async: false,
      success: function(data) {
        if (data != '') {
          data_info = data.split('|||');

          var notice_new_flag = true;
          if($("#notice_id_str").val()){
            var notice_id_str = $("#notice_id_str").val();
            var notice_id_array = new Array();
            notice_id_array = notice_id_str.split(",");
            for(x in notice_id_array){

              if(notice_id_array[x] == data_info[2]){

                notice_new_flag = false;
                break;
              }
            } 
          }

          var update_time_id = $("#update_time_id").val();
          if (document.getElementById('leave_time_'+data_info[2])) {
            if (data_info[0] != document.getElementById('more_single').value) {
              $('#show_head_notice').html(data_info[3]); 
            }
          } else {
            $('#show_head_notice').html(data_info[3]); 
          }
          
          if (data_info[1] <= 0) {
            document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background = '#FFB3B5'; 
          } else {
            orgin_bg = document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background; 
            if (orgin_bg.indexOf('rgb(255, 179, 181)') > 0) {
              document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background = '#FFFFFF'; 
            }
          }
          if(data_info[5] == '1'){
            $("#alarm_id_"+data_info[4]).html('ON');
          }else{
            $("#alarm_id_"+data_info[4]).html('OFF'); 
          }
          $("#alarm_user_"+data_info[4]).html(data_info[6]+'&nbsp;'+header_text_alert_link);
          $("#icon_images_id").html(data_info[4]);
          $("#memo_contents").html(data_info[5]);
        } else {
          $('#show_head_notice').html(data); 
          orgin_bg = document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background; 
          if (orgin_bg.indexOf('rgb(255, 179, 181)') > 0) {
            document.getElementById('leave_time_'+data_info[2]).parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.style.background = '#FFFFFF'; 
          }
        }
        
        if (no_type == 1) { 
          if((alert_update_id != data_info[2] && notice_new_flag == true) || (data_info[6] > update_time_id)){
            $("#show_all_notice").hide();
            $('#alert_buttons').css('background-color','darkred');
            $('#alert_time').css('background-color','darkred');
            $('#alert_close').css('background-color','darkred');
            alert_update_id = data_info[2];
          }else{
            if($("#notice_id_str").val()){
              $("#notice_id_str").remove();
              $('#show_all_notice').append('<input type="hidden" value="'+notice_id_str+'" id="notice_id_str">'); 
            }
          }
          setTimeout(function() {show_head_notice(1)}, 35000);
        }
      } 
      });
}

//check new preorder
function checkHeadPreOrders(t)
{
  $.ajax({
    dataType: 'text',
    url: 'ajax_preorders.php?action=get_new_orders&type=check&prev_customer_action='+t,
    success: function(msg) {
      if (msg == '1') {
        check_head_pre_o_single = '1';
      } else {
        check_head_pre_o_single = '0';
      }
    }
  });
}
//play sound
function playHeadSound()  
{  
  var hnode=document.getElementById('head_sound');  
  if(hnode!=null)  
  {  
   $.ajax({
    dataType: 'text', 
    url: 'ajax_orders.php?action=check_play_sound',
    success: function(msg) {
      if (msg == '1') {
        splay('images/presound.mp3');
      }
    }
   }); 
  }
}
//check preorder header
function check_preorder_head() {
  $.ajax({
    dataType: 'text',
    url: 'ajax_preorders.php?action=last_customer_action',
    success: function(last_head_customer_action) {
      if (last_head_customer_action != cfg_head_last_customer_action && prev_head_customer_action != last_head_customer_action){
        checkHeadPreOrders(prev_head_customer_action != '' ?  prev_head_customer_action : cfg_head_last_customer_action);
        if (check_head_pre_o_single == '1') {
          $('.preorder_head').css('background-color', '#83dc94');
          prev_head_customer_action = last_head_customer_action;
          playHeadSound();
          check_head_pre_o_single = '0'; 
        }
      }
    }
  });
  setTimeout(function(){check_preorder_head()}, 70000);
}
  
//check new order
function checkHeadOrders(t)
{
  $.ajax({
    dataType: 'text',
    url: 'ajax_orders.php?action=get_new_orders&type=check&prev_customer_action='+t,
    success: function(msg) {
      if (msg == '1') {
        check_head_o_single = '1';
      } else {
        check_head_o_single = '0';
      }
    }
  });
}
//play sound
function playOrderHeadSound()  
{  
  var ohnode=document.getElementById('head_warn');  
  if(ohnode!=null)  
  {  
   $.ajax({
    dataType: 'text', 
    url: 'ajax_orders.php?action=check_play_sound',
    success: function(msg) {
      if (msg == '1') {
        splay('images/warn.mp3');
      }
    }
   });
  }
}
//check order header
function check_order_head() {
  $.ajax({
    dataType: 'text',
    url: 'ajax_orders.php?action=last_customer_action',
    success: function(last_ohead_customer_action) {
      if (last_ohead_customer_action != cfg_ohead_last_customer_action && prev_ohead_customer_action != last_ohead_customer_action){
        checkHeadOrders(prev_ohead_customer_action != '' ?  prev_ohead_customer_action : cfg_ohead_last_customer_action);
        if (check_head_o_single == '1') {
          $('.preorder_head').css('background-color', '#ffcc99');
          prev_ohead_customer_action = last_ohead_customer_action;
          playOrderHeadSound();
          check_head_o_single = '0';    
        }
      }
    }
  });
  setTimeout(function(){check_order_head()}, 90000);
}
