<?php
/*
  $Id$
*/
if ($cPath) {
  $menu_ca_arr = explode('_', $cPath);
}
$menu_categories = array();
// ccdd
$menu_categories_query = tep_db_query("
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
while ($menu_category = tep_db_fetch_array($menu_categories_query))  {
  $menu_categories[] = $menu_category;
}
if($cPath){
  $menu_id = split('_', $cPath);
}
?>
<!-- search-->
<div id="search">
<div class="menu_top"><span>MENU</span></div>
<div id='categories'>
  <ul class='l_m_category_ul'>
    <?php foreach($menu_categories as $me_key => $menu_category) {?>
        <li class='l_m_category_li'>
          <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath='.$menu_category['categories_id']);?>">
            <?php echo $menu_category['categories_name'];?>
          </a>
        </li>
      <?php }?>   
      </ul>
</div>
<!-- categories_eof //-->
</div>
