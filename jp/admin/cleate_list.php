<?php
ob_start();
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
$cPath=cpathPart($_GET['cpath']);
$oid = $_GET['o_id'];
$action = $HTTP_GET_VARS['action'];
switch ($HTTP_GET_VARS['action']){
/* -----------------------------------------------------
   case 'data_cleate' 新建批发商的数据   
------------------------------------------------------*/
case 'data_cleate':
  $cPath=cpathPart($_POST['cpath']);
  $setdata=$_POST['set_list'];
  $date=date("Y-m-d H:i:s");
  $cid = $_POST['cid'];
  $o_id = $_POST['oid'];
  foreach ($setdata as $key=>$value){
    if(trim($value)){
    $oroid = $key;
    $sql = 'insert into set_oroshi_datas (oroshi_id ,parent_id,datas,set_date) values(';
    $sql.= '"'.$key.'",';
    $sql.= '"'.$cid.'",';
    $sql.= '"'.$value.'",';
    $sql.= 'now()';
    $sql.= ')';
    tep_db_query($sql);
    }
  }
  if(isset($_GET['src_id'])&&$_GET['src_id']!=null){
    $jump_url = 'cleate_list.php?action=prelist&cid='. $cid  .'&oid='.$o_id.'&src_id=his&cPath='.$_POST['cPath'];
  }else{
    $jump_url = 'cleate_list.php?action=prelist&cid=' . $cid .'&oid='.$o_id.'&cPath='.$_POST['cPath'];
  }
  tep_redirect($jump_url);
    break;
}
/*
  危险　24小时　价格没有更新
  警告　未满4小时　未满7小时　24小时　价格没有更新
*/    //DB里保存的最大值是20
    
//有必要记录到DB里
/*
  批发商名、$cPath、时间、数据
*/
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
<title>
<?php 
if(isset($_GET['action']) && $_GET['action']!=""){
echo CLEATE_LIST_TITLE;

}
?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script type="text/javascript">
function one_time_pwd(page_name){
  $.ajax({
url: 'ajax_orders.php?action=getpwdcheckbox',
type: 'POST',
data: 'page_name='+page_name,
dataType: 'text',
async : false,
success: function(data) {
if(data !='false'){
var pwd_arr = data.split(",");
if(data.indexOf('[SQL-ERROR]')==-1){
pwd =  window.prompt("<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>","");
var tmp_pos_single = false;
if (data.indexOf(pwd+',') > -1 || data.indexOf(','+pwd) > -1) {
  tmp_pos_single = true;
}
if (tmp_pos_single == false) {
  if (data.indexOf(pwd) > -1) {
    tmp_pos_single = true;
  }
}
if (data.indexOf(pwd+',') > -1 || data.indexOf(pwd) > -1 || data.indexOf(','+pwd) > -1) {
$.ajax({
url: 'ajax_orders.php?action=save_pwd_log',
type: 'POST',
data: 'one_time_pwd='+pwd+'&page_name='+page_name,
dataType: 'text',
async : false,
success: function(_data) {
}
});
}else{
  alert("<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>");
  location=location;
}
}else{
  location.href='/admin/sql_error.php';
}
}
}
});
}
</script>
<?php 
  if($oid){
    ?>
<script language="javascript" >
    $(document).ready(function(){
        $("#textarea_<?php echo $oid;?>").focus()
          })
    </script>
<?php } ?>
<script language="javascript" >
function goto(){
  var link = document.getElementById('back_link').href;
  location.href=link;
}
<?php //提交动作?>
function toggle_cleat_list_form(c_permission)
{
  if (c_permission == 31) {
    document.forms.ce_form.submit(); 
  } else {
    $.ajax({
      url: 'ajax_orders.php?action=getallpwd',   
      type: 'POST',
      dataType: 'text',
      data: 'current_page_name=<?php echo $_SERVER['PHP_SELF']?>', 
      async: false,
      success: function(msg) {
        var tmp_msg_arr = msg.split('|||'); 
        var pwd_list_array = tmp_msg_arr[1].split(',');
        if (tmp_msg_arr[0] == '0') {
          document.forms.ce_form.submit(); 
        } else {
          var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
          var tmp_pos_single = false;
          if (tmp_msg_arr[1].indexOf(input_pwd_str+',') > -1 || tmp_msg_arr[1].indexOf(','+input_pwd_str) > -1) {
            tmp_pos_single = true;
          }
          if (tmp_pos_single == false) {
            if (tmp_msg_arr[1].indexOf(input_pwd_str) > -1) {
              tmp_pos_single = true;
            }
          }
          if (tmp_pos_single == true) {
            $.ajax({
              url: 'ajax_orders.php?action=record_pwd_log',   
              type: 'POST',
              dataType: 'text',
              data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.ce_form.action),
              async: false,
              success: function(msg_info) {
                document.forms.ce_form.submit(); 
              }
            }); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      }
    });
  }
}
</script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
$belong = str_replace('&','|||',$belong);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
<?php 
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
<script language='javascript'>
  one_time_pwd('<?php echo $page_name;?>');
</script>
<?php }?>
<div id="spiffycalendar" class="text"></div>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
   <tr>
      <td width="<?php echo BOX_WIDTH; ?>" valign="top">
         <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
            <tr>
               <td><?php require(DIR_WS_INCLUDES . 'column_left.php'); ?></td>
            </tr>   
         </table>
      </td>
      <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
         <div class="compatible">
         <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
               <td class = "pageHeading" height="40"><?php echo CLEATE_LIST_TITLE;?>
               <input type="button" onClick = "goto()" value='<?php echo IMAGE_BACK;?>'>
  <?php if ($ocertify->npermission >7){?>
               <input type="button" onClick="location.href='cleate_oroshi.php'" value="<?php echo CLEATE_LIST_SETNAME_BUTTON;?>">
  <?php }?>
               </td>
            </tr>
            <tr>
               <td>
                  <table width="100%" class="list" border="0" cellspacing="0" cellpadding="4" bgcolor="#F0F1F1">
                     <tr bordercolor="#F0F1F1">
                        <td></td>
                     </tr>   
            <?php 
  //根据 oid 取出当前 oid 关系了哪些个分类
  if ($action == 'oroshi'){
    $back_url = 'cleate_oroshi.php';
  $getMyCate = 'select cd.categories_name,soc.categories_id  from
    set_oroshi_categories soc ,categories_description cd,categories c where cd.site_id =0 and
    soc.categories_id = cd.categories_id and cd.categories_id = c.categories_id and soc.oroshi_id = "'.$oid.'" order by c.sort_order asc,cd.categories_name asc';
$res = tep_db_query($getMyCate);
while ($col = tep_db_fetch_array($res)){
  $cate_id = $col['categories_id'];
  $cate_name = $col['categories_name'];
  
  $colmunLimit = 2;//分几行
  $colmunLimit_add_1 = $colmunLimit+1;

  echo "<tr bgcolor='#F0F1F1' onmouseover='this.style.backgroundColor=\"#FFD700\"' onmouseout='this.style.backgroundColor=\"#F0F1F1\"'>";
  echo "<td><a href=
    'cleate_list.php?action=prelist&cid=".$cate_id."&oid=".$_GET['o_id']."' >".$cate_name.'</a></td>';
  echo "</tr>";
}

  }
if ($action =='prelist'){
  $cid = $_GET['cid'];
  $oid = $_GET['oid'];
  $back_url = 'cleate_list.php';
  $back_url_params = 'action=oroshi&o_id='.$oid;
  $form_action = 'cleate_list.php?action=data_cleate';

  if (isset($_GET['src_id'])&&$_GET['src_id']!=null){
    $src_id=$_GET['src_id'];
    $back_url_params = 'action=oroshi&cid='.$cid.'&o_id='.$oid.'&src_id='.$src_id;
    $back_url = 'history.php';
    $form_action = 'cleate_list.php?action=data_cleate&src_id='.$src_id;
  }else if (isset($_GET['cPath']) && '' != $_GET['cPath']) {
    
    $back_url = "categories.php";
    $back_url_params = "cPath=".$_GET['cPath'];
  }
  $res =tep_db_query('select * from set_oroshi_names son, categories c ,set_oroshi_categories soc where c.categories_id = "'.$cid.'" and c.categories_id = soc.categories_id and son.oroshi_id = soc.oroshi_id order by son.sort_order,soc.oroshi_id ');
      $html2 = '';
      $c=0;
    while($col = tep_db_fetch_array($res)){
      $c++;
      $oroname = $col['oroshi_name'];
      $oroid = $col['oroshi_id'];

      $html.= "<td><a href='history.php?action=oroshi_c&cPath=".$_GET['cid']."&oid=".$col['oroshi_id']."&fullpath=".$_GET['cPath']."' title='".TEXT_CLEATE_HISTORY."'>".$col['oroshi_name']."</a>&nbsp;&nbsp;&nbsp;<a href='history.php?action=oroshi_c&cPath=".$_GET['cid']."&oid=".$col['oroshi_id']."&fullpath=".$_GET['cPath']."' title='".TEXT_CLEATE_HISTORY."'>".TEXT_CLEATE_HISTORY."</a></td>";
      $html2.= '';
      $html2.="<td><textarea rows='5' cols='30' id='textarea_".$col['oroshi_id']."' name='set_list[".$oroid."]' ></textarea></td>";

}
echo $html;
?>
          <form method="post" action="<?php echo $form_action;?>" name="ce_form">
                     <tr bgcolor='#F0F1F1'>
            <?php
  echo $html2;
?>
                        <td>
                        <input type="hidden" value="<?php echo $cid;?>" name='cid' />
                        <input type="hidden" value="<?php echo $oid;?>" name='oid' />
                        <input type="hidden" value="<?php echo $_GET['cPath'];?>" name='cPath' />
                        </td>
                        <td></td>
                     </tr>
        <tr>
          <td colspan="<?php echo count($c)+2;?>"><a href="javascript:void(0);"><?php echo tep_html_element_button(TEXT_CLEATE_LIST, 'onclick="toggle_cleat_list_form(\''.$ocertify->npermission.'\')"');?></a></td>
        </tr>
          </form>
          <?php
    $lines_arr = array();
$oroname = array();
$cr = array("\r\n", "\r");   // 用于换行代码替换
$orocnt = tep_db_query('select distinct(soc.oroshi_id) 
    from set_oroshi_categories  soc,
    set_oroshi_names son
    where soc.categories_id = "'.$cid.'" 
    and soc.oroshi_id = son.oroshi_id
    order by son.sort_order,soc.oroshi_id');
while($testcol = tep_db_fetch_array($orocnt)){
  $oroids[] = $testcol['oroshi_id'];
}
if($oroids){
foreach($oroids as $key=>$value){
  $res = tep_db_query("select * from set_oroshi_names son, set_oroshi_datas sod where sod.oroshi_id ='". $value."' and sod.oroshi_id = son.oroshi_id and  parent_id='".$cid."' ORDER BY sod.list_id desc limit 1");
  $col = tep_db_fetch_array($res);
  $cols[]=$col;
}

foreach($cols as $col){
  if($col['set_date']){
    $oroname[] = $col['oroshi_name'];
    $orotime[] = date('Y/m/d H:i:s', strtotime($col['set_date']));
    $lines = spliteOroData($col['datas']);
    $count[]=count($lines);
    $lines_arr[]=$lines;
  }
} 
                                
  $cnt = count($count);

for($n=0;$n<$cnt;$n++){
  if($count[0]<=$count[$n]){
    $count[0]=$count[$n];
  }
}

if($orotime){
echo "<tr>";  
  foreach ($orotime as $value){
    echo "<td>$value</td>";
  }
echo "</tr>";
}
if($oroname){
echo "<tr>";  
  foreach ($oroname as $value){
    echo "<td align='left'>$value</td>";
  }

echo "</tr>";
}

}
if($lines_arr){
for($i=0;$i < $count[0];$i++){
  echo "<tr id=color>";
  for($j=0;$j<$cnt;$j++){
    echo "<td>".$lines_arr[$j][$i]."</td>";
  }
  echo "</tr>";
}
}
} 
?>
        </table>
               </td>
            </tr>
            <tr>
               <td><a id="back_link" style="display:none" href="<?php echo tep_href_link($back_url, $back_url_params); ?>">go back</a></td>
            </tr>
         </table>
         </div>
         </div>
      </td>
  </tr>
  
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
