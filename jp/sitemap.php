<?php
/*
  $Id$

  html网站地图
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SITEMAP);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SITEMAP));
?>
<?php page_head();?>
</head>
<body>
<div class="body_shadow" align="center">
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border">
    <tr>
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border">
        <!-- left_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
        <!-- left_navigation_eof //-->
      </td>
      <!-- body_text //-->
      <td valign="top" id="contents">
        <h1 class="pageHeading"><?php echo HEADING_TITLE ; ?></h1>
        <div>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td>
                <table border="0" width="100%" cellspacing="1" cellpadding="2">
                  <tr>
                    <td width="50%" class="main" valign="top">
                      <?php require DIR_WS_CLASSES . 'category_tree.php'; $osC_CategoryTree = new osC_CategoryTree; echo $osC_CategoryTree->buildTree(); ?>
                    </td>
                    <td width="50%" class="main" valign="top">
                      <ul>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT, '', 'SSL') . '">' . PAGE_ACCOUNT . '</a>'; ?></li>
                        <li class="subcategory_tree">
                        <ul>
                          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . PAGE_ACCOUNT_EDIT . '</a>'; ?></li>
                          <li><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . PAGE_ACCOUNT_HISTORY . '</a>'; ?></li>
                          <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_NOTIFICATIONS, '', 'SSL') . '">' . PAGE_ACCOUNT_NOTIFICATIONS . '</a>'; ?></li>
                        </ul>
                        </li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL') . '">' . PAGE_SHOPPING_CART . '</a>'; ?></li>
                        <li><?php echo '<a href="' .  tep_href_link(FILENAME_CHECKOUT_ATTRIBUTES, '', 'SSL') . '">' . PAGE_CHECKOUT_SHIPPING . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_REORDER) . '">'.PAGE_REORDER.'</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_ADVANCED_SEARCH) . '">' . PAGE_ADVANCED_SEARCH . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_NEW) . '">' . PAGE_PRODUCTS_NEW . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_SPECIALS) . '">' . PAGE_SPECIALS . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_REVIEWS) . '">' . PAGE_REVIEWS . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_NEWS) . '">' . PAGE_NEWS . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_CONTACT_US,'','SSL') . '">' . BOX_INFORMATION_CONTACT . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN) . '">' . PAGE_PASSWORD_FORGOTTEN . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_SEND_MAIL) . '">' . PAGE_SEND_MAIL . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_EMAIL_TROUBLE) . '">' . PAGE_EMAIL_TROUBLE . '</a>'; ?></li>
                        <li><?php echo '<a href="' . tep_href_link(FILENAME_BROWSER_IE6X) . '">' . PAGE_BROWSER_IE6X. '</a>'; ?></li>
                        <li><?php echo '<a href="/link/">'.PAGE_LINK.'</a>'; ?></li>
                        <li><?php echo '<a href="'.tep_href_link('manufacturers.php').'">'.MENU_MU.'</a>'; ?></li>
                        <li><?php echo '<a href="'.tep_href_link(FILENAME_SPECIALS).'">'.BOX_HEADING_SPECIALS.'</a>'; ?></li>
                        <?php
                        $present_query = tep_db_query("select count(*) as cnt from " . TABLE_PRESENT_GOODS . " where site_id = '".SITE_ID."' ");
                        $present_result = tep_db_fetch_array($present_query);
                        if($present_result['cnt'] > 0) {
                          echo '<li><a href="'.tep_href_link(FILENAME_PRESENT).'">'.BOX_HEADING_PRESENT.'</a></li>';
                          }
                        ?>
                        <li><?php echo '<a href="/tags/">'.TEXT_TAGS.'</a>'; ?></li>
                        <li><?php echo BOX_HEADING_INFORMATION; ?></li>
                        <li class="subcategory_tree">
                        <ul>
                          <?php
                          
  $contents_page = tep_db_query("
      select * 
      from ".TABLE_INFORMATION_PAGE." 
      where status = 1 
        and site_id = ".SITE_ID." 
      order by sort_id
  ");
  while($result = tep_db_fetch_array($contents_page)){
       if($result['show_status'] != '1'){
             echo '<li><a href="'.info_tep_href_link($result['romaji']).'">'.$result['heading_title'].'</a></li>'."\n" ;
       }
  } 
// Extra Pages ADDED END
?>
                        </ul>
                        </li>
                      </ul>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </div>
      </td>
      <!-- body_text_eof //-->
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>">
        <!-- right_navigation //-->
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
        <!-- right_navigation_eof //-->
      </td>
    </tr>
  </table>
  <!-- body_eof //-->
  <!-- footer //-->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof //-->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
