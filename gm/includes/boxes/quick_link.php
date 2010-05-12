<?php
/*
  $Id$

*/
?>
<!-- quick_link //-->
<?php if (!(isset($cPath) && tep_not_null($cPath))) {?>
            <?php if (isset($_COOKIE['quick_categories_id'])) {?>
              <?php
                $quick_category = tep_get_category_by_id($_COOKIE['quick_categories_id'], SITE_ID, $languages_id);
              ?>
            <?php }?>
<div id='quick_link'>
              <a href='<?php echo tep_href_link(FILENAME_DEFAULT, 'action=select');?>' title='マイゲーム'><img src="images/design/box/my_game.gif" width="172" height="107" alt="マイゲーム" ></a>
            <?php if (isset($quick_category) && $quick_category && !$quick_category['categories_status']){?>
                <a href='<?php echo tep_href_link(FILENAME_DEFAULT, 'cPath=' . $quick_category['categories_id']);?>' title="<?php echo $quick_category['categories_name'];?>"><img src='/images/<?php echo $quick_category['categories_image2'];?>' width="<?php echo CATEGORY_IMAGE_WIDTH;?>" height="<?php echo CATEGORY_IMAGE_HEIGHT;?>" alt="<?php echo $quick_category['categories_name'];?>" ></a>
            <?php } ?>
</div>
<?php }?>
<!-- quick_link_eof //-->
