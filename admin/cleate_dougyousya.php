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
  //通过传递的orrshi_id 查找对应的categories_id 插入到数组 skstr
  $sql = "select * from set_dougyousya_categories where
    dougyousya_id='".$orrshi_id."'";
  $res = tep_db_query($sql);
  $ckstr = array();
  while($col = tep_db_fetch_array($res)){
  $ckstr[] = $col['categories_id'];   
  }
//  $sql = 'update set_dougyousya_names set dougyousya_name = "'.$name.'", parent_id = '.$cpath .' where dougyousya_id = '.$orrshi_id;
//  tep_db_query($sql);
  break;
case 'set_oroshi':
  //orrshi_id是一个隐藏域 只有通过修改给该变量设置值
  $orrshi_id = $_POST['orrshi_id'];
  //只有通过追加生成的文本存在值时执行该条件语句
  if(isset($_POST['set_oroshi'])){
  /*
   ocid_arr是对应 set_oroshi的2维数组 、
   例如 ocid_arr[0] 保存了 set——oroshi[0]对应的值
  */
  $ocid_arr = $_POST['ocid'];
  $oro_name = $_POST['set_oroshi'];
  $cot = count($oro_name);
  $j = 0 ;
  while ( $j < $cot) {
  $ocid = $ocid_arr[$j];
  $sql = 'insert into set_dougyousya_names (dougyousya_name) values
    ("'.$oro_name[$j].'")';
    $j++;
  if(!$oro_name[$j-1]||!$ocid){
    continue;
  }
  tep_db_query($sql);
  $res = tep_db_query("select max(dougyousya_id) dougyousya_id from set_dougyousya_names");
  $col = tep_db_fetch_array($res);
  $cnt = count($ocid);
  $i = 0;
  $flag = false;
  while ( $i < $cnt ) {
       $i++;
    $sql = 'insert into set_dougyousya_categories (dougyousya_id,categories_id) values
      ("'.$col['dougyousya_id'].'","'.$ocid[$i-1].'")';
       tep_db_query($sql);
  }
  }
  }else if (isset($orrshi_id)){
  //这个是修改的方法
  $ocid = $_POST['ocid'];
  $cnt = count($ocid);
  $i = 0;
  $flag = false;
  $res = tep_db_query("select categories_id from
    set_dougyousya_categories where dougyousya_id='".$orrshi_id."'");
  $douno = array();
  //douno 是以选定的 checkbox(数据库中以存在数据)
  $j = 0; 
  while($col = tep_db_fetch_array($res)){
    $douno[$j] = $col['categories_id'];
    $j++;
  }
    $sql = 'delete FROM set_dougyousya_categories WHERE
      dougyousya_id="'.$orrshi_id.'"';
    tep_db_query($sql);
  foreach ($ocid as $diffval) {
    $sql = 'insert into set_dougyousya_categories (dougyousya_id,categories_id) values
      ("'.$orrshi_id.'","'.$diffval.'")';
       tep_db_query($sql);
  }
  $name = $_POST['up_oroshi'];
  $sql = 'delete from set_dougyousya_history where dougyousya_id = "'.$orrshi_id.'"and categories_id not in (select sdc.categories_id from set_dougyousya_categories sdc where dougyousya_id ="'.$orrshi_id.'")';
  tep_db_query($sql);
  $sql = 'update set_dougyousya_names set dougyousya_name="'.$name[$orrshi_id].'"
  where dougyousya_id="'.$orrshi_id.'"';
   tep_db_query($sql);
  }
  
  /*
  $updata=$_POST['up_oroshi'];
  $res=tep_db_query("select * from set_dougyousya_names where parent_id='".$cPath."'");
  $cnt = count($updata);
  $i=0;
  while($col=tep_db_fetch_array($res)){
    if(($updata[$i] != $col['dougyousya_name'])&&($_POST['cpath'] == $col['parent_id'] ) || $updata[$i] != ""){
      if($updata[$i] != ""){
        tep_db_query("update set_dougyousya_names set dougyousya_name = '".$updata[$i]."' where  dougyousya_id = '".$col['dougyousya_id']."'");
      }         
    }else{
      tep_db_query("delete from set_dougyousya_names where dougyousya_id = '".$col['dougyousya_id']. "'");
    }
    $i++;
  }
  
  $setdata=$_POST['set_oroshi'];
  if(isset($setdata)){
    foreach($setdata as $val){
      if($val != ""){
        tep_db_query("insert into set_dougyousya_names (parent_id,dougyousya_name) values ('".$cPath."','".$val."')");
      }
    } 
  }
  */
  tep_redirect('cleate_dougyousya.php');
  break;
  
case 'delete':
  $dougyousya_id=$_GET['id'];
  //  $cPath=$_GET['cpath'];
  $sql = "delete from  set_dougyousya_names  where dougyousya_id ='".$dougyousya_id.
    "'";
  tep_db_query($sql);
  $sql = "delete from set_dougyousya_categories where dougyousya_id='".$dougyousya_id."'";
  tep_db_query($sql);
  $sql = "delete from set_dougyousya_history where dougyousya_id='".$dougyousya_id."'";
  tep_db_query($sql);
  tep_redirect('cleate_dougyousya.php');
  break;  
}
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
  <script type="text/javascript">
  var html = new Array();
var i=0;
function input_add(){
      
  var cbox_head  = "追加:<input type='text' name='set_oroshi[]'><br />"; 
  var cbox = document.getElementById("oo_input").innerHTML;
  //使用replace 方法替换checkbox name 为2维数组
  cbox =  cbox.replace(/ocid/g,'ocid['+i+']');
  html[i] = cbox_head+cbox; 
  
  document.getElementById("o_input").innerHTML=html;
  if(document.getElementById("change_one")){
    document.getElementById("change_one").innerHTML='';
    document.getElementById("orrshi_id").value='';
  }
  i++;
}

function resset_cb(){
  location.href= 'cleate_dougyousya.php'; 
}
    
function w_close(){
  if((!document.getElementById("orrshi_id")||document.getElementsByName('set_oroshi[]')[0])&&html.length==1){
    var j;
    var o_cid;
    var test;
    var o_name = document.getElementsByName('set_oroshi[]');
      o_cid = document.getElementsByName('ocid[0][]');
      if(o_name[0].value == null||o_name[0].value == ''){
        alert('業者名はご記入ください');
        return false;
      }else {
        test=0;
        for (j=0 ;j<o_cid.length; j++){
          if(!o_cid[j].checked){
            test++;
          }
        }
        var ex_name =  document.getElementsByName('exist_name[]');
        var z;
        for(z=0;z<ex_name.length;z++){
          if(ex_name[z].value==o_name[0].value){
            alert(o_name[0].value+'はもう存在しています');
            return false;
          }
        }
        if (test == j) {
          alert('ゲームタイトルを一つ選択してください');
          return false;
        }
      }
  }else if(document.getElementById("orrshi_id")){
    var o_cid = document.getElementById("orrshi_id").value;
    if(document.getElementById("name_"+o_cid)){
    var o_name = document.getElementById("name_"+o_cid).value;
    var s_name = document.getElementById("src_name_"+o_cid).value;
    var ocid = document.getElementsByName('ocid[]');
    var test = 0;
    if (o_name == ''||o_name == null){
      alert('業者名はご記入ください');
      return false;
    }else{
        var ex_name =  document.getElementsByName('exist_name[]');
        var z;
        for(z=0;z<ex_name.length;z++){
          if(ex_name[z].value==s_name){
            continue;
          }
          if(ex_name[z].value==o_name){
            alert(o_name+'はもう存在しています');
            return false;
          }
        }
    for(x=0;x<ocid.length;x++){
      if(!ocid[x].checked){
        test++;
      }
    }
    if (test == x){
       alert('ゲームタイトルを一つ選択してください');
       return false;
    }
    }
    }
  }
        if(html.length>1){
          var o_name = document.getElementsByName('set_oroshi[]');
          var ex_name =  document.getElementsByName('exist_name[]');
          var le;
          var z;
          var set_name_arr = new Array();
          for(le=0;le<o_name.length;le++){
            if(o_name[le].value != null&&o_name[le].value != ''){
               for(z=0;z<ex_name.length;z++){
                if(ex_name[z].value==o_name[le].value){
                  alert(o_name[le].value+'はもう存在しています');
                  return false;
                }
               }
            }
            set_name_arr[le] = o_name[le].value;
          }
          var nary=set_name_arr.sort();
          for(var ii=1;ii<nary.length;ii++){
            if (nary[ii-1]==nary[ii]){
              alert("入力された内容は同じになってはいけません");
              return false;
            }
          }
        }
  if(!document.getElementsByName('set_oroshi[]')[0]&&!document.getElementById("orrshi_id")){
    alert('まず、入力フォーム追加してください');
    return false;
  }
  return true;
  //  window.close(); 
}

function show_history(id){
  location.href= 'history.php?action=dougyousya&cid='+id;

}

    
function del_oroshi(id){
      
  var flg=confirm('削除しますか？');
  if(flg){
    location.href="cleate_dougyousya.php?action=delete&id="+id;
  }else{
      
  }
}
</script>
<title>同業者の名前設定</title>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
  <div id="spiffycalendar" class="text"></div>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table border="0" width="100%" cellspacing="2" cellpadding="2">
     <tr>
        <td width="<?php echo BOX_WIDTH; ?>" valign="top">
           <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
                    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
           </table>
        </td>
        <td width="100%" valign="top">
           <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                 <td class="pageHeading" height="40">同業者の名前設定</td>
              </tr>
              <tr>
                 <td>
  <form method="post" action="cleate_dougyousya.php?action=set_oroshi"  onSubmit="return w_close()">
  <table width="100%" cellspacing="0" cellpadding="0">
     <tr>
        <td class="cleate_add" valign="top">
  <input type="button" value="入力フォーム追加" name='b1' onClick="input_add()">
        </td>
     </tr>
     <tr>
        <td class="cleate_main">
  <input type="hidden" value="<?php echo $cPath ?>" name="cpath">
  <?php if(isset($orrshi_id)){
  echo '<input type="hidden" value="'.$orrshi_id .'"
    name="orrshi_id" id="orrshi_id">';
  }
  $start = 0;
$categories_subtree = getSubcatergories($start);

$res=tep_db_query("select * from set_dougyousya_names ORDER BY dougyousya_id ASC");
      
echo "<table>";
while($col=tep_db_fetch_array($res)){
  echo "<tr>";
  echo "<td width='150'>同業者：".$col['dougyousya_name']."</td>";
  echo '<input type="hidden" name="exist_name[]" value='.$col['dougyousya_name'].'>';
  echo "<td width='50'><a href=
    'cleate_dougyousya.php?action=edit_oroshi&id=".$col['dougyousya_id']."'>編集</a></td>";
  echo "<td width='50'><a
    href='javascript:void(0);' onclick='del_oroshi(".$col['dougyousya_id'].")'>削除</a></td>";
  //  echo "<td><input type='button' value='履歴' name='b[]'
  //   onclick='show_history(".$col['parent_id'].")'></td>";
  echo "<td><a href='history.php?action=dougyousya&dougyousya_id=".$col['dougyousya_id']."'>履歴</a>";
  echo "</tr>";
  if(isset($ckstr)&&$orrshi_id == $col['dougyousya_id']){
    echo "<tr><td colspan ='4'>";
    echo '<div id="change_one">';
    echo "<input type='text'
      id='name_".$col['dougyousya_id']."'name='up_oroshi[".$col['dougyousya_id']."]'
      value='".$col['dougyousya_name']."'><br>";
  echo '<input type="hidden" id="src_name_'.$col['dougyousya_id'].'" value='.$col['dougyousya_name'].'>';
    echo makeCheckbox($categories_subtree,$ckstr);
    echo '<input type="submit" value="更新"><input type = "button" value = "取り消し"
      onclick="resset_cb()"><br><br>';
    echo '</div>';
    echo "</td></tr>";
  }
}
      
?>
<tr>
<td  colspan ='4'>
<div id="o_input"></div>
</td>
</tr>
</table>
<div id="oo_input" style="display:none">
<?php
    echo makeCheckbox($categories_subtree); 
?>
</div>
<input type="submit" value="同業者登録">
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
  <?php require(DIR_WS_INCLUDES . 'application_bottom.php');?>
