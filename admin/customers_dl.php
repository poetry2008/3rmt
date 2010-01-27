<?php
/*
  $Id: mail.php,v 1.3 2003/03/18 02:51:42 ptosh Exp $


  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com


  Copyright (c) 2002 osCommerce


  Released under the GNU General Public License
*/


  require('includes/application_top.php');
  require(DIR_FS_CATALOG . 'includes/configure.php');
  require("includes/jcode.phps");
  
  $msg = "";


  if (isset($HTTP_POST_VARS['action']) && $HTTP_POST_VARS['action'] == 'download'){
	    header("Content-Type: application/force-download");
	    header('Pragma: public');
	    header("Content-Disposition: attachment; filename=customers.csv");
	
 	    $query = tep_db_query("select 
	    				c.customers_id, 
	    				c.customers_gender, 
	    				c.customers_lastname, 
	    				c.customers_firstname, 
					c.customers_dob, 
	    				c.customers_email_address, 
	    				c.customers_telephone, 
					c.customers_fax, 
					c.customers_password, 
					c.customers_newsletter, 
					c.customers_default_address_id, 
					a.entry_company, 

					a.entry_postcode, 
					a.entry_zone_id, 
					a.entry_city, 
					a.entry_street_address, 
					a.entry_suburb, 
					a.entry_country_id 
					from " . TABLE_CUSTOMERS . " c left join " . TABLE_ADDRESS_BOOK . " a 
					on c.customers_default_address_id = a.address_book_id and c.customers_id = a.customers_id");
	    //$query = tep_db_query("select * from " . TABLE_CUSTOMERS . " order by customers_id");
	    //$mail_query = tep_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
	    //while($customers_values = tep_db_fetch_array($mail_query)) {
	    //$customers[] = array('id' => $customers_values['customers_email_address'],
            //           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    	    //}


	    $CsvFields = array("ＩＤ","","","" ,"","メールアドレス","電話","FAX","パスワード","ニュースレター","アドレスＩＤ","" ,"","","" ,"住所","住所(2)","");
		for($i=0;$i<count($CsvFields);$i++){
			print jcodeconvert($CsvFields[$i],0,2) . ",";
		}
		print "\n";
		
		while($result = mysql_fetch_array($query)) {
	      //ＩＤ
		  print jcodeconvert($result['customers_id'],0,2) . ",";
	      //
		  print jcodeconvert($result['customers_gender'],0,2) . ",";
	      //
		  print jcodeconvert($result['customers_lastname'],0,2) . ",";
	      //
		  print jcodeconvert($result['customers_firstname'],0,2) . ",";
	      //
		  print jcodeconvert($result['customers_dob'],0,2) . ",";
	      //メールアドレス
		  print jcodeconvert($result['customers_email_address'],0,2) . ",";
	      //
		  print jcodeconvert($result['customers_telephone'],0,2) . ",";
	      //FAX
		  print jcodeconvert($result['customers_fax'],0,2) . ",";
	      //パスワード
		  print jcodeconvert($result['customers_password'],0,2) . ",";
	      //ニュースレター
		  print jcodeconvert($result['customers_newsletter'],0,2) . ",";
	      //アドレスＩＤ
		  print jcodeconvert($result['customers_default_address_id'],0,2) . ",";
	      //
		  print jcodeconvert($result['entry_company'],0,2) . ",";
	      //
		  print jcodeconvert($result['entry_postcode'],0,2) . ",";
	      //
		  $zone = tep_get_zone_name($result['entry_zone_id']);
		  print jcodeconvert($zone,0,2) . ",";
	      //
		  print jcodeconvert($result['entry_city'],0,2) . ",";
	      //住所
		  print jcodeconvert($result['entry_street_address'],0,2) . ",";
	      //住所(2)
		  print jcodeconvert($result['entry_suburb'],0,2) . ",";
	      //
		  print jcodeconvert($result['entry_country_id'],0,2) . "\n";
	    }

		/*
		for($j=0;$j<mysql_num_rows($query);$j++){
		    for($k=0;$k<mysql_num_fields($query);$k++){
			print jcodeconvert(mysql_result($query,$j,$k),0,2),0,2) . ",";
		    }
		    print "\n";
		}
		*/
		
		//header("Location: ". $_SERVER['PHP_SELF']);
		$msg = MSG_UPLOAD_IMG;
		


 }else{


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
    <td width="100%" valign="top">
	<form action="customers_dl.php" method="post">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">CUSTOMER DATA DOWNLOAD</td>
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
                <td colspan="2" align="right"><input type="submit" value="DOWNLOAD"></td>
              </tr>
            </table>
			<input type="hidden" name="action" value="download">
			</form>
			</td>
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