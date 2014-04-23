<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  include(DIR_FS_ADMIN . DIR_WS_LANGUAGES .  '/default.php');
  if (isset($_GET['action'])) {
    switch($_GET['action']){
/* -----------------------------------------------------
   case 'insert' 新建点数邮件 
   case 'save' 更新点数邮件  
   case 'deleteconfirm' 删除点数邮件   
------------------------------------------------------*/
      case 'insert':
      case 'save':
        $point_mail_date = tep_db_prepare_input($_POST['mail_date']);
        if($_GET['action'] == 'insert'){
           $sql_point_mail_array = array('mail_date' => $point_mail_date,
					 'user_added' => $_POST['user_added'],
					 'user_update' =>$_POST['user_update'],
                                         'created_at' => 'now()',
                                         'updated_at' => 'now()');
           tep_db_perform(TABLE_POINT_MAIL,$sql_point_mail_array);
           $last_insert_id = mysql_insert_id();
           //同步对应的邮件模板
           tep_db_query("insert into ". TABLE_MAIL_TEMPLATES ." values(NULL,'POINT_NOTIFY_MAIL_TEMPLATES_".$last_insert_id."','0','".TEXT_INFO_POINT_MAIL_DATE.$point_mail_date.TEXT_POINT_NOTIFY_TITLE."','".TEXT_POINT_NOTIFY_USE_DESCRIPTION."','','','".TEXT_POINT_NOTIFY_DESCRIPTION."','1','1','".$_POST['user_added']."',now(),'".$_POST['user_added']."',now())");
           tep_redirect(tep_href_link(FILENAME_POINT_EMAIL,'page='.$_GET['page'].'&id='.$last_insert_id));

        }else if($_GET['action'] == 'save'){
           $sql_point_mail_array = array('mail_date' => $point_mail_date,
					 'user_update' =>$_POST['user_update'],
                                         'updated_at' => 'now()');
           tep_db_perform(TABLE_POINT_MAIL,$sql_point_mail_array,'update',
               'id='.tep_db_input($_POST['id']));
        }
        tep_redirect(tep_href_link(FILENAME_POINT_EMAIL,'page=' .
              $_GET['page'].'&id='.tep_db_input($_POST['id'])));
          break;
        case 'deleteconfirm';
          $id = tep_db_prepare_input($_GET['id']);
          tep_db_query("delete from ".TABLE_POINT_MAIL. " where id ='".$id."'");
          //删除对应的邮件模板
          tep_db_query("delete from ". TABLE_MAIL_TEMPLATES ." where flag='POINT_NOTIFY_MAIL_TEMPLATES_".$id."'");
          tep_redirect(tep_href_link(FILENAME_POINT_EMAIL, 'page=' .
                $_GET['page']));
          break;
    }

  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css?v=<?php echo $back_rand_info?>">
<script language="javascript" src="includes/javascript/jquery_include.js?v=<?php echo $back_rand_info?>"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js&v=<?php echo $back_rand_info?>"></script>
<script>
	var js_point_email_self = '<?php echo $_SERVER['PHP_SELF']?>';
	var js_onetime_pwd = '<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>';
	var js_onetime_error = '<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>';
</script>
<script language="javascript" src="includes/javascript/admin_point_mail.js?v=<?php echo $back_rand_info?>"></script>
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo
                TABLE_HEADING_MAIL_DATE; ?></td> 
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
$point_mail_query_raw = "select * from ".TABLE_POINT_MAIL." order by `mail_date`";
$point_mail_split = new splitPageResults($_GET['page'],
    MAX_DISPLAY_SEARCH_RESULTS,$point_mail_query_raw,$point_mail_query_numrows);
$point_mail_query = tep_db_query($point_mail_query_raw);
while($point_mail = tep_db_fetch_array($point_mail_query)){
  if(((!isset($_GET['id'])||!$_GET['id'])||($_GET['id'] == $point_mail['id']))
    &&(!isset($point_info)||!$point_info)
    &&(!isset($_GET['action'])||substr($_GET['action'],0,3) != 'new')){
    $point_info = new objectInfo($point_mail);
  }
  $even = 'dataTableSecondRow';
  $odd  = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even; 
  } else {
    $nowColor = $odd; 
  }
  if(isset($point_info) && (is_object($point_info)) && ($point_mail['id'] ==
        $point_info->id)){
    echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" 
      onclick="document.location.href=\'' . tep_href_link(FILENAME_POINT_EMAIL,
    'page=' . $_GET['page']. '&id=' . $point_info->id ) .
      '\'">' . "\n";
  }else{
    echo '<tr class="'.$nowColor.'"
      onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'"
      onmouseout="this.className=\''.$nowColor.'\'"
      onclick="document.location.href=\'' . tep_href_link(FILENAME_POINT_EMAIL,
      'page=' . $_GET['page'] .'&id=' . $point_mail['id']) .'\'">' .
        "\n";
  }
  ?>
    <td class="dataTableContent" ><?php echo $point_mail['mail_date'];?></td> 
    <td class="dataTableContent" align="right">
    <?php
    if ( isset($point_info) && (is_object($point_info)) && ($point_mail['id']
          == $point_info->id) ) 
    { 
      echo tep_image(DIR_WS_IMAGES .
          'icon_arrow_right.gif', ''); 
    } else { 
      echo '<a href="' .
            tep_href_link(FILENAME_POINT_EMAIL, 'page=' . $_GET['page'] . '&id='
                . $point_mail['id']) . '">' .
              tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
    } 
    ?>&nbsp;
    </td>
    </tr>
     
  <?php
}
?>
            </table>
			<table border="0" width="100%" cellspacing="0"
      cellpadding="0">
      <tr>
      <td class="smallText" valign="top"><?php echo
      $point_mail_split->display_count($point_mail_query_numrows,
          MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'],
          TEXT_DISPLAY_NUMBER_OF_POINT_EMAIL); ?></td>
      <td class="smallText" align="right"><div class="td_box"><?php echo
      $point_mail_split->display_links($point_mail_query_numrows,
          MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']);
  ?></div></td>
  </tr>
<?php
  if (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right">
					<div class="td_button">
                    <?php 
                    echo '<a href="'.tep_href_link(FILENAME_POINT_EMAIL, 'page=' .
                    $_GET['page'] .
                    '&action=new').'">'.tep_html_element_button(IMAGE_NEW_PROJECT).'</a>';
                    ?></div></td>
                  </tr>
<?php
  }
?>
                </table>
			</td>
<?php
  $heading = array();
  $contents = array();
  $explanation = TEXT_POINT_EMAIL_GLOBAL_TEXT;
  switch (isset($_GET['action'])?$_GET['action']:null) {
/* -----------------------------------------------------
   case 'new' 右侧新建点数邮件页面 
   case 'edit' 右侧编辑点数邮件页面  
   case 'delete' 右侧删除点数邮件页面  
   default 右侧默认页面
------------------------------------------------------*/
    case 'new':
      $heading[] = array('text' => TEXT_INFO_HEADING_NEW);

      $contents = array('form' => tep_draw_form('point_email_form', FILENAME_POINT_EMAIL, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
$contents[] = array('text' => '<input type="hidden" name="user_added" value="'.$user_info['name'].'">');
$contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');

    //point mail date
    $point_mail_inputs_string .= TEXT_INFO_POINT_MAIL_DATE .
      '<br>' . tep_draw_input_field('mail_date');

      $contents[] = array('text' => '<br>' . $point_mail_inputs_string);
      
      $contents[] = array('align' => 'center', 'text' => '<br><a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_SAVE, 'onclick="check_point_email_form(\''.$ocertify->npermission.'\');"').  '</a><a href="'.tep_href_link(FILENAME_POINT_EMAIL,'page=' .  $_GET['page']).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>');
      break;
    case 'edit':
      $heading[] = array('text' => TEXT_INFO_HEADING_EDIT_POINT_MAIL);

      $contents = array('form' => tep_draw_form('point_email_form', FILENAME_POINT_EMAIL, 'page=' . $_GET['page'] . '&id=' . $point_info->id  . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => '<input type="hidden" name="id"
          value="'.$point_info->id.'">');
      $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');

      $point_mail_inputs_string = '';

      //mail date
      $point_mail_inputs_string .= TEXT_INFO_POINT_MAIL_DATE .
        '<br>' . tep_draw_input_field('mail_date', $point_info->mail_date); 
      $contents[] = array('text' => '<br>'.$point_mail_inputs_string);
      $contents[] = array('align' => 'center' , 'text' => '<br><a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_SAVE, 'onclick="check_point_email_form(\''.$ocertify->npermission.'\');"').  '</a><a href="'.tep_href_link(FILENAME_POINT_EMAIL,'page=' .  $_GET['page'].'id='.$point_mail->id).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>');
      break;
    case 'delete':
      $heading[] = array('text' => TEXT_INFO_HEADING_DELETE);

      $contents = array('form' => tep_draw_form('point_email_form', FILENAME_POINT_EMAIL,
            'page=' . $_GET['page'] . '&id=' . $point_info->id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE);
      $contents[] = array('text' => '<br>' . $point_info->mail_date);
      $contents[] = array('text' => '<br>' .
          preg_replace("/\r\n|\n/",'<br>',$point_info->description));
      $contents[] = array('align' => 'center' , 'text' => '<br><a href="javascript:void(0);">' .  tep_html_element_button(IMAGE_DELETE, 'onclick="check_point_email_form(\''.$ocertify->npermission.'\')"').  '</a><a href="'.tep_href_link(FILENAME_POINT_EMAIL,'page=' .  $_GET['page'].'id='.$point_mail->id).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>');
      break;
    default:
  if (isset($point_info) and is_object($point_info)) {
        $heading[] = array('text' => TABLE_HEADING_MAIL_DATE.":"
            . $point_info->mail_date); 
        $contents[] = array('align' => 'center' ,
            'text' =>
            '<a href="'.tep_href_link(FILENAME_POINT_EMAIL, 'page=' . $_GET['page'] .'&id='.$point_info->id.'&action=edit').'">'.tep_html_element_button(IMAGE_EDIT).'</a>'.
            (($ocertify->npermission >= 15)?'<a href="'.tep_href_link(FILENAME_POINT_EMAIL, 'page=' .  $_GET['page'] .'&id='.$point_info->id.'&action=delete').'">'.tep_html_element_button(IMAGE_DELETE).'</a>':''));
      }
if(tep_not_null($point_info->user_added)){
$contents[] = array('text' =>  TEXT_USER_ADDED. ' ' .$point_info->user_added);
}else{
$contents[] = array('text' =>  TEXT_USER_ADDED. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null($point_info->created_at)){
$contents[] = array('text' =>  TEXT_DATE_ADDED. ' ' .tep_datetime_short($point_info->created_at));
}else{
$contents[] = array('text' =>  TEXT_DATE_ADDED. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null($point_info->user_update)){
$contents[] = array('text' =>  TEXT_USER_UPDATE. ' ' .$point_info->user_update);
}else{
$contents[] = array('text' =>  TEXT_USER_UPDATE. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null($point_info->updated_at)){
$contents[] = array('text' =>  TEXT_DATE_UPDATE. ' ' .tep_datetime_short($point_info->updated_at));
}else{
$contents[] = array('text' =>  TEXT_DATE_UPDATE. ' ' .TEXT_UNSET_DATA);
}

      break;
  }

  if ( (tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td width="25%" valign="top">' . "\n";

    $box = new box;
    echo $box->infoBox($heading, $contents);

    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table>
    </div> 
    </div>
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
