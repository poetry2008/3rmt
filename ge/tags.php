<?php
/*
 $Id$
*/
require('includes/application_top.php');
forward404();
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_TAGS);

$breadcrumb->add(TAGS_NAVBAR_TITLE, tep_href_link(FILENAME_TAGS));

?>
<?php page_head($breadcrumb->trail_title(' &raquo; '));?>
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
<div id="content">
<div class="headerNavigation"><?php echo $breadcrumb->trail(' &raquo; '); ?></div>
<h2 class="pageHeading"><?php echo TAGS_HEADING_TITLE; ?></h2>
<div class="tag_list01">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php 
$tags_query_string = "
    select * 
    from " . TABLE_TAGS . " 
    order by tags_order
";
$tags_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $tags_query_string, $tags_numrows);
//ccdd
$tags_query = tep_db_query($tags_query_string);
if (($tags_numrows > 0 ) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')))
{
?>
<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td class="smallText">
<?php 
echo $tags_split->display_count($tags_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
?>
<br>
<br>
</td>
<td align="right" class="smallText">
<?php
echo TEXT_RESULT_PAGE;
?>
<?php 
echo $tags_split->display_links($tags_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y')));
?>
<br>
<br>
</td>
</tr>
</table>
<?php
//echo tep_draw_separator('pixel_trans.gif', '100%', '10') . "\n";
?>
</td>
</tr>
<?php
}
?>
<tr>
<td>
<?php
while ($tag = tep_db_fetch_array($tags_query))
{
  if (tep_session_is_registered('customer_id'))
  {
//ccdd
    $products_query = tep_db_query("
      select * 
      from (
        select p.products_id,
               p.products_quantity,
               p.products_model,
               p.products_image,
               p.products_image2,
               p.products_image3,
               p.products_price,
               p.products_price_offset,
               p.products_date_added,
               p.products_last_modified,
               p.products_date_available,
               p.products_weight,
               pd.products_status,
               p.products_tax_class_id,
               p.manufacturers_id,
               p.products_ordered,
               p.products_bflag,
               p.products_cflag,
               p.products_small_sum,
               p.option_type,
               p.sort_order,
               p2t.tags_id,
               pd.language_id,
               pd.products_name,
               pd.products_description,
               pd.site_id,
               p.products_attention_1,
               p.products_attention_2,
               p.products_attention_3,
               p.products_attention_4,
               p.products_attention_5,
               pd.products_url,
               pd.products_viewed
        from " . TABLE_PRODUCTS_TO_TAGS . " as p2t 
          join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id 
          left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id 
        where p2t.tags_id = " . $tag['tags_id'] .  " 
        order by pd.site_id DESC
      ) p
      where site_id  = '0'
         or site_id = '".SITE_ID."' 
        group by products_id
        order by sort_order asc , products_name asc
        limit 5
    ");
    /*
    $products_query = tep_db_query("
        select *,p.products_id 
        from " . TABLE_PRODUCTS_TO_TAGS . " as p2t 
          join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id 
          left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id 
          left join " . TABLE_SPECIALS . " as s on p.products_id = s.products_id 
        where p2t.tags_id = " . $tag['tags_id'] .  " 
          and pd.site_id = '".SITE_ID."' 
        order by p.products_date_added desc 
        limit 5
    ");
    */
  } else {
//ccdd
    $products_query = tep_db_query("
      select *
      from (
        select p.products_id,
               p.products_quantity,
               p.products_model,
               p.products_image,
               p.products_image2,
               p.products_image3,
               p.products_price,
               p.products_price_offset,
               p.products_date_added,
               p.products_last_modified,
               p.products_date_available,
               p.products_weight,
               pd.products_status,
               p.products_tax_class_id,
               p.manufacturers_id,
               p.products_ordered,
               p.products_bflag,
               p.products_cflag,
               p.products_small_sum,
               p.option_type,
               p.sort_order,
               p2t.tags_id,
               pd.language_id,
               pd.products_name,
               pd.products_description,
               pd.site_id,
               p.products_attention_1,
               p.products_attention_2,
               p.products_attention_3,
               p.products_attention_4,
               p.products_attention_5,
               pd.products_url,
               pd.products_viewed
        from " . TABLE_PRODUCTS_TO_TAGS . " as p2t 
          join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id 
          left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id 
        where p2t.tags_id = " . $tag['tags_id'] .  " 
        order by pd.site_id DESC
      ) p
      where site_id = '0'
         or site_id = '".SITE_ID."' 
      group by products_id
      order by sort_order asc , products_name asc
      limit 5
    ");
    /*
    $products_query = tep_db_query("
        select *,p.products_id 
        from " . TABLE_PRODUCTS_TO_TAGS . " as p2t 
          join ". TABLE_PRODUCTS . " as p on p2t.products_id = p.products_id 
          left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd on p.products_id = pd.products_id 
          left join " . TABLE_SPECIALS . " as s on p.products_id = s.products_id 
        where p2t.tags_id = " . $tag['tags_id'] .  " 
          and pd.site_id = '".SITE_ID."' 
        order by p.products_date_added desc 
        limit 5
    ");
    */
  } 
  if (tep_db_num_rows($products_query))
  {

    echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";
    echo '  <tr>' . "\n";
    echo '    <td width="120" class="smallText" valign="top"><h3 class="tag_h03"><b>'.$tag['tags_name'].'</b></h3><div class="manufacturer_image01">' .
                  tep_image(DIR_WS_IMAGES.$tag['tags_images'],$tag['tags_name']) .
                  '</div>' . "\n";
                //<!-- '.mb_substr(strip_tags($manufacturer['manufacturers_url']),0,100,'utf8') .'... --></td>' . "\n";
    echo '    </tr><tr><td valign="bottom">' . "\n";
  
    echo '      <table width="100%" border="0" cellspacing="2" cellpadding="0">' . "\n";
    echo '        <tr>' . "\n";
    while($products = tep_db_fetch_array($products_query)) {
      $products['products_name'] = tep_get_products_name($products['products_id']);
      $products['products_description'] = tep_get_products_description($products['products_id']);
      echo '<td align="center" valign="bottom" class="smallText"
                          width="20%" style="padding-bottom:8px;">';
                        echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id='.$products['products_id']) . '">';
                        if ($products['products_image'])
                        {
                          echo tep_image2(DIR_WS_IMAGES.'products/'.$products['products_image'],$products['products_name'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT,'class="image_border"');
                        }
                        else
                        {
                          echo tep_image2(DIR_WS_IMAGES.'new_products_blank_small.gif',$products['products_name'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT,'class="image_border"');
                        }
                          echo '<br>' .$products['products_name'] . '</a><br>';
      if (tep_get_special_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'])) {
        echo '<s>' . $currencies->display_price(tep_get_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum']), tep_get_tax_rate($products['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum']), tep_get_tax_rate($products['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        echo $currencies->display_price(tep_get_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum']), tep_get_tax_rate($products['products_tax_class_id']));
      }
                          /*if ($products['specials_new_products_price'])
                          {
                            echo $currencies->display_price($products['specials_new_products_price'], tep_get_tax_rate($products['products_tax_class_id']));
                          }
                          else
                          {
                            echo $currencies->display_price($products['products_price'], tep_get_tax_rate($products['products_tax_class_id']));
                          }*/
                          echo '</td>'."\n";
    }
    echo '        </tr>' . "\n";
    echo '      </table>' . "\n";
    echo '    </td>' . "\n";
    echo '  </tr>' . "\n";
    echo '  <tr>' . "\n";
    echo '    <td colspan="2" align="right" class="smallText" >' .  '<a href="'.tags_tep_href_link($tag['tags_id']).'">'.TAGS_TEXT_MORE.'</a></td>'."\n";
    echo '  </tr>' . "\n";
    echo '</table><br><div class="dot">&nbsp;</div>' . "\n";
  }

}
?>
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                <?php
                            if (tep_db_num_rows($tags_query)) {
?>
                                <tr>
                                  <td class="smallText"><?php echo
                                  $tags_split->display_count($tags_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
                                  <td align="right" class="smallText"><?php echo
                                  TEXT_RESULT_PAGE; ?> <?php echo
                                  $tags_split->display_links($tags_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
                                </tr>
                                <?php
  }
?>
                                <tr>
                                  <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                                </tr>
                              </table>
                            </td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
      </div></div>
      <!-- body_text_eof --> 
<!-- right_navigation --> 
<div id="r_menu">
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
</div>
<!-- right_navigation_eof -->
  <!-- body_eof -->
  <!-- footer -->
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <!-- footer_eof -->
</div>
</body>
</html><?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>