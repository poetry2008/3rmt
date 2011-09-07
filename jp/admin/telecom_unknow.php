<?php
/*
   $Id$
*/

  require('includes/application_top.php');
  //require(DIR_WS_CLASSES . 'currencies.php');
  //$currencies          = new currencies();
  if ($_GET['action'] == 'hide' && $_GET['id']) {
    tep_db_perform('telecom_unknow', array('type' => 'hide'), 'update', "id='".$_GET['id']."'") && print('success');
    exit;
  } else if ($_GET['action'] == 'hide_more') {
    foreach($_POST['ids'] as $id){
      tep_db_perform('telecom_unknow', array('type' => 'hide'), 'update', "id='".$id."'");
    }
    tep_redirect(tep_href_link('telecom_unknow.php?keywords='.$_POST['keywords'].'&rel='.$_POST['rel'].'&page='.$_POST['page']));
  }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
<script>
  function hide(id, ele){
    $.ajax({
      dataType: 'text',
      url: 'telecom_unknow.php?action=hide&id='+id,
      success: function(text) {
        if (text == 'success') {
          $(ele).parent().parent().remove();
        }
      }
    });
  }
// 全选
function all_check(ele){
  var error = false;
  if (ele.checked) {

    $('.a_checkbox').each(function(){
      if (this.value == 'false') {
        error = error || true;
      }
    });
    if (error) {
      alert('引当が「済」になっているものは、非表示には出来ません');
      ele.checked = false;
      return false;
    }

    $('.a_checkbox').attr('checked',true).parent().parent().find('td').css('background','#f08080');
  } else {
    $('.a_checkbox').each(function(){
      this.checked=false;
      check_one(this);
    });

  }
}
function check_one(ele){
  if(ele.value=='false') {
    alert('引当が「済」になっているものは、非表示には出来ません');
    ele.checked = false;
    return false;
  }
  if(ele.checked){
    $('#tr_'+ele.value+' td').css('background','#f08080');
  } else {
    $('#tr_'+ele.value+' td').css('background',$('#red_'+ele.value).val()==1 ? 'red' :($('#rel_'+ele.value).val() == 'yes'?'#fff':'#ccc'));
  }
}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( isset($_GET['action']) && ($_GET['action'] == 'edit') && ($order_exists) ) {
    // edit start
?>

<?php
  // edit over
  } else {
  // list start
?>
    <tr>
      <td width="100%">
  
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading" width="33%" height="40"><a href="telecom_unknow.php"><?php echo TELECOM_UNKNOW_TITLE;?></a></td>
      <td align="center">
        <form action="?" method="get">
          <input type="text" name="keywords" value="<?php echo $_GET['keywords'];?>">
          <input type="checkbox" name="rel_yes" value="1" <?php if (!(!$_GET['rel_yes'] && $_GET['rel_no'])) {echo 'checked';} ?>><?php echo TELECOM_UNKNOW_SEARCH_SUCCESS;?> 
          <input type="checkbox" name="rel_no" value="1" <?php if (!(!$_GET['rel_no'] && $_GET['rel_yes'])) {echo 'checked';} ?>><?php echo TELECOM_UNKNOW_SEARCH_FAIL;?>
          <input type="submit" value="Go">
        </form>
      </td>
      <td align="right" class="smallText" width="33%">&nbsp;</td>
    </tr>
  </table>
      </td>
    </tr>
    <tr>
      <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td valign="top">
    <form action="?action=hide_more" method="post" onsubmit="return confirm('<?php echo TELECOM_UNKNOW_SELECT_NOTICE;?>')">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr class="dataTableHeadingRow">
      <td class="dataTableHeadingContent" align="left" width="20"><input type="checkbox" onclick="all_check(this)"></td>
      <td class="dataTableHeadingContent" align="center" width="50"><?php echo TELECOM_UNKNOW_TABLE_CAL_METHOD;?></td>
      <td class="dataTableHeadingContent" align="center" width="150"><?php echo TELECOM_UNKNOW_TABLE_TIME;?></td>
      <td class="dataTableHeadingContent" align="center"><?php echo TELECOM_UNKNOW_TABLE_CAL;?></td>
      <td class="dataTableHeadingContent" align="center"><?php echo TELECOM_UNKNOW_TABLE_YIN;?></td>
      <td class="dataTableHeadingContent" align="center"><?php echo TELECOM_UNKNOW_TABLE_SURNAME;?></td>
      <td class="dataTableHeadingContent" align="center"><?php echo TELECOM_UNKNOW_TABLE_TEL;?></td>
      <td class="dataTableHeadingContent" align="center"><?php echo TELECOM_UNKNOW_TABLE_EMAIL;?></td>
      <td class="dataTableHeadingContent" align="center"><?php echo TELECOM_UNKNOW_TABLE_PRICE;?></td>
      <td class="dataTableHeadingContent" align="center">&nbsp;</td>
    </tr>
<?php
        /* 
    if (isset($_GET['keywords']) && tep_not_null($_GET['keywords'])) {
        $where_str .= " and (";
        $where_str .= "(username = '" . $_GET['keywords'] . "' or email = '" .  $_GET['keywords'] . "' or telno = '" . $_GET['keywords'] . "' or money = '" . $_GET['keywords'] . "')";
        $where_str .= " )";
      if (tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
        $where_str .= " and (";
        for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
          switch ($search_keywords[$i]) {
            case '(':
            case ')':
            case 'and':
            case 'or':
              $where_str .= " " . $search_keywords[$i] . " ";
              break;
            default:
              $where_str .= "(username like '%" . addslashes($search_keywords[$i]) . "%' or email like '%" . addslashes($search_keywords[$i]) . "%' or telno like '%" . addslashes($search_keywords[$i]) . "%' or money like '%" . addslashes($search_keywords[$i]) . "%'";
              $where_str .= ')';
              break;
          }
        }
        $where_str .= " )";
      }
    }
      */ 
    if (isset($_GET['search_type']) && tep_not_null($_GET['search_type'])) {
      if (isset($_GET['keywords']) && tep_not_null($_GET['keywords'])) {
        switch ($_GET['search_type']) {
          case 'username':
            if (tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
              $where_str .= " and (";
              for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
                switch ($search_keywords[$i]) {
                  case '(':
                  case ')':
                  case 'and':
                  case 'or':
                    $where_str .= " " . $search_keywords[$i] . " ";
                    break;
                  default:
                    $where_str .= "(username like '%" . addslashes($search_keywords[$i]) . "%')";
                    break;
                }
              }
              $where_str .= " )";
            }
            break;
          case 'telno':
            if (tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
              $where_str .= " and (";
              for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
                switch ($search_keywords[$i]) {
                  case '(':
                  case ')':
                  case 'and':
                  case 'or':
                    $where_str .= " " . $search_keywords[$i] . " ";
                    break;
                  default:
                    $where_str .= "(telno like '%" . addslashes($search_keywords[$i]) . "%')";
                    break;
                }
              }
              $where_str .= " )";
            }
            break;
          case 'email':
            if (tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
              $where_str .= " and (";
              for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
                switch ($search_keywords[$i]) {
                  case '(':
                  case ')':
                  case 'and':
                  case 'or':
                    $where_str .= " " . $search_keywords[$i] . " ";
                    break;
                  default:
                    $where_str .= "(email like '%" . addslashes($search_keywords[$i]) . "%')";
                    break;
                }
              }
              $where_str .= " )";
            }
            break;
          case 'money':
            if (tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
              $where_str .= " and (";
              for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
                switch ($search_keywords[$i]) {
                  case '(':
                  case ')':
                  case 'and':
                  case 'or':
                    $where_str .= " " . $search_keywords[$i] . " ";
                    break;
                  default:
                    $where_str .= "(money like '%" . addslashes($search_keywords[$i]) . "%')";
                    break;
                }
              }
              $where_str .= " )";
            }
            break;
          default:
            if (tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
              $where_str .= " and (";
              for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
                switch ($search_keywords[$i]) {
                  case '(':
                  case ')':
                  case 'and':
                  case 'or':
                    $where_str .= " " . $search_keywords[$i] . " ";
                    break;
                  default:
                    $where_str .= "(username like '%" . addslashes($search_keywords[$i]) . "%' or email like '%" . addslashes($search_keywords[$i]) . "%' or telno like '%" . addslashes($search_keywords[$i]) . "%' or money like '%" . addslashes($search_keywords[$i]) . "%'";
                    $where_str .= ')';
                    break;
                }
              }
              $where_str .= " )";
            }
            break;
        }
      }
    } else {
      if (isset($_GET['keywords']) && tep_not_null($_GET['keywords'])) {
        if (tep_parse_search_string(stripslashes($_GET['keywords']), $search_keywords)) {
          $where_str .= " and (";
          for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
            switch ($search_keywords[$i]) {
              case '(':
              case ')':
              case 'and':
              case 'or':
                $where_str .= " " . $search_keywords[$i] . " ";
                break;
              default:
                $where_str .= "(username like '%" . addslashes($search_keywords[$i]) . "%' or email like '%" . addslashes($search_keywords[$i]) . "%' or telno like '%" . addslashes($search_keywords[$i]) . "%' or money like '%" . addslashes($search_keywords[$i]) . "%'";
                $where_str .= ')';
                break;
            }
          }
          $where_str .= " )";
        }
      } 
    }
    /*
    if ($_GET['rel']) {
      $where_str .= " and rel='".$_GET['rel']."'";
    }
    */
    if ($_GET['rel_yes'] && !$_GET['rel_no']) {
      $where_str .= " and rel='yes'";
    }
    if ($_GET['rel_no'] && !$_GET['rel_yes']) {
      $where_str .= " and rel='no'";
    }
      // 默认显示不明和后台创建的信用卡
      $orders_query_raw = "
        select *
        from telecom_unknow
        where 1 ".(!$_GET['keywords']?" and type is null":'')." ".$where_str."
        order by date_added DESC
      ";
      
      $orders_query_numrows_raw = "
        select count(id) as count
        from telecom_unknow
        where 1 ".(!$_GET['keywords']?" and type is null":'')." ".$where_str."
      ";
      
      //echo $orders_query_raw;

    $orders_split = new splitPageResults($_GET['page'], MAX_DISPLAY_ORDERS_RESULTS, $orders_query_raw, &$orders_query_numrows, $orders_query_numrows_raw);
    $orders_query = tep_db_query($orders_query_raw);

    while ($orders = tep_db_fetch_array($orders_query)) {
      echo '    <tr onmouseover="this.style.background=\'#FFCC99\'" onmouseout="this.style.background=\''.(tep_match_by_keywords($orders['telno'],TELNO_KEYWORDS)?'red':($orders['rel'] == 'no'?'#ccc':'#fff')).'\'" style="border-bottom:1px solid #000000;'.(tep_match_by_keywords($orders['telno'],TELNO_KEYWORDS)?'background:red':($orders['rel'] == 'no'?'background:#ccc':'background:#fff')).'" class="dataTableRow" id="tr_'.$orders['id'].'">' . "\n";
?>
      <td align="left"   style="border-bottom:1px solid #000000;" class="dataTableContent"><input type="checkbox" name="ids[]" class="a_checkbox" onclick="check_one(this)" value="<?php echo $orders['type'] == 'success' ? 'false' : $orders['id'];?>"></td>
      <td align="center"   style="border-bottom:1px solid #000000;" class="dataTableContent"><?php echo $orders['payment_method'] == 'paypal' ? 'ペイパル' : 'テレコム'; ?>&nbsp;</td>
      <td align="center" style="border-bottom:1px solid #000000;" class="dataTableContent"><?php echo tep_datetime_short($orders['date_added']); ?>&nbsp;</td>
      <td align="center" style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['rel'] == 'yes' ? '成功' : '失敗'; ?>&nbsp;</td>
      <td align="center" style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['type'] == 'success' ? '済' : '<font color="darkred">未</font>'; ?>&nbsp;</td>
      <td align="left"   style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['username']; ?>&nbsp;</td>
      <td align="left"   style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo tep_high_light_by_keywords($orders['telno'],TELNO_KEYWORDS);?>&nbsp;</td>
      <td align="left"   style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['email']; ?>&nbsp;</td>
      <td align="right"  style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><?php echo $orders['money']; ?>&nbsp;</td>
      <td align="right"  style="border-bottom:1px solid #000000;" class="dataTableContent" align="right"><input type="hidden" id="rel_<?php echo $orders['id'];?>" value="<?php echo $orders['rel'];?>"><input type="hidden" id="red_<?php echo $orders['id'];?>" value="<?php echo tep_match_by_keywords($orders['telno'],TELNO_KEYWORDS)?1:0;?>"><?php if (!($orders['type'] == 'success')) {?><a href="javascript:void(0);" onclick="return confirm('非表示にしますか？') && hide(<?php echo $orders['id'];?>, this)"><img src="images/icons/cross.gif" ></a><?php } else { echo '&nbsp;'; } ?></td>
    </tr>
<?php }?>
  </table>
   
  <!-- display add end-->
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
      <td colspan="5">
        <table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" ><input type="submit" value="一括非表示"><?php echo $orders_split->display_count($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_ORDERS); ?></td>
            <td class="smallText" align="right"><?php echo $orders_split->display_links($orders_query_numrows, MAX_DISPLAY_ORDERS_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'oID', 'action'))); ?></td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="page" value="<?php echo $_GET['page'];?>">
    <input type="hidden" name="keywords" value="<?php echo $_GET['keywords'];?>">
    <input type="hidden" name="rel" value="<?php echo $_GET['rel'];?>">
  </form>
      </td>
    </tr>
  </table>
      </td>
    </tr>
<?php
  }
?>

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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
