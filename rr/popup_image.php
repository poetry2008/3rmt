<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  $navigation->remove_current_page();
 
  $products_query = tep_db_query("
      select * from (select pd.products_name, 
             pd.products_image,
             pd.products_image2,
             pd.products_id,
             pd.site_id, 
             pd.products_status,
             pd.products_image3 
      from " . TABLE_PRODUCTS .  " p 
        left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id 
      where p.products_id = '" .  (int)$_GET['pIID'] . "' 
        and pd.language_id = '" . $languages_id . "' 
      order by site_id DESC
   ) c where site_id = '".SITE_ID."' or site_id = '0' group by products_id having c.products_status != '0' and c.products_status != '3'");
  $products_values = tep_db_fetch_array($products_query);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<title><?php echo $products_values['products_name']; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<script type="text/javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+60-i);
  self.focus();
}
--></script>
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

<body oncontextmenu="alert('Copy Right <?php echo STORE_NAME ; ?>'); return false;" leftmargin="0" topmargin="0" onLoad="window.resizeTo(document.featImage.width+150,document.featImage.height+265);">
<h1 class="pageHeading"><?php echo $products_values['products_name'] ; ?></h1>
      <table border="0" cellspacing="6" cellpadding="0">
<tr>
        <?php
        //获取商品图片 
        $img_array =
        tep_products_images($products_values['products_id'],$products_values['site_id']);
        foreach($img_array as $img_value){
       
          if(tep_not_null($img_value)){
         
            echo '<td align="center" class="image_border"><a
              href="popup_image.php?image='.$img_value .'&pIID='.$_GET['pIID'].'
              ">'. tep_image2(DIR_WS_IMAGES .'products/'. $img_value, $products_values['products_name'], 60, 60, 'name="prod_thum_1"').'</a></td>';
          }
        }
        ?>
</tr>
</table>


<div align="center">
<?php 
   if($_GET['image'] && $_GET['image'] != '') {
     echo tep_image(DIR_WS_IMAGES . 'products/' . $_GET['image'], $products_values['products_name']);
  }else{   
    echo tep_image(DIR_WS_IMAGES .'products/'. $img_array[0], $products_values['products_name'],'','','name="featImage"');
  }
  
  ?></div>
<p class="main" align="right"><?php echo '<a href="javascript:window.close()">' . TEXT_CLOSE_WINDOW . '</a>'; ?></p>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
