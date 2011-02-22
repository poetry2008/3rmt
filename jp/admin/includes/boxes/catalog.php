<?php
/*
  $Id$
*/
?>
<!-- catalog //-->
          <tr>
            <td>
<?php
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_CATALOG ,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=catalog'));
                           if(COLOR_SEARCH_BOX_TF == "true" ){
                                   $_color_l = '<a href="' . tep_href_link(FILENAME_COLOR, '', 'NONSSL') . '" class="menuBoxContentLink">カラーマスタ登録</a><br>'  ;
                                   }
  
  
  if ($selected_box == 'catalog') {
    $contents[] = array('text'  => '<a href="' . tep_href_link(FILENAME_CATEGORIES, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_CATEGORIES_PRODUCTS . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_CATEGORIES_PRODUCTS_ATTRIBUTES . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_TAGS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_PRODUCTS_TAGS . '</a><br>' .
                                   '<a href="' . tep_href_link('products_tags.php', '', 'NONSSL') . '" class="menuBoxContentLink">タグ関連設定</a><br>' .
                                   //'<a href="' .  tep_href_link(FILENAME_IMAGE_DOCUMENT, '', 'NONSSL') . '" class="menuBoxContentLink">' .  BOX_CATALOG_IMAGE_DOCUMENT . '</a><br>' .
                                   '<a href="' . tep_href_link(FILENAME_MANUFACTURERS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_MANUFACTURERS . '</a><br>'.
                                    (isset($_color_l)?$_color_l:'') .
                                   '<a href="' . tep_href_link(FILENAME_REVIEWS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_REVIEWS . '</a><br>' .
                                   //'<a href="' . tep_href_link(FILENAME_SPECIALS, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_SPECIALS . '</a><br>' .
                                   '<a href="' .
                                   tep_href_link(FILENAME_PRODUCTS_EXPECTED, '',
                                     'NONSSL') . '" class="menuBoxContentLink">' .
                                   BOX_CATALOG_PRODUCTS_EXPECTED . '</a><br>'.
                                   //'<a href="'.tep_href_link('cleate_list.php', '', 'NONSSL').'" class="menuBoxContentLink">卸業者のデータ登録</a><br>'.
                                   '<a href="'.tep_href_link('cleate_oroshi.php', '', 'NONSSL').'"
                                    class="menuBoxContentLink">卸業者の名前設定</a><br>'.
                                   '<a href="'.tep_href_link('cleate_dougyousya.php', '', 'NONSSL').
                                   '"class="menuBoxContentLink">同業者の名前設定</a><br>'.
                                   '<a href="'.tep_href_link(FILENAME_CATEGORIES_ADMIN,'','NONSSL').'" class="menuBoxContentLink">'.'商品卸価格管理'. '</a><br>'.
                                   '<!--<hr>' . 
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_UP, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_PRODUCTS_UP . '</a><br>' . 
                                   '<a href="' . tep_href_link(FILENAME_PRODUCTS_DL, '', 'NONSSL') . '" class="menuBoxContentLink">' . BOX_CATALOG_PRODUCTS_DL . '</a>-->');
  }

  $box = new box;
  echo $box->menuBox($heading, $contents);
?>
            </td>
          </tr>
<!-- catalog_eof //-->
