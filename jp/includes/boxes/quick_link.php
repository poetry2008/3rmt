<?php
/*
   2009-4-15
   haomai maker

*/
?>
<!-- quick_link //-->
<?php if (!(isset($cPath) && tep_not_null($cPath))) {?>
            <?php if (isset($_COOKIE['quick_categories_id'])) {?>
              <?php
                $sql = "select * from " . TABLE_CATEGORIES . " left join categories_description on categories.categories_id = categories_description.categories_id where categories.categories_id = '" . $_COOKIE['quick_categories_id'] . "' and categories_description.site_id =".SITE_ID;
                $quick_category_query = tep_db_query($sql);
                $quick_category = tep_db_fetch_array($quick_category_query);
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
