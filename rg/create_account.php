<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ACCOUNT);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CREATE_ACCOUNT));
?>
<?php page_head();?>
<?php require('includes/form_check.js.php'); ?>
<script type="text/javascript" language="javascript">
<!--
function pass_hidd(){
  var idx = document.account_edit.elements["guestchk"].selectedIndex;
  var CI = document.account_edit.elements["guestchk"].options[idx].value;
  
  if(CI == '0'){
    document.getElementById('trpass1').style.display = "";
	document.getElementById('trpass2').style.display = "";
  }else{
    document.getElementById('trpass1').style.display = "none";
	document.getElementById('trpass2').style.display = "none";
  }
}
-->
</script>
</head>
<body>
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof //--> 
  <!-- body //--> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border" summary="box"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof //--> </td> 
      <!-- body_text //--> 
      <td valign="top" id="contents"><?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_CREATE_ACCOUNT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 
        <h1 class="pageHeading"><img align="top" alt="" src="images/menu_ico.gif"><span><?php echo HEADING_TITLE ; ?></span></h1> 
        <div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0" summary="table"> 
            <?php
  if (sizeof($navigation->snapshot) > 0) {
?> 
            <tr> 
              <td class="smallText"><br> 
                <?php echo sprintf(TEXT_ORIGIN_LOGIN, tep_href_link(FILENAME_LOGIN, tep_get_all_get_params(), 'SSL')); ?></td> 
            </tr> 
            <?php
  }
?> 
            <tr> 
              <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td> 
            </tr> 
            <tr> 
              <td><?php
  if (isset($_GET['email_address'])) $email_address = tep_db_prepare_input($_GET['email_address']);
  $account['entry_country_id'] = STORE_COUNTRY;

  require(DIR_WS_MODULES . 'account_details.php');
?> </td> 
            </tr> 
            <tr> 
              <td align="right" class="main"><br> 
                <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
            </tr> 
          </table> 
          </div>
          </form> 

      	<p class="pageBottom"></p>  
      </td> 
      <!-- body_text_eof //--> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation //--> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof //--> </td> 
  </table> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
