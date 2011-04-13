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
        and c.categories_id = '".FF_CID."' 
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
  $id = split('_', $cPath);
}
?>

<div id='categories'>
  <div class="menu_top"><?php echo $categories[0]['categories_name'];?></div>
  <?php 
    foreach ($categories as $key => $category) { 
  ?>
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
        <?php
        if (!empty($subcategories)) {
        ?>
          <ul class="l_m_category_ul">
        <?php
        foreach ($subcategories as $skey => $subcategory) {
        ?>
            <li class="l_m_category_li2">
              <a class="l_m_category_li2_link" href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id'].'_'.$subcategory['categories_id']);?>">
              <?php
              if (in_array($subcategory['categories_id'], $id)) {
              ?>
              <strong> 
              <?php
              }
              ?>
              <?php  echo $subcategory['categories_name'];?>
              <?php
              if (in_array($subcategory['categories_id'], $id)) {
              ?>
              </strong> 
              <?php
              }
              ?>
              </a> 
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
            <?php
            if (empty($_subcategories)) { 
            ?>
            <ul class="l_m_category_ul2"> 
            <?php
              foreach ($_subcategories as $_skey => $_subcategory) {
            ?>
              <li class="l_m_categories_tree">
              <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id'].'_'.$subcategory['categories_id'].'_'.$_subcategory['categories_id']);?>">
              <?php
              if (in_array($_subcategory['categories_id'], $id)) {
              ?>
              <strong> 
              <?php
              }
              ?>
              <?php  echo $_subcategory['categories_name'];?>
              <?php
              if (in_array($_subcategory['categories_id'], $id)) {
              ?>
              </strong> 
              <?php
              }
              ?>
              </a> 
              </li>
            <?php
              }
            ?>
            </ul> 
            <?php
            }
            ?>
            </li>
        <?php
          }
        ?>
        </ul>
        <?php
        }
        ?>
 <?php }?>
</div>
<!-- categories_eof //-->
