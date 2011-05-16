<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  //require("includes/jcode.phps");
  $msg = "";
  if (isset($_GET['action']) && $_GET['action'] == 'download'){
      header("Content-Type: application/force-download");
      header('Pragma: public');
      header("Content-Disposition: attachment; filename=mag_list.csv");
      print chr(0xEF).chr(0xBB).chr(0xBF);
      $query = tep_db_query("
          select mag_id,
                 mag_email,
                 mag_name
          from mail_magazine 
          where site_id = '".$_GET['site_id']."'
          order by mag_id");


      $CsvFields = array("ＩＤ","メールアドレス","姓名");
    for($i=0;$i<count($CsvFields);$i++){
      print $CsvFields[$i] . ",";
    }
    print "\n";
    
    while($result = tep_db_fetch_array($query)) {
      //ID
      print $result['mag_id'] . ",";
    
      //メールアドレス
      print $result['mag_email'] . ",";
    
      //姓名
      print $result['mag_name'];
      
      print "\n";
    }
    
    //header("Location: ". $_SERVER['PHP_SELF']);
    $msg = MSG_UPLOAD_IMG;
    


 }else{


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET;?>">
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo MAG_DL_TITLE_TEXT;?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right">
                  <form action="mag_dl.php" method="get">
                    <?php echo tep_site_pull_down_menu('', false);?>
                    <input type="submit" value="ダウンロード">
                    <input type="hidden" name="action" value="download">
                  </form>
                </td>
              </tr>
            </table></td>
          </tr>


<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table></td>
  </tr>
</table>
<!-- body_eof //-->


<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->

<br>
</body>
</html>
<?php 
  require(DIR_WS_INCLUDES . 'application_bottom.php');


  }
?>
