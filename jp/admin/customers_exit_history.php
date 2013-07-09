<?php
/*
   $Id$
 */
require('includes/application_top.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/all_page.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php 
$belong = FILENAME_CUSTOMERS_EXIT_HISTORY;
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
<script language='javascript'>
  one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
</script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->
<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
      <!-- left_navigation -->
      <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
      <!-- left_navigation_eof -->
      </table>
    </td>
    <!-- body_text -->
    <td width="100%" valign="top">
      <div class="box_warp">
      <?php echo $notes;?>
      <div class="compatible">
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td width="100%" height="40">
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading">
                <?php echo HEADING_TITLE;?> 
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td valign="top">
                <?php
                  $exit_history_query = tep_db_query("select * from ".TABLE_CUSTOMERS_EXIT_HISTORY." where customers_id = '".$_GET['customers_id']."' order by created_at desc"); 
                  $i = tep_db_num_rows($exit_history_query); 
                  if (!$i) {
                    echo '<font color="#FF0000">'.TEXT_DATA_IS_EMPTY.'</font>'; 
                  }
                  while ($exit_history = tep_db_fetch_array($exit_history_query)) {
                    $exit_history_other = @unserialize($exit_history['other_info']);
                ?>
                  <fieldset> 
                  <legend style="color:#FF0000"><?php echo EXIT_HISTORY_RECORD.$i;?></legend> 
                  <table border="0" cellspacing="0" cellpadding="2" width="100%">
                    <tr>
                      <td class="main" width="220"><?php echo EXIT_HISTORY_SITE_ID;?></td> 
                      <td class="main">
                      <?php
                      $site_info_query = tep_db_query("select * from ".TABLE_SITES." where id = '".$exit_history['site_id']."'"); 
                      $site_info = tep_db_fetch_array($site_info_query);
                      echo $site_info['romaji'];
                      ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_FIRSTNAME;?></td> 
                      <td class="main">
                      <?php echo $exit_history['customers_firstname'];?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_LASTNAME;?></td> 
                      <td class="main">
                      <?php echo $exit_history['customers_lastname'];?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_EMAIL;?></td> 
                      <td class="main">
                      <?php echo $exit_history['customers_email'];?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_MAGZINE;?></td> 
                      <td class="main">
                      <?php 
                      if ($exit_history['customers_newsletter'] == '1') {
                        echo ORDER_MAGZINE; 
                      } else {
                        echo NO_ORDER_MAGZINE; 
                      }
                      ?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_RESET_FLAG;?></td> 
                      <td class="main">
                      <?php 
                      if ($exit_history_other['reset_flag'] == '1') {
                        echo EXIT_HISTORY_YES_TEXT; 
                      } else {
                        echo EXIT_HISTORY_NO_TEXT; 
                      }
                      ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_BAN_ORDER;?></td> 
                      <td class="main">
                      <?php 
                      if ($exit_history_other['is_seal'] == '1') {
                        echo EXIT_HISTORY_YES_TEXT; 
                      } else {
                        echo EXIT_HISTORY_NO_TEXT; 
                      }
                      ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_NO_SEND_MAIL;?></td> 
                      <td class="main">
                      <?php 
                      if ($exit_history_other['is_send_mail'] == '1') {
                        echo EXIT_HISTORY_YES_TEXT; 
                      } else {
                        echo EXIT_HISTORY_NO_TEXT; 
                      }
                      ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_CALC_QUANTITY;?></td> 
                      <td class="main">
                      <?php 
                      if ($exit_history_other['is_calc_quantity'] == '1') {
                        echo EXIT_HISTORY_YES_TEXT; 
                      } else {
                        echo EXIT_HISTORY_NO_TEXT; 
                      }
                      ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_POINT;?></td> 
                      <td class="main">
                      <?php echo $exit_history['point'].'&nbsp;P';?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_PIC_ICON_ALT;?></td> 
                      <td class="main">
                      <?php 
                      if (!empty($exit_history['pic_icon'])) {
                        $pic_info_raw = tep_db_query("select * from ".TABLE_CUSTOMERS_PIC_LIST." where pic_name = '".$exit_history['pic_icon']."'"); 
                        $pic_info = tep_db_fetch_array($pic_info_raw); 
                        echo $pic_info['pic_alt']; 
                      }
                      ?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_QUITEDATE;?></td> 
                      <td class="main">
                      <?php echo $exit_history['quited_date'];?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_SEARCH;?></td> 
                      <td class="main">
                      <?php echo nl2br($exit_history['customers_fax']);?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_LAST_LOGIN_DATE;?></td> 
                      <td class="main">
                      <?php echo $exit_history_other['last_login'];?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_LOGIN_NUM;?></td> 
                      <td class="main">
                      <?php echo $exit_history_other['login_num'];?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_ORDER_NUM;?></td> 
                      <td class="main">
                      <?php echo $exit_history_other['order_num'];?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo EXIT_HISTORY_REVIEW_NUM;?></td> 
                      <td class="main">
                      <?php echo $exit_history_other['review_num'];?>                      
                      </td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo TEXT_USER_ADDED.(tep_not_null($exit_history_other['user_add'])?$exit_history_other['user_add']:TEXT_UNSET_DATA);?></td> 
                      <td class="main"><?php echo TEXT_DATE_ADDED.(tep_not_null($exit_history_other['user_add_date'])?$exit_history_other['user_add_date']:TEXT_UNSET_DATA);?></td>
                    </tr>
                    <tr>
                      <td class="main"><?php echo TEXT_USER_UPDATE.(tep_not_null($exit_history_other['user_update'])?$exit_history_other['user_update']:TEXT_UNSET_DATA);?></td> 
                      <td class="main"><?php echo TEXT_DATE_UPDATE.(tep_not_null($exit_history_other['user_update_date'])?$exit_history_other['user_update_date']:TEXT_UNSET_DATA);?></td>
                    </tr>
                  </table>
                  </fieldset> 
                  <br> 
                <?php
                  $i--;                  
                  }
                ?>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      </div>
      </div>
      <!-- body_text_eof -->
    </td>
  </tr>
</table>
<!-- body_eof -->
<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
