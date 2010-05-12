<?php
/*
  $Id$
*/
?>
<!-- manufacturers //-->
              <table width="170"  border="0" cellpadding="0" cellspacing="0"> 
                <tr> 
                  <td height="40"><?php echo tep_image(DIR_WS_IMAGES.'design/menu_left/by_brand.gif',BOX_HEADING_MANUFACTURERS);?></td> 
                </tr> 
<?php
// ccdd
  $manufacturers_query = tep_db_query("
      select manufacturers_id, 
             manufacturers_name 
      from " . TABLE_MANUFACTURERS . " 
      order by manufacturers_name
  ");
  if (tep_db_num_rows($manufacturers_query) <= MAX_DISPLAY_MANUFACTURERS_IN_A_LIST) {
// Display a list
    $manufacturers_list = '';
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_name = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? mb_substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturers['manufacturers_name']);
      if (isset($_GET['manufacturers_id']) && ($_GET['manufacturers_id'] == $manufacturers['manufacturers_id'])) $manufacturers_name = '<b>' . $manufacturers_name .'</b>';
      $manufacturers_list .= '<tr><td height="20" class="c_menu"><h3><a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $manufacturers['manufacturers_id']) . '">' . $manufacturers_name . '</a></h3></td></tr>';
    }

   // $info_box_contents = array();
  //  $info_box_contents[] = array('text' => substr($manufacturers_list, 0, -4));
  echo $manufacturers_list ;
  
  
  } else {
// Display a drop-down
    $manufacturers_array = array();
    if (MAX_MANUFACTURERS_LIST < 2) {
      $manufacturers_array[] = array('id' => '', 'text' => PULL_DOWN_DEFAULT);
    }

    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_name = ((strlen($manufacturers['manufacturers_name']) > MAX_DISPLAY_MANUFACTURER_NAME_LEN) ? mb_substr($manufacturers['manufacturers_name'], 0, MAX_DISPLAY_MANUFACTURER_NAME_LEN) . '..' : $manufacturers['manufacturers_name']);
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'],
                                     'text' => $manufacturers_name);
    }

    $info_box_contents = array();
    $info_box_contents[] = array('form' => tep_draw_form('manufacturers', tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false), 'get'),
                                 'text' => tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $_GET['manufacturers_id'], 'onChange="this.form.submit();" size="' . MAX_MANUFACTURERS_LIST . '" style="width: 100%"') . tep_hide_session_id());
   echo '<tr><td>'."\n". 
       tep_draw_form('manufacturers', tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false), 'get','style="width:150px;"') ."\n".
       tep_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $_GET['manufacturers_id'], 'onChange="this.form.submit();" size="' . MAX_MANUFACTURERS_LIST . '" style="width: 100%"') . tep_hide_session_id()."\n".
       '</form></td></tr>';

 
  }
  
  //new infoBox($info_box_contents);
?>
    </table>
<!-- manufacturers_eof //-->
