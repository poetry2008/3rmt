<?php
/*
  $Id$
*/
?>
<!-- bestlinks //-->
<script src="js/blank.js" type="text/javascript"></script>
<div class="boxText ad">
  <img src="images/design/box/bestlinks.gif" alt="公式 攻略 相場 Wikiなどリンク集" width="171" height="42">
<?php
  if (isset($current_category_id) && ($current_category_id > 0)) {
    $g_id = $current_category_id;
    //if ($g_id == "168" || $g_id == "169" || $g_id == "170" || $g_id == "171" || $g_id == "177" || $g_id == "178" || $g_id == "179" || $g_id == "190" || $g_id == "195" || $g_id == "200" || $g_id == "203" || $g_id == "206" || $g_id == "209" || $g_id == "212") {
    if (file_exists('includes/modules/ad/' . (int)$g_id . '.php')) {
      include('includes/modules/ad/' . (int)$g_id . '.php');
    } else {
      include('includes/modules/ad/main.php');
    }
  } else {
    include('includes/modules/ad/main.php');
  }
?>
<img src="images/design/box/bestlinks_bottom_bg.gif" width="171" height="10" alt="" >
</div>
<!-- bestlinks_eof //-->
