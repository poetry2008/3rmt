<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- configuration //-->
          <tr>
            <td>
<?php
  /* 
  $heading = array();
  $contents = array();

  $heading[] = array('text'  => BOX_HEADING_CONFIGURATION,
                     'link'  => tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) . 'selected_box=configuration'));

  if ($selected_box == 'configuration') {
    $cfg_groups = '';
    $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");
    while ($configuration_groups = tep_db_fetch_array($configuration_groups_query)) {
      $cfg_groups .= '<a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '" class="menuBoxContent_Link">' . $configuration_groups['cgTitle'] . '</a><br>';
    }
    //$cfg_groups .= '<a href="configuration_design.php" class="menuBoxContent_Link">デザイン設定</a>';
    $contents[] = array('text'  => $cfg_groups);
  }
 */
  //$box = new box;
  //echo $box->menuBox($heading, $contents);
?>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
							&nbsp; 
							<?php echo tep_image(DIR_WS_IMAGES . 'img/basic_setting.gif'); ?>						
							<a class="menuBoxHeading_Link" href="javascript:void(0);" onclick="toggle_lan('col1');"><?php echo BOX_HEADING_CONFIGURATION;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col1', $l_select_box_arr)) {
            ?>
            <div id="col1" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col1" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
<?php 
$configuration_groups_img = array();
$configuration_groups_img = array("901"=>"shop_set.gif",
	"16" =>"time.gif",
	"1"  =>"shop_news.gif",
	"2"  =>"minimize.gif",
	"3"  =>"maximize.gif",
	"18" =>"trade.gif",
	"19" =>"comments.gif",
	"5"  =>"account.gif",
	"7"  =>"icon_package_get.gif",
	"9"  =>"file_manager.gif",
	"10" =>"design_record.gif",
	"11" =>"cache.gif",
	"12" =>"email.gif",
	"13" =>"download.gif",
	"14" =>"compress.gif",
	"15" =>"section.gif",
	"100"=>"comment.gif",
	"900"=>"plan_add.gif",
	"30" =>"sign_warning.gif",
	"2031"=>"information.gif"
);
                $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");
                while ($configuration_groups = tep_db_fetch_array($configuration_groups_query)) {
                  echo '<span class="menuBoxContent_image">'.tep_image(DIR_WS_IMAGES . 'img/'.$configuration_groups_img[$configuration_groups['cgID']]).'</span><span class="menuBoxContent_span"><a href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '" class="menuBoxContent_Link">' . $configuration_groups['cgTitle'] . '</a></span><br>';
                ?>
                <?php
                }
                ?> 
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- configuration_eof //-->
