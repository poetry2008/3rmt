<?php
/*
 * 住所作成 Ajax
 */
require('includes/application_top.php');
$id = tep_db_prepare_input($_POST['id']);
$type_value = tep_db_prepare_input($_POST['type']);
$sort_id = tep_db_prepare_input($_POST['sort']);
$flag = tep_db_prepare_input($_POST['flag']);

if(isset($id) && $id != 0){
     
  if($sort_id > 0){
     if($flag == 1){ 
       $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort>$sort_id order by  sort asc limit 0,1");
     }elseif($flag == 0){
       $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort<$sort_id order by sort desc limit 0,1"); 
     }

     if($address_query){
       $address_array = tep_db_fetch_array($address_query);
       $sort_id = $address_array['sort'];
     }

     $sort_id = $sort_id == '' ? tep_db_prepare_input($_POST['sort']) : $sort_id;
     tep_db_free_result($address_query);

     $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort=$sort_id");
   }else{
     $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where id=$id");

   }
   $address_array = tep_db_fetch_array($address_query);
   $cid = $address_array['id'];
   $title = $address_array['title'];
   $name = $address_array['name'];
   $type = $address_array['type'];
   $comment = $address_array['comment'];
   $type_comment = $address_array['type_comment'];
   $sort = $address_array['sort'];
   $limit = $address_array['num_limit'];
   $required = $address_array['required'];

   tep_db_free_result($address_query);
}

$type_start = $type;
$type = $type_value != '' ? $type_value : $type;

$prev = $sort;
$next = $sort;

$options_query = tep_db_query("select * from ". TABLE_ADDRESS ." where type='option' and status='0' order by sort");
$json_array = array();
$options_str = '';
$options_str_temp = '';
  while($options_array = tep_db_fetch_array($options_query)){
    $temp_array = unserialize($options_array['type_comment']); 
    $temp_arr = $temp_array;
    $temp_array = $temp_array['option_list'];
    $json_array[$options_array['id']] = $temp_array;
    if(isset($temp_arr['select_value']) && $temp_arr['select_value'] != ''){
      $options_str_temp .= '<option value="'. $options_array['id'] .'">'. $options_array['name'] .'</option>'; 
    }
  }
  tep_db_free_result($options_query); 
?>
<script type="text/javascript">
function check_option(value){
  var arr  = new Array();
<?php
  foreach($json_array as $key=>$value){
    echo 'arr['. $key .'] = new Array();';
    foreach($value as $k=>$val){

      echo 'arr['. $key .']['. $k .'] = "'. $val .'";';
    } 
  }  
?>
  var option_id = document.getElementById("parent");
  option_id.options.length = 0;
  len = arr[value].length;
  for(i = 0;i < len;i++){
    option_id.options[option_id.options.length]=new Option(arr[value][i], arr[value][i]);
  }
}

function check_option_show(value){
  var arr  = new Array();
  var arr_set = new Array();
<?php
  if($cid != ''){
    $option_show_query = tep_db_query("select * from ". TABLE_ADDRESS ." where id=$cid");
    $option_show_array = tep_db_fetch_array($option_show_query);
    $show_array = unserialize($option_show_array['type_comment']);
  }
  foreach($show_array as $show_key=>$show_value){
    echo 'arr["'. $show_key .'"] = new Array();';
    echo 'arr_set["'. $show_key .'"] = new Array();';

    foreach($show_value['option_list'] as $show_k=>$show_val){

      echo 'arr["'. $show_key .'"]['. $show_k .'] = "'. $show_val .'";';
    }


    echo 'arr_set["'. $show_key .'"][0] = "'. $show_value['select_value'] .'";';

  }  
   
?>
  html_str = '';
  var i = 0;
  var sel = '';

if(!arr[value]){
    html_str = '<tr id="o0"><td width="30%" height="30" align="left">&nbsp;初期選択肢</td><td width="41"></td><td><input type="text" name="option_comment[]" value=""><input type="radio" name="option_value" value="0" checked><input type="button" value="削除" onclick="check_del(\'0\');"></td></tr>';
    for(j=1;j<5;j++){
     
      html_str += '<tr id="o'+j+'"><td width="30%" height="30" align="left">&nbsp;選択肢</td><td width="41"></td><td><input type="text" name="option_comment[]" value=""><input type="radio" name="option_value" value="'+j+'"><input type="button" value="削除" onclick="check_del('+j+');"></td></tr>';

    }
    document.getElementById('num').value = 5;
}else{

  for(x in arr[value]){
    if(arr_set[value][0] == arr[value][x]){ sel = 'checked';
      show_title = '初期選択肢';
    
      html_str += '<tr id="o0"><td width="30%" height="30" align="left">&nbsp;'+show_title+'</td><td width="41"></td><td><input type="text" name="option_comment[]" value="'+arr[value][x]+'"><input type="radio" name="option_value" value="0" '+sel+'></td></tr>';
      i++;
      sel = '';
    }
  }
  for(x in arr[value]){
    if(arr_set[value][0] == arr[value][x]){continue;}
    show_title = '選択肢';
     
    html_str += '<tr id="o'+i+'"><td width="30%" height="30" align="left">&nbsp;'+show_title+'</td><td width="41"></td><td><input type="text" name="option_comment[]" value="'+arr[value][x]+'"><input type="radio" name="option_value" value="'+i+'" '+sel+'><input type="button" value="削除" onclick="check_del('+i+');"></td></tr>';
    i++;
    sel = '';
  }

  document.getElementById('num').value = i;
}
  
  $("#show_id").html('');
  $("#button_add").html(html_str); 

}

</script>
<form name="form" method="post" id="addressform" action="address.php">
<table border="0" width="100%" cellspacing="0" cellpadding="0" valign="top" bgcolor="yellow">

<?php
if($id == 0){
?>
 <tr><td bgcolor="#000000" class="dataTableHeadingContent">&nbsp;&nbsp;<?php echo TABLE_NEW.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right"><a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}else{
?>
  <tr><td bgcolor="#000000" class="dataTableHeadingContent">&nbsp;&nbsp;<?php echo $title.TABLE_TITLE_1;?></td><td bgcolor="#000000" align="right" class="dataTableHeadingContent" onmouseover="this.style.cursor=\'hand\'"><a href="javascript:show_text(<?php echo $id;?>,'','',<?php echo $prev;?>,0);"><font color="#FFFFFF"><?php echo TABLE_PREV;?></font></a>&nbsp;<a href="javascript:show_text(<?php echo $id;?>,'','',<?php echo $next;?>,1);"><font color="#FFFFFF"><?php echo TABLE_NEXT;?></font></a>&nbsp;<a href="javascript:hide_text();"><font color="#FFFFFF">X</font></a></td></tr>
<?php
}
?>
<tr><td>&nbsp;</td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_1;?></td><td><input type="text" name="title" id="title" value="<?php echo $title;?>"><span id="error_title"><font color="red">*</font></span><input type="hidden" name="cid" value="<?php echo $address_array['id'];?>"</td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_2;?></td><td><input type="text" name="name" id="name" value="<?php echo $name;?>"><span id="error_name"><font color="red">*</font></span></td></tr>
<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_3;?></td><td>
<select name="type" onchange="show_text(<?php echo $id;?>,'',this.value);">
<option value="text" <?php echo $type == 'text' ? 'selected' : '';?>>text</option>
<option value="textarea" <?php echo $type == 'textarea' ? 'selected' : '';?>>textarea</option>
<option value="option" <?php echo $type == 'option' ? 'selected' : '';?>>option</option>
</select>
</td></tr>
<?php

if($type == 'text'){


  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_4_1 .'</td><td><textarea name="comment" rows="6" cols="30">'. $comment .'</textarea></td></tr>';
  /*
  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_9 .'</td><td><input type="text" name="limit" value="'. $limit.'"></td></tr>';
  $required_true = $required == 'true' ? 'checked' : '';
  $required_false = $required == 'false' ? 'checked' : '';
  $required_true = $required == '' && $required_true == '' ? 'checked' : 'checked';
  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_10 .'</td><td><input type="radio" name="required" value="true" '. $required_true .'>True&nbsp;<input type="radio" name="required" value="false" '. $required_false .'>False</td></tr>';
   */

}elseif($type == 'textarea'){

 $type_comment_array = unserialize($type_comment);
 $rows = $type_comment_array['rows'];
 $rows = $rows == '' ? 1 : $rows;
 $type_limit = $type_comment_array['type_limit'];
 $set_value = $type_comment_array['set_value'];
 $select_all = $type_limit == 'all' ? 'selected' : '';
 $select_false_name = $type_limit == 'false_name' ? 'selected' : '';
 $select_english_num = $type_limit == 'english_num' ? 'selected' : '';
 $select_english = $type_limit == 'english' ? 'selected' : '';
 $select_num = $type_limit == 'num' ? 'selected' : '';
 $select_email = $type_limit == 'email' ? 'selected' : '';

 echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_6 .'</td><td><input type="text" name="comment" value="'. $comment .'"></td></tr>';
  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_7 .'</td><td><input type="text" name="option_comment[]" value="'. $rows .'"></td></tr>';
  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_8 .'</td><td>
    <select name="text_type">
    <option value="all" '. $select_all .'>全て</option>
    <option value="false_name" '. $select_false_name .'>カナ</option>
    <option value="english_num" '. $select_english_num .'>英数</option>
    <option value="english" '. $select_english .'>英</option>
    <option value="num" '. $select_num .'>数</option>
    <option value="email" '. $select_email .'>Email</option>
    </td></tr>';
  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_9 .'</td><td><input type="text" name="limit" value="'. $limit.'"></td></tr>';
  $required_true = $required == 'true' ? 'checked' : '';
  $required_false = $required == 'false' ? 'checked' : '';
  $required_true = $required == '' && $required_true == '' ? 'checked' : 'checked';

  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_10 .'</td><td><input type="radio" name="required" value="true" '. $required_true .'>True&nbsp;<input type="radio" name="required" value="false" '. $required_false .'>False</td></tr>';
  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_11 .'</td><td><input type="text" name="option_comment[]" value="'. $set_value .'"></td></tr>';

}else{

  $type_comment_array = unserialize($type_comment);
  if($type_start == 'option'){ 
    if(!isset($type_comment_array['select_value']) && $type_comment_array['select_value'] == ''){ 
      $n = 0; 
      foreach($type_comment_array as $key=>$value){
        if($n == 0){ 
          $select_value = $type_comment_array[$key]['select_value'];
          $option_list = $type_comment_array[$key]['option_list'];
          $parent_id = $type_comment_array[$key]['parent_id'];
          $parent_name = $type_comment_array[$key]['parent_name'];
        }
        $n++;
     }
    }else{
      $select_value = $type_comment_array['select_value'];
      $option_list = $type_comment_array['option_list']; 
   } 
 }
    
  $options_str = '<select name="parent_option[]" onchange="check_option(this.value);">';

  if($parent_id == ''){ 
    $options_str .= '<option value="0">--</option>';
    $options_string = '<option value="0">--</option>';
  }else{
    $parent_query = tep_db_query("select * from ". TABLE_ADDRESS ." where id=".$parent_id);
    $parent_row = tep_db_fetch_array($parent_query);

    $parent_type_comment = unserialize($parent_row['type_comment']);
    $parent_type_array = $parent_type_comment['option_list'];
    tep_db_close();
    $options_str_1 = '<option value="'. $parent_id .'">'. $parent_row['name'] .'</option>';
    $options_str .= $options_str_1;
    $options_string .= '<option value="'. $parent_name .'">'. $parent_name .'</option>'; 

    foreach($parent_type_array as $value){

      if($parent_name != $value){
        $options_string .= '<option value="'. $value .'">'. $value .'</option>'; 
      }
    }
  }
  $options_str_temp = str_replace($options_str_1,'',$options_str_temp);
  $options_str .= $options_str_temp;
  $options_str .= '</select>'; 

  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_6     .'</td><td><input type="text" name="comment" value="'. $comment .'"></td></tr>';
  //option 所属上一级

  if($parent_id != '' || $id == 0){
    echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_12_1 .'</td><td>'. $options_str .'&nbsp;<select name="parent_option[]" id="parent" onchange="check_option_show(this.value);">'. $options_string .'</select></td></tr>';
  }
  echo '<tr><td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_id">';
  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_12 .'</td><td><input type="text" name="option_comment[]" value="'. $select_value .'"><input type="radio" name="option_value" value="0" checked></td></tr>';
  $option_num = 0;
  unset($option_list[array_search($select_value,$option_list)]);
  $option_list = array_values($option_list);
  foreach($option_list as $key=>$value){
    $key++;
    if($value != ''){
      $option_num++;
      echo '<tr id="o'.$option_num.'"><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_13 .'</td><td><input type="text" name="option_comment[]" value="'. $value .'"><input type="radio" name="option_value" value="'.$option_num.'"><input type="button" value="'. TABLE_BUTTON_DEL .'" onclick="check_del('.$option_num.');"></td></tr>';
    }
  } 
  $option_num++;
  if(empty($option_list)){
     for($i = 1;$i < 5;$i++){
       echo '<tr id="o'.$i.'"><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_13 .'</td><td><input type="text" name="option_comment[]" value=""    ><input type="radio" name="option_value" value="'.$i.'"><input type="button" value="'. TABLE_BUTTON_DEL .'" onclick="check_del('.$i.');"></td></tr>';
     }
     $option_num = 5;
  }
  echo '</table></td></tr>';
  echo '<tr><td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0"><div id="button_add"></div></table></td></tr>'; 
  echo '<tr><td width="30%" height="31" align="right" colspan="2"><input type="hidden" id="num_1" value="'.$option_num.'"><input type="hidden" id="num" value="'.$option_num.'"><input type="button" value="'. TABLE_ADD .'" onclick="check_add();">&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>'; 
  $required_true = $required == 'true' ? 'checked' : '';
  $required_false = $required == 'false' ? 'checked' : '';
  $required_true = $required == '' && $required_true == '' ? 'checked' : 'checked';

  echo '<tr><td width="30%" height="30" align="left">&nbsp;'. TABLE_LIST_10 .'</td><td><input type="radio" name="required" value="true" '. $required_true .'>True&nbsp;<input type="radio" name="required" value="false" '. $required_false .'>False</td></tr>';

 
}

?>

<tr><td width="30%" height="30" align="left">&nbsp;<?php echo TABLE_LIST_5;?></td><td><input type="text" name="sort" value="<?php echo $sort;?>"></td></tr>
<tr><td width="30%" height="30" colspan="2" align="right"><input type="button" name="new" value="<?php echo TABLE_BUTTON_SUBMIT;?>" onclick="show_text(0,'','text');">&nbsp;<input type="button" name="save" value="<?php echo TABLE_BUTTON_SAVE;?>" onclick="if(check_form()){check('save');}else{return check_form();}">&nbsp;

<?php
if($id != 0){
?>
<input type="button" name="del" value="<?php echo TABLE_BUTTON_DEL;?>" onclick="if(confirm('このレコードを削除してもよろしいですか？')){check('del');}else{return false;}">
<?php
}
?>
&nbsp;</td></tr>
</table>

</form>

