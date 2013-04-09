<?php
/*
  $Id$
*/
?>
<!-- configuration -->
          <tr>
            <td>
            <table cellspacing="0" cellpadding="2" border="0" width="150"> 
              <tr>
              <td onclick="toggle_lan('col1');" onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">&nbsp;<span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_settings.gif'); ?></span><span>
              <a class="menuBoxHeading_Link" href="javascript:void(0);"><?php echo BOX_HEADING_CONFIGURATION;?></a></span>&nbsp; 
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
$configuration_groups_img = array("901"=>"icon_shop_settings.gif",
	"16" =>"icon_time.gif",
	"1"  =>"icon_shop_information.gif",
	"2"  =>"icon_minimum.gif",
	"3"  =>"icon_maximum.gif",
	"18" =>"icon_dealing.gif",
	"19" =>"icon_review.gif",
	"5"  =>"icon_account.gif",
	"7"  =>"icon_packing.gif",
	"9"  =>"icon_inventory.gif",
	"10" =>"icon_record.gif",
	"11" =>"icon_cache.gif",
	"12" =>"icon_email.gif",
	"13" =>"icon_download_sale.gif",
	"14" =>"icon_compression.gif",
	"15" =>"icon_session.gif",
	"100"=>"icon_safety.gif",
	"900"=>"icon_affiliate.gif",
	"30" =>"icon_warning.gif",
	"2031"=>"icon_order_info.gif",
        "4"  =>"icon_img.gif"
);
                $configuration_groups_query = tep_db_query("select configuration_group_id as cgID, configuration_group_title as cgTitle from " . TABLE_CONFIGURATION_GROUP . " where visible = '1' order by sort_order");
                while ($configuration_groups = tep_db_fetch_array($configuration_groups_query)) {
                 if(str_replace('/admin/','',$_SERVER['PHP_SELF']).'?gID='.$_GET['gID'] == FILENAME_CONFIGURATION.'?gID='.$configuration_groups['cgID']){
                   echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_CONFIGURATION, 'gID=' .  $configuration_groups['cgID'], 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON.$configuration_groups_img[$configuration_groups['cgID']]).'</span><span>';
                  if(constant($configuration_groups['cgTitle'])){
                    echo constant($configuration_groups['cgTitle']);
                  }else{
                    echo $configuration_groups['cgTitle']; 
                  }
                  echo '</span></div>';
                 }else{
                  echo '<div onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_CONFIGURATION, 'gID=' .  $configuration_groups['cgID'], 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON.$configuration_groups_img[$configuration_groups['cgID']]).'</span><span>';
                  if(constant($configuration_groups['cgTitle'])){
                    echo constant($configuration_groups['cgTitle']);
                  }else{
                    echo $configuration_groups['cgTitle']; 
                  }
                  echo '</span></div>';
                 }
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
<!-- configuration_eof -->
