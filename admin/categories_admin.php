<?php
/*
  $Id$
*/


require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$cPath_yobi = cpathPart($_GET['cPath'],1);
$colspan = 7;
$currencies = new currencies();

//设置一个特别的函数 则日本人编辑   
function tep_get_specials_special_price($product_id) {
  $product_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "' and status");
  $product = tep_db_fetch_array($product_query);

  return $product['specials_new_products_price'];
}

//如果包括 insert update setflag 则加载reset_seo_cache.php @todo 好像没有用
if ( eregi("(insert|update|setflag)", $action) ) include_once('includes/reset_seo_cache.php');


//开始 处理请求{{
if (isset($_GET['action']) && $_GET['action']) {
  switch ($_GET['action']) 
    {
      //tep_db_prepare_input＝変数か文字列の判定 //更新するもの・・・特別価格と同業者の価格＋radioのチェック
    case 'all_update': //一括更新　　
      require('includes/set/all_update.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $HTTP_GET_VARS['cPath'] . '&pID=' .$products_id));
      break;
    case 'toggle':
      require('includes/set/toggle.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath=' . $HTTP_GET_VARS['cPath']));
      break;
    case 'setflag':
      require('includes/set/setflag.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $_GET['cPath']));
      break;
    case 'simple_update': // 価格と数量の簡易アップデート
      require('includes/set/simple_update.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $_GET['cPath'] . '&pID=' . $products_id));
      break;
    case 'insert_category':
    case 'update_category':
      require('includes/set/update_category.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $categories_id));
      break;
    case 'delete_product_description':
      require('includes/set/delete_product_description.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID='. (int)$_GET['pID']));
      break;
    case 'delete_category_description':
      require('includes/set/delete_category_description.php');;
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID='. (int)$_GET['cID']));
      break;
    case 'delete_category_confirm':
      require('includes/set/delete_category_confirm.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath));
      break;
    case 'delete_product_confirm':
      require('includes/set/delete_product_confirm.php');

      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath));
      break;
    case 'move_category_confirm':
      require('includes/set/move_category_confirm.php');

      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $new_parent_id . '&cID=' . $categories_id));
      break;
    case 'move_product_confirm':
      require('includes/set/move_product_confirm.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $new_parent_id . '&pID=' . $products_id));
      break;

    case 'insert_product':
    case 'update_product':
      require('includes/set/update_product.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $products_id));
      break;
    case 'copy_to_confirm':
      require('includes/set/copy_to_confirm.php');
      tep_redirect(tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $categories_id . '&pID=' . $products_id));
      break;
    }
}

//switch end

if (is_dir(tep_get_upload_root())) {
  if (!is_writeable(tep_get_upload_root())) $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_NOT_WRITEABLE, 'error');
} else {
  $messageStack->add(ERROR_CATALOG_IMAGE_DIRECTORY_DOES_NOT_EXIST, 'error');
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <title><?php echo TITLE; ?></title>
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
  <script type="text/javascript" src="includes/general.js"></script>
  <script type="text/javascript" src="includes/javascript/jquery.js"></script>
  <script type="text/javascript" src="includes/set/c_admin.js"></script>
  <script type="text/javascript">
$(document).ready(function(){
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
  <td width="<?php echo BOX_WIDTH; ?>" valign="top">
  <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
  <!-- left_navigation //-->
  <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  <!-- left_navigation_eof //-->
  </table>
  </td>
  <!-- body_text //-->
  <td width="100%" valign="top">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <?php //table start 
  ?>
  <tr>
  <td>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
  <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
  <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
  <td class="smallText" align="right"><?php echo tep_draw_form('search', FILENAME_CATEGORIES_ADMIN, '', 'get') . "\n"; ?> <?php echo HEADING_TITLE_SEARCH . ' ' . tep_draw_input_field('search', isset($_GET['search'])?$_GET['search']:'') . "\n"; ?>
  </form>
  </td>
  <td class="smallText" align="right"><?php echo tep_draw_form('goto', FILENAME_CATEGORIES_ADMIN, '', 'get') . "\n"; ?> <?php echo HEADING_TITLE_GOTO . ' ' . tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"') . "\n"; ?>
  </form></td>
  </tr>
  </table>
  </td>
  </tr>
  <tr>
  <!--main-->
  <td>
  <form name="myForm1" action="categories_admin.php?<?php echo "cPath=".$cPath."&pID=".$products['products_id']."&action=all_update"; ?>" method="POST" onSubmit="return false">
  <input type="hidden" name="flg_up" value="" />
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <!--dataTableHeadingRow-->
  <tr class="dataTableHeadingRow">
  <td class="dataTableHeadingContent" height="30"><?php echo TABLE_HEADING_CATEGORIES_PRODUCTS; ?></td>
<?php if ($ocertify->npermission == 15 or $ocertify->npermission == 10) {?>
<?php if (!isset($_GET['cPath']) or !$_GET['cPath']){?>
  <td class="dataTableHeadingContent" align="right">表示</td>
     <?php }?>
   <?php }?>
  <td class="dataTableHeadingContent" align="center">個数/架空</td>
  <td class="dataTableHeadingContent" align="center">数量</td>
  <td class="dataTableHeadingContent" align="center">
  <a href="#" onClick="history('history.php',' <?php echo $cPath_yobi;?>','<?php echo $current_category_id; ?>','oroshi')">価格/業者</a>
  </td>
  <?php  
  //读取当前的计算公式  
  $res=tep_db_query("select bairitu from set_auto_calc where parent_id='".$cPath_yobi."'"); 
$col=tep_db_fetch_array($res);
?>
<td class="dataTableHeadingContent" align="center"><?php echo $col['bairitu']?>倍</td>
  <?php
  if ($cPath_yobi){
    $res=tep_db_query("select count(*) as cnt from set_dougyousya_names sdn  ,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id = ".$cPath_yobi." ");
    $count_dougyousya=tep_db_fetch_array($res);
    if($count_dougyousya['cnt'] > 0) {
      $res=tep_db_query("select * from set_dougyousya_names sdn ,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id = ".$cPath_yobi. " ORDER BY sdc.dougyousya_id ASC");
      while($col_dougyousya=tep_db_fetch_array($res)){
        $i++;
        echo "<td class='dataTableHeadingContent' align='center'><a href='#' onClick=history('history.php',".$cPath_yobi.",".$current_category_id.",'dougyousya')>".$col_dougyousya['dougyousya_name']."</a>";
        echo "<input type='hidden' name='d_id[]' value='".$col_dougyousya['dougyousya_id']."'>";
        echo "</td>";
      }
    }
  
  else {
    echo "<td class='dataTableHeadingContent' align='center'>同業者未設定</td>";
  }
  }
?>
<td class="dataTableHeadingContent" align="right">価格</td>
  <td class="dataTableHeadingContent" align="right">価格設定</td>
  <td class="dataTableHeadingContent" align="center"><?php echo  TABLE_HEADING_STATUS; ?></td>
  <td class="dataTableHeadingContetn" align="right"></td>
  <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?></td>
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

  if ( (isset($cInfo) && is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) ) {
    echo ' <!--dataTableRowSelected--> <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ' . ' onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES_ADMIN, tep_get_path($categories['categories_id'])) . '\'">' . "\n";
  } else {
    echo '              <tr class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'" onclick="document.location.href=\'' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . ($HTTP_GET_VARS['page'] ? ('&page=' . $HTTP_GET_VARS['page']) : '' ) . '&cID=' . $categories['categories_id']) . '\'">' . "\n"; 
  }
  ?>
  <td class="dataTableContent">
     <?php 
     echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, tep_get_path($categories['categories_id'])) . '">' . tep_image(DIR_WS_ICONS . 'folder.gif', ICON_FOLDER) . '</a>&nbsp;<b>' . $categories['categories_name'] . '</b>'; ?>
     </td>
         <?php 
         if (!isset($_GET['cPath']) or !$_GET['cPath']) {
           echo '<td class="dataTableContent" align="right" colspan="';
           $cCount = isset($count_dougyousya_dougyousya['cnt'])?$count_dougyousya_dougyousya['cnt']:0;
           echo $colspan+$cCount;
           echo '">';
?>

             &nbsp;
           </td>
               <?php
               } else {
           echo '<td class="dataTableContent" align="right" colspan="';
           $cCount = isset($count_dougyousya_dougyousya['cnt'])?$count_dougyousya_dougyousya['cnt']:0;
           echo $colspan+$cCount+2;
           echo '">';
?>


             &nbsp;
           </td>
      <?php }?>

      <td class="dataTableContent" align="right">
         <?php
         if ($ocertify->npermission == 15 or $ocertify->npermission == 10) {
           ?>
           <?php if($categories['categories_status'] == '1'){
           ?>
           <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=0&cPath='.$HTTP_GET_VARS['cPath']);?>">
           <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', '');?>
           </a>
           <a href="<?php echo tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=toggle&cID='.$categories['categories_id'].'&status=2&cPath='.$HTTP_GET_VARS['cPath']);?>">
           <?php echo tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', '');?>
           </a> 
           <?php 
           echo tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', '');  
           } else if($categories['categories_status'] == '2'){
      //nothing but why
    }
} else {
           //nothing but why
         }

?>
</td>
<td class="dataTableContent" align="right">&nbsp;  </td>
<td class="dataTableContent" align="right">
  <?php

  if ( (isset($cInfo) && is_object($cInfo)) && ($categories['categories_id'] == $cInfo->categories_id) )     {
    echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
  } else { 
    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $categories['categories_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
    }
?>
  &nbsp;</td>
            </tr>
  <!--dataTableRowSelected end-->

<?php

}
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
               p2c.categories_id 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        where p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and pd.products_name like '%" . $_GET['search'] . "%' 
          and pd.site_id='0'
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
               p.products_status 
        from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c 
        where p.products_id = pd.products_id 
          and pd.language_id = '" . $languages_id . "' 
          and p.products_id = p2c.products_id 
          and p2c.categories_id = '" . $current_category_id . "' 
          and pd.site_id='0'
        order by pd.products_name";
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

  if ( (isset($pInfo) && is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) {
    echo ' <!--dataTableRowSelected--> <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" ' . '\'">' . "\n";
  } else {
    echo '              <tr class="' . $nowColor . '" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'' . $nowColor . '\'"> ' . "\n";
  }
  ?>
  <?php
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
     echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $products['products_id'] . '&action=new_product_preview&read=only') . '">' . tep_image(DIR_WS_ICONS . 'preview.gif', ICON_PREVIEW) . '</a>&nbsp;&nbsp;<a href="orders.php?keywords=' . urlencode($products['products_name']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_time.gif', '', 16, 16) . '</a>&nbsp;&nbsp;' . $products['products_name']; 
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
  <td class="dataTableContent" align='center'>
     <!--    <input type="text" size="5" value="<?php  echo $imaginary; ?>" name='imaginary[<?php echo $products_count-1;?>]' /> -->
     <span align='center' > <?php echo $imaginary;?></span>
  </td>
      <?php
     ////数量 
     ?>
      <td class="dataTableContent" align='center'>
     <!--        <span name='zaiko[]' align='center'  id='zaiko_<?php echo $products_count;?>' onKeyDown="ctrl_keydown(event,'zaiko','<?php  echo $products_count;?>'")> -->
          <input type='text' name='quantity[<?php echo $target_cnt;?>]' value='<?php echo $products['products_quantity'];?>' size="5"><?php //echo $products['products_quantity'];?>
<!--        </span> -->
    <?php
    /*
      if (empty($products['products_quantity'])) {//数量・・・在庫がない場合
        echo "<span align='center' name='zaiko[]' id='zaiko_".$products_count."' onKeyDown=ctrl_keydown(event,'zaiko',".$products_count.")>".'在庫切れ</span>';

      } else {//在庫がある場合
        echo "<span name='zaiko[]' align = 'center'  id='zaiko_".$products_count."' onKeyDown=ctrl_keydown(event,'zaiko',".$products_count.")><input type='text' name='quantity[".$products['products_id']."]' value='".$products['products_quantity']."'></span>";
      }*/ ?></td>
  <td align='center' class="dataTableContent" >
              <span  name="TRADER_INPUT[]"  id="TRADER_<?php echo $products['products_id'].'"'; ?> 
              onKeyDown="ctrl_keydown(event,'TRADER_INPUT','<?php echo $products_count; ?>' )" onBlur="event_onblur('<?php echo $products_count; ?>')" >
                <?php echo $kakaku_treder;?>
                </span>
                </td>
                <?php //価格業者  ?>
                <td align='center' class="dataTableContent" >
                <span name="INCREASE_INPUT" onKeyDown="ctrl_keydown(event,'INCREASE_INPUT','<?php echo $products_count; ?>')" >
                <?php echo floor($kakaku_treder*$col['bairitu']);?>
                </span></td>
                <?php //価格倍率  ?>
                <?php
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
                for($i=0;$i<$count['cnt'];$i++) {
                  echo "
                    <td class='dataTableContent' >
                    <input type='radio' value='".($i+1)."' name='chk[".$target_cnt."]' onClick='chek_radio(".$target_cnt.")'".(($i+1) == $dougyousya?' checked':'').">
                    <span name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' >".get_dougyousya_history($products['products_id'], $i+1)."</span>
                    <!--<input type='text' size='7' value='".get_dougyousya_history($products['products_id'], $i+1)."' name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' onBlur='event_onblur(".$products_count.")' onkeydown=ctrl_keydown(event,'TARGET_INPUT',".$products_count.",".$i.",".$count['cnt'].") readonly>-->
                    </td>";//価格同業者
                }
                /*
                for($i=0;$i<$count['cnt'];$i++) {
                  if($i==0){ //初期チェック$radio_col['products_id']== $products['products_id'] && $radio_col['radio_chk']==1
                    echo "
                      <td class='dataTableContent' >
                      <input type='radio' value='".$i."' name='chk_".$target_cnt."[]' onClick='chek_radio(".$target_cnt.")' checked>
                      <input type='text' size='7' name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' onBlur='event_onblur(".$products_count.")' onkeydown=ctrl_keydown(event,'TARGET_INPUT',".$products_count.",".$i.",".$count['cnt'].") >
                      <input type='hidden' name='radiochk[]' id='radiochk".$target_cnt."_".$i."' value='".$i."' >
                      </td>";//価格同業者
                  }else{
                    echo "
                      <td class='dataTableContent' >
                      <input type='radio' value='".$i."' name='chk_".$target_cnt."[]' onClick='chek_radio(".$target_cnt.")' >
                      <input type='text' name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' onBlur='event_onblur(".$products_count.")' onkeydown=ctrl_keydown(event,'TARGET_INPUT',".$products_count.",".$i.",".$count['cnt'].") >
                      <input type='hidden' name='radiochk[]' id='radiochk".$target_cnt."_".$i."' value='".$i."' >
                      </td>";//価格同業者  
                  }
                }*/
              }else{
                echo "
                  <td class='dataTableContent' >
                  <input type='radio' value='0' name='chk[".$target_cnt."]' onClick='chek_radio(".$target_cnt.")' checked>
                  <input type='text' size='7' name='TARGET_INPUT[]' id='target_".$target_cnt."_".$i."' onBlur='event_onblur(".$products_count.")' onkeydown=ctrl_keydown(event,'TARGET_INPUT',".$products_count.",".$i.",".$count['cnt'].") >
                  </td>";//価格同業者
                //echo "<td class='dataTableContent' ><span   name='TARGET_INPUT[]' id='target_".$target_cnt."_0' onBlur='event_onblur(".$products_count.")' onkeydown=ctrl_keydown(event,'TARGET_INPUT',".$products_count.",'0','0') ></span></td>";//価格同業者 
                //echo "<input type='hidden' name='radiochk[]' id='radiochk".$target_cnt."_".$i."' value='1' >";
              }
              ?>
              <td class="dataTableContent" align="right"><?php
                 $special_price_check = tep_get_specials_special_price($products['products_id']);
              if (!empty($special_price_check)) {//特価がある場合
                echo '<s>' . $currencies->format($products['products_price']) . '</s> <span class="specialPrice">' . $currencies->format($special_price_check) . '</span>';
              } else {//特価がない場合
                echo $currencies->format($products['products_price']);
              } ?></td>
              <td class="dataTableContent" align="right"><input type="text" size='6' value="" name="price[]" id="<?php echo "price_input_".$products_count; ?>" onKeyDown="ctrl_keydown('price_input_','<?php echo $products_count; ?>','<?php echo $count['cnt'];?>')" ></td>
                      <?php //サイト入力  ?>
                      <td class="dataTableContent" align="center"><?php
                      if ($ocertify->npermission >= 10) { //表示制限
                        if ($statusable) {
                          if ($products['products_status'] == '1') {
                            echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=2&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                          } else if ($products['products_status'] == '2') {
                            echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=0&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
                          } else {
                            echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=1&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'action=setflag&flag=2&pID=' . $products['products_id'] . '&cPath=' . $cPath) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_blue_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                          }
                        } else {
                          if ($products['products_status'] == '1') {
                            echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10);
                          } else if ($products['products_status'] == '2') {
                            echo tep_image(DIR_WS_IMAGES . 'icon_status_blue.gif', IMAGE_ICON_STATUS_GREEN, 10, 10);
                          } else {
                            echo tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
                          }
                        }
                      } else {
                        // 価格更新警告
                        /*$last_modified_array = getdate(strtotime(tep_datetime_short($products['products_last_modified'])));
                          $today_array = getdate();
                          if ($last_modified_array["year"] == $today_array["year"] && $last_modified_array["mon"] == $today_array["mon"] && $last_modified_array["mday"] == $today_array["mday"]) {
                          if ($last_modified_array["hours"] >= ($today_array["hours"]-2)) {
                          echo tep_image(DIR_WS_ICONS . 'signal_blue.gif', '更新正常');
                          } elseif ($last_modified_array["hours"] >= ($today_array["hours"]-5)) {
                          echo tep_image(DIR_WS_ICONS . 'signal_yellow.gif', '更新注意');
                          } else {
                          echo tep_image(DIR_WS_ICONS . 'signal_red.gif', '更新警告');
                          }
                          } else {
                          echo tep_image(DIR_WS_ICONS . 'signal_blink.gif', '更新異常');
                          }
  
                          echo '&nbsp;&nbsp;' . tep_image(DIR_WS_ICONS . 'battery_0.gif', '数量異常');
                        */
                      }
              ?></td>
              <td><?php 
                 $last_modified_array = getdate(strtotime(tep_datetime_short($products['products_last_modified'])));
              $today_array = getdate();
              if ($last_modified_array["year"] == $today_array["year"] && $last_modified_array["mon"] == $today_array["mon"] && $last_modified_array["mday"] == $today_array["mday"]) {
                if ($last_modified_array["hours"] >= ($today_array["hours"]-2)) {
                  echo tep_image(DIR_WS_ICONS . 'signal_blue.gif', '更新正常');
                } elseif ($last_modified_array["hours"] >= ($today_array["hours"]-5)) {
                  echo tep_image(DIR_WS_ICONS . 'signal_yellow.gif', '更新注意');
                } else {
                  echo tep_image(DIR_WS_ICONS . 'signal_red.gif', '更新警告');
                }
              } else {
                echo tep_image(DIR_WS_ICONS . 'signal_blink.gif', '更新異常');
              }
  
              echo '&nbsp;&nbsp;' . tep_image(DIR_WS_ICONS . 'battery_0.gif', '数量異常');
              ?>
              <input type="hidden" name="this_price[]" value="<?php echo $currencies->format($special_price_check);?>" >
                 <input type="hidden" name="proid[]" value="<?php echo $products['products_id']; ?>" >
                 <input type="hidden" name="pprice[]" value="<?php echo $products['products_price']; ?>" >
                 </td>
                 <td class="dataTableContent" align="right">
                 <?php 
                 if ( (is_object($pInfo)) && ($products['products_id'] == $pInfo->products_id) ) { 
                   echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', ''); 
                 } else { 
                   echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $products['products_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; 
                 } 
              ?>
              &nbsp;</td>
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
  $res_list=tep_db_query("select parent_id from categories where categories_id =".tep_db_prepare_input($cPath));
  $col_list=tep_db_fetch_array($res_list);
  $cPath_yobi=$col_list['parent_id'];
}

//$cPath_back = isset($cPath_back) && $cPath_back ? 'cPath=' . $cPath_back : '';

?>
<!--dataTableRowSelected-->
<tr>
<input type="hidden" value="<?php echo $cPath; ?>" name="cpath">
  <input type="hidden" value="<?php echo $cPath_yobi; ?>" name="cpath_yobi">
  <input type="hidden" value="<?php echo $current_category_id; ?>" name="cID_list" >
  <?php //予備　?>
  <?php
          
  echo "<td align='right' colspan='9'><input type='button' value='計算設定' name='b[]' onClick=cleat_set('set_bairitu.php','300','450')></td>";//追加
?>
<td align="right" ><?php echo "<input type='button' value='リスト表示' name='d[]' onClick=list_display(".$cPath_yobi.",".$current_category_id.")>";//追加?></td>
<td align="right" ><input type="button" name="x" value="一括更新" onClick="all_update()"></td>
  </tr>
  <!--dataTableRowSelected end-->
  <tr>
  <td class="smallText" valign="top"><?php echo $products_split->display_count($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_CUSTOMERS); ?></td>
  <td class="smallText" align="right" colspan="10"><?php echo $products_split->display_links($products_query_numrows, MAX_DISPLAY_PRODUCTS_ADMIN, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'info', 'x', 'y', 'pID'))); ?> </td>
  </tr>
  <tr>
  <td colspan="11">
  <table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
  <td class="smallText"><?php echo 'カテゴリー:' . '&nbsp;' . $categories_count .
  '<br>' . '商品数:' . '&nbsp;' . $products_query_numrows; ?></td>
  <td align="right" class="smallText">
  <?php
  if ($cPath) {
    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, $cPath_back . '&cID=' . $current_category_id) . '">' . tep_image_button('button_back.gif', IMAGE_BACK) . '</a>&nbsp;';
  }
?>
&nbsp;</td>
</tr>
</table>
</td>
</tr>
</table>
</form>
</td>
</tr>
</table>
</td>
</tr>
</table>
<?php
$heading = array();
$contents = array();
switch (isset($_GET['action'])?$_GET['action']:null) {
case 'new_category':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_CATEGORY . '</b>');

  $contents = array('form' => tep_draw_form('newcategory', FILENAME_CATEGORIES_ADMIN, 'action=insert_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"'));
  $contents[] = array('text' => TEXT_NEW_CATEGORY_INTRO);

  $category_inputs_string = '';
  $languages = tep_get_languages();
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']').'<br>'."\n".
      '<br>トップページカテゴリバナー画像:<br>'.tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;'.tep_draw_file_field('categories_image2').'<br>'."\n".
      '<br>カテゴリタイトル画像:<br>'.tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;'.tep_draw_file_field('categories_image3').'<br><font color="red">画像がない場合はテキスト表示されます</font><br>'."\n".
      '<br>METAタグ<br>（この説明文はトップページのカテゴリバナーの下に表示される文章としても使用されます。2行にするにはカンマ「,」区切りで文章を記述してください。)<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_meta_text[' . $languages[$i]['id'] . ']','',30,3).

      '<br>SEOネーム:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('seo_name[' . $languages[$i]['id'] . ']', '').'<br>'."\n".
      '<br>カテゴリHeaderのテキスト:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_header_text[' . $languages[$i]['id'] . ']','soft',30,3,'','categories_header_text').
      '<br>カテゴリFooterのテキスト:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_footer_text[' . $languages[$i]['id'] . ']','soft',30,3,'','categories_footer_text').
      '<br>テキストのインフォメーション:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('text_information[' . $languages[$i]['id'] . ']','soft',30,3,'','text_information').
      '<br>metaのキーワード:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('meta_keywords[' . $languages[$i]['id'] . ']','soft',30,3,'','meta_keywords').
      '<br>metaの説明:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('meta_description[' . $languages[$i]['id'] . ']','soft',30,3,'','meta_description').
      "\n";
  }

  $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_NAME . $category_inputs_string);
  $contents[] = array('text' => '<br>' . TEXT_CATEGORIES_IMAGE . '<br>' . tep_draw_file_field('categories_image'));
  $contents[] = array('text' => '<br>' . TEXT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', '', 'size="2"'));
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
  break;
case 'edit_category':
  $site_id = isset($_GET['site_id'])?$_GET['site_id']:0;
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_CATEGORY . '</b>');

  $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES_ADMIN, 'action=update_category&cPath=' . $cPath, 'post', 'enctype="multipart/form-data"') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
  $contents[] = array('text' => TEXT_EDIT_INTRO);
  $contents[] = array('text' => tep_draw_hidden_field('site_id', $site_id));
 
  $category_inputs_string = '';
  $languages = tep_get_languages();
  for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
    $category_inputs_string .= '<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', tep_get_category_name($cInfo->categories_id, $languages[$i]['id'], $site_id, true), ($site_id ? 'class="readonly" readonly' : '')).'<br>'."\n".
      '<br>'.tep_image(tep_get_web_upload_dir($site_id) .'categories/'. $cInfo->categories_image2, $cInfo->categories_name).'<br>' . tep_get_upload_dir($site_id) . 'categories/<br><b>' . $cInfo->categories_image2 . '</b><br><br>トップページカテゴリバナー画像<br>'.tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;'.tep_draw_file_field('categories_image2').'<br>'."\n".
      '<br>'.tep_image(tep_get_web_upload_dir($site_id) . 'categories/'. $cInfo->categories_image3, $cInfo->categories_name).'<br>' . tep_get_upload_dir($site_id). 'categories/<br><b>' . $cInfo->categories_image3 . '</b><br><br>カテゴリタイトル画像<br>'.tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;'.tep_draw_file_field('categories_image3').'<br>'."\n".
      '<br>METAタグ（この説明文はトップページのカテゴリバナーの下に表示される文章としても使用されます。2行にするにはカンマ「,」区切りで文章を記述してください。)<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_meta_text[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_category_meta_text($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).

      '<br>SEOネーム:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' . tep_draw_input_field('seo_name[' . $languages[$i]['id'] . ']', tep_get_seo_name($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).'<br>'."\n".
      '<br>カテゴリHeaderのテキスト:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_header_text[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_categories_header_text($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
      '<br>カテゴリFooterのテキスト:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('categories_footer_text[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_categories_footer_text($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
      '<br>テキストのインフォメーション:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('text_information[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_text_information($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
      '<br>metaのキーワード:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('meta_keywords[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_meta_keywords($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
      '<br>metaの説明:<br>' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']) . '&nbsp;' .tep_draw_textarea_field('meta_description[' . $languages[$i]['id'] . ']','soft',30,3,tep_get_meta_description($cInfo->categories_id, $languages[$i]['id'], $site_id, true)).
      '';
  }

  $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_NAME . $category_inputs_string);
  $contents[] = array('text' => '<br>' . tep_image(tep_get_web_upload_dir($site_id).'categories/'. $cInfo->categories_image, $cInfo->categories_name) . '<br>' . tep_get_upload_dir($site_id). 'categories/<br><b>' . $cInfo->categories_image . '</b>');
  $contents[] = array('text' => '<br>' . TEXT_EDIT_CATEGORIES_IMAGE . '<br>' . tep_draw_file_field('categories_image'));
  $contents[] = array('text' => '<br>' . TEXT_EDIT_SORT_ORDER . '<br>' . tep_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'));
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_save.gif', IMAGE_SAVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');

  break;
case 'delete_category':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_CATEGORY . '</b>');

  $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES_ADMIN, 'action=delete_category_confirm&cPath=' . $cPath) . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
  $contents[] = array('text' => TEXT_DELETE_CATEGORY_INTRO);
  $contents[] = array('text' => '<br><b>' . $cInfo->categories_name . '</b>');
  if ($cInfo->childs_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_CHILDS, $cInfo->childs_count));
  if ($cInfo->products_count > 0) $contents[] = array('text' => '<br>' . sprintf(TEXT_DELETE_WARNING_PRODUCTS, $cInfo->products_count));
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
  break;
case 'move_category':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_CATEGORY . '</b>');

  $contents = array('form' => tep_draw_form('categories', FILENAME_CATEGORIES_ADMIN, 'action=move_category_confirm') . tep_draw_hidden_field('categories_id', $cInfo->categories_id));
  $contents[] = array('text' => sprintf(TEXT_MOVE_CATEGORIES_INTRO, $cInfo->categories_name));
  $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $cInfo->categories_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree('0', '', $cInfo->categories_id), $current_category_id));
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
  break;
case 'delete_product':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_PRODUCT . '</b>');

  $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES_ADMIN, 'action=delete_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
  $contents[] = array('text' => TEXT_DELETE_PRODUCT_INTRO);
  $contents[] = array('text' => '<br><b>' . $pInfo->products_name . '</b>');

  $product_categories_string = '';
  $product_categories = tep_generate_category_path($pInfo->products_id, 'product');
  for ($i = 0, $n = sizeof($product_categories); $i < $n; $i++) {
    $category_path = '';
    for ($j = 0, $k = sizeof($product_categories[$i]); $j < $k; $j++) {
      $category_path .= $product_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
    }
    $category_path = substr($category_path, 0, -16);
    $product_categories_string .= tep_draw_checkbox_field('product_categories[]', $product_categories[$i][sizeof($product_categories[$i])-1]['id'], true) . '&nbsp;' . $category_path . '<br>';
  }
  $product_categories_string = substr($product_categories_string, 0, -4);

  $contents[] = array('text' => '<br>' . $product_categories_string);
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
  break;
case 'move_product':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_MOVE_PRODUCT . '</b>');

  $contents = array('form' => tep_draw_form('products', FILENAME_CATEGORIES_ADMIN, 'action=move_product_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
  $contents[] = array('text' => sprintf(TEXT_MOVE_PRODUCTS_INTRO, $pInfo->products_name));
  $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
  $contents[] = array('text' => '<br>' . sprintf(TEXT_MOVE, $pInfo->products_name) . '<br>' . tep_draw_pull_down_menu('move_to_category_id', tep_get_category_tree(), $current_category_id));
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_move.gif', IMAGE_MOVE) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
  break;
case 'copy_to':
  $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_COPY_TO . '</b>');

  $contents = array('form' => tep_draw_form('copy_to', FILENAME_CATEGORIES_ADMIN, 'action=copy_to_confirm&cPath=' . $cPath) . tep_draw_hidden_field('products_id', $pInfo->products_id));
  $contents[] = array('text' => TEXT_INFO_COPY_TO_INTRO);
  $contents[] = array('text' => '<br>' . TEXT_INFO_CURRENT_CATEGORIES . '<br><b>' . tep_output_generated_category_path($pInfo->products_id, 'product') . '</b>');
  $contents[] = array('text' => '<br>' . TEXT_CATEGORIES . '<br>' . tep_draw_pull_down_menu('categories_id', tep_get_category_tree(), $current_category_id));
  $contents[] = array('text' => '<br>' . TEXT_HOW_TO_COPY . '<br>' . tep_draw_radio_field('copy_as', 'link', true) . ' ' . TEXT_COPY_AS_LINK . '<br>' . tep_draw_radio_field('copy_as', 'duplicate') . ' ' . TEXT_COPY_AS_DUPLICATE);
  $contents[] = array('align' => 'center', 'text' => '<br>' . tep_image_submit('button_copy.gif', IMAGE_COPY) . ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id) . '">' . tep_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
  break;
default:
  if ($rows > 0) {
    if (isset($cInfo) && is_object($cInfo)) { // category info box contents
      $heading[] = array('text' => '<b>' . $cInfo->categories_name . '</b>');

      if ($ocertify->npermission >= 10) { //表示制限
        $contents[] = array(
                            'align' => 'left', 
                            'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>'  
                            . ($ocertify->npermission == 15 ? ( '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=delete_category') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'):'')
                            . '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=move_category') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a>');
        foreach(tep_get_sites() as $site){
          $contents[] = array('text' => '<b>' . $site['romaji'] . '</b>');
          $contents[] = array(
                              'align' => 'left', 
                              'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=edit_category&site_id='.$site['id']) . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>'.
                              (tep_categories_description_exist($cInfo->categories_id, $site['id'], $languages_id) 
                               ? (' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&cID=' . $cInfo->categories_id . '&action=delete_category_description&site_id='.$site['id']) . '">'.tep_image_button('button_deffect.gif', IMAGE_DELETE).'</a>')
                               :''
                               ));
        }
      }

      //print_r($cInfo);
      $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($cInfo->date_added));
      if (tep_not_null($cInfo->last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_date_short($cInfo->last_modified));
      $contents[] = array('text' => '<br>' . tep_info_image('categories/'.$cInfo->categories_image, $cInfo->categories_name, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT, 0) . '<br>' . $cInfo->categories_image);
      $contents[] = array('text' => '<br>' . TEXT_SUBCATEGORIES . ' ' . $cInfo->childs_count . '<br>' . TEXT_PRODUCTS . ' ' . $cInfo->products_count);
    } elseif (isset($pInfo) && is_object($pInfo)) { // product info box contents
      $heading[] = array('text' => '<b>' . tep_get_products_name($pInfo->products_id, $languages_id) . '</b>');

      if ($ocertify->npermission >= 10) { //表示制限
        $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' 
                            . ($ocertify->npermission == 15 ? (' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product') . '">' . tep_image_button('button_delete.gif', IMAGE_DELETE) . '</a>'):'')
                            . ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=move_product') . '">' . tep_image_button('button_move.gif', IMAGE_MOVE) . '</a> <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=copy_to') . '">' . tep_image_button('button_copy_to.gif', IMAGE_COPY_TO) . '</a>');
        foreach(tep_get_sites() as $site){
          $contents[] = array('text' => '<b>' . $site['romaji'] . '</b>');
          $contents[] = array('align' => 'left', 'text' => '<a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=new_product') . '&site_id='. $site['id'] .'">' . tep_image_button('button_edit.gif', IMAGE_EDIT) . '</a>' . (
                                                                                                                                                                                                                                                                                            tep_products_description_exist($pInfo->products_id, $site['id'], $languages_id) ? ' <a href="' . tep_href_link(FILENAME_CATEGORIES_ADMIN, 'cPath=' . $cPath . '&pID=' . $pInfo->products_id . '&action=delete_product_description&site_id='.$site['id']) . '">' . tep_image_button('button_deffect.gif', IMAGE_DELETE) . '</a>'
                                                                                                                                                                                                                                                                                            : ''
                                                                                                                                                                                                                                                                                            ) );
        }
      }

      $contents[] = array('text' => '<br>' . TEXT_DATE_ADDED . ' ' . tep_date_short($pInfo->products_date_added));
      if (tep_not_null($pInfo->products_last_modified)) $contents[] = array('text' => TEXT_LAST_MODIFIED . ' ' . tep_datetime_short($pInfo->products_last_modified));
      if (date('Y-m-d') < $pInfo->products_date_available) $contents[] = array('text' => TEXT_DATE_AVAILABLE . ' ' . tep_date_short($pInfo->products_date_available));
      $contents[] = array('text' => '<br>' . tep_info_image('products/'.$pInfo->products_image, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 0) . '<br>' . $pInfo->products_image);
      if($pInfo->products_image2) {
        $contents[] = array('text' => '<br>' . tep_info_image('products/'.$pInfo->products_image2, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image2, 0);
      }
      if($pInfo->products_image3) {
        $contents[] = array('text' => '<br>' . tep_info_image('products/'.$pInfo->products_image3, $pInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '<br>' . $pInfo->products_image3, 0);
      }
      //特価がある場合の処理
      $special_price_check = tep_get_products_special_price($pInfo->products_id);
      if (!empty($special_price_check)) {
        $contents[] = array('text' => '<br><b>' . TEXT_PRODUCTS_PRICE_INFO . ' <s>' . $currencies->format($pInfo->products_price) . '</s> <span class="specialPrice">' . $currencies->format($special_price_check) . '</span></b>');
      } else {
        $contents[] = array('text' => '<br><b>' .TEXT_PRODUCTS_PRICE_INFO.' ' . $currencies->format($pInfo->products_price) . '</b>');
      }
      $contents[] = array('text' => '<br><b>' .TEXT_PRODUCTS_QUANTITY_INFO.' ' . $pInfo->products_quantity . '個</b>');
      $contents[] = array('text' => '<br>' . TEXT_PRODUCTS_AVERAGE_RATING . ' ' . number_format($pInfo->average_rating, 2) . '%');
    }
  } else { // create category/product info
    $heading[] = array('text' => '<b>' . EMPTY_CATEGORY . '</b>');

    $contents[] = array('text' => sprintf(TEXT_NO_CHILD_CATEGORIES_OR_PRODUCTS, isset($parent_categories_name)?$parent_categories_name:''));
  }
  break;
}

?>

<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
