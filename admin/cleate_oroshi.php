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
  $sql = 'select * from set_oroshi_categories where oroshi_id="'.$orrshi_id.'"';
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
  if (isset($_POST['sort'])) {
    /**
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    exit;
    /**/
    foreach($_POST['sort_order'] as $oid => $sort_order) {
      tep_db_perform('set_oroshi_names', array('sort_order' => (int)$sort_order), 'update', "oroshi_id='".$oid."'");
    }
  } else if(isset($_POST['set_oroshi'])){
  $ocid_arr = $_POST['ocid'];
  $oro_name = $_POST['set_oroshi'];
  $cot = count($oro_name);
  $j = 0 ;
  while ( $j < $cot) {
  $ocid = $ocid_arr[$j];
  $sql = 'insert into set_oroshi_names (oroshi_name) values
    ("'.$oro_name[$j].'")';
    $j++;
  if(!$oro_name[$j-1]||!$ocid){
    continue;
  }
  tep_db_query($sql);
  $res = tep_db_query("select max(oroshi_id) oroshi_id from set_oroshi_names");
  $col = tep_db_fetch_array($res);
  $cnt = count($ocid);
  $i = 0;
  $flag = false;
  while ( $i < $cnt ) {
       $i++;
    $sql = 'insert into set_oroshi_categories (oroshi_id,categories_id) values
      ("'.$col['oroshi_id'].'","'.$ocid[$i-1].'")';
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
    set_oroshi_categories where oroshi_id='".$orrshi_id."'");
  $douno = array();
  $j = 0; 
  while($col = tep_db_fetch_array($res)){
    $douno[$j] = $col['categories_id'];
  }

  $sql = 'delete from set_oroshi_categories where oroshi_id="'.$orrshi_id.'"';
  tep_db_query($sql);
  foreach ($ocid as $diffval) {
    $sql = 'insert into set_oroshi_categories (oroshi_id,categories_id) values
      ("'.$orrshi_id.'","'.$diffval.'")';
       tep_db_query($sql);
  }

//  $sql = 'delete from set_oroshi_datas where oroshi_id = "'.$orrshi_id.'" and parent_id not in (select soc.categories_id from set_oroshi_categories soc where oroshi_id ="'.$orrshi_id.'")';
//  tep_db_query($sql);
  
    $name = $_POST['up_oroshi'];
    $sql = 'update set_oroshi_names set oroshi_name="'.$name[$orrshi_id].'"
    where oroshi_id="'.$orrshi_id.'"';
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
  tep_redirect(tep_href_link('cleate_oroshi.php','action=edit_oroshi&id='.$_POST['orrshi_id']));
  break;
  
case 'delete':
  $oroshi_id=$_GET['id'];
  $sql = "delete from set_oroshi_names  where oroshi_id = '".$oroshi_id."'";
  tep_db_query($sql);
  $sql = "delete from set_oroshi_categories  where oroshi_id = '".$oroshi_id."'";
  tep_db_query($sql);
  $sql = "delete from set_oroshi_datas where oroshi_id = '".$oroshi_id."'";
  tep_db_query($sql);
  tep_redirect('cleate_oroshi.php');
  break;  
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
  <script type="text/javascript" src="includes/javascript/jquery.js"></script>
  <script type="text/javascript">
  var html = new Array();
var i=0;
function input_add(){
      
  var cbox_head = "<div class='add_link'>追加:<input type='text' name='set_oroshi[]'></div>"; 
  var cbox  = document.getElementById("oo_input").innerHTML;
  cbox = cbox.replace(/ocid/g,'ocid['+i+']');
  html[i] = cbox_head+cbox;
  var html_text='';
  var o;
  if(i>0){
    for(o=0;o<html.length;o++){
      html_text+=html[o];
    }
  }else{
    html_text=html[0]
  }
  document.getElementById("o_input").innerHTML=html_text;
  if(document.getElementById("change_one")){
    document.getElementById("change_one").innerHTML='';
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
var valmethod = true;
function notval(){
  valmethod = false;
}
    
function w_close(){
  if(valmethod){
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
        var ex_name =  document.getElementsByName('exist_name[]');
        var z;
        for(z=0;z<ex_name.length;z++){
          if(ex_name[z].value==o_name[0].value){
            alert(o_name[0].value+'はもう存在しています');
            return false;
          }
        }
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
            if (nary[ii-1]!=null||nary[ii-1]!=''){
              continue;
            }
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
  }
  return true;
  //  window.close(); 
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
//var sort_changed = false;
function ex(id){
  for(exi=1;exi<6;exi++){
    tmp = document.getElementById('tr_'+id+'_'+exi).innerHTML;
    document.getElementById('tr_'+id+'_'+exi).innerHTML = document.getElementById('tr_'+(id-1)+'_'+exi).innerHTML;
    document.getElementById('tr_'+(id-1)+'_'+exi).innerHTML = tmp;
  }
  $('#tr_'+id+'_1>.sort_order_input').val(id);
  $('#tr_'+(id-1)+'_1>.sort_order_input').val(id-1);
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
  <?php if(isset($orrshi_id)){?>
    <input type="hidden" value="<?php echo $orrshi_id;?>" id = "orrshi_id" name="orrshi_id">
  <?php }
  //get categorie_tree
  $start = 0;
  $categories_subtree = getSubcatergories($start);
  $res=tep_db_query("select * from set_oroshi_names ORDER BY sort_order ASC");
  $i = 0;
?>
<table>
<?php while($col=tep_db_fetch_array($res)){?>
  <tr>
  <!--卸業者：<input type='text' name='up_oroshi[]' value='<?php echo $col['oroshi_name'];?>'>
  <input type='button' value='削除' name='b[]' onclick='del_oroshi(<?php echo $col['oroshi_id'];?>, <?php echo $cPath;?>)'><br>-->
  <td width="10"><?php if ($i) {?><a href="javascript:void(0);" onclick="ex(<?php echo $i;?>)">↑</a><?php }?></td>
  <td id="tr_<?php echo $i;?>_1">
    <input type="hidden" name="sort_order[<?php echo $col['oroshi_id'];?>]" value="<?php echo $i;?>" class="sort_order_input">
    卸業者：<?php echo $col['oroshi_name'];?><input type="hidden" name="exist_name[]" value='<?php echo $col['oroshi_name'];?>'>
  </td>
  <td id="tr_<?php echo $i;?>_2" width='50'><a href='cleate_oroshi.php?action=edit_oroshi&id=<?php echo $col['oroshi_id'];?>'>編集</a></td>
  <td id="tr_<?php echo $i;?>_3" width='50'><a href='javascript:void(0);' onclick='del_oroshi(<?php echo $col['oroshi_id'];?>)'>削除</a></td>
  <td id="tr_<?php echo $i;?>_4" width='50'><a href='cleate_list.php?action=oroshi&o_id=<?php echo $col['oroshi_id'];?>'>データ</a></td>
  <td id="tr_<?php echo $i;?>_5" width='50'><a href='history.php?action=oroshi&o_id=<?php echo $col['oroshi_id'];?>'>履歴</a>
  </tr>
<?php if(isset($ckstr)&&$orrshi_id == $col['oroshi_id']){?>
  <tr>
    <td colspan="6">
      <div id="change_one">
        <input type='text' name='up_oroshi[<?php echo $col['oroshi_id'];?>]' id='name_<?php echo $col['oroshi_id'];?>' value='<?php echo $col['oroshi_name'];?>' />
        <input type="hidden" id="src_name_<?php echo $col['oroshi_id'];?>" value='<?php echo $col['oroshi_name'];?>'><br>
        <?php echo makeCheckbox($categories_subtree,$ckstr);?>
        <input type="submit" value="更新"><input type = "button" value = "取り消し" onclick="resset_cb()"><br />
      </div>
    </td>
  </tr>
<?php
  }
  $i++;
}
?>
<tr>
  <td colspan='6'>
    <div id="o_input"></div>
  </td>
</tr>
</table>
<div id="oo_input" style="display:none">
<?php
   echo makeCheckbox($categories_subtree);
?>
</div>
<input type="submit" value="卸業者登録"        name="add">
<input type="submit" onClick="notval()" value="順序を更新する" name="sort">
</form>
                          </td>
                       </tr>
                    </table>      

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
