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

      case 'load':
        $logs = array();
        $query = tep_db_query("select * from micro_logs where deleted = '0' order by alarm='".date('Y-m-d')."' desc,sort_order desc limit 20");
        while($l = tep_db_fetch_array($query))
          $logs[] = $l;
        exit(json_encode($logs));
      case 'more':
        $logs = array();
        $query = tep_db_query("select * from micro_logs where deleted = '0' and alarm!='".date('Y-m-d')."' and sort_order<'".strtotime($_GET['last_sort_order'])."' order by sort_order desc limit 20");
        while($l = tep_db_fetch_array($query))
          $logs[] = $l;
        exit(json_encode($logs));
      case 'delete': 
        tep_db_query("delete from micro_logs where log_id ='".$_GET['id']."'");
        //tep_db_perform('micro_logs',array('deleted' => 1),'update','log_id='.$_GET['id']);
        exit($_GET['id']);
        break;
      case 'new': 
        $arr = array(
            'content' => $_POST['content'],
            'alarm'   => $_POST['alarm'],
            'author'  => $ocertify->auth_user,
            'sort_order' => time(),
            'date_added' => date('Y-m-d H:i:s', time()),
            'level' => $_POST['level']
          );
        tep_db_perform('micro_logs',$arr);
        $arr['log_id'] = tep_db_insert_id();
        exit(json_encode($arr));
      case 'update':
        $arr = array(
            'content' => $_POST['content'],
            'alarm'   => date('Y-n-j',strtotime($_POST['alarm'])),
            'last_modified' => time(),
            'level'   => $_POST['level']
          );
        tep_db_perform('micro_logs',$arr,'update','log_id='.$_GET['id']);
        //$arr['log_id'] = $_GET['id'];
        $log = tep_db_fetch_array(tep_db_query("select * from micro_logs where log_id='".$_GET['id']."'"));
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
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/styles.css">

<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/javascript/datePicker.js"></script>
<script>
//function auto_reload(){
//  window.location.reload();
//}

//timerID = setInterval("auto_reload()", 15 * 60 * 1000); //1秒：1000

</script>
<style type="text/css">
a.date-picker{
display:block;
float:none;
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
width:80px;
}
.log{
  border:#999 solid 1px;
  background:#eee;
  clear: both;
}
.log .content{
  padding:3px;
  font-size:12px;
}
.log .alarm{
  display:none;
  font-size:10px;
  background:url(images/icons/alarm.gif) no-repeat left center;
}
.log .level{
  font-size:10px;
  font-weight:bold;
  display:none;
  width:100px;
  *width:120px;
}
.log .level input{
margin:0;
padding:0;
}
.log .info{
  font-size:10px;
  background:#fff;
  position:absolute;
  right:0;
  bottom:0;
  /*padding-left:18px;
  background:url(images/icons/info.gif) no-repeat left center;*/
}
.info02{
width:50px;
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
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">引継メモ</td>
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
              <input type="radio" name="level" value="0" checked="checked" id="level_0">重1
              <input type="radio" name="level" value="1" id="level_1">重2
              <input type="radio" name="level" value="2" id="level_2">重3
            </td>
            <td><!--<img src="images/icons/alarm.gif">--><input type="text" name="alarm" id="input_alarm" /></td>
            <td align="right"><input type="submit" value="メモ保存" /></td>
          </tr>
          </table>
          </form>
<script>
$(function() {
  $.datePicker.setDateFormat('ymd','-');
  $('#input_alarm').datePicker();
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
    }
  }); 
  
});

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
  $str += '      <td style="background:#fff;"><div style="background:#fff;"><div class="content">'+text['content'].replace(/\n/g,'<br>')+'</div><div class="info">'+t2.getFullYear() + '/' + (t2.getMonth()+1) + '/' + t2.getDate()+ ' ' + hour + ':' + minutes+'</div></div></td>';
  $str += '      <td class="info02">';
  $str += '           <div class="level">'+parseInt(text['level'])+'</div>';
  $str += '           <div class="alarm">'+text['alarm']+'</div>';
  $str += '           <div class="action"><a href="javascript:void(0);" onclick="edit_log('+text['log_id']+')"><img src="images/icons/preview.gif"></a> <a href="javascript:void(0);" onclick="delete_log('+text['log_id']+')"><img src="images/icons/delete.gif"></a> <a href="javascript:void(0)" onclick="up('+text['log_id']+')" ><img src="images/icons/up.gif"></a> <a href="javascript:void(0)" onclick="down('+text['log_id']+')" ><img src="images/icons/down.gif"></a></div>';
  $str += '           <div class="edit_action"><input type="submit" value="メモ保存" /></div>';
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
  $('#log_'+id+' .alarm').html('<input class="alarm_input" type="text" name="alarm" value="'+$('#log_'+id+' .alarm').html()+'">');
  $('#log_'+id+' .alarm input').datePicker();
  l = $('#log_'+id+' .level').html();
  $('#log_'+id+' .level').show().html('<input type="radio" name="level" value="0" '+(l == '0' ? 'checked' : '')+' />重1<input type="radio" name="level" value="1" '+(l == '1' ? 'checked' : '')+' />重2<input type="radio" name="level" value="2" '+(l == '2' ? 'checked' : '')+' />重3');
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
  if (confirm("このメモを削除しますか？")) {
    url = 'micro_log.php?id='+id+'&action=delete';
    $.ajax({
      url: url,
      dataType: 'text',
      success: function(text) {
        $('#log_' + id).hide();
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
          <div id="div_more"><button onClick="more_log()">さらに表示</button></div>
              -->
        </td>
      </tr>
    </table>
  </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
