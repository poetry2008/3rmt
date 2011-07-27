<?php
/*
   $Id$
 */
if ((strpos($_SERVER['PHP_SELF'], 'history.php') === false)  && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_oroshi.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'list_display.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_orders.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_new_orders.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'categories.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'micro_log.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'products_tags.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'categories_admin.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'pw_manager_log.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_dougyousya.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'customers_products.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'pw_manager.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'edit_new_orders2.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'telecom_unknow.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'cleate_list.php') === false) && 
    (strpos($_SERVER['PHP_SELF'], 'orders.php') === false)) {
?>
<script type="text/javascript" src="includes/javascript/jquery.js"></script>
<?php }?>
<script type="text/javascript">
function redirect_new_url(new_object)
{
  var url_str = $(new_object).parent().attr('href');
  window.location.href = url_str;
}
</script>
<?php
if($_SESSION['user_permission'] == 15 ){
  ?>
  <div >
  <?php 
    $sql_check = "select * from ".TABLE_PWD_CHECK." where
    page_name='".$_SERVER['PHP_SELF']."'";
    $query_check = tep_db_query($sql_check);
    $arr_check = array();
    while($row_check = tep_db_fetch_array($query_check)){
      $arr_check[] = $row_check['check_value'];
    }
    if(empty($arr_check)){
  ?>
  <input type='checkbox' name='pwd_check' value='staff'>Staff&nbsp;
  <input type='checkbox' name='pwd_check' value='chief'>Chief&nbsp;
  <input type='checkbox' name='pwd_check' value='admin' checked="checked">Admin&nbsp;
  <input type='checkbox' name='pwd_check' value='onetime'><?php echo 
    TEXT_FOOTER_ONE_TIME;?>&nbsp;
  <input type='button' onclick="save_once_pwd_checkbox()" value="<?php echo
    TEXT_FOOTER_CHECK_SAVE;?>">
  <?php }else{ 
    if(in_array('staff',$arr_check)){
      echo "<input type='checkbox' name='pwd_check' value='staff'
        checked='checked'>Staff&nbsp;";
    }else{
      echo "<input type='checkbox' name='pwd_check' value='staff'
        >Staff&nbsp;";
    }
    if(in_array('chief',$arr_check)){
      echo "<input type='checkbox' name='pwd_check' value='chief'
        checked='checked'>Chief&nbsp;";
    }else{
      echo "<input type='checkbox' name='pwd_check' value='chief'
        >Chief&nbsp;";
    }
    if(in_array('admin',$arr_check)){
      echo "<input type='checkbox' name='pwd_check' value='admin'
        checked='checked'>Admin&nbsp;";
    }else{
      echo "<input type='checkbox' name='pwd_check' value='admin'
        >Admin&nbsp;";
    }
    if(in_array('onetime',$arr_check)){
      echo "<input type='checkbox' name='pwd_check' value='onetime'
        checked='checked'>".TEXT_FOOTER_ONE_TIME."&nbsp;";
    }else{
      echo "<input type='checkbox' name='pwd_check' value='onetime'
        >".TEXT_FOOTER_ONE_TIME."&nbsp;";
    }
  echo "<input type='button' onclick='save_once_pwd_checkbox()' value='SAVE'>";
  }
  ?>
  </div>
  <script language='javascript' >
    function save_once_pwd_checkbox(){
      var check_str = '';
      $("input|[name=pwd_check]").each(function(){
          if($(this).attr('checked')){
            check_str += $(this).val()+',';
          }
      });
      check_str = check_str.substring(0, check_str.lastIndexOf(','));
      $.ajax({
        url: 'ajax_orders.php?action=pwd_check_save',
        data: 'check_str='+check_str+'&page_name=<?php echo $_SERVER['PHP_SELF'];?>',
        type: 'POST',
        dataType: 'text',
        async : false,
        success: function(data) {
          if(data == "noall"){
            alert("チェックがありません。チェックを入れてください");
          }else if(data == "noadmin"){
            alert("Adminのチェックを入れてください");
          }else if(data == "true"){
            alert("保存成功");
          }else{
            alert("エラー");
          }
        }
      });
    }
  </script>
    <?
}

//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log

// 显示SQL执行记录
if (STORE_DB_TRANSACTIONS == 'true') {?>
  <div id="debug_info">
    <pre>
    <?php if(isset($logger)){
      foreach ($logger->queries as $qk => $qv) {
        echo '[' . $logger->times[$qk] . ']' . $qk . "\t=>\t" . $qv."\n";
      }
    }
  print_r($_SESSION);
  ?>
    <?php //print_r($logger->times);?>
    </pre>
    </div>
    <?php }?>
