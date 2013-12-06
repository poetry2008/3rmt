<?php include(DIR_FS_ADMIN.DIR_WS_LANGUAGES.'/'.$language.'/'.FILENAME_ORDERS)?>
<?php //浏览器窗口缩放时执行的函数?>
function resizepage(){
  if($(".note_head").val()== ""&&$("#orders_list_table").width()< 714){
    $(".box_warp").css('height',$('.compatible').height());
  }
}
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
