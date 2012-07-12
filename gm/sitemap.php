<?php
/*
  $Id$
*/

  require('includes/application_top.php');

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SITEMAP);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SITEMAP));
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
     <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
      <tr>
        <td>
           <table border="0" width="100%" cellspacing="1" cellpadding="2">
            <tr>
               <td width="50%" class="box_des sitemap" valign="top">
               <?php  include(DIR_WS_CLASSES.'category_tree.php'); $osC_CategoryTree = new osC_CategoryTree; echo $osC_CategoryTree->buildTree(); ?></td>
               <td width="50%" class="box_des sitemap" valign="top">
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
                  <li><?php echo '<a href="' . tep_href_link('reorder.php') .  '">'.PAGE_REORDER_LINK.'</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_ADVANCED_SEARCH) . '">' . PAGE_ADVANCED_SEARCH . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCTS_NEW) . '">' . PAGE_PRODUCTS_NEW . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_SPECIALS) . '">' . PAGE_SPECIALS . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_REVIEWS) . '">' . PAGE_REVIEWS . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_LATEST_NEWS) . '">' . PAGE_NEWS . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">' . BOX_INFORMATION_CONTACT . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN) . '">' . PAGE_PASSWORD_FORGOTTEN . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_SEND_MAIL) . '">' . PAGE_SEND_MAIL . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_EMAIL_TROUBLE) . '">' . PAGE_EMAIL_TROUBLE . '</a>'; ?></li>
                  <li><?php echo '<a href="' . tep_href_link(FILENAME_BROWSER_IE6X) . '">' . PAGE_BROWSER_IE6X. '</a>'; ?></li>
                  <li><?php echo '<a href="'.HTTP_SERVER.'/link/">'.PAGE_LINK_TEXT.'</a>'; ?></li>
                  <li><?php echo '<a href="'.HTTP_SERVER.'/faq/">'.PAGE_FAQ_LINK.'</a>'; ?></li>
                  <li style="font-size:18px;margin-top:28px"><?php echo BOX_HEADING_INFORMATION; ?></li>
        <li class="subcategory_tree">
                <ul>
<?php
                          // ccdd
  $contents_page = tep_db_query("
      select * 
      from ".TABLE_INFORMATION_PAGE." 
      where status = 1 
        and site_id = ".SITE_ID." 
      order by sort_id
  ");
   while($result = tep_db_fetch_array($contents_page)){
     // add info romaji          
     echo '<li><a href="'.info_tep_href_link($result['romaji']).'">'.$result['heading_title'].'</a></li>'."\n" ;
  } 
// Extra Pages ADDED END
?>
        </ul>
                </li>
              </ul>
            </td>
          </tr>
        </table></td>
      </tr>
    </table></div>
	</div>
<?php include('includes/float-box.php');?>
</div>
      <!-- body_text_eof //--> 
  <!-- body_eof //--> 
  <!-- footer //--> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof //--> 
</div> 

</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
