<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/password_success.php');
  $breadcrumb->add(HEADING_TITLE);
?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- left_navigation //-->
<div id="layout" class="yui3-u">        <div id="current"><?php echo
$breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
		<?php include('includes/search_include.php');?>


	<div id="main-content">
    <h2><?php echo HEADING_TITLE ; ?></h2>
    <?php echo tep_draw_form('account_edit', tep_href_link(FILENAME_ACCOUNT_EDIT_PROCESS, '', 'SSL'), 'post', 'onSubmit="return check_form();"') . tep_draw_hidden_field('action', 'process'); ?> 
    <div style="font-size:14px;"><?php 
          echo UPDATE_PASSWORD_SUCCESS_TEXT;
        ?></div>
 <table class="botton-continue" border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td align="right">
                <?php echo '<a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?> </td> 
            </tr> 
          </table></form>
        </div></div>
 <?php include('includes/float-box.php');?>
        </div>
        <?php include('includes/new.php');?>
</div> 
 <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
