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
  $sql = 'select * from set_oroshi_categories where oroshi_id='.$orrshi_id;
  $res = tep_db_query($sql);
  $ckstr = array();
  while($col = tep_db_fetch_array($res)){
    $ckstr[] = $col['categories_id'];
  }

//  $sql = 'update set_oroshi_names set oroshi_name = "'.$name.'"';
//  $sql.=', parent_id = '.$cpath;
//  $sql.=' where oroshi_id = '.$orrshi_id;
//  tep_db_query($sql);

  break;
case 'set_oroshi':
  $orrshi_id = $_POST['orrshi_id'];
  if(isset($_POST['set_oroshi'])){
  $ocid_arr = $_POST['ocid'];
  $oro_name = $_POST['set_oroshi'];
  $cot = count($oro_name);
  $j = 0 ;
  while ( $j < $cot) {
  $ocid = $ocid_arr[$j];
  $sql = 'insert into set_oroshi_names (oroshi_name) values
    ("'.$oro_name[$j].'")';
  tep_db_query($sql);
  $res = tep_db_query("select max(oroshi_id) oroshi_id from set_oroshi_names");
  $col = tep_db_fetch_array($res);
  $cnt = count($ocid);
  $i = 0;
  $flag = false;
  while ( $i < $cnt ) {
       $i++;
    $sql = 'insert into set_oroshi_categories (oroshi_id,categories_id) values
      ('.$col['oroshi_id'].','.$ocid[$i-1].')';
       tep_db_query($sql);
  }
    $j++;
  }
  }else if (isset($orrshi_id)){
  $ocid = $_POST['ocid'];
  $cnt = count($ocid);
  $i = 0;
  $flag = false;
  $res = tep_db_query("select categories_id from
    set_oroshi_categories where oroshi_id=".$orrshi_id);
  $douno = array();
  $j = 0; 
  while($col = tep_db_fetch_array($res)){
    $douno[$j] = $col['categories_id'];
    $j++;
  }
    $sql = 'delete from set_oroshi_categories where oroshi_id='.$orrshi_id;
    tep_db_query($sql);
  foreach ($ocid as $diffval) {
    $sql = 'insert into set_oroshi_categories (oroshi_id,categories_id) values
      ('.$orrshi_id.','.$diffval.')';
       tep_db_query($sql);
  }
    $name = $_POST['up_oroshi'];
    $sql = 'update set_oroshi_names set oroshi_name="'.$name[$orrshi_id].'"
    where oroshi_id='.$orrshi_id;
    tep_db_query($sql);
  }
  /*
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
  }*/
  //拡張配列で作っていく
		
  break;
	
case 'delete':
  $oroshi_id=$_GET['id'];
  $sql = "delete from set_oroshi_names  where oroshi_id = '".$oroshi_id."'";
  tep_db_query($sql);
  $sql = "delete from set_oroshi_categories  where oroshi_id = '".$oroshi_id."'";
  tep_db_query($sql);
  $sql = "delete from set_oroshi_datas where oroshi_id = '".$oroshi_id."'";
  tep_db_query($sql);
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
			
  var cbox_head = "追加:<input type='text' name='set_oroshi[]'><br />"; 
  var cbox  = document.getElementById("oo_input").innerHTML;
  cbox = cbox.replace(/ocid/g,'ocid['+i+']');
  html[i] = cbox_head+cbox;
  document.getElementById("o_input").innerHTML=html;
  if(document.getElementById("change_one")){
    document.getElementById("change_one").innerHTML=null;
    document.getElementById("orrshi_id").value='';
  }
  i++;
}
function jump_oroshi_data(id,oid){
  location.href = 'cleate_list.php?cPath='+id+'&cpath='+id+'&oid='+oid;
}
function resset_cb(){
  location.href="cleate_oroshi.php";
}
		
function w_close(){
  var i;
  if(!document.getElementById("orrshi_id")||document.getElementsByName('set_oroshi[]')[0]){
    var j;
    var o_cid;
    var test;
    var o_name = document.getElementsByName('set_oroshi[]');
    for (i=0 ;i<o_name.length; i++){
      o_cid = document.getElementsByName('ocid['+i+'][]');
      if(o_name[i].value == null||o_name[i].value == ''){
        alert('業者名はご記入ください');
        return false;
      }else {
        test=0;
        for (j=0 ;j<o_cid.length; j++){
          if(!o_cid[j].checked){
            test++;
          }
        }
        if (test == j) {
          alert('ゲームタイトルを一つ選択してください');
          return false;
        }
      }
    }
  }else{
    var o_cid = document.getElementById("orrshi_id").value;
    var o_name = document.getElementById("name_"+o_cid).value;
    var ocid = document.getElementsByName('ocid[]');
    var test = 0;
    if (o_name == ''||o_name == null){
      alert('業者名はご記入ください');
      return false;
    }
    for(i=0;i<ocid.length;i++){
      if(!ocid[i].checked){
        test++;
      }
    }
    if (test == i){
       alert('ゲームタイトルを一つ選択してください');
       return false;
    }
  }
  if(!document.getElementsByName('set_oroshi[]')[0]&&!document.getElementById("orrshi_id")){
    alert('まず、入力フォーム追加してください');
    return false;
  }
  return true;
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

  var selectName = 'parent_id_'+id;
  var oroName = 'name_'+id;
//  var path = document.getElementById(selectName).value;
//  var name = document.getElementById(oroName).value;
  location.href= 'cleate_oroshi.php?action=edit_oroshi&id='+id; 

}
</script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
<?php  //<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" onLoad="SetFocus();">
  ?>
  <div id="spiffycalendar" class="text"></div>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
     <tr>
        <td width="<?php echo BOX_WIDTH; ?>" valign="top">
           <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
              <tr>
                 <td><?php require(DIR_WS_INCLUDES . 'column_left.php'); ?></td>
              </tr> 
           </table>
        </td>
        <td width="100%" valign="top">
           <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                 <td class="pageHeading" height="40">卸業者の名前設定</td>
              </tr>
              <tr>
                 <td>
  <form method="post" action="cleate_oroshi.php?action=set_oroshi" onSubmit="return w_close()">
                    <table width="100%" cellspacing="0" cellpadding="0">
                       <tr>
                          <td valign="top" class="cleate_add">
  <input type="button" value="入力フォーム追加"　name='b1' onClick="input_add()">
                          </td>
                       </tr>
                       <tr>
                          <td class="cleate_main"> 
  <input type="hidden" value="<?php echo $cPath ?>" name="cpath">
  <?php if(isset($orrshi_id)){
    echo '<input type="hidden" value="'.$orrshi_id.'" id = "orrshi_id" name="orrshi_id">';
  }

  //get categorie_tree
  $start = 0;
  $categories_subtree = getSubcatergories($start);





  $res=tep_db_query("select * from set_oroshi_names ORDER BY oroshi_id ASC");
			
echo '<table>';
while($col=tep_db_fetch_array($res)){
  echo '<tr>';
	
  //  echo "卸業者：<input type='text' name='up_oroshi[]' value='".$col['oroshi_name']."'><input type='button' value='削除' name='b[]' onclick='del_oroshi(".$col['oroshi_id'].",".$cPath.")'><br>";
  echo "<td width='150'>卸業者：".$col['oroshi_name'].'</td>';

  // show drop down list

  echo "<td width='50'><a href=
    'cleate_oroshi.php?action=edit_oroshi&id=".$col['oroshi_id']."'>编辑</a></td>";
  echo "<td width='50'><a href='' onclick='del_oroshi(".$col['oroshi_id'].")'>削除</a></td>";
  //  echo "<td><input type='button' value='".OROSHI_DATA_MANAGE."' name='b[]'
  //    onclick='jump_oroshi_data(".$col['parent_id'].",".$col['oroshi_id'].")'></td>";
  echo "<td><a href='cleate_list.php?action=oroshi&o_id=".$col['oroshi_id']."'>".OROSHI_DATA_MANAGE."</a>";
  echo "<td><a href='history.php?action=oroshi&o_id=".$col['oroshi_id']."'>"."履歴"."</a>";
  echo '</tr>';
   if(isset($ckstr)&&$orrshi_id == $col['oroshi_id']){
    echo '<tr><td colspan="4">';
    echo '<div id="change_one">';
    echo "<input type='text' name='up_oroshi[".$col['oroshi_id']."]' id
      ='name_".$col['oroshi_id']."' value='".$col['oroshi_name']."' / ><br>";
    echo makeCheckbox($categories_subtree,$ckstr);
    echo '<input type="submit" value="更新"><input type = "button" value = "取り消し"
      onclick="resset_cb()"><br />';
    echo '</div>';
    echo '</td></tr>';
   }

				
}
			
?>
<tr>
<td colspan='4'>
<div id="o_input"></div>
</td>
</tr>
</table>
<div id="oo_input" style="display:none">
<?php
   echo makeCheckbox($categories_subtree);
?>
</div>
<input type="submit" value="卸業者登録">
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
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <br>
  </body>
  </html>
  <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
