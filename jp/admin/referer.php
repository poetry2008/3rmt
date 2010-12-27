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
            <td class="pageHeading">アクセスランキング</td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
          <div class="list_type">
            <span<?php if($_GET['type'] != 'adsense'){?> class="site_filter_selected"<?php }?>><a href="<?php echo tep_href_link('referer.php','type=referer'); ?>">Referer</a></span>
            <span<?php if($_GET['type'] == 'adsense'){?> class="site_filter_selected"<?php }?>><a href="<?php echo tep_href_link('referer.php','type=adsense'); ?>">Adsense</a></span>
          </div>
        </td>
      </tr>
      <tr>
        <td>
        <div align="center">

    <form action="<?php echo tep_href_link('referer.php','site_id='.$_GET['site_id'].'&type='.$_GET['type']) ; ?>" method="get">
      <input type="hidden" name="" value="">
      <input type="hidden" name="" vlaue="">
    <fieldset>
    <table  border="0" align="center" cellpadding="0" cellspacing="2">
    <tr>
      <td class="smallText">
      開始日:
      <select name="sy">
      <?php 
      for($i=2007; $i<2011; $i++) { 
        if ((isset($_GET['sy']) && $i == $_GET['sy']) or (!isset($_GET['sy']) && $i == date('Y'))) {
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; 
        } else {
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        }
      } ?>
      </select>
      年
      <select name="sm">
      <?php for($i=1; $i<13; $i++) { if((isset($_GET['sm']) && $i == $_GET['sm']) or (!isset($_GET['sm']) && $i == date('m')-1)){ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }else{ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }  } ?>    
      </select>
      月
      <select name="sd">
      <?php
      for($i=1; $i<32; $i++) {
        if((isset($_GET['sd']) && $i == $_GET['sd']) or (!isset($_GET['sd']) && $i == date('d'))){
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
      <select name="ey">
      <?php
      for($i=2002; $i<2011; $i++) {
        if((isset($_GET['ey']) && $i == $_GET['ey']) or (!isset($_GET['ey']) && $i == date('Y'))){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      年
      <select name="em">
      <?php
      for($i=1; $i<13; $i++) {
        if((isset($_GET['em']) && $i == $_GET['em']) or (!isset($_GET['em']) && $i == date('m'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      月
      <select name="ed">
      <?php
      for($i=1; $i<32; $i++) {
        if((isset($_GET['ed']) && $i == $_GET['ed']) or (!isset($_GET['ed']) && $i == date('d'))){
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
        <?php tep_site_filter('referer.php');?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent">アクセス来たソース</td>
                <td class="dataTableHeadingContent">件数</td>
                <td class="dataTableHeadingContent">順位</td>
              </tr>
<?php
  if ($_GET['type'] == 'adsense') {
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
  } else {
  $ref_site_query = tep_db_query("
    select * from (
      select count(orders_id) as cnt , concat( ifnull( orders_ref_site, '' ) , if( orders_adurl is null, '', '(Adsense)' ) ) AS orders_ref_site2
      from " . TABLE_ORDERS . " o, ".TABLE_SITES." s
      where s.id = o.site_id
        and orders_ref_site IS NOT NULL
        " . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and s.id = '" . intval($_GET['site_id']) . "' " : '') . 
        (isset($_GET['sy']) && isset($_GET['sm']) && isset($_GET['sd']) ? " and o.date_purchased > '".$_GET['sy'].'-'.$_GET['sm'].'-'.$_GET['sd'] ."'" : " and o.date_purchased > '".date('Y-m-d H:i:s', time()-(86400*30)) . "' ") . 
        (isset($_GET['ey']) && isset($_GET['em']) && isset($_GET['ed']) ? " and o.date_purchased < '".$_GET['ey'].'-'.$_GET['em'].'-'.$_GET['ed'] ." 23:59:59'" : '') . "
      group by orders_ref_site2
    ) s
    order by cnt desc
      ");
    $i = 1;
    while ($ref_site = tep_db_fetch_array($ref_site_query)) {
      $ad_cnt = tep_db_fetch_array(tep_db_query("select count(orders_id) as cnt from orders where orders_adurl='".$ref_site['orders_ref_site']."'"));
  ?>
                <tr class="dataTableRow">
                  <td class="dataTableContent"><?php echo $ref_site['orders_ref_site2'];?></td>
                  <td class="dataTableContent"><?php echo $ref_site['cnt'];?><?php //echo $ad_cnt['cnt']?'('.$ad_cnt['cnt'].')':'';?></td>
                  <td class="dataTableContent"><?php echo $i;?></td>
                </tr>
  <?php
      $i++;
    }
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
