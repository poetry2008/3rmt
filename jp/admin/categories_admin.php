<?php
/*
  $Id$
*/


require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$cPath_yobi = cpathPart($_GET['cPath'],1);
//$colspan = 8;
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
    //case 'goto': //一括更新　　
      //tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $HTTP_GET_VARS['cPath'] . '&pID=' .$products_id));
      //break;
      //tep_db_prepare_input＝変数か文字列の判定 //更新するもの・・・特別価格と同業者の価格＋radioのチェック
    case 'all_update': //一括更新　　
      require('includes/set/all_update.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $HTTP_GET_VARS['cPath'] . '&pID=' .$products_id));
      break;
    case 'toggle':
      //require('includes/set/toggle.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $HTTP_GET_VARS['cPath']));
      break;
    case 'setflag':
      //require('includes/set/setflag.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $_GET['cPath']));
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
  <script type="text/javascript" src="includes/general.js"></script>
  <script type="text/javascript" src="includes/javascript/jquery.js"></script>
  <script type="text/javascript" src="includes/javascript/udlr.js"></script>
  <script type="text/javascript" src="includes/set/c_admin.js"></script>
  <script type="text/javascript">
  $(document).ready(function(){
    $(".udlr").udlr();
    ajaxLoad('<?php echo $cPath;?>');
  })
  </script>
  </head>
  <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
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
  <td width="100%" valign="top">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
  <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td class="pageHeading">商品卸価格管理</td>
      <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
      <td class="smallText" align="right"><?php echo tep_draw_form('search', FILENAME_CATEGORIES_ADMIN, '', 'get') . "\n"; ?> <?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search', isset($_GET['search'])?$_GET['search']:'') . "\n"; ?></form></td>
      <td class="smallText" align="right"><?php echo tep_draw_form('goto', FILENAME_CATEGORIES_ADMIN, '', 'get') . "\n"; ?> <?php echo HEADING_TITLE_GOTO . ' ' . tep_draw_pull_down_menu('cPath', tep_get_category_tree_cpath(), $current_category_id, 'onChange="this.form.submit();"') . "\n"; ?></form></td>
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
      <td  class="dataTableContent"><b>単価ルール:</b> </td>
      <td  class="dataTableContent"><?php echo nl2br($comment['rule']);?></td>
      </tr>
    </table>
  <?
  }
  ?>
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <!--dataTableHeadingRow-->
  <tr class="dataTableHeadingRow" valign="top">
  <td class="dataTableHeadingContent" height="30"><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></td>
  <td class="dataTableHeadingContent" align="right" width="50">架空</td>
  <td class="dataTableHeadingContent" align="right" width="50">数量</td>
  <td class="dataTableHeadingContent" align="right" width="50">
      <a style="font-weight:bold;" href="cleate_list.php?cid=<?php echo $cPath_yobi;?>&action=prelist&cPath=<?php echo $_GET['cPath'];?>">業者</a><br>
      <small style="font-weight:bold;font-size:12px;"><?php echo $kakaku_updated;?></small>
  </td>
  <?php  
  //读取当前的计算公式  
  $res=tep_db_query("select bairitu from set_auto_calc where parent_id='".$current_category_id."'"); 
  $col=tep_db_fetch_array($res);
  if (!$col) $col['bairitu'] = 1.1;
?>
<td class="dataTableHeadingContent" align="right" width="50"><?php echo $col['bairitu']?>倍</td>
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
        <td class='dataTableHeadingContent' align='center'>
          <a style="font-weight:bold;" href='javascript:void(0);' onClick=dougyousya_history('history.php',<?php echo $cPath_yobi;?>,<?php echo $current_category_id;?>,'dougyousya_categories','<?php echo $col_dougyousya['dougyousya_id'];?>','<?php echo $_GET['cPath'];?>')><?php echo $col_dougyousya['dougyousya_name'];?></a>
          <input type='hidden' name='d_id[]' value='<?php echo $col_dougyousya['dougyousya_id'];?>'>
          <br><small style="font-weight:bold;font-size:12px"><?php echo $dougyousya_updated;?></small>
        </td>
        <?php
      }
    } else {
      $count_dougyousya['cnt'] = 1;
      //echo "<td class='dataTableHeadingContent' align='center' width='100'><a href='cleate_dougyousya.php'>同業者未設定</a></td>";
      echo "<td class='dataTableHeadingContent' align='center' width='100'>同業者未設定</td>";
    }
  }
?>
  <td class="dataTableHeadingContent" align="center" width='100'>現在単価</td>
  <td class="dataTableHeadingContent" align="center" width='50'>単価設定</td>
  <td class="dataTableHeadingContent" align="center" width='30'>増減</td>
  <td class="dataTableHeadingContent" align="center" <?php if ($ocertify->npermission < 10) {?>width='1'<?php } else {?>width='80'<?php }?>><?php
  if ($ocertify->npermission >7) {
    echo  TABLE_HEADING_STATUS; 
  }
  ?></td>
  <td class="dataTableHeadingContent" align="center" width='80'>&nbsp;</td>
  </tr>
  <!--dataTableHeadingRow end-->
<?php
  $categories_count = 0;
$rows = 0;
if (isset($_GET['search']) && $_GET['search']) {
  $categories_query_raw = "
        select c.categories_id, 
               c.categories_status, 
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
               c.categories_status, 
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

  //if ( (isset($cInfo) && is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) {
  //  echo ' <!--dataTableRowSelected--> <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ' . ' onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES_ADMIN, tep_get_path($categories['categories_id'])) . '\'">' . "\n";
  //} else {
    echo '              <tr class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES_ADMIN, tep_get_path($categories['categories_id'])) . '\'">' . "\n"; 
  //}
  ?>
  <td class="dataTableContent">
   <?php echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, tep_get_path($categories['categories_id'])) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $categories['categories_name'] . '</b>'; ?>
  </td>
  <td class="dataTableContent" align="right" colspan="<?php echo 7 + $count_dougyousya['cnt'];?>">&nbsp;</td>
  <td class="dataTableContent" align="center">
<?php if ($ocertify->npermission == 15 or $ocertify->npermission == 10) {?>
<?php if (!isset($_GET['cPath']) or !$_GET['cPath']){?>
                <?php if($categories['categories_status'] == '1'){?>
                  <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$HTTP_GET_VARS['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', '');?></a> <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '');?> 
                <?php } else if($categories['categories_status'] == '2'){?>
                  <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '');?></a> <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', '');?> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$HTTP_GET_VARS['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '');?></a>
                <?php } else {?>
                  <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', '');?> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$_GET['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', '');?></a> <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=1&cPath='.$_GET['cPath']);?>"><?php echo tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', '');?></a> 
                <?php }?>
            <?php }?>
<?php }?>
      </td>
<td class="dataTableContent" align="right">&nbsp;</td>
</tr>
  <!--dataTableRowSelected end-->
<?php }
  $products_count = 0;
if (isset($_GET['search']) && $_GET['search']) {
  $products_query_raw = "
        select p.products_id, 
               pd.products_name, 
               p.products_quantity, 
               p.products_image,
               p.products_image2,
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_last_modified, 
               p.products_date_available, 
               p.products_status, 
               p.products_bflag,
               p2c.categories_id 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        where p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and pd.products_name like '%" . $_GET['search'] . "%' 
          and pd.site_id='0'
          ".($ocertify->npermission>7?'':" and p.products_status='1' ")."
        order by pd.products_name";
} else {
  $products_query_raw = "
        select p.products_id, 
               pd.products_name, 
               p.products_quantity, 
               p.products_image,
               p.products_image2,
               p.products_image3, 
               p.products_price, 
               p.products_price_offset,
               p.products_date_added, 
               p.products_last_modified, 
               p.products_date_available, 
               p.products_bflag,
               p.products_status 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        where p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = '" . $current_category_id . "' 
          and pd.site_id='0'
          ".($ocertify->npermission>7?'':" and p.products_status='1' ")."
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
    $menu_datas[$i_cnt][1]=$col_kaku['kakuukosuu'];
    $menu_datas[$i_cnt][2]=$col_kaku['kakaku'];
    $i_cnt++;
  }
  ?>
  <td class="dataTableContent">
     <?php 
     echo '<a href="orders.php?search_type=products_name&keywords=' . urlencode($products['products_name']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_time.gif', '', 16, 16) . '</a>&nbsp;&nbsp;<span id="products_name_'.$products['products_id'].'">' . $products['products_name'] . '</span>'; 
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
  <?php ////個数架空 ?>
  <td class="dataTableContent" align='right'>
     <!--    <input type="text" size="5" value="<?php  echo $imaginary; ?>" name='imaginary[<?php echo $products_count-1;?>]' /> -->
     <span align='center' > <?php echo $imaginary;?></span>
  </td>
<?php ////数量 ?>
  <td class="dataTableContent" align='right' onmouseover='this.style.cursor="pointer"' style="font-weight:bold;" id='quantity_<?php echo $products['products_id']; ?>' onclick="update_quantity(<?php echo $products['products_id']; ?>)"><?php echo $products['products_quantity'];?></td>
  <td align='center' class="dataTableContent" ><span class = 'TRADER_INPUT'  name="TRADER_INPUT[]"  id="TRADER_<?php echo $products['products_id']; ?>"><?php echo $kakaku_treder?$kakaku_treder:0;?></span></td>
<?php //価格業者  ?>
  <td align='center' class="dataTableContent" ><span name="INCREASE_INPUT" class = 'INCREASE_INPUT'>
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
                    <td class='dataTableContent' align='center'>
                    <input type='radio' id='radio_".$target_cnt."_".$i."' value='".$all_dougyousya[$i]['dougyousya_id']."' name='chk[".$target_cnt."]' onClick='chek_radio(".$target_cnt.")'".(in_dougyousya($dougyousya, $all_dougyousya) ? ($all_dougyousya[$i]['dougyousya_id'] == $dougyousya?' checked':'') : ($i == 0 ? ' checked':'')).">
                    <span name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' >".get_dougyousya_history($products['products_id'], $all_dougyousya[$i]['dougyousya_id'])."</span>
                    </td>";//価格同業者
                  /*echo "
                    <td class='dataTableContent' >
                    <input type='radio' value='".$all_dougyousya[$i]['dougyousya_id']."' name='chk[".$target_cnt."]' onClick='chek_radio(".$target_cnt.")'".($all_dougyousya[$i]['dougyousya_id'] == $dougyousya?' checked':'').">
                    <span name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' >".get_dougyousya_history($products['products_id'], $all_dougyousya[$i]['dougyousya_id'])."</span>
                    </td>";//価格同業者*/
                }
              }else{
                echo "
                  <td class='dataTableContent' align='center'>
                  <input type='radio' value='0' name='chk[".$target_cnt."]' checked>
                  <span name='TARGET_INPUT[]' id='target_".$target_cnt."_0' >0</span>
                  </td>";//価格同業者
              }
  }
              ?>
<td class="dataTableContent" align="center" style="font-weight:bold"><?php
      $product_price = tep_get_products_price($products['products_id']);
      if ($product_price['sprice']) {
        echo '<s>' . $currencies->format($product_price['price']) . '</s> <span class="specialPrice">' . $currencies->format($product_price['sprice']) . '</span>';
      } else {
        echo $currencies->format($product_price['price']);
      }
?></td>
<td class="dataTableContent" align="center"><input style="text-align:right;" pos="<?php echo $products_count;?>_1" class="udlr" type="text" size='6' value="<?php echo (int)$products['products_price'];?>" name="price[]" id="<?php echo "price_input_".$products_count; ?>" onblur="event_onblur(<?php echo $products_count; ?>)" onchange="event_onchange(<?php echo $products_count; ?>)"><span id="price_error_<?php echo $products_count; ?>"></span></td>
<td class="dataTableContent" align="right"><?php echo $products['products_price_offset'];?><input style="text-align:right;" pos="<?php echo $products_count;?>_2" class="_udlr" type="hidden" size='6' value="<?php echo $products['products_price_offset'];?>" name="offset[]" id="<?php echo "offset_input_".$products_count; ?>"><span id="offset_error_<?php echo $products_count; ?>" onchange="this.value=SBC2DBC(this.value)"></span></td>
<?php //サイト入力  ?>
<td class="dataTableContent" align="center"><?php
if ($ocertify->npermission >= 10) { //表示制限
    if ($products['products_status'] == '1') {
      echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=2&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else if ($products['products_status'] == '2') {
      echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
    } else {
      echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=2&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
    }
}
?></td>
<td class="dataTableContent" align='right'><?php 

  $last_modified_array = getdate(strtotime(tep_datetime_short($products['products_last_modified'])));
  $today_array = getdate();
  $last_modified = date('n/j H:i:s',strtotime(tep_datetime_short($products['products_last_modified'])));
  if ($last_modified_array["year"] == $today_array["year"] && $last_modified_array["mon"] == $today_array["mon"] && $last_modified_array["mday"] == $today_array["mday"]) {
    if ($last_modified_array["hours"] >= ($today_array["hours"]-2)) {
      //echo tep_image(DIR_WS_ICONS . 'signal_blue.gif', '更新正常');
      echo tep_image(DIR_WS_ICONS . 'signal_blue.gif', $last_modified);
    } elseif ($last_modified_array["hours"] >= ($today_array["hours"]-5)) {
      //echo tep_image(DIR_WS_ICONS . 'signal_yellow.gif', '更新注意');
      echo tep_image(DIR_WS_ICONS . 'signal_yellow.gif', $last_modified);
    } else {
      //echo tep_image(DIR_WS_ICONS . 'signal_red.gif', '更新警告');
      echo tep_image(DIR_WS_ICONS . 'signal_red.gif', $last_modified);
    }
  } else {
    //echo tep_image(DIR_WS_ICONS . 'signal_blink.gif', '更新異常');
    echo tep_image(DIR_WS_ICONS . 'signal_blink.gif', $last_modified);
  }
  echo '&nbsp;&nbsp;' . tep_image(DIR_WS_ICONS . 'battery_0.gif', '数量異常');
  ?>
  <input type="hidden" name="this_price[]" value="<?php echo (int)$special_price_check;?>" >
  <input type="hidden" name="proid[]"      value="<?php echo $products['products_id']; ?>" >
  <input type="hidden" name="pprice[]"     value="<?php echo $products['products_price']; ?>" >
  <input type="hidden" name="bflag[]"      value="<?php echo $products['products_bflag']; ?>" >
</td>
<td>
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
  <td align='right' colspan='<?php echo 10 + $count_dougyousya['cnt'];?>'>
    <input type="hidden" value="<?php echo $cPath; ?>"               name="cpath">
    <input type="hidden" value="<?php echo $cPath_yobi; ?>"          name="cpath_yobi">
    <input type="hidden" value="<?php echo $current_category_id; ?>" name="cID_list" >
  <?php if ($ocertify->npermission > 7) { ?>
    <input type='button' value='計算設定' name='b[]' onClick="cleat_set('set_bairitu.php')">
  <?php }?>
  &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='担当者登録' name='e[]' onClick="location.href='set_comment.php?cID=<?php echo $current_category_id;?>&cPath=<?php echo $_GET['cPath'];?>'">
  &nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='卸業者単価設定' name='d[]' onClick="list_display('<?php echo $cPath_yobi?$cPath_yobi:0;?>','<?php echo $current_category_id;?>','<?php echo $_GET['cPath'];?>')">
  &nbsp;&nbsp;&nbsp;&nbsp;<input type="button" name="x" value="一括更新" onClick="all_update()"></td>
</tr>
<!--dataTableRowSelected end-->
<tr>
  <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
  <td class="smallText" align="right" colspan="<?php echo 9 + $count_dougyousya['cnt'];?>"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?> </td>
</tr>
<tr>
  <td colspan="<?php echo 10 + $count_dougyousya['cnt'];?>">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText"><?php echo 'カテゴリー:' . '&nbsp;' . $categories_count .
         '<br>' . '商品数:' . '&nbsp;' . $products_query_numrows; ?></td>
        <td align="right" class="smallText"><?php
  if ($cPath) {
    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, $cPath_back . '&cID=' . $current_category_id) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>&nbsp;';
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
tep_display_google_results();
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
    <td class="dataTableContent" align='right'><b>更新日の日付:</b></td>
    <td class="dataTableContent"><?php echo date('Y/m/d H:i:s', strtotime($comment['last_modified']));?></td>
  </tr>
  <tr>
    <td class="dataTableContent" align='right'><b>担当者:</b></td>
    <td class="dataTableContent"><?php echo $comment['author'];?></td>
  </tr>
  <tr>
    <td class="dataTableContent" align='right'><b>コメント:</b></td>
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
