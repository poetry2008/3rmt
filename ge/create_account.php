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
<script type="text/javascript">
<!--
function pass_hidd(CI){
  /*
  var idx = document.account_edit.elements["guestchk"].selectedIndex;
  var CI = document.account_edit.elements["guestchk"].options[idx].value;
*/
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
<!-- header --> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof --> 
<!-- body --> 
<div id="main">
<!-- left_navigation -->
<div id="l_menu">
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
</div>
<!-- left_navigation_eof -->
<!-- body_text -->
<div id="content"><?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_CREATE_ACCOUNT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 
        <div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
<div class="comment"> 
          <table border="0" width="100%" cellspacing="0" cellpadding="0"> 
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
              <td> <?php
  if (isset($_GET['email_address'])) $email_address = tep_db_prepare_input($_GET['email_address']);
  $account['entry_country_id'] = STORE_COUNTRY;

  require(DIR_WS_MODULES . 'account_details.php');
?> </td> 
            </tr> 
            <tr> 
              <td align="right" class="main">
                <?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td> 
            </tr> 
          </table> 
        </div></form></div>
      <!-- body_text_eof --> 
<!-- right_navigation --> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
  <script>
  document.onreadystatechange=function(){
  var obj = document.getElementsByName("guestchk"); 
  for(i = 0;i < obj.length;i++)    { 
    if(obj[i].checked){ 
      CI = obj[i].value; 
    } 
  }      
  pass_hidd(CI);  
  }
  </script>
</div>
<!-- right_navigation_eof --> 
  <!-- body_eof -->  
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
