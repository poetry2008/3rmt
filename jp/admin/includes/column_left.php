<?php
/*
  $Id$
*/
?>
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

  if (arrow_status == 'none') {
    document.cookie = 'tarrow=open';  
  } else {
    document.cookie = 'tarrow=close';  
  }
  
  $('.columnLeft').toggle();
  if ($('.leftright').children().attr('src').indexOf('boult.gif') == -1) {
     $('.leftright').children().attr('src', 'includes/languages/japanese/images/boult.gif')
  } else {
     $('.leftright').children().attr('src', 'includes/languages/japanese/images/boult_back.gif')
  }
  var menu_div_width = $('#categories_right_td').width();
  if(menu_div_width>=480){
    //$('#categories_tree').animate({width:(menu_div_width-5)+"px"});
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
  var ele_obj = '';
  if("undefined" != typeof temp_id){
    if(temp_id != ''){
      var box_warp = '';
      var box_warp_top = 0;
      var box_warp_left = 0;
      if($(".box_warp").offset()){
        box_warp = $(".box_warp").offset();
        box_warp_top = box_warp.top;
        box_warp_left = box_warp.left;
      }
      ele_obj = $("#popup_window_value_"+temp_id).offset();
      var font_size = $("#popup_window_value_"+temp_id).css("font-size");
      font_size = font_size.replace("px","");
      font_size = parseInt(font_size);
      if(navigator.userAgent.indexOf("MSIE")>0){
        font_size = 0;
      }
      $("#popup_window").css('top',ele_obj.top-box_warp_top+font_size);
      $("#popup_window").css('left',ele_obj.left-box_warp_left);
    }
  }
<?php if($_SERVER['PHP_SELF'] == '/admin/campaign.php'){ ?>
setTimeout("show_campaign_info_offset()",10); 
<?php } ?>
<?php if($_SERVER['PHP_SELF'] == '/admin/orders.php'){ ?>
setTimeout("orders_info_box_offset()",10);
<?php } ?>
  if($('#show_info_id').length > 0){
    show_info_str = $('#show_info_id').val();
    leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
    $('#'+show_info_str).css('left',leftset);
  }
  if($(".box_warp").height() < $(".compatible").height()){
      $(".box_warp").height($(".compatible").height());
  }
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
