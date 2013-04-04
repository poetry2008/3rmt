<?php
require('includes/application_top.php');
//把指定页面设置为不缓存
if(isset($_GET['action']) && ($_GET['action'] == 'setting_products_tags' || $_GET['action'] == 'setting_products_to_tags')){

  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  # 永远是改动过的
  header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
  # HTTP/1.1
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  # HTTP/1.0
  header("Pragma: no-cache");
}
$sort_str = '';
if (isset($_GET['sort'])&&$_GET['sort']){
  $sort_str = '&sort='.$_GET['sort'];
}
if (isset($_GET['action']) and $_GET['action']) {
    switch ($_GET['action']) {
/*-----------------------------------
 case 'insert' 添加标签  
 case 'save'   更新标签
 case 'deleteconfirm' 删除标签
 case 'delete_tags' 删除选中的标签
 case 'delete_products_tags' 删除选中标签的所有关联
 case 'products_tags_save' 保存产品标签
 ----------------------------------*/
      case 'insert':
        $tags_name = tep_db_prepare_input($_POST['tags_name']);
        $param_str = tep_db_prepare_input($_POST['param_str']);
        $param_str = str_replace('|||','&',$param_str);

        $t_query = tep_db_query("select * from ". TABLE_TAGS . " where tags_name = '" . $tags_name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res) {
          $messageStack->add_session(TEXT_TAGS_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_TAGS,$param_str));
        }
    
        $tags_images = tep_get_uploaded_file('tags_images');

        $image_directory = tep_get_local_path(tep_get_upload_dir().'tags/');
        if (is_uploaded_file($tags_images['tmp_name'])) {
          tep_copy_uploaded_file($tags_images, $image_directory);
        }

        tep_db_query("insert into " . TABLE_TAGS . " (tags_checked, tags_images, tags_name,user_added,date_added,user_update,date_update) values ('0', '" . (isset($tags_images['name']) ? 'tags/'.$tags_images['name'] : '') . "', '" . tep_db_input($tags_name) . "','".$_SESSION['user_name']."',now(),'".$_SESSION['user_name']."',now())");
        if($sort_str){
          tep_redirect(tep_href_link(FILENAME_TAGS.'?'.$sort_str));
        }else{
          tep_redirect(tep_href_link(FILENAME_TAGS));
        }
        break;
      case 'save':
        $tags_id = tep_db_prepare_input($_POST['tags_id']);
        $tags_name = tep_db_prepare_input($_POST['tags_name']);
        $param_str = tep_db_prepare_input($_POST['param_str']);
        $param_str = str_replace('|||','&',$param_str);
        
        $t_query = tep_db_query("select * from ". TABLE_TAGS . " where tags_name = '" . $tags_name . "'");
        $t_res = tep_db_fetch_array($t_query);
        if ($t_res && $t_res['tags_id'] != $tags_id) {
          $messageStack->add_session(TEXT_TAGS_NAME_EXISTS, 'error');
          tep_redirect(tep_href_link(FILENAME_TAGS, $param_str));
        }

        if(isset($_POST['delete_image']) && $_POST['delete_image']){
          //unlink(DIR_FS_CATALOG_IMAGES . $t_res['tags_images']);
          unlink(tep_get_upload_dir(). $t_res['tags_images']);
          tep_db_query("update " . TABLE_TAGS . " set tags_images = '' where tags_id = '" . tep_db_input($tags_id) . "'");
        }

        $tags_image = tep_get_uploaded_file('tags_images');

        //$image_directory = tep_get_local_path(DIR_FS_CATALOG_IMAGES) . DIRECTORY_SEPARATOR . 'tags' . DIRECTORY_SEPARATOR;
        $image_directory = tep_get_local_path(tep_get_upload_dir().'tags/');
        if (is_uploaded_file($tags_image['tmp_name'])) {
          tep_copy_uploaded_file($tags_image, $image_directory);
        }

        tep_db_query("update " . TABLE_TAGS . " set " . (isset($tags_image['name']) && $tags_image['name'] ? "tags_images = 'tags/" . tep_db_input($tags_image['name'])."', " : '') . " tags_name = '" . tep_db_input($tags_name) . "',user_update='".$_SESSION['user_name']."',date_update=now() where tags_id = '" . tep_db_input($tags_id) . "'");
        tep_redirect(tep_href_link(FILENAME_TAGS, $param_str));
        break;
      case 'deleteconfirm':
        $tags_id = tep_db_prepare_input($_POST['tags_id']);
        $t_query = tep_db_query("select * from ". TABLE_TAGS . " where tags_id = '" . tep_db_input($tags_id) . "'");
        $t_res = tep_db_fetch_array($t_query); 
        tep_db_free_result($t_query);
        unlink(tep_get_upload_dir(). $t_res['tags_images']);
        tep_db_query("delete from " . TABLE_TAGS . " where tags_id = '" . tep_db_input($tags_id) . "'");
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_TAGS . " where tags_id = '" . tep_db_input($tags_id) . "'"); 
        tep_redirect(tep_href_link(FILENAME_TAGS, $param_str));
        break;
      case 'delete_tags':
        $tags_list_array = $_POST['tags_list_id'];
        $tags_list_str = implode(',',$tags_list_array);
        $t_query = tep_db_query("select * from ". TABLE_TAGS . " where tags_id in (" . $tags_list_str . ")");
        while($t_res = tep_db_fetch_array($t_query)){

          unlink(tep_get_upload_dir(). $t_res['tags_images']);
        } 
        tep_db_free_result($t_query); 
        tep_db_query("delete from " . TABLE_TAGS . " where tags_id in (" . $tags_list_str . ")");
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_TAGS . " where tags_id in (" . $tags_list_str . ")");
        tep_redirect(tep_href_link(FILENAME_TAGS, $param_str));
        break;
      case 'delete_products_tags':
        $tags_list_array = $_POST['tags_list_id'];
        $tags_list_str = implode(',',$tags_list_array);
        $tags_url = $_POST['tags_url'];
        tep_db_query("delete from " . TABLE_PRODUCTS_TO_TAGS . " where tags_id in (" . $tags_list_str . ")");
        tep_redirect(tep_href_link(FILENAME_TAGS, $tags_url));
        break;
      case 'products_tags_save': 
        $tags_id_list = $_POST['tags_id_list'];
        $tags_url = $_POST['tags_url'];
        $tags_id_list_array = explode(',',$tags_id_list);
        if (is_array($tags_id_list_array) && !empty($tags_id_list_array)) {
          foreach($tags_id_list_array as $tid) {
          tep_db_query("update " . TABLE_TAGS . " set user_update='".$_SESSION['user_name']."',date_update=now() where tags_id = '" . tep_db_input($tid) . "'");
          tep_db_query("delete from products_to_tags where tags_id='".$tid."'");
            if ($_POST['products_id']) {
               foreach($_POST['products_id'] as $pid) {
                tep_db_perform("products_to_tags", array('products_id' => (int)$pid, 'tags_id' => (int)$tid));
               }
            }
          } 
        }
        tep_redirect(tep_href_link(FILENAME_TAGS,$tags_url));
        break;
    }
  }
  if(isset($_GET['action']) && $_GET['action'] == 'setting_products_tags' && isset( $_POST['tags_list_id']) &&  is_array($_POST['tags_list_id'])){

    $tags_id_array = $_POST['tags_list_id'];
    $tags_id_str = implode(',',$tags_id_array);
    if(count($tags_id_array) == 1){

      $tags_id_query = tep_db_query("select products_id from products_to_tags where tags_id='".$tags_id_array[0]."'");
      $products_tags_array = array();
      while($tags_id_array = tep_db_fetch_array($tags_id_query)){

        $products_tags_array[] = $tags_id_array['products_id'];
      }
      tep_db_free_result($tags_id_query);
    }
  }
/*-----------------------------------
 功能：产品复选框
 参数：$cid(string) 类别ID值
 参数：$products_id_array(array) 商品ID数组 
 返回值：无
 ----------------------------------*/ 
  function products_box($cid,$products_id_array){
      global $checked_flag,$i;
      $products_query = tep_db_query("select * from products p,products_to_categories p2c,products_description pd where p.products_id = pd.products_id and p2c.products_id=pd.products_id and pd.site_id=0 and p2c.categories_id='".$cid."' order by p.sort_order, pd.products_name, pd.products_id");
      if (tep_db_num_rows($products_query)) {
        echo '<ul id="p_'.$categories['categories_id'].'" class="products_box"'.($cid == 0 ? ' style="padding-left:0;"' : '').'>'."\n";
        while($products = tep_db_fetch_array($products_query)) {
          if(!empty($products_id_array)){
            if(in_array($products['products_id'],$products_id_array)){

              $checked = ' checked';
            }else{
              $checked = ''; 
              $checked_flag = false;
            } 
          }
          echo '<li>'."\n";
          echo '<input type="checkbox" class="products_checkbox" name="products_id[]" id="products_'.$products['products_id'].'" value="'.$products['products_id'].'"'.$checked.'>'.$products['products_name']."\n";
          echo '</li>'."\n";
          $i++;
        }
        echo '</ul>'."\n";
      }else{
        $checked_flag = false; 
      }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo (isset($_GET['action']) && $_GET['action'] == 'setting_products_tags') ? PRODUCTS_TO_TAGS_TITLE : HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<script language="javascript">
$(document).ready(function() {
  if(document.getElementsByName("select_edit_tags")[0]){
    document.getElementsByName("select_edit_tags")[0].value = 0;
  }
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_popup_info').css('display') != 'none') {
        close_tags_info(); 
      }
    }
    if (event.which == 13) {
      <?php //回车?> 
      if ($('#show_popup_info').css('display') != 'none') {  
        if($("#show_popup_info").find('input:submit').first().val()){
          $("#show_popup_info").find('input:submit').first().trigger("click");
        }else{
          $("#show_popup_info").find('input:button').first().trigger("click"); 
        }
      } 
    }
    if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_popup_info').css('display') != 'none') {
        if ($("#tags_prev")) {
          $("#tags_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_popup_info').css('display') != 'none') {
        if ($("#tags_next")) {
          $("#tags_next").trigger("click");
        }
      } 
    }
  });    
});
window.onresize = resize_option_page;
function resize_option_page()
{
  if ($(".box_warp").height() < $(".compatible").height()) {
    $(".box_warp").height($(".compatible").height()); 
  }
}

<?php //商品管理页跳转过来的，排序处理?>
function change_sort_tags(sort_type)
{
  url = 'tags.php?<?php echo preg_replace("/&sort=.+/","",$_SERVER['QUERY_STRING']);?>&sort=' +sort_type;
  window.location.href = url;
}

<?php //商品管理页跳转过来的，针对不同的方法，执行不同的动作?>
function select_type_changed_products(value)
{
  switch(value){
 
  case '1':
    setting_products_to_tags('tags_list_id[]');
    break;
  case '2': 
    delete_select_products_to_tags('tags_list_id[]');
    break;
  }
}

<?php //商品管理页跳转过来的标签关联商品时，判断多选框是否被选中?>
function setting_products_to_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    document.edit_tags.action = '<?php echo FILENAME_TAGS;?>?action=setting_products_to_tags';
    document.edit_tags.submit(); 
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    document.getElementsByName("select_edit_tags")[1].value = 0;
    alert('<?php echo TEXT_TAGS_MUST_SELECT;?>'); 
  }
}

<?php //商品管理页跳转过来，删除商品的关联时的确认提示?>
function delete_select_products_to_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  } 

  if(sel_num == 1){
    if (confirm('<?php echo TEXT_PRODUCTS_DELETE_TAGS_CONFIRM;?>')) {
      document.edit_tags.action = '<?php echo FILENAME_CATEGORIES;?>?action=products_tags_delete';
      document.edit_tags.submit(); 
    }else{

      document.getElementsByName("select_edit_tags")[0].value = 0;
      document.getElementsByName("select_edit_tags")[1].value = 0;
    } 
  }else{
    document.getElementsByName("select_edit_tags")[0].value = 0;
    document.getElementsByName("select_edit_tags")[1].value = 0;
    alert('<?php echo TEXT_TAGS_MUST_SELECT;?>'); 
  }
}

<?php //提交时，对数据完成型的判断?>
function create_tags_submit(){

  var error = false;
  var tags_name = document.getElementsByName("tags_name")[0];
  var tags_name_value = tags_name.value;
  tags_name_value = tags_name_value.replace(/\s/g,"");
  if(tags_name_value == ''){

    error = true; 
    $("#tags_name_error").html('&nbsp;<font color="#FF0000"><?php echo TEXT_TAGS_MUST_INPUT;?></font>');
  }else{
    if(!document.getElementsByName("tags_id")[0]){
      var tags_images = document.getElementsByName("tags_images")[0];
      tags_images = tags_images.value;
      tags_images = tags_images.split('\\');
      tags_images = tags_images.pop();
      if(tags_images != ''){
      $.ajax({
        url: 'ajax.php?action=check_file_exists',      
        data: 'table=<?php echo TABLE_TAGS;?>&field=tags_images&dir=tags/&file='+tags_images,
        type: 'POST',
        dataType: 'text',
        async:false,
        success: function (data) {
          if(parseInt(data) > 0){
 
            if(confirm('<?php echo TEXT_CHECK_FILE_EXISTS;?>')){

              error = false; 
            }else{
              error = true; 
            }   
          }else{
            error = false; 
          }
        }
      }); 
      }else{
        error = false; 
      }
    }
  }

  if(error == true){

    return false;
  }
  return true;
}
<?php //提交类型处理?>
function edit_tags_submit(action){

  if(action == 'deleteconfirm'){
    if(confirm('<?php echo TEXT_INFO_DELETE_INTRO;?>')){
      var tags_images_id = $("#tags_images_id").val();
      tags_images = tags_images_id;
      $.ajax({
        url: 'ajax.php?action=check_file_exists',      
        data: 'table=<?php echo TABLE_TAGS;?>&field=tags_images&dir=tags/&file='+tags_images,
        type: 'POST',
        dataType: 'text',
        async:false,
        success: function (data) {
          if(parseInt(data) > 1){
            if(confirm('<?php echo TEXT_CHECK_FILE_EXISTS_DELETE;?>')){

              document.tags_form.action = '<?php echo FILENAME_TAGS;?>?action='+action;
              document.tags_form.submit();
            } 
          }else{
            document.tags_form.action = '<?php echo FILENAME_TAGS;?>?action='+action;
            document.tags_form.submit(); 
          }
        }
      }); 
    }
  }else{
    if(create_tags_submit()){
      var tags_images = document.getElementsByName("tags_images")[0];
      var delete_image = '';
      if(document.getElementsByName("delete_image")[0]){
        delete_image = document.getElementsByName("delete_image")[0];
        delete_image = delete_image.checked;
      }
      var tags_images_id = $("#tags_images_id").val();
      tags_images = tags_images.value;
      tags_images = tags_images.split('\\');
      tags_images = tags_images.pop();
      if(delete_image == true){tags_images = tags_images_id;}
      if(tags_images != '' || delete_image == true){
      $.ajax({
        url: 'ajax.php?action=check_file_exists',      
        data: 'table=<?php echo TABLE_TAGS;?>&field=tags_images&dir=tags/&file='+tags_images,
        type: 'POST',
        dataType: 'text',
        async:false,
        success: function (data) {
          if(parseInt(data) > 0){

           if(delete_image == false){
            if(confirm('<?php echo TEXT_CHECK_FILE_EXISTS;?>')){

              document.tags_form.action = '<?php echo FILENAME_TAGS;?>?action='+action;
              document.tags_form.submit();
            }
           }else{
            if(parseInt(data) > 1){
            if(confirm('<?php echo TEXT_CHECK_FILE_EXISTS_DELETE;?>')){

              document.tags_form.action = '<?php echo FILENAME_TAGS;?>?action='+action;
              document.tags_form.submit();
            }  
            }else{

              document.tags_form.action = '<?php echo FILENAME_TAGS;?>?action='+action;
              document.tags_form.submit();
            }
           }
          }else{
            document.tags_form.action = '<?php echo FILENAME_TAGS;?>?action='+action;
            document.tags_form.submit(); 
          }
        }
      }); 
      }else{

        document.tags_form.action = '<?php echo FILENAME_TAGS;?>?action='+action;
        document.tags_form.submit();
      }
    }
  }
}
<?php //多选框全选动作?>
function all_select_tags(tags_list_id)
{
  var check_flag = document.edit_tags.all_check.checked;
  if (document.edit_tags.elements[tags_list_id]) {
    if (document.edit_tags.elements[tags_list_id].length == null) {
      if (check_flag == true) {
        document.edit_tags.elements[tags_list_id].checked = true;
      } else {
        document.edit_tags.elements[tags_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
        if (check_flag == true) {
          document.edit_tags.elements[tags_list_id][i].checked = true;
        } else {
          document.edit_tags.elements[tags_list_id][i].checked = false;
        }
      }
    }
  }
}

<?php //删除标签及关联时，判断多选框是否被选中及删除时的确认提示?>
function delete_select_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    if (confirm('<?php echo TEXT_TAGS_DELETE_CONFIRM;?>')) {
      var tags_images = '';
      var tags_images_flag = false;
      for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
        if (document.edit_tags.elements[tags_list_id][i].checked == true) {

          if(document.edit_tags.elements["tags_list_images[]"][i].value != ''){
            tags_images = document.edit_tags.elements["tags_list_images[]"][i].value;
            $.ajax({
            url: 'ajax.php?action=check_file_exists',      
            data: 'table=<?php echo TABLE_TAGS;?>&field=tags_images&dir=tags/&file='+tags_images,
            type: 'POST',
            dataType: 'text',
            async:false,
            success: function (data) {
            if(parseInt(data) > 1){
              tags_images_flag = true;
            }
          }
          });
          }
        }
        if(tags_images_flag == true){break;}
      } 
      if(tags_images_flag == true){

        if(confirm('<?php echo TEXT_CHECK_FILE_EXISTS_DELETE;?>')){
           document.edit_tags.action = '<?php echo FILENAME_TAGS;?>?action=delete_tags';
           document.edit_tags.submit(); 
        }
      }else{
        document.edit_tags.action = '<?php echo FILENAME_TAGS;?>?action=delete_tags';
        document.edit_tags.submit(); 
      } 
    }else{
      document.getElementsByName("select_edit_tags")[0].value = 0; 
    }
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    alert('<?php echo TEXT_TAGS_MUST_SELECT;?>'); 
  }
}

<?php //删除标签的关联时，判断多选框是否被选中及删除时的确认提示?>
function delete_select_products_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    if (confirm('<?php echo TEXT_TAGS_DELETE_PRODUCTS_CONFIRM;?>')) {
      document.edit_tags.action = '<?php echo FILENAME_TAGS;?>?action=delete_products_tags';
      document.edit_tags.submit(); 
    }else{

      document.getElementsByName("select_edit_tags")[0].value = 0;
    }
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    alert('<?php echo TEXT_TAGS_MUST_SELECT;?>'); 
  }
}

<?php //标签关联商品时，判断多选框是否被选中?>
function setting_products_tags(tags_list_id)
{
  sel_num = 0;
  if (document.edit_tags.elements[tags_list_id].length == null) {
    if (document.edit_tags.elements[tags_list_id].checked == true) {
      sel_num = 1;
    }
  } else {
    for (i = 0; i < document.edit_tags.elements[tags_list_id].length; i++) {
      if (document.edit_tags.elements[tags_list_id][i].checked == true) {
        sel_num = 1;
        break;
      }
    }
  }
  
  if (sel_num == 1) {
    document.edit_tags.action = '<?php echo FILENAME_TAGS;?>?action=setting_products_tags';
    document.edit_tags.submit(); 
  } else {
    document.getElementsByName("select_edit_tags")[0].value = 0;
    alert('<?php echo TEXT_TAGS_MUST_SELECT;?>'); 
  }
}

<?php //针对不同的方法，执行不同的动作?>
function select_type_changed(value)
{
  switch(value){

  case '1':
    delete_select_tags('tags_list_id[]'); 
    break;
  case '2':
    delete_select_products_tags('tags_list_id[]'); 
    break;
  case '3':
    setting_products_tags('tags_list_id[]'); 
    break;
  }
}

<?php //关闭弹出框?>
function close_tags_info()
{
  $('#show_popup_info').html(''); 
  $('#show_popup_info').css('display', 'none'); 
}
<?php //编辑tags的信息?>
function show_tags_info(ele, tid, param_str)
{
  ele = ele.parentNode;
  param_str = decodeURIComponent(param_str);
  param_str = param_str.replace(/&/g,'|||');
  $.ajax({
    url: 'ajax.php?action=edit_tags',      
    data: 'tags_id='+tid+'&param_str='+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data); 
      if (document.documentElement.clientHeight < document.body.scrollHeight) {
        if (ele.offsetTop+$('#tags_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.body.scrollHeight) {
          offset = ele.offsetTop+$('#tags_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height();
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#tags_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      } else {
        if (ele.offsetTop+$('#tags_list_box').position().top+ele.offsetHeight+$('#show_popup_info').height() > document.documentElement.clientHeight) {
          offset = ele.offsetTop+$('#tags_list_box').position().top-$('#show_popup_info').height()-$('#offsetHeight').height()-ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        } else {
          offset = ele.offsetTop+$('#tags_list_box').position().top+ele.offsetHeight;
          $('#show_popup_info').css('top', offset).show(); 
        }
      }
      $('#show_popup_info').show(); 
    }
  }); 
}
<?php //新建tags的信息?>
function create_tags_info(ele)
{
  ele = ele.parentNode;
  $.ajax({
    url: 'ajax.php?action=create_tags',      
    data: '',
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  }); 
}
<?php //编辑tags的上一个，下一个信息?>
function show_link_tags_info(tid, param_str)
{
  param_str = decodeURIComponent(param_str);
  param_str = param_str.replace(/&/g,'|||');
  $.ajax({
    url: 'ajax.php?action=edit_tags',      
    data: 'tags_id='+tid+'&param_str='+param_str,
    type: 'POST',
    dataType: 'text',
    async:false,
    success: function (data) {
      $('#show_popup_info').html(data);  
      $('#show_popup_info').show(); 
    }
  });  
}
<?php //判断是否选中了具体商品?>
function products_tags_submit(){

  var submit_flag = false;
  $(".products_checkbox").each(function(){

    if($(this).attr('checked') == 'checked'){
    
      submit_flag = true; 
    }
  }); 
  if(submit_flag == true){

    document.products_to_tags.submit();
  }else{

    alert('<?php echo TEXT_PRODUCTS_TAGS_CHECK;?>');
  }
}
<?php //多选框全选动作?>
function all_select_products(tags_list_id)
{
  var check_flag = document.products_to_tags.all_check.checked;
  if (document.products_to_tags.elements[tags_list_id]) {
    if (document.products_to_tags.elements[tags_list_id].length == null) {
      if (check_flag == true) {
        document.products_to_tags.elements[tags_list_id].checked = true;
      } else {
        document.products_to_tags.elements[tags_list_id].checked = false;
      }
    } else {
      for (i = 0; i < document.products_to_tags.elements[tags_list_id].length; i++) {
        if (check_flag == true) {
          document.products_to_tags.elements[tags_list_id][i].checked = true;
        } else {
          document.products_to_tags.elements[tags_list_id][i].checked = false;
        }
      }
    }
  }
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
  display:block;
}
</style>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_array);
if($belong_array[0][0] != ''){

  $belong = $href_url.'?'.$belong_array[0][0];

}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onload="SetFocus();">
<!-- header //-->
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
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
    <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?><div class="compatible">
<?php
            //商品关联标签时，标签显示页面 
            if(isset($_GET['action']) && $_GET['action'] == 'products_to_tags'){

              if(isset($_POST['products_id_list']) && is_array($_POST['products_id_list']) && !empty($_POST['products_id_list']) && empty($_POST['categories_id_list'])){

                if(count($_POST['products_id_list']) == 1){

                  $products_id_num = $_POST['products_id_list'][0];
                  $products_tags_list_array = array();
                  $products_tags_query = tep_db_query("select tags_id from products_to_tags where products_id='".$products_id_num."'");
                  while($products_tags_array = tep_db_fetch_array($products_tags_query)){

                    $products_tags_list_array[] = $products_tags_array['tags_id']; 
                  }
                  tep_db_free_result($products_tags_query);
                }
              }
            ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo TEXT_PRODUCTS_TO_TAGS_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
          <tr>
            <td><?php echo TEXT_PRODUCTS_TO_TAGS_TITLE_SELECT; ?></td>
            <td class="pageHeading" align="right">
                   <?php 
                    $tags_url_string = str_replace('action=products_to_tags&','',$_SERVER['QUERY_STRING']); 
                    $tags_url_string = str_replace('action=products_to_tags','',$tags_url_string);
                    $tags_url_string = preg_replace("/sort=.+/",'',$tags_url_string);
                    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $tags_url_string) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; 
                    ?>
                    <select name="select_edit_tags" onchange="select_type_changed_products(this.value);">
                      <option value="0"><?php echo TEXT_PRODUCTS_TO_TAGS_SELECT;?></option>
                      <option value="1"><?php echo TEXT_PRODUCTS_TO_TAGS_SETTING;?></option>
                      <option value="2"><?php echo TEXT_PRODUCTS_TO_TAGS_DELETE;?></option>
                    </select> 
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
          <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" id="tags_list_box">
              <tr>
              <td colspan='4' align="right">
                 <select onchange="if(options[selectedIndex].value) change_sort_tags(options[selectedIndex].value)">
                 <?php if(!isset($_GET['sort'])){ ?>
                    <option selected="" value="4a"><?php echo LISTING_TITLE_A_TO_Z;?></option> <option value="4d"><?php echo LISTING_TITLE_Z_TO_A;?></option>
                    <option value="5a"><?php echo LISTING_TITLE_A_TO_N;?></option>
                    <option value="5d"><?php echo LISTING_TITLE_N_TO_A;?></option>
                    <?php }else{ 
                    if($_GET['sort']=='4a'){
                      echo '<option selected="" value="4a">'.LISTING_TITLE_A_TO_Z.'</option>';
                    }else{
                      echo '<option value="4a">'.LISTING_TITLE_A_TO_Z.'</option>';
                    }
                    if($_GET['sort']=='4d'){
                      echo '<option selected="" value="4d">'.LISTING_TITLE_Z_TO_A.'</option>';
                    }else{
                      echo '<option value="4d">'.LISTING_TITLE_Z_TO_A.'</option>';
                    } if($_GET['sort']=='5a'){
                      echo '<option selected="" value="5a">'.LISTING_TITLE_A_TO_N.'</option>';
                    }else{
                      echo '<option value="5a">'.LISTING_TITLE_A_TO_N.'</option>';
                    }
                    if($_GET['sort']=='5d'){
                      echo '<option selected="" value="5d">'.LISTING_TITLE_N_TO_A.'</option>';
                    }else{
                      echo '<option value="5d">'.LISTING_TITLE_N_TO_A.'</option>';
                    }
                    }
                    ?>
                 </select>
              </td>
              </tr>
              <form name="edit_tags" method="post" action="<?php echo FILENAME_TAGS;?>">
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td><input type="checkbox" name="all_check" onclick="all_select_tags('tags_list_id[]');"><?php echo TEXT_PRODUCTS_TAGS_ALL_CHECK; ?></td></tr></table></td>
                <?php
            if(isset($_POST['categories_id_list']) && is_array($_POST['categories_id_list'])){

              $categories_id_list_str = implode(',',$_POST['categories_id_list']);
              $_SESSION['categories_id_list_string'] = $categories_id_list_str;
            }else{
              if(isset($_SESSION['categories_id_list_string']) && !isset($_POST['categories_id_list']) && !isset($_POST['products_id_list'])){

                $categories_id_list_str = $_SESSION['categories_id_list_string'];
              }else{
                unset($_SESSION['categories_id_list_string']); 
              } 
            }
            if(isset($_POST['products_id_list']) && is_array($_POST['products_id_list'])){

              $products_id_list_str = implode(',',$_POST['products_id_list']);
              $_SESSION['products_id_list_string'] = $products_id_list_str;
            }else{

              if(isset($_SESSION['products_id_list_string']) && !isset($_POST['categories_id_list']) && !isset($_POST['products_id_list'])){

                $products_id_list_str = $_SESSION['products_id_list_string'];
              }else{
                unset($_SESSION['products_id_list_string']); 
              }
            }
            $tags_url = $_SERVER['QUERY_STRING'];
            $tags_url_array = explode('&',$tags_url); 
            unset($tags_url_array[0]);
            $tags_url = implode('&',$tags_url_array);
                ?>
                  <td cols="3">&nbsp;<input type="hidden" name="categories_id_list" value="<?php echo $categories_id_list_str;?>"><input type="hidden" name="products_id_list" value="<?php echo $products_id_list_str;?>"><input type="hidden" name="tags_url" value="<?php echo $tags_url;?>"></td>
              </tr>
<?php
  //echo MAX_DISPLAY_SEARCH_RESULTS;
  $tags_query_raw = "
  select t.tags_id, t.tags_name, t.tags_images, t.tags_checked, t.user_added,t.date_added,t.user_update,t.date_update
  from " . TABLE_TAGS . " t order by t.tags_order,t.tags_name";
  if(isset($_GET['sort'])&&$_GET['sort']){
    $tags_query_raw = "
      select t.tags_id, t.tags_name, t.tags_images, t.tags_checked
      from " . TABLE_TAGS ." t ";
    switch($_GET['sort']){
/*----------------------------
 case '4a'  排列顺序(a-z) 递增
 case '4d'  排列顺序(z-a) 递减
 case '5a'  排列顺序(あ-ん) 递增
 case '5d'  排列顺序(ん-あ) 递减
 ---------------------------*/
      case '4a':
        $tags_query_raw .=' order by t.tags_name asc'; 
        break;
      case '4d':
        $tags_query_raw .=' order by t.tags_name desc'; 
        break;
      case '5a':
        $tags_query_raw .=' order by t.tags_name asc'; 
        break;
      case '5d':
        $tags_query_raw .=' order by t.tags_name desc'; 
        break;
    }
  }
  $tags_query = tep_db_query($tags_query_raw);
  $tags_sum = tep_db_num_rows($tags_query);
  $tags_cols = ceil($tags_sum/4);
  $tags_number = 0;
  echo '<tr><td width="25%" class="dataTableContent" valign="top"><table width="100%" cellspacing="0" cellpadding="2" border="0">';
  while ($tags = tep_db_fetch_array($tags_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $tags['tags_id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($tags);
    }
 
    echo '              <tr>' . "\n";
?>
  <td class="dataTableContent" width="10%" valign="top"><input type="checkbox" name="tags_list_id[]" value="<?php echo $tags['tags_id'];?>"<?php echo in_array($tags['tags_id'],$products_tags_list_array) ? ' checked="checked"' : '';?>></td>      
                <td class="dataTableContent"><?php echo $tags['tags_name']; ?></td> 
                <td class="dataTableContent" align="right">&nbsp;</td>
              </tr>
<?php
    $tags_number++;
    if($tags_number % $tags_cols == 0){

      echo '</table></td><td width="25%" class="dataTableContent" valign="top"><table width="100%" cellspacing="0" cellpadding="2" border="0">';
    } 
  }
?>
              </table>
              </td>
              </tr>
              </form>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2"> 
                  <tr>
                    <td colspan="2" align="right">
                    <?php 
                    echo '<a href="' . tep_href_link(FILENAME_CATEGORIES, $tags_url_string) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; 
                    ?> 
                    <select name="select_edit_tags" onchange="select_type_changed_products(this.value);">
                      <option value="0"><?php echo TEXT_PRODUCTS_TO_TAGS_SELECT;?></option>
                      <option value="1"><?php echo TEXT_PRODUCTS_TO_TAGS_SETTING;?></option>
                      <option value="2"><?php echo TEXT_PRODUCTS_TO_TAGS_DELETE;?></option>
                    </select> 
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table>
           <?php
            //商品管理页跳转过来的商品关联标签时,所有商品显示页面
            }else if(isset($_GET['action']) && $_GET['action'] == 'setting_products_to_tags'){
              if(isset($_GET['action']) && $_GET['action'] == 'setting_products_to_tags' && isset( $_POST['tags_list_id']) &&  is_array($_POST['tags_list_id'])){

                $tags_id_array = $_POST['tags_list_id'];
                $tags_id_str = implode(',',$tags_id_array);
                $categories_id_list = $_POST['categories_id_list'];
                $products_id_list = $_POST['products_id_list']; 
                if(trim($categories_id_list) != ''){
                  $categories_id_list = explode(',',$categories_id_list);
                }
                if(trim($products_id_list) != ''){
                  $products_id_list = explode(',',$products_id_list); 
                }
                $products_tags_array = array();
               if(!empty($categories_id_list)){
                foreach($categories_id_list as $categories_id_value){

                  $parent_categories_query = tep_db_query("select parent_id from categories  where categories_id='".$categories_id_value."'");
                  $parent_categories_array = tep_db_fetch_array($parent_categories_query);
                  tep_db_free_result($parent_categories_query);

                  $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_id_value."'");
                  while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                     $products_tags_array[] = $products_id_list_array['products_id'];
                  }
                  tep_db_free_result($products_id_list_query);
                  if($parent_categories_array['parent_id'] == '0'){

                    $child_categories_query = tep_db_query("select categories_id from categories  where parent_id='".$categories_id_value."'");
                    while($child_categories_array = tep_db_fetch_array($child_categories_query)){

                      $parent_categories_id_query = tep_db_query("select categories_id from categories  where parent_id='".$child_categories_array['categories_id']."'");
                      
                      if(tep_db_num_rows($parent_categories_id_query)){
                        
                        while($parent_categories_id_array = tep_db_fetch_array($parent_categories_id_query)){
                          $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$parent_categories_id_array['categories_id']."'");
                          while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                            $products_tags_array[] = $products_id_list_array['products_id'];
                          }
                          tep_db_free_result($products_id_list_query);
                        }
                        tep_db_free_result($parent_categories_id_query);
                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$child_categories_array['categories_id']."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }else{

                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$child_categories_array['categories_id']."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }
                    }
                    tep_db_free_result($child_categories_query);
                  }else{

                    $parent_categories_id_query = tep_db_query("select categories_id from categories  where parent_id='".$categories_id_value."'");
                      
                      if(tep_db_num_rows($parent_categories_id_query)){
                        
                        while($parent_categories_id_array = tep_db_fetch_array($parent_categories_id_query)){
                          $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$parent_categories_id_array['categories_id']."'");
                          while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                            $products_tags_array[] = $products_id_list_array['products_id'];
                          }
                          tep_db_free_result($products_id_list_query);
                        }
                        tep_db_free_result($parent_categories_id_query);
                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_id_value."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }else{

                        $products_id_list_query = tep_db_query("select products_id from ". TABLE_PRODUCTS_TO_CATEGORIES ." where categories_id='".$categories_id_value."'");
                        while($products_id_list_array = tep_db_fetch_array($products_id_list_query)){


                          $products_tags_array[] = $products_id_list_array['products_id'];
                        }
                        tep_db_free_result($products_id_list_query);
                      }
                  }
                }
               }
               if(!empty($products_id_list)){
                foreach($products_id_list as $products_id_value){

                  $products_tags_array[] = $products_id_value;
                }
               }
              }
              $tags_url = $_POST['tags_url'];
            ?>
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
  <?php echo tep_draw_form('products_to_tags',FILENAME_CATEGORIES, 'action=products_tags_save', 'post');?>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr> 
          <td valign="top" align="left">&nbsp;<input type="checkbox" name="all_check" onclick="all_select_products('categories_id[]');all_select_products('products_id[]')"><?php echo TEXT_PRODUCTS_TAGS_ALL_CHECK;?><input type="hidden" name="tags_id_list" value="<?php echo $tags_id_str;?>"><input type="hidden" name="tags_url" value="<?php echo preg_replace("/&sort=.+/","",$tags_url);?>"><br><td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_TAGS, 'action=products_to_tags&'.$tags_url) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>';?><input type="button" class="element_button" value="<?php echo IMAGE_SAVE;?>" onclick="products_tags_submit();"></td><table width="100%" class="box_ul"><tr>
<?php
  $i = 0;
  $j = 0;
  $temp_array = array();
  $products_num_query = tep_db_query("select products_id from ". TABLE_PRODUCTS);
  $products_num = tep_db_num_rows($products_num_query);
  tep_db_free_result($products_num_query);
  $products_num = ($products_num/5);
  $categories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='0' and cd.site_id='0' order by c.sort_order, cd.categories_name");
  if (tep_db_num_rows($categories_query)) {
    echo "<td width='25%' valign='top'><ul style='padding-left:1px;'>"."\n";
    while($categories = tep_db_fetch_array($categories_query)){
      echo '<li>'."\n";
      echo '<input onclick="check_all('.$categories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$categories['categories_id'].'" value="'.$categories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$categories['categories_id'].')">'.$categories['categories_name'].'</a>'."\n";
      echo '<div id="d_'.$categories['categories_id'].'" class="categories_box">';
      $subcategories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='".$categories['categories_id']."' and cd.site_id='0' order by c.sort_order, cd.categories_name");
      if (tep_db_num_rows($subcategories_query)) {
        echo '<ul id="c_'.$categories['categories_id'].'">'."\n";
        $categories_checked_flag = true;
        while($subcategories = tep_db_fetch_array($subcategories_query)) { echo '<li>'."\n";
          echo '<input onclick="check_all('.$subcategories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$subcategories['categories_id'].'" value="'.$subcategories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$subcategories['categories_id'].')">'.$subcategories['categories_name'].'</a>'."\n";
          echo '<div id="d_'.$subcategories['categories_id'].'" class="categories_box">';
          $subsubcategories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='".$subcategories['categories_id']."' and cd.site_id='0' order by c.sort_order, cd.categories_name");
          if (tep_db_num_rows($subsubcategories_query)) {
            echo '<ul id="c_'.$subcategories['categories_id'].'">';
            while($subsubcategories = tep_db_fetch_array($subsubcategories_query)) {
              echo '<li>'."\n";
              echo '<input onclick="check_all('.$subsubcategories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$subsubcategories['categories_id'].'" value="'.$subsubcategories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$subsubcategories['categories_id'].')">'.$subsubcategories['categories_name'].'</a>'."\n";
              echo '<div id="d_'.$subsubcategories['categories_id'].'" class="categories_box">';
              $checked_flag = true;
              products_box($subsubcategories['categories_id'],$products_tags_array);
              if($checked_flag == true && !empty($products_tags_array)){

                echo '<script language="javascript">';
                echo '$("#categories_'.$subsubcategories['categories_id'].'").attr("checked","checked");';
                echo '</script>';
              }
              echo '</div>'."\n";
              echo '</li>'."\n";
            }
            echo '</ul>'."\n";
          }
          $checked_flag = true;
          products_box($subcategories['categories_id'],$products_tags_array);
          if($checked_flag == true && !empty($products_tags_array)){

            echo '<script language="javascript">';
            echo '$("#categories_'.$subcategories['categories_id'].'").attr("checked","checked");';
            echo '</script>';
          }else{

            $categories_checked_flag = false;
          }
          echo '</div>'."\n";
          echo '</li>'."\n";
        }
        if($categories_checked_flag == true && !empty($products_tags_array)){

          echo '<script language="javascript">';
          echo '$("#categories_'.$categories['categories_id'].'").attr("checked","checked");';
          echo '</script>';
        }
        echo '</ul>'."\n";
      }
      products_box($categories['categories_id'],$products_tags_array);
      echo '</div>'."\n";
      echo '</li>'."\n";  
      if (!in_array(intval($i/$products_num),$temp_array) && intval($i/$products_num) != 0 && $i-$j >= $products_num) {
        $temp_array[] = intval($i/$products_num);
        echo '</ul></td><td width="25%" valign="top"><ul style="padding-left:0;">';  
        $j = $i;
      }
    }
    echo "</ul>";
    products_box(0,$products_tags_array);
    echo "</td>"."\n"; 
  } else {
    echo '<td width="100%">'.TEXT_P_TAGS_NO_TAG.'<td>';
  }
?>
            </tr></table>
            </td>
          </tr>
          <tr>
           <td colspan="2" align="right">
           <?php echo '<a href="' . tep_href_link(FILENAME_TAGS, 'action=products_to_tags&'.$tags_url) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>';?><input type="button" class="element_button" value="<?php echo IMAGE_SAVE;?>" onclick="products_tags_submit();"> 
           </td>
          </tr> 
         </table>
         </form>
        </td>
      </tr>
  </table>
<?php
//标签关联商品时，所有商品显示页面
}else if(isset($_GET['action']) && $_GET['action'] == 'setting_products_tags'){
?>
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
  <?php echo tep_draw_form('products_to_tags','tags.php', 'action=products_tags_save', 'post');?>
  <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr> 
          <td valign="top" align="left">&nbsp;<input type="checkbox" name="all_check" onclick="all_select_products('categories_id[]');all_select_products('products_id[]')"><?php echo TEXT_PRODUCTS_TAGS_ALL_CHECK;?><input type="hidden" name="tags_id_list" value="<?php echo $tags_id_str;?>"><input type="hidden" name="tags_url" value="<?php echo $_POST['tags_url'];?>"><br><td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_TAGS, $_POST['tags_url']) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?><input type="button" class="element_button" value="<?php echo IMAGE_SAVE;?>" onclick="products_tags_submit();"></td><table width="100%" class="box_ul"><tr>
<?php
  $i = 0;
  $j = 0;
  $temp_array = array();
  $products_num_query = tep_db_query("select products_id from ". TABLE_PRODUCTS);
  $products_num = tep_db_num_rows($products_num_query);
  tep_db_free_result($products_num_query);
  $products_num = ($products_num/5);
  $categories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='0' and cd.site_id='0' order by c.sort_order, cd.categories_name");
  if (tep_db_num_rows($categories_query)) {
    echo "<td width='25%' valign='top'><ul style='padding-left:1px;'>"."\n";
    while($categories = tep_db_fetch_array($categories_query)){
      echo '<li>'."\n";
      echo '<input onclick="check_all('.$categories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$categories['categories_id'].'" value="'.$categories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$categories['categories_id'].')">'.$categories['categories_name'].'</a>'."\n";
      echo '<div id="d_'.$categories['categories_id'].'" class="categories_box">';
      $subcategories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='".$categories['categories_id']."' and cd.site_id='0' order by c.sort_order, cd.categories_name");
      if (tep_db_num_rows($subcategories_query)) {
        echo '<ul id="c_'.$categories['categories_id'].'">'."\n";
        $categories_checked_flag = true;
        while($subcategories = tep_db_fetch_array($subcategories_query)) { echo '<li>'."\n";
          echo '<input onclick="check_all('.$subcategories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$subcategories['categories_id'].'" value="'.$subcategories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$subcategories['categories_id'].')">'.$subcategories['categories_name'].'</a>'."\n";
          echo '<div id="d_'.$subcategories['categories_id'].'" class="categories_box">';
          $subsubcategories_query = tep_db_query("select * from categories c,categories_description cd where c.categories_id=cd.categories_id and c.parent_id='".$subcategories['categories_id']."' and cd.site_id='0' order by c.sort_order, cd.categories_name");
          if (tep_db_num_rows($subsubcategories_query)) {
            echo '<ul id="c_'.$subcategories['categories_id'].'">';
            while($subsubcategories = tep_db_fetch_array($subsubcategories_query)) {
              echo '<li>'."\n";
              echo '<input onclick="check_all('.$subsubcategories['categories_id'].')" type="checkbox" name="categories_id[]" id="categories_'.$subsubcategories['categories_id'].'" value="'.$subsubcategories['categories_id'].'"><a href="javascript:void(0)" onclick="switch_categories('.$subsubcategories['categories_id'].')">'.$subsubcategories['categories_name'].'</a>'."\n";
              echo '<div id="d_'.$subsubcategories['categories_id'].'" class="categories_box">';
              $checked_flag = true;
              products_box($subsubcategories['categories_id'],$products_tags_array);
              if($checked_flag == true && !empty($products_tags_array)){

                echo '<script language="javascript">';
                echo '$("#categories_'.$subsubcategories['categories_id'].'").attr("checked","checked");';
                echo '</script>';
              }
              echo '</div>'."\n";
              echo '</li>'."\n";
            }
            echo '</ul>'."\n";
          }
          $checked_flag = true;
          products_box($subcategories['categories_id'],$products_tags_array);
          if($checked_flag == true && !empty($products_tags_array)){

            echo '<script language="javascript">';
            echo '$("#categories_'.$subcategories['categories_id'].'").attr("checked","checked");';
            echo '</script>';
          }else{

            $categories_checked_flag = false;
          }
          echo '</div>'."\n";
          echo '</li>'."\n";
        }
        if($categories_checked_flag == true && !empty($products_tags_array)){

          echo '<script language="javascript">';
          echo '$("#categories_'.$categories['categories_id'].'").attr("checked","checked");';
          echo '</script>';
        }
        echo '</ul>'."\n";
      }
      products_box($categories['categories_id'],$products_tags_array);
      echo '</div>'."\n";
      echo '</li>'."\n";  
      if (!in_array(intval($i/$products_num),$temp_array) && intval($i/$products_num) != 0 && $i-$j >= $products_num) {
        $temp_array[] = intval($i/$products_num);
        echo '</ul></td><td width="25%" valign="top"><ul style="padding-left:0;">';  
        $j = $i;
      }
    }
    echo "</ul>";
    products_box(0,$products_tags_array);
    echo "</td>"."\n"; 
  } else {
    echo '<td width="100%">'.TEXT_P_TAGS_NO_TAG.'<td>';
  }
?>
            </tr></table>
            </td>
          </tr>
          <tr>
           <td colspan="2" align="right">
           <?php echo '<a href="' . tep_href_link(FILENAME_TAGS, $_POST['tags_url']) . '">' . tep_html_element_button(IMAGE_BACK) . '</a>'; ?><input type="button" class="element_button" value="<?php echo IMAGE_SAVE;?>" onclick="products_tags_submit();"> 
           </td>
          </tr> 
         </table>
         </form>
        </td>
      </tr>
  </table>
<?php
//标签关联商品时，标签显示页面
}else{
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
              <td colspan='3' align="right">
                 <select onchange="if(options[selectedIndex].value) change_sort_type(options[selectedIndex].value)">
                 <?php if(!isset($_GET['sort'])){ ?>
                    <option selected="" value="4a"><?php echo LISTING_TITLE_A_TO_Z;?></option> <option value="4d"><?php echo LISTING_TITLE_Z_TO_A;?></option>
                    <option value="5a"><?php echo LISTING_TITLE_A_TO_N;?></option>
                    <option value="5d"><?php echo LISTING_TITLE_N_TO_A;?></option>
                    <?php }else{ 
                    if($_GET['sort']=='4a'){
                      echo '<option selected="" value="4a">'.LISTING_TITLE_A_TO_Z.'</option>';
                    }else{
                      echo '<option value="4a">'.LISTING_TITLE_A_TO_Z.'</option>';
                    }
                    if($_GET['sort']=='4d'){
                      echo '<option selected="" value="4d">'.LISTING_TITLE_Z_TO_A.'</option>';
                    }else{
                      echo '<option value="4d">'.LISTING_TITLE_Z_TO_A.'</option>';
                    } if($_GET['sort']=='5a'){
                      echo '<option selected="" value="5a">'.LISTING_TITLE_A_TO_N.'</option>';
                    }else{
                      echo '<option value="5a">'.LISTING_TITLE_A_TO_N.'</option>';
                    }
                    if($_GET['sort']=='5d'){
                      echo '<option selected="" value="5d">'.LISTING_TITLE_N_TO_A.'</option>';
                    }else{
                      echo '<option value="5d">'.LISTING_TITLE_N_TO_A.'</option>';
                    }
                    }
                    ?>
                 </select>
              </td>
              </tr>
      <tr>
        <td><div id="show_popup_info" style="background-color:#FFFF00;position:absolute;width:70%;min-width:550px;margin-left:0;display:none;"></div><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
          <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2" id="tags_list_box"> 
              <form name="edit_tags" method="post" action="<?php echo FILENAME_TAGS;?>">
              <tr class="dataTableHeadingRow">
              <td class="dataTableHeadingContent" width="10%"><input type="checkbox" name="all_check" onclick="all_select_tags('tags_list_id[]');"><input type="hidden" name="tags_url" value="<?php echo $_SERVER['QUERY_STRING'];?>"></td>
                <td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TAGS_NAME; ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
              </tr>
<?php
  $tags_query_raw = "
  select t.tags_id, t.tags_name, t.tags_images, t.tags_checked, t.user_added,t.date_added,t.user_update,t.date_update
  from " . TABLE_TAGS . " t order by t.tags_order,t.tags_name";
  if(isset($_GET['sort'])&&$_GET['sort']){
    $tags_query_raw = "
      select t.tags_id, t.tags_name, t.tags_images, t.tags_checked
      from " . TABLE_TAGS ." t ";
    switch($_GET['sort']){
/*----------------------------
 case '4a'  排列顺序(a-z) 递增
 case '4d'  排列顺序(z-a) 递减
 case '5a'  排列顺序(あ-ん) 递增
 case '5d'  排列顺序(ん-あ) 递减
 ---------------------------*/
      case '4a':
        $tags_query_raw .=' order by t.tags_name asc'; 
        break;
      case '4d':
        $tags_query_raw .=' order by t.tags_name desc'; 
        break;
      case '5a':
        $tags_query_raw .=' order by t.tags_name asc'; 
        break;
      case '5d':
        $tags_query_raw .=' order by t.tags_name desc'; 
        break;
    }
  }
  $tags_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $tags_query_raw, $tags_query_numrows);
  $tags_query = tep_db_query($tags_query_raw);
  while ($tags = tep_db_fetch_array($tags_query)) {
      if (( (!@$_GET['cID']) || (@$_GET['cID'] == $tags['tags_id'])) && (!@$cInfo) && (substr(@$_GET['action'], 0, 3) != 'new')) {
      $cInfo = new objectInfo($tags);
    }

    $even = 'dataTableSecondRow';
    $odd  = 'dataTableRow';
    $tags_images_array = explode('/',$tags['tags_images']);
    $tags_images_str = end($tags_images_array);
    if (isset($nowColor) && $nowColor == $odd) {
      $nowColor = $even; 
    } else {
      $nowColor = $odd; 
    }
    if (isset($cInfo) && (is_object($cInfo)) && ($tags['tags_id'] == $cInfo->tags_id) && isset($_GET['cID'])) {
      echo '              <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'">' . "\n";
    } else {
      echo '              <tr class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'">' . "\n";
    }
?>
  <td class="dataTableContent"><input type="checkbox" name="tags_list_id[]" value="<?php echo $tags['tags_id'];?>"><input type="hidden" name="tags_list_images[]" value="<?php echo $tags_images_str;?>"></td>     
                <?php
                  if (isset($cInfo) && (is_object($cInfo)) && ($tags['tags_id'] == $cInfo->tags_id) ) {           
                ?>
                    <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' . $cInfo->tags_id .$sort_str);?>'"><?php echo $tags['tags_name']; ?></td>
                <?php
                  }else{
                ?>
                    <td class="dataTableContent" onclick="document.location.href='<?php echo tep_href_link(FILENAME_TAGS, 'page=' . $_GET['page'] . '&cID=' . $tags['tags_id'].$sort_str);?>'"><?php echo $tags['tags_name']; ?></td>
                <?php
                  }
                ?> 
                <td class="dataTableContent" align="right"><?php echo '<a href="javascript:void(0);" onclick="show_tags_info(this, \''.$tags['tags_id'].'\', \''.$_SERVER["QUERY_STRING"].'\');">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';?>&nbsp;</td>
              </tr>
<?php
  }
?>
              </form>
              <tr>
                <td colspan="4"><table border="0" width="100%" cellspacing="0" cellpadding="2" style="padding-left: 2px;">
                  <tr>
                    <td class="smallText" valign="top"><?php echo $tags_split->display_count($tags_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_TAGS); ?></td>
                    <?php if(isset($_GET['sort'])&&$_GET['sort']){ ?>
                    <td class="smallText" align="right"><?php echo $tags_split->display_links($tags_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'],'sort='.$_GET['sort']); ?></td>
                    <?php }else{ ?>
                    <td class="smallText" align="right"><?php echo $tags_split->display_links($tags_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
                     <?php }?>
                  </tr>
                  <tr>
                    <td>
                     <select name="select_edit_tags" onchange="select_type_changed(this.value);">
                      <option value="0"><?php echo TEXT_TAGS_SELECT;?></option> 
                      <option value="3"><?php echo TEXT_TAGS_ASSOCIATE_SETTING;?></option> 
                      <option value="2"><?php echo TEXT_TAGS_ASSOCIATE_DELETE;?></option>
                      <option value="1"><?php echo TEXT_TAGS_DELETE;?></option> 
                     </select>
                    </td>
                    <td colspan="2" align="right"><?php echo '<a href="javascript:void(0);" onclick="create_tags_info(this);">' . tep_html_element_button(IMAGE_NEW_PROJECT) . '</a>'; ?> 
                    </td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table>
<?php
}
?>
</div>
</div></td>
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
