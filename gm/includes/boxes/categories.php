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
             cd.categories_status, 
             c.parent_id,
             cd.site_id,
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
?>
<div id='box'>
  <ul class='l_m_category_ul'>
    <?php foreach($categories as $key => $category) {?>
        <li class='l_m_category_li'><a  class='l_m_category_a' href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$category['categories_id']);?>"><?php echo $category['categories_name'];?></a></li>
    <?php }?>
   </ul>
</div>

<!-- categories_eof //-->
