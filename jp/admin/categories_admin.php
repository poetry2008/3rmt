<?php
/*
  $Id$
*/


require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$cPath_yobi = cpathPart($_GET['cPath'],1);
$currencies = new currencies();

function in_dougyousya($d_id, $dougyousya) {
  foreach ($dougyousya as $d) {
    if ($d['dougyousya_id'] === $d_id) {
      return true;
    }
  }
  return false;
}

//如果包括 insert update setflag 则加载reset_seo_cache.php @todo 好像没有用
if ( eregi("(insert|update|setflag)", $action) ) include_once('includes/reset_seo_cache.php');

//开始 处理请求{{
if (isset($_GET['action']) && $_GET['action']) {
  switch ($_GET['action']) 
    {
    case 'all_update': //一括更新　　
      require('includes/set/all_update.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $HTTP_GET_VARS['cPath'] . '&pID=' .$products_id));
      break;
/*
    case 'toggle':
      $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
      if ($_GET['cID']) {
        $cID = intval($_GET['cID']);
        $site_id = 0;
        if (isset($_GET['status']) && ($_GET['status'] == 0 || $_GET['status'] == 1 || $_GET['status'] == 2 || $_GET['status'] == 3)) {
          tep_set_category_link_product_status($cID, $_GET['status'], $site_id); 
        }
      }
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' .  $HTTP_GET_VARS['cPath'].$c_page));
      break;
    case 'setflag':
      //require('includes/set/setflag.php');
      $p_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
       
      tep_set_all_product_status($_GET['pID'], $_GET['flag']); 
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' .  $_GET['cPath'].$p_page));
      break;
*/
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
	 if(isset($_GET['cPath']) && $_GET['cPath']!=""){
		if(strpos($_GET['cPath'],"_")){
		$cpath_arr = explode("_",$_GET['cPath']);
	 	$cpath = end($cpath_arr);
		}else{
	        $cpath = $_GET['cPath']	;
		}
$categories_query = tep_db_query("select categories_name from ".TABLE_CATEGORIES_DESCRIPTION." where categories_id='".$cpath."' and site_id='".$site_id."'");
$categories_array = tep_db_fetch_array($categories_query);
echo CATEGORY_ADMIN_TITLE."&nbsp;&nbsp;&nbsp;".$categories_array['categories_name'];
	}
	
	else{
	echo CATEGORY_ADMIN_TITLE; 
	}
?>

</title>
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
  <script type="text/javascript" src="includes/general.js"></script>
  <script type="text/javascript" src="includes/javascript/jquery.js"></script>
  <script type="text/javascript" src="includes/javascript/udlr.js"></script>
  <script type="text/javascript" src="includes/set/c_admin.js"></script>
  <script language="javascript" src="includes/javascript/jquery_include.js"></script>
  <script language="javascript" src="includes/javascript/one_time_pwd.js"></script>
  <script type="text/javascript">
$(window).resize(function (){
    var menu_div_width = $('#categories_right_td').width();
    if(menu_div_width>=480){
      $('#categories_tree').animate({width:(menu_div_width-5)+"px"});
    }
});
  $(document).ready(function(){
    $(".udlr").udlr();
    ajaxLoad('<?php echo $cPath;?>');
    var menu_div_width = $('#categories_right_td').width();
<?php if ($_COOKIE['tarrow'] == 'open') {?>
    menu_div_width = menu_div_width-125;
<?php } ?>
    if(menu_div_width>=480){
      $('#categories_tree').animate({width:(menu_div_width-10)+"px"});
    }else{
      $('#categories_tree').animate({width:"470px"});
    }
  })
  </script>
<?php require("includes/note_js.php");?>
  </head>
  <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
  <?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
  <div id="spiffycalendar" class="text"></div>
  <!-- header //-->
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <!-- header_eof //-->
  <!-- body //-->
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
  <td <?php if ($ocertify->npermission < 10) {?>width='1'<?php } else {?> width="<?php echo BOX_WIDTH; ?>"<?php }?> valign="top">
  <table border="0" <?php if ($ocertify->npermission <10) {?>width='1'<?php } else {?> width="<?php echo BOX_WIDTH; ?>"<?php }?> cellspacing="1" cellpadding="1" class="columnLeft">
  <!-- left_navigation //-->
  <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  <!-- left_navigation_eof //-->
  </table>
  </td>
  <!-- body_text //-->
  <td width="100%" valign="top" id='categories_right_td'>
  <?php echo $notes;?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
  <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading" height="40">
      <?php echo CATEGORY_ADMIN_TITLE;?> 
      &nbsp; 
      <?php
      if ($cPath) {
        $display_ca_str = display_category_link($cPath, $current_category_id,
            $languages_id, 0, FILENAME_CATEGORIES_ADMIN,true); 
        echo $display_ca_str; 
        if (empty($display_ca_str)) {
          echo get_same_level_category($cPath, $current_category_id, $languages_id,
              $site_id, FILENAME_CATEGORIES_ADMIN,true); 
        }
      }else{
        echo "<td class='smallText' align='right'>";
      }
      ?>
      <?php echo tep_draw_form('search', FILENAME_CATEGORIES_ADMIN, '', 'get') . "\n"; ?> <?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search', isset($_GET['search'])?$_GET['search']:'') . "\n"; ?>
      <input type = "submit" value = "<?php echo IMAGE_SEARCH;?>" >
      </form></td>
      <td class="smallText" width="60" align="right">
        <div id="gotomenu">
          <a href="javascript:void(0)" onclick="$('#categories_tree').toggle()"><?php echo CATEGORY_ADMIN_CATREE_TITLE;?></a>
          <div id="categories_tree">
          <?php
            require(DIR_WS_CLASSES . 'category_tree.php');
            $osC_CategoryTree = new osC_CategoryTree(true, true); 
            echo $osC_CategoryTree->buildTree();
          ?>
          </div>
        </div>
    <?php //echo tep_draw_form('goto', FILENAME_CATEGORIES_ADMIN, '', 'get') . "\n"; ?> <?php //echo HEADING_TITLE_GOTO . ' ' . tep_draw_pull_down_menu('cPath', tep_get_category_tree_cpath(), $current_category_id, 'onChange="this.form.submit();"') . "\n"; ?><!--</form>-->
      
      </td>
    </tr>
  </table>
  </td>
  </tr>
  <tr>
  <!--main-->
  <td>
  <form name="myForm1" action="categories_admin.php?<?php echo "cPath=".$cPath."&pID=".$products['products_id']."&action=all_update"; ?>" method="POST" onSubmit="return false">
  <input type="hidden" name="flg_up" value="" />
  <?php
    // 取得価格/業者更新时间
    $set_menu_list  = tep_db_fetch_array(tep_db_query("select * from set_menu_list where categories_id='".$current_category_id."'"));
    $kakaku_updated = $set_menu_list?date('n/j G:i',strtotime($set_menu_list['last_modified'])):'';
    ?>
  <?php
  $comment = tep_db_fetch_array(tep_db_query("select * from set_comments where categories_id='".$current_category_id."'"));
  if ($comment) {
    ?>
    <table>
      <tr>
      <td  class="dataTableContent"><b><?php echo CATEGORY_ADMIN_SINGLE_PRICE;?></b> </td>
      <td  class="dataTableContent"><?php echo nl2br($comment['rule']);?></td>
      </tr>
    </table>
  <?
  }
  ?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <!--dataTableHeadingRow-->
  <tr class="dataTableHeadingRow" valign="top">
  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></td>
  <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_CATEGORIES_PREORDER_PRODUCTS_NUM; ?></td>
  <td class="dataTableHeadingContent" align="center" ><?php echo TABLE_HEADING_CATEGORIES_ZHUWEN_NUM;?></td>
  <td class="dataTableHeadingContent" align="center" ><?php echo TABLE_HEADING_CATEGORIES_JIAKONG;?></td>
  <td class="dataTableHeadingContent" align="center" ><?php echo TABLE_HEADING_CATEGORIES_SHIZAIKU;?></td>
  <td class="dataTableHeadingContent" align="center" ></td>
  <td class="dataTableHeadingContent" align="center" >
      <a style="font-weight:bold;" href="cleate_list.php?cid=<?php echo $cPath_yobi;?>&action=prelist&cPath=<?php echo $_GET['cPath'];?>"><?php echo TABLE_HEADING_CATEGORIES_YEZHE?></a><br>
      <small style="font-weight:bold;font-size:12px;"><?php echo str_replace(' ','<br>',$kakaku_updated);?></small>
  </td>
  <?php  
  //读取当前的计算公式  
  $res=tep_db_query("select bairitu from set_auto_calc where parent_id='".$current_category_id."'"); 
  $col=tep_db_fetch_array($res);
  if (!$col) $col['bairitu'] = 1.1;
?>
<td class="dataTableHeadingContent" align="center" ><?php echo $col['bairitu']?><?php echo CATEGORY_ADMIN_BEI_TEXT;?></td>
  <?php
  if ($cPath_yobi){
    $res=tep_db_query("select count(*) as cnt from set_dougyousya_names sdn
        ,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id
        and sdc.categories_id = '".$cPath_yobi."'");
    $count_dougyousya=tep_db_fetch_array($res);
    if($count_dougyousya['cnt'] > 0) {
      $res=tep_db_query("select * from set_dougyousya_names sdn
          ,set_dougyousya_categories sdc  where sdn.dougyousya_id =
          sdc.dougyousya_id and sdc.categories_id = '".$cPath_yobi. "' ORDER BY sdc.dougyousya_id ASC");
      while($col_dougyousya=tep_db_fetch_array($res)){
        $i++;
        $dougyousya_history = tep_db_fetch_array(tep_db_query("select * from set_dougyousya_history where categories_id='".$current_category_id."' and dougyousya_id='".$col_dougyousya['dougyousya_id']."' order by last_date desc"));
        $dougyousya_updated = $dougyousya_history?date('n/j G:i',strtotime($dougyousya_history['last_date'])):'';
        ?>
        <td class='dataTableHeadingContent' align='center' >
          <a style="font-weight:bold;" href='javascript:void(0);' onClick=dougyousya_history('history.php',<?php echo $cPath_yobi;?>,<?php echo $current_category_id;?>,'dougyousya_categories','<?php echo $col_dougyousya['dougyousya_id'];?>','<?php echo $_GET['cPath'];?>')><?php echo $col_dougyousya['dougyousya_name'];?></a>
          <input type='hidden' name='d_id[]' value='<?php echo $col_dougyousya['dougyousya_id'];?>'>
          <br><small style="font-weight:bold;font-size:12px"><?php echo str_replace(' ','<br>',$dougyousya_updated);?></small>
        </td>
        <?php
      }
    } else {
      $count_dougyousya['cnt'] = 1;
      echo "<td class='dataTableHeadingContent' align='center' >".CATEGORY_ADMIN_SETTING_TONGYE."</td>";
    }
  }
?>
  <td class="dataTableHeadingContent" align="center" ><?php echo TABLE_HEADING_CATEGORIES_NOW_PRICE;?></td>
  <td class="dataTableHeadingContent" align="center" ><?php echo TABLE_HEADING_CATEGORIES_SETTING_PRICE;?></td>
  <td class="dataTableHeadingContent" align="center" ><?php echo TABLE_HEADING_CATEGORIES_CHAE;?></td>
  <?php /*
  <td class="dataTableHeadingContent" align="center" <?php if ($ocertify->npermission < 10) {?>width='1'<?php } else {?>width='180'<?php }?>>
  <div class="datatablecontent_warpper">
  <?php
  if ($ocertify->npermission >7) {
    echo  TABLE_HEADING_STATUS; 
  }
  ?>
  </div>
  </td>
  */ ?>
  <td class="dataTableHeadingContent" align="center">&nbsp;</td>
  </tr>
  <!--dataTableHeadingRow end-->
<?php
  $categories_count = 0;
$rows = 0;
if (isset($_GET['search']) && $_GET['search']) {
  $categories_query_raw = "
        select c.categories_id, 
               cd.categories_status, 
               cd.categories_name, 
               cd.categories_image2, 
               cd.categories_image3, 
               cd.categories_meta_text, 
               c.categories_image, 
               c.parent_id, 
               c.sort_order, 
               c.date_added, 
               c.last_modified 
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.categories_id = cd.categories_id 
          and cd.language_id = '" . $languages_id . "' 
          and cd.categories_name like '%" . $_GET['search'] . "%' 
          and cd.site_id = '0'
        order by c.sort_order, cd.categories_name";
} else {
  $categories_query_raw = "
        select c.categories_id, 
               cd.categories_status, 
               cd.categories_name, 
               cd.categories_image2, 
               cd.categories_image3, 
               cd.categories_meta_text, 
               c.categories_image, 
               c.parent_id, 
               c.sort_order, 
               c.date_added, 
               c.last_modified 
        from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd 
        where c.parent_id = '" . $current_category_id . "' 
          and c.categories_id = cd.categories_id 
          and cd.language_id = '" . $languages_id . "' 
          and cd.site_id = '0'
        order by c.sort_order, cd.categories_name";
}
$categories_query = tep_db_query($categories_query_raw);
while ($categories = tep_db_fetch_array($categories_query)) {
  $categories_count++;
  $rows++;

  // Get parent_id for subcategories if search 
  if (isset($_GET['search']) && $_GET['search']) $cPath= $categories['parent_id'];

  if ( 
      ((!isset($_GET['cID']) || !$_GET['cID']) && (!isset($_GET['pID']) || !$_GET['pID']) || (isset($_GET['cID']) && $_GET['cID'] == $categories['categories_id'])) 
      && (!isset($cInfo) || !$cInfo) 
      && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_')
       ) {
    $category_childs = array('childs_count' => tep_childs_in_category_count($categories['categories_id']));
    $category_products = array('products_count' => tep_products_in_category_count($categories['categories_id']));

    $cInfo_array = tep_array_merge($categories, $category_childs, $category_products);
    $cInfo = new objectInfo($cInfo_array);
  }

  // 列を色違いにする
  $even = 'dataTableSecondRow';
  $odd = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even;
  } else {
    $nowColor = $odd;
  }

  echo '              <tr class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES_ADMIN, tep_get_path($categories['categories_id'])) . '\'">' . "\n"; 
  ?>
  <td class="dataTableContent1">
   <?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, tep_get_path($categories['categories_id'])) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $categories['categories_name'] . '</b>'; ?>
  </td>
  <td class="dataTableContent" align="right" colspan="<?php echo 9 + $count_dougyousya['cnt'];?>">&nbsp;</td>
  <td class="dataTableContent5" align="center">&nbsp;</td>
<?php /*
<td class="dataTableContent4" align="center"><?php if ($ocertify->npermission == 15 or $ocertify->npermission == 10) {?>
<?php //if (!isset($_GET['cPath']) or !$_GET['cPath']){?>
<?php $c_page = (isset($_GET['page']))?'&page='.$_GET['page']:'';?>
                <?php if($categories['categories_status'] == '1'){?>
                  <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', '');?></a> <?php echo tep_image(DIR_WS_IMAGES .  'icon_status_red.gif', '');?> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=3&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', '');?></a>
                <?php } else if($categories['categories_status'] == '2'){?>
                  <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '');?></a> <?php echo tep_image(DIR_WS_IMAGES .  'icon_status_blue.gif', '');?> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=3&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', '');?></a>
                <?php } else if ($categories['categories_status'] == '3') {?>
                  <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', '');?></a> <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_black.gif', '');?>
                <?php } else {?>
                  <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '');?> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$_GET['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$_GET['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=3&cPath='.$HTTP_GET_VARS['cPath'].$c_page);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', '');?></a>
                <?php }?>
            <?php //}?>
<?php }?>
</td>
*/?>
<td>&nbsp;</td>
</tr>
  <!--dataTableRowSelected end-->
<?php }
  $products_count = 0;
if (isset($_GET['search']) && $_GET['search']) {
  $products_query_raw = "
        select p.products_id, 
               pd.products_name, 
               p.products_real_quantity + p.products_virtual_quantity as products_quantity,
               p.products_real_quantity, 
               p.products_virtual_quantity, 
               p.products_image,
               p.products_image2,
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_last_modified, 
               p.products_date_available, 
               pd.products_status, 
               p.products_bflag,
               p2c.categories_id 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        where p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and pd.products_name like '%" . $_GET['search'] . "%' 
          and pd.site_id='0'
          ".($ocertify->npermission>7?'':" and pd.products_status='1' ")."
        order by pd.products_name";
} else {
  $products_query_raw = "
        select p.products_id, 
               pd.products_name, 
               p.products_real_quantity + p.products_virtual_quantity as products_quantity,
               p.products_real_quantity, 
               p.products_virtual_quantity, 
               p.products_image,
               p.products_image2,
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_last_modified, 
               p.products_date_available, 
               p.products_bflag,
               pd.products_status 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        where p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = '" . $current_category_id . "' 
          and pd.site_id='0'
          ".($ocertify->npermission>7?'':" and pd.products_status='1' ")."
        order by p.sort_order,pd.products_name";
}
$products_split = new splitPageResults($_GET['page'], MAX_DISPLAY_PRODUCTS_ADMIN, $products_query_raw, $products_query_numrows);
$products_query = tep_db_query($products_query_raw);
while ($products = tep_db_fetch_array($products_query)) {
  $products_count++;
  $rows++;

  // Get categories_id for product if search 
  if (isset($_GET['search']) && $_GET['search']) $cPath=$products['categories_id'];

  if ( 
      ((!isset($_GET['pID']) || !$_GET['pID']) && (!isset($_GET['cID']) || !$_GET['cID']) || (isset($_GET['pID']) && $_GET['pID'] == $products['products_id'])) 
      && (!isset($pInfo) || !$pInfo) 
      && (!isset($cInfo) || !$cInfo) 
      && (!isset($_GET['action']) || substr($_GET['action'], 0, 4) != 'new_') 
       ) {
    // find out the rating average from customer reviews
    $reviews_query = tep_db_query("select (avg(reviews_rating) / 5 * 100) as average_rating from " . TABLE_REVIEWS . " where products_id = '" . $products['products_id'] . "'");
    $reviews = tep_db_fetch_array($reviews_query);
    $pInfo_array = tep_array_merge($products, $reviews);
    $pInfo = new objectInfo($pInfo_array);
  }

  // 列を色違いにする
  // products list
  $even = 'dataTableSecondRow';
  $odd = 'dataTableRow';
  if (isset($nowColor) && $nowColor == $odd) {
    $nowColor = $even;
  } else {
    $nowColor = $odd;
  }

  echo '              <tr class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"> ' . "\n";

  $res_kaku=tep_db_query("select * from set_menu_list where categories_id='".$current_category_id."' ORDER BY set_list_id ASC");
  $i_cnt=0;
  while($col_kaku=tep_db_fetch_array($res_kaku)){
    $menu_datas[$i_cnt][0]=$col_kaku['products_id'];
    $menu_datas[$i_cnt][1]=tep_get_kakuukosuu_by_products_id($col_kaku['products_id']);
    $menu_datas[$i_cnt][2]=$col_kaku['kakaku'];
    $i_cnt++;
  }
  ?>
  <td class="dataTableContent1">
<?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, 'from=admin&cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;&nbsp;';?>
     <?php 
     //echo '<a style="margin-left:-4px;" href="orders.php?real_name=true&search_type=products_name&keywords=' . urlencode($products['products_name']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_time.gif', '', 16, 16) . '</a>&nbsp;&nbsp;<span id="products_name_'.$products['products_id'].'">' . $products['products_name'] . '</span>'; 
echo '<a style="margin-left:-4px;" href="orders.php?search_type=products_id&products_id=' .$products['products_id']. '">' . tep_image(DIR_WS_IMAGES . 'icon_time.gif', '', 16, 16) . '</a>&nbsp;&nbsp;<span id="products_name_'.$products['products_id'].'">' . $products['products_name'] . '</span>'; 

  ?>
  </td>
      <?php
      for($i=0;$i<$i_cnt;$i++){
        if($products['products_id']==$menu_datas[$i][0]){
          $imaginary=$menu_datas[$i][1];
          $kakaku_treder=$menu_datas[$i][2];
          break;
        }else{
          $imaginary=0;
          $kakaku_treder=0;
        }
      }
      $target_cnt=$products_count-1;//同業者専用
  ?>
  <td class="dataTableContent6" align='right'>
  <?php
    $preorder_products_raw = tep_db_query("select sum(products_quantity) as pre_total from ".TABLE_PREORDERS_PRODUCTS." where products_id = '".$products['products_id']."'"); 
    $preorder_products_res = tep_db_fetch_array($preorder_products_raw);
    if ($preorder_products_res) {
      if ($preorder_products_res['pre_total']) {
        echo '<a href="preorders.php?keywords='.urlencode($products['products_id']).'&search_type=sproducts_id" target="_blank" style="text-decoration:underline;">';
        echo $preorder_products_res['pre_total'];
        echo '</a>';
      } else {
        echo ''; 
      }
    } 
    ?>
  </td>
  <td class="dataTableContent6" align='right'>
<?php
  if(tep_get_order_cnt_by_pid($products['products_id'])){
    echo '<a href="orders.php?keywords='.urlencode($products['products_id']).'&search_type=sproducts_id" target="_blank" style="text-decoration:underline;">';
    echo tep_get_order_cnt_by_pid($products['products_id']);
    echo '</a>';  
  } 
  ?></td>
  <?php //個数架空 ?>
  <td class="dataTableContent6" align='right' onmouseover='this.style.cursor="pointer"'  id='virtual_quantity_<?php echo $products['products_id']; ?>' onclick="update_virtual_quantity(<?php echo $products['products_id']; ?>)"><?php echo $imaginary;?></td>
<?php //数量 ?>
  <td class="dataTableContent6" align='right' onmouseover='this.style.cursor="pointer"' style="font-weight:bold;" id='quantity_<?php echo $products['products_id']; ?>' onclick="update_quantity(<?php echo $products['products_id']; ?>)"><?php echo $products['products_real_quantity'];?></td>
  <td class="dataTableContent6">
  <?php
    if (tep_check_show_isbuy($products['products_id'])) { 
      if (tep_check_best_sellers_isbuy($products['products_id'])) {
        $diff_oday = tep_calc_limit_time_by_order_id($products['products_id']); 
        if ($diff_oday !== '') {
          echo '<img src="images/icons/mae1.gif" alt="'.$diff_oday.PIC_MAE_ALT_TEXT.'" title="'.$diff_oday.PIC_MAE_ALT_TEXT.'">'; 
        } else {
          echo '<img src="images/icons/mae3.gif" alt="">'; 
        }
      } else {
        $diff_oday = tep_calc_limit_time_by_order_id($products['products_id'], true); 
        if ($diff_oday !== '') {
          echo '<img src="images/icons/mae2.gif" alt="'.$diff_oday.PIC_MAE_ALT_TEXT.'" title="'.$diff_oday.PIC_MAE_ALT_TEXT.'">'; 
        } else {
          echo '<img src="images/icons/mae3.gif" alt="">'; 
        }
      }
    }
  ?>
  </td>
  <td align='right' class="dataTableContent2" ><span class = 'TRADER_INPUT'  name="TRADER_INPUT[]"  id="TRADER_<?php echo $products['products_id']; ?>"><?php echo $kakaku_treder?round($kakaku_treder,2):0;?></span></td>
<?php //価格業者  ?>
  <td align='right' class="dataTableContent6" ><span name="INCREASE_INPUT" class = 'INCREASE_INPUT'>
    <?php //echo ceil($kakaku_treder*$col['bairitu']);?>
<?php
  if (strpos($col['bairitu'], '.') !== false) {
    $float_number = strlen(substr($col['bairitu'], strpos($col['bairitu'], '.')));
  } else {
    $float_number = 0;
  }
  echo ceil(number_format($col['bairitu']*$kakaku_treder,$float_number,'.',''));
?>
  </span></td>
                <?php //価格倍率  ?>
                <?php
if ($cPath_yobi){
                if($products_count==1)
                  {
                    $res       = tep_db_query("select count(*) as cnt from set_dougyousya_names sdn ,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id='".$cPath_yobi."'");
                    $count     = tep_db_fetch_array($res);
                    $radio_res = tep_db_query("select * from set_dougyousya_history where categories_id='".$current_category_id."' order by history_id asc");
                    $radio_col = tep_db_fetch_array($radio_res);
                    
                  }
              
              /*同業者価格を文字列で表示させる必要あり
                現在は代変でreadonlyと書いている
                取得方法はまだ書いていない
                history.phpのaction=dougyousyaに方法は書いてある
              */
              if($count['cnt'] > 0){
                $dougyousya = get_products_dougyousya($products['products_id']);
                $all_dougyousya = get_all_products_dougyousya($cPath_yobi, $products['products_id']);
                for($i=0;$i<$count['cnt'];$i++) {
                  echo "
                    <td class='dataTableContent2' align='left'>
                    <input type='radio' id='radio_".$target_cnt."_".$i."' value='".$all_dougyousya[$i]['dougyousya_id']."' name='chk[".$target_cnt."]' onClick='chek_radio(".$target_cnt.")'".(in_dougyousya($dougyousya, $all_dougyousya) ? ($all_dougyousya[$i]['dougyousya_id'] == $dougyousya?' checked':'') : ($i == 0 ? ' checked':'')).">
                    <span name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' >".get_dougyousya_history($products['products_id'], $all_dougyousya[$i]['dougyousya_id'])."</span>
                    </td>";//価格同業者
                }
              }else{
                echo "
                  <td class='dataTableContent6' align='center'>
                  <input type='radio' value='0' name='chk[".$target_cnt."]' checked>
                  <span name='TARGET_INPUT[]' id='target_".$target_cnt."_0' >0</span>
                  </td>";//価格同業者
              }
  }
              ?>
<td class="dataTableContent4" align="right" style="font-weight:bold"><div class="datatablecontent_warpper02"><?php
      $product_price = tep_get_products_price($products['products_id']);
      if ($product_price['sprice']) {
        echo '<s>' . $currencies->format($product_price['price']) . '</s> <span class="specialPrice">' . $currencies->format($product_price['sprice']) . '</span>';
      } else {
        echo $currencies->format($product_price['price']);
      }
?></div></td>
<td class="dataTableContent6" align="center"><input style="text-align:right;" pos="<?php echo $products_count;?>_1" class="udlr" type="text" size='6' value="<?php echo (int)abs($products['products_price']);?>" name="price[]" id="<?php echo "price_input_".$products_count; ?>" onblur="event_onblur(<?php echo $products_count; ?>)" onkeyup="clearNoNum(this);" onchange="event_onchange(<?php echo $products_count; ?>)"><span id="price_error_<?php echo $products_count; ?>"></span></td>
<td class="dataTableContent2" align="center"><?php echo (float)$products['products_price_offset'] > 0 ?"+":'';?><?php echo $products['products_price_offset'];?><input style="text-align:right;" pos="<?php echo $products_count;?>_2" class="_udlr" type="hidden" size='6' value="<?php echo $products['products_price_offset'];?>" name="offset[]" id="<?php echo "offset_input_".$products_count; ?>"><span id="offset_error_<?php echo $products_count; ?>" onchange="this.value=SBC2DBC(this.value)"></span></td>
<?php //サイト入力  ?>
<?php /*
<td class="dataTableContent5" align="center"><?php
if ($ocertify->npermission >= 10) { //表示制限
    $p_page = (isset($_GET['page']))?'&page='.$_GET['page']:''; 
    if ($products['products_status'] == '1') {
      echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' .  tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=2&pID=' .  $products['products_id'] . '&cPath=' . $cPath.$p_page) . '">' .  tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="' .  tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=0&pID=' .  $products['products_id'] . '&cPath=' . $cPath.$p_page) . '">' .  tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="' .  tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=3&pID=' .  $products['products_id'] . '&cPath=' . $cPath.$p_page) . '">' .  tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else if ($products['products_status'] == '2') {
      echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' .  $cPath.$p_page) . '">' . tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) .  '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' .  tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=0&pID=' .  $products['products_id'] . '&cPath=' . $cPath.$p_page) . '">' .  tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="' .  tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=3&pID=' .  $products['products_id'] . '&cPath=' . $cPath.$p_page) . '">' .  tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else if ($products['products_status'] == '3') {
      echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' .  $cPath.$p_page) . '">' . tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) .  '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=2&pID=' . $products['products_id'] . '&cPath=' .  $cPath.$p_page) . '">' . tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) .  '</a>&nbsp;<a href="' .  tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=0&pID=' .  $products['products_id'] . '&cPath=' .  $cPath.$p_page) . '">' .  tep_image(DIR_WS_IMAGES .  'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) .  '</a>&nbsp;' .  tep_image(DIR_WS_IMAGES . 'icon_status_black.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10); 
    } else {
      echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' .  $cPath.$p_page) . '">' . tep_image(DIR_WS_IMAGES .  'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) .  '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=2&pID=' . $products['products_id'] . '&cPath=' .  $cPath.$p_page) . '">' . tep_image(DIR_WS_IMAGES .  'icon_status_blue_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) .  '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10).'&nbsp;<a href="' .  tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=3&pID=' .  $products['products_id'] . '&cPath=' . $cPath.$p_page) . '">' .  tep_image(DIR_WS_IMAGES . 'icon_status_black_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    }
}
?></td>
*/?>
<td class="dataTableContent6" align='center'><?php 

  $last_modified_array = getdate(strtotime(tep_datetime_short($products['products_last_modified'])));
  $today_array = getdate();
  $last_modified = date('n/j H:i:s',strtotime(tep_datetime_short($products['products_last_modified'])));
  if (
     $last_modified_array["year"] == $today_array["year"] 
  && $last_modified_array["mon"] == $today_array["mon"] 
  && $last_modified_array["mday"] == $today_array["mday"]
  ) {
    if ($last_modified_array["hours"] >= ($today_array["hours"]-2)) {
      echo tep_image(DIR_WS_ICONS . 'signal_blue.gif', $last_modified);
    } elseif ($last_modified_array["hours"] >= ($today_array["hours"]-5)) {
      echo tep_image(DIR_WS_ICONS . 'signal_yellow.gif', $last_modified);
    } else {
      echo tep_image(DIR_WS_ICONS . 'signal_red.gif', $last_modified);
    }
  } else {
    echo tep_image(DIR_WS_ICONS . 'signal_blink.gif', $last_modified);
  }
  //echo '&nbsp;&nbsp;' . tep_image(DIR_WS_ICONS . 'battery_0.gif', '数量異常');
  ?>
  <input type="hidden" name="this_price[]" value="<?php echo (int)$special_price_check;?>" >
  <input type="hidden" name="proid[]"      value="<?php echo $products['products_id']; ?>" >
  <input type="hidden" name="pprice[]"     value="<?php echo abs($products['products_price']); ?>" >
  <input type="hidden" name="bflag[]"      value="<?php echo $products['products_bflag']; ?>" >
</td>
</tr>
<!--dataTableRowSelected end-->
<?php
}
?>
<?php
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

//$cPath_yobi=cpathPart($cPath_back);

/* リスト表示に必要な情報を得る */
if(empty($cPath_back)&&empty($cID)&&isset($cPath)){ 
  $res_list=tep_db_query("select parent_id from categories where categories_id
      ='".tep_db_prepare_input($cPath)."'");
  $col_list=tep_db_fetch_array($res_list);
  $cPath_yobi=$col_list['parent_id'];
}

//$cPath_back = isset($cPath_back) && $cPath_back ? 'cPath=' . $cPath_back : '';

?>
<!--dataTableRowSelected-->
<tr>
  <td align='right' colspan='<?php echo 11 + $count_dougyousya['cnt'];?>'>
    <input type="hidden" value="<?php echo $cPath; ?>"               name="cpath">
    <input type="hidden" value="<?php echo $cPath_yobi; ?>"          name="cpath_yobi">
    <input type="hidden" value="<?php echo $current_category_id; ?>" name="cID_list" >
  <?php if ($ocertify->npermission > 7) { ?>
    <input type='button' value='<?php echo CATEGORY_ADMIN_BUTTON_CAL_SETTING;?>' name='b[]' onClick="cleat_set('set_bairitu.php')">
  <?php }?>
  &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='<?php echo CATEGORY_ADMIN_BUTTON_LOGIN;?>' name='e[]' onClick="location.href='set_comment.php?cID=<?php echo $current_category_id;?>&cPath=<?php echo $_GET['cPath'];?>'">
  &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='<?php echo CATEGORY_ADMIN_BUTTON_XIEYE_PRICE;?>' name='d[]' onClick="list_display('<?php echo $cPath_yobi?$cPath_yobi:0;?>','<?php echo $current_category_id;?>','<?php echo $_GET['cPath'];?>')">
  &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="x" value="<?php echo CATEGORY_ADMIN_BUTTON_ALL_UPDATE;?>" onClick="all_update()"></td>
</tr>
<!--dataTableRowSelected end-->
<tr>
  <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
  <td class="smallText" align="right" colspan="<?php echo 10 + $count_dougyousya['cnt'];?>"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?> </td>
</tr>
<tr>
  <td colspan="<?php echo 11 + $count_dougyousya['cnt'];?>">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText"><?php echo CATEGORY_ADMIN_COUNTNUM_TEXT . '&nbsp;' . $categories_count .
         '<br>' . CATEGORY_ADMIN_PRODUCT_NUM . '&nbsp;' . $products_query_numrows; ?></td>
        <td align="right" class="smallText"><?php
  if ($cPath) {
    $rPath_back = isset($cPath_back) && $cPath_back ? 'cPath=' . $cPath_back : ''; 
    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, $rPath_back .  '&cID=' . $current_category_id) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>&nbsp;';
  }
?>
&nbsp;</td>
</tr>
<?php
// google start
define('TEXT_KEYWORD','キーワード');
define('TEXT_GOOGLE_SEARCH','はGOOGLEで%sがキーワードとしての検索結果');
define('TEXT_RENAME','リネーム');
define('TEXT_INFO_KEYWORD','キーワードを変更する');
define('TEXT_NO_SET_KEYWORD','キーワードを設置しない');
define('TEXT_NO_DATA','該当の情報は見つかりませんでした');
define('TEXT_LAST_SEARCH_DATA','最後から&nbsp;%s&nbsp;つの検索結果');
define('TEXT_FIND_DATA_STOP','%sをさがしましたが、表示を停止します。');
define('TEXT_NOT_ENOUGH_DATA','前からの&nbsp;50&nbsp;件検索結果に不重複な結果は&nbsp;%s&nbsp;件があります');
tep_display_google_results(FILENAME_CATEGORIES_ADMIN);
// google end
?>
</table>
</td>
</tr>
</table>
</form>
  <?php
  if ($comment) {
?>
<table>
  <tr>
    <td class="dataTableContent" align='right'><b><?php echo CATEGORY_ADMIN_LAST_MODIFIED_TEXT;?></b></td>
    <td class="dataTableContent"><?php echo date('Y/m/d H:i:s', strtotime($comment['last_modified']));?></td>
  </tr>
  <tr>
    <td class="dataTableContent" align='right'><b><?php echo CATEGORY_ADMIN_AUTHOR_TEXT;?></b></td>
    <td class="dataTableContent"><?php echo $comment['author'];?></td>
  </tr>
  <tr>
    <td class="dataTableContent" align='right'><b><?php echo CATEGORY_ADMIN_COMMENT_TEXT;?></b></td>
    <td class="dataTableContent">
<?php echo nl2br($comment['comment']);?>
    </td>
   </tr>
</table>
<?php
  }
  ?>
</td>
</tr>
</table>
</td>
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
