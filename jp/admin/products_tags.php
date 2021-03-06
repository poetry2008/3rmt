<?php
  require('includes/application_top.php');
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
  if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
/*----------------------------
 case 'load_products_to_tags' 加载产品标签
 case 'save' 更新产品标签
 ---------------------------*/
      case 'load_products_to_tags':
        $query = tep_db_query("select * from products_to_tags where tags_id='".(int)$_GET['tags_id']."'");
        $arr = array();
        while ($p2t = tep_db_fetch_array($query)) {
          $arr[] = $p2t['products_id'];
        }
        echo json_encode($arr);
        exit;
        break;
      case 'save':
        if($_POST['categories_id']){
         $tags_query = tep_db_query("select * from tags where tags_id");
         $tags_array = tep_db_fetch_array($tags_query);
         tep_db_query("UPDATE `tags` SET user_update = '".$_SESSION['user_name']."', date_update = '".date('Y-m-d H:i:s',time())."'  WHERE  `tags_id` = '".$tags_array['tags_id']."'");  
        };
        if ($_POST['tags_id']) {
          foreach($_POST['tags_id'] as $tid) {
          tep_db_query("delete from products_to_tags where tags_id='".$tid."'");
            if ($_POST['products_id']) {
               foreach($_POST['products_id'] as $pid) {
                tep_db_perform("products_to_tags", array('products_id' => (int)$pid, 'tags_id' => (int)$tid));
              }
            }
          }
        if($tid){
         tep_db_query("UPDATE `tags` SET user_update = '".$_SESSION['user_name']."', date_update = '".date('Y-m-d H:i:s',time())."'  WHERE  `tags_id` = '".$tid."'");  
         }
        }
        tep_redirect(tep_href_link('products_tags.php'));
        break;
    }
  }
/*-----------------------------------
 功能：产品复选框
 参数：$cid(string) 类别ID值
 返回值：无
 ----------------------------------*/ 
  function products_box($cid){
      $products_query = tep_db_query("select * from products_to_categories p2c,products_description pd where p2c.products_id=pd.products_id and pd.site_id=0 and p2c.categories_id='".$cid."'");
      if (tep_db_num_rows($products_query)) {
        echo '<ul id="p_'.$categories['categories_id'].'" class="products_box">'."\n";
        while($products = tep_db_fetch_array($products_query)) {
          echo '<li>'."\n";
          echo '<input type="checkbox" class="products_checkbox" name="products_id[]" id="products_'.$products['products_id'].'" value="'.$products['products_id'].'">'.$products['products_name']."\n";
          echo '</li>'."\n";
        }
        echo '</ul>'."\n";
      }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo PRODUCTS_TO_TAGS_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="includes/javascript/jquery.js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script>
<?php //加载产品标签 ?>
function load_products_to_tags(){
  count = checked_count();
  if (count == 1){
    tid = get_tid();
     $.ajax({
      dataType: 'json',
      url: 'products_tags.php?action=load_products_to_tags&tags_id='+tid,
      success: function(pid) {
          $('.products_checkbox').removeAttr('checked');
          for(i in pid){
            $('#products_'+pid[i]).attr('checked','checked');
          }
      }
    });
  } else if (count == 0) {
    $('.products_checkbox').removeAttr('checked');
  }
}
<?php //检查数量?>
function checked_count(){
  var i = 0;
  $('.all_tags').each(function(){
    if(this.checked == true) i++;
  });
  return i;
}
<?php //获取标签ID ?>
function get_tid(){
  tid = null;
  count = checked_count();
  if (count == 1){
    $('.all_tags').each(function(){
      if(this.checked == true) {
        tid = $(this).val();
      }
    });
  }
  return tid;
}
<?php //类别ID开关 ?>
function switch_categories(cid){
  if ($('#d_'+cid).css('display') == 'block') {
    $('#d_'+cid).css('display', 'none');
  } else {
    $('#d_'+cid).css('display', 'block');
  }
}
<?php //检查全部 ?>
function check_all(cid){
  if ($('#categories_'+cid).attr('checked')) {
    $('#d_'+cid+' input[type=checkbox]').attr('checked','checked');
  } else {
    $('#d_'+cid+' input[type=checkbox]').removeAttr('checked');
  }
}
</script>
<style>
.categories_box{
  display:none;
}
</style>
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
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><div class="box_warp">
<?php echo $notes;?>
<div class="compatible">
<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo PRODUCTS_TO_TAGS_TITLE;?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
  <?php echo tep_draw_form('products_to_tags','products_tags.php', 'action=save', 'post');?>
  <input type="submit" value="<?php echo IMAGE_SAVE;?>">
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top" width="50%">
<?php
  $tags_query = tep_db_query("select * from tags order by tags_name");
  if (tep_db_num_rows($tags_query)) {
    echo "<ul>\n";
    while($tag = tep_db_fetch_array($tags_query)){
      echo '<li><input onchange="load_products_to_tags()" type="checkbox"  class="all_tags" name="tags_id['.$tag['tags_id'].']" id="tags_id" value="'.$tag['tags_id'].'"'.($_GET['tags_id'] == $tag['tags_id']?' checked="checked"':'').'>'.$tag['tags_name'].'</li>'."\n";
    }
    echo "</ul>\n";
  } else {
    echo TEXT_P_TAGS_NO_TAG;
  }
?>
            </td>
            <td valign="top" align="left">
  <a href="javascript:void(0);" onclick="$('.categories_box').css('display','block')">Open</a>|<a href="javascript:void(0);" onclick="$('.categories_box').css('display','none')">Close</a><br>
<?php
  $categories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='0' and cd.site_id='0' order by c.sort_order, cd.categories_name");
  if (tep_db_num_rows($categories_query)) {
    echo "<ul>"."\n";
    while($categories = tep_db_fetch_array($categories_query)){
      echo '<li>'."\n";
      echo '<input onclick="check_all('.$categories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$categories['categories_id'].'" value="'.$categories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$categories['categories_id'].')">'.$categories['categories_name'].'</a>'."\n";
      echo '<div id="d_'.$categories['categories_id'].'" class="categories_box">';
      $subcategories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='".$categories['categories_id']."' and cd.site_id='0' order by c.sort_order, cd.categories_name");
      if (tep_db_num_rows($subcategories_query)) {
        echo '<ul id="c_'.$categories['categories_id'].'">'."\n";
        while($subcategories = tep_db_fetch_array($subcategories_query)) {
          echo '<li>'."\n";
          echo '<input onclick="check_all('.$subcategories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$subcategories['categories_id'].'" value="'.$subcategories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$subcategories['categories_id'].')">'.$subcategories['categories_name'].'</a>'."\n";
          echo '<div id="d_'.$subcategories['categories_id'].'" class="categories_box">';
          $subsubcategories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='".$subcategories['categories_id']."' and cd.site_id='0' order by c.sort_order, cd.categories_name");
          if (tep_db_num_rows($subsubcategories_query)) {
            echo '<ul id="c_'.$subcategories['categories_id'].'">';
            while($subsubcategories = tep_db_fetch_array($subsubcategories_query)) {
              echo '<li>'."\n";
              echo '<input onclick="check_all('.$subsubcategories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$subsubcategories['categories_id'].'" value="'.$subsubcategories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$subsubcategories['categories_id'].')">'.$subsubcategories['categories_name'].'</a>'."\n";
              echo '<div id="d_'.$subsubcategories['categories_id'].'" class="categories_box">';
              products_box($subsubcategories['categories_id']);
              echo '</div>'."\n";
              echo '</li>'."\n";
            }
            echo '</ul>'."\n";
          }
          products_box($subcategories['categories_id']);
          echo '</div>'."\n";
          echo '</li>'."\n";
        }
        echo '</ul>'."\n";
      }
      products_box(0);
      echo '</div>'."\n";
      echo '</li>'."\n";
    }
    echo "</ul>"."\n";
  } else {
    echo TEXT_P_TAGS_NO_TAG;
  }
?>
            </td>
          </tr>
          <tr>
           <td colspan="2">
           <?php 
            echo TEXT_USER_ADDED.'&nbsp;';
            echo TEXT_UNSET_DATA;
           ?>
           </td>
          </tr>
          <tr>
           <td colspan="2">
           <?php 
            echo TEXT_USER_UPDATE.'&nbsp;';
            echo TEXT_UNSET_DATA;
           ?>
           </td>
          </tr>
         <?php 
             $tags_date = tep_db_query("select * from tags order by date_update desc ");
             $tags_row = tep_db_fetch_array($tags_date);
             if(tep_not_null($tags_row['user_update'])){
         ?>
          <tr>
           <td colspan="2">
           <?php 
            echo TEXT_USER_UPDATE.'&nbsp;';
            echo $tags_row['user_update'];
           ?>
           </td>
          </tr>
          <?php }else{ ?> 
          <tr>
           <td colspan="2">
           <?php 
            echo TEXT_USER_UPDATE.'&nbsp;';
            echo TEXT_UNSET_DATA;
           ?>
           </td>
          </tr>
          <?php }if(tep_not_null($tags_row['date_update'])){ ?>
          <tr>
           <td colspan="2">
           <?php
           echo TEXT_DATE_UPDATE.'&nbsp;'; 
           echo $tags_row['date_update'];
           ?>

           </td>
          </tr>
          <?php }else{?>
          <tr>
           <td colspan="2">
           <?php
           echo TEXT_DATE_UPDATE.'&nbsp;'; 
           echo TEXT_UNSET_DATA;
           ?>
           </td>
          </tr>
         <?php } ?>
         </table>
         </form>
        </td>
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
