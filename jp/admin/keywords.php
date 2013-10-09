<?php
/*
  $Id$
*/

  $xx_mins_ago = (time() - 900);

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
      $sql_site_where = 'site_id in ('.str_replace('-', ',', $_GET['site_id']).')';
      $show_list_array = explode('-',$_GET['site_id']);
  } else {
      $show_list_str = tep_get_setting_site_info('keywords.php');
      $sql_site_where = 'site_id in ('.$show_list_str.')';
      $show_list_array = explode(',',$show_list_str);
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo KEYWORDS_TITLE_TEXT; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
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
            <td class="pageHeading"><?php echo KEYWORDS_TITLE_TEXT;?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
    <form action="<?php echo tep_href_link('keywords.php') ; ?>" method="get">
    <!--<legend class="smallText"><b>xxxxx</b></legend>-->
    <table  border="0" cellpadding="0" cellspacing="2">
    <tr>
      <td>
      <input type="hidden" name="site_id" value="<?php echo $_GET['site_id'];?>"> 
      <?php echo KEYWORDS_SEARCH_START_TEXT;?> 
      <select name="s_y">
      <?php 
      for($i=2007; $i<=date('Y'); $i++) { 
        if ((isset($_GET['s_y']) && $i == $_GET['s_y']) or (!isset($_GET['s_y']) && $i == date('Y'))) {
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ; 
        } else {
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        }
      } ?>
      </select>
       <?php echo YEAR_TEXT;?> 
      <select name="s_m">
      <?php for($i=1; $i<13; $i++) { if((isset($_GET['s_m']) && $i == $_GET['s_m']) or (!isset($_GET['s_m']) && $i == date('m')-1)){ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }else{ echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n"; }  } ?>    
      </select>
      <?php echo MONTH_TEXT;?> 
      <select name="s_d">
      <?php
      for($i=1; $i<32; $i++) {
        if((isset($_GET['s_d']) && $i == $_GET['s_d']) or (!isset($_GET['s_d']) && $i == date('d'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo DAY_TEXT;?> </td>
      <td width="20" align="center">～</td>
      <td>
      <?php echo KEYWORDS_SEARCH_END_TEXT;?> 
      <select name="e_y">
      <?php
      for($i=2002; $i<=date('Y'); $i++) {
        if((isset($_GET['e_y']) && $i == $_GET['e_y']) or (!isset($_GET['e_y']) && $i == date('Y'))){
          echo '<option value="'.$i.'" selected>'.$i.'</option>'."\n" ;
        }else{
          echo '<option value="'.$i.'">'.$i.'</option>'."\n" ;
        } 
      }
      ?>    
      </select>
      <?php echo YEAR_TEXT;?> 
      <select name="e_m">
      <?php
      for($i=1; $i<13; $i++) {
        if((isset($_GET['e_m']) && $i == $_GET['e_m']) or (!isset($_GET['e_m']) && $i == date('m'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo MONTH_TEXT;?> 
      <select name="e_d">
      <?php
      for($i=1; $i<32; $i++) {
        if((isset($_GET['e_d']) && $i == $_GET['e_d']) or (!isset($_GET['e_d']) && $i == date('d'))){
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'" selected>'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        }else{
          echo '<option value="'.str_pad($i,2,0,STR_PAD_LEFT).'">'.str_pad($i,2,0,STR_PAD_LEFT).'</option>'."\n";
        } 
      }
      ?>    
      </select>
      <?php echo DAY_TEXT;?> </td>
        <td>&nbsp;</td>
        <td><input type="submit" value="<?php echo IMAGE_SEARCH;?>"></td>
      </tr>
    </table><br>
    </form>
        <?php tep_show_site_filter('keywords.php',true,array(0));?>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
            <?php
            if(!isset($_GET['type']) || $_GET['type'] == ''){
                    $_GET['type'] = 'asc';
            }
            if($keywords_type == ''){
                    $keywords_type = 'asc';
            }
            if(!isset($_GET['sort']) || $_GET['sort'] == ''){
              $keywords_str = 'cnt desc,orders_ref_keywords2 asc';
            }else if($_GET['sort'] == 'orders_ref_keywords'){
                  if($_GET['type'] == 'desc'){
                    $keywords_str = 'orders_ref_keywords2 desc';
                    $keywords_type = 'asc';
                  }else{
                    $keywords_str = 'orders_ref_keywords2 asc';
                    $keywords_type = 'desc';
                  }
            }else if($_GET['sort'] == 'cnt'){
                  if($_GET['type'] == 'desc'){
                    $keywords_str = 'cnt desc';
                    $keywords_type = 'asc';
                  }else{
                    $keywords_str = 'cnt asc';
                    $keywords_type = 'desc';
                  }
            }else if($_GET['sort'] == 'cnt_orders'){
                  if($_GET['type'] == 'desc'){
                    $keywords_str = 'rownum desc';
                    $keywords_type = 'asc';
                  }else{
                    $keywords_str = 'rownum asc';
                    $keywords_type = 'desc';
                  }
            }
            if($_GET['sort'] == 'orders_ref_keywords'){
                 if($_GET['type'] == 'desc'){
                    $orders_ref_keywords2 = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                    $orders_ref_keywords2 = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
            if($_GET['sort'] == 'cnt'){
                 if($_GET['type'] == 'desc'){
                    $cnt = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                    $cnt = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
            if($_GET['sort'] == 'cnt_orders'){
                 if($_GET['type'] == 'desc'){
                    $cnt_orders = "<font color='#c0c0c0'>".TEXT_SORT_ASC."</font><font color='#facb9c'>".TEXT_SORT_DESC."</font>";
                 }else{
                    $cnt_orders = "<font color='#facb9c'>".TEXT_SORT_ASC."</font><font color='#c0c0c0'>".TEXT_SORT_DESC."</font>";
                 }
            }
        if($_GET['s_y'] == ''){
           $_GET['s_y'] = date('Y');
        }
        if($_GET['s_m'] == ''){
           $_GET['s_m'] = date('m')-1;
        }
        if($_GET['s_d'] == ''){
           $_GET['s_d'] = date('d');
        }
        if($_GET['e_y'] == ''){
           $_GET['e_y'] = date('Y');
        }
        if($_GET['e_m'] == ''){
           $_GET['e_m'] = date('m');
        }
        if($_GET['e_d'] == ''){
           $_GET['e_d'] = date('d');
        }

            $keywords_table_params = array('width' => '100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
            $notice_box = new notice_box('','',$keywords_table_params);  
            $keywords_table_row = array();
            $keywords_title_row = array();
            $keywords_title_row[] = array('params' => 'class="dataTableHeadingContent"','text' => '<input type="checkbox">');
            if(isset($_GET['sort']) && $_GET['sort'] == 'cnt_orders'){
            $keywords_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('keywords.php','sort=cnt_orders&type='.$keywords_type.'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).' &page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_NUMBER.$cnt_orders.'</a>');
            }else{
            $keywords_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('keywords.php','sort=cnt_orders&type=desc&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).' &page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.TABLE_HEADING_NUMBER.$cnt_orders.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'orders_ref_keywords'){
            $keywords_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('keywords.php','sort=orders_ref_keywords&type='.$keywords_type.'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).' &page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.KEYWORDS_TABLE_COLUMN_ONE_TEXT.$orders_ref_keywords2.'</a>');
            }else{
            $keywords_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('keywords.php','sort=orders_ref_keywords&type=desc&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).' &page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.KEYWORDS_TABLE_COLUMN_ONE_TEXT.$orders_ref_keywords2.'</a>');
            }
            if(isset($_GET['sort']) && $_GET['sort'] == 'cnt'){
            $keywords_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('keywords.php','sort=cnt&type='.$keywords_type.'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).' &page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.KEYWORDS_TABLE_COLUMN_TWO_TEXT.$cnt.'</a>');
            }else{
            $keywords_title_row[] = array('params' => 'class="dataTableHeadingContent_order"','text' => '<a href="'.tep_href_link('keywords.php','sort=cnt&type=desc&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&e_d='.$_GET['e_d']).' &page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&id='.$_GET['id'].'">'.KEYWORDS_TABLE_COLUMN_TWO_TEXT.$cnt.'</a>');
            }
            $keywords_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"','text' => TABLE_HEADING_ACTION);
            $keywords_table_row[] = array('params' => 'class="dataTableHeadingRow"','text' => $keywords_title_row);
  $ref_site_sql= "
    select * from (select (@mycnt := @mycnt + 1) as rownum,cnt,orders_ref_keywords2 from (
      select count(orders_id) as cnt , concat( ifnull( orders_ref_keywords, '' ) ,
        if( orders_adurl is null, '', '(Adsense)' ) ) AS
      orders_ref_keywords2,site_id as s_id
      from " . TABLE_ORDERS . " o, ".TABLE_SITES." s
      where s.id = o.site_id
        and orders_ref_keywords IS NOT NULL
        and orders_ref_keywords != '' and
        " .$sql_site_where. 
        (isset($_GET['s_y']) && isset($_GET['s_m']) && isset($_GET['s_d']) ? " and o.date_purchased > '".$_GET['s_y'].'-'.$_GET['s_m'].'-'.$_GET['s_d'] ."'" : " and o.date_purchased > '".date('Y-m-d H:i:s', time()-(86400*30)) . "' ") . 
        (isset($_GET['e_y']) && isset($_GET['e_m']) && isset($_GET['e_d']) ? " and o.date_purchased < '".$_GET['e_y'].'-'.$_GET['e_m'].'-'.$_GET['e_d'] ." 23:59:59'" : '') . "
      group by orders_ref_keywords2
    ) s order by cnt desc,orders_ref_keywords2 asc) q
    order by ".$keywords_str;
  $keywords_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $ref_site_sql, $keywords_query_numrows);
  tep_db_query("set @mycnt=0");
  $ref_site_query = tep_db_query($ref_site_sql);  
  $keywords_num = tep_db_num_rows($ref_site_query);
  if($_GET['sort'] == 'cnt_orders' && $_GET['type'] == 'desc'){
  $i = $keywords_num;
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
      $onclick = 'onClick="document.location.href=\''.tep_href_link('keywords.php','sort='.$_GET['sort'].'&type='.$_GET['type'].'&s_y='.$_GET['s_y'].'&s_m='.$_GET['s_m'].'&s_d='.$_GET['s_d'].'&e_y='.$_GET['e_y'].'&e_m='.$_GET['e_m'].'&page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&e_d='.$_GET['e_d']).'&id='.$i.'\'"';
      if($_GET['id'] == $i){
      $keywords_params = 'class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ';
      }else{
      $keywords_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
      }
      $keywords_info = array();
      $keywords_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => '<input type="checkbox" disabled="disabled">'
          );
      $keywords_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => $ref_site['rownum']
          );
      $keywords_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => preg_replace('/%./','',$ref_site['orders_ref_keywords2'])
          );
      $keywords_info[] = array(
          'params' => 'class="dataTableContent"'.$onclick,
          'text'   => $ref_site['cnt']
          );
      $keywords_info[] = array(
          'params' => 'class="dataTableContent" align="right"',
          'text'   => tep_image('images/icons/info_gray.gif') 
          );

  if($_GET['sort'] == 'cnt_orders' && $_GET['type'] == 'desc'){
    $i--;
  }else{
    $i++;
  }
    $keywords_table_row[] = array('params' => $keywords_params, 'text' => $keywords_info);
  }
    $notice_box->get_contents($keywords_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="margin-top:5px;">
      <tr>
        <td>
         <?php
           if($keywords_num > 0){
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
           <td class="smallText" valign="top"><?php echo $keywords_split->display_count($keywords_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
           <td class="smallText" align="right"><div class="td_box"><?php echo $keywords_split->display_links($keywords_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'cID'))); ?></div></td>
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
