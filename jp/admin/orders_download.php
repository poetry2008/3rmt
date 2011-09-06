<?php
/*
   $Id$
*/
  //ob_start();
  require('includes/application_top.php');
  $all_orders_statuses =  array();
  $all_preorders_statuses =  array();
  $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");

  while ($orders_status = tep_db_fetch_array($orders_status_query)) {
    $all_orders_statuses[] = array('id' => $orders_status['orders_status_id'], 'text' => $orders_status['orders_status_name']);
  }
  $preorders_status_query = tep_db_query("select orders_status_id, orders_status_name
      from " . TABLE_PREORDERS_STATUS . " where language_id = '" .
      $languages_id . "'");
  while ($preorders_status = tep_db_fetch_array($preorders_status_query)) {
    $all_preorders_statuses[] = array('id' => $preorders_status['orders_status_id'],
        'text' => $preorders_status['orders_status_name']);
  }


  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<?php 
  // 订单详细页，TITLE显示交易商品名
  if ($_GET['action']=='edit' && $_GET['oID']) {?>
<title><?php echo tep_get_orders_products_names($_GET['oID']); ?></title>
<?php } else { ?>
<title><?php echo TITLE; ?></title>
<?php }?>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript">
function change_action(url){
  document.getElementById('orders_download').action=url;
  document.getElementById('orders_download').submit();
}
</script>
</head>
<body>
<?php
if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<?php
  if ($ocertify->npermission >= 10) {
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation //-->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof //-->';
    echo '</table>';
    echo '</td>';
  } else {
    echo '<td>&nbsp;</td>';
  }
?>

<td width="100%" valign="top"><table border="0" width="100%" cellspacing="0"
cellpadding="2">
<?php
  if ($ocertify->npermission == 15) {
?>
  </tr>
      <td class="pageHeading" height="40">
    <!--ORDER EXPORT SCRIPT //-->
    <form id="orders_download" action="<?php echo tep_href_link('orders_csv_exe.php','csv_exe=true', 'SSL') ; ?>" method="post">
    <!--
    <fieldset><legend class="smallText">-->
    <?php echo TEXT_ORDER_DOWNLOPAD;?><!--</legend>-->
   
    <!--ORDER EXPORT SCRIPT EOF //-->
    </td>
<?php
  }
?>
    </tr>
    <tr>
    <td>
     <!--<span class="smallText">-->
    <?php echo TEXT_ORDER_SERVER_BUSY;?><!--</span>-->
<!--</fieldset>
    </form>-->
    <br>
    <br>
    </td>
    </tr>
    <tr>
    <td>
        <table  border="0" align="left" cellpadding="0" cellspacing="2" width="100%">
    <tr>
      <td class="smallText" height="25" colspan="3">
      <?php echo TEXT_ORDER_SITE_TEXT;?>:
      <?php echo tep_site_pull_down_menu_with_all(isset($_GET['site_id']) ? $_GET['site_id'] :'', false);?>
      </td>
    </tr>
    <tr>
      <td class="smallText" height="25" width="210">
      <?php echo TEXT_ORDER_START_DATE;?>
      <select name="s_y">
      <?php for($i=2002; $i<=date('Y'); $i++) { if($i == date('Y')){ echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; }else{ echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;} } ?>
      </select>
      <?php echo TEXT_ORDER_YEAR;?>
      <select name="s_m">
      <?php for($i=1; $i<13; $i++) { if($i == date('m')-1){ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }else{ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }  } ?>    
      </select>
      <?php echo TEXT_ORDER_MONTH;?>
      <select name="s_d">
      <?php
      for($i=1; $i<32; $i++) {
        if($i == date('d')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo TEXT_ORDER_DAY;?></td>
      <td width="80" align="center">～</td>
      <td class="smallText"><?php echo TEXT_ORDER_END_DATE;?>
      <select name="e_y">
      <?php
      for($i=2002; $i<=date('Y'); $i++) {
        if($i == date('Y')){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      <?php echo TEXT_ORDER_YEAR;?>
      <select name="e_m">
      <?php
      for($i=1; $i<13; $i++) {
        if($i == date('m')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo TEXT_ORDER_MONTH;?>
      <select name="e_d">
      <?php
      for($i=1; $i<32; $i++) {
        if($i == date('d')){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo TEXT_ORDER_DAY;?></td></tr>
      <tr>
       <td class="smallText" height="30"><?php echo HEADING_TITLE_ORDER_STATUS . ' ' .
       tep_draw_pull_down_menu('order_status', tep_array_merge(array(array('id' => '', 'text' => TEXT_ALL_ORDERS)), $all_orders_statuses), '', ''); ?></td>
       <td align="center">|</td>
       <td class="smallText" height="30"><?php echo HEADING_TITLE_PREORDER_STATUS . ' ' .
       tep_draw_pull_down_menu('preorder_status', tep_array_merge(array(array('id' => '',
                 'text' => TEXT_ALL_PREORDERS)), $all_preorders_statuses), '', ''); ?></td>
      </tr>
      <tr>
    <td height="30"><?php 
    echo tep_html_element_submit(TEXT_ORDER_CSV_OUTPUT,"onclick='change_action(\"".tep_href_link('orders_csv_exe.php','csv_exe=true', 'SSL')."\")'");
    ?>
    </td>
    <td align="center">|</td>
    <td height="30">
    <?php
      echo
    tep_html_element_submit(TEXT_PREORDER_CSV_OUTPUT,"onclick='change_action(\"".tep_href_link('preorders_csv_exe.php','csv_exe=true', 'SSL')."\")'");
    ?></td>
      </tr>
    </table>
    </td>
    </tr>

    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<embed id="warn_sound" src="images/warn.mp3" width="0" height="0" loop="false" autostart="false"></embed>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); 
   //ob_end_flush();
?>
