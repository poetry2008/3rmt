<?php
/*
  $Id$
*/

  $xx_mins_ago = (time() - 900);

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
     $sql_site_where = 's.id in ('.str_replace('-', ',', $_GET['site_id']).')';
     $show_list_array = explode('-',$_GET['site_id']);
   } else {
      $show_list_str = tep_get_setting_site_info('referer.php');
      $sql_site_where = 's.id in ('.$show_list_str.')';
      $show_list_array = explode(',',$show_list_str);
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo REFERER_TITLE_TEXT; ?></title>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>', '<?php echo (!empty($_SERVER['HTTP_REFERER']))?urlencode($_SERVER['HTTP_REFERER']):urlencode(tep_href_link(FILENAME_DEFAULT));?>');
  </script>
<?php }?>
<!-- header -->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo REFERER_TITLE_TEXT;?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
    <form action="<?php echo tep_href_link('referer.php'); ?>" method="get">
    <input type="hidden" name="" value="">
    <input type="hidden" name="" vlaue="">
    <table  border="0"  cellpadding="0" cellspacing="0">
    <tr>
      <td>
      <input type="hidden" name="site_id" value="<?php echo $_GET['site_id'];?>">
      <input type="hidden" name="type" value="<?php echo $_GET['type'];?>">
      <?php echo KEYWORDS_SEARCH_START_TEXT;?> 
      <select name="sy">
      <?php 
      for($i=2007; $i<=date('Y'); $i++) { 
        if ((isset($_GET['sy']) && $i == $_GET['sy']) or (!isset($_GET['sy']) && $i == date('Y'))) {
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; 
        } else {
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        }
      } ?>
      </select>
      <?php echo YEAR_TEXT;?>
      <select name="sm">
      <?php for($i=1; $i<13; $i++) { if((isset($_GET['sm']) && $i == $_GET['sm']) or (!isset($_GET['sm']) && $i == date('m')-1)){ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }else{ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }  } ?>    
      </select>
      <?php echo MONTH_TEXT;?>
      <select name="sd">
      <?php
      for($i=1; $i<32; $i++) {
        if((isset($_GET['sd']) && $i == $_GET['sd']) or (!isset($_GET['sd']) && $i == date('d'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo DAY_TEXT;?> </td>
      <td width="40" align="center">～</td>
      <td>
      <?php echo KEYWORDS_SEARCH_END_TEXT;?> 
      <select name="ey">
      <?php
      for($i=2002; $i<=date('Y'); $i++) {
        if((isset($_GET['ey']) && $i == $_GET['ey']) or (!isset($_GET['ey']) && $i == date('Y'))){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      <?php echo YEAR_TEXT;?>
      <select name="em">
      <?php
      for($i=1; $i<13; $i++) {
        if((isset($_GET['em']) && $i == $_GET['em']) or (!isset($_GET['em']) && $i == date('m'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo MONTH_TEXT;?>
      <select name="ed">
      <?php
      for($i=1; $i<32; $i++) {
        if((isset($_GET['ed']) && $i == $_GET['ed']) or (!isset($_GET['ed']) && $i == date('d'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo DAY_TEXT;?> </td>
        <td>&nbsp;</td>
        <td><input type="submit" value="<?php echo IMAGE_SEARCH?>"></td>
      </tr>
    </table><br>
    </form>
        <?php tep_show_site_filter('referer.php',true,array(0));?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
        <?php 
        if(!isset($_GET['type']) || $_GET['type'] == ''){
                   $_GET['type'] = 'asc';
        }
        if($referer_type == ''){
                   $referer_type = 'asc';
        }
        if(!isset($_GET['sort']) || $_GET['sort'] == ''){
           $referer_str = 'cnt desc';
        }else if($_GET['sort'] == 'orders_ref_site2'){
              if($_GET['type'] == 'desc'){
                 $referer_str = 'orders_ref_site2 desc';
                 $referer_type = 'asc';
               }else{
                 $referer_str = 'orders_ref_site2 asc';
                 $referer_type = 'desc';
               }
        }else if($_GET['sort'] == 'cnt'){
              if($_GET['type'] == 'desc'){
                 $referer_str = 'cnt desc';
                 $referer_type = 'asc';
               }else{
                 $referer_str = 'cnt asc';
                 $referer_type = 'desc';
               }
        }else if($_GET['sort'] == 'cnt_order'){
              if($_GET['type'] == 'desc'){
                 $referer_str = 'rownum desc';
                 $referer_type = 'asc';
               }else{
                 $referer_str = 'rownum asc';
                 $referer_type = 'desc';
               }
        }
        if($_GET['sort'] == 'orders_ref_site2'){
              if($_GET['type'] == 'desc'){
                 $orders_ref_site2 = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
              }else{
                 $orders_ref_site2 = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
              }
        }
        if($_GET['sort'] == 'cnt'){
              if($_GET['type'] == 'desc'){
                 $cnt = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
              }else{
                 $cnt = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
              }
        }
        if($_GET['sort'] == 'cnt_order'){
              if($_GET['type'] == 'desc'){
                 $cnt_order = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
              }else{
                 $cnt_order = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
              }
        }
        $referer_able_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
        $notice_box = new notice_box('','',$referer_table_params);
        $referer_table_row = array();
        $referer_title_row = array();
        $referer_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox">');
        if(isset($_GET['sort']) && $_GET['sort'] == 'orders_ref_site2'){
        $referer_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('referer.php','sort=orders_ref_site2&type='.$referer_type.'&sy='.$_GET['sy'].'&sm='.$_GET['sm'].'&sd='.$_GET['sd'].'&ey='.$_GET['ey'].'&em='.$_GET['em'].'&ed='.$_GET['ed']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.REFERER_TITLE_URL.$orders_ref_site2.'</a>');
        }else{
        $referer_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('referer.php','sort=orders_ref_site2&type=desc&sy='.$_GET['sy'].'&sm='.$_GET['sm'].'&sd='.$_GET['sd'].'&ey='.$_GET['ey'].'&em='.$_GET['em'].'&ed='.$_GET['ed']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.REFERER_TITLE_URL.$orders_ref_site2.'</a>');
        }
        if(isset($_GET['sort']) && $_GET['sort'] == 'cnt'){
        $referer_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('referer.php','sort=cnt&type='.$referer_type.'&sy='.$_GET['sy'].'&sm='.$_GET['sm'].'&sd='.$_GET['sd'].'&ey='.$_GET['ey'].'&em='.$_GET['em'].'&ed='.$_GET['ed']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.REFERER_TITLE_NUM.$cnt.'</a>');
        }else{
        $referer_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('referer.php','sort=cnt&type=desc&sy='.$_GET['sy'].'&sm='.$_GET['sm'].'&sd='.$_GET['sd'].'&ey='.$_GET['ey'].'&em='.$_GET['em'].'&ed='.$_GET['ed']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.REFERER_TITLE_NUM.$cnt.'</a>');
        }
        if(isset($_GET['sort']) && $_GET['sort'] == 'cnt_order'){
        $referer_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('referer.php','sort=cnt_order&type='.$referer_type.'&sy='.$_GET['sy'].'&sm='.$_GET['sm'].'&sd='.$_GET['sd'].'&ey='.$_GET['ey'].'&em='.$_GET['em'].'&ed='.$_GET['ed']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.REFERER_TITLE_SORT_NUM.$cnt_order.'</a>');
        }else{
        $referer_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('referer.php','sort=cnt_order&type=desc&sy='.$_GET['sy'].'&sm='.$_GET['sm'].'&sd='.$_GET['sd'].'&ey='.$_GET['ey'].'&em='.$_GET['em'].'&ed='.$_GET['ed']).'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.REFERER_TITLE_SORT_NUM.$cnt_order.'</a>');
        }
        $referer_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
        $referer_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $referer_title_row);
  //全部访问排名 
  $ref_site_sql= "
    select * from ( select (@mycnt := @mycnt + 1) as rownum,cnt,orders_ref_site2,id from (
      select orders_id as id,count(orders_id) as cnt , concat( ifnull( orders_ref_site, '' ) , if( orders_adurl is null, '', '(Adsense)' ) ) AS orders_ref_site2
      from " . TABLE_ORDERS . " o, ".TABLE_SITES." s
      where s.id = o.site_id
        and orders_ref_site IS NOT NULL
        and orders_ref_site != ''
        and " .$sql_site_where. 
        (isset($_GET['sy']) && isset($_GET['sm']) && isset($_GET['sd']) ? " and o.date_purchased > '".$_GET['sy'].'-'.$_GET['sm'].'-'.$_GET['sd'] ."'" : " and o.date_purchased > '".date('Y-m-d H:i:s', time()-(86400*30)) . "' ") . 
        (isset($_GET['ey']) && isset($_GET['em']) && isset($_GET['ed']) ? " and o.date_purchased < '".$_GET['ey'].'-'.$_GET['em'].'-'.$_GET['ed'] ." 23:59:59'" : '') . "
      group by orders_ref_site2
    ) s ";
    if(!isset($_GET['sort']) || $_GET['sort'] == 'orders_ref_site2'){
    $ref_site_sql .= 'order by cnt desc';
    }else if($_GET['sort'] == 'cnt'){
       if($_GET['type'] == 'desc'){
        $ref_site_sql .= 'order by cnt desc';
       }else{
        $ref_site_sql .= 'order by cnt asc';
       }
    }else if($_GET['sort'] == 'cnt_order'){
       if($_GET['type'] == 'desc'){
        $ref_site_sql .= 'order by cnt desc';
       }else{
        $ref_site_sql .= 'order by cnt desc';
       }
    }

    $ref_site_sql .= ") q order by ".$referer_str;
    $referer_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS,$ref_site_sql, $referer_query_numrows);
    tep_db_query("set @mycnt=0");
    $ref_site_query = tep_db_query($ref_site_sql);
    $referer_num = tep_db_num_rows($ref_site_query);
    if($_GET['sort'] == 'cnt_order' && $_GET['type'] == 'desc'){
    $i = $referer_num;
    }else{
    $i = 1;
    }
    while ($ref_site = tep_db_fetch_array($ref_site_query)) {
      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
    if($_GET['id'] == $ref_site['id']){
       $referer_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
     }else{
       $referer_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
     }
    $onclick = 'onClick="document.location.href=\''.tep_href_link('referer.php','sort='.$_GET['sort'].'&type='.$_GET['type'].'&sy='.$_GET['sy'].'&sm='.$_GET['sm'].'&sd='.$_GET['sd'].'&ey='.$_GET['ey'].'&em='.$_GET['em'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&ed='.$_GET['e_d']).'&id='.$ref_site['id'].'\'"';
    $referer_info = array();
    $referer_info[] = array(
        'params' => 'class="dataTableContent"',
        'text'   => '<input type="checkbox" disabled="disabled">'
        );
    $referer_info[] = array(
        'params' => 'class="dataTableContent"'.$onclick,
        'text'   => $ref_site['orders_ref_site2']
        );
    $referer_info[] = array(
        'params' => 'class="dataTableContent"'.$onclick,
        'text'   => $ref_site['cnt']
        );
    $referer_info[] = array(
        'params' => 'class="dataTableContent"'.$onclick,
        'text'   => $ref_site['rownum']
        );
    $referer_info[] = array(
        'params' => 'class="dataTableContent" align="right"',
        'text'   => tep_image('images/icons/info_gray.gif')
        );
    $referer_table_row[] = array('params' => $referer_params, 'text' => $referer_info);
    if($_GET['sort'] == 'cnt_order' && $_GET['type'] == 'desc'){
      $i--;
    }else{
      $i++;
    }
    }
    $notice_box->get_contents($referer_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
    <?php
     if($referer_num > 0){
        if($ocertify->npermission >= 15){
            echo '<select  disabled="disabled">';
            echo '<option value="0">'.TEXT_CONTENTS_SELECT_ACTION.'</option>';
            echo '<option value="1">'.TEXT_CONTENTS_DELETE_ACTION.'</option>';
            echo '</select>';
          }
      }else{
            echo TEXT_DATA_EMPTY;
      }
     ?>
    </td>
  </tr>
  <tr>
    <td class="smallText" valign="top"><?php echo $referer_split->display_count($referer_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
    <td class="smallText" align="right"><div class="td_box"><?php echo $referer_split->display_links($referer_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></div></td>
  </tr>
</table>
            </td>
          </tr>
        </table></td>
      </tr>
    </table>
    </div> 
    </div>
    </td>
<!-- body_text_eof -->
  </tr>
</table>
<!-- body_eof -->

<!-- footer -->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof -->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
