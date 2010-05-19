<?php
/*
  $Id$
*/

$categories = array();
// ccdd
$categories_query = tep_db_query("
    select * 
    from (
      select c.categories_id, 
             cd.categories_name, 
             c.categories_status, 
             c.parent_id,
             cd.site_id,
             c.sort_order
      from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
      where c.categories_status = '0' 
        and c.parent_id = '0' 
        and c.categories_id = cd.categories_id 
        and cd.language_id='" . $languages_id ."' 
      order by sort_order, cd.categories_name, site_id DESC
    ) c 
    where site_id = ".SITE_ID."
       or site_id = 0
    group by categories_id
    order by sort_order, categories_name
");
while ($category = tep_db_fetch_array($categories_query))  {
  $categories[] = $category;
}
if($cPath){
  $id = split('_', $cPath);
}
?>

<div id='categories'>
  <img width="172" height="51" alt="RMT 価格" title="RMT 価格" src="images/design/box/menu.gif">
  <ul class='l_m_category_ul'>
    <?php foreach($categories as $key => $category) {?>
      <?php if($cPath && in_array($category['categories_id'], $id)) {?>
        <li class='l_m_category_li2'>
          <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']);?>">
            <?php if (in_array($category['categories_id'], $id)) {?>
              <strong>
            <?php }?>
            <?php echo str_replace(' RMT', '', $category['categories_name']);?>
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
                       c.categories_status, 
                       cd.categories_name, 
                       c.parent_id,
                       cd.site_id,
                       c.sort_order
                from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                where c.categories_status = '0' 
                  and c.parent_id = '".$category['categories_id']."' 
                  and c.categories_id = cd.categories_id 
                  and cd.language_id='" . $languages_id ."' 
                order by sort_order, cd.categories_name, cd.site_id DESC
                ) c
              where site_id = 0
                 or site_id = ".SITE_ID."
              group by categories_id
              order by sort_order, categories_name
              ");
         while ($subcategory = tep_db_fetch_array($subcategories_query))  {
            $subcategories[] = $subcategory;
          }
          ?>
          <ul class='l_m_category_ul2'>
          <?php foreach($subcategories as $skey =>  $subcategory){?>
            <?php if($cPath && in_array($subcategory['categories_id'], $id)) {?>
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
                         c.categories_status, 
                         cd.categories_name, 
                         c.parent_id,
                         cd.site_id,
                         c.sort_order
                  from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
                  where c.categories_status = '0' 
                    and c.parent_id = '".$subcategory['categories_id']."' 
                    and c.categories_id = cd.categories_id 
                    and cd.language_id='" . $languages_id ."' 
                  order by sort_order, cd.categories_name, cd.site_id DESC
                ) c
                where site_id = 0
                   or site_id = ".SITE_ID."
                group by categories_id
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
        <li class='l_m_category_li'><a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']);?>"><?php echo str_replace(' RMT', '', $category['categories_name']);?></a></li>
      <?php }?>
    <?php }?>


        </ul>
  <img src="images/design/box/box_bottom_bg_01.gif" width="172" height="14" alt="" >
</div>
<div>
<ul class="l_m_category_ul">
        <img src="images/design/box/new_title01.gif" width="172" height="51" alt="こだわり検索" >

    <li class="l_m_category_li">
      <a href="<?php echo tep_href_link(FILENAME_SPECIALS); ?>"><?php echo BOX_HEADING_SPECIALS; ?></a>
    </li>
<?php
// ccdd
  $present_query = tep_db_query("
      select count(*) as cnt 
      from " . TABLE_PRESENT_GOODS . "
      where site_id = '".SITE_ID."'
  ");
  $present_result = tep_db_fetch_array($present_query);
  if($present_result['cnt'] > 0) {
    echo '    <li class="l_m_category_li">
      
      <a href="' . tep_href_link(FILENAME_PRESENT) . '">' . BOX_HEADING_PRESENT . '</a>
    </li>' . "\n";
  }
?>
    <li class="l_m_category_li">
      <a href="<?php echo tep_href_link('manufacturers.php'); ?>"><?php echo MENU_MU; ?></a>
    </li>
    <li class="l_m_category_li">
      <a class='l_m_category_a' href="<?php echo tep_href_link('tags.php');?>">タグ一覧</a>
    </li>
  </ul>
  <img src="images/design/box/box_bottom_bg_01.gif" width="172" height="14" alt="" >
</div>

<!-- categories_eof //-->
