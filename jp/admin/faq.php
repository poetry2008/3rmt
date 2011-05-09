<?php
/*
   $Id$
   
   GM的FAQ管理
*/
  require('includes/application_top.php');

  //define('FILENAME_FAQ', 'faq.php');
  define('TABLE_FAQ_CATEGORIES', 'faq_categories');
  define('TABLE_FAQ_QUESTIONS',  'faq_questions');

  //require(DIR_WS_LANGUAGES . $language . '/' .  FILENAME_FAQ);
  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
      case 'delete_faq_category_confirm':

        break;
      case 'insert_faq':
      case 'update_faq':
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         //   $edit_per=editPermission($site_arr, $site_id);//判断是否拥有相应网站的管理权限
         forward401Unless(editPermission($site_arr, $site_id));
        $question = tep_db_prepare_input($_POST['question']);
        $answer = tep_db_prepare_input($_POST['answer']);
        $faq_order = tep_db_prepare_input($_POST['faq_order']);
        $sql_data_array = array('question' => $question,
            'answer' => $answer,
            'faq_order' => $faq_order?$faq_order:0,
            );
        if($_GET['action'] == 'insert_faq'){
          $faq_category_id = tep_db_prepare_input($_POST['faq_category_id']);
          $insert_sql_data = array('faq_category_id' => $faq_category_id);
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_FAQ_QUESTIONS, $sql_data_array);
          $faq_id = tep_db_insert_id();
        }else if($_GET['action'] == 'update_faq'){
          $faq_id = tep_db_prepare_input($_POST['faq_id']);
          tep_db_perform(TABLE_FAQ_QUESTIONS, $sql_data_array, 'update','faq_id
              =\''.$faq_id.'\'');
        }
        tep_redirect(tep_href_link(FILENAME_FAQ,
              'cPath='.$cPath.'&qID='.$faq_id.'&site_id='.$site_id));
        break;
      case 'insert_faq_category':
      case 'update_faq_category':
        $faq_id = tep_db_prepare_input($_POST['id']);
        $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         //   $edit_per=editPermission($site_arr, $site_id);//判断是否拥有相应网站的管理权限
         forward401Unless(editPermission($site_arr, $site_id));
        $faq_name = tep_db_prepare_input($_POST['name']);
        $faq_order = tep_db_prepare_input($_POST['order']);
        if($site_id){
        $sql_data_array = array('site_id' => $site_id,'name' => $faq_name);
        }else{
        $sql_data_array = array('name' => $faq_name);
        }
        if($_GET['action'] == 'insert_faq_category'){
          $faq_order = tep_db_prepare_input($_POST['order']);
          $faq_parent_id = tep_db_prepare_input($_POST['parent_id'])?
            tep_db_prepare_input($_POST['parent_id']):0;
          $insert_sql_data = array(
              'sort_order' => $faq_order,
              'parent_id' => $faq_parent_id,
              'created_at' => 'now()');
          $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
          tep_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array);
          $faq_id = tep_db_insert_id();
        }else if($_GET['action'] == 'update_faq_category'){
          $update_sql_data = array(
              'sort_order' => $faq_order,
              'updated_at' => 'now()');
          $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
          tep_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array, 'update','id
              =\''.$faq_id.'\'');
        }
        tep_redirect(tep_href_link(FILENAME_FAQ,
              'cPath='.$cPath.'&cID='.$faq_id.'&site_id='.$site_id));
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
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
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
            <td class="pageHeading" align="right">
            <?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
            </td>
            <td class="smallText" align="right">
            <?php 
            //search form for faq
            echo tep_draw_form('search',FILENAME_FAQ,'','get')."\n";
            ?>
            <div class="faq_search">
            <?php
            echo tep_draw_hidden_field('site_id',isset($_GET['site_id'])?$_GET['site_id']:'0');
            echo HEADING_TITLE_SEARCH. ' ' .
            tep_draw_input_field('search',isset($_GET['search'])?$_GET['search']:'');
            ?>
            </div>
            </form>
            </td>
            <td class="smallText" align="right">
            <div class="faq_gotomenu">
              goto menu
            </div>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td ><div ><?php tep_site_filter(FILENAME_FAQ);?></div></td>
              </tr>
              <tr>
                <td >
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <?php /* faq infor table */?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent">
                      <?php echo TABLE_FAQ_CATEGORY_NAME; ?>
                    </td>
                    <td class="dataTableHeadingContent" align="right">
                      <?php echo TABLE_HEADING_ACTION;?>&nbsp;
                    </td>
                  </tr>
                  <?php 
                  // faq category list
                  $faq_category_count = 0;
                  $rows = 0;
                  if(isset($_GET['search']) && $_GET['search']) {
                    $faq_category_query_raw = "
                      select `id`,`parent_id`,`sort_order`,`name`,`site_id`
                          ,`created_at`,`updated_at`
                          from ".TABLE_FAQ_CATEGORIES." 
                          where `name` like '%".$_GET['search']."%'  
                          and site_id ='".((isset($_GET['site_id']) &&
                          $_GET['site_id'])?$_GET['site_id']:0)."' 
                          order by `sort_order` DESC";
                  }else{
                    $faq_category_query_raw = "
                      select `id`,`parent_id`,`sort_order`,`name`,`site_id`
                          ,`created_at`,`updated_at`
                          from ".TABLE_FAQ_CATEGORIES." 
                          where site_id ='".((isset($_GET['site_id']) &&
                          $_GET['site_id'])?$_GET['site_id']:0)."' 
                          and `parent_id`='".$current_category_id."'
                          order by `sort_order` DESC";

                  }
                  $faq_category_query = tep_db_query($faq_category_query_raw);
                  while($faq_category = tep_db_fetch_array($faq_category_query)){
                    $faq_count++;
                    $rows++;

                    $faq_info_arr =
                      array('id','parent_id','sort_order','name','site_id');
                    if(isset($_GET['search']) && $_GET['search']){
                        $cPath= $categories['parent_id'];
                    }
                    if(
                        ((!isset($_GET['cID']) || !$_GET['cID']) &&
                         (!isset($_GET['qID']) || !$_GET['qID']) ||
                         (isset($_GET['cID']) && $_GET['cID'] == $faq_category['id'])) 
                         && (!isset($faq_info) || !$faq_info) 
                         && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_')
                      ){
                    $faq_info = new objectInfo($faq_category);
                    }

                    // row color 
                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if(isset($newColor) && $newColor == $odd) {
                      $nowColor = $even;
                    }else{
                      $nowColor = $odd;
                    }

                    if((isset($faq_info)&&is_object($faq_info))&&
                        ($faq_category['id'] == $faq_info->id)){
                      echo '<tr class="dataTableRowSelected" 
                        onmouseover="this.style.cursor=\'hand\'" 
                        onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,
                        tep_get_faq_path($faq_category['id']).
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        . '\'">' . "\n";

                    }else{
                      echo '<tr class="'.$nowColor.'" 
                        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" 
                        onmouseout="this.className=\''.$nowColor.'\'" 
                        onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.
                          $cPath.(isset($_GET['page'])&&$_GET['page']?('&page='.$_GET['page']):'').
                          '&cID='.$faq_category['id'].'&site_id='.
                          ((isset($_GET['site_id'])?$_GET['site_id']:0))).
                          '\'">'."\n";
                    }

                    echo '<td class="dataTableContent">';
                    echo '<a
                      href="'.tep_href_link(FILENAME_FAQ,
                        tep_get_faq_path($faq_category['id']).'&site_id='.
                      ((isset($_GET['site_id'])?$_GET['site_id']:0))). '">' .
                      tep_image(DIR_WS_ICONS.'folder.gif',ICON_FOLDER) .'</a>&nbsp;
                    <b>'.$faq_category['name'].'</b>';
                    echo '</td>';
                    echo '<td class="dataTableContent" align="right">';
                    if((isset($faq_info)&&is_object($faq_info))&&
                        ($faq_category['id'] == $faq_info->id)){
                      echo tep_image(DIR_WS_IMAGES.'icon_arrow_right.gif','');
                    }else{
                      echo '<a
                        href="'.tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                        '&cID='.$faq_category['id'].
                        '&site_id'.((isset($_GET['site_id'])?$_GET['site_id']:0))).
                        '">'.tep_image(DIR_WS_IMAGES . 'icon_info.gif',
                        IMAGE_ICON_INFO) . '</a>';
                    }
                    echo '&nbsp;</td>';
                    echo '</tr>';
                  }
                  $faq_count=0;
                  if(isset($_GET['search']) && $_GET['search']) {
                    $faq_query_raw = "select 
                      faq_id,faq_category_id,faq_order,question,answer,site_id 
                      from ".TABLE_FAQ_QUESTIONS." 
                      where question like '%".$_GET['search']."%' 
                      and site_id ='".((isset($_GET['site_id']) &&
                      $_GET['site_id'])?$_GET['site_id']:0)."' 
                      order by faq_order ";
                  }else{
                    $faq_query_raw = "select 
                      faq_id,faq_category_id,faq_order,question,answer,site_id  
                      from ".TABLE_FAQ_QUESTIONS." 
                      where faq_category_id = '".$current_category_id."'  
                      and site_id ='".((isset($_GET['site_id']) &&
                      $_GET['site_id'])?$_GET['site_id']:0)."' 
                      order by faq_order ";
                  }
                  define(MAX_DISPLAY_FAQ_ADMIN,5);
                  $faq_split = new splitPageResults($_GET['page'],MAX_DISPLAY_FAQ_ADMIN,
                      $faq_query_raw,$faq_query_number);
                  $_faq_query = tep_db_query($faq_query_raw);
                  while($_faq_info = tep_db_fetch_array($_faq_query)){
                    $faq_count++;
                    $rows++;

                    if(isset($_GET['search'])&&$_GET['search']){
                      $cPath=$_faq_info['faq_category_id'];
                    }
                    if(
                        ((!isset($_GET['qID']) || !$_GET['qID']) &&
                         (!isset($_GET['cID']) || !$_GET['cID']) ||
                         (isset($_GET['qID']) && $_GET['qID'] ==
                          $_faq_info['faq_id']))
                        && (!isset($qInfo) || !$qInfo)
                        && (!isset($faq_info) || !$faq_info)
                        && (!isset($_GET['action']) || substr($_GET['action'], 0, 4)
                          != 'new_')
                      ){
                      $qInfo = new objectInfo($_faq_info);
                    }

                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if(isset($newColor) && $newColor == $odd) {
                      $nowColor = $even;
                    }else{
                      $nowColor = $odd;
                    }

                    if((isset($qInfo)&&is_object($qInfo))&&
                        ($_faq_info['faq_id'] == $qInfo->faq_id)){
                      echo '<tr class="dataTableRowSelected" 
                        onmouseover="this.style.cursor=\'hand\'" 
                        onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .
                        '&qID='.$_faq_info['faq_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        . '\'">' . "\n";

                    }else{
                      echo '<tr class="'.$nowColor.'" 
                        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" 
                        onmouseout="this.className=\''.$nowColor.'\'" 
                        onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .
                        '&qID='.$_faq_info['faq_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        .  '\'">'."\n";
                    }
                      echo "<td class='dataTableContent'>";
                      echo "<b>".$_faq_info['question']."</b>";
                      echo "</td>";
                      echo '<td class="dataTableContent" align="right">';
                      if(isset($qInfo)&&(is_object($qInfo))&&
                            $_faq_info['faq_id'] ==  $qInfo->faq_id){
                        echo tep_image(DIR_WS_IMAGES.'icon_arrow_right.gif', '');
                      }else{
                        echo '<a href="'.tep_href_link(FILENAME_FAQ,
                             'cPath='.$cPath.'&qID='.$_faq_info['faq_id'].'&site_id='.
                               ((isset($_GET['site_id'])?$_GET['site_id']:0))).'">'.
                               tep_image(DIR_WS_IMAGES . 'icon_info.gif',
                                   IMAGE_ICON_INFO) . '</a>';
                      }
                      echo "&nbsp;</td>";
                      echo "</tr>";
                  }

                  ?>
                </table>
                </td>
              </tr>
              <tr>
                <td align="right" class="smallText">
                  <?php 
    //path array to  path_back
    if ($cPath_array) {
      $cPath_back = '';
      for($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
        if ($cPath_back == '') {
          $cPath_back .= $cPath_array[$i];
        } else {
          $cPath_back .= '_' . $cPath_array[$i];
        }    
      }    
    }    

    $cPath_back = isset($cPath_back) && $cPath_back ? 'cPath=' . $cPath_back : '';
                  // new faq category and new faq button
                  if($cPath){
                    echo tep_html_button(IMAGE_BACK,
                      tep_href_link(FILENAME_FAQ,$cPath_back.'&cID='.$current_category_id.
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))));
                    echo '&nbsp;';
                  }
                  if((!isset($_GET['search'])||!$_GET['search'])&&
                      $ocertify->npermission >= 10){
                    echo tep_html_button(IMAGE_NEW_FAQ_CATEGORY,tep_href_link(FILENAME_FAQ,
                            'cPath='.$cPath.'&action=new_faq_category'.'&site_id='.$site_id));
                    echo '&nbsp;';
                    echo tep_html_button(IMAGE_NEW_FAQ,tep_href_link(FILENAME_FAQ,
                          'cPath='.$cPath.'&action=new_faq'.'&site_id='.$site_id));
                  }


                  ?>
                </td>
              </tr>
            </table></td>
<?php
  $heading = array();
  $contents = array();
  // faq right info
switch (isset($_GET['action'])? $_GET['action']:'') {
  default:
    //default right
    if($rows >0 ){
      if(isset($faq_info)&&is_object($faq_info)){
        $heading[] = array('text' => '<b>'.$faq_info->name.'</b>');
        if($ocertify->npermission >= 10 ){  // about permission
           if(empty($_GET['site_id'])){
             $contents[] = array(
                'align' => 'left',
                'text' => tep_html_button(IMAGE_EDIT,
                  tep_href_link(FILENAME_FAQ,'cPath='.$cPath.'&cID='.
                    $faq_info->id.'&action=edit_faq_category'))
                  .($ocertify->npermission == 15 ? (tep_html_button(IMAGE_DELETE,
                  tep_href_link(FILENAME_FAQ,'cPath='.$cPath.'&cID='.
                    $faq_info->id.'&action=delete_faq_category'))):'').
                  tep_html_button(IMAGE_MOVE,
                  tep_href_link(FILENAME_FAQ,'cPath='.$cPath.'&cID='.
                    $faq_info->id.'&action=move_faq_category'))
                 );
           }
           if(isset($_GET['site_id'])){
           foreach(tep_get_sites() as $site){
             if($site['id']!= $_GET['site_id']){
               continue;
             }
             $contents[] = array('text' => '<b>' . $site['romaji'] . '</b>');
             $contents[] = array(
                'align' => 'left',
                'text' => tep_html_button(IMAGE_EDIT,
                  tep_href_link(FILENAME_FAQ,'cPath'.$cPath.'&cID='.$faq_info->id.
                    '&action=edit_faq_category&site_id='.$site['id'])).
                  tep_html_button(IMAGE_DELETE,
                  tep_href_link(FILENAME_FAQ,'cPath'.$cPath.'&cID='.$faq_info->id.
                    '&action=delete_faq_category&site_id='.$site['id']))
                );
           }
           }
        }
      }else if(isset($qInfo)&&is_object($qInfo)){
        if($ocertify->npermission >= 10){
          if (empty($_GET['site_id'])) {
            $heading[] = array('text' => '<b>'.TEXT_FAQ_INFO.'</b>');
            $contents[] = array('text' => '<b>'.TEXT_FAQ_Q.'</b><br>'.$qInfo->question);
            $contents[] = array('text' => '<b>'.TEXT_FAQ_A.'</b><br>'.
                tep_draw_textarea_field('answer','','50','5',$qInfo->answer));
            $contents[] = array('text' =>
                tep_html_button(IMAGE_EDIT,tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                    '&qID='.$qInfo->faq_id.'&action=edit_faq')).
                tep_html_button(IMAGE_DELETE,tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                    '&qID='.$qInfo->faq_id.'&action=delete_faq')).
                tep_html_button(IMAGE_MOVE,tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                    '&qID='.$qInfo->faq_id.'&action=move_faq')).
                tep_html_button(IMAGE_COPY_TO,tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                    '&qID='.$qInfo->faq_id.'&action=copy_to'))
                  );
          }
          if(isset($_GET['site_id'])){
            foreach(tep_get_sites() as $site){
              if($site['id'] != $_GET['site_id']){
                continue;
              }
              $heading[] = array('text' => '<b>'.$site['romaji'].TEXT_FAQ_INFO.'</b>');
              $contents[] = array('text' => '<b>'.TEXT_FAQ_Q.'</b><br>'.$qInfo->question);
              $contents[] = array('text' => '<b>'.TEXT_FAQ_A.'</b><br>'.
                tep_draw_textarea_field('answer','','50','5',$qInfo->answer));
            $contents[] = array('text' =>
                tep_html_button(IMAGE_EDIT,tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                    '&site_id='.$site['id'].'&qID='.$qInfo->faq_id.'&action=edit_faq')).
                tep_html_button(IMAGE_DELETE,tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                    '&site_id='.$site['id'].'&qID='.$qInfo->faq_id.'&action=delete_faq')).
                tep_html_button(IMAGE_MOVE,tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                    '&site_id='.$site['id'].'&qID='.$qInfo->faq_id.'&action=move_faq')).
                tep_html_button(IMAGE_COPY_TO,tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                    '&site_id='.$site['id'].'&qID='.$qInfo->faq_id.'&action=copy_to'))
                  );
            }
          }
        }
      }
    }
    break;
  case 'edit_faq_category':
      $site_id = isset($_GET['site_id'])?$_GET['site_id']:0;
      $heading[] = array('text' =>
          '<b>'.TEXT_INFO_HEADING_EDIT_FAQ_CATEGORY.'</b>');
      $contents = array('form' => tep_draw_form('edit_faq_categories',FILENAME_FAQ,
            'action=update_faq_category&cPath='.$cPath,'post',
            'onSubmit="return cmess();"').
          tep_draw_hidden_field('id',$faq_info->id));
      $contents[] = array('text' =>
          TEXT_EDIT_INFO.($site_id?('<br><b>'.tep_get_site_name_by_id($site_id).'</b>'):''));
      $contents[] = array('text' => tep_draw_hidden_field('site_id',$site_id));
      $contents[] = array('text' => TEXT_FAQ_CATEGORY_NAME.'<br>'.
          tep_draw_input_field('name',$faq_info->name));
      $contents[] = array('align' => 'center', 'text' => '<br>'.
          tep_html_submit(IMAGE_SAVE).tep_html_button(IMAGE_CANCEL,
            tep_href_link(FILENAME_FAQ,'cPath='.$cPath.'&cID='.$faq_info->id.'&site_id='.$site_id)));
      
    break;
  case 'delete_faq_category':
    $heading[] = array('text' => '<b>'. TEXT_INFO_HEADING_DELETE_FAQ_CATEGORY.
        '</b>');
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    $contents = array('form' => tep_draw_form('delete_faq_categories',FILENAME_FAQ,
            'action=delete_faq_category_confirm&cPath='.$cPath.$dc_page,'post').
          tep_draw_hidden_field('id',$faq_info->id));
    $contents[] = array('text' => TEXT_DELETE_FAQ_CATEGORY_INTOR);
    $contents[] = array('text' => '<br><b>'.$faq_info->name.'</b>');
    $contents[] = array('align' => 'center', 'text' => '<br>'.
        tep_html_submit(IMAGE_DELETE).tep_html_button(IMAGE_CANCEL,
          tep_href_link(FILENAME_FAQ,'cPath='.$cPath.'&cID='.$faq_info->id.
            '&site_id='.$site_id.$dc_page)));
    break;
  case 'new_faq_category':
    $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_NEW_FAQ_CATEGORY.'</b>');
    $contents = array('form' => tep_draw_form('new_faq_categories',FILENAME_FAQ,
            'action=insert_faq_category&cPath='.$cPath,'post',
            'onSubmit="return cmess();"')
        .tep_draw_hidden_field('site_id',$site_id)
        .tep_draw_hidden_field('parent_id',$current_category_id));
    $contents[] = array('text' => TEXT_FAQ_CATEGORY_NAME.'<br>'.
        tep_draw_input_field('name'));
    $contents[] = array('text' => TEXT_FAQ_CATEGORY_ORDER.'<br>'.
        tep_draw_input_field('order','','size="2"'));
    $contents[] = array('align'=>'center',
        'text' => tep_html_submit(IMAGE_SAVE).'&nbsp;'.
        tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_FAQ,'cPath='.$cPath)));
    break;
  case 'new_faq':
    $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_NEW_FAQ.'</b>');
    $contents = array('form' => tep_draw_form('new_faq_categories',FILENAME_FAQ,
            'action=insert_faq&cPath='.$cPath,'post',
            'onSubmit="return cmess();"')
        .tep_draw_hidden_field('faq_category_id',$current_category_id));
    $contents[] = array('text'=> TEXT_FAQ_Q.'<br>'.
        tep_draw_input_field('question'));
    $contents[] = array('text'=> TEXT_FAQ_A.'<br>'.
        tep_draw_textarea_field('answer','','50','5',''));
    $contents[] = array('text' => TEXT_FAQ_ORDER.'<br>'.
        tep_draw_input_field('faq_order','','size="2"'));
    $contents[] = array('align'=>'center',
        'text' => tep_html_submit(IMAGE_SAVE).'&nbsp;'.
        tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_FAQ,'cPath='.$cPath)));
    break;
  case 'edit_faq':
    $heading[] = array('text' => '<b>'.TEXT_INFO_HEADING_EDIT_FAQ.'</b>');
    $contents = array('form' => tep_draw_form('new_faq_categories',FILENAME_FAQ,
            'action=update_faq&cPath='.$cPath,'post',
            'onSubmit="return cmess();"')
        .tep_draw_hidden_field('faq_id',$qInfo->faq_id));
    $contents[] = array('text'=> TEXT_FAQ_Q.'<br>'.
        tep_draw_input_field('question',$qInfo->question));
    $contents[] = array('text'=> TEXT_FAQ_A.'<br>'.
        tep_draw_textarea_field('answer','','50','5',$qInfo->answer));
    $contents[] = array('text' => TEXT_FAQ_ORDER.'<br>'.
        tep_draw_input_field('faq_order',$qInfo->faq_order,'size="2"'));
    $contents[] = array('align'=>'center',
        'text' => tep_html_submit(IMAGE_SAVE).'&nbsp;'.
        tep_html_button(IMAGE_CANCEL,tep_href_link(FILENAME_FAQ,'cPath='.$cPath)));
    break;
  }

  if ((tep_not_null($heading)) && (tep_not_null($contents)) ) {
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
