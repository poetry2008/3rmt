<?php
/*
   $Id$
 */
?>
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
  echo "<input type='button' onclick='save_once_pwd_checkbox()'
    value='".TEXT_FOOTER_CHECK_SAVE."'>";
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
$page_name = $_SERVER['PHP_SELF'];
if($_SESSION['last_page']!= $page_name){
    unset($_SESSION[$_SESSION['last_page']]);
    $_SESSION['last_page'] = $page_name;
}

//for sql_log
$logNumber = 0;
tep_db_query('select * from cache');
$testArray = array();
//end for sql_log

// 显示SQL执行记录
if (STORE_DB_TRANSACTIONS == 'true' ) {?>
  <div id="debug_info">
    <pre>
    <?php 
    $i=0;
    $j=0;
    if(isset($logger)){
      foreach ($logger->queries as $qk => $qv) {
        if($logger->times[$qk]>1){
          $j++;
        echo '[' . $logger->times[$qk] . ']' . $qk . "\t=>\t" . $qv."\n";
        }
        $i+=$logger->times[$qk];
      }
    }
  
  echo "<<<<<".$j.">>>>><br>";
  echo "<<<<<".$i.">>>>>";
  print_r($_SESSION);
  ?>
    <?php //print_r($logger->times);?>
    </pre>
    </div>
    <?php
    /*
    $query = tep_db_query("show variables like '%timeout'");
while($value = tep_db_fetch_array($query)){
    var_dump($value);
    }
    */
    ?>
    <?php }?>
