<?php
/*
  $Id$
*/
  require('includes/application_top.php');

  if(isset($_GET['campaign_id'])&&$_GET['campaign_id']){
    $c_id = tep_db_prepare_input($_GET['campaign_id']);
  }else if(isset($_POST['campaign_id'])&&$_POST['campaign_id']){
    $c_id = tep_db_prepare_input($_POST['campaign_id']);
  }
  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'setflag';
        tep_db_query("update `".TABLE_CAMPAIGN."` set `status` = '".(int)$_GET['flag']."' where id = '".$_GET['campaign_id']."'");
        tep_redirect(tep_href_link(FILENAME_CAMPAIGN, isset($_GET['site_id'])?('site_id='.$_GET['site_id']):'')); 
        break;
      case 'update':
      case 'insert':
        $error = false;
        if (empty($_POST['title'])) {
          $error = true;
          $title_error = TEXT_CAMPAIGN_TITLE_IS_NULL;
        }
        if (empty($_POST['name'])) {
          $error = true;
          $name_error = TEXT_CAMPAIGN_NAME_IS_NULL;
        }
        
        if (empty($_POST['keyword'])) {
          $error = true;
          $keyword_error = TEXT_CAMPAIGN_KEYWORD_IS_NULL;
        } else {
          if (!preg_match('/^[0-9a-zA-Z]+$/', $_POST['keyword'])) {
            $error = true;
            $keyword_error = TEXT_CAMPAIGN_KEYWORD_IS_NULL;
          } else {
            if (preg_match('/^[0-9]+$/', $_POST['keyword'])) {
              $error = true;
              $keyword_error = TEXT_CAMPAIGN_KEYWORD_IS_NULL;
            } else {
              if ($_GET['action'] == 'update') {
                $exists_cam_raw = tep_db_query("select id from ".TABLE_CAMPAIGN." where keyword = '".$_POST['keyword']."' and id != '".$_POST['campaign_id']."'"); 
              } else {
                $exists_cam_raw = tep_db_query("select id from ".TABLE_CAMPAIGN." where keyword = '".$_POST['keyword']."'"); 
              }
              if (tep_db_num_rows($exists_cam_raw)) {
                $error = true;
                $keyword_error = TEXT_CAMPAIGN_KEYWORD_EXISTS;
              }
            }
          }
        }
        if (!preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $_POST['syear'].'-'.$_POST['smonth'].'-'.$_POST['sday']) || !preg_match('/^[\d]{4}-[\d]{1,2}-[\d]{1,2}$/', $_POST['eyear'].'-'.$_POST['emonth'].'-'.$_POST['eday'])) {
          $error = true;
          $date_error = TEXT_CAMPAIGN_DATE_WRONG;
        } else {
          $start_time = @strtotime($_POST['syear'].'-'.$_POST['smonth'].'-'.$_POST['sday'].' 00:00:00'); 
          $end_time = @strtotime($_POST['eyear'].'-'.$_POST['emonth'].'-'.$_POST['eday'].' 00:00:00'); 
          if ($start_time > $end_time) {
            $error = true;
            $date_error = TEXT_CAMPAIGN_DATE_WRONG;
          }
        }
        if (!$error) {
         if ($_POST['type'] == 2) {
           $limit_value = 0-$_POST['limit_value']; 
         } else {
           $limit_value = $_POST['limit_value']; 
         }
         $percent_pos = strpos($_POST['point_value'], '%');
         if ($percent_pos !== false) {
           $point_value = '-'.$_POST['point_value']; 
         } else {
           $point_value = 0-$_POST['point_value']; 
         }
         $sql_data_array = array(
            'title' => tep_db_prepare_input($_POST['title']),
            'name' => tep_db_prepare_input($_POST['name']),
            'keyword' => tep_db_prepare_input($_POST['keyword']),
            'is_preorder' => tep_db_prepare_input($_POST['is_preorder']),
            'start_date' => tep_db_prepare_input($_POST['syear'].'-'.$_POST['smonth'].'-'.$_POST['sday']),
            'end_date' => tep_db_prepare_input($_POST['eyear'].'-'.$_POST['emonth'].'-'.$_POST['eday']),
            'max_use' => tep_db_prepare_input($_POST['max_use']),
            'point_value' => tep_db_prepare_input($point_value),
            'limit_value' => tep_db_prepare_input($limit_value),
            'type' => tep_db_prepare_input($_POST['type']),
            );
         if($_GET['action']=='update'){
           tep_db_perform(TABLE_CAMPAIGN, $sql_data_array, 'update', 'id = \'' .  $_POST['campaign_id']. '\' and site_id = \''.(int)$_GET['site_id'].'\'');
        }else if($_GET['action']=='insert'){
          $insert_sql_data = array(
              'created_at' => 'now()',
              'status' => '1',
              'site_id' => tep_db_prepare_input($_GET['site_id']));
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_CAMPAIGN, $sql_data_array);
        }
        if (isset($_GET['st_id'])) {
          tep_redirect(tep_href_link(FILENAME_CAMPAIGN, 'site_id='.(int)$_GET['st_id']));
        } else {
          tep_redirect(tep_href_link(FILENAME_CAMPAIGN, 'site_id='.(int)$_GET['site_id']));
        }
        }
        if ($_GET['action'] == 'update') {
          $_GET['action'] = 'edit_campaign'; 
        } else {
          $_GET['action'] = 'new_campaign'; 
        }
        break; 
      case 'deleteconfirm':
        tep_db_query('delete from '.TABLE_CAMPAIGN." where id = '".$_GET['campaign_id']."'"); 
        if (empty($_GET['site_id'])) {
          tep_redirect(tep_href_link(FILENAME_CAMPAIGN));
        } else {
          tep_redirect(tep_href_link(FILENAME_CAMPAIGN, 'site_id='.$_GET['site_id']));
        }
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/general.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script style="text/javascript">
function show_link_campaign_info(cid, sid)
{
  $.ajax({
    url: 'ajax_orders.php?action=edit_campaign',     
    data:'cid='+cid+'&st_id='+sid, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_campaign_info').html(data); 
      $('#show_campaign_info').show(); 
    }
  });
}
function show_campaign_info(ele, cid, sid)
{
  ele = ele.parentNode; 
  $.ajax({
    url: 'ajax_orders.php?action=edit_campaign',     
    data:'cid='+cid+'&st_id='+sid, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_campaign_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
      /*
          offset = ele.offsetTop + ele.offsetHeight + $('#show_campaign_info').height() > $('#campaign_list_box').height()?  ele.offsetTop+$("#campaign_list_box").position().top-$('#show_campaign_info').height()-$('#offsetHeight').height():ele.offsetTop+$("#campaign_list_box").position().top+ele.offsetHeight;
        $('#show_campaign_info').css('top', offset).show(); 
      */
        if (ele.offsetTop+$('#campaign_list_box').position().top+ele.offsetHeight+$('#show_campaign_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#campaign_list_box').position().top-$('#show_campaign_info').height()-$('#offsetHeight').height();
          $('#show_campaign_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#campaign_list_box').position().top+ele.offsetHeight;
          $('#show_campaign_info').css('top', offset).show(); 
        }
      } else {
        if (ele.offsetTop+$('#campaign_list_box').position().top+ele.offsetHeight+$('#show_campaign_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#campaign_list_box').position().top-$('#show_campaign_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_campaign_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#campaign_list_box').position().top+ele.offsetHeight;
          $('#show_campaign_info').css('top', offset).show(); 
        }
      }
      $('#show_campaign_info').show(); 
    }
  });
}

function close_campaign_info()
{
  $('#show_campaign_info').html('');  
  $('#show_campaign_info').hide(); 
}

function show_new_campaign(std)
{
  $.ajax({
    url: 'ajax_orders.php?action=new_campaign',     
    data: 'site_id='+std, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_campaign_info').html(data);  
      $('#show_campaign_info').show(); 
    }
  });
}

function check_campaign_info(cid, check_type, site_id)
{
   var chkObjs = document.getElementsByName("type");
   var type_value = 1; 
   for(var i=0;i<chkObjs.length;i++){
     if(chkObjs[i].checked){
       type_value = chkObjs[i].value; 
       break; 
     }
   }
   $.ajax({
    url: 'ajax_orders.php?action=check_campaign',     
    data:'title='+document.getElementById('title').value+'&name='+document.getElementById('name').value+'&keyword='+document.getElementById('keyword').value+'&syear='+document.getElementById('syear').value+'&smonth='+document.getElementById('smonth').value+'&sday='+document.getElementById('sday').value+'&eyear='+document.getElementById('eyear').value+'&emonth='+document.getElementById('emonth').value+'&eday='+document.getElementById('eday').value+'&max_use='+document.getElementById('max_use').value+'&point_value='+document.getElementById('point_value').value+'&limit_value='+document.getElementById('limit_value').value+'&type='+type_value+'&check='+check_type+'&site_id='+site_id+'&campaign_id='+cid, 
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      var error_arr = data.split('|||'); 
      $('#title_error').html(error_arr[0]); 
      $('#name_error').html(error_arr[1]); 
      $('#keyword_error').html(error_arr[2]); 
      $('#date_error').html(error_arr[3]); 
      $('#max_use_error').html(error_arr[4]); 
      $('#point_value_error').html(error_arr[5]); 
      $('#limit_value_error').html(error_arr[6]); 
      if (data == '||||||||||||||||||') {
        document.forms.campaign.submit(); 
      }
    }
  });
}

function toggle_type_info(ele)
{
  if (ele.value == '1') {
    document.getElementById('type_symbol').innerHTML = '+'; 
    document.getElementById('limit_value_text').innerHTML = '<?php
      echo TEXT_CAMPAIGN_LIMIT_VALUE_READ_UP;?>'; 
  } else {
    document.getElementById('type_symbol').innerHTML = '-'; 
    document.getElementById('limit_value_text').innerHTML = '<?php
      echo TEXT_CAMPAIGN_LIMIT_VALUE_READ_DOWN;?>'; 
  }
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
              <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        <div id="show_campaign_info" style="display:none;"></div> 
        <?php tep_site_filter(FILENAME_CAMPAIGN);?>
        <table id="campaign_list_box" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_SITE; ?></td>
                <td class="dataTableHeadingContent"><?php 
                echo TABLE_HEADING_CAMPAIGN_TITLE; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_CAMPAIGN_NAME; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_CAMPAIGN_KEYWORD; ?></td>
                <td class="dataTableHeadingContent" align="left"><?php 
                echo TABLE_HEADING_CAMPAIGN_EFFECT_DATE; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php 
                echo TABLE_HEADING_CAMPAIGN_USE_NUM; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php 
                echo TABLE_HEADING_CAMPAIGN_VALUE; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="center"><?php 
                echo TABLE_HEADING_CAMPAIGN_STATUS; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php 
                echo TABLE_HEADING_CAMPAIGN_FETCH_USE_NUM; ?>&nbsp;</td>
                <td class="dataTableHeadingContent" align="right"><?php 
                echo TABLE_HEADING_CAMPAIGN_ACTION; ?>&nbsp;</td>
              </tr>
<?php
    $rows = 0;

    //$latest_news_count = 0;
    $campaign_query_raw = '
        select c.id, 
               c.name, 
               c.title, 
               c.keyword, 
               c.end_date, 
               c.start_date, 
               c.is_preorder,
               c.max_use,
               c.point_value,
               c.used,
               c.created_at,
               c.status,
               c.site_id
        from ' . TABLE_CAMPAIGN . ' c
        where 1 
        ' . (isset($_GET['site_id']) && intval($_GET['site_id']) ? " and (c.site_id = '" . intval($_GET['site_id']) . "') " : '') . '
        order by created_at desc
    ';
    $campaign_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $campaign_query_raw, $campaign_query_numrows);
    $campaign_query = tep_db_query($campaign_query_raw);
    while ($campaign = tep_db_fetch_array($campaign_query)) {
      //$campaign_count++;
      $rows++;
      
      if ( ((!isset($_GET['campaign_id']) || !$_GET['campaign_id']) || ($_GET['campaign_id'] == $campaign['id'])) && (!isset($selected_item) || !$selected_item) && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_') ) {
        $selected_item = $campaign;
      }
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      if ( (isset($selected_item) && is_array($selected_item)) && ($campaign['id'] == $selected_item['id']) ) {
        echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'">' . "\n";
      } else {
        echo '              <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'">' . "\n";
      }
?>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'"><?php echo '&nbsp;'.tep_get_site_romaji_by_id($campaign['site_id']); ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'"><?php echo '&nbsp;' . $campaign['title']; ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'"><?php echo '&nbsp;' . $campaign['name']; ?></td>
                <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'"><?php echo '&nbsp;' .  $campaign['keyword']; ?></td>
                <td class="dataTableContent"  onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
<?php
echo $campaign['start_date'].'～'.$campaign['end_date'];
?></td>
                <td class="dataTableContent" align="right" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
                <?php
                echo $campaign['max_use'];
                ?>
                </td>
                <td class="dataTableContent" align="right" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
                <?php
                echo $campaign['point_value'];
                ?>
                </td>
                <td class="dataTableContent" align="center" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
                <?php
                if ($campaign['status'] == '1') {
                  echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' .  tep_href_link(FILENAME_CAMPAIGN, 'action=setflag&flag=0&campaign_id=' . $campaign['id']. (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                } else {
                  echo '<a href="' . tep_href_link(FILENAME_CAMPAIGN, 'action=setflag&flag=1&campaign_id=' . $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                }
                ?>
                </td>
                <td class="dataTableContent" align="right" onclick="document.location.href='<?php echo tep_href_link(FILENAME_CAMPAIGN, 'page='.$_GET['page'].'&campaign_id=' .  $campaign['id'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):''));?>'">
                <?php
                $cam_count_raw = tep_db_query("select count(*) as total from ".TABLE_CUSTOMER_TO_CAMPAIGN." where campaign_id = '".$campaign['id']."'"); 
                $cam_count = tep_db_fetch_array($cam_count_raw);
                if ($cam_count['total']) {
                  echo $cam_count['total']; 
                } else {
                  echo '0'; 
                }
                ?>
                </td>
                <td class="dataTableContent" align="right">
<?php
      echo '<a href="javascript:void(0);" onclick="show_campaign_info(this, \''.$campaign['id'].'\', \''.(!empty($_GET['site_id'])?$_GET['site_id']:0).'\');">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    ?>&nbsp;
    </td>
              </tr>
<?php
    }

?>
              <tr>
                <td colspan="10"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $campaign_split->display_count($campaign_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
                    <td class="smallText" align="right"><?php echo $campaign_split->display_links($campaign_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'campaign_id'))); ?></td>
                  </tr>
                  <tr>
                    <td colspan="2" align="right" class="smallText">
                    <?php 
                    echo '&nbsp;<a href="javascript:void(0);">' .tep_html_element_button(IMAGE_NEW_PROJECT, 'onclick="show_new_campaign(\''.(!empty($_GET['site_id'])?$_GET['site_id']:0).'\');"') . '</a>'; 
                    ?>
                    &nbsp;
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
<?php
    $heading = array();
    $contents = array();
    switch (isset($_GET['action'])?$_GET['action']:null) {
      case 'delete_campaign': //generate box for confirming a news article deletion
        $heading[] = array('text'   => '<b>' . TEXT_INFO_HEADING_DELETE_ITEM . '</b>');
        
        $contents = array('form'    => tep_draw_form('compaign', FILENAME_CAMPAIGN, 'action=deleteconfirm'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) . tep_draw_hidden_field('campaign_id', $_GET['campaign_id']));
        $contents[] = array('text'  => TEXT_DELETE_ITEM_INTRO);
        $contents[] = array('text'  => '<br><b>' . $selected_item['headline'] . '</b>');
        
        $contents[] = array('align' => 'center',
                            'text'  => '<br>' .  tep_html_element_submit(IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CAMPAIGN, 'campaign_id=' . $selected_item['id']) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
        break;
      case 'new_campaign':
        if(isset($_GET['site_id'])&&$_GET['site_id']!=0){
        $heading[] = array('text'   => '<b>' . TEXT_INFO_HEADING_NEW_ITEM . '</b>');
        $contents = array('form'    => tep_draw_form('compaign', FILENAME_CAMPAIGN,
              'action=insert'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')));
        $contents[] = array('text' => '<br>' . TEXT_INFO_TITLE . '<br>' .
          tep_draw_input_field('title').'<br><font color="#ff0000">'.$title_error.'</font>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_NAME . '<br>' .
          tep_draw_input_field('name').'<br><font color="#ff0000">'.$name_error.'</font>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_KEYWORD . '<br>' .
          tep_draw_input_field('keyword').'<br><font color="#ff0000">'.$keyword_error.'</font>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_PREORDER . '<br>' .
          tep_draw_radio_field('is_preorder',1,false).TEXT_TRUE."".
          tep_draw_radio_field('is_preorder',0,true).TEXT_FALSE);
        $contents[] = array('text' => '<br>' . TEXT_INFO_START_DATE . '<br>' .
          tep_draw_input_field('start_date'));
        $contents[] = array('text' => '<br>' . TEXT_INFO_END_DATE . '<br>' .
          tep_draw_input_field('end_date').'<br><font color="#ff0000">'.$date_error.'</font>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_MAX_USE . '<br>' .
          tep_draw_input_field('max_use'));
        $contents[] = array('text' => '<br>' . TEXT_INFO_POINT_VALUE . '<br>' .
          tep_draw_input_field('point_value'));
        $contents[] = array('align' => 'center',
                            'text'  => '<br>' .
                            tep_html_element_submit(IMAGE_INSERT) . ' <a href="' . tep_href_link(FILENAME_CAMPAIGN, 'campaign_id=' . $selected_item['id']) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
        }
        break;
      case 'edit_campaign':
        if((isset($_GET['site_id'])&&$_GET['site_id']!=0)||
            (isset($selected_item['site_id'])&&$selected_item['site_id']!=0)){
        if(isset($_GET['site_id'])&&$_GET['site_id']!=0){
          $site_id = $_GET['site_id'];
        }else if(isset($selected_item['site_id'])&&$selected_item['site_id']!=0){
          $site_id = $selected_item['site_id'];
        }
        $heading[] = array('text'   => '<b>' . TEXT_INFO_HEADING_NEW_ITEM . '</b>');
        $contents = array('form'    => tep_draw_form('compaign', FILENAME_CAMPAIGN,
              'action=update'.(isset($site_id)?('&site_id='.$site_id):'')));
        $contents[] = array('text' => '<br>' . TEXT_INFO_TITLE . '<br>' .
          tep_draw_input_field('title',$selected_item['title']).'<br><font color="#ff0000">'.$title_error.'</font>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_NAME . '<br>' .
          tep_draw_input_field('name',$selected_item['name']).'<br><font color="#ff0000">'.$name_error.'</font>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_KEYWORD . '<br>' .
          tep_draw_input_field('keyword',$selected_item['keyword']).'<br><font color="#ff0000">'.$keyword_error.'</font>');
        if($selected_item['is_preorder']==0){
        $contents[] = array('text' => '<br>' . TEXT_INFO_PREORDER . '<br>' .
          tep_draw_radio_field('is_preorder',1,false).TEXT_TRUE."".
          tep_draw_radio_field('is_preorder',0,true).TEXT_FALSE);
        }else{
        $contents[] = array('text' => '<br>' . TEXT_INFO_KEYWORD . '<br>' .
          tep_draw_radio_field('is_preorder',1,true).TEXT_TRUE."".
          tep_draw_radio_field('is_preorder',0,false).TEXT_FALSE);
        }
        $contents[] = array('text' => '<br>' . TEXT_INFO_START_DATE . '<br>' .
          tep_draw_input_field('start_date',$selected_item['start_date']));
        $contents[] = array('text' => '<br>' . TEXT_INFO_END_DATE . '<br>' .
          tep_draw_input_field('end_date',$selected_item['end_date']).'<br><font color="#ff0000">'.$date_error.'</font>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_MAX_USE . '<br>' .
          tep_draw_input_field('max_use',$selected_item['max_use']));
        $contents[] = array('text' => '<br>' . TEXT_INFO_POINT_VALUE . '<br>' .
          tep_draw_input_field('point_value',$selected_item['point_value']).
          tep_draw_hidden_field('campaign_id',$selected_item['id']));
        $contents[] = array('align' => 'center',
                            'text'  => '<br>' .
                            tep_html_element_submit(IMAGE_UPDATE) . ' <a href="' . tep_href_link(FILENAME_CAMPAIGN, 'campaign_id=' . $selected_item['id']) . '">' .  tep_html_element_button(IMAGE_CANCEL) . '</a>');
        }
        break;

      default:
        if ($rows > 0) {
          if (is_array($selected_item)) { //an item is selected, so make the side box
            $heading[] = array('text' => '<b>' . $selected_item['title'] . '</b>');

            $contents[] = array('align' => 'center', 
                                'text' => '<a href="' .  tep_href_link(FILENAME_CAMPAIGN, 'campaign_id=' .  $selected_item['id'] .  '&action=edit_campaign') .  (isset($_GET['site_id'])?('&lsite_id='.$_GET['site_id']):'').'">' . tep_html_element_button(IMAGE_EDIT) . '</a> <a href="' . tep_href_link(FILENAME_CAMPAIGN, 'campaign_id=' . $selected_item['id'] .  '&action=delete_campaign'.  (isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'')) .  '">' . tep_html_element_button(IMAGE_DELETE) .  '</a>');
          }
        }
        break;
    }

    if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
      //echo '            <td width="25%" valign="top">' . "\n";

      //$box = new box;
      //echo $box->infoBox($heading, $contents);

      //echo '            </td>' . "\n";
    }
?>
          </tr>
        </table></td>
      </tr>
    </table></td>
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
