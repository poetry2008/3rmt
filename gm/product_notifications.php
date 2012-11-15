<?php
/*
  $Id$

*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  # For Guest
  if($guestchk == '1') {
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'update_notifications')) {
    $products = $_POST['products'];
    $remove = '';
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      $remove .= '\'' . $products[$i] . '\',';
    }
    $remove = substr($remove, 0, -1);

    if (tep_not_null($remove)) {
      // ccdd
      tep_db_query("
          delete 
          from " . TABLE_PRODUCTS_NOTIFICATIONS . " 
          where customers_id = '" . $customer_id . "' 
            and products_id in (" . $remove . ")
      ");
    }

    tep_redirect(tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL'));
  } elseif (isset($_GET['action']) && ($_GET['action'] == 'global_notify')) {
    if (isset($_POST['global']) && ($_POST['global'] == 'enable')) {
      // ccdd
      tep_db_query("
          update " . TABLE_CUSTOMERS_INFO . " 
          set global_product_notifications = '1' 
          where customers_info_id = '" . $customer_id . "'
      ");
    } else {
      // ccdd
      $check_query = tep_db_query("
          select count(*) as count 
          from " . TABLE_CUSTOMERS_INFO . " 
          where customers_info_id = '" . $customer_id . "' 
            and global_product_notifications = '1'
      ");
      $check = tep_db_fetch_array($check_query);
      if ($check['count'] > 0) {
        // ccdd
        tep_db_query("
            update " . TABLE_CUSTOMERS_INFO . " 
            set global_product_notifications = '0' 
            where customers_info_id = '" . $customer_id . "'
        ");
      }
    }

    tep_redirect(tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_NOTIFICATIONS);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL'));

  //ccdd
  $global_status_query = tep_db_query("
      SELECT global_product_notifications 
      FROM " . TABLE_CUSTOMERS_INFO . " 
      WHERE customers_info_id = '" . $customer_id . "'
  ");
  $global_status = tep_db_fetch_array($global_status_query);
?>
<?php page_head();?>
</head>
<body>
<!-- header //--> 
<?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
<!-- header_eof //--> 
<!-- body //--> 
<div id="main">
<!-- body_text //-->
<div class="yui3-u" id="layout">
<div id="current"><?php echo $breadcrumb->trail(' <img src="images/point.gif"> '); ?></div>
<?php include('includes/search_include.php');?>
<div id="main-content">
<h2><?php echo HEADING_TITLE; ?></h2>
        <div id="hm-notifications"> 
          <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0"> 
            <tr> 
              <td>
              
             
              <table class="box_des" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td><?php echo TEXT_PRODUCT_NOTIFICATIONS_INTRODUCTION; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
<?php
  if ($global_status['global_product_notifications'] == '1') {
?>
          <tr>
            <td><b><?php echo HEADING_GLOBAL_PRODUCT_NOTIFICATIONS; ?></b></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td><?php echo TEXT_GLOBAL_PRODUCT_NOTIFICATIONS_ENABLED; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td><?php echo TEXT_GLOBAL_PRODUCT_NOTIFICATIONS_DESCRIPTION_ENABLED; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>

            <td><?php echo tep_draw_checkbox_field('global', 'enable', true) . '&nbsp;' . TEXT_ENABLE_GLOBAL_NOTIFICATIONS; ?></td>
    
            </tr>

       
           <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '20'); ?></td>
          </tr>
          
          <tr>

            <td>
  <?php echo tep_draw_form('global', tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, 'action=global_notify', 'SSL')); ?>

            <div>
            <?php echo tep_image_submit('button_update.gif',
                IMAGE_BUTTON_UPDATE,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_update.gif\'" onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_update_hover.gif\'"'); ?>           </div>
</form>  </td>
          </tr>

          </table>


                    <table width="100%">
<?php
  } else {
?>
          <tr>
            <td><b><?php echo HEADING_GLOBAL_PRODUCT_NOTIFICATIONS; ?></b></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td><?php echo TEXT_GLOBAL_PRODUCT_NOTIFICATIONS_DISABLED; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td><?php echo TEXT_GLOBAL_PRODUCT_NOTIFICATIONS_DESCRIPTION_DISABLED; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          </table>
          <?php echo tep_draw_form('global', tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, 'action=global_notify', 'SSL')); ?>
          <table width="100%">
          <tr>
            <td><?php echo tep_draw_checkbox_field('global', 'enable') . '&nbsp;' . TEXT_ENABLE_GLOBAL_NOTIFICATIONS; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '20'); ?></td>
          </tr>
          <tr>
            <td><?php echo tep_image_submit('button_update.gif',
                IMAGE_BUTTON_UPDATE,'onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_update.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_update_hover.gif\'"'); ?></td>
          </tr>
          </table>
          </form>
          <table>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '20'); ?></td>
          </tr>
          <tr>
            <td><b><?php echo HEADING_PRODUCT_NOTIFICATIONS; ?></b></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td><?php echo TEXT_PRODUCT_NOTIFICATIONS_LIST; ?></td>
          </tr>
          </table>
          <?php echo tep_draw_form('notifications', tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, 'action=update_notifications', 'SSL')); ?>
          <table width="100%" class="botton-continue">
<?php
    // ccdd
    $products_query = tep_db_query("
      select *
      from (
        select pd.products_id, 
               pd.products_name ,
               pd.site_id,
               pn.customers_id
        from " .  TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_NOTIFICATIONS . " pn 
        where pn.customers_id = '" . $customer_id . "' 
          and pn.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
        order by site_id DESC
      ) p
      where site_id = '0'
         or site_id = '".SITE_ID."' 
      group by products_id, customers_id
      order by products_name
    ");
    while ($products = tep_db_fetch_array($products_query)) {
      echo '          <tr>' . "\n" .
           '            <td>' . tep_draw_checkbox_field('products[]', $products['products_id']) . '&nbsp;' . $products['products_name'] . '</td>' . "\n" .
           '          </tr>' . "\n";
    }
?>
          
          <tr>
           <td align="left"><?php echo '<a href="' .
            tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' .
            tep_image_button('button_back.gif',
                IMAGE_BUTTON_BACK,' onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_back.gif\'"   onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_back_hover.gif\'"') . '</a>'; ?></td>
            <td align="right"><?php echo tep_image_submit('button_remove_notifications.gif',
                IMAGE_BUTTON_REMOVE_NOTIFICATIONS,'  onmouseout="this.src=\'includes/languages/japanese/images/buttons/button_remove_notifications.gif\'"  onmouseover="this.src=\'includes/languages/japanese/images/buttons/button_remove_notifications_hover.gif\'"'); ?></td>
            <td>     
            </tr>
     <?php
  }
?>
</table>
         </form>

        </td>
             </tr>
          </table>
             </td>
      </tr>
    </table></div></div></div>
	<?php include('includes/float-box.php');?>
		  </div>
      <!-- body_text_eof //--> 
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?>   
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
