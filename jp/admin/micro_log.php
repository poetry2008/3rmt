<?php
/*
  $Id$
*/
  require('includes/application_top.php');



  if (isset($_GET['action']) && $_GET['action']) {
      header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
      # 永远是改动过的
      header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
      # HTTP/1.1
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      # HTTP/1.0
      header("Pragma: no-cache");
    switch ($_GET['action']) {
      case 'set_read':
        $log_read_raw = tep_db_query("select * from micro_to_user where user = '".$ocertify->auth_user."' and micro_id = '".$_POST['lid']."'" ); 
        $log_read = tep_db_fetch_array($log_read_raw);
        if ($log_read) {
          tep_db_query("update `micro_to_user` set `is_read` = '".$_POST['is_read']."' where micro_id = '".$_POST['lid']."' and user = '".$ocertify->auth_user."'"); 
        } else {
          tep_db_query("insert into `micro_to_user` values('".$_POST['lid']."', '".$ocertify->auth_user."', '".$_POST['is_read']."')"); 
        } 
        
        echo $_POST['is_read']; 
        exit; 
        break;
      case 'load':
        $logs = array();
        $query = tep_db_query("select * from micro_logs where deleted = '0' order by alarm='".date('Y-m-d')."' desc,sort_order desc limit 20");
        while($l = tep_db_fetch_array($query)) {
          $logs_read_raw = tep_db_query("select is_read from micro_to_user where micro_id = '".$l['log_id']."' and user = '".$ocertify->auth_user."'"); 
          $logs_read = tep_db_fetch_array($logs_read_raw);
          if ($logs_read) {
            $l['is_read'] = $logs_read['is_read'];   
          } else {
            $l['is_read'] = 0;   
          }
          $logs[] = $l;
        }
         
        exit(json_encode($logs));
      case 'more':
        $logs = array();
        $query = tep_db_query("select * from micro_logs where deleted = '0' and alarm!='".date('Y-m-d')."' and sort_order<'".strtotime($_GET['last_sort_order'])."' order by sort_order desc limit 20");
        while($l = tep_db_fetch_array($query))
          $logs[] = $l;
        exit(json_encode($logs));
      case 'delete': 
        tep_db_query("delete from micro_logs where log_id ='".$_GET['id']."'");
        $notice_raw = tep_db_query("select id from ".TABLE_NOTICE." where from_notice = '".$_GET['id']."' and type = '1'"); 
        $notice_res = tep_db_fetch_array($notice_raw);
        if ($notice_res) {
          tep_db_query("delete from ".TABLE_NOTICE_TO_MICRO_USER." where notice_id = '".$notice_res['id']."'"); 
        }
        tep_db_query("delete from ".TABLE_NOTICE." where from_notice = '".$_GET['id']."' and type = '1'"); 
        tep_db_query("delete from micro_to_user where micro_id = '".$_GET['id']."'"); 
        exit; 
        exit($_GET['id']);
        break;
      case 'new': 
        $insert_time = date('Y-m-d H:i:s', time()); 
        $arr = array(
            'content' => $_POST['content'],
            'alarm'   => $_POST['alarm'],
            'author'  => $ocertify->auth_user,
            'sort_order' => time(),
            'date_added' => $insert_time,
            'level' => $_POST['level']
          );
        tep_db_perform('micro_logs',$arr);
        $arr['log_id'] = tep_db_insert_id();
        
        if (!empty($_POST['alarm']) && preg_match('/^(\d){4}-(\d){2}-(\d){2}$/', $_POST['alarm']) && ($_POST['alarm'] != '0000-00-00')) {
          $sql_data_array = array(
              'type' => 1,
              'title' => mb_substr($_POST['content'], 0, 30, 'utf-8'),
              'set_time' => $_POST['alarm'].' 00:00:00',
              'from_notice' => $arr['log_id'],
              'created_at' => date('Y-m-d H:i:s', time()),
              );
          tep_db_perform(TABLE_NOTICE, $sql_data_array); 
        } 
        $arr['is_read'] = 0; 
        exit(json_encode($arr));
      case 'update':
        $arr = array(
            'content' => $_POST['content'],
            'last_modified' => time(),
            'level'   => $_POST['level']
          );
        if (!empty($_POST['alarm'])) {
          if (preg_match('/^(\d){4}-(\d){2}-(\d){2}$/', $_POST['alarm']) && ($_POST['alarm'] != '0000-00-00')) {
            $arr['alarm'] = date('Y-n-j',strtotime($_POST['alarm'])); 
          }
        } else {
          $arr['alarm'] = '0000-00-00'; 
        }
        tep_db_perform('micro_logs',$arr,'update','log_id='.$_GET['id']);
       
        if (!empty($_POST['alarm'])) {
          if (preg_match('/^(\d){4}-(\d){2}-(\d){2}$/', $_POST['alarm']) && ($_POST['alarm'] != '0000-00-00')) {
          
            $notice_exists_raw = tep_db_query("select * from ".TABLE_NOTICE." where from_notice = '".$_GET['id']."' and type = '1'");  
            if (tep_db_num_rows($notice_exists_raw) > 0) {
              tep_db_query("update `".TABLE_NOTICE."` set `title` = '".mb_substr($_POST['content'], 0, 30, 'utf-8')."', `set_time` = '".date('Y-m-d 00:00:00', strtotime($_POST['alarm']))."' where `from_notice` = '".$_GET['id']."' and `type` = '1'"); 
            } else {
              $sql_data_array = array(
                  'type' => 1,
                  'title' => mb_substr($_POST['content'], 0, 30, 'utf-8'),
                  'set_time' => $_POST['alarm'].' 00:00:00',
                  'from_notice' => $_GET['id'],
                  'created_at' => date('Y-m-d H:i:s', time()),
                  );
              tep_db_perform(TABLE_NOTICE, $sql_data_array); 
            }
          } else {
            $notice_raw = tep_db_query("select id from ".TABLE_NOTICE." where from_notice = '".$_GET['id']."' and type = '1'"); 
            $notice_res = tep_db_fetch_array($notice_raw);
            if ($notice_res) {
              tep_db_query("delete from ".TABLE_NOTICE_TO_MICRO_USER." where notice_id = '".$notice_res['id']."'"); 
            }
            tep_db_query("delete from ".TABLE_NOTICE." where from_notice = '".$_GET['id']."' and type = '1'"); 
          }
        } else {
          $notice_raw = tep_db_query("select id from ".TABLE_NOTICE." where from_notice = '".$_GET['id']."' and type = '1'"); 
          $notice_res = tep_db_fetch_array($notice_raw);
          if ($notice_res) {
            tep_db_query("delete from ".TABLE_NOTICE_TO_MICRO_USER." where notice_id = '".$notice_res['id']."'"); 
          }
          tep_db_query("delete from ".TABLE_NOTICE." where from_notice = '".$_GET['id']."' and type = '1'"); 
        }
         
        $log = tep_db_fetch_array(tep_db_query("select * from micro_logs where log_id='".$_GET['id']."'"));
        tep_db_query("delete from micro_to_user where micro_id = '".$_GET['id']."'"); 
        $log_read_raw = tep_db_query("select is_read from micro_to_user where micro_id = '".$_GET['id']."' and user = '".$ocertify->auth_user."'"); 
        $log_read = tep_db_fetch_array($log_read_raw); 
        if ($log_read) {
          $log['is_read'] = $log_read['is_read'];   
        } else {
          $log['is_read'] = 0;   
        }
        exit(json_encode($log));
      case 'chpos':
        $log1 = tep_db_fetch_array(tep_db_query("select * from micro_logs where log_id='".$_GET['id1']."'"));
        $log2 = tep_db_fetch_array(tep_db_query("select * from micro_logs where log_id='".$_GET['id2']."'"));
        tep_db_perform('micro_logs',array('sort_order' => $log2['sort_order']),'update','log_id='.$_GET['id1']);
        tep_db_perform('micro_logs',array('sort_order' => $log1['sort_order']),'update','log_id='.$_GET['id2']);
        exit;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">

<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/3.4.1/build/yui/yui.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script>
//function auto_reload(){
//  window.location.reload();
//}

//timerID = setInterval("auto_reload()", 15 * 60 * 1000); //1秒：1000

</script>
<style type="text/css">
.yui3-skin-sam input {
  float:left;
}
a.dpicker {
	width: 16px;
	height: 16px;
	border: none;
	color: #fff;
	padding: 0;
	margin: 0;
	overflow: hidden;
        display:block;	
        cursor: pointer;
	background: url(./includes/calendar.png) no-repeat; 
	float:left;
} 
.popup-calendar {
top:20px;
left:-95px;
left:-163px;
}
.number{
font-size:24px;
font-weight:bold;
width:20px;
text-align:center;
}
form{
margin:0;
padding:0;
}
.alarm_input{
width:75px;
}
.log{
  border:#999 solid 1px;
  background:#eee;
  clear: both;
}
.log .content{
  padding:3px 0;
  font-size:12px;
}
.log .content textarea{ font-size:12px;}
.log .alarm{
  display:none;
  background:url(images/icons/alarm.gif) no-repeat left center;
}
.log .level{
  font-size:10px;
  font-weight:bold;
  display:none;
  width:99px;
}
.log .level input{
margin:0;
padding:0;
}
.log .info{
  font-size:10px;
  background:#fff;
  text-align:right;
  /*
  position:relative;
  right:0;
  bottom:0;
  */
  /*padding-left:18px;
  background:url(images/icons/info.gif) no-repeat left center;*/
}
.info02{
width:50px;
padding:0 5px;
}
.log .action{
text-align:center;
  font-size:10px;
}
.edit_action{
  display:none;
/*float:right;*/
  font-size:10px;
line-height:24px;
padding-right:5px;
}
.action a{
padding:0 3px;
}
textarea,input{
  font-size:14px;
}
textarea{
  width:100%;
  padding:0;
  margin:0;
}
.alarm_on{
  border:2px solid #ff8e90;
  background:#ffe6e6;
}
.clr{
clear:both;
width:100%;
height:5px;
overflow:hidden;
}
.popup-calendar-wrapper{
float:left;
}

#new_yui3 {
	margin-left:-170px;
	margin-left:-19px\9;
	top:235px;
	top:208px\9;
	position: absolute;
	z-index:200px;
}
@media screen and (-webkit-min-device-pixel-ratio:0) {
#new_yui3{
	top:208px;
	margin-left:-430px;
    padding-left:260px;
	position: absolute;
	z-index:200px;
}
}
</style>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
<td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
<div class="compatible">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
	  <td class="pageHeading"><?php echo HEADING_TITLE;?></td>
            <td class="pageHeading" align="right"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
          <form action="?action=new" method="post" id="log_form">
          <table width="100%">
          <tr>
          <td colspan="3" id="div_input"><textarea name="content"></textarea></td>
          </tr>
          <tr>
            <td width="250px">
              <input type="radio" name="level" value="0" checked="checked" id="level_0"><?php echo TYPEA;?>
              <input type="radio" name="level" value="1" id="level_1"><?php echo TYPEB;?>
              <input type="radio" name="level" value="2" id="level_2"><?php echo TYPEC;?>
            </td>
            <td>
            <div class="yui3-skin-sam yui3-g">
            <input type="text" name="alarm" id="input_alarm" /><a href="javascript:void(0);" onclick="open_new_calendar();" class="dpicker"></a>
            <input type="hidden" name="toggle_open" value="0" id="toggle_open"> 
            <div class="yui3-u" id="new_yui3">
            <div id="mycalendar"></div>
            </div> 
            </div>
            </td>
	    <td align="right"><input type="submit" value="<?php echo SAVE_BUTTON;?>" /></td>
          </tr>
          </table>
          </form>
<script>
$(function() {
  load_log();
  setInterval(function(){load_log()},1000 * 60 * 10);
  $('#log_form').ajaxForm({
    beforeSubmit: function(formData, form, options){
      if(formData[0]['value']) 
        return true; 
      else 
        return false;
    },
    dataType: 'json',
    success: function(text){
            add_log(text);
            $('#log_form')[0].reset();
            show_head_notice(0); 
    }
  }); 
  

});

function open_new_calendar()
{
  var is_open = $('#toggle_open').val(); 
  if (is_open == 0) {
    browser_str = navigator.userAgent.toLowerCase(); 
    if (browser_str.indexOf("msie 9.0") > 0) {
      $('#new_yui3').css('margin-left', '-170px'); 
    }
    $('#toggle_open').val('1'); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
        var calendar = new Y.Calendar({
            contentBox: "#mycalendar",
            width:'170px',

        }).render();
      var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        $("#input_alarm").val(dtdate.format(newDate)); 
        $('#toggle_open').val('0');
        $('#toggle_open').next().html('<div id="mycalendar"></div>');
      });
    });
  }
}
function open_update_calendar(mid)
{
  var is_open = $('#toggle_open_'+mid).val(); 
  if (is_open == 0) {
    $('#toggle_open_'+mid).val('1'); 
    c_y_pos = $("#dpk_"+mid).position().top+16; 
    $("#pos_m_"+mid).css({"right":"87px", "position":"absolute"}); 
    $("#pos_m_"+mid).css('top', c_y_pos+'px'); 
    YUI().use('calendar', 'datatype-date',  function(Y) {
      var calendar = new Y.Calendar({
            contentBox: "#calc_"+mid+"_show",
            width:'170px',

        }).render();
      var dtdate = Y.DataType.Date;
      calendar.on("selectionChange", function (ev) {
        var newDate = ev.newSelection[0];
        $("#alarm_date_"+mid).val(dtdate.format(newDate)); 
        $('#toggle_open_'+mid).val('0');
        $('#toggle_open_'+mid).next().html('<div id="calc_'+mid+'_show"></div>');
      });
    });
  }
}
function toggle_log_read(t_object, lid, is_read)
{
  $.ajax({
    type: "POST",
    data:'lid='+lid+'&is_read='+is_read,
    async:false,
    url: 'micro_log.php?action=set_read',
    success: function (msg) {
      if (msg == 1) {
        str = '<a href="javascript:void(0);" onclick="toggle_log_read(this, \''+lid+'\', \'0\');"><img src="images/icons/green_right.gif"></a>'; 
      } else {
        str = '<a href="javascript:void(0);" onclick="toggle_log_read(this, \''+lid+'\', \'1\');"><img src="images/icons/gray_right.gif"></a>'; 
      }
      $(t_object).parent().html(str); 
    }
    });
}

function parseDate(str){  
  if(typeof str == 'string'){  
    var results = str.match(/^ *(\d{4})-(\d{1,2})-(\d{1,2}) *$/);  
    if(results && results.length>3)  
      return new Date(parseInt(results[1]),parseInt(results[2]) -1,parseInt(results[3]));   
    results = str.match(/^ *(\d{4})-(\d{1,2})-(\d{1,2}) +(\d{1,2}):(\d{1,2}):(\d{1,2}) *$/);  
    if(results && results.length>6)  
      return new Date(parseInt(results[1]),parseInt(results[2]) -1,parseInt(results[3]),parseInt(results[4]),parseInt(results[5]),parseInt(results[6]));   
    results = str.match(/^ *(\d{4})-(\d{1,2})-(\d{1,2}) +(\d{1,2}):(\d{1,2}):(\d{1,2})\.(\d{1,9}) *$/);  
    if(results && results.length>7)  
      return new Date(parseInt(results[1]),parseInt(results[2]) -1,parseInt(results[3]),parseInt(results[4]),parseInt(results[5]),parseInt(results[6]),parseInt(results[7]));   
  }  
  return null;
} 
function log_html(text){
  t = new Date;
  var c_admin_name = '<?php echo $ocertify->auth_user;?>'; 
  if (
    text['alarm'] == t.getFullYear() + '-' + (t.getMonth()+1) + '-' + t.getDate()
    || text['alarm'] == t.getFullYear() + '-0' + (t.getMonth()+1) + '-' + t.getDate()
    || text['alarm'] == t.getFullYear() + '-' + (t.getMonth()+1) + '-0' + t.getDate()
    || text['alarm'] == t.getFullYear() + '-0' + (t.getMonth()+1) + '-0' + t.getDate()
    )
    c = 'alarm_on';
   else 
    c = 'alarm_off';
  t2 = parseDate(text['date_added']);
  $str  = '<form action="?action=update&id='+text['log_id']+'" id="log_form_'+text['log_id']+'" method="post">';
  $str += '  <table cellpadding="0" cellspacing="0" class="log '+c+'" id="log_'+text['log_id']+'" width="100%" border="0">';
  $str += '    <tr>';
  $str += '<td width="20">'; 
  $str += '<div>';
  if (text['is_read'] == 0) {
    toggle_read = 1; 
  } else {
    toggle_read = 0; 
  }
  $str += '<a href="javascript:void(0);" onclick="toggle_log_read(this, \''+text['log_id']+'\', \''+toggle_read+'\');">'; 
  if (text['is_read'] == 0) {
    $str += '<img src="images/icons/gray_right.gif">'; 
  } else {
    $str += '<img src="images/icons/green_right.gif">'; 
  }
  $str += '</a>';
  $str += '</div>';
  $str += '</td>'; 
  $str += '      <td class="number" style="color:'+(text['level']!='0'?(text['level']=='2'?'red':'orange'):'black')+'">'+(parseInt(text['level'])+1)+'</td>';
  if(t2.getHours()<10){
     var hour = '0'+t2.getHours();
  }else{
     var hour = t2.getHours();
  }
  if(t2.getMinutes()<10){
     var minutes = '0'+t2.getMinutes();
  }else{
     var minutes = t2.getMinutes();
  }
  
  var log_date = text['date_added'];
  var log_date_len = text['date_added'].length;
  var log_date_str = log_date.substring(0, log_date_len-3); 
  
  $str += '      <td style="background:#fff;"><div style="background:#fff;"><div class="content">'+text['content'].replace(/\n/g,'<br>')+'</div><div class="info">'+log_date_str+'</div></div></td>';
  $str += '      <td class="info02">';
  $str += '           <div class="level">'+parseInt(text['level'])+'</div>';
  $str += '           <div class="alarm">'+text['alarm']+'</div>';
  $str += '           <div class="action"><a href="javascript:void(0);" onclick="edit_log('+text['log_id']+')"><img src="images/icons/edit_img.gif"></a> <a href="javascript:void(0)" onclick="up('+text['log_id']+')" ><img src="images/icons/up.gif"></a> <a href="javascript:void(0);" onclick="delete_log('+text['log_id']+')"><img src="images/icons/del_img.gif"></a> <a href="javascript:void(0)" onclick="down('+text['log_id']+')" ><img src="images/icons/down.gif"></a></div>';
  $str += '           <div class="edit_action"><input type="submit" value="<?php echo SAVE_BUTTON;?>" /></div>';
  $str += '      </td>';
  $str += '    </tr>';
  $str += '  </table>';
  $str += '<div class="clr"></div>';
  $str += '</form>';
  return $str;
}

function add_log(text){
  $('#div_logs').prepend(log_html(text));
  band_form(text['log_id']);
}

function band_form(log_id){

  $('#log_form_'+log_id).ajaxForm({
    dataType:'json',
    success: function(j){
      show_head_notice(0); 
      $('#log_form_'+j['log_id']).replaceWith(log_html(j));
      band_form(j['log_id']);
    }
  });

}

function append_log(text){
  $('#div_logs').append(log_html(text));
  $('#log_form_'+text['log_id']).ajaxForm({
    dataType:'json',
    success: function(j){
      $('#log_'+j['log_id']).replaceWith(log_html(j));
    }
  });
}

function edit_log(id)
{
  $('#log_'+id+' .content').html('<textarea name="content" style="height:'+ ($('#log_'+id+' .content').height()+ 20) +'px">'+$('#log_'+id+' .content').html().replace(/<br>/ig,'\n')+'</textarea>');
  
  $('#log_'+id+' .alarm').html('<div id="demo" class="yui3-skin-sam yui3-g"><a id="dpk_'+id+'" href="javascript:void(0);" class="dpicker" onclick="open_update_calendar('+id+');"></a><input class="alarm_input" id="alarm_date_'+id+'" type="text" name="alarm" value="'+$('#log_'+id+' .alarm').html()+'"><input type="hidden" name="toggle_open_'+id+'" id="toggle_open_'+id+'" value="0"><div id="pos_m_'+id+'" class="yui3-u"><div id="calc_'+id+'_show"></div></div></div></div>');

  l = $('#log_'+id+' .level').html();
  $('#log_'+id+' .level').show().html('<input type="radio" name="level" value="0" '+(l == '0' ? 'checked' : '')+' /><?php echo TYPEA;?><input type="radio" name="level" value="1" '+(l == '1' ? 'checked' : '')+' /><?php echo TYPEB;?><input type="radio" name="level" value="2" '+(l == '2' ? 'checked' : '')+' /><?php echo TYPEC;?>');
  $('#log_'+id+' .edit_action').show();
  $('#log_'+id+' .alarm').show();
  $('#log_'+id+' .action').hide();
}
/*
function refresh()
{
}
*/
function delete_log(id)
{
  if (confirm("<?php echo DELETE_CONFIRMATION;?>")) {
    url = 'micro_log.php?id='+id+'&action=delete';
    $.ajax({
      url: url,
      dataType: 'text',
      success: function(text) {
        $('#log_' + id).hide();
        show_head_notice(0); 
      }
    });
  }
}
function up(id)
{
  id1 = id;
  id2 = $('#log_form_'+id).prev().children('.log').attr('id').substring(4);
  
  $.ajax({
    url: '?action=chpos&id1='+id1+'&id2='+id2,
    dataType: 'text',
    success: function(text) {
      $('#log_form_'+id).prev().before($('#log_form_'+id));
    }
  });
}
function down(id)
{
  id1 = id;
  id2 = $('#log_form_'+id).next().children('.log').attr('id').substring(4);
  
  $.ajax({
    url: '?action=chpos&id1='+id1+'&id2='+id2,
    dataType: 'text',
    success: function(text) {
      $('#log_form_'+id).next().after($('#log_form_'+id));
    }
  });
}
function load_log(){
   $.ajax({
    dataType: 'json',
    url: '?action=load',
    success: function(text) {
      for(i=text.length;i>0;i--){
        if (false == $('#log_'+text[i-1]['log_id']).length > 0) {
        add_log(text[i-1]);
        }
      }
    }
  });
}
function more_log(){
  t = new Date;
  if ($('#div_logs .log:last').attr('class') == 'log alarm_off') {
    last_sort_order = $('#div_logs .log:last .info').html();
  } else {
    last_sort_order = t.getFullYear() + '-' + (t.getMonth()+1) + '-' + t.getDate();
  }
  $.ajax({
    dataType: 'json',
    url: '?action=more&last_sort_order='+last_sort_order,
    success: function(text) {
      
      for(i=0;i<text.length;i++){
        //if (false == $('#log_'+text[i-1]['log_id']).length > 0) {
        append_log(text[i]);
        //}
      }
      
    }
  });
}
</script>

          <div id="div_logs"></div>
              <!--
	      <div id="div_more"><button onClick="more_log()"><?php echo FURTHER_STATED; ?></button></div>
              -->
        </td>
      </tr>
    </table>
    </div>
    </div>
  </td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
