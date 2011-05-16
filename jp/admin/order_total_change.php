<?php
require('includes/application_top.php');
//  $_GET['y']=2010;
/*
   set_time_limit(0);
   if($_GET['y'] == '2007') {
   $query = tep_db_query("select * from orders where date_purchased>'2007-1-1 00:00:00' and date_purchased<'2008-1-1 00:00:00'");
   } else if ($_GET['y'] == '2008'){
   $query = tep_db_query("select * from orders where date_purchased>'2008-1-1 00:00:00' and date_purchased<'2009-1-1 00:00:00'");
   } else if ($_GET['y'] == '2009'){
   $query = tep_db_query("select * from orders where date_purchased>'2009-1-1 00:00:00' and date_purchased<'2010-1-1 00:00:00'");
   } else if ($_GET['y'] == '2010'){
   $query = tep_db_query("select * from orders where date_purchased>'2010-1-1 00:00:00' and date_purchased<'2011-1-1 00:00:00'");
   } else if ($_GET['y'] == '2011'){
   $query = tep_db_query("select * from orders where date_purchased>'2011-1-1 00:00:00' and date_purchased<'2012-1-1 00:00:00'");
   } else {
   exit('no parameter');
   }

   while($o = tep_db_fetch_array($query)) {
   }
 */
$right_order_sql = "select count(*) cnt from orders_temp where isreg = 1";
$error_order_sql = "select count(*) cnt from orders_temp where isreg = 0";
$unknow_order_sql = "select count(*) cnt from orders_temp where suborder <> 3";

$r_order_query = tep_db_query($right_order_sql);
if($r_order_row = tep_db_fetch_array($r_order_query)){
  $right_count = $r_order_row['cnt'];
}

$e_order_query = tep_db_query($error_order_sql);
if($e_order_row = tep_db_fetch_array($e_order_query)){
  $error_count = $e_order_row['cnt'];
}

$u_order_query = tep_db_query($unknow_order_sql);
if($u_order_row = tep_db_fetch_array($u_order_query)){
  $unknow_count = $u_order_row['cnt'];
}
if(isset($_GET['isreg'])){
  $show_orders_query = tep_db_query("select * from orders_temp where isreg =
      '".$_GET['isreg']."' limit 0 , 30 ");
}
?>
<script src="includes/javascript/jquery.js" language="javascript"></script>
<script language="javascript">
function explode(inputstring, separators, includeEmpties) {

  inputstring = new String(inputstring);
  separators = new String(separators);

  if(separators == "undefined") {
    separators = " :;";
  }

  fixedExplode = new Array(1);
  currentElement = "";
  count = 0;

  for(x=0; x < inputstring.length; x++) {
    str = inputstring.charAt(x);
    if(separators.indexOf(str) != -1) {
      if ( ( (includeEmpties <= 0) || (includeEmpties == false)) && (currentElement == "")) {
      }
      else {
        fixedExplode[count] = currentElement;
        count++;
        currentElement = "";
      }
    }
    else {
      currentElement += str;
    }
  }

  if (( ! (includeEmpties <= 0) && (includeEmpties != false)) || (currentElement != "")) {
    fixedExplode[count] = currentElement;
  }
  return fixedExplode;
}

function save_change(o_id){
  total = $('input[name=total_'+o_id+']').val();
  sub = $('input[name=sub_'+o_id+']').val();
  point = $('input[name=point_'+o_id+']').val();
  $.ajax({
url: 'ajax_order_change_total.php?action=save_change',
data: 'o_id='+o_id+'&total='+total+'&sub='+sub+'&point='+point,
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
data_arr = explode(data,',');
$('input[name=total_'+o_id+']').val(data_arr[0]);
$('input[name=sub_'+o_id+']').val(data_arr[1]);
$('input[name=point_'+o_id+']').val(data_arr[2]);
$('input[name=suborder_'+o_id+']').val(data_arr[3]);
$('input[name=isreg_'+o_id+']').val(data_arr[4]);
$('input[name=type_'+o_id+']').val(data_arr[5]);
}
});
}
function reset_db(){
  $.ajax({
url: 'ajax_order_change_total.php?action=reset_db',
type: 'POST',
dataType: 'text',
async : false,
success: function(data) {
  alert(data);
}
});
}
</script>
<div>
<input type="button" value="reset_db" onclick="reset_db()">
</div>
<?php
$i=0;
if($show_orders_query){
while($orders_info = tep_db_fetch_array($show_orders_query)){
  if($i==0){
    echo "<table>";
    echo "<tr>";
    echo "<td>order_id</td>";
    echo "<td>order_total</td>";
    echo "<td>order_subtotal</td>";
    echo "<td>order_point</td>";
    echo "<td>order_suborder</td>";
    echo "<td>order_isreg</td>";
    echo "<td>order_type</td>";
    echo "<td>order_option</td>";
    echo "</tr>";
  }
  $i++;
  echo "<tr>";
  echo "<td>".$orders_info['id']."</td>";

  echo "<td><input size='5' type='text' name='total_".$orders_info['id'].
    "' value='".$orders_info['total']."' ></td>";

  echo "<td><input size='5' type='text' name='sub_".$orders_info['id'].
    "' value='".$orders_info['sub']."' ></td>";

  echo "<td><input size='5' type='text' name='point_".$orders_info['id'].
    "' value='".$orders_info['point']."' ></td>";


  echo "<td><input size='5' type='text' readonly='readonly' name='suborder_".$orders_info['id'].
    "' value='".$orders_info['suborder']."'  ></td>";

  echo "<td><input size='5' type='text' readonly='readonly' name='isreg_".$orders_info['id'].
    "' value='".$orders_info['isreg']."' ></td>";

  echo "<td><input size='5' type='text' readonly='readonly' name='type_".$orders_info['id'].
    "' value='".$orders_info['type']."' ></td>";

  echo "<td><input type='button' value ='save_change'
    onclick='save_change(\"".$orders_info['id']."\")'</td>";
  echo "</tr>";
}
if($i>0){
  echo "</table>";
}
}
?>
<a href="order_total_change.php?isreg=1">正しい注文書が（<?php echo 
$right_count;?>）つある</a>
<br>
<a href="order_total_change.php?isreg=0">正しくない注文書が（<?php echo
$error_count;?>）つある</a>



<?php

/*
   echo '小計と合計が違う値<br>';
   echo '<hr>';
   echo $red2."<br>";
   echo '注文した商品が存在してません</br>';
   echo '<hr>';
   echo $del."<br>";
 */
