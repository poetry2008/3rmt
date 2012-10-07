<?php
/*
  $Id$
*/
?>
<!-- catalog //-->
          <tr>
            <td>
<?php
  /* 
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_CATALOG ,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=catalog'));
                           if(COLOR_SEARCH_BOX_TF == "true" ){
                                   $_color_l = '<a href="' . tep_href_link(FILENAME_COLOR, '', 'NONSSL') . '" class="menuBoxContent_Link">カラーマスタ登録</a><br>'  ;
                                   }
  
  
  if ($selected_box == 'catalog') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_TAGS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_PRODUCTS_TAGS . '</a><br>' .
                                   '<a href="' . tep_href_link('products_tags.php', '', 'NONSSL') . '" class="menuBoxContent_Link">タグ関連設定</a><br>' .
                                   //'<a href="' .  tep_href_link(FILENAME_IMAGE_DOCUMENT, '', 'NONSSL') . '" class="menuBoxContent_Link">' .  BOX_CATALOG_IMAGE_DOCUMENT . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_MANUFACTURERS . '</a><br>'.
                                    (isset($_color_l)?$_color_l:'') .
                                   '<a href="' . tep_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_REVIEWS . '</a><br>' .
                                   //'<a href="' . tep_href_link(FILENAME_SPECIALS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_SPECIALS . '</a><br>' .
                                   '<a href="' .
                                   tep_href_link(FILENAME_PRODUCTS_EXPECTED, '',
                                     'NONSSL') . '" class="menuBoxContent_Link">' .
                                   BOX_CATALOG_PRODUCTS_EXPECTED . '</a><br>'.
                                   //'<a href="'.tep_href_link('cleate_list.php', '', 'NONSSL').'" class="menuBoxContent_Link">卸業者のデータ登録</a><br>'.
                                   '<a href="'.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'"
                                    class="menuBoxContent_Link">卸業者の名前設定</a><br>'.
                                   '<a href="'.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').
                                   '"class="menuBoxContent_Link">同業者の名前設定</a><br>'.
                                   '<a href="'.tep_href_link(FILENAME_CATEGORIES_ADMIN,'','NONSSL').'" class="menuBoxContent_Link">'.'商品卸価格管理'. '</a><br>'.
                                   '<!--<hr>' . 
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_UP, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_PRODUCTS_UP . '</a><br>' . 
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_DL, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_PRODUCTS_DL . '</a>-->');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
  */
?>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
						 <?php echo tep_image(DIR_WS_IMAGES . 'img/contents.gif'); ?> <a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col2');"><?php echo BOX_HEADING_CATALOG;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col2', $l_select_box_arr)) {
            ?>
            <div id="col2" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col2" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php 
    echo '<span class="menuBoxContent_image">'.tep_image(DIR_WS_IMAGES . 'img/list_shoping.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS . '</a></span><br><span class="menuBoxContent_image">' .
			tep_image(DIR_WS_IMAGES . 'img/link_delete.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_SHOW_USELESS_OPTION, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_SHOW_USELESS_OPTION . '</a></span><br><span class="menuBoxContent_image">' .
                                   tep_image(DIR_WS_IMAGES . 'img/table_sort.gif') .'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_OPTION_GROUP, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES . '</a></span><br><span class="menuBoxContent_image">' .
																	 tep_image(DIR_WS_IMAGES . 'img/label.gif') . '</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_TAGS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_PRODUCTS_TAGS . '</a></span><br><span class="menuBoxContent_image">' .
                                   tep_image(DIR_WS_IMAGES . 'img/lable_add.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link('products_tags.php', '', 'NONSSL') . '" class="menuBoxContent_Link">'.FILENAME_PRODUCTS_TAGS_TEXT.'</a></span><br><span class="menuBoxContent_image">' .
																	 tep_image(DIR_WS_IMAGES . 'img/register.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_MANUFACTURERS . '</a></span><br><span class="menuBoxContent_image">'.
                                    (isset($_color_l)?$_color_l:'') .
                                   tep_image(DIR_WS_IMAGES . 'img/comment_add.gif').'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '" class="menuBoxContent_Link">' . BOX_CATALOG_REVIEWS . '</a></span><br><span class="menuBoxContent_image">' .
                                  // '<a href="' .
                                  // tep_href_link(FILENAME_PRODUCTS_EXPECTED, '',
                                   //  'NONSSL') . '" class="menuBoxContent_Link">' .
                                   //BOX_CATALOG_PRODUCTS_EXPECTED . '</a><br>'.
                                   tep_image(DIR_WS_IMAGES . 'img/group_error.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'"
																	 class="menuBoxContent_Link">'.FILENAME_CLEATE_OROSHI_TEXT.'</a></span><br><span class="menuBoxContent_image">'.
																		 tep_image(DIR_WS_IMAGES . 'img/group_link.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').
                                   '"class="menuBoxContent_Link">'.FILENAME_CLEATE_DOUGYOUSYA_TEXT.'</a></span><br><span class="menuBoxContent_image">'.
                                   tep_image(DIR_WS_IMAGES . 'img/price.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link(FILENAME_CATEGORIES_ADMIN,'','NONSSL').'"
                                   class="menuBoxContent_Link">'.FILENAME_CATEGORIES_ADMIN_TEXT.
                                     '</a></span><br><span class="menuBoxContent_image">'.
                                   tep_image(DIR_WS_IMAGES . 'img/house_address.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('address.php','','NONSSL').'"
                                   class="menuBoxContent_Link">'.BOX_CREATE_ADDRESS.'</a></span><br><span class="menuBoxContent_image">
                                   '.tep_image(DIR_WS_IMAGES . 'img/money_dollar.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('country_fee.php','','NONSSL').'"
                                   class="menuBoxContent_Link">'.BOX_COUNTRY_FEE.'</a></span><br><span class="menuBoxContent_image">
                                   '.tep_image(DIR_WS_IMAGES . 'img/time_go.gif').'</span><span class="menuBoxContent_span"><a href="'.tep_href_link('products_shipping_time.php','','NONSSL').'"
                                   class="menuBoxContent_Link">'.BOX_SHIPPING_TIME.'</a></span>';

?>
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- catalog_eof //-->
