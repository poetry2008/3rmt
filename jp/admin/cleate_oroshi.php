<?php
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');


$currencies = new currencies();
$cPath=$_POST['cpath'];
switch ($HTTP_GET_VARS['action']){
/*---------------------------
 case 'edit_oroshi' 编辑批发商名称 
 case 'set_oroshi' 设置批发商名称
 case 'delete' 删除批发商名称
 --------------------------*/
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

  break;
case 'set_oroshi':
  $orrshi_id = $_POST['orrshi_id'];
  if (isset($_POST['sort'])) {
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
  $sql = 'insert into set_oroshi_names  (oroshi_name,user_added,date_added,user_update,date_update) values ("'.$oro_name[$j].'","'.$_SESSION['user_name'].'","'.date('Y-m-d  H:i:s',time()).'","'.$_SESSION['user_name'].'","'.date('Y-m-d H:i:s',time()).'")';
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

  
    $name = $_POST['up_oroshi'];
    $sql = 'update set_oroshi_names set oroshi_name="'.$name[$orrshi_id].'",user_update="'.$_SESSION['user_name'].'",date_update = "'.date("Y-m-d H:i:s",time()).'"  where oroshi_id="'.$orrshi_id.'"';
    tep_db_query($sql);
  }
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo CLEATE_LIST_SETNAME_BUTTON;?> </title>
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
  <script type="text/javascript" src="includes/javascript/jquery.js"></script>
  <script language="javascript" src="includes/javascript/jquery_include.js"></script>
  <script language="javascript" src="js2php.php?path=includes|javascript&name=one_time_pwd&type=js"></script>
  <script type="text/javascript">
  var html = new Array();
var i=0;
<?php //添加输入形式 ?>
function input_add(){
      
  var cbox_head = "<div class='add_link'><?php echo BUTTON_ADD_TEXT;?>:<input type='text' name='set_oroshi[]'></div>"; 
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
<?php //链接cleate_list.php页面 ?>
function jump_oroshi_data(id,oid){
  location.href = 'cleate_list.php?cPath='+id+'&cpath='+id+'&oid='+oid;
}
<?php //跳转cleate_oroshi.php页 ?>
function resset_cb(){
  location.href="cleate_oroshi.php";
}
var valmethod = true;
<?php //更新排序 ?>
function notval(){
  valmethod = false;
}
<?php //关闭设置 ?>    
function w_close(c_permission, co_type){
  if (co_type == 1) {
    valmethod = false;
    document.getElementById("h_sort").innerHTML = '<input type="hidden" name="sort" value="<?php echo CLEATE_DOUGYOUSYA_UPDATE_SORT;?>">';
  }
  var o_error = false; 
  if(valmethod){
  if((!document.getElementById("orrshi_id")||document.getElementsByName('set_oroshi[]')[0])&&html.length==1){
    var j;
    var o_cid;
    var test;
    var o_name = document.getElementsByName('set_oroshi[]');
      o_cid = document.getElementsByName('ocid[0][]');
      if(o_name[0].value == null||o_name[0].value == ''){
        o_error = true; 
        alert('<?php echo TRADE_NAME; ?>');
      }else {
        var ex_name =  document.getElementsByName('exist_name[]');
        var z;
        for(z=0;z<ex_name.length;z++){
          if(ex_name[z].value==o_name[0].value){
             o_error = true; 
             alert(o_name[0].value+'<?php echo ALREADY_EXISTS; ?>');
          }
        }
        test=0;
        for (j=0 ;j<o_cid.length; j++){
          if(!o_cid[j].checked){
            test++;
          }
        }
        if (test == j) {
          o_error = true; 
          alert('<?php echo PLEASE_GAME_TITLE; ?>');
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
      o_error = true; 
      alert('<?php echo TRADE_NAME; ?>');
    }else{
        var ex_name =  document.getElementsByName('exist_name[]');
        var z;
        for(z=0;z<ex_name.length;z++){
          if(ex_name[z].value==s_name){
            continue;
          }
          if(ex_name[z].value==o_name){
            o_error = true; 
            alert(o_name+'<?php echo TRADE_NAME; ?>');
          }
        }
    for(x=0;x<ocid.length;x++){
      if(!ocid[x].checked){
        test++;
      }
    }
    if (test == x){
       o_error = true; 
       alert('<?php echo PLEASE_GAME_TITLE; ?>');
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
                  o_error = true; 
                  alert(o_name[le].value+'<?php echo ALREADY_EXISTS; ?>');
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
              o_error = true; 
              alert("<?php echo CONTENT_NOT_SAME; ?>");
            }
          }
        }
  if(!document.getElementsByName('set_oroshi[]')[0]&&!document.getElementById("orrshi_id")){
    o_error = true; 
    alert('<?php echo CREATE_INPUT_BOX; ?>');
  }
  }
  
  if (o_error == false) {
    if (c_permission == 31) {
      document.forms.o_form.submit(); 
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
            document.forms.o_form.submit(); 
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      });
    }
  }
}
<?php //删除批发商名称 ?> 
function del_oroshi(id, c_permission){
  var flg=confirm('<?php echo DELETE;?>');
  if(flg){
    if (c_permission == 31) {
      window.location.href="cleate_oroshi.php?action=delete&id="+id;
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
            window.location.href="cleate_oroshi.php?action=delete&id="+id;
          } else {
            alert('<?php echo JS_TEXT_ONETIME_PWD_ERROR;?>'); 
          }
        }
      });
    }
  }
}
<?php //编辑批发商名称 ?>
function edit_oroshi(id){
  var selectName = 'parent_id_'+id;
  var oroName = 'name_'+id;
  location.href= 'cleate_oroshi.php?action=edit_oroshi&id='+id; 

}
<?php //往上排一级 排序 ?>
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
<?php 
$belong = str_replace('/admin/','',$_SERVER['SCRIPT_NAME']);
require("includes/note_js.php");
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
<?php if(!(isset($_SESSION[$page_name])&&$_SESSION[$page_name])&&$_SESSION['onetime_pwd']){?>
  <script language='javascript'>
    one_time_pwd('<?php echo $page_name;?>');
  </script>
<?php }?>
  <div id="spiffycalendar" class="text"></div>
  <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
  <table border="0" width="100%" cellspacing="2" cellpadding="2" class="content">
     <tr>
        <td width="<?php echo BOX_WIDTH; ?>" valign="top">
           <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
              <tr>
                 <td><?php require(DIR_WS_INCLUDES . 'column_left.php'); ?></td>
              </tr> 
           </table>
        </td>
        <td width="100%" valign="top"><div class="box_warp"><?php echo $notes;?>
        <div class="compatible">
           <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                 <td class="pageHeading" height="40"><?php echo CLEATE_LIST_SETNAME_BUTTON;?></td>
              </tr>
              <tr>
                 <td>
  <form method="post" action="cleate_oroshi.php?action=set_oroshi" name="o_form">
                    <table width="100%" cellspacing="0" cellpadding="0">
                       <tr>
                          <td valign="top" class="cleate_add">
  <input type="button" value="<?php echo CLEATE_DOUGYOUSYA_ADD_BUTTON;?>" name='b1' onClick="input_add()">
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
  $res=tep_db_query("select * from set_oroshi_names ORDER BY sort_order,oroshi_id ASC");
  $i = 0;
?>
<table width="100%" cellspacing="0" border="0" cellpadding="2">
<?php
while($col=tep_db_fetch_array($res)){
if(empty($HTTP_GET_VARS['id'])){
   $HTTP_GET_VARS['id'] = $col['oroshi_id']; 
   echo '<tr class="dataTableRowSelected">';
 }else if($col['oroshi_id'] == $HTTP_GET_VARS['id']){
   echo '<tr class="dataTableRowSelected">';
 } else{
   echo '<tr>';
 }
  ?>
  <td width="10"><?php if ($i) {?><a href="javascript:void(0);" onclick="ex(<?php echo $i;?>)">↑</a><?php }?></td>
  <td id="tr_<?php echo $i;?>_1">
    <input type="hidden" name="sort_order[<?php echo $col['oroshi_id'];?>]" value="<?php echo $i;?>" class="sort_order_input">
   <?php echo WHOLESALERS.$col['oroshi_name'];?><input type="hidden" name="exist_name[]" value='<?php echo $col['oroshi_name'];?>'>
  </td>
  <td id="tr_<?php echo $i;?>_2" width='50'><a href='cleate_oroshi.php?action=edit_oroshi&id=<?php echo $col['oroshi_id'];?>'><?php echo CLEATE_DOUGYOUSYA_EDIT;?></a></td>
   
  <?php
  if ($ocertify->npermission >= 15) {
  ?>
  <td id="tr_<?php echo $i;?>_3" width='50'><a href='javascript:void(0);' onclick="del_oroshi(<?php echo $col['oroshi_id'];?>, '<?php echo $ocertify->npermission;?>')"><?php echo CLEATE_DOUGYOUSYA_DEL;?></a></td>
  <?php
  } else {
  ?>
  <td id="tr_<?php echo $i;?>_3" width='50'>&nbsp;</td>
  <?php
  }
  ?> 
  <td id="tr_<?php echo $i;?>_4" width='50'><a href='cleate_list.php?action=oroshi&o_id=<?php echo $col['oroshi_id'];?>'><?php echo OROSHI_DATA_MANAGE?></a></td>
  <td id="tr_<?php echo $i;?>_5" width='50'><a href='history.php?action=oroshi&o_id=<?php echo $col['oroshi_id'];?>'><?php echo CLEATE_DOUGYOUSYA_HISTORY;?></a>
  <td width='50' align="right"><a href='cleate_oroshi.php?action=select_oroshi&id=<?php echo
  $col['oroshi_id'];?>'><?php if($col['oroshi_id'] == $HTTP_GET_VARS['id']){echo tep_image(DIR_WS_IMAGES.
      'icon_arrow_right.gif');}else{ echo tep_image(DIR_WS_IMAGES . 'icon_info.gif');}?></a></td>
  </tr>
<?php if(isset($ckstr)&&$orrshi_id == $col['oroshi_id']){?>
  <tr>
    <td colspan="6">
      <div id="change_one">
        <input type='text' name='up_oroshi[<?php echo $col['oroshi_id'];?>]' id='name_<?php echo $col['oroshi_id'];?>' value='<?php echo $col['oroshi_name'];?>' />
        <input type="hidden" id="src_name_<?php echo $col['oroshi_id'];?>" value='<?php echo $col['oroshi_name'];?>'><br>
        <?php echo makeCheckbox($categories_subtree,$ckstr);?> 
        <a href="javascript:void(0);"><?php echo tep_html_element_button(IMAGE_UPDATE, 'onclick="w_close(\''.$ocertify->npermission.'\', \'0\');"');?></a>
        <input type = "button" value = "<?php echo IMAGE_CANCEL;?>" onclick="resset_cb()"><br />
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
</td>
<td width="25%" valign="top" class="right_column_boxes">
<table width="100%" cellspacing="0" cellpadding="2" border="0">
 <tr class="infoBoxHeading">
    <td class="infoBoxHeading">
    <?php 
    $oroshi_query = tep_db_query("select * from set_oroshi_names where oroshi_id = '".$HTTP_GET_VARS['id']."'");
    $oroshi = tep_db_fetch_array($oroshi_query);
    echo '<table width="100%" cellspacing="0" cellpadding="2" border="0">
    <tr><td class="infoBoxContent"
    align="left">'.$oroshi['oroshi_name'].'</td></tr>';
    if(tep_not_null($oroshi['user_added'])){  
    echo '<tr><td>'.TEXT_USER_ADDED.'&nbsp;'.$oroshi['user_added'].'</td></tr>';
    }else{
    echo '<tr><td>'.TEXT_USER_ADDED.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
    }if(tep_not_null(tep_datetime_short($oroshi['date_added']))){
    echo '<tr><td>'.TEXT_DATE_ADDED.'&nbsp;'.$oroshi['date_added'].'</td></tr>';
    }else{
    echo '<tr><td>'.TEXT_DATE_ADDED.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
    }if(tep_not_null($oroshi['user_update'])){
    echo '<tr><td>'.TEXT_USER_UPDATE.'&nbsp;'.$oroshi['user_update'].'</td></tr>';
    }else{
    echo '<tr><td>'.TEXT_USER_UPDATE.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
    }if(tep_not_null(tep_datetime_short($oroshi['date_update']))){
    echo '<tr><td>'.TEXT_DATE_UPDATE.'&nbsp;'.$oroshi['date_update'].'</td></tr>';
    }else{
    echo '<tr><td>'.TEXT_DATE_UPDATE.'&nbsp;'.TEXT_UNSET_DATA.'</td></tr>';
    }
    echo '</table>';
    ?></td>
  </tr>
</table>
</td>
</tr>
</table>
<div id="oo_input" style="display:none">
<?php
   echo makeCheckbox($categories_subtree);
?>
</div>
<a href="javascript:void(0);"><?php echo tep_html_element_button(CLEATE_LIST_LOGIN_BUTTON, 'onclick="w_close(\''.$ocertify->npermission.'\', \'0\');"');?></a>
<a href="javascript:void(0);"><?php echo tep_html_element_button(CLEATE_DOUGYOUSYA_UPDATE_SORT, 'onclick="w_close(\''.$ocertify->npermission.'\', \'1\');"');?></a>
<div id="h_sort"></div>
</form>
                          </td>
                       </tr>
                    </table>      

                 </td>
              </tr>
           </table>
           </div>
           </div>
        </td>
      </tr>
  </table>
  <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
  <br>
  </body>
  </html>
  <?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
