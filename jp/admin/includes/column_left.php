<?php
/*
  $Id$
*/
/*
if ((strpos($_SERVER['PHP_SELF'], 'history.php') === false)  && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_oroshi.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'list_display.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_orders.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_new_orders.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'categories.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'micro_log.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'products_tags.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'categories_admin.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'pw_manager_log.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_dougyousya.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'customers_products.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'pw_manager.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_new_orders2.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'telecom_unknow.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_list.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'orders.php') === false)) {
?>
<script type="text/javascript" src="includes/javascript/jquery.js"></script>
<?php }
*/?>
<script type="text/javascript" >
if (typeof window.jQuery == "undefined") {
    document.write('<script language="javascript" src="includes/javascript/jquery.js"><\/script>');
}
</script>
<?php
if ($ocertify->npermission >= 10) {?>
<script>
$(document).ready(function(){
  <?php
  if ($_COOKIE['tarrow'] == 'open') {
  ?>
  $('.columnLeft').parent().after('<td class="show_left_menu" valign="top" style="padding-top:5px; *padding-top:48px; padding-right:5px; *padding-left:0;"><a href="javascript:void(0);" class="leftright" onclick="toggle_leftColumn();"><img src="includes/languages/japanese/images/boult_back.gif" alt="img"></a></td>');
  $('.columnLeft').css('display', 'block'); 
  <?php
  } else {
  ?>
  $('.columnLeft').parent().after('<td class="show_left_menu" valign="top" style="padding-top:5px; *padding-top:48px; padding-right:5px; *padding-left:0;"><a href="javascript:void(0);" class="leftright" onclick="toggle_leftColumn();"><img src="includes/languages/japanese/images/boult.gif" alt="img"></a></td>');
  <?php
  }
  ?>
  $('.columnLeft').parent().parent().parent().parent().attr('cellspacing', '0');
  $('.columnLeft').parent().parent().parent().parent().attr('cellpadding', '0');
  $('.columnLeft').parent().addClass('leftmenu');
});
function toggle_leftColumn()
{
    
  var arrow_status = $('.columnLeft').css('display');
<?php 
if($belong == "" || $user_info == ""){
$belong = str_replace("/admin/","",$_SERVER['REQUEST_URI']);
$user_info['name'] = tep_get_user_info($ocertify->auth_user);
}
?>
  if (arrow_status == 'none') {
    document.cookie = 'tarrow=open'; 
    $.ajax({
url: 'ajax_memo.php',
data: 'tarrow=open&belong=<?php echo $belong;?>&author=<?php echo $user_info['name'];?>',		    
type: 'POST',
dataType: 'text',
async: 'false',
success :function(data){
<?php 
$query = tep_db_query("select * from notes where belong='".$belong."' and (attribute='1' or (attribute='0' and author='".$user_info['name']."'))  order by id desc");

while($array = tep_db_fetch_array($query)){
?>
var left = $("#note_<?php echo $array['id']?>").css("left"); 
left = left.replace("px","");
left = parseInt(left);
    $("#note_<?php echo $array['id']?>").css("left",(left+160)+"px");

<?php
}

?>

}
    });
  } else {
    document.cookie = 'tarrow=close'; 
    $.ajax({
url: 'ajax_memo.php',
data: 'tarrow=close&belong=<?php echo $belong;?>&author=<?php echo $user_info['name'];?>',		    
type: 'POST',
dataType: 'text',
async: 'false',
success :function(data){
<?php 
$query = tep_db_query("select * from notes where belong='".$belong."' and (attribute='1' or (attribute='0' and author='".$user_info['name']."'))  order by id desc");

while($array = tep_db_fetch_array($query)){
?>
var left = $("#note_<?php echo $array['id']?>").css("left"); 
left = left.replace("px","");
left = parseInt(left);
    $("#note_<?php echo $array['id']?>").css("left",(left-160)+"px");

<?php
}
?>

}
    });
  }
  
  $('.columnLeft').toggle();
  if ($('.leftright').children().attr('src').indexOf('boult.gif') == -1) {
     $('.leftright').children().attr('src', 'includes/languages/japanese/images/boult.gif')
  } else {
     $('.leftright').children().attr('src', 'includes/languages/japanese/images/boult_back.gif')
  }
  var menu_div_width = $('#categories_right_td').width();
  if(menu_div_width>=480){
    $('#categories_tree').animate({width:(menu_div_width-5)+"px"});
  }
  <?php
  if ($_SERVER['PHP_SELF'] == '/admin/orders.php' || $_SERVER['PHP_SELF'] == '/admin/preorders.php') {
    if (!isset($_GET['action'])) {
    ?> 
    showRightInfo(); 
    <?php 
    }
  }
  ?>
}
function toggle_lan(sobj)
{
  var current_status = $('#'+sobj).css('display');
  if (current_status == 'none') {
    var action_str = 'insert'; 
  } else {
    var action_str = 'del'; 
  }
  $('#'+sobj).slideToggle(); 
  $.ajax({
    url: "<?php echo tep_href_link('toggle_left_menu.php');?>",
    data:"id="+sobj+"&action="+action_str, 
    type: "POST",
    success:function(msg) {
    }
  }); 
}
</script>
<?php }?>
<?php
$l_select_box_arr = array();
if (isset($_SESSION['l_select_box'])) {
  $l_select_box_arr = explode(',', $_SESSION['l_select_box']);
}
if ($ocertify->npermission >= 10) {
  require(DIR_WS_BOXES . 'configuration.php');
  require(DIR_WS_BOXES . 'catalog.php');
  require(DIR_WS_BOXES . 'modules.php');
  require(DIR_WS_BOXES . 'customers.php');
  // require(DIR_WS_BOXES . 'taxes.php');
  require(DIR_WS_BOXES . 'localization.php');
  require(DIR_WS_BOXES . 'reports.php');
  require(DIR_WS_BOXES . 'tools.php');
  require(DIR_WS_BOXES . 'users.php');
}
?>
