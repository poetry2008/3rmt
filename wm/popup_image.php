<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  $navigation->remove_current_page();

//ccdd
  $products_query = tep_db_query("
      select pd.products_name, 
             p.products_image,
             p.products_image2,
             p.products_image3 
      from " . TABLE_PRODUCTS .  " p 
        left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id 
      where p.products_status = '1' 
        and p.products_id = '" .  (int)$_GET['pIID'] . "' 
        and pd.language_id = '" . $languages_id . "' 
        and (pd.site_id = '".SITE_ID."' or pd.site = '0')
      order by site_id DESC
  ");
  $products_values = tep_db_fetch_array($products_query);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo $products_values['products_name']; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<script language="javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+60-i);
  self.focus();
}
//--></script>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<style type="text/css">
BODY {
 margin:0;
 }
TD.pageHeading, DIV.pageHeading, H1.pageHeading {
  font-family: Tahoma , Osaka, Verdana, Arial, sans-serif;
  font-size: 20px;
  font-weight: bold;
  color: #9a9a9a;
  margin-top: 20px;
  margin-left: 20px;
  margin-bottom: 10px;
}
TD.main, P.main {  font-family: Tahoma , Osaka, Verdana, Arial, sans-serif;
font-size: 11px;
  line-height: 1.5;
}
A { 
  color: #029DBB;
  text-decoration: none;
}
.image_border {
  padding: 2px;
  border: 1px solid #E8E8E8;
}

</style>
</head>

<body Oncontextmenu="alert('Copy Right <?php echo STORE_NAME ; ?>'); return false;" leftmargin="0" topmargin="0" onload="window.resizeTo(document.featImage.width+150,document.featImage.height+265);">
<h1 class="pageHeading"><?php echo $products_values['products_name'] ; ?></h1>
      <table border="0" cellspacing="6" cellpadding="0">
<tr>
        <?php echo (tep_not_null($products_values['products_image'])) ?  '<td align="center" class="image_border"><a href="popup_image.php?image='.$products_values['products_image'] .'&pIID='.$_GET['pIID'].' ">'. tep_image2(DIR_WS_IMAGES . $products_values['products_image'], $products_values['products_name'], 60, 60, 'name="prod_thum_1"').'</a></td>' : '' ; ?>
        <?php echo (tep_not_null($products_values['products_image2'])) ?  '<td align="center" class="image_border"><a href="popup_image.php?image='.$products_values['products_image2'] .'&pIID='.$_GET['pIID'].' ">'. tep_image2(DIR_WS_IMAGES . $products_values['products_image2'], $products_values['products_name'], 60, 60, 'name="prod_thum_1"').'</a></td>' : '' ; ?>
        <?php echo (tep_not_null($products_values['products_image3'])) ?  '<td align="center" class="image_border"><a href="popup_image.php?image='.$products_values['products_image3'] .'&pIID='.$_GET['pIID'].' ">'. tep_image2(DIR_WS_IMAGES . $products_values['products_image3'], $products_values['products_name'], 60, 60, 'name="prod_thum_1"').'</a></td>' : '' ; ?>

</tr>
</table>


<div align="center">
<?php 
   if($_GET['image'] && $_GET['image'] != '') {
     echo tep_image(DIR_WS_IMAGES . $_GET['image'], $products_values['products_name']);
  }else{   
    echo tep_image(DIR_WS_IMAGES . $products_values['products_image'], $products_values['products_name'],'','','name="featImage"');
  }
  
  ?></div>
<p class="main" align="right"><?php echo '<a href="javascript:window.close()">' . TEXT_CLOSE_WINDOW . '</a>'; ?></p>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
