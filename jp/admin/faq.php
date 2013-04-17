<?php
/*
   $Id$
   
   GM的FAQ管理
*/
  require('includes/application_top.php');


  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
/* -----------------------------------------------------
   case 'toggle' 设置faq分类的状态 
   case 'setflag' 设置faq问题的状态 
   case 'delete_faq_category_confirm' 删除faq分类 
   case 'delete_faq_question_confirm' 删除faq问题 
   case 'insert_faq_question' 新建faq问题 
   case 'update_faq_question' 更新faq问题 
   case 'insert_faq_category' 新建faq分类 
   case 'update_faq_category' 更新faq分类 
------------------------------------------------------*/
        case 'toggle':
        if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
        else $site_arr="";
        $site_id = (isset($_GET['site_id']))?$_GET['site_id']:0;
        forward401Unless(editPermission($site_arr, $site_id));
        tep_insert_pwd_log($_GET['once_pwd'],$ocertify->auth_user);
        $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
        if($_GET['cID']&&isset($_GET['status'])&&
            ($_GET['status']==1||$_GET['status']==0)
            &&$site_id != 0){
          tep_set_faq_category_link_question_status($cID, $_GET['status'], $site_id);
        }
        tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' .
              $HTTP_GET_VARS['cPath'].'&site_id='
              .((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page));
        break;
      case 'setflag':
        if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
        else $site_arr="";
        forward401Unless(editPermission($site_arr, $site_id));
        tep_insert_pwd_log($_GET['once_pwd'],$ocertify->auth_user);
        $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';

        $site_id = (isset($_GET['site_id']))?$_GET['site_id']:0;
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['qID']&&$site_id!=0) {
            tep_set_faq_question_status_by_site_id($_GET['qID'], $_GET['flag'],
                $site_id);
          }
        }
        tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' .$_GET['cPath'].
              '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)).$p_page));
        break;
      case 'delete_faq_category_confirm':
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         forward401Unless(editPermission($site_arr, $site_id));
         $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
         $dc_site = (isset($_POST['site_id']))?'&site_id='.$_POST['site_id']:'';
         if ($_POST['faq_category_id']) {
          $faq_category_id = tep_db_prepare_input($_POST['faq_category_id']);
          tep_set_time_limit(0);
          $categories = tep_get_faq_category_tree($faq_category_id, '', '0', '', true);
          $questions = array();
          $questions_delete = array();

          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            $question_ids_query = tep_db_query("select faq_category_id from " .
                TABLE_FAQ_QUESTION_TO_CATEGORIES . "
                where faq_category_id = '" . $categories[$i]['id'] . "'");
            while ($question_ids = tep_db_fetch_array($question_ids_query)) {
              $questions[$question_ids['faq_category_id']]['categories'][] =
                $categories[$i]['id'];
            }
          }
          reset($questions);
          while (list($key, $value) = each($questions)) {
            $category_ids = '';
            for ($i = 0, $n = sizeof($value['categories']); $i < $n; $i++) {
              $category_ids .= '\'' . $value['categories'][$i] . '\', ';
            }
            $category_ids = substr($category_ids, 0, -2);

            $check_query = tep_db_query("select count(*) as total from " .
                TABLE_FAQ_QUESTION_TO_CATEGORIES . " where faq_question_id = '" .
                $key . "' and faq_category_id not in (" . $category_ids . ")");
            $check = tep_db_fetch_array($check_query);
            if ($check['total'] < '1') {
              $questions_delete[$key] = $key;
            }
          }
          for ($i = 0, $n = sizeof($categories); $i < $n; $i++) {
            tep_remove_faq_category($categories[$i]['id']);
          }

          reset($questions_delete);
          while (list($key) = each($questions_delete)) {
            tep_remove_faq_question($key);
          }
         }
         tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' .
               $cPath.$dc_page.$dc_site));
         break;
      case 'delete_faq_question_confirm':
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         forward401Unless(editPermission($site_arr, $site_id));
         $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
         $d_site = (isset($_POST['site_id']))?'&site_id='.$_POST['site_id']:'';
         if(($_POST['faq_question_id']) && (is_array($_POST['question_categories']))){
           $faq_question_id = tep_db_prepare_input($_POST['faq_question_id']);
           $question_categories = $_POST['question_categories'];
           for ($i = 0, $n = sizeof($question_categories); $i < $n; $i++) {
            tep_db_query("delete from " . 
                TABLE_FAQ_QUESTION_TO_CATEGORIES . " 
                where faq_question_id = '" . tep_db_input($faq_question_id) . "' 
                and faq_category_id = '" . tep_db_input($question_categories[$i]) . "'");
           }
           $question_categories_query = tep_db_query("select count(*) as total from " .
               TABLE_FAQ_QUESTION_TO_CATEGORIES . 
               " where faq_question_id = '" . tep_db_input($faq_question_id) . "'");
           $question_categories = tep_db_fetch_array($question_categories_query);
           if ($question_categories['total'] == '0') {
             tep_remove_faq_question($faq_question_id);
           }
         }
         tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath.$d_page.$d_site));
         break;
      case 'insert_faq_question':
      case 'update_faq_question':
         $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         forward401Unless(editPermission($site_arr, $site_id));
         $faq_question_id = tep_db_prepare_input($_POST['faq_question_id']);
         $sort_order = tep_db_prepare_input($_POST['sort_order']);
         $sql_data_array = array('sort_order' => $sort_order);

         if($_GET['action'] == 'insert_faq_question') {
           $insert_sql_data = array('updated_at' => 'now()',
		                    'user_update' => $_POST['user_update'],
				    'user_added' => $_POST['user_added'],
                                    'created_at' => 'now()');
           $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
           tep_db_perform(TABLE_FAQ_QUESTION, $sql_data_array);
           $faq_question_id = tep_db_insert_id();
           $faq_c2q_arr = array('faq_category_id' => $current_category_id,
                                'faq_question_id' => $faq_question_id);
           tep_db_perform(TABLE_FAQ_QUESTION_TO_CATEGORIES, $faq_c2q_arr);
         }else{
           $update_sql_data = array('updated_at' => 'now()','user_update' => $_POST['user_update']);
           $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
           tep_db_perform(TABLE_FAQ_QUESTION, $sql_data_array, 'update',
               'id = \'' . $faq_question_id . '\'');
         }
         $sql_data_array = array(
            'romaji' => str_replace(array('/','_'),'-',tep_db_prepare_input($_POST['romaji'])),
            'ask' => tep_db_prepare_input($_POST['ask']),
            'keywords' => tep_db_prepare_input($_POST['keywords']),
            'answer' => tep_db_prepare_input($_POST['answer']),
              );
         if($_GET['action'] == 'insert_faq_question' || ($_GET['action'] ==
               'update_faq_question' &&
               !tep_faq_question_description_exist($faq_question_id,$site_id))) {
           if($_GET['action'] == 'insert_faq_question'){
            $insert_sql_data = array('faq_question_id' => $faq_question_id,
                                     'site_id' => $site_id);
           }else{
            $insert_sql_data = array('faq_question_id' => $faq_question_id,
                                     'site_id' => $site_id);
           }
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $row_faq_sql = "select * from  
              ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
              ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
              where fqd.faq_question_id=fq2c.faq_question_id and
              fq2c.faq_category_id='".$current_category_id."'      and
              fqd.romaji='".$sql_data_array['romaji']."' and
              fqd.site_id='".$site_id."'";
            if(tep_db_num_rows(tep_db_query($row_faq_sql))){
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
            tep_db_perform(TABLE_FAQ_QUESTION_DESCRIPTION, $sql_data_array);
         }else{
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $row_faq_sql = "select * from  
              ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd,
              ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
              where fqd.faq_question_id=fq2c.faq_question_id and
              fq2c.faq_category_id='".$current_category_id."'      and
              fqd.romaji='".$sql_data_array['romaji']."' and
              fqd.site_id='".$site_id."' and 
              fqd.faq_question_id != '".$faq_question_id."'";
            if(tep_db_num_rows(tep_db_query($row_faq_sql))){
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
           tep_db_perform(TABLE_FAQ_QUESTION_DESCRIPTION, $sql_data_array,
               'update','faq_question_id =\''.$faq_question_id.'\' and site_id =
               \''.$site_id.'\'');

         }
            set_time_limit(0);
            $page_sort_order = tep_get_rownum_faq_question($current_category_id,$faq_question_id,
                $site_id,$_GET['search']);
            $select_page = intval((intval($page_sort_order)-1)/MAX_DISPLAY_FAQ_ADMIN)+1;
         if(isset($_GET['rdirect'])){
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&qID=' . $faq_question_id.'&site_id=0'.'&page='.$select_page));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .'&qID=' .
                 $faq_question_id.'&site_id='.$site_id.'&page='.$select_page));
         }

                break;
      case 'insert_faq_category':
      case 'update_faq_category':
        $site_id = isset($_POST['site_id'])?$_POST['site_id']:0;
         if(isset($_SESSION['site_permission'])) $site_arr=$_SESSION['site_permission'];
         else $site_arr="";
         forward401Unless(editPermission($site_arr, $site_id));
         $faq_category_id = tep_db_prepare_input($_POST['faq_category_id']);
         $sort_order = tep_db_prepare_input($_POST['sort_order']);
         $sql_data_array = array('sort_order' => $sort_order);

         if($_GET['action'] == 'insert_faq_category') {
           $insert_sql_data = array('parent_id' => $current_category_id,
		                    'user_added'=> $_POST['user_added'],
				    'user_update'=>$_POST['user_update'],
                                    'updated_at' => 'now()',
                                    'created_at' => 'now()');
           $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
           tep_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array);
           $faq_category_id = tep_db_insert_id();
         }else{
           $update_sql_data = array('updated_at' => 'now()','user_update' => $_POST['user_update']);
           $sql_data_array = tep_array_merge($sql_data_array, $update_sql_data);
           tep_db_perform(TABLE_FAQ_CATEGORIES, $sql_data_array, 'update',
               'id = \'' . $faq_category_id . '\'');
         }
         $sql_data_array = array(
            'romaji' => str_replace(array('/','_'),'-',tep_db_prepare_input($_POST['romaji'])),
            'title' => tep_db_prepare_input($_POST['title']),
            'keywords' => tep_db_prepare_input($_POST['keywords']),
            'description' => tep_db_prepare_input($_POST['description']),
              );
         if($_GET['action'] == 'insert_faq_category' || ($_GET['action'] ==
               'update_faq_category' &&
               !tep_faq_categories_description_exist($faq_category_id,$site_id))) {
            if($_GET['action'] == 'insert_faq_category'){
            $insert_sql_data = array('faq_category_id' => $faq_category_id,
                                     'site_id' => $site_id);
            }else{
            $insert_sql_data = array('faq_category_id' => $faq_category_id,
                                     'site_id' => $site_id);
            }
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $row_faq_sql = "select * from ".TABLE_FAQ_CATEGORIES." fc,
              ".TABLE_FAQ_CATEGORIES_DESCRIPTION." 
              fcd where fc.id=fcd.faq_category_id and
              fc.parent_id='".$current_category_id."'      and
              fcd.romaji='".$sql_data_array['romaji']."' and
              fcd.site_id='".$site_id."'";
            if(tep_db_num_rows(tep_db_query($row_faq_sql))){
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $sql_data_array = tep_array_merge($sql_data_array, $insert_sql_data);
            tep_db_perform(TABLE_FAQ_CATEGORIES_DESCRIPTION, $sql_data_array);
         }else{
            if(!tep_check_romaji($sql_data_array['romaji'])){
              $messageStack->add_session(TEXT_ROMAJI_ERROR, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
            $row_faq_sql = "select * from ".TABLE_FAQ_CATEGORIES." fc,
              ".TABLE_FAQ_CATEGORIES_DESCRIPTION." 
              fcd where fc.id=fcd.faq_category_id and
              fc.parent_id='".$current_category_id."'      and
              fcd.romaji='".$sql_data_array['romaji']."' and
              fcd.site_id='".$site_id."' and 
              fc.id != '".$faq_category_id."'";
            if(tep_db_num_rows(tep_db_query($row_faq_sql))){
              $messageStack->add_session(TEXT_ROMAJI_EXISTS, 'error');
              tep_redirect(tep_href_link(FILENAME_FAQ));
            }
           tep_db_perform(TABLE_FAQ_CATEGORIES_DESCRIPTION, $sql_data_array,
               'update','faq_category_id =\''.$faq_category_id.'\' and site_id =
               \''.$site_id.'\'');

         }
         if(isset($_GET['rdirect'])){
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&cID=' . $faq_category_id.'&site_id=0'));
         }else{
           tep_redirect(tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath .
                 '&cID=' . $faq_category_id.'&site_id='.$site_id));
         }
        break;
    }
  }

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title>
<?php 
$site_id = isset($_GET['site_id']) ? $_GET['site_id']:0;
  if((isset($_GET['cPath']) && $_GET['cPath']=="" && isset($_GET['cID']) && $_GET['cID']!="")){
  echo HEADING_TITLE;
  }else if(isset($_GET['cPath']) && $_GET['cPath']!=""){
$faq_query = tep_db_query("select title from ".TABLE_FAQ_CATEGORIES_DESCRIPTION." where faq_category_id='".$_GET['cPath']."'");
 $faq_array = tep_db_fetch_array($faq_query);
  echo HEADING_TITLE.$faq_array['title']; 
  }else{
 echo   HEADING_TITLE;
  }
?>
</title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript">
<?php //显示/关闭分类树?>
function display(){
    var categories_tree = document.getElementById('categories_tree'); 
    offset = $("#categories_block").offset();
      if(categories_tree.style.display == 'none'){
           categories_tree.style.top = offset.top + 'px';
           categories_tree.style.display = 'block';
              }else{
                    categories_tree.style.display = 'none';
                       }
         }
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/cPath=[^&]+/',$belong,$belong_array);
if($belong_array[0][0] != '' && $belong_array[0][0] != 'cPath=0'){

  $belong = $href_url.'?'.$belong_array[0][0];
}else{

  $belong = $href_url;
}

require("includes/note_js.php");
?>
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

<div id="categories_tree">
                <?php
                  require(DIR_WS_CLASSES . 'faq_tree.php');
                  $osC_FaqTree = new osC_FaqTree;
                  echo $osC_FaqTree->buildTree();
                ?>
                </div>
<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div
    class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" height="40" width="50%">
            <?php echo HEADING_TITLE; ?>
            <?php
            echo tep_get_faq_breadcreumb_by_cpath($_GET['cPath'],$_GET['site_id']);
            ?>
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
            <input type="submit" value="<?php echo IMAGE_SEARCH;?>">
            </div>
            </form>
            </td>
            <td class="smallText" align="right">
            <div class="faq_gotomenu">
            <?php //goto menu start?>              
              <div id="gotomenu">
                <a href="javascript:void(0)" onclick="display()"><?php echo CATEGORY_TREE_SELECT_TEXT;?></a>
                
              </div>
            </div>
<?php
  //end menu
?>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr id="categories_block">
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              
              <tr>
                <td >
                <?php tep_faq_site_filter(FILENAME_FAQ,true);?>
                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                <?php /* faq infor table */?>
                  <tr class="dataTableHeadingRow">
                    <td class="dataTableHeadingContent">
                      <?php echo TABLE_FAQ_SITE; ?>
                    </td>
                    <td class="dataTableHeadingContent">
                      <?php echo TABLE_FAQ_CATEGORY_TITLE; ?>
                    </td>
                    <td class="dataTableHeadingContent" align="center">
                      <?php echo TABLE_FAQ_CATEGORY_IS_SHOW; ?>
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
                      select 
                      fcd.id as fcd_id,
                      fcd.is_show,
                      fcd.faq_category_id,
                      fc.parent_id,
                      fc.created_at,
                      fc.updated_at,
                      fc.sort_order,
                      fcd.site_id,
                      fcd.romaji,
                      fcd.title,
                      fcd.keywords,
                      fcd.description 
                      from ".TABLE_FAQ_CATEGORIES." fc, 
                      ".TABLE_FAQ_CATEGORIES_DESCRIPTION. " fcd 
                      where fc.id = fcd.faq_category_id 
                      and fcd.title like '%".$_GET['search']."%' 
                      and fcd.site_id = '0'
                      order by fc.sort_order,fcd.title 
                       ";
                  }else if(isset($_GET['site_id'])&&$_GET['site_id']){
                    $faq_category_query_raw = "
                      select * from 
                      (
                        select 
                        fcd.id as fcd_id,
                        fcd.is_show,
                        fcd.faq_category_id,
                        fc.parent_id,
                        fc.user_added,
			fc.user_update,
                        fc.created_at,
                        fc.updated_at,
                        fc.sort_order,
                        fcd.site_id,
                        fcd.romaji,
                        fcd.title,
                        fcd.keywords,
                        fcd.description 
                        from ".TABLE_FAQ_CATEGORIES." fc, 
                        ".TABLE_FAQ_CATEGORIES_DESCRIPTION. " fcd 
                        where fc.parent_id = '".$current_category_id."'
                        and fc.id = fcd.faq_category_id 
                        order by site_id DESC
                      ) c 
                      where site_id = ".((isset($_GET['site_id']) &&
                      $_GET['site_id'])?$_GET['site_id']:0)."
                      or site_id = 0 
                      group by c.faq_category_id 
                      order by sort_order,title
                      ";
                  }else{
                    $faq_category_query_raw = "
                      select * from 
                      (
                        select 
                        fcd.id as fcd_id,
                        fcd.is_show,
                        fcd.faq_category_id,
                        fc.parent_id,
                        fc.created_at,
                        fc.updated_at,
			fc.user_added,
			fc.user_update,
                        fc.sort_order,
                        fcd.site_id,
                        fcd.romaji,
                        fcd.title,
                        fcd.keywords,
                        fcd.description 
                        from ".TABLE_FAQ_CATEGORIES." fc, 
                        ".TABLE_FAQ_CATEGORIES_DESCRIPTION. " fcd 
                        where fc.parent_id = '".$current_category_id."'
                        and fc.id = fcd.faq_category_id 
                        order by site_id DESC
                      ) c 
                      group by c.faq_category_id 
                      order by site_id,sort_order,title
                      ";
                  }
                  $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
                  $faq_category_query = tep_db_query($faq_category_query_raw);
                  while($faq_category = tep_db_fetch_array($faq_category_query)){
                    $faq_count++;
                    $rows++;

                    $faq_info_arr =
                      array('id','parent_id','sort_order','name','site_id');
                    if(isset($_GET['search']) && $_GET['search']){
                        $cPath= $faq_category['parent_id'];
                    }
                    if(
                        ((!isset($_GET['cID']) || !$_GET['cID']) &&
                         (!isset($_GET['qID']) || !$_GET['qID']) ||
                         (isset($_GET['qID']) && $_GET['qID']&&
                          !tep_is_set_faq_question($current_category_id,$_GET['qID'],
                            $_GET['site_id'],$_GET['search'],$_GET['page']))||
                         (isset($_GET['cID']) && ($_GET['cID'] ==
                          $faq_category['faq_category_id']||
                          (isset($_GET['site_id'])&&$_GET['site_id']&&
                          !tep_is_set_faq_category($_GET['cID'],$_GET['site_id'])))))
                         && (!isset($faq_info) || !$faq_info) 
                         && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_')
                      ){
                    $faq_category_childs = array('childs_count' => 
                        tep_childs_in_faq_category_count($faq_category['faq_category_id']));
                    $faq_category_question = array('question_count' => 
                        tep_question_in_faq_category_count($faq_category['faq_category_id']));
                    $faq_array = tep_array_merge($faq_category,$faq_category_childs,
                        $faq_category_question);
                      $faq_info = new objectInfo($faq_array);
                    }

                    // row color 
                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if(isset($nowColor) && $nowColor == $odd) {
                      $nowColor = $even;
                    }else{
                      $nowColor = $odd;
                    }

                    if((isset($faq_info)&&is_object($faq_info))&&
                        ($faq_category['faq_category_id'] ==
                         $faq_info->faq_category_id)){
                      echo '<tr class="dataTableRowSelected" 
                        onmouseover="this.style.cursor=\'hand\'" 
                        >' . "\n";
                    echo '<td width="50px" class="dataTableContent"
                        onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,
                        tep_get_faq_path($faq_category['faq_category_id']).
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        . '\'"
                      >';
                    }else{
                      echo '<tr class="'.$nowColor.'" 
                        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" 
                        onmouseout="this.className=\''.$nowColor.'\'" 
                          >'."\n";
                    echo '<td width="50px" class="dataTableContent"
                        onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.
                          $cPath.(isset($_GET['page'])&&$_GET['page']?('&page='.$_GET['page']):'').
                          '&cID='.$faq_category['faq_category_id'].'&site_id='.
                          ((isset($_GET['site_id'])?$_GET['site_id']:0))).
                          '\'"
                      >';
                    }
                    echo get_romaji_by_site_id($faq_category['site_id']);
                    echo "</td>";
                    if((isset($faq_info)&&is_object($faq_info))&&
                        ($faq_category['faq_category_id'] ==
                         $faq_info->faq_category_id)){
                    echo '<td class="dataTableContent"
                        onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,
                        tep_get_faq_path($faq_category['faq_category_id']).
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        . '\'"
                      >';
                    }else{
                    echo '<td class="dataTableContent"
                        onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.
                          $cPath.(isset($_GET['page'])&&$_GET['page']?('&page='.$_GET['page']):'').
                          '&cID='.$faq_category['faq_category_id'].'&site_id='.
                          ((isset($_GET['site_id'])?$_GET['site_id']:0))).
                          '\'"
                      >';
                    }
                    echo '<a
                      href="'.tep_href_link(FILENAME_FAQ,
                        tep_get_faq_path($faq_category['faq_category_id']).'&site_id='.
                      ((isset($_GET['site_id'])?$_GET['site_id']:0))). '">' .
                      tep_image(DIR_WS_ICONS.'folder.gif',ICON_FOLDER) .'</a>&nbsp;
                    '.$faq_category['title'];
                    echo '</td>';
                    echo "<td align='center'>";
                    //faq category is show 
                      if($faq_category['is_show']=='1'){
                        echo '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=toggle&cID='.  $faq_category['faq_category_id'].'&status=1&cPath='.  $HTTP_GET_VARS['cPath'].'&site_id='.  ((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page) .'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '') .'</a>';
                        echo '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=toggle&cID='.  $faq_category['faq_category_id'].'&status=0&cPath='.  $HTTP_GET_VARS['cPath'].'&site_id='.  ((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page) .'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '') .'</a>';
                      }else{
                        echo '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=toggle&cID='.  $faq_category['faq_category_id'].'&status=1&cPath='.  $HTTP_GET_VARS['cPath'].'&site_id='.  ((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page) .'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '') .'</a>';
                        echo '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=toggle&cID='.  $faq_category['faq_category_id'].'&status=0&cPath='.  $HTTP_GET_VARS['cPath'].'&site_id='.  ((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page) .'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '') .'</a>';
                      }
                    echo "</td>";
                    echo '<td class="dataTableContent" align="right">';
                    if((isset($faq_info)&&is_object($faq_info))&&
                        ($faq_category['faq_category_id'] ==
                         $faq_info->faq_category_id)){
                      echo tep_image(DIR_WS_IMAGES.'icon_arrow_right.gif','');
                    }else{
                      echo '<a
                        href="'.tep_href_link(FILENAME_FAQ,'cPath='.$cPath.
                        '&cID='.$faq_category['faq_category_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))).
                        '">'.tep_image(DIR_WS_IMAGES . 'icon_info.gif',
                        IMAGE_ICON_INFO) . '</a>';
                    }
                    echo '&nbsp;</td>';
                    echo '</tr>';
                  }
                  $faq_count=0;
                  // sql for question 
                  if(isset($_GET['search']) && $_GET['search']) {
                    $faq_query_raw = "select 
                      fqd.is_show,
                      fq2c.faq_category_id,
                      fqd.faq_question_id,
                      fqd.romaji,
                      fqd.ask,
                      fqd.keywords,
                      fqd.answer,
                      fq.sort_order,
                      fq.created_at,
                      fq.updated_at,
                      fqd.site_id 
                      from ".TABLE_FAQ_QUESTION." fq, 
                           ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
                           ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
                      where fq.id = fqd.faq_question_id 
                      and fq.id = fq2c.faq_question_id 
                      and fqd.ask like '%".$_GET['search']."%' 
                      and fqd.site_id ='0' 
                      order by fq.sort_order,fqd.ask,fq.id  
                      ";
                  }else if(isset($_GET['site_id'])&&$_GET['site_id']){
                    $faq_query_raw = "select * from (
                      select 
                      fqd.is_show,
                      fq2c.faq_category_id,
                      fqd.faq_question_id,
                      fqd.romaji,
                      fqd.ask,
                      fqd.keywords,
                      fqd.answer,
                      fq.sort_order,
                      fq.created_at,
                      fq.updated_at,
		      fq.user_added,
		      fq.user_update,
                      fqd.site_id 
                      from ".TABLE_FAQ_QUESTION." fq, 
                           ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
                           ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
                      where fq.id = fqd.faq_question_id 
                      and fq.id = fq2c.faq_question_id 
                      and fq2c.faq_category_id = '". $current_category_id . "' 
                      order by fqd.site_id DESC
                      ) c  
                      where site_id = ".((isset($_GET['site_id']) &&
                      $_GET['site_id'])?$_GET['site_id']:0)." 
                      or site_id = 0 
                      group by c.faq_question_id 
                      order by c.sort_order,c.ask,c.faq_question_id 
                      ";
                  }else{
                    $faq_query_raw = "select * from (
                      select 
                      fqd.is_show,
                      fq2c.faq_category_id,
                      fqd.faq_question_id,
                      fqd.romaji,
                      fqd.ask,
                      fqd.keywords,
                      fqd.answer,
                      fq.sort_order,
                      fq.created_at,
                      fq.updated_at,
		      fq.user_added,
		      fq.user_update,
                      fqd.site_id 
                      from ".TABLE_FAQ_QUESTION." fq, 
                           ".TABLE_FAQ_QUESTION_DESCRIPTION." fqd ,
                           ".TABLE_FAQ_QUESTION_TO_CATEGORIES." fq2c 
                      where fq.id = fqd.faq_question_id 
                      and fq.id = fq2c.faq_question_id 
                      and fq2c.faq_category_id = '". $current_category_id . "' 
                      order by fqd.site_id DESC
                      ) c  
                      group by c.faq_question_id 
                      order by c.sort_order,c.ask,c.faq_question_id 
                      ";
                  }
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
                         (isset($_GET['qID']) && (($_GET['qID'] ==
                          $_faq_info['faq_question_id']&&
                          (!isset($_GET['site_id'])||$_GET['site_id']==0||
                          (isset($_GET['site_id'])&&$_GET['site_id']&&
                           tep_is_set_faq_question($current_category_id,$_GET['qID'],
                             $_GET['site_id'],$_GET['search'],$_GET['page']))))||
                           !tep_is_set_faq_question($current_category_id,$_GET['qID'],
                             $_GET['site_id'],$_GET['search'],$_GET['page'])
                                                  )))
                        && (!isset($qInfo) || !$qInfo)
                        && (!isset($faq_info) || !$faq_info)
                        && (!isset($_GET['action']) || substr($_GET['action'], 0, 4)
                          != 'new_')
                      ){
                      $qInfo = new objectInfo($_faq_info);
                    }

                    $even = 'dataTableSecondRow';
                    $odd = 'dataTableRow';
                    if(isset($nowColor) && $nowColor == $odd) {
                      $nowColor = $even;
                    }else{
                      $nowColor = $odd;
                    }

                    if((isset($qInfo)&&is_object($qInfo))&&
                        ($_faq_info['faq_question_id'] == $qInfo->faq_question_id)){
                      echo '<tr class="dataTableRowSelected" 
                        onmouseover="this.style.cursor=\'hand\'" 
                        >' . "\n";
                      echo '<td width="50px" class="dataTableContent"
                        onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .
                        '&qID='.$_faq_info['faq_question_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        . '\'"
                        >';
                    }else{
                      echo '<tr class="'.$nowColor.'" 
                        onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" 
                        onmouseout="this.className=\''.$nowColor.'\'" 
                        >'."\n";
                      echo '<td width="50px" class="dataTableContent"
                        onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .
                        '&qID='.$_faq_info['faq_question_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        .  '\'"
                        >';
                    }
                    echo get_romaji_by_site_id($_faq_info['site_id']);
                    echo "</td>";
                    if((isset($qInfo)&&is_object($qInfo))&&
                        ($_faq_info['faq_question_id'] == $qInfo->faq_question_id)){
                      echo '<td class="dataTableContent"
                        onclick="document.location.herf=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .
                        '&qID='.$_faq_info['faq_question_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        . '\'"
                        >';
                    }else{
                      echo '<td class="dataTableContent"
                        onclick="document.location.href=\''.tep_href_link(FILENAME_FAQ,
                        'cPath='.$cPath.($_GET['page'] ? ('&page=' . $_GET['page']): '' ) .
                        '&qID='.$_faq_info['faq_question_id'].
                        '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0)))
                        .  '\'"
                        >';
                    }
                      echo "".$_faq_info['ask']."";
                      echo "</td>";
                      echo "<td align='center'>";
                      //question is show
                      if($_faq_info['is_show']=='1'){
                        echo '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=setflag&qID='.  $_faq_info['faq_question_id'].'&flag=1&cPath='.  $cPath.'&site_id='.  ((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page) .'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '') .'</a>';
                        echo '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=setflag&qID='.  $_faq_info['faq_question_id'].'&flag=0&cPath='.  $cPath.'&site_id='.  ((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page) .'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '') .'</a>';
                      }else{
                        echo '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=setflag&qID='.  $_faq_info['faq_question_id'].'&flag=1&cPath='.  $cPath.'&site_id='.  ((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page) .'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '') .'</a>';
                        echo '<a class="faq_status_link" href="javascript:viod(0);" onclick ="change_status(\''.  tep_href_link(FILENAME_FAQ,'action=setflag&qID='.  $_faq_info['faq_question_id'].'&flag=0&cPath='.  $cPath.'&site_id='.  ((isset($_GET['site_id'])?$_GET['site_id']:0)).$c_page) .'\')">'.'&nbsp;'.  tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '') .'</a>';
                      }
                      echo "</td>";
                      echo '<td class="dataTableContent" align="right">';
                      if(isset($qInfo)&&(is_object($qInfo))&&
                            $_faq_info['faq_question_id'] ==  $qInfo->faq_question_id){
                        echo tep_image(DIR_WS_IMAGES.'icon_arrow_right.gif', '');
                      }else{
                        echo '<a href="'.tep_href_link(FILENAME_FAQ,
                             'cPath='.$cPath.'&qID='.$_faq_info['faq_question_id'].'&site_id='.
                               ((isset($_GET['site_id'])?$_GET['site_id']:0)).
                               '&page='.((isset($_GET['page']))?$_GET['page']:0)).'">'.
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
				  echo '<div class="td_box">';
                  if($cPath){
                    echo '<a href="'.tep_href_link(FILENAME_FAQ,$cPath_back.'&cID='.$current_category_id.  '&site_id='.((isset($_GET['site_id'])?$_GET['site_id']:0))).'">'.tep_html_element_button(IMAGE_BACK).'<a>';
                    echo '&nbsp;';
                  }
                  if((!isset($_GET['search'])||!$_GET['search'])&&
                      $ocertify->npermission >=
                      10&&isset($_GET['site_id'])&&$_GET['site_id']){
                    echo '<a href="'.tep_href_link(FILENAME_FAQ, 'cPath='.$cPath.'&action=new_faq_category'.'&site_id='.$site_id).'">'.tep_html_element_button(IMAGE_NEW_FAQ_CATEGORY).'</a>';
                    echo '&nbsp;';
                    echo '<a href="'.tep_href_link(FILENAME_FAQ, 'cPath='.$cPath.'&action=new_faq_question'.'&site_id='.$site_id).'">'.tep_html_element_button(IMAGE_NEW_FAQ).'</a>';
                  }
                  echo '</div>';

                  ?>
                </td>
              </tr>
              <tr> 
    <td class="smallText" valign="top" align='right'>
      <div class='faq_page_text'>
      <?php echo $faq_split->display_count($faq_query_number, MAX_DISPLAY_FAQ_ADMIN, $_GET['page'],
          TEXT_DISPLAY_NUMBER_OF_FAQ); ?>
      </div>
      <div class='faq_page_link'>
      <?php echo $faq_split->display_links($faq_query_number, MAX_DISPLAY_FAQ_ADMIN, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?>
      </div>
    </td>
  </tr>  
            </table></td>
<?php
  $heading = array();
  $contents = array();
  // faq right info
if(isset($_GET['action'])&& $_GET['action']&&(!isset($site_id)||$site_id==0)){
  $_action = null;
}else{
  $_action = $_GET['action'];
}
switch (isset($_action)? $_action:'') {
/* -----------------------------------------------------
   case 'new_faq_category' 右侧新建faq分类页  
   case 'new_faq_question' 右侧新建faq问题页   
   case 'edit_faq_category' 右侧编辑faq分类页   
   case 'edit_faq_question' 右侧编辑faq问题页   
   case 'delete_faq_category' 右侧删除faq分类页   
   case 'delete_faq_question' 右侧删除faq问题页 
   default 右侧默认页
------------------------------------------------------*/
  case 'new_faq_category':
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    $heading[] = array('text' => TEXT_INFO_HEADING_NEW_FAQ_CATEGORY);
    $contents = array('form' =>
        tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=insert_faq_category&cPath='.$cPath,
          'post','onsubmit="return faq_category_form_validator(\''.$current_category_id.'\',\'\',\''.$site_id.'\')"'));
    $contents[] = array('text' => TEXT_NEW_FAQ_CATEGORY_INFO);
    $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');
    $contents[] = array('text' => '<input type="hidden" name="user_added" value="'.$user_info['name'].'">');

    $faq_category_inputs_string = ''; 
    $faq_category_inputs_string .= '<br>URL:<br>'.
      tep_draw_input_field('romaji','','id="cromaji"').
      '<input type="button" onclick =
      "faq_c_is_set_romaji(\''.$current_category_id.'\',\'\',\''.$site_id.'\')"
      value="'.TEXT_ROMAJI_IS_SET.'">'.
      '<input type="button" onclick = "faq_c_is_set_error_char(\'\')"
      value="'.IS_SET_ERROR_CHAR.'">'.
      '<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_TITLE.':<br>'.
      tep_draw_input_field('title','','id="title"').'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_KEYWORDS.':<br>'.
      tep_draw_textarea_field('keywords','soft',30,7).'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_DESCRIPTION.':<br>'.
      tep_draw_textarea_field('description','soft',30,7).'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_SORT_ORDER.':<br>'.
      tep_draw_input_field('sort_order','1000','size="2"').'<br>';
    $contents[] = array('text' => $faq_category_inputs_string.
        tep_draw_hidden_field('site_id',$site_id));
    $contents[] = array('align' => 'center' ,
        'text' => tep_html_element_submit(TEXT_SAVE).  '<a href="'.tep_href_link(FILENAME_FAQ,
      'cPath='.$cPath.$dc_page."&site_id=".$site_id).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>');
    break;
  case 'new_faq_question':
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    $heading[] = array('text' => TEXT_INFO_HEADING_NEW_FAQ_QUESTION);
    $contents = array('form' =>
        tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=insert_faq_question&cPath='.$cPath.
          '&site_id='.$site_id.'&qID='.$_GET['qID'],'post',
          'onsubmit="return faq_question_form_validator(\''.$current_category_id.'\',\'\',\''.$site_id.'\')"'));
    $contents[] = array('text' => TEXT_NEW_FAQ_QUESTION_INFO);
    $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');
    $contents[] = array('text' => '<input type="hidden" name="user_added" value="'.$user_info['name'].'">');

    $faq_question_inputs_string = ''; 
    $faq_question_inputs_string .= '<br>URL:<br>'.
      tep_draw_input_field('romaji','','id="qromaji"').
      '<input type="button" onclick = "faq_q_is_set_romaji(\''.$current_category_id.'\',\'\',\''.$site_id.'\')"
      value="'.TEXT_ROMAJI_IS_SET.'">'.
      '<input type="button" onclick = "faq_q_is_set_error_char(\'\')"
      value="'.IS_SET_ERROR_CHAR.'">'.
      '<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_KEYWORDS.':<br>'.
      tep_draw_textarea_field('keywords','soft',30,7).'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_ASK.':<br>'.
      tep_draw_input_field('ask','','id="title"').'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_ANSWER.':<br>'.
      '<br>'.TEXT_NEW_FAQ_QUESTION_ANSWER_HELP.'<br>'.
      tep_draw_textarea_field('answer','soft',30,7).'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_SORT_ORDER.':<br>'.
      tep_draw_input_field('sort_order','1000','size="2"').'<br>';
    $contents[] = array('text' => $faq_question_inputs_string.
        tep_draw_hidden_field('site_id',$site_id));
    $contents[] = array('align' => 'center' ,
        'text' => tep_html_element_submit(TEXT_SAVE).  '<a href="'.tep_href_link(FILENAME_FAQ,
      'cPath='.$cPath.'&site_id='.$site_id.'&qID='.$_GET['qID'].$dc_page).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>');
    break;
  case 'edit_faq_category':
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    $heading[] = array('text' => TEXT_INFO_HEADING_EDIT_FAQ_CATEGORY);
    $contents = array('form' =>
        tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=update_faq_category&cPath='.$cPath,
          'post','onsubmit="return faq_category_form_validator(\''.$current_category_id.'\',\''.$faq_info->faq_category_id.'\',\''.$site_id.'\')"'));
    $contents[] = array('text' => TEXT_EDIT_FAQ_CATEGORY_INFO);
    $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');

    $faq_category_inputs_string = ''; 
    $faq_category_inputs_string .= '<br>URL:<br>'.
      tep_draw_input_field('romaji',$faq_info->romaji,'id="cromaji"').
      '<input type="button" onclick = "faq_c_is_set_romaji(\''.$current_category_id.'\',\''.$faq_info->faq_category_id.'\',\''.$site_id.'\')"
      value="'.TEXT_ROMAJI_IS_SET.'">'.
      '<input type="button" onclick = "faq_c_is_set_error_char()"
      value="'.IS_SET_ERROR_CHAR.'">'.
      '<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_TITLE.':<br>'.
      tep_draw_input_field('title',$faq_info->title,'id="title"').'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_KEYWORDS.':<br>'.
      tep_draw_textarea_field('keywords','soft',30,7,$faq_info->keywords).'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_DESCRIPTION.':<br>'.
      tep_draw_textarea_field('description','soft',30,7,$faq_info->description).'<br>';
    $faq_category_inputs_string .= '<br>'.TEXT_NEW_FAQ_CATEGORY_SORT_ORDER.':<br>'.
      tep_draw_input_field('sort_order',$faq_info->sort_order,'size="2"').'<br>';
    $contents[] = array('text' => $faq_category_inputs_string.
      tep_draw_hidden_field('faq_category_id',$faq_info->faq_category_id).
      tep_draw_hidden_field('site_id',$site_id));
    $contents[] = array('align' => 'center' ,
        'text' => tep_html_element_submit(TEXT_SAVE).  '<a
        href="'.tep_href_link(FILENAME_FAQ,'cPath='.$cPath.$dc_page."&site_id=".$site_id).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>');
    break;
  case 'edit_faq_question':
    $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
    $heading[] = array('text' => TEXT_INFO_HEADING_EDIT_FAQ_QUESTION);
    $contents = array('form' =>
        tep_draw_form('newfaqcategory',FILENAME_FAQ,'action=update_faq_question&cPath='.$cPath.
          '&site_id='.$site_id.'&qID='.$_GET['qID'],'post',
          'onsubmit="return faq_question_form_validator(\''.$current_category_id.'\',\''.$qInfo->faq_question_id.'\',\''.$site_id.'\')"'));
    $contents[] = array('text' => TEXT_EDIT_FAQ_QUESTION_INFO);
    $contents[] = array('text' => '<input type="hidden" name="user_update" value="'.$user_info['name'].'">');
    $faq_question_inputs_string = ''; 
    $faq_question_inputs_string .= '<br>URL:<br>'.
      tep_draw_input_field('romaji',$qInfo->romaji,'id="qromaji"').
      '<input type="button" onclick =
      "faq_q_is_set_romaji(\''.$current_category_id.'\',\''.$qInfo->faq_question_id.'\',\''.$site_id.'\')"
      value="'.TEXT_ROMAJI_IS_SET.'">'.
      '<input type="button" onclick = "faq_q_is_set_error_char()"
      value="'.IS_SET_ERROR_CHAR.'">'.
      '<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_KEYWORDS.':<br>'.
      tep_draw_textarea_field('keywords','soft',30,7,$qInfo->keywords).'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_ASK.':<br>'.
      tep_draw_input_field('ask',$qInfo->ask,'id="title"').'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_ANSWER.':<br>'.
      '<br>'.TEXT_NEW_FAQ_QUESTION_ANSWER_HELP.'<br>'.
      tep_draw_textarea_field('answer','soft',30,7,$qInfo->answer).'<br>';
    $faq_question_inputs_string .= '<br>'.TEXT_NEW_FAQ_QUESTION_SORT_ORDER.':<br>'.
      tep_draw_input_field('sort_order',$qInfo->sort_order,'size="2"').'<br>';
    $contents[] = array('text' => $faq_question_inputs_string.
      tep_draw_hidden_field('faq_question_id',$qInfo->faq_question_id).
        tep_draw_hidden_field('site_id',$site_id));
    $contents[] = array('align' => 'center' ,
        'text' => tep_html_element_submit(TEXT_SAVE).  '<a
        href="'.tep_href_link(FILENAME_FAQ,'cPath='.$cPath.'&site_id='.$site_id.$dc_page.'&qID='.$_GET['qID']).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>');
    break;
  case 'delete_faq_category':
      $heading[] = array('text' => TEXT_INFO_HEADING_DELETE_CATEGORY);
      $dc_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
      $contents = array('form' => tep_draw_form('categories', FILENAME_FAQ,
            'action=delete_faq_category_confirm&cPath=' . $cPath.$dc_page) .
          tep_draw_hidden_field('faq_category_id', $faq_info->faq_category_id).
          tep_draw_hidden_field('site_id', $_GET['site_id']));
      $contents[] = array('text' => TEXT_DELETE_FAQ_CATEGORY_INTRO);
      $contents[] = array('text' => '<br>' . $faq_info->title . '');
      if($faq_info->childs_count > 0) {
         $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS,
               $faq_info->childs_count));
      }
      if($faq_info->question_count >0){
         $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_QUESTION,
               $faq_info->question_count));
      }
      $contents[] = array('align' => 'center',
          'text' => '<br>'.tep_html_element_submit(IMAGE_DELETE).  '<a
          href="'.tep_href_link(FILENAME_FAQ, 'cPath=' .  $cPath . '&cID=' .
        $faq_info->faq_category_id.$dc_page."&site_id=".$site_id).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>')
          ;
    break;
  case 'delete_faq_question':
    $heading[] = array('text' => TEXT_INFO_HEADING_DELETE_FAQ_QUESTION);
    $d_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';
      $contents = array('form' => tep_draw_form('question', FILENAME_FAQ,
            'action=delete_faq_question_confirm&cPath=' .  $cPath.$d_page,
            'post').
            tep_draw_hidden_field('faq_question_id', $qInfo->faq_question_id).
            tep_draw_hidden_field('site_id', $_GET['site_id']));
    $contents[] = array('text' => TEXT_DELETE_QUESTION_INTRO);
    $contents[] = array('text' => '<br>' . $qInfo->ask . '');

    $question_categories_string = '';
    $question_categories = tep_generate_faq_category_path($qInfo->faq_question_id, 'question');
    for ($i = 0, $n = sizeof($question_categories); $i < $n; $i++) {
       $category_path = '';
       for ($j = 0, $k = sizeof($question_categories[$i]); $j < $k; $j++) {
         $category_path .= $question_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
       }    
      $category_path = substr($category_path, 0, -16);
      $question_categories_string .= tep_draw_checkbox_field('question_categories[]', $question_categories[$i][sizeof($question_categories[$i])-1]['id'], true) . '&nbsp;' . $category_path . '<br>'; 
    }    
    $question_categories_string = substr($question_categories_string, 0, -4); 

    $contents[] = array('text' => '<br>' . $question_categories_string);
    $contents[] = array('align' => 'center',
            'text' => tep_html_element_submit(IMAGE_DELETE).
            '<a href="'.tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath . '&qID=' . $qInfo->faq_question_id.  '&site_id='.$_GET['site_id'].$d_page).'">'.tep_html_element_button(IMAGE_CANCEL).'</a>');
    break;
  default:
    if ($rows > 0){
      if(isset($faq_info)&&is_object($faq_info)){
        $heading[] = array('text' => $faq_info->title);
        if($ocertify->npermission >= 10 ){
          if (isset($_GET['site_id'])&&$_GET['site_id']!=0){
            foreach(tep_get_sites() as $site){
              if($site['id'] != $_GET['site_id']){
                continue;
              }
              $contents[] = array(
                  'align' => 'left',
                  'text' => '<a href="'.tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath . '&cID=' .  $faq_info->faq_category_id .  '&action=edit_faq_category&site_id='.$site['id']).'">'.tep_html_element_button(IMAGE_EDIT).'</a>'.
                  (tep_faq_categories_description_exist($faq_info->faq_category_id,$site['id'])?  '<a href="'.tep_href_link(FILENAME_FAQ,'cPath=' . $cPath . '&cID=' .      $faq_info->faq_category_id .  '&action=delete_faq_category&site_id='.$site['id']).'">'.tep_html_element_button(IMAGE_DELETE).'</a>' :'')
                  );
            }
          }
        }
if(tep_not_null($faq_info->user_added)){
$contents[] = array('text' =>  TEXT_USER_ADDED. ' ' .$faq_info->user_added);
}else{
$contents[] = array('text' =>  TEXT_USER_ADDED. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null(tep_datetime_short($faq_info->created_at))){
$contents[] = array('text' =>  TEXT_CREATED_AT. ' ' .tep_datetime_short($faq_info->created_at));
}else{
$contents[] = array('text' =>  TEXT_CREATED_AT. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null($faq_info->user_update)){
$contents[] = array('text' => TEXT_USER_UPDATE. ' ' .$faq_info->user_update);
}else{
$contents[] = array('text' => TEXT_USER_UPDATE. ' ' .TEXT_UNSET_DATA);
}if(tep_not_null(tep_datetime_short($faq_info->updated_at))){
$contents[] = array('text' =>  TEXT_UPDATED_AT. ' ' .tep_datetime_short($faq_info->updated_at));
}else{
$contents[] = array('text' =>  TEXT_UPDATED_AT. ' ' .TEXT_UNSET_DATA);
}

      }else if(isset($qInfo)&&is_object($qInfo)){
        $heading[] = array('text' => $qInfo->ask);
        if($ocertify->npermission >= 10) {  
          //limit for permission
          if(isset($_GET['site_id'])){
            foreach(tep_get_sites() as $site){
              if($site['id'] != $_GET['site_id']){
                continue;
              }
              $contents[] = array('align' => 'left' , 'text' => '<a href="'.tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath . '&qID=' . $qInfo->faq_question_id .  '&action=edit_faq_question'. '&site_id='.  $site['id'].'&page='.$_GET['page']).'">'.tep_html_element_button(IMAGE_EDIT).'</a>'.(tep_faq_question_description_exist($qInfo->faq_question_id,$site['id'])?'<a href="'.tep_href_link(FILENAME_FAQ, 'cPath=' . $cPath . '&qID=' . $qInfo->faq_question_id .  '&action=delete_faq_question&site_id='.  $site['id'].'&page='.$_GET['page']).'">'.tep_html_element_button(IMAGE_DELETE).'</a>':'')
                  );
            }
          }
if(tep_not_null($qInfo->user_added)){
$contents[] = array('text' => TEXT_USER_ADDED. ' ' .$qInfo->user_added);
}else{
$contents[] = array('text' => TEXT_USER_ADDED. ' ' .TEXT_UNSET_DATA);
}
if(tep_not_null(tep_datetime_short($qInfo->created_at))){
$contents[] = array('text' =>  TEXT_CREATED_AT. ' ' .tep_datetime_short($qInfo->created_at));
}else{
$contents[] = array('text' =>  TEXT_CREATED_AT. ' ' .TEXT_UNSET_DATA);
}
if(tep_not_null($qInfo->user_update)){
$contents[] = array('text' =>  TEXT_USER_UPDATE. ' ' .$qInfo->user_update);
}else{
$contents[] = array('text' =>  TEXT_USER_UPDATE. ' ' .TEXT_UNSET_DATA);
}
if(tep_not_null(tep_datetime_short($qInfo->updated_at))){
$contents[] = array('text' =>  TEXT_UPDATED_AT. ' '  .tep_datetime_short($qInfo->updated_at));
}else{
$contents[] = array('text' =>  TEXT_UPDATED_AT. ' '  .TEXT_UNSET_DATA);
}
        }
      }

    }else{
      $heading[] = array('text' => EMPTY_FAQ_CATEGORY);
    }
    break;
  
  }

  if ((tep_not_null($heading)) && (tep_not_null($contents)) ) {
    echo '            <td class="right_column_b" width="25%" valign="top">' . "\n";
    $box = new box;
    echo $box->infoBox($heading, $contents);
    echo '            </td>' . "\n";
  }
?>
          </tr>
        </table></td>
      </tr>
    </table></div></div></td>
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
