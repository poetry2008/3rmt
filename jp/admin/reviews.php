<?php
/*
  $Id$
*/

  require('includes/application_top.php');
  require(DIR_FS_ADMIN . 'classes/notice_box.php');
  $sites_id=tep_db_query("SELECT site_permission,permission FROM `permissions` WHERE `userid`= '".$ocertify->auth_user."' limit 0,1");
  while($userslist= tep_db_fetch_array($sites_id)){
    $site_arr = $userslist['site_permission']; 
  }
  $site_permission = editPermission($site_arr, $_GET['site_id'],true);
  if(!$site_permission){
    $str_disabled = ' disabled="disabled" ';
  }else{
    $str_disabled = '';
  }

  if (isset($_GET['site_id'])&&$_GET['site_id']!='') {
    $sql_site_where = ' site_id in ('.str_replace('-', ',', $_GET['site_id']).')'; 
  } else {
    $sql_site_where = ' site_id in ('.tep_get_setting_site_info(FILENAME_REVIEWS).')'; 
  }
  if(isset($_GET['site_id'])&&$_GET['site_id']==''){
    $_GET['site_id'] = str_replace(',','-',tep_get_setting_site_info(FILENAME_REVIEWS));
  }
  

  if (isset($_GET['action']) && $_GET['action']) {
    switch ($_GET['action']) {
/*------------------------------------
 case 'new_preview'  添加评论
 case 'setflag'      设置标志
 case 'update'       更新评论 
 case 'deleteconfirm' 确认删除评论
 -----------------------------------*/
      case 'new_preview':
        $sql_array = array(
          'reviews_id' => 'null',
          'products_id' => $_POST['products_id'],
          'customers_id' => '0',
          'customers_name' => $_POST['customers_name'] ? $_POST['customers_name'] : TEXT_NO_NAME,
          'reviews_rating' => $_POST['reviews_rating'],
          'date_added' => $_POST['year'].'-'.$_POST['m'].'-'.$_POST['d'].' '.$_POST['h'].':'.$_POST['i'].':'.$_POST['s'],
          'last_modified' => '',
          'reviews_read' => '0',
          'site_id' => $_POST['insert_site_id'],
          'reviews_status' => $_POST['reviews_status'],
        );
        tep_db_perform(TABLE_REVIEWS, $sql_array);
        $insert_id = tep_db_insert_id();
        $sql_description_array = array(
          'reviews_id' => $insert_id,
          'languages_id' => '4',
          'reviews_text' => $_POST['reviews_text']
        );
        tep_db_perform(TABLE_REVIEWS_DESCRIPTION, $sql_description_array);
        tep_redirect(tep_href_link(FILENAME_CATEGORIES, 'cPath='.$_POST['cPath'].(trim($_POST['search'])?'&search='.trim($_POST['search']):'')));
        break;
      case 'setflag':
        $site_id = isset($_GET['action_sid']) ? $_GET['action_sid'] :0;
        forward401Unless(editPermission($site_arr, $site_id,true));
        if ( ($_GET['flag'] == '0') || ($_GET['flag'] == '1') ) {
          if ($_GET['pID']) {
            $pID = (int)$_GET['pID'];
            $flag = (int)$_GET['flag'];
            tep_db_query("UPDATE ".TABLE_REVIEWS." 
                SET reviews_status = '".$flag."',
                user_update='".$_SESSION['user_name']."',
                last_modified=now()
                WHERE reviews_id = '".$pID."'");
          }
        }
        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' .  $_GET['page'].'&site_id='.$_GET['site_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'')));
        break;
      case 'update':
        if($_POST['hidden_select'] == $_POST['hidden_products_name']){
             $add_products = 0;  
        }else{
             $add_products = 1;  
        } 
        $reviews_id     = tep_db_prepare_input($_GET['rID']);
        $site_id=tep_get_rev_sid_by_id($reviews_id);
        if(!$site_id['site_id']){
          $site_id['site_id'] = $_GET['action_sid'];
        }
        if($_POST['action_type']=='insert'){
          $site_id['site_id'] = $_POST['insert_site_id'];
        }
        forward401Unless(editPermission($site_arr, $site_id['site_id'],true));
        $reviews_rating = tep_db_prepare_input($_POST['reviews_rating']);
        $last_modified  = tep_db_prepare_input($_POST['last_modified']);
        $reviews_text   = tep_db_prepare_input($_POST['reviews_text']);
        $reviews_status = tep_db_prepare_input($_POST['reviews_status']);
        $date_added     = $_POST['year'].'-'.$_POST['m'].'-'.$_POST['d'].' '.$_POST['h'].':'.$_POST['i'].':00';
        $customers_name = $_POST['customers_name'] ? $_POST['customers_name'] : TEXT_NO_NAME;
        $add_product_products_id = $_POST['add_product_products_id'];
       if($add_products == 1){
        if(isset($_POST['action_type'])&&$_POST['action_type']=='insert'){
          $sql_array = array(
            'reviews_id' => 'null',
            'products_id' => $add_product_products_id,
            'customers_id' => '0',
            'customers_name' => $_POST['customers_name'] ? $_POST['customers_name'] : TEXT_NO_NAME,
            'reviews_rating' => $_POST['reviews_rating'],
            'date_added' => $_POST['year'].'-'.$_POST['m'].'-'.$_POST['d'].' '.$_POST['h'].':'.$_POST['i'].':'.$_POST['s'],
            'last_modified' => 'now()',
            'reviews_read' => '0',
            'site_id' => $_POST['insert_site_id'],
            'reviews_status' => $_POST['reviews_status'],
            'user_added'  => $_SESSION['user_name'],
            'user_update' => $_SESSION['user_name'],
          );
          tep_db_perform(TABLE_REVIEWS, $sql_array);
          $insert_id = tep_db_insert_id();
          $sql_description_array = array(
            'reviews_id' => $insert_id,
            'languages_id' => '4',
            'reviews_text' => $_POST['reviews_text']
          );
          tep_db_perform(TABLE_REVIEWS_DESCRIPTION, $sql_description_array);
          tep_db_query(" delete from " . TABLE_REVIEWS . " where reviews_id = '" . tep_db_input($reviews_id) . "'");
          tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . tep_db_input($reviews_id) . "'");
        }else{
          tep_db_query("
            update " . TABLE_REVIEWS . " 
            set products_id = '".$add_product_products_id."',
                reviews_rating = '" . tep_db_input($reviews_rating) . "', 
                last_modified = now(), 
	        user_update = '".$_SESSION['user_name']."',
                reviews_status = '".$reviews_status."',
                date_added = '".$date_added."',
                customers_name = '".$customers_name."'
            where reviews_id = '" . tep_db_input($reviews_id) . "'");
        
          tep_db_query("
              update " . TABLE_REVIEWS_DESCRIPTION . " 
              set reviews_text = '" . tep_db_input($reviews_text) . "' 
              where reviews_id = '" . tep_db_input($reviews_id) . "'");
        }
        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] .  '&site_id='.$_POST['site_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'')));
        break;
       } 
        tep_db_query("
            update " . TABLE_REVIEWS . " 
            set reviews_rating = '" . tep_db_input($reviews_rating) . "', 
                last_modified = now(), 
	        user_update = '".$_SESSION['user_name']."',
                reviews_status = '".$reviews_status."',
                date_added = '".$date_added."',
                customers_name = '".$customers_name."'
            where reviews_id = '" . tep_db_input($reviews_id) . "'");
        
        tep_db_query("
            update " . TABLE_REVIEWS_DESCRIPTION . " 
            set reviews_text = '" . tep_db_input($reviews_text) . "' 
            where reviews_id = '" . tep_db_input($reviews_id) . "'");

        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'] .  '&site_id='.$_POST['site_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'')));
        break;
      case 'deleteconfirm':
        if($ocertify->npermission >= 15){
        if (!empty($_POST['review_id'])) {
                   foreach ($_POST['review_id'] as $ge_key => $ge_value) {
                   tep_db_query(" delete from " . TABLE_REVIEWS . " where reviews_id = '" . $ge_value . "'");
                   tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $ge_value . "'");
                   }
                   tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'')));
        }
        $reviews_id = tep_db_prepare_input($_GET['rID']);
        tep_db_query(" delete from " . TABLE_REVIEWS . " where reviews_id = '" . tep_db_input($reviews_id) . "'");
        tep_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . tep_db_input($reviews_id) . "'");
        }
        tep_redirect(tep_href_link(FILENAME_REVIEWS, 'page=' . $_GET['page'].(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'')));
        break;
    }
  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo HEADING_TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/jquery.autocomplete.css">
<script language="javascript" src="js2php.php?path=includes&name=general&type=js"></script>
<script language="javascript" src="includes/javascript/jquery_include.js"></script>
<script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
<?php require('includes/javascript/show_site.js.php');?>
<script language="javascript" >
    $(function() {
       function format(group) {
           return group.name;
       }
       $("#keyword").autocomplete('ajax_create_order.php?action=search_product_name&site_id=<?php echo $_GET['site_id'];?>', {
                      multipleSeparator: '',
                      dataType: "json",
                      parse: function(data) {
                      return $.map(data, function(row) {
                      return {
                             data: row,
                             value: row.name,
                             result: row.name
                             }
                      });
                      },
                      formatItem: function(item) {
                          return format(item);
                        }
                      }).result(function(e, item) {
                      });
    });
    function all_select_review(review_str){
        var check_flag = document.del_review.all_check.checked;
          if (document.del_review.elements[review_str]) {
           if (document.del_review.elements[review_str].length == null){
             if (check_flag == true) {
                  document.del_review.elements[review_str].checked = true;
             } else {
                  document.del_review.elements[review_str].checked = false;
             }
            } else {
                for (i = 0; i < document.del_review.elements[review_str].length; i++){
                  if(!document.del_review.elements[review_str][i].disabled){
                   if (check_flag == true) {
                       document.del_review.elements[review_str][i].checked = true;
                    } else {
                       document.del_review.elements[review_str][i].checked = false;
                    }
                  }
                }
             }
           }
         }
   function delete_select_review(review_str, c_permission){
      sel_num = 0;
      if (document.del_review.elements[review_str].length == null) {
          if (document.del_review.elements[review_str].checked == true) {
              sel_num = 1;
          }
       } else {
          for (i = 0; i < document.del_review.elements[review_str].length; i++) {
                  if (document.del_review.elements[review_str][i].checked == true) {
                      sel_num = 1;
                      break;
                   }
           }
       }
       if (sel_num == 1) {
           if (confirm('<?php echo TEXT_DEL_REVIEW;?>')) {
             if (c_permission == 31) {
               document.forms.del_review.submit(); 
             } else {
               $.ajax({
                 url: 'ajax_orders.php?action=getallpwd',   
                 type: 'POST',
                 dataType: 'text',
                 async: false,
                 success: function(msg) {
                   pwd_list_array = msg.split(','); 
                   var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                   if (in_array(input_pwd_str, pwd_list_array)) {
                     document.forms.del_review.submit(); 
                   } else {
                     alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                     document.getElementsByName('reviews_action')[0].value = 0; 
                   }
                 }
               });
             }
           } else {
              document.getElementsByName('reviews_action')[0].value = 0; 
           }
        } else {
            document.getElementsByName('reviews_action')[0].value = 0; 
            alert('<?php echo TEXT_REVIEW_MUST_SELECT;?>'); 
           }
    }
    function change_hidden_select(ele){
       set_rpid(ele);
       document.getElementById("hidden_select").value=ele.options[ele.selectedIndex].value;
    }
    function change_review_products_id(ele,page,rID,site_id){
      set_rcid(ele);
      var review_products_id_info = document.getElementById('review_products_id').value;
      var site_id_name = document.getElementById('site_id').value;
      site_id = site_id_name;
      var show_site_id = 0;
      if(document.getElementById('add_site_id')){
        show_site_id = document.getElementById('add_site_id').value;
      }
      refresh(rID,page,review_products_id_info,site_id,show_site_id);
     }
    function refresh(rID,page,review_products_id_info,site_id,show_site_id){
         var product_name = document.getElementById('keyword').value;
         var con_cname = $('#customers_name').val();
         var con_text = $('#reviews_text').val();
         set_default_value();
         $.ajax({
               url: "ajax.php?&action=edit_reviews&validate=true",
               data: {rID:rID,page:page,review_products_id_info:review_products_id_info,site_id:site_id,product_name:product_name,add_site_id:show_site_id},
               async:false,
               success: function(data){
                  $("#show_text_reviews").html(data);
                }
            });
        $('#customers_name').val(con_cname);
        $('#reviews_text').val(con_text);
     
    }
    function check_review_submit(rID,page){
          var show_site_id = 0;
          if(document.getElementById('add_site_id')){
            show_site_id = document.getElementById('add_site_id').value;
          }
          var site_id = document.getElementById('site_id').value;
          var add_id = document.getElementById('add_product_products_id').value;
          var customers_name = document.getElementById('customers_name').value;
          var product_name = document.getElementById('keyword').value;
          var con_cname = $('#customers_name').val();
          var con_text = $('#reviews_text').val();
          set_default_value();
          $.ajax({
               url: "ajax.php?&action=edit_reviews&validate=true",
               data: {rID:rID,page:page,site_id:site_id,add_id:add_id,customers_name:customers_name,product_name:product_name,add_site_id:show_site_id},
               async:false,
               success: function(data){
                  $("#show_text_reviews").html(data);
                }
            });
         $('#customers_name').val(con_cname);
         $('#reviews_text').val(con_text);

         if (document.getElementById('add_product_products_id').value != 0) {
           if (document.getElementById('reviews_text').value.length < <?php echo REVIEW_TEXT_MIN_LENGTH;?>) {
             alert("<?php echo REVIEWS_NOTICE_TOTALNUM_ERROR;?>");
           } else {
             <?php
             if ($ocertify->npermission == 31) {
             ?>
               document.forms.review.submit();
             <?php
             } else {
             ?>
              $.ajax({
                url: 'ajax_orders.php?action=getallpwd',   
                type: 'POST',
                dataType: 'text',
                async: false,
                success: function(msg) {
                  pwd_list_array = msg.split(','); 
                  var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
                  if (in_array(input_pwd_str, pwd_list_array)) {
                    document.forms.review.submit();
                  } else {
                    alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
                  }
                }
              });
             <?php
             }
             ?>
           }
         }
    }
    function check_review(){
        if (document.getElementById('reviews_text').value.length < <?php echo REVIEW_TEXT_MIN_LENGTH;?>) {
            alert("<?php echo REVIEWS_NOTICE_TOTALNUM_ERROR;?>");
            return false;
        } else {
            return true;
        }
    }
$(document).ready(function() {
  <?php //监听按键?> 
  $(document).keyup(function(event) {
    if (event.which == 27) {
      <?php //esc?> 
      if ($('#show_text_reviews').css('display') != 'none') {
        hidden_info_box(); 
      }
    }
     if (event.which == 13) {
           <?php //回车?>
        if ($('#show_text_reviews').css('display') != 'none') {
               $("#show_text_reviews").find('input:button').first().trigger("click");
            }
        }

     if (event.ctrlKey && event.which == 37) {
      <?php //Ctrl+方向左?> 
      if ($('#show_text_reviews').css('display') != 'none') {
        if ($("#option_prev")) {
          $("#option_prev").trigger("click");
        }
      } 
    }
    if (event.ctrlKey && event.which == 39) {
      <?php //Ctrl+方向右?> 
      if ($('#show_text_reviews').css('display') != 'none') {
        if ($("#option_next")) {
          $("#option_next").trigger("click");
        }
      } 
    }
  });    
});

function show_text_reviews(ele,page,rID,site_id,action_sid){
 var product_name = document.getElementById('keyword').value;
 $.ajax({
 url: 'ajax.php?&action=edit_reviews',
 data: {page:page,rID:rID,site_id:site_id,product_name:product_name,action_sid:action_sid} ,
 dataType: 'text',
 async : false,
 success: function(data){
  $("div#show_text_reviews").html(data);
ele = ele.parentNode;
head_top = $('.compatible_head').height();
box_warp_height = 0;
if(rID != 0){
if(document.documentElement.clientHeight < document.body.scrollHeight){
if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
if(ele.offsetTop < $('#show_text_reviews').height()){
offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
box_warp_height = offset-head_top;
}else{
if (((head_top+ele.offsetTop+$('#show_text_reviews').height()) > $('.box_warp').height())&&($('#show_text_reviews').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_text_reviews').height()+head_top;
} else {
offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
}
box_warp_height = offset-head_top;
}
}else{
  if (((head_top+ele.offsetTop+$('#show_text_reviews').height()) > $('.box_warp').height())&&($('#show_text_reviews').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
    offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_text_reviews').height()+head_top;
  } else {
    offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
    offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
  }
}
$('#show_text_reviews').css('top',offset);
}else{
  if((document.documentElement.clientHeight-ele.offsetTop) < ele.offsetTop){
    if (((head_top+ele.offsetTop+$('#show_text_reviews').height()) > $('.box_warp').height())&&($('#show_text_reviews').height()<ele.offsetTop+parseInt(head_top)-$("#show_text_list").position().top-1)) {
      offset = ele.offsetTop+$("#show_text_list").position().top-1-$('#show_text_reviews').height()+head_top;
    } else {
      offset = ele.offsetTop+$("#show_text_list").position().top+$(ele).height()+head_top;
      offset = offset + parseInt($('#show_text_list').attr('cellpadding'))+parseInt($('.compatible table').attr('cellpadding'));
    }
    box_warp_height = offset-head_top;
  }else{
    offset = ele.offsetTop+$("#show_text_list").position().top+ele.offsetHeight+head_top;
    box_warp_height = offset-head_top;
  }
  $('#show_text_reviews').css('top',offset);
}
}
box_warp_height = box_warp_height + $('#show_text_reviews').height();
if($('.show_left_menu').width()){
  leftset = $('.leftmenu').width()+$('.show_left_menu').width()+parseInt($('.leftmenu').css('padding-left'))+parseInt($('.show_left_menu').css('padding-right'))+parseInt($('#categories_right_td table').attr('cellpadding'));
}else{
  leftset = parseInt($('.content').attr('cellspacing'))+parseInt($('.content').attr('cellpadding'))*2+parseInt($('.columnLeft').attr('cellspacing'))*2+parseInt($('.columnLeft').attr('cellpadding'))*2+parseInt($('.compatible table').attr('cellpadding'));
} 
if(rID == 0){
  show_text_list = $('#show_text_list').offset();
$('#show_text_reviews').css('top',show_text_list.top);
}
$('#show_text_reviews').css('z-index','1');
$('#show_text_reviews').css('left',leftset);
$('#show_text_reviews').css('display', 'block');
word_count();
  }
  }); 
}
function word_count(){
      document.getElementById('count_box').innerHTML = document.getElementById('reviews_text').value.length;
}
function hidden_info_box(){
$('#show_text_reviews').css('display','none');
}
function set_rstatus(_this){
  $("#r_status").val(_this.value);
}
function set_rating(_this){
  $("#r_rating").val(_this.value);
}
function set_ryear(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_year").val(value);
}
function set_rmonth(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_month").val(value);
}
function set_rday(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_day").val(value);
}
function set_rhour(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_hour").val(value);
}
function set_rminute(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_minute").val(value);
}
function set_rcid(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_cid").val(value);
  $("#r_pid").val('0');
}
function set_rpid(_this){
  var index = _this.selectedIndex;
  var value = _this.options[index].value;
  $("#r_pid").val(value);
  if(value){
    $("#p_error").css('display','none');
  }
}
function set_default_value(){
  var df_status = $("#r_status").val();
  var df_rating = $("#r_rating").val();
  var df_year = $("#r_year").val();
  var df_m = $("#r_month").val();
  var df_d = $("#r_day").val();
  var df_h = $("#r_hour").val();
  var df_i = $("#r_minute").val();
  var df_cid = $("#r_cid").val();
  var df_pid = $("#r_pid").val();
  $.ajax({
    url: "ajax.php?&action=edit_reviews&default_value=save",
    data:{df_status:df_status,df_rating:df_rating,df_year:df_year,df_m:df_m,df_d:df_d,df_h:df_h,df_i:df_i,df_cid:df_cid,df_pid:df_pid},
    async:false,
    success: function(data){
    }
  });
}
<?php //选择动作?>
function review_change_action(r_value, r_str) {
  if (r_value == '1') {
    delete_select_review(r_str, '<?php echo $ocertify->npermission;?>');
  }
}
<?php //动作链接?>
function toggle_reviews_action(reviews_url_str) 
{
  <?php
    if ($ocertify->npermission == 31) {
  ?>
  window.location.href = reviews_url_str;  
  <?php
    } else {
  ?>
  $.ajax({
    url: 'ajax_orders.php?action=getallpwd',   
    type: 'POST',
    dataType: 'text',
    async: false,
    success: function(msg) {
      pwd_list_array = msg.split(','); 
      var input_pwd_str = window.prompt('<?php echo JS_TEXT_INPUT_ONETIME_PWD;?>', ''); 
      if (in_array(input_pwd_str, pwd_list_array)) {
        window.location.href = reviews_url_str;  
      } else {
        alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
      }
    }
  });
  <?php
    }
  ?>
}
</script>
<?php 
$href_url = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
$belong = str_replace('/admin/','',$_SERVER['REQUEST_URI']);
$belong = preg_replace('/\?XSID=[^&]+/','',$belong);
preg_match_all('/action=[^&]+/',$belong,$belong_temp_array);
if($belong_temp_array[0][0] != '' && $belong_temp_array[0][0] != 'action=delete'){
  preg_match_all('/rID=[^&]+/',$belong,$belong_array);
  if($belong_array[0][0] != ''){

    $belong = $href_url.'?'.$belong_array[0][0].'|||'.$belong_temp_array[0][0];
  }else{

    $belong = $href_url;
  }
}else{

  $belong = $href_url;
}
require("includes/note_js.php");
?>
<script language="javascript" src="includes/javascript/jquery.autocomplete.js"></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header -->
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof -->

<!-- body -->
<input type="hidden" id="show_info_id" value="show_text_reviews" name="show_info_id">
<div id="show_text_reviews" style="min-width: 550px; position: absolute; background: none repeat scroll 0% 0% rgb(255, 255, 0); width: 70%; display:none;"></div>
<table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation -->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof -->
    </table></td>
<!-- body_text -->
    <td width="100%" valign="top" id="categories_right_td"><div class="box_warp"><?php echo $notes;?><div class="compatible"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" height="40"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right">
            <?php 
            if(isset($_GET['site_id'])&&$_GET['site_id']!=''){
              $search_sid ='?site_id='.$_GET['site_id'];
            }else{
              $search_sid ='';
            }
            ?>
            <form method="GET" action="reviews.php<?php echo $search_sid;?>"> 
            <input type="text" value="<?php echo isset($_GET['product_name'])?trim($_GET['product_name']):'';?>" id="keyword" name="product_name" size="40">&nbsp;&nbsp;<input type="submit" value="<?php echo IMAGE_SEARCH;?>"> 
            <input type="hidden" name="site_id" value="<?php echo $_GET['site_id'];?>">
            </form>
            </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>
        <?php echo tep_show_site_filter(FILENAME_REVIEWS,false,array(0));?>
        <table id="show_text_list" border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top">
                   <?php
                    $review_table_params = array('width'=>'100%','cellpadding'=>'2','border'=>'0', 'cellspacing'=>'0');
                    $notice_box = new notice_box('','',$review_table_params);
                    $review_table_row = array();
                    $review_title_row = array();
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => '<input type="checkbox" name="all_check" onclick="all_select_review(\'review_id[]\');">' );
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_SITE);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent"', 'text' => TABLE_HEADING_PRODUCTS);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent" align="center"', 'text' => TABLE_HEADING_RATING);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent" align="center"', 'text' => TABLE_HEADING_DATE_ADDED);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent" align="center"', 'text' => TABLE_HEADING_STATUS);
                    $review_title_row[] = array('params' => 'class="dataTableHeadingContent" align="right"', 'text' => TABLE_HEADING_ACTION);
                    $review_table_row[] = array('params' => 'class="dataTableHeadingRow"', 'text' => $review_title_row);
    if(isset($_GET['product_name']) && $_GET['product_name']){
       $p_list_arr = array();
       $p_list_arr_site = array();
       if(isset($_GET['site_id'])&&$_GET['site_id']){
         $p_list_arr_site_sql = "select products_id,products_name from ".
           TABLE_PRODUCTS_DESCRIPTION." where 
           products_name like '%".trim($_GET['product_name'])."%' 
           and ".$sql_site_where;
         $p_list_arr_site_query = tep_db_query($p_list_arr_site_sql);
         while($p_list_arr_site_res = tep_db_fetch_array($p_list_arr_site_query)){
           $p_list_arr[] = $p_list_arr_site_res['products_id'];
           $p_list_arr_site[$p_list_arr_site_res['products_id']] =
           $p_list_arr_site_res['products_name'];
         }
       }
       if(isset($_GET['site_id'])&&$_GET['site_id']){
         $p_list_arr_sql = "SELECT products_id FROM ".TABLE_PRODUCTS_DESCRIPTION." 
           WHERE site_id = 0 
           and products_name like '%".trim($_GET['product_name'])."%'
           and products_id not in 
           (select products_id FROM ".TABLE_PRODUCTS_DESCRIPTION." 
            where ".$sql_site_where.")";
       }else{
         $p_list_arr_sql = "select products_id from ".
           TABLE_PRODUCTS_DESCRIPTION." where 
           products_name like '%".trim($_GET['product_name'])."%' 
           and site_id = 0";
       }
         $p_list_arr_query = tep_db_query($p_list_arr_sql);
         while($p_list_arr_res = tep_db_fetch_array($p_list_arr_query)){
           if(!in_array($p_list_arr_res['products_id'],$p_list_arr)){
             $p_list_arr[] = $p_list_arr_res['products_id'];
             $p_list_arr_site[$p_list_arr_res['products_id']] =
             $p_list_arr_res['products_name'];
           }
         }
         $where_str = ' and r.products_id in ('.implode(',',$p_list_arr).') ';
    }
    $reviews_query_raw = "
      select r.reviews_id, 
             r.products_id, 
             r.date_added, 
             r.last_modified, 
	     r.user_added,
	     r.user_update,
             r.site_id,
             r.reviews_rating, 
             r.reviews_status ,
             s.romaji,
             s.name as site_name
     from " . TABLE_REVIEWS . " r, ".TABLE_SITES." s
     where r.site_id = s.id
        and " . $sql_site_where . "".$where_str."
     order by date_added DESC";
    
    $reviews_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $reviews_query_raw, $reviews_query_numrows);
    $reviews_query = tep_db_query($reviews_query_raw);
    while ($reviews = tep_db_fetch_array($reviews_query)) {
      if ( ((!isset($_GET['rID']) || !$_GET['rID']) || ($_GET['rID'] == $reviews['reviews_id'])) && (!isset($rInfo) || !$rInfo) ) {
        $reviews_text_query = tep_db_query("
            select r.reviews_read, 
                   r.customers_name, 
                   r.site_id,
                   length(rd.reviews_text) as reviews_text_size 
            from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd 
            where r.reviews_id = '" . $reviews['reviews_id'] . "' 
              and r.reviews_id = rd.reviews_id");
        $reviews_text = tep_db_fetch_array($reviews_text_query);

        $products_image_query = tep_db_query("
            select products_image 
            from " . TABLE_PRODUCTS . " 
            where products_id = '" . $reviews['products_id'] . "'
        ");
        $products_image = tep_db_fetch_array($products_image_query);

        $products_name_query = tep_db_query("
            select products_name 
            from " . TABLE_PRODUCTS_DESCRIPTION . " 
            where products_id = '" . $reviews['products_id'] . "' 
              and site_id ='0'
              and language_id = '" . $languages_id . "'");
        $products_name = tep_db_fetch_array($products_name_query);

        $reviews_average_query = tep_db_query("
            select (avg(reviews_rating) / 5 * 100) as average_rating 
            from " . TABLE_REVIEWS . " 
            where products_id = '" . $reviews['products_id'] . "'
          ");
        $reviews_average = tep_db_fetch_array($reviews_average_query);

        $review_info = tep_array_merge($reviews_text, $reviews_average, $products_name);
        $rInfo_array = tep_array_merge($reviews, $review_info, $products_image);
        $rInfo = new objectInfo($rInfo_array);
      }

      $even = 'dataTableSecondRow';
      $odd  = 'dataTableRow';
      if (isset($nowColor) && $nowColor == $odd) {
        $nowColor = $even; 
      } else {
        $nowColor = $odd; 
      }
      $review_params = 'class="'.$nowColor.'" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\''.$nowColor.'\'"';
      $review_info = array();
      $site_array = explode(',',$site_arr);
      if(in_array($reviews['site_id'],$site_array)){
          $reviews_checkbox = '<input type="checkbox" name="review_id[]" value="'.$reviews['reviews_id'].'">';
      }else{
          $reviews_checkbox = '<input disabled="disabled" type="checkbox" name="review_id[]" value="'.$reviews['reviews_id'].'">';
      }
      $review_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => $reviews_checkbox 
      );
 
      $review_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   => ''.$reviews['romaji']
      );
      $review_info[] = array(
          'params' => 'class="dataTableContent"',
          'text'   =>
          tep_get_products_name($reviews['products_id'],$languages_id,$reviews['site_id'],true)
      );
      $action_sid_str = '&action_sid='.$reviews['site_id'];
      if ($reviews['reviews_status'] == '1') {
        $review_image = tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN) . '&nbsp;&nbsp;<a href="javascript:void(0);" onclick="toggle_reviews_action(\'' . tep_href_link(FILENAME_REVIEWS, 'action=setflag&flag=0'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'&page=' . (isset($_GET['page'])?$_GET['page']:'') . '&pID=' .  $reviews['reviews_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT) . '</a>';
      } else {
        $review_image = '<a href="javascript:void(0);" onclick="toggle_reviews_action(\'' . tep_href_link(FILENAME_REVIEWS, 'action=setflag&flag=1'.(isset($_GET['site_id'])?('&site_id='.$_GET['site_id']):'').'&page=' . (isset($_GET['page'])?$_GET['page']:'') . '&pID=' .  $reviews['reviews_id'].(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):'')) . '\');">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED);
      }
      $review_info[] = array(
          'params' => 'class="dataTableContent" align="center"',
          'text'   => $reviews['reviews_rating'] 
      );
       $review_info[] = array(
          'params' => 'class="dataTableContent" align="center"',
          'text'   =>  tep_date_short($reviews['date_added']) . ' ' .date('H:i:s', strtotime($reviews['date_added'])) 
      );
      $review_info[] = array(
          'params' => 'class="dataTableContent" align="center"',
          'text'   => ''.$review_image 
      );
      if(empty($_GET['site_id'])){ $_GET['site_id'] = ''; } 
      $review_date_info = (tep_not_null($reviews['last_modified']) && ($reviews['last_modified'] != '0000-00-00 00:00:00'))?$reviews['last_modified']:$reviews['date_added'];
      $review_info[] = array(
          'params' => 'class="dataTableContent" align="right"',
          'text'   => '<a href="javascript:void(0);"
          onclick="show_text_reviews(this,\''.$_GET['page'].'\',\''.$reviews['reviews_id'].'\',\''.$_GET['site_id'].'\',\''.$reviews['site_id'].'\')">'.
          tep_get_signal_pic_info($review_date_info).'</a>'
      );
    $review_table_row[] = array('params' => $review_params, 'text' => $review_info);
    }
    $review_form = tep_draw_form('del_review', FILENAME_REVIEWS, 'page='.$_GET['page'].'&site_id='.$_GET['site_id'].'&action=deleteconfirm'.(isset($_GET['product_name'])?('&product_name='.$_GET['product_name']):''));
    $notice_box->get_form($review_form);
    $notice_box->get_contents($review_table_row);
    $notice_box->get_eof(tep_eof_hidden());
    echo $notice_box->show_notice();
?>
            </table>
		<table border="0" width="100%" cellspacing="0" cellpadding="0" class="table_list_box">
                  <tr>
                    <td colspan="2">
                      <?php 
                      if($ocertify->npermission >= 15){
                      ?>
                      <select name="reviews_action" onchange="review_change_action(this.value, 'review_id[]');">
                        <option value="0"><?php echo TEXT_REVIEWS_SELECT_ACTION;?></option> 
                        <option value="1"><?php echo TEXT_REVIEWS_DELETE_ACTION;?></option> 
                      </select>
                    <?php }?> 
                    </td>
                  </tr>
                  <tr>
                    <td class="smallText" valign="top"><?php echo $reviews_split->display_count($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                    <td class="smallText" align="right">
					<div class="td_box"><?php echo $reviews_split->display_links($reviews_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], tep_get_all_get_params(array('page', 'rID'))); ?></div></td>
                  </tr>
                  <tr>
                    <td class="smallText" align="right" colspan="2">
                     <div class="td_button">   
                      <button type="button" onclick="show_text_reviews(this,'<?php echo $_GET['page']; ?>','0','<?php echo $_GET['site_id'];?>','')"><?php echo IMAGE_NEW_PROJECT;?></button>
                      </div>
                    </td>
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
