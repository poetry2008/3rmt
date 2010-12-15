<?php
/*
  $Id$
*/

  $xx_mins_ago = (time() - 900);

  require('includes/application_top.php');

  //require(DIR_WS_CLASSES . 'currencies.php');
  //$currencies = new currencies();

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
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
            <td class="pageHeading">Adsense Logs</td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
        <div align="center">



    <form action="<?php echo tep_href_link('adsense.php','site_id='.$_GET['site_id']) ; ?>" method="get">
    <fieldset><!--<legend class="smallText"><b>xxxxx</b></legend>-->
    <table  border="0" align="center" cellpadding="0" cellspacing="2">
    <tr>
      <td class="smallText">
      開始日:
      <select name="s_y">
      <?php 
      for($i=2007; $i<2011; $i++) { 
        if ((isset($_GET['s_y']) && $i == $_GET['s_y']) or (!isset($_GET['s_y']) && $i == date('Y'))) {
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; 
        } else {
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        }
      } ?>
      </select>
      年
      <select name="s_m">
      <?php for($i=1; $i<13; $i++) { if((isset($_GET['s_m']) && $i == $_GET['s_m']) or (!isset($_GET['s_m']) && $i == date('m')-1)){ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }else{ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }  } ?>    
      </select>
      月
      <select name="s_d">
      <?php
      for($i=1; $i<32; $i++) {
        if((isset($_GET['s_d']) && $i == $_GET['s_d']) or (!isset($_GET['s_d']) && $i == date('d'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      日 </td>
      <td width="80" align="center">～</td>
      <td class="smallText">終了日
      <select name="e_y">
      <?php
      for($i=2002; $i<2011; $i++) {
        if((isset($_GET['e_y']) && $i == $_GET['e_y']) or (!isset($_GET['e_y']) && $i == date('Y'))){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      年
      <select name="e_m">
      <?php
      for($i=1; $i<13; $i++) {
        if((isset($_GET['e_m']) && $i == $_GET['e_m']) or (!isset($_GET['e_m']) && $i == date('m'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      月
      <select name="e_d">
      <?php
      for($i=1; $i<32; $i++) {
        if((isset($_GET['e_d']) && $i == $_GET['e_d']) or (!isset($_GET['e_d']) && $i == date('d'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      日 </td>
        <td>&nbsp;</td>
        <td><input type="submit" value="検索"></td>
      </tr>
    </table></fieldset>
    </form>


        </div>
        <?php tep_site_filter('adsense.php');?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">キーワード</td>
                <td class="dataTableHeadingContent">件数</td>
                <td class="dataTableHeadingContent">順位</td>
              </tr>
<?php
  $ref_site_query = tep_db_query("
    select * from (
      select count(orders_id) as cnt,orders_adurl
      from " . TABLE_ORDERS . " o, ".TABLE_SITES." s
      where s.id = o.site_id
        and orders_adurl IS NOT NULL
        and orders_adurl != ''
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" . intval($_GET['site_id']) . "' " : '') . 
        (isset($_GET['s_y']) && isset($_GET['s_m']) && isset($_GET['s_d']) ? " and o.date_purchased > '".$_GET['s_y'].'-'.$_GET['s_m'].'-'.$_GET['s_d'] ."'" : " and o.date_purchased > '".date('Y-m-d H:i:s', time()-(86400*30)) . "' ") . 
        (isset($_GET['e_y']) && isset($_GET['e_m']) && isset($_GET['e_d']) ? " and o.date_purchased < '".$_GET['e_y'].'-'.$_GET['e_m'].'-'.$_GET['e_d'] ." 23:59:59'" : '') . "
      group by orders_adurl
    ) s
    order by cnt desc
      ");
  $i = 1;
  while ($ref_site = tep_db_fetch_array($ref_site_query)) {
?>
              <tr class="dataTableRow">
                <td class="dataTableContent"><?php echo $ref_site['orders_adurl'];?></td>
                <td class="dataTableContent"><?php echo $ref_site['cnt'];?></td>
                <td class="dataTableContent"><?php echo $i;?></td>
              </tr>
<?php
    $i++;
  }
?>
              <tr>
                <td class="smallText" colspan="7"><?php //echo sprintf(TEXT_NUMBER_OF_CUSTOMERS, tep_db_num_rows($whos_online_query)); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
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
