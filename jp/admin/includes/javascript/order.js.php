<?php include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_ORDERS)?>
<?php //浏览器窗口缩放时执行的函数?>
function resizepage(){
  if($(".note_head").val()== ""&&$("#orders_list_table").width()< 714){
    $(".box_warp").css('height',$('.compatible').height());
  }
}
window.onresize = resizepage;
<?php //提交表单?>
function check_list_order_submit() {
  if (submit_confirm()) {
    confrim_list_mail_title();
  }
}

<?php //等待元素隐藏?> 
function read_time(){
  $("#wait").hide();
}

<?php //以当前时间为付款日?>
function q_3_2(){
  if ($('#q_3_1').attr('checked') == true){
    if ($('#q_3_2_m').val() == '' || $('#q_3_2_m').val() == '') {
      $('#q_3_2_m').val(new Date().getMonth()+1);
      $('#q_3_2_d').val(new Date().getDate());
    }
  }
}

<?php //以当前时间为付款日?>
function q_4_3(){
  if ($('#q_4_2').attr('checked') == true){
    if ($('#q_4_3_m').val() == '' || $('#q_4_3_m').val() == '') {
      $('#q_4_3_m').val(new Date().getMonth()+1);
      $('#q_4_3_d').val(new Date().getDate());
    }
  }
}

<?php //更新orders_comment_flag的值?>
function validate_comment(){
  var o_comment = $('textarea|[name=orders_comment]');
  if(o_comment.val()){
    return true;
  }else{
    o_comment_flag = $('input|[name=orders_comment_flag]');
    o_comment_flag.val('true');
    return true;
  }
}

<?php //显示手册全部内容?>
function manual_show(action){

  switch(action){

  case 'top':
    $("#manual_top_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_top_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'top\');"><u><?php echo ORDER_MANUAL_ALL_HIDE;?></u></a>');
    break;
  case 'top_categories':
    $("#manual_top_categories_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_top_categories_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'top_categories\');"><u><?php echo ORDER_MANUAL_ALL_HIDE;?></u></a>');
    break;
  case 'categories':
    $("#manual_categories_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_categories_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'categories\');"><u><?php echo ORDER_MANUAL_ALL_HIDE;?></u></a>');
    break;
  case 'categories_children':
    $("#manual_categories_children_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_categories_children_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'categories_children\');"><u><?php echo ORDER_MANUAL_ALL_HIDE;?></u></a>');
    break;
  case 'products':
    $("#manual_products_show").css({"height":"","overflow":"hidden"}); 
    $("#manual_products_all").html('<a href="javascript:void(0);" onclick="manual_hide(\'products\');"><u><?php echo ORDER_MANUAL_ALL_HIDE;?></u></a>');
    break;
  }   
}
<?php //显示手册部分内容?>
function manual_hide(action){

  switch(action){

  case 'top':
    $("#manual_top_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_top_all").html('<a href="javascript:void(0);" onclick="manual_show(\'top\');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a>');
    break;
  case 'top_categories':
    $("#manual_top_categories_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_top_categories_all").html('<a href="javascript:void(0);" onclick="manual_show(\'top_categories\');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a>');
    break;
  case 'categories':
    $("#manual_categories_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_categories_all").html('<a href="javascript:void(0);" onclick="manual_show(\'categories\');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a>');
    break;
  case 'categories_children':
    $("#manual_categories_children_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_categories_children_all").html('<a href="javascript:void(0);" onclick="manual_show(\'categories_children\');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a>');
    break;
  case 'products':
    $("#manual_products_show").css({"height":"200px","overflow":"hidden"});
    $("#manual_products_all").html('<a href="javascript:void(0);" onclick="manual_show(\'products\');"><u><?php echo ORDER_MANUAL_ALL_SHOW;?></u></a>');
    break;
  }  
}

<?php //确认邮件标题?>
function confrim_list_mail_title(){
  var _end = $("#mail_title_status").val();
  var o_id_list = ''; 
  var direct_single = false;
  if (document.sele_act.elements['chk[]']) {
    if (document.sele_act.elements['chk[]'].length == null) {
      if (document.sele_act.elements['chk[]'].checked == true) {
        o_id_list += document.sele_act.elements['chk[]'].value+','; 
      }
    } else {
      for (var i = 0; i < document.sele_act.elements['chk[]'].length; i++) {
        if (document.sele_act.elements['chk[]'][i].checked == true) {
          o_id_list += document.sele_act.elements['chk[]'][i].value+','; 
        }
      }
    }
  }
  if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
  }else{
    if(confirm("<?php echo TEXT_STATUS_MAIL_TITLE_CHANGED;?>")){
    } else {
      direct_single = true;
    }
  }
  
  $.ajax({
    type:"POST",
    data:"c_comments="+$('#c_comments').val()+'&o_id_list='+o_id_list+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end,
    async:false,
    url:'ajax_orders.php?action=check_order_list_variable_data',
    success: function(msg) {
      if (msg != '') {
        if (direct_single == false) {
          alert(msg); 
        } 
      } else {
        if (direct_single == false) {
          document.forms.sele_act.submit(); 
        }
      } 
    }
  }); 
}

<?php //选中/非选中网站?> 
function change_site(site_id,flag,site_list,param_url){  
  var ele = document.getElementById("site_"+site_id);
  $.ajax({
    dataType: 'text',
    type:"POST",
    data:'param_url='+param_url+'&flag='+flag+'&site_list='+site_list+'&site_id='+site_id,
    async:false, 
    url: 'ajax_orders.php?action=select_site',
    success: function(data) {
      if (data != '') {
        if (ele.className == 'site_filter_selected') {
          ele.className='';
        } else {
          ele.className='site_filter_selected';
        }
        window.location.href = data; 
     }
   }
  });
}

<?php //给订单加标识?> 
function change_read(oid,user){
  var orders_id = document.getElementById("oid_"+oid); 
  var orders_id_src = orders_id.src;
  var orders_id_src_array = new Array();
  var flag = 0;
  orders_id_src_array = orders_id_src.split("/"); 
  if(orders_id_src_array[orders_id_src_array.length-1] == 'green_right.gif'){
    flag = 1;
  }
  $.ajax({
    type: "POST",
    data: 'oid='+oid+'&user='+user+'&flag='+flag,
    beforeSend: function(){$('body').css('cursor','wait');$("#wait").show()},
    async:false,
    url: 'ajax_orders.php?action=read_flag',
    success: function(msg) {
      if(flag == 0){
        orders_id.src="images/icons/green_right.gif";
        orders_id.title=" <?php echo TEXT_FLAG_CHECKED;?> ";
        orders_id.alt="<?php echo TEXT_FLAG_CHECKED;?>";
      }else{
        orders_id.src="images/icons/gray_right.gif";
        orders_id.title=" <?php echo TEXT_FLAG_UNCHECK;?> ";
        orders_id.alt="<?php echo TEXT_FLAG_UNCHECK;?>";
      }
      $('body').css('cursor','');
      setTimeout('read_time()',500);
    }
  }); 
}

<?php //确认邮件标题?>
function confrim_mail_title(oid_info){
  var _end = $("#mail_title_status").val();
  
  var direct_single = false;
  if($("#confrim_mail_title_"+_end).val()==$("#mail_title").val()){
  }else{
    if(confirm("<?php echo TEXT_STATUS_MAIL_TITLE_CHANGED;?>")){
    } else {
      direct_single = true;
    }
  }
  
  $.ajax({
    type:"POST",
    data:"c_comments="+$('#c_comments').val()+'&o_id='+oid_info+'&c_title='+$('#mail_title').val()+'&c_status_id='+_end,
    async:false,
    url:'ajax_orders.php?action=check_order_variable_data',
    success: function(msg) {
      if (msg != '') {
        if (direct_single == false) {
          alert(msg); 
        } 
      } else {
        if (direct_single == false) {
          document.forms.sele_act.submit(); 
        }
      } 
    }
  }); 
}

<?php //删除订单指定状态?>
function del_confirm_payment_time(oid, status_id)
{
  $.ajax({
url: 'ajax_orders.php?action=getallpwd',
type: 'POST',
dataType: 'text',
data: 'current_page_name=<?php echo $_GET['other']?>', 
async : false,
success: function(data) {
var tmp_msg_arr = data.split('|||'); 
var pwd_list_array = tmp_msg_arr[1].split(',');
<?php
if ($ocertify->npermission == 31) {
?>
if (window.confirm('<?php echo NOTICE_DEL_CONFIRM_PAYEMENT_TIME;?>')) {
$.ajax({
type:"POST", 
url:"<?php echo tep_href_link('handle_payment_time.php')?>",
data:"oID="+oid+"&stid="+status_id, 
success:function(msg) {
alert('<?php echo NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS;?>'); 
window.location.href = window.location.href; 
window.location.reload; 
}
}); 
}
<?php
} else {
?>
if (tmp_msg_arr[0] == '0') {
if (window.confirm('<?php echo NOTICE_DEL_CONFIRM_PAYEMENT_TIME;?>')) {
$.ajax({
type:"POST", 
url:"<?php echo tep_href_link('handle_payment_time.php')?>",
data:"oID="+oid+"&stid="+status_id, 
success:function(msg) {
alert('<?php echo NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS;?>'); 
window.location.href = window.location.href; 
window.location.reload; 
}
}); 
}
} else {
 var input_pwd_str = window.prompt('<?php echo TEXT_INPUT_ONE_TIME_PASSWORD;?>', ''); 
 if (in_array(input_pwd_str, pwd_list_array)) {
   $.ajax({
     url: 'ajax_orders.php?action=record_pwd_log',   
     type: 'POST',
     dataType: 'text',
     data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(window.location.href),
     async: false,
     success: function(msg_info) {
      if (window.confirm('<?php echo NOTICE_DEL_CONFIRM_PAYEMENT_TIME;?>')) {
      $.ajax({
      type:"POST", 
      url:"<?php echo tep_href_link('handle_payment_time.php')?>",
      data:"oID="+oid+"&stid="+status_id+"&once_pwd="+input_pwd_str, 
      success:function(msg) {
      alert('<?php echo NOTICE_DEL_CONFIRM_PAYMENT_TIME_SUCCESS;?>'); 
      window.location.href = window.location.href; 
      window.location.reload; 
      }
      }); 
      }
     }
   }); 
 } else {
   alert("<?php echo TEXT_INPUT_PASSWORD_ERROR;?>"); 
 }
}
<?php
}
?>
}
});
}

<?php //删除订单?>
function confirm_del_order_info()
{
<?php
if ($ocertify->npermission == 31) {
?>
  document.forms.orders.submit();
<?php
} else {
?>
  $.ajax({
     url: 'ajax_orders.php?action=getallpwd',
     type: 'POST',
     dataType: 'text',
     data: 'current_page_name=<?php echo $_GET['other']?>', 
     async : false,
     success: function(data) {
       var tmp_msg_arr = data.split('|||'); 
       var pwd_list_array = tmp_msg_arr[1].split(',');
       if (tmp_msg_arr[0] == '0') {
         document.forms.orders.submit();
       } else {
         var input_pwd_str = window.prompt('<?php echo TEXT_INPUT_ONE_TIME_PASSWORD;?>', ''); 
         if (in_array(input_pwd_str, pwd_list_array)) {
           $.ajax({
             url: 'ajax_orders.php?action=record_pwd_log',   
             type: 'POST',
             dataType: 'text',
             data: 'current_pwd='+input_pwd_str+'&url_redirect_str='+encodeURIComponent(document.forms.orders.action),
             async: false,
             success: function(msg_info) {
               document.forms.orders.submit();
             }
           }); 
         } else {
           alert("<?php echo TEXT_INPUT_PASSWORD_ERROR;?>"); 
         }
       }
     }
   });
<?php
}
?>
}
<?php
$__orders_status_query = tep_db_query("
    select orders_status_id 
    from " . TABLE_ORDERS_STATUS . " 
    where language_id = " . $languages_id . " 
    order by orders_status_id");
$__orders_status_ids   = array();
while($__orders_status = tep_db_fetch_array($__orders_status_query)){
  $__orders_status_ids[] = $__orders_status['orders_status_id'];
}
if(join(',', $__orders_status_ids)!=''){
  $select_query = tep_db_query("
      select 
      orders_status_id,
      nomail
      from ".TABLE_ORDERS_STATUS."
      where language_id = " . $languages_id . " 
      and orders_status_id IN (".join(',', $__orders_status_ids).")");

  while($select_result = tep_db_fetch_array($select_query)){
    $osid = $select_result['orders_status_id'];

    //获取对应的邮件模板
    $mail_templates_query = tep_db_query("select site_id,title,contents from ". TABLE_MAIL_TEMPLATES ." where flag='ORDERS_STATUS_MAIL_TEMPLATES_".$osid."' and site_id='0'");
    $mail_templates_array = tep_db_fetch_array($mail_templates_query);

    $mt[$osid][$mail_templates_array['site_id']?$mail_templates_array['site_id']:0] = $mail_templates_array['contents'];
    $mo[$osid][$mail_templates_array['site_id']?$mail_templates_array['site_id']:0] = $mail_templates_array['title'];
    $nomail[$osid] = $select_result['nomail'];
  }
}

// 输出订单邮件
foreach ($mo as $oskey => $value){
  echo 'window.status_title['.$oskey.'] = new Array();'."\n";
  foreach ($value as $sitekey => $svalue) {
    echo 'window.status_title['.$oskey.']['.$sitekey.'] = "' . str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'),$svalue) . '";' . "\n";
  }
}

//content
foreach ($mt as $oskey => $value){
  echo 'window.status_text['.$oskey.'] = new Array();'."\n";
  foreach ($value as $sitekey => $svalue) {
    echo 'window.status_text['.$oskey.']['.$sitekey.'] = "' . str_replace(array("\r\n","\r","\n"), array('\n', '\n', '\n'),$svalue) . '";' . "\n";
  }
}

//no mail
echo 'var nomail = new Array();'."\n";
foreach ($nomail as $oskey => $value){
  echo 'nomail['.$oskey.'] = "' . $value . '";' . "\n";
}
?>
var o_action = <?php echo $_GET['o_action']?>;
if (o_action == '1') {
  $(function() {
    left_show_height = $('#orders_list_table').height();
    right_show_height = $('#rightinfo').height();
    if (right_show_height <= left_show_height) {
      $('#rightinfo').css('height', left_show_height);  
      }
    });
  function showRightInfo() {
    left_show_height = $('#orders_list_table').height();
    $('#rightinfo').css('height', left_show_height);  
  }
  $(window).resize(function() {
    showRightInfo();
  });
}
