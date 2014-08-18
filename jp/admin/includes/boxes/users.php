<?php
// 文件名
define('FILENAME_USERS', 'users.php');
define('FILENAME_USERS_LOGINLOG', 'users_log.php');
define('FILENAME_ONCE_PWD_LOG', 'pwd_log.php');

  if (file_exists(DIR_WS_LANGUAGES . $language . '/boxes_users.php')) {
    include(DIR_WS_LANGUAGES . $language . '/boxes_users.php');
  }

?>

<!-- users -->
          <tr>
            <td>
<?php
?>
            <table cellspacing="0" cellpadding="2" border="0" class="leftTitle"> 
              <tr>
              <td onclick="toggle_lan('col8');" onmouseout="this.className='menusidebar'" onmouseover="this.className='menusidebarover';this.style.cursor='hand'" class="menusidebar" style="">
              &nbsp;
                 <span><?php echo tep_image(DIR_WS_MENU_ICON . 'icon_user.gif');?></span><span><a class="menuBoxHeading_Link" href="javascript:void(0);"><?php echo BOX_HEADING_USER;?></a></span>
              &nbsp; 
              </td>
              </tr>
            </table> 
            <?php
            if (in_array('col8', $l_select_box_arr)) {
            ?>
            <div id="col8" style="display:block"> 
            <?php
            } else {
            ?>
            <div id="col8" style="display:none"> 
            <?php
            }
            ?>
            <table cellspacing="0" cellpadding="2" border="0" width="100%"> 
              <tr>
                <td class="menuBoxContent">
    <?php  
        $loginlog = '<span>'.tep_image(DIR_WS_MENU_ICON . 'icon_access_log.gif').'</span><span>' . BOX_USER_LOG . '</span>';
        if (!check_whether_is_limited(FILENAME_USERS)) {
          if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_USERS){
            echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_USERS, '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_user_manage.gif').'</span><span>' . BOX_USER_ADMIN . '</span></div>';
          }else{
            echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_USERS, '', 'NONSSL').'\';"><span>'.tep_image(DIR_WS_MENU_ICON .  'icon_user_manage.gif').'</span><span>' . BOX_USER_ADMIN . '</span></div>';
          }
        }
        if (!check_whether_is_limited(FILENAME_PERSONAL_SETTING)) {
          if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_PERSONAL_SETTING){
            echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_PERSONAL_SETTING).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_personal_setting.gif').  '</span><span>'.HEADER_TEXT_PERSONAL_SETTING.'</span></div>'; 
          }else{
            echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_PERSONAL_SETTING).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_personal_setting.gif').  '</span><span>'.HEADER_TEXT_PERSONAL_SETTING.'</span></div>'; 
          }
        }
        if (!check_whether_is_limited(FILENAME_GROUPS)) {
          if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_GROUPS){
            echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_GROUPS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_groups.png').  '</span><span>'.HEADER_TEXT_GROUPS.'</span></div>'; 
          }else{
            echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_GROUPS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_groups.png').  '</span><span>'.HEADER_TEXT_GROUPS.'</span></div>'; 
          }
        }
        if (!check_whether_is_limited(FILENAME_ROSTER_RECORDS)) {
          if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ROSTER_RECORDS){
            echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_ROSTER_RECORDS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_roster_records.png').  '</span><span>'.ROSTER_TITLE_TEXT.'</span></div>'; 
          }else{
            echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_ROSTER_RECORDS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_roster_records.png').  '</span><span>'.ROSTER_TITLE_TEXT.'</span></div>'; 
          }
        }
        //计算工资
        //管理员可管理的组
        $admin_group_list_array = array();
        $admin_group_query = tep_db_query("select id,payrolls_admin from ".TABLE_GROUPS);
        while($admin_group_array = tep_db_fetch_array($admin_group_query)){

          if(trim($admin_group_array['payrolls_admin']) != ''){

            $payrolls_admin_array = explode('|||',$admin_group_array['payrolls_admin']);

            if(in_array($ocertify->auth_user,$payrolls_admin_array)){

              $admin_group_list_array[] = $admin_group_array['id'];  
            }
          }
        }
        tep_db_free_result($admin_group_query);
        if (!check_whether_is_limited(FILENAME_PAYROLLS) && (!empty($admin_group_list_array) || $ocertify->npermission == 31)) {
          if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_PAYROLLS){
            echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_PAYROLLS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_wage.gif').  '</span><span>'.HEADER_TEXT_PAYROLLS.'</span></div>'; 
          }else{
            echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_PAYROLLS).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_wage.gif').  '</span><span>'.HEADER_TEXT_PAYROLLS.'</span></div>'; 
          }
        }
        if (!check_whether_is_limited(FILENAME_ALERT_LOG)) {
          if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ALERT_LOG){
            echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_ALERT_LOG).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_alarm_log.gif').  '</span><span>'.HEADER_TEXT_ALERT_LOG.'</span></div>'; 
          }else{
            echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_ALERT_LOG).'\';"><span>' .  tep_image(DIR_WS_MENU_ICON .  'icon_alarm_log.gif').  '</span><span>'.HEADER_TEXT_ALERT_LOG.'</span></div>'; 
          }
        }
        if (!check_whether_is_limited(FILENAME_USERS_LOGINLOG)) {
          if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_USERS_LOGINLOG){
            echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_USERS_LOGINLOG, '', 'NONSSL').'\';">'.  $loginlog.'</div>';
          }else{
            echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_USERS_LOGINLOG, '', 'NONSSL').'\';">'.  $loginlog.'</div>';
          }
        }
        if (!check_whether_is_limited(FILENAME_ONCE_PWD_LOG)) {
          if(str_replace('/admin/','',$_SERVER['PHP_SELF']) == FILENAME_ONCE_PWD_LOG){
            echo '<div class="sidebarselected" onclick="window.location.href=\''.tep_href_link(FILENAME_ONCE_PWD_LOG).'\';">'."<span>".  tep_image(DIR_WS_MENU_ICON . 'icon_log.gif').  '</span><span>'.BOX_ONCE_PWD_LOG.'</span></div>';
          }else{
            echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(FILENAME_ONCE_PWD_LOG).'\';">'."<span>".  tep_image(DIR_WS_MENU_ICON . 'icon_log.gif').  '</span><span>'.BOX_ONCE_PWD_LOG.'</span> </div>';
          }
        }
        echo '<div style="" onmouseout="this.className=\'sidebar\'" onmouseover="this.className=\'sidebarover\';this.style.cursor=\'hand\'" class="sidebar" onclick="window.location.href=\''.tep_href_link(basename($PHP_SELF), 'execute_logout_user=1&num='.time(), 'NONSSL').'\'">'."<span>".  tep_image(DIR_WS_MENU_ICON . 'icon_logout.gif').  '</span><span>' . BOX_USER_LOGOUT .  '</span></div>';
    ?>
                </td>
              </tr>
            </table> 
            </div> 
            </td>
          </tr>
<!-- users -->
