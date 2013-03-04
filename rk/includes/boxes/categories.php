<?php
/*
  $Id$
*/
if ($cPath) {
  $ca_arr = explode('_', $cPath);
}
$categories = array();
// ccdd
$categories_query = tep_db_query("
    select * 
    from (
      select c.categories_id, 
             cd.categories_name, 
             cd.categories_status, 
             c.parent_id,
             cd.site_id,
             cd.categories_image2,
             c.sort_order
      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
      where c.parent_id = '0' 
        and c.categories_id = cd.categories_id 
        and cd.language_id='" . $languages_id ."' 
      order by site_id DESC
    ) c 
    where site_id = ".SITE_ID."
       or site_id = 0
    group by categories_id
    having c.categories_status != '1' and c.categories_status != '3' 
    order by sort_order, categories_name
");
while ($category = tep_db_fetch_array($categories_query))  {
  $categories[] = $category;
}
if($cPath){
  $id = preg_split('/_/', $cPath);
}

$left_show_single = false;
if (basename($_SERVER['PHP_SELF']) == FILENAME_PREORDER) {
  $left_products_id = tep_preorder_get_products_id_by_param();
  $left_ca_path = tep_get_product_path($left_products_id);
  if (tep_not_null($left_ca_path)) {
    $id = tep_parse_category_path($left_ca_path); 
    $ca_arr = $id; 
  }
  $left_show_single = true;
} else if (basename($_SERVER['PHP_SELF']) == FILENAME_PREORDER_PAYMENT) {
  $left_ca_path = tep_get_product_path($_POST['products_id']);
  if (tep_not_null($left_ca_path)) {
    $id = tep_parse_category_path($left_ca_path); 
    $ca_arr = $id; 
  }
  $left_show_single = true;
} else if (basename($_SERVER['PHP_SELF']) == FILENAME_PREORDER_SUCCESS) {
  $left_preorder_product_raw = tep_db_query("select products_id from ".TABLE_PREORDERS_PRODUCTS." where orders_id = '".$_SESSION['send_preorder_id']."'"); 
  $left_preorder_product = tep_db_fetch_array($left_preorder_product_raw);
  $left_ca_path = tep_get_product_path($left_preorder_product['products_id']);
  if (tep_not_null($left_ca_path)) {
    $id = tep_parse_category_path($left_ca_path); 
    $ca_arr = $id; 
  }
  $left_show_single = true;
}
?>

<div id='categories'>
  <div class="menu_top"><img src="images/menu_ico.gif" alt="" align="top">&nbsp;MENU</div>
  <ul class='l_m_category_ul'>
    <?php foreach($categories as $key => $category) {?>
      <?php if(($cPath && in_array($category['categories_id'], $id)) || ($left_show_single && in_array($category['categories_id'], $id))) {?>
        <li class='l_m_category_li2'>
          <a class="l_m_category_li2_link"href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']);?>">
            <?php if (in_array($category['categories_id'], $id)) {?>
              <strong> 
            <?php }?>
            <?php echo $category['categories_name'];?>
            <?php if (in_array($category['categories_id'], $id)) {?>
              </strong> 
            <?php }?>
          </a>
        <?php
          $subcategories = array();
          // ccdd
          $subcategories_query = tep_db_query("
              select *
              from (
                select c.categories_id, 
                       cd.categories_status, 
                       cd.categories_name, 
                       c.parent_id,
                       cd.site_id,
                       c.sort_order
                from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                where c.parent_id = '".$category['categories_id']."' 
                  and c.categories_id = cd.categories_id 
                  and cd.language_id='" . $languages_id ."' 
                order by cd.site_id DESC
                ) c
              where site_id = 0
                 or site_id = ".SITE_ID."
              group by categories_id
              having c.categories_status != '1' and c.categories_status != '3' 
              order by sort_order, categories_name
              ");
         while ($subcategory = tep_db_fetch_array($subcategories_query))  {
            $subcategories[] = $subcategory;
          }
          ?>
          <ul class='l_m_category_ul2'>
            <li></li>
          <?php foreach($subcategories as $skey =>  $subcategory){?>
            <?php if(($cPath && in_array($subcategory['categories_id'], $id)) || ($left_show_single && in_array($subcategory['categories_id'], $id))) {?>
              <li class='l_m_categories_tree'>
                <?php if($skey == (count($subcategories)-1)){?>
                  <img class="middle" src="images/design/tree_end.gif" width="7" height="8" alt="">
                <?php } else {?>
                  <img class="middle" src="images/design/tree_icon.gif" width="7" height="8" alt="">
                <?php }?>
                <?php if (in_array($subcategory['categories_id'], $id)) {?>
                  <strong>
                <?php }?>
                <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id'].'_'.$subcategory['categories_id']);?>">
                  <?php echo $subcategory['categories_name'];?>
                </a>
                <?php if (in_array($subcategory['categories_id'], $id)) {?>
                  </strong>
                <?php }?>
        <?php
            $_subcategories = array();
            $_subcategories_query = tep_db_query("
                select *
                from (
                  select c.categories_id, 
                         cd.categories_status, 
                         cd.categories_name, 
                         c.parent_id,
                         cd.site_id,
                         c.sort_order
                  from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                  where c.parent_id = '".$subcategory['categories_id']."' 
                    and c.categories_id = cd.categories_id 
                    and cd.language_id='" . $languages_id ."' 
                  order by cd.site_id DESC
                ) c
                where site_id = 0
                   or site_id = ".SITE_ID."
                group by categories_id
                having c.categories_status != '1' and c.categories_status != '3' 
                order by sort_order, categories_name
            ");
            while ($_subcategory = tep_db_fetch_array($_subcategories_query))  {
              $_subcategories[] = $_subcategory;
            }
            ?>
            <?php if($_subcategories){?>
            <ul class='l_m_category_ul3'>
            <?php foreach($_subcategories as $_skey => $_subcategory){?>
                <li class='l_m_categories_tree3'>
                  <?php if($_skey == (count($_subcategories)-1)){?>
                    <img class="middle" src="images/design/tree_end.gif" width="7" height="8" alt="">
                  <?php } else {?>
                    <img class="middle" src="images/design/tree_icon.gif" width="7" height="8" alt="">
                  <?php }?>
                  <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id'].'_'.$subcategory['categories_id'].'_'.$_subcategory['categories_id']);?>">
                    <?php if (in_array($_subcategory['categories_id'], $id)) {?>
                      <strong>
                    <?php }?>
                      <?php echo $_subcategory['categories_name'];?>
                    <?php if (in_array($_subcategory['categories_id'], $id)) {?>
                      </strong>
                    <?php }?>
                  </a>
                </li>
            <?php }?>
            </ul>
            <?php }?>
          </li>
            <?php } else {?>
              <li class='l_m_categories_tree'>
                <?php if($skey == (count($subcategories)-1)){?>
                  <img class="middle" src="images/design/tree_end.gif" width="7" height="8" alt="">
                <?php } else {?>
                  <img class="middle" src="images/design/tree_icon.gif" width="7" height="8" alt="">
                <?php }?>
                <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id'].'_'.$subcategory['categories_id']);?>"><?php echo $subcategory['categories_name'];?></a>
              </li>
            <?php }?>
          <?}?>
          </ul>
      <?php } else {?>
                <?php
                if (!isset($ca_arr)) {
                ?>
                <li class='l_m_category_li'><a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']);?>">
                <?php echo $category['categories_name'];?> 
                </a></li>
                <?php
                } else if (in_array($category['categories_id'], $ca_arr)) { 
                ?>
                <li class='l_m_category_li'><a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']);?>">
                <?php 
                echo $category['categories_name']; 
                ?>
        </a></li>
      <?php }?>
      <?php }?>
    <?php }?>
        </ul>
  <div class="categories_bottom"><img src="images/design/box/box_bottom_bg_01.gif" width="170" height="14" alt="" ></div>
</div>
<!-- categories_eof -->
