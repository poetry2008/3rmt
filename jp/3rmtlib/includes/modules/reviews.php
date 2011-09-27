<?php
/*
  $Id$
*/
?>
<table class="box_des" border="0" cellspacing="0" cellpadding="2">
<?php
  if (sizeof($reviews_array) < 1) {
?>
  <tr>
    <td class="main"><?php echo TEXT_NO_REVIEWS; ?></td>
  </tr>
<?php
  } else {
    for($i=0, $n=sizeof($reviews_array); $i<$n; $i++) {
?>
  <tr>
    <td valign="top" class="main">
    <?php 
    if ($reviews_array[$i]['products_status'] == 0 || $reviews_array[$i]['products_status'] == 3) {
      echo tep_image(DIR_WS_IMAGES . $reviews_array[$i]['products_image'], $reviews_array[$i]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); 
    } else {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $reviews_array[$i]['products_id'] . '&reviews_id=' . $reviews_array[$i]['reviews_id']) . '">' . tep_image(DIR_WS_IMAGES . $reviews_array[$i]['products_image'], $reviews_array[$i]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>'; 
    }
    ?>
    </td>
    <td valign="top" class="main">
    <?php 
    if ($reviews_array[$i]['products_status'] == 0 || $reviews_array[$i]['products_status'] == 3) {
      if ($reviews_array[$i]['products_status'] != 3) {
        echo '<b>' .  $reviews_array[$i]['products_name'] . '</b>';
      }
    } else {
      echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' .  $reviews_array[$i]['products_id'] . '&reviews_id=' .  $reviews_array[$i]['reviews_id']) . '"><b><u>' .  $reviews_array[$i]['products_name'] . '</u></b></a>';
    }
    if ($reviews_array[$i]['products_status'] == 3) {
      echo '&nbsp;&nbsp;<b>'.BLACK_REVIEWS_NOTICE.'</b><br>'; 
      echo sprintf(TEXT_REVIEW_BY, $reviews_array[$i]['authors_name']) . '<br>';
    } else {
      echo '&nbsp;&nbsp;' . sprintf(TEXT_REVIEW_BY, $reviews_array[$i]['authors_name']) . '<br>';
    }
    echo $reviews_array[$i]['review'] . '<br><br><i>' . sprintf(TEXT_REVIEW_RATING, tep_image(DIR_WS_IMAGES . 'stars_' . $reviews_array[$i]['rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews_array[$i]['rating'])), sprintf(TEXT_OF_5_STARS, $reviews_array[$i]['rating'])) . '</i>'; 
    ?>
    </td>
  </tr>
<?php
      if (($i+1) != $n) {
?>
  <tr>
    <td colspan="2" class="main">&nbsp;</td>
  </tr>
<?php
      }
    }
  }
?>
</table>
