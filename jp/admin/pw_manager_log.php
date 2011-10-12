<?php
/*
   $Id$
*/
  require('includes/application_top.php');
  define('MAX_DISPLAY_PW_MANAGER_LOG_RESULTS',20);
  if($_SESSION['user_permission']!=15){
  header($_SERVER["SERVER_PROTOCOL"] . " 403 Forbidden");
  exit;
  }

  if(isset($_GET['pw_id'])&&$_GET['pw_id']){
    $pwid = tep_db_prepare_input($_GET['pw_id']);
  }
  if(isset($_GET['site_id'])&&$_GET['site_id']){
    $site_id = tep_db_prepare_input($_GET['site_id']);
  }else{
    $site_id = '';
  }
  if(isset($_GET['pw_l_id'])&&$_GET['pw_l_id']){
    $pwlid = tep_db_prepare_input($_GET['pw_l_id']);
  }
  if (isset($_GET['action']) && $_GET['action']) {
    $user_info = tep_get_user_info($ocertify->auth_user);
    switch ($_GET['action']) {
      case 'deleteconfirm':
        //unlink();
        if(tep_has_pw_manager_log($pwid)){
        if($_GET['select']=='all'){
        tep_db_query("delete from " . TABLE_IDPW_LOG. " where idpw_id='".$pwid."'");
        }else{
        tep_db_query("delete from " . TABLE_IDPW_LOG . " where id = '" .
            tep_db_input($pwlid) . "'");
        }
        }
        tep_redirect(tep_href_link(FILENAME_PW_MANAGER_LOG, 'page=' .
              $_GET['page'].'&pw_id='.$pwid.'&site_id='.$site_id));
        break;

    }
  }
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TEXT_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery.form.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script language="javascript" >
function delete_all(){
  if(confirm('履歴を削除しますか？')){
    location.href='<?php echo
      tep_href_link(FILENAME_PW_MANAGER_LOG,'action=deleteconfirm&select=all&pw_id='.$pwid);?>';
  }
}
</script>
</head>
<body>
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<!-- header //-->
<?php
  require(DIR_WS_INCLUDES . 'header.php');
?>
<!-- header_eof //-->
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<?php
  if ($ocertify->npermission >= 10) {
    echo '<td width="' . BOX_WIDTH . '" valign="top">';
    echo '<table border="0" width="' . BOX_WIDTH . '" cellspacing="1" cellpadding="1" class="columnLeft">';
    echo '<!-- left_navigation //-->';
    require(DIR_WS_INCLUDES . 'column_left.php');
    echo '<!-- left_navigation_eof //-->';
    echo '</table>';
    echo '</td>';
  } else {
    echo '<td>&nbsp;</td>';
  }
?>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="100%" colspan='2'>
  
  <table border="0" width="100%" cellspacing="2" cellpadding="0">
    <tr>
      <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
      <td align="right" class="smallText">
        <table width=""  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td class="smallText" valign='top'>
              <?php echo tep_draw_form('orders1', FILENAME_PW_MANAGER_LOG, '', 'get','id="orders1" onsubmit="return false"'); ?>検索 : 
              <input name="keywords" type="text" id="keywords" size="40" value="<?php if(isset($_GET['keywords'])) echo stripslashes($_GET['keywords']); ?>">
              <select name="search_type" onChange='search_type_changed(this)'>
                <option value="none"><?php echo PW_MANAGER_SELECT_NONE;?></option>
                <option value="priority"><?php echo PW_MANAGER_SELECT_ONE;?></option>
                <option value="loginurl"><?php echo PW_MANAGER_SELECT_TWO;?></option>
                <option value="title"><?php echo PW_MANAGER_SELECT_THREE;?></option>
                <option value="url"><?php echo PW_MANAGER_SELECT_FOUR;?></option>
                <option value="username"><?php echo PW_MANAGER_SELECT_FIVE;?></option>
                <option value="password"><?php echo PW_MANAGER_SELECT_SIX;?></option>
                <option value="operator"><?php echo PW_MANAGER_SELECT_SEVEN;?></option>
                <option value="comment"><?php echo PW_MANAGER_SELECT_EIGHT;?></option>
                <option value="memo"><?php echo PW_MANAGER_SELECT_NINE;?></option>
                <option value="site_id"><?php echo PW_MANAGER_SELECT_TEN;?></option>
              </select>
              </form>
            </td>
            <td valign="top"></td>
          </tr>
        </table>
      </td>
      <td align="right">
      </td>
    </tr>
  </table>

      </td>
    </tr>
    <tr>
      <td valign="top">
    <table width="100%">
      <tr>
        <td>
    <?php tep_site_filter(FILENAME_PW_MANAGER_LOG);?>
        </td>
        <td align="right">
        </td>
      </tr>
    </table>
    <?php
      //add order 
      $order_str = ''; 
      /*
      if (!isset($HTTP_GET_VARS['sort'])||$HTTP_GET_VARS['sort']=='') {
        $order_str = '`updated_at` desc, `title` asc'; 
      } else {
        if($HTTP_GET_VARS['sort'] == 'nextdate'){
        $order_str = '`date_order` '.$HTTP_GET_VARS['type']; 
        }else{
        $order_str = '`'.$HTTP_GET_VARS['sort'].'` '.$HTTP_GET_VARS['type']; 
        }
      }
      */
      if (!isset($HTTP_GET_VARS['sort'])||$HTTP_GET_VARS['sort']=='') {
        $next_str = "IF(nextdate = '0000-00-00', '1', '0') as date_order, ";
        $order_str = '`date_order`,`nextdate` asc, `title` asc'; 
      } else {
        if($HTTP_GET_VARS['sort'] == 'nextdate'){
          if($HTTP_GET_VARS['type'] == 'desc' ){
            $next_str = 'nextdate as ';
            $order_str = 'nextdate '.$HTTP_GET_VARS['type']; 
          }else{
            $next_str = "IF(nextdate = '0000-00-00', '1', '0') as date_order, ";
            $order_str = 'date_order,nextdate '.$HTTP_GET_VARS['type']; 
          }    
        }else{
            $next_str = 'nextdate as ';
        $order_str = '`'.$HTTP_GET_VARS['sort'].'` '.$HTTP_GET_VARS['type']; 
        }    
      }
      
      if ($HTTP_GET_VARS['type'] == 'asc') {
        $type_str = 'desc'; 
      } else {
        $type_str = 'asc'; 
      }
    ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2" id='orders_list_table'>
    <tr class="dataTableHeadingRow">
<?php 
?>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'priority') {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=priority&type='.$type_str);?>"><?php echo TEXT_PRIORITY;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=priority&type=asc');?>"><?php echo TEXT_PRIORITY;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'loginurl') {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=loginurl&type='.$type_str);?>"><?php echo TEXT_LOGINURL;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=loginurl&type=asc');?>"><?php echo TEXT_LOGINURL;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'title') {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG,
        tep_get_all_get_params(array('x', 'y', 'type',
              'sort')).'sort=title&type='.$type_str);?>"><?php echo TEXT_INFO_TITLE;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG,
      tep_get_all_get_params(array('x', 'y', 'type',
            'sort')).'sort=title&type=asc');?>"><?php echo TEXT_INFO_TITLE;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'username') {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=username&type='.$type_str);?>"><?php echo TEXT_USERNAME;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=username&type=asc');?>"><?php echo TEXT_USERNAME;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'password') {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=password&type='.$type_str);?>"><?php echo TEXT_PASSWORD;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=password&type=asc');?>"><?php echo TEXT_PASSWORD;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'operator') {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG,
        tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=operator&type='.$type_str);?>"><?php echo TEXT_PRIVILEGE;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG,
      tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=operator&type=asc');?>"><?php echo TEXT_PRIVILEGE;?></a> 
      <?php
      }
      ?>
      </td>
      <td class="dataTableHeadingContent_pw">
      <?php 
      if ($HTTP_GET_VARS['sort'] == 'nextdate') {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=nextdate&type='.$type_str);?>"><?php echo TEXT_NEXTDATE;?></a> 
      <?php
      } else {
      ?>
      <a href="<?php echo tep_href_link(FILENAME_PW_MANAGER_LOG, tep_get_all_get_params(array('x', 'y', 'type', 'sort')).'sort=nextdate&type=asc');?>"><?php echo TEXT_NEXTDATE;?></a> 
      <?php
      }
      ?>
      <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
    </tr>
    <?php 
    if(isset($site_id)&&$site_id){
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from
                             ".TABLE_IDPW_LOG." where site_id='".$site_id."'
                             order by ".$order_str;
    }else if(isset($_GET['search_type'])&&$_GET['search_type']&&
        isset($_GET['keywords'])&&$_GET['keywords']){
      $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from
                             ".TABLE_IDPW_LOG." 
                             where ".$_GET['search_type']." like '%".
                             $_GET['keywords']."%'
                             order by ".$order_str;
    }else if(isset($pwid)&&$pwid){
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from 
                             ".TABLE_IDPW_LOG." where idpw_id = '".$pwid."'
                             order by ".$order_str;

    }else{
    $pw_manager_query_raw = "select id,title,priority,site_id,url,
                             loginurl,username,password,comment,memo
                             ,".$next_str."nextdate,privilege,operator,created_at,
                             updated_at,onoff,update_user from
                             ".TABLE_IDPW_LOG." order by ".$order_str;
    }
    $pw_manager_split = new splitPageResults($_GET['page'],
        MAX_DISPLAY_PW_MANAGER_LOG_RESULTS, $pw_manager_query_raw, $pw_manager_query_numrows);
    //var_dump($pw_manager_query_raw);
       
    $pw_manager_query = tep_db_query($pw_manager_query_raw);
    while($pw_manager_row = tep_db_fetch_array($pw_manager_query)){
      if (( (!@$_GET['pw_l_id']) || (@$_GET['pw_l_id'] == $pw_manager_row['id'])) &&
          (!@$pwInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $pwInfo = new objectInfo($pw_manager_row);
    }
    if (isset($pwInfo) && (is_object($pwInfo)) && ($pw_manager_row['id'] == $pwInfo->id) ) {
      echo '              <tr class="dataTableRowSelected"
        onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\''
        . tep_href_link(FILENAME_PW_MANAGER_LOG, 'page=' . $_GET['page'] .
            '&pw_l_id=' . $pwInfo->id . '&pw_id='.$pwid.'&site_id='.$site_id) . '\'">' . "\n";
    } else {
      echo '              <tr class="dataTableRow"
        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'"
        onmouseout="this.className=\'dataTableRow\'"
        onclick="document.location.href=\'' . tep_href_link(FILENAME_PW_MANAGER_LOG,
        'page=' . $_GET['page'] . '&pw_l_id=' .
          $pw_manager_row['id'].'&pw_id='.$pwid.'&site_id='.$site_id) . '\'">' . "\n";
    }
      $priority_str = "<font color='";
      switch($pw_manager_row['priority']){
        case '1':
            $priority_str .="black";
          break;
        case '2':
            $priority_str .="orange";
          break;
        case '3':
            $priority_str .="red";
          break;

      }
      $priority_str .= "' ><b>".$pw_manager_row['priority']."</b></font>";
      echo "<td class='dataTableContent' ".$onclick." >".$priority_str."</td>";
      echo "<td class='dataTableContent'>".$pw_manager_row['loginurl']."</td>";
      echo "<td class='dataTableContent'>".mb_substr($pw_manager_row['title'],0,12,'utf-8')."</td>";
      echo "<td class='dataTableContent'>".mb_substr($pw_manager_row['username'],0,8,'utf-8')."</td>";
      echo "<td class='dataTableContent'>".mb_substr($pw_manager_row['password'],0,8,'utf-8')."</td>";
      echo "<td class='dataTableContent'".$onclick." >";
        if($pw_manager_row['privilege'] =='7'){
         echo "Staff以上";
        }else if($pw_manager_row['privilege'] =='10'){
         echo "Chief以上";
        }else{
         if($pw_manager_row['self']!=''){
         $self_info = tep_get_user_info($pw_manager_row['self']);
         echo mb_substr($self_info['name'],0,5,'utf-8');
         }else{
         echo mb_substr($pw_manager_row['operator'],0,5,'utf-8');
         }
        }
      echo "</td>";
      echo "<td class='dataTableContent'>";
      echo $pw_manager_row['nextdate'];
      echo "</td>";
      echo '<td class="dataTableContent" align="right">';
      if ( isset($pwInfo) && (is_object($pwInfo)) && ($pw_manager_row['id'] == $pwInfo->id) ) { 
        echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
      } else { 
        echo '<a href="' . tep_href_link(FILENAME_PW_MANAGER_LOG, 'page=' .
          $_GET['page'] . '&pw_id='.$pw_id.'&site_id='.$site_id. '&type='.$_GET['type'].'&sort='.$_GET['sort'].'&pw_l_id=' . $pw_manager_row['id']) . '">' . 
          tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
      }
      echo '&nbsp;</td>';
      echo "</tr>";
    }



    ?>
    <tr>
       <td colspan="9" align="right">
         <?php
          echo "<button type='button'
          onclick=\"delete_all()\">" .
          TEXT_BUTTON_DELETE_ALL."</button>"; 
          
          echo "<button type='button'
          onclick=\"location.href='".
          tep_href_link(FILENAME_PW_MANAGER,'pw_id='.$pwid.'&site_id='.$site_id) 
          ."'\">" .
          TEXT_BUTTON_BACK."</button>"; 
         ?>
       </td>
    </tr>
    <tr>
      <td colspan="9">
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" valign="top"><?php echo
            $pw_manager_split->display_count($pw_manager_query_numrows,
                MAX_DISPLAY_PW_MANAGER_LOG_RESULTS, $_GET['page'],
                TEXT_DISPLAY_NUMBER_OF_PW_MANAGER_LOG); ?></td>
            <td class="smallText" align="right"><?php echo
            $pw_manager_split->display_links($pw_manager_query_numrows,
                MAX_DISPLAY_PW_MANAGER_LOG_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],
                tep_get_all_get_params(array('page', 'site_id', 'action','pwid'))); ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
      </td>
<?php
  $heading = array();
  $contents = array();
switch (isset($_GET['action'])? $_GET['action']:'') {
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PW_MANAGER_LOG . '</b>');

      $contents = array('form' => tep_draw_form('pw_manager', FILENAME_PW_MANAGER_LOG,
            'page=' . $_GET['page'] . '&pw_l_id=' . $pwInfo->id .
            '&action=deleteconfirm'.'&site_id='.$site_id.'&pw_id='.$pwid));
      $contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      $contents[] = array('text' => '<br><b>' . $pwInfo->title . '</b>');
      $contents[] = array('align' => 'center', 'text' => '<br>' .
          "<button type='submit' >".TEXT_BUTTON_DELETE."</button>"
          . '&nbsp;' .
          "<button type='button'
          onclick=\"location.href='".
          tep_href_link(FILENAME_PW_MANAGER_LOG, 'page=' . $_GET['page'] . '&pw_l_id=' .
            $pwInfo->id.'&site_id='.$site_id.'&pw_id='.$pwid)  
          ."'\">" .
          TEXT_BUTTON_CLEAR."</button>" 
          );
          /*
          tep_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' .
          tep_href_link(FILENAME_PW_MANAGER_LOG, 'page=' . $_GET['page'] . '&pw_l_id=' .
            $pwInfo->id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
            */
      break;
  default:
      $heading[] = array('text' => '');
      $contents[] = array('align' => 'center', 'text' => '<br>'.
          "<button type='button'
          onclick=\"location.href='".
          tep_href_link(FILENAME_PW_MANAGER_LOG,
            'action=delete&pw_l_id='.$pwInfo->id.'&'.tep_get_all_get_params(array('pw_l_id','action','search_type','keywords')))
          ."'\">" .
          TEXT_BUTTON_DELETE."</button>"
          );
      $contents[] = array('text' => '<br>' . TEXT_INFO_TITLE . '<br>' .
          $pwInfo->title);
      $contents[] = array('text' => '<br>' . TEXT_INFO_PRIORITY . '<br>' .
          $pwInfo->priority);
          $site_str = tep_get_site_info($pwInfo->site_id);
      $contents[] = array('text' => '<br>' . TEXT_INFO_SITE_ID . '<br>' .
          $site_str['romaji']);
      $contents[] = array('text' => '<br>' . TEXT_INFO_URL . '<br>' .
          $pwInfo->url);
      $contents[] = array('text' => '<br>' . TEXT_INFO_LOGINURL . '<br>' .
          $pwInfo->loginurl);
      $contents[] = array('text' => '<br>' . TEXT_USERNAME . '<br>' .
          $pwInfo->username);
      $contents[] = array('text' => '<br>' . TEXT_PASSWORD . '<br>' .
          $pwInfo->password);
      $contents[] = array('text' => '<br>' . TEXT_NEXTDATE . '<br>' .
          $pwInfo->nextdate);
      $contents[] = array('text' => '<br>' . TEXT_INFO_COMMENT . '<br>' .
          $pwInfo->comment);
      $contents[] = array('text' => '<br>' . TEXT_INFO_MEMO . '<br>' .
          $pwInfo->memo);
      $contents[] = array('align' => '','text' => '<br>' . TEXT_INFO_CREATED .  '&nbsp;&nbsp;&nbsp;' .
          $pwInfo->created_at);
      $contents[] = array('align' => '','text' => '<br>' . TEXT_INFO_UPDATED . '&nbsp;&nbsp;&nbsp;' .
          $pwInfo->updated_at);
      $contents[] = array('align' => '','text' => '<br>' . TEXT_INFO_OPRATER . '&nbsp;&nbsp;&nbsp;' .
          $pwInfo->update_user);
    break;
}
  if (tep_has_pw_manager_log($pwid)&& (tep_not_null($heading)) && (tep_not_null($contents))) {
    echo '            <td class="right_column01" width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }

?>
    </tr>
  </table>
      </td>
    </tr>

    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php
    require(DIR_WS_INCLUDES . 'footer.php');
?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
