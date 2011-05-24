<?php
/*
  $Id$
*/
  require('includes/application_top.php');
  if (isset($_GET['action'])) {
    switch($_GET['action']){
      case 'insert':
      case 'save':
        $point_mail_date = tep_db_prepare_input($_POST['mail_date']);
        $point_mail_description = tep_db_prepare_input($_POST['description']);
        if($_GET['action'] == 'insert'){
           $sql_point_mail_array = array('mail_date' => $point_mail_date,
                                         'description' => $point_mail_description,
                                         'created_at' => 'now()',
                                         'updated_at' => 'now()');
           tep_db_perform(TABLE_POINT_MAIL,$sql_point_mail_array);
        }else if($_GET['action'] == 'save'){
           $sql_point_mail_array = array('mail_date' => $point_mail_date,
                                         'description' => $point_mail_description,
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
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
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
                <td class="dataTableHeadingContent"><?php echo
                TABLE_HEADING_DESCRIPTION; ?></td>
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
  if(isset($point_info) && (is_object($point_info)) && ($point_mail['id'] ==
        $point_info->id)){
    echo '<tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" 
      onclick="document.location.href=\'' . tep_href_link(FILENAME_POINT_EMAIL,
    'page=' . $_GET['page']. '&id=' . $point_info->id . '&action=edit') .
      '\'">' . "\n";
  }else{
    echo '<tr class="dataTableRow"
      onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'"
      onmouseout="this.className=\'dataTableRow\'"
      onclick="document.location.href=\'' . tep_href_link(FILENAME_POINT_EMAIL,
      'page=' . $_GET['page'] .'&id=' . $point_mail['id'] . '&action=edit') .'\'">' .
        "\n";
  }
  ?>
    <td class="dataTableContent" ><?php echo $point_mail['mail_date'];?></td>
    <td class="dataTableContent" ><?php echo $point_mail['description'];?></td>
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
  <tr>
      <td colspan="3"><table border="0" width="100%" cellspacing="0"
      cellpadding="2">
      <tr>
      <td class="smallText" valign="top"><?php echo
      $point_mail_split->display_count($point_mail_query_numrows,
          MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'],
          TEXT_DISPLAY_NUMBER_OF_POINT_EMAIL); ?></td>
      <td class="smallText" align="right"><?php echo
      $point_mail_split->display_links($point_mail_query_numrows,
          MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']);
  ?></td>
  </tr>
<?php
  if (!isset($_GET['action']) || substr($_GET['action'], 0, 3) != 'new') {
?>
                  <tr>
                    <td colspan="2" align="right">
                    <?php 
                    echo tep_html_button(IMAGE_INSERT,tep_href_link(FILENAME_POINT_EMAIL,
                          'page=' . $_GET['page'] . '&action=new'));
                    ?></td>
                  </tr>
<?php
  }
?>
                </table></td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  $explanation = TEXT_POINT_EMAIL_GLOBAL_TEXT;
  switch (isset($_GET['action'])?$_GET['action']:null) {
    case 'new':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW . '</b>');

      $contents = array('form' => tep_draw_form('status', FILENAME_POINT_EMAIL, 'page=' . $_GET['page'] . '&action=insert', 'post', 'enctype="multipart/form-data"'));
    //point mail date
    $point_mail_inputs_string .= '' . TEXT_INFO_POINT_MAIL_DATE .
      '<br>' . tep_draw_input_field('mail_date');
    
    //point mail description
    $point_mail_inputs_string .= '<br><br>' . TEXT_INFO_POINT_DESCRIPTION .
      '<br>' . tep_draw_textarea_field('description', 'soft', '25', '5').'<br>'.$explanation ;

      $contents[] = array('text' => '<br>' . $point_mail_inputs_string);
      
      $contents[] = array('align' => 'center', 'text' => '<br>' . 
          tep_html_submit(IMAGE_INSERT).
          tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_POINT_EMAIL,'page=' .
              $_GET['page'])));
      break;
    case 'edit':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_POINT_MAIL . '</b>');

      $contents = array('form' => tep_draw_form('status', FILENAME_POINT_EMAIL,
            'page=' . $_GET['page'] . '&id=' . $point_info->id  . '&action=save', 'post', 'enctype="multipart/form-data"'));
      $contents[] = array('text' => '<input type="hidden" name="id"
          value="'.$point_info->id.'">');

      $point_mail_inputs_string = '';

      //mail date
      $point_mail_inputs_string .= '' . TEXT_INFO_POINT_MAIL_DATE .
        '<br>' . tep_draw_input_field('mail_date', $point_info->mail_date);

      //mail description
      $point_mail_inputs_string .= '<br><br>' . TEXT_INFO_POINT_DESCRIPTION .
        '<br>' . tep_draw_textarea_field('description', 'soft', '25', '5',
            $point_info->description) .
        '<br>' . $explanation;
      $contents[] = array('text' => '<br>'.$point_mail_inputs_string);
      $contents[] = array('align' => 'center' , 'text' => '<br>' .
          tep_html_submit(IMAGE_EDIT).
          tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_POINT_EMAIL,'page=' .
              $_GET['page'].'id='.$point_mail->id)));
      break;
    case 'delete':
      $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE . '</b>');

      $contents = array('form' => tep_draw_form('status', FILENAME_POINT_EMAIL,
            'page=' . $_GET['page'] . '&id=' . $point_info->id  . '&action=deleteconfirm'));
      $contents[] = array('text' => TEXT_INFO_DELETE);
      $contents[] = array('text' => '<br><b>' . $point_info->mail_date . '</b>');
      $contents[] = array('text' => '<br><b>' .
          preg_replace("/\r\n|\n/",'<br>',$point_info->description) . '</b>');
      $contents[] = array('align' => 'center' , 'text' => '<br>' .
          tep_html_submit(IMAGE_DELETE).
          tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_POINT_EMAIL,'page=' .
              $_GET['page'].'id='.$point_mail->id)));
      break;
    default:
  if (isset($point_info) and is_object($point_info)) {
        $heading[] = array('text' => '<b>' .TABLE_HEADING_MAIL_DATE.":"
            . $point_info->mail_date . '</b>');
        $point_mail_inputs_string = '';
        $point_mail_inputs_string .= '<br><br>' . TEXT_INFO_POINT_DESCRIPTION .
        '<br><br>' .preg_replace("/\r\n|\n/",'<br>',$point_info->description) .
        '<br><br>' . $explanation;
        $contents[] = array('text' => $point_mail_inputs_string);
        $contents[] = array('align' => 'center' ,
            'text' =>
            tep_html_button(IMAGE_EDIT,tep_href_link(FILENAME_POINT_EMAIL, 'page='
                . $_GET['page'] .'&id='.$point_info->id.'&action=edit')).
            tep_html_button(IMAGE_DELETE,tep_href_link(FILENAME_POINT_EMAIL, 'page='
                . $_GET['page'] .'&id='.$point_info->id.'&action=delete')));
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
