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
<div class="body_shadow" align="center"> 
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?> 
  <!-- header_eof --> 
  <!-- body --> 
  <table width="900" border="0" cellpadding="0" cellspacing="0" class="side_border"> 
    <tr> 
      <td width="<?php echo BOX_WIDTH; ?>" align="right" valign="top" class="left_colum_border"> <!-- left_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?> 
        <!-- left_navigation_eof --> </td> 
      <!-- body_text --> 
      <td valign="top" id="contents"><h1 class="pageHeading"><?php echo TAGS_HEADING_TITLE ; ?></h1> 
        
        <div class="comment"> 
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


$all_tags_page = ceil($tags_numrows/MAX_DISPLAY_SEARCH_RESULTS);
if($_GET['page'] > $all_tags_page){
  forward404();
}
if (($tags_numrows > 0 ) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')))
{
?>
<tr>
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr>
<td class="smallText" style="border-bottom:#ccc solid 1px;">
<?php 
echo $tags_split->display_count($tags_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_PRODUCTS);
?>
<br>
<br>
</td>
<td align="right" class="smallText" style="border-bottom:#ccc solid 1px;">
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
echo tep_draw_separator('pixel_trans.gif', '100%', '10') . "\n";
?>
</td>
</tr>
<?php
}
?>
<tr>
<td>
<?php
$z=0;
while ($tag = tep_db_fetch_array($tags_query))
{
  $products_id_sql = "
      SELECT products_id from 
      ".TABLE_PRODUCTS_TO_TAGS." 
      WHERE tags_id = '".$tag['tags_id']."'";
  /*根据tags_id 查找对应的 products_id*/
  $products_id_query = tep_db_query($products_id_sql);
  
  $products_ids = "(";
  $has_products_ids = false;
  while($_products_id = tep_db_fetch_array($products_id_query)){
    $has_products_ids = true;
    $products_ids .= $_products_id['products_id'].",";
  }
  if($has_products_ids){
    $products_ids = substr($products_ids,0,-1);
    $products_ids .= ")";
    $products_sql = "
        SELECT distinct(pd.products_id)
        FROM 
        ".TABLE_PRODUCTS_DESCRIPTION." pd 
        left join ".TABLE_PRODUCTS." p 
        on p.products_id = pd.products_id 
        WHERE pd.products_id in ".$products_ids."  
        and pd.products_status != 3
        and pd.products_status != 0
        and (pd.site_id ='0' or pd.site_id = '".SITE_ID."') 
        order by pd.site_id desc, p.sort_order ASC ,pd.products_name asc
        limit 5";
   /*
    在查找到的products_id 中 查找 5条记录 通过 sort order 和 products_name
    排序
   */
    $products_query = tep_db_query($products_sql);


  if (tep_db_num_rows($products_query))
  {

    echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">' . "\n";
    echo '<tr>' . "\n";
    echo '<td class="smallText" valign="top"><a href="'.tags_tep_href_link($tag['tags_id']).'"><b>'.$tag['tags_name'].'</b></a><div class="manufacturer_image">' .  tep_image(DIR_WS_IMAGES.$tag['tags_images'],$tag['tags_name'],100, 100) .  '</div>' . "\n";
    echo '</td></tr><tr><td valign="bottom">' . "\n";
  
    echo '<table width="100%" border="0" cellspacing="2" cellpadding="0">' . "\n";
    echo '<tr>' . "\n";
    while($_products = tep_db_fetch_array($products_query)) {
      $_products_sql ="
        select p.products_id,
               p.products_real_quantity + p.products_virtual_quantity as products_quantity,
               p.products_model,
               p.products_image,
               p.products_image2,
               p.products_image3,
               p.products_price,
               p.products_price_offset,
               p.products_date_added,
               pd.products_last_modified,
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
               pd.language_id,
               pd.products_name,
               pd.products_description,
               pd.site_id,
               pd.products_url,
               pd.products_viewed,
               pd.products_status 
        from ". TABLE_PRODUCTS . " as p 
        left join " . TABLE_PRODUCTS_DESCRIPTION . " as pd 
        on p.products_id = pd.products_id 
        where p.products_id = '".$_products['products_id']."'
        and (pd.site_id ='0' or pd.site_id = '".SITE_ID."')
        order by pd.site_id desc";
      /*根据products_id 查找商品的详细 信息*/
      $_products_query = tep_db_query($_products_sql);
      $products = tep_db_fetch_array($_products_query);
      if($products['products_status'] != 3 && $products['products_status'] != 0){
      //$products['products_name'] = tep_get_products_name($products['products_id']);
      //$products['products_description'] = tep_get_products_description($products['products_id']);
      echo '<td align="center" valign="top" class="smallText" width="20%" style="padding-bottom:8px;">';
                        echo '<a href="' .
                          tep_href_link(FILENAME_PRODUCT_INFO,'products_id='.  $products['products_id']) . '">';
            echo '<span>';
                        if ($products['products_image'])
                        {
                          echo tep_image(DIR_WS_IMAGES.'products/'.$products['products_image'],$products['products_name'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT,'class="image_border"');
                        }
                        else
                        {
                          echo tep_image(DIR_WS_IMAGES.'new_products_blank_small.gif',$products['products_name'],SMALL_IMAGE_WIDTH,SMALL_IMAGE_HEIGHT,'class="image_border"');
                        }
            echo '</span>';
                          echo '<br>' .$products['products_name'] . '</a><br>';
      if (tep_get_special_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum'])) {
        echo '<s>' . $currencies->display_price(tep_get_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum']), tep_get_tax_rate($products['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price(tep_get_special_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum']), tep_get_tax_rate($products['products_tax_class_id'])) . '</span>&nbsp;';
      } else {
        echo $currencies->display_price(tep_get_price($products['products_price'], $products['products_price_offset'], $products['products_small_sum']), tep_get_tax_rate($products['products_tax_class_id']));
      }
      echo '</td>'."\n";
                          if($z==0){
                           $products_id_sql_1 = $products_id_sql; 
                           $products_sql_1 = $products_sql; 
                           $_products_sql_1 = $_products_sql; 

                          }
                          $z++;
      }
    }
    echo '        </tr>' . "\n";
    echo '      </table>' . "\n";
    echo '    </td>' . "\n";
    echo '  </tr>' . "\n";
    echo '  <tr>' . "\n";
    echo '    <td colspan="2" align="right" class="smallText" style="padding-top:5px;border-bottom:#ccc solid 1px;">' . '<a href="'.tags_tep_href_link($tag['tags_id']).'">'.TAGS_TEXT_MORE.'</a></td>'."\n";
    echo '  </tr>' . "\n";
    echo '</table><br><div class="dot">&nbsp;</div>' . "\n";
  }
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
</div> 
    <p class="pageBottom"></p>
    </td> 
      <!-- body_text_eof --> 
      <td valign="top" class="right_colum_border" width="<?php echo BOX_WIDTH; ?>"> <!-- right_navigation --> 
        <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?> 
        <!-- right_navigation_eof --> </td> 
                    </tr>
  </table> 
  <!-- body_eof --> 
  <!-- footer --> 
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?> 
  <!-- footer_eof --> 
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
