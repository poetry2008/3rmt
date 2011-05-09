<?php
?>
<div>
<img src="images/design/box/new_title01.gif" width="172" height="51" alt="こだわり検索" >
<ul class="l_m_category_ul">
    <li class="l_m_category_li">
      <a href="<?php echo tep_href_link(FILENAME_SPECIALS); ?>"><?php echo BOX_HEADING_SPECIALS; ?></a>
    </li>
<?php
// ccdd
  $present_left_query = tep_db_query("
      select count(*) as cnt 
      from " . TABLE_PRESENT_GOODS . "
      where site_id = '".SITE_ID."'
  ");
  $present_left_result = tep_db_fetch_array($present_left_query);
  if($present_left_result['cnt'] > 0) {
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
