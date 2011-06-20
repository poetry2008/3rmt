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
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) .  'selected_box=catalog');?>"><?php echo BOX_HEADING_CATALOG;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php 
            if ($selected_box == 'catalog') {
            ?>
            <div id="col2"> 
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php 
    echo '<a '.((basename($PHP_SELF) == FILENAME_CATEGORIES)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '">' . BOX_CATALOG_CATEGORIES_PRODUCTS . '</a><br>' .
                                   '<a '.((basename($PHP_SELF) == FILENAME_PRODUCTS_ATTRIBUTES)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '', 'NONSSL') . '">' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES . '</a><br>' .
                                   '<a '.((basename($PHP_SELF) == FILENAME_TAGS)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_TAGS, '', 'NONSSL') . '">' . BOX_CATALOG_PRODUCTS_TAGS . '</a><br>' .
                                   '<a '.((basename($PHP_SELF) == 'products_tags.php')?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link('products_tags.php', '', 'NONSSL') . '">タグ関連設定</a><br>' .
                                   '<a '.((basename($PHP_SELF) == FILENAME_MANUFACTURERS)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '">' . BOX_CATALOG_MANUFACTURERS . '</a><br>'.
                                    (isset($_color_l)?$_color_l:'') .
                                   '<a '.((basename($PHP_SELF) == FILENAME_REVIEWS)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '">' . BOX_CATALOG_REVIEWS . '</a><br>' .
                                   '<a '.((basename($PHP_SELF) == FILENAME_PRODUCTS_EXPECTED)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' .
                                   tep_href_link(FILENAME_PRODUCTS_EXPECTED, '',
                                     'NONSSL') . '">' .
                                   BOX_CATALOG_PRODUCTS_EXPECTED . '</a><br>'.
                                   '<a '.((basename($PHP_SELF) == 'cleate_oroshi.php')?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="'.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'">卸業者の名前設定</a><br>'.
                                   '<a '.((basename($PHP_SELF) == 'cleate_dougyousya.php')?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="'.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').  '">同業者の名前設定</a><br>'.
                                   '<a '.((basename($PHP_SELF) == FILENAME_CATEGORIES_ADMIN)?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="'.tep_href_link(FILENAME_CATEGORIES_ADMIN,'','NONSSL').'">'.'商品卸価格管理'.
                                   '</a><br>';
?>
                </td>
              </tr>
            </table> 
            </div> 
            <?php }?> 
            </td>
          </tr>
<!-- catalog_eof //-->
