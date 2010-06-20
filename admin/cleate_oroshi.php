<?php
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');


$currencies = new currencies();
$cPath=$_POST['cpath'];
switch ($HTTP_GET_VARS['action']){

case 'edit_oroshi':
  $cpath = $HTTP_GET_VARS['cpath'];
  $orrshi_id = $HTTP_GET_VARS['id'];
  $name = $HTTP_GET_VARS['name'];
  
  $sql = 'update set_oroshi_names set oroshi_name = "'.$name.'"';
  $sql.=', parent_id = '.$cpath;
  $sql.=' where oroshi_id = '.$orrshi_id;
  tep_db_query($sql);

  break;
case 'set_oroshi':
  $updata=$_POST['up_oroshi'];
  $res=tep_db_query("select * from set_oroshi_names where parent_id='".$cPath."'");
  $cnt = count($updata);
  $i=0;
  while($col=tep_db_fetch_array($res)){
    if(($updata[$i] != $col['oroshi_name'])&&($_POST['cpath'] == $col['parent_id'] ) || $updata[$i] != ""){
      if($updata[$i] != ""){
        tep_db_query("update set_oroshi_names set oroshi_name = '".$updata[$i]."' where  oroshi_id = '".$col['oroshi_id']."'");
      }					
    }else{
      tep_db_query("delete from  set_oroshi_names  where oroshi_id = '".$col['oroshi_id']. "'");
    }
    $i++;
  }

  $setdata=$_POST['set_oroshi'];
  if(isset($setdata)){
    foreach($setdata as $val){
      if($val != ""){
        tep_db_query("insert into set_oroshi_names (parent_id,oroshi_name) values ('".$cPath."','".$val."')");
      }
    }
  }
  //拡張配列で作っていく
		
  break;
	
case 'delete':
  $oroshi_id=$_GET['id'];
  $cPath=$_GET['cpath'];
  tep_db_query("delete from  set_oroshi_names  where oroshi_id = '".$oroshi_id. "'");
  break;  
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
  <script type="text/javascript">
  var html = new Array();
var i=0;
function input_add(){
			
  html[i] = "追加:<input type='text' name='set_oroshi[]'><br />"; 
  document.getElementById("o_input").innerHTML=html;
  t
    i++;
}
function jump_oroshi_data(id,oid){
  location.href = 'cleate_list.php?cPath='+id+'&cpath='+id+'&oid='+oid;
}
		
function w_close(){
		
  //	window.close();	
}
		
function del_oroshi(id){
			
  var flg=confirm('削除しますか？');
  if(flg){
    location.href="cleate_oroshi.php?action=delete&id="+id;
    //    location.href="cleate_oroshi.php?action=delete&id="+id+"&cpath="+path;
  }else{
			
  }
}

function edit_oroshi(id){

   var flg=confirm('修改しますか？');
  var selectName = 'parent_id_'+id;
  var oroName = 'name_'+id;
  var path = document.getElementById(selectName).value;
  var name = document.getElementById(oroName).value;
  if (flg) {
    location.href= 'cleate_oroshi.php?action=edit_oroshi&id='+id+"&cpath="+path+"&name="+name; 
  }

}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
<?php  //<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
  ?>
  <div id="spiffycalendar" class="text"></div>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr><td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
  <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
  </table></td>
  <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
  <table><tr><td class = "pageHeading">卸業者の名前設定</td></tr><tr><td>
  <form method="post" action="cleate_oroshi.php?action=set_oroshi" onSubmit="w_close()">
  <input type="button" value="入力フォーム追加"　name='b1' onClick="input_add()"><br />
  <input type="hidden" value="<?php echo $cPath ?>" name="cpath">
  <?php 
  //get categorie_tree
  $start = 0;
  $categories_subtree = getSubcatergories($start);





  $res=tep_db_query("select * from set_oroshi_names ORDER BY oroshi_id ASC");
			
while($col=tep_db_fetch_array($res)){
	
  //  echo "卸業者：<input type='text' name='up_oroshi[]' value='".$col['oroshi_name']."'><input type='button' value='削除' name='b[]' onclick='del_oroshi(".$col['oroshi_id'].",".$cPath.")'><br>";
  echo "卸業者：<input type='text' name='up_oroshi[]' id ='name_".$col['oroshi_id']."' value='".$col['oroshi_name']."' / >";

  // show drop down list

  $selectText  = '<select id ="parent_id_'.$col['oroshi_id'].'" >';
  $selectText.=   '<option value = "0" >'.PLEASE_SELECT.'</option>';
  $selectText.=makeSelectOption($categories_subtree,$col['parent_id']).'</select>';
  echo $selectText;
  echo "<input type='button' value='编辑' name='b[]' onclick='edit_oroshi(".$col['oroshi_id'].")'>";
  echo "<input type='button' value='削除' name='b[]' onclick='del_oroshi(".$col['oroshi_id'].")'>";
  echo "<input type='button' value='".OROSHI_DATA_MANAGE."' name='b[]' onclick='jump_oroshi_data(".$col['parent_id'].",".$col['oroshi_id'].")'></br>";

				
}
			
?><div id="o_input"></div>
<input type="submit" value="卸業者登録">
  </form>
  </td></tr></table>
  </td>
  </tr>
  </table>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <br>
  </body>
  </html>
  <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
