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
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
              <td onmouseover="this.style.cursor='hand'" class="menuBoxHeading">
              &nbsp; 
              <a class="menuBoxHeading_Link" href="<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('selected_box')) .  'selected_box=configuration');?>"><?php echo BOX_HEADING_CONFIGURATION;?></a> 
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php  
            if ($selected_box == 'configuration') {
            ?> 
            <div id="col1"> 
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
                <?php 
                $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");
                while ($configuration_groups = tep_db_fetch_array($configuration_groups_query)) {
                  echo '<a '.(($_GET['gID'] == $configuration_groups['cgID'])?'class="s_column_bar" ':'class="menuBoxContent_Link" ').'href="' . tep_href_link(FILENAME_CONFIGURATION, 'gID=' . $configuration_groups['cgID'], 'NONSSL') . '">' . $configuration_groups['cgTitle'] . '</a><br>';
                ?>
                <?php
                }
                ?> 
                </td>
              </tr>
            </table> 
            </div> 
            <?php }?> 
            </td>
          </tr>
<!-- configuration_eof //-->
