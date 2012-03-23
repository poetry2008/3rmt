<?php
/*
  $Id$
*/
  require('includes/application_top.php');

  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'add_more_alarm':
        $now_year = date('Y', time());   
        $now_month = date('m', time());   
        $now_day = date('d', time());   
        $now_hour = date('H', time());   
        $now_minute = date('i', time());   
        
        $str = '<span>'; 
        $str .= '<table>'; 
        $str .= '<tr><td>';
        $str .= ALARM_TITLE_TEXT.'</td><td>';
        $str .= tep_draw_input_field('new_arr[title][]', $_POST['oid'], 'size="45"'); 
        $str .= '<font color="#ff0000">'.ALARM_REQUIRE_TEXT.'</font>'; 
        $str .= '</td></tr>'; 
        $str .= '<tr><td>'; 
        $str .= ALARM_SET_TIME_TEXT;
        $str .= '</td><td>';
        $str .= '<select name="new_arr[year][]">'; 
        for($i = 2012; $i <= 2050; $i++) {
          $str .= '<option value="'.$i.'" '.(($now_year == $i)?'selected':'').'>'.$i.YEAR_TEXT.'</option>'; 
        } 
        $str .= '</select>&nbsp;'; 
        
        $str .= '<select name="new_arr[month][]">'; 
        for($j = 1; $j <= 12; $j++) {
          $str .= '<option value="'.$j.'" '.(($now_month == $j)?'selected':'').'>'.sprintf('%02d', $j).MONTH_TEXT.'</option>'; 
        } 
        $str .= '</select>&nbsp;'; 
        
        $str .= '<select name="new_arr[day][]">'; 
        for($k = 1; $k <= 31; $k++) {
          $str .= '<option value="'.$k.'" '.(($now_day == $k)?'selected':'').'>'.sprintf('%02d', $k).DAY_TEXT.'</option>'; 
        } 
        $str .= '</select>&nbsp;'; 
        
        $str .= '<select name="new_arr[hour][]">'; 
        for($h = 0; $h <= 23; $h++) {
          $str .= '<option value="'.$h.'" '.(($now_hour == $h)?'selected':'').'>'.sprintf('%02d', $h).HOUR_TEXT.'</option>'; 
        } 
        $str .= '</select>&nbsp;'; 
        
        $str .= '<select name="new_arr[minute][]">'; 
        for($m = 0; $m <= 59; $m++) {
          $str .= '<option value="'.$m.'" '.(($now_minute == $m)?'selected':'').'>'.sprintf('%02d', $m).MINUTE_TEXT.'</option>'; 
        } 
        $str .= '</select>'; 
        $str .= '<font color="#ff0000">'.ALARM_REQUIRE_TEXT.'</font>'; 
        
        $str .= '</td></tr>'; 
        $str .= '</table>';
        $str .= '<a href="javascript:void(0);">'.tep_html_element_button(IMAGE_DELETE, 'onclick="delete_add_line(this);"').'</a>';
        $str .= '</span>'; 
        $str .= '<br>'; 
        echo $str;
        exit;
        break;
      case 'process':
        if (!empty($_POST['new_arr'])) {
          foreach ($_POST['new_arr']['title'] as $n_key => $n_value) {
            $alarm_date = @mktime($_POST['new_arr']['hour'][$n_key], $_POST['new_arr']['minute'][$n_key], 0, $_POST['new_arr']['month'][$n_key], $_POST['new_arr']['day'][$n_key], $_POST['new_arr']['year'][$n_key]);  
            $sql_data_array = array(
                 'title' => $n_value,
                 'orders_id' => $_GET['oID'], 
                 'alarm_date' => date('Y-m-d H:i:00', $alarm_date),
                 'adminuser' => $ocertify->auth_user,
                 'created_at' => 'now()',
                 );  
            tep_db_perform(TABLE_ALARM, $sql_data_array);         
            
            $alarm_id = tep_db_insert_id();
            
            $sql_data_array = array(
                'type' => 0,
                'title' => $n_value,
                'set_time' => date('Y-m-d H:i:00', $alarm_date),
                'from_notice' => $alarm_id,
                'user' => $ocertify->auth_user,
                'created_at' => 'now()'
                ); 
            tep_db_perform(TABLE_NOTICE, $sql_data_array);         
          }
        }
        
        if (!empty($_POST['update_arr'])) {
          foreach ($_POST['update_arr'] as $u_key => $u_value) {
            $alarm_date = @mktime($u_value['hour'], $u_value['minute'], 0, $u_value['month'], $u_value['day'], $u_value['year']);  
            
            $update_alarm_sql = "update `".TABLE_ALARM."` set `title` = '".$u_value['title']."', `alarm_date` = '".date('Y-m-d H:i:00', $alarm_date)."' where `alarm_id` = '".$u_key."'"; 
            tep_db_query($update_alarm_sql); 
            
            $update_notice_sql = "update `".TABLE_NOTICE."` set `title` = '".$u_value['title']."', `set_time` = '".date('Y-m-d H:i:00', $alarm_date)."' where `type` = '0' and `from_notice` = '".$u_key."'";
            tep_db_query($update_notice_sql); 
          }
        }
        
        tep_redirect(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))));
        break;
      case 'check_alarm':
        if (!empty($_POST['new_arr'])) {
          foreach ($_POST['new_arr']['title'] as $n_key => $n_value) {
            if ($n_value == '') {
               echo ALARM_NAME_IS_NULL;
               exit;
            } else {
              if (!checkdate($_POST['new_arr']['month'][$n_key], $_POST['new_arr']['day'][$n_key], $_POST['new_arr']['year'][$n_key])) {
                echo ALARM_SET_TIME_IS_WRONG;
                exit;
              }
            }
          }
        }
        
        if (!empty($_POST['update_arr'])) {
          foreach ($_POST['update_arr'] as $u_key => $u_value) {
            if ($u_value['title'] == '') {
              echo ALARM_NAME_IS_NULL;
              exit;
            } else {
              if (!checkdate($u_value['month'], $u_value['day'], $u_value['year'])) {
                echo ALARM_SET_TIME_IS_WRONG;
                exit;
              }
            }
          } 
        } 
        
        echo 'success';
        exit;
        break;
      case 'delete':
        break; 
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script type="text/javascript">
function add_more_alarm()
{
  $.ajax({
    url: 'alarm.php?action=add_more_alarm',     
    type: 'POST',
    data: 'oid='+'<?php echo $_GET['oID'];?>', 
    dataType: 'text',
    async:false,
    success: function(data) {
      $('#add_alarm').append(data);   
    }
  });
}
function check_alarm()
{
   param_str = ''; 
   
   $("#alarm_list").find("input").each(function() {
       param_str += $(this).attr('name')+'='+$(this).val()+'&';
   }); 
   
   $("#alarm_list").find("select").each(function() {
       param_str += $(this).attr('name')+'='+$(this).val()+'&';
   });
   
   pos_str = param_str.lastIndexOf('&') 
   
   $.ajax({
       url: 'alarm.php?action=check_alarm',
       data: param_str.substring(0, pos_str), 
       type: 'POST',
       dataType: 'text', 
       async:false,
       success: function(msg) {
         if (msg == 'success') {
           document.forms.alarm.submit(); 
         } else {
           alert(msg); 
         }
       }
       });
}

function delete_add_line(obj) 
{
  $(obj).parent().parent().html('');
}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo $_GET['oID'].'&nbsp;'.ALARM_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <?php
        echo tep_draw_form('alarm', FILENAME_ALARM, '&action=process&'.tep_get_all_get_params(array('action'))); 
        ?>
        <div id="alarm_list"> 
        <?php
        $alarm_raw = tep_db_query("select * from ".TABLE_ALARM." where adminuser = '".$ocertify->auth_user."' and orders_id = '".$_GET['oID']."' order by created_at desc"); 
        while ($alarm = tep_db_fetch_array($alarm_raw)) {
        ?>
        <table>
          <tr>
            <td>
            <?php echo ALARM_TITLE_TEXT;?> 
            </td>
            <td>
            <?php echo
            tep_draw_input_field('update_arr['.$alarm['alarm_id'].'][title]', $alarm['title'], 'size="45"');?>            
            <font color="#ff0000"><?php echo ALARM_REQUIRE_TEXT;?></font> 
            </td>
          </tr>
          <tr>
            <td>
            <?php echo ALARM_SET_TIME_TEXT;?> 
            </td>
            <td>
            <?php
            $alarm_data = explode(' ', $alarm['alarm_date']);
            $alarm_date_info = explode('-', $alarm_data[0]);
            $alarm_time_info = explode(':', $alarm_data[1]);
            $alarm_year = $alarm_date_info[0]; 
            $alarm_month = $alarm_date_info[1]; 
            $alarm_day = $alarm_date_info[2]; 
            $alarm_hour = $alarm_time_info[0]; 
            $alarm_minute = $alarm_time_info[1]; 
            ?>
            <select name="update_arr[<?php echo $alarm['alarm_id'];?>][year]"> 
            <?php 
            for($i = 2012; $i <= 2050; $i++) {
            ?>
            <option value="<?php echo $i;?>" <?php echo ($alarm_year == $i)?'selected':'';?>><?php echo $i.YEAR_TEXT;?></option> 
            <?php
            }
            ?>
            </select> 
            <select name="update_arr[<?php echo $alarm['alarm_id'];?>][month]"> 
            <?php 
            for($j = 1; $j <= 12; $j++) {
            ?>
            <option value="<?php echo $j;?>" <?php echo ($alarm_month == $j)?'selected':'';?>><?php echo sprintf('%02d',$j).MONTH_TEXT;?></option> 
            <?php
            }
            ?>
            </select>
            <select name="update_arr[<?php echo $alarm['alarm_id'];?>][day]"> 
            <?php 
            for($k = 1; $k <= 31; $k++) {
            ?>
            <option value="<?php echo $k;?>" <?php echo ($alarm_day == $k)?'selected':'';?>><?php echo sprintf('%02d', $k).DAY_TEXT;?></option> 
            <?php
            }
            ?>
            </select>
            <select name="update_arr[<?php echo $alarm['alarm_id'];?>][hour]"> 
            <?php 
            for($h = 0; $h <= 23; $h++) {
            ?>
            <option value="<?php echo $h;?>" <?php echo ($alarm_hour == $h)?'selected':'';?>><?php echo sprintf('%02d', $h).HOUR_TEXT;?></option> 
            <?php
            }
            ?>
            </select>
            <select name="update_arr[<?php echo $alarm['alarm_id'];?>][minute]"> 
            <?php 
            for($m = 0; $m <= 59; $m++) {
            ?>
            <option value="<?php echo $m;?>" <?php echo ($alarm_minute == $m)?'selected':'';?>><?php echo sprintf('%02d', $m).MINUTE_TEXT;?></option> 
            <?php
            }
            ?>
            </select>
            <font color="#ff0000"><?php echo ALARM_REQUIRE_TEXT;?></font> 
            </td>
          </tr>
        </table>
        <br>
        <?php
        }
        ?>
        <div id="add_alarm">
        <?php if (!tep_db_num_rows($alarm_raw)) {?> 
        <?php
        $now_year = date('Y', time());   
        $now_month = date('m', time());   
        $now_day = date('d', time());   
        $now_hour = date('H', time());   
        $now_minute = date('i', time());   
        ?>
          <table> 
          <tr>
            <td>
            <?php echo ALARM_TITLE_TEXT;?> 
            </td>
            <td>
            <?php echo tep_draw_input_field('new_arr[title][]', $_GET['oID'], 'size="45"');?>            
            <font color="#ff0000"><?php echo ALARM_REQUIRE_TEXT;?></font> 
            </td>
          </tr>
          <tr>
            <td>
            <?php echo ALARM_SET_TIME_TEXT;?> 
            </td>
            <td>
            <select name="new_arr[year][]"> 
            <?php 
            for($i = 2012; $i <= 2050; $i++) {
            ?>
            <option value="<?php echo $i;?>" <?php echo ($now_year == $i)?'selected':'';?>><?php echo $i.YEAR_TEXT;?></option> 
            <?php
            }
            ?>
            </select> 
            <select name="new_arr[month][]"> 
            <?php 
            for($j = 1; $j <= 12; $j++) {
            ?>
            <option value="<?php echo $j;?>" <?php echo ($now_month == $j)?'selected':'';?>><?php echo sprintf('%02d',$j).MONTH_TEXT;?></option> 
            <?php
            }
            ?>
            </select>
            <select name="new_arr[day][]"> 
            <?php 
            for($k = 1; $k <= 31; $k++) {
            ?>
            <option value="<?php echo $k;?>" <?php echo ($now_day == $k)?'selected':'';?>><?php echo sprintf('%02d', $k).DAY_TEXT;?></option> 
            <?php
            }
            ?>
            </select>
            <select name="new_arr[hour][]"> 
            <?php 
            for($h = 0; $h <= 23; $h++) {
            ?>
            <option value="<?php echo $h;?>" <?php echo ($now_hour == $h)?'selected':'';?>><?php echo sprintf('%02d', $h).HOUR_TEXT;?></option> 
            <?php
            }
            ?>
            </select>
            <select name="new_arr[][minute]"> 
            <?php 
            for($m = 0; $m <= 59; $m++) {
            ?>
            <option value="<?php echo $m;?>" <?php echo ($now_minute == $m)?'selected':'';?>><?php echo sprintf('%02d', $m).MINUTE_TEXT;?></option> 
            <?php
            }
            ?>
            </select>
            <font color="#ff0000"><?php echo ALARM_REQUIRE_TEXT;?></font> 
            </td>
          </tr>
          </table> 
          <br> 
        <?php }?> 
        </div>
        </div>
        <div class="element_add"><a href="javascript:void(0);"><?php echo tep_html_element_button(ADD_OTHER_ALARM, 'onclick="add_more_alarm();"');?></a></div>
        <div class="check_button"><a href="javascript:void(0);"><?php echo tep_html_element_button(IMAGE_SAVE, 'onclick="check_alarm();"');?></a> 
        <a href="<?php echo tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action')));?>"><?php echo tep_html_element_button(IMAGE_BACK);?></a></div> 
        </form> 
        </td> 
      </tr>
     </table> 
    </td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
