<?php
/*
 * 住所作成 Ajax
 */
require('includes/application_top.php');
$id = tep_db_prepare_input($_POST['id']);
$type_value = tep_db_prepare_input($_POST['type']);
$sort_id = tep_db_prepare_input($_POST['sort']);
$flag = tep_db_prepare_input($_POST['flag']);
$_title = tep_db_prepare_input($_POST['title']);
$_name = tep_db_prepare_input($_POST['name']);
$_comment = tep_db_prepare_input($_POST['comment']);

if(isset($id) && $id != 0){
  if(isset($sort_id) && $sort_id != ''){
     if($flag == 1){ 
       $address_sort_query = tep_db_query("select count(*) total,max(id) maxid from ". TABLE_ADDRESS ." where sort=$sort_id");
       $address_sort_array = tep_db_fetch_array($address_sort_query);
       if($address_sort_array['total'] > 1){
         if($id < $address_sort_array['maxid']){
           $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort=$sort_id and id>$id order by sort asc,id asc");
         }else{
           $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort>$sort_id order by sort asc limit 0,1");
         }
       }else{
         $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort>$sort_id order by sort asc limit 0,1");
       }
     }elseif($flag == 0){
       $address_sort_query = tep_db_query("select count(*) total,min(id) minid from ". TABLE_ADDRESS ." where sort=$sort_id");
       $address_sort_array = tep_db_fetch_array($address_sort_query);
       if($address_sort_array['total'] > 1){
         if($id > $address_sort_array['minid']){
            
           $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort=$sort_id and id<$id order by sort desc,id desc");
         }else{
           $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort<$sort_id order by sort desc limit 0,1");  
         }
       }else{
         $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where sort<$sort_id order by sort desc,id desc limit 0,1");
       }
     } 
  }

     if($address_query){
       $address_array = tep_db_fetch_array($address_query);
       $sort_id = $address_array['id'];
     }

     tep_db_free_result($address_query);
      
     $add_sort_query = tep_db_query("select max(sort) maxsort,min(sort) minsort from ". TABLE_ADDRESS);
     $add_sort_array = tep_db_fetch_array($add_sort_query);
     $maxsort = $add_sort_array['maxsort'];
     $minsort = $add_sort_array['minsort'];
     tep_db_free_result($add_sort_query);
     $address_sort_max_query = tep_db_query("select max(id) maxid from ". TABLE_ADDRESS ." where sort=$maxsort");
     $address_sort_max_array = tep_db_fetch_array($address_sort_max_query); 
     $maxid = $address_sort_max_array['maxid'];
     tep_db_free_result($address_sort_max_query);
     $address_sort_min_query = tep_db_query("select min(id) minid from ". TABLE_ADDRESS ." where sort=$minsort");
     $address_sort_min_array = tep_db_fetch_array($address_sort_min_query); 
     $minid = $address_sort_min_array['minid'];
     tep_db_free_result($address_sort_min_query);

     if(isset($flag) && $flag != ''){
       $id = $sort_id != '' ? $sort_id : $id;
     } 
       
   
   $address_query = tep_db_query("select * from ". TABLE_ADDRESS ." where id=$id");
   $address_array = tep_db_fetch_array($address_query);
   $cid = $address_array['id'];
   $title = $address_array['title'];
   $name = $address_array['name'];
   $type = $address_array['type'];
   $comment = $address_array['comment'];
   $comment_text = $address_array['comment'];
   $type_comment = $address_array['type_comment'];
   $sort = $address_array['sort'];
   $limit = $address_array['num_limit']; 
   $limit_min = $address_array['num_limit_min'];
   $required = $address_array['required'];
   $show_title = $address_array['show_title'];

   tep_db_free_result($address_query);     
   
}

$sort = $sort == '' ? 0 : $sort;

$type_start = $address_array['type'];
$type = $type_value != '' ? $type_value : $type;

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
    html_str = '<tr id="o0"><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;初期選択肢</td><td><input type="text" name="option_comment[]" value=""><input type="radio" name="option_value" value="0" checked><input type="button" value="削除" onclick="check_del(\'0\');"></td></tr>';
    for(j=1;j<5;j++){
     
      html_str += '<tr id="o'+j+'"><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;選択肢</td><td><input type="text" name="option_comment[]" value=""><input type="radio" name="option_value" value="'+j+'"><input type="button" value="削除" onclick="check_del('+j+');"></td></tr>';

    }
    document.getElementById('num').value = 5;
}else{

  for(x in arr[value]){
    if(arr_set[value][0] == arr[value][x]){ sel = 'checked';
      show_title = '初期選択肢';
    
      html_str += '<tr id="o0"><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'+show_title+'</td><td><input type="text" name="option_comment[]" value="'+arr[value][x]+'"><input type="radio" name="option_value" value="0" '+sel+'></td></tr>';
      i++;
      sel = '';
    }
  }
  for(x in arr[value]){
    if(arr_set[value][0] == arr[value][x]){continue;}
    show_title = '選択肢';
     
    html_str += '<tr id="o'+i+'"><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'+show_title+'</td><td><input type="text" name="option_comment[]" value="'+arr[value][x]+'"><input type="radio" name="option_value" value="'+i+'" '+sel+'><input type="button" value="削除" onclick="check_del('+i+');"></td></tr>';
    i++;
    sel = '';
  }

  document.getElementById('num').value = i;
}
  
  $("#show_id").html('');
  $("#button_add").html(html_str); 

}

</script>
<table border="0" width="100%" cellspacing="0" cellpadding="2" valign="top" class="campaign_top">
<?php
if($id == 0 || $maxid == $minid){
?>
 <tr><td width="20"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?></td><td><b><?php echo TABLE_NEW.TABLE_TITLE_1;?></b></td><td align="right"><a href="javascript:hide_text();">X</a></td></tr>
<?php
}else{
  $prev_str = '';
  $next_str = '';
  if($sort == $maxsort && $id == $maxid){

    $prev_str = '<a href="javascript:show_text('. $id .',\'\',\'\','. $sort .',0);">'. TABLE_PREV .'</a>';

  }elseif($sort == $minsort && $id == $minid){
 
    $next_str = '<a href="javascript:show_text('. $id .',\'\',\'\','. $sort .',1);">'. TABLE_NEXT .'</a>';
  }else{
    
    $prev_str = '<a href="javascript:show_text('. $id .',\'\',\'\','. $sort .',0);">'. TABLE_PREV .'</a>';
    $next_str = '<a href="javascript:show_text('. $id .',\'\',\'\','. $sort .',1);">'. TABLE_NEXT .'</a>';
  }
?>
  <tr><td width="20"><?php echo tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO); ?></td><td><b><?php echo $title.TABLE_TITLE_1;?></b></td><td align="right" onmouseover="this.style.cursor=\'hand\'"><?php echo $prev_str;?>&nbsp;<?php echo $next_str;?>&nbsp;<a href="javascript:hide_text();">X</a></td></tr>
<?php
}
?>
</table>
<form name="form" method="post" id="addressform" action="address.php">
<table border="0" width="100%" cellspacing="0" cellpadding="2" valign="top" bgcolor="yellow" class="campaign_body">

<?php
$title = $_title != '' ? $_title : $title;
$name = $_name != '' ? $_name : $name;
$comment = $_comment != '' ? $_comment : $comment;
?>
<tr><td width="30%"align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_1;?></td><td><input type="text" name="title" id="title" class="option_text" value="<?php echo $title;?>">&nbsp;<font color="red"><?php echo TABLE_REQUIRED;?></font><br><span id="error_title"></span><input type="hidden" id="cid" name="cid" value="<?php echo $address_array['id'];?>"</td></tr>
<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_2;?></td><td><input type="text" name="name" id="name" class="option_text" value="<?php echo $name;?>">&nbsp;<font color="red"><?php echo TABLE_REQUIRED;?></font><br><span id="error_name"></span></td></tr>
<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_3;?></td><td>

<select name="type" onchange="show_text(<?php echo $id;?>,'',this.value,'','',document.getElementById('title').value,document.getElementById('name').value,document.getElementById('comment').value);">
<option value="text" <?php echo $type == 'text' ? 'selected' : '';?>><?php echo TABLE_TEXT;?></option>
<option value="textarea" <?php echo $type == 'textarea' ? 'selected' : '';?>><?php echo TABLE_TEXTAREA;?></option>
<option value="option" <?php echo $type == 'option' ? 'selected' : '';?>><?php echo TABLE_SELECT;?></option>
</select>
</td></tr>
<?php

if($type == 'text'){


  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_4_1 .'</td><td><input type="hidden" id="comment" value="'.$comment.'"><textarea name="comment" rows="6" cols="30" class="option_text">'. $comment_text .'</textarea></td></tr>';
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
 $limit = $limit == '' ? 0 : $limit;
 $limit_min = $limit == '' ? 0 : $limit_min;

 echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_6 .'</td><td><input type="text" id="comment" name="comment" class="option_text" value="'. $comment .'"></td></tr>';
  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_7 .'</td><td><input type="text" name="option_comment[]" value="'. $rows .'" style="text-align: right;"></td></tr>';
  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_8 .'</td><td>
    <select name="text_type">
    <option value="all" '. $select_all .'>全て</option>
    <option value="false_name" '. $select_false_name .'>カナ</option>
    <option value="english_num" '. $select_english_num .'>英数</option>
    <option value="english" '. $select_english .'>英</option>
    <option value="num" '. $select_num .'>数</option>
    <option value="email" '. $select_email .'>Email</option>
    </select><br>'. TABLE_PROMPT_1 .'</td></tr>';
  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_9 .'</td><td><input type="text" name="limit" value="'. $limit.'" style="text-align: right;"><br>'. TABLE_PROMPT_2 .'</td></tr>';
  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_9_1 .'</td><td><input type="text" name="limit_min" value="'. $limit_min.'" style="text-align: right;"><br>'. TABLE_PROMPT_2_1 .'</td></tr>';
  $required_true = $required == 'true' ? 'checked' : '';
  $required_false = $required == 'false' ? 'checked' : '';
  $required_true = $required == '' && $required_true == '' ? 'checked' : 'checked';

  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_10 .'</td><td><input type="radio" name="required" value="true" '. $required_true .'>True&nbsp;<input type="radio" name="required" value="false" '. $required_false .'>False</td></tr>';
  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_11 .'</td><td><input type="text" name="option_comment[]" class="option_text" value="'. $set_value .'"></td></tr>';

}else{

  $type_comment_array = unserialize($type_comment);
  if($type_start == 'option'){ 
    if(!isset($type_comment_array['select_value']) && $type_comment_array['select_value'] == ''){ 
      $n = 0; 
      foreach($type_comment_array as $key=>$value){
        if($n == 0){ 
          //$select_value = $type_comment_array[$key]['select_value'];
          //$option_list = $type_comment_array[$key]['option_list'];
          $parents_id = $type_comment_array[$key]['parent_id'];
          //$parent_name = $type_comment_array[$key]['parent_name'];
        }else{
          break;
        }
        $n++;
      }
      $parents_query = tep_db_query("select * from ". TABLE_ADDRESS ." where id=".$parents_id);
      $parents_row = tep_db_fetch_array($parents_query);
      tep_db_free_result($parents_query);
      $parents_type_comment = unserialize($parents_row['type_comment']);
      foreach($type_comment_array as $key=>$value){
        if($parents_type_comment['select_value'] == $key){ 
          $select_value = $type_comment_array[$key]['select_value'];
          $option_list = $type_comment_array[$key]['option_list'];
          $parent_id = $type_comment_array[$key]['parent_id'];
          $parent_name = $type_comment_array[$key]['parent_name'];
        }
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
    //$options_string .= '<option value="'. $parent_name .'">'. $parent_name .'</option>'; 

    foreach($parent_type_array as $value){

      //if($parent_name != $value){
        $selected = $value == $parent_type_comment['select_value'] ? ' selected' : '';
        $options_string .= '<option value="'. $value .'"'. $selected .'>'. $value .'</option>'; 
      //}
    }
  }
  $options_str_temp = str_replace($options_str_1,'',$options_str_temp);
  $options_str .= $options_str_temp;
  $options_str .= '</select>'; 

  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_6     .'</td><td><input type="text" id="comment" name="comment" class="option_text" value="'. $comment .'"></td></tr>';
  //option 所属上一级

  if($parent_id != '' || $id == 0){
    echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_12_1 .'</td><td>'. $options_str .'&nbsp;<select name="parent_option[]" id="parent" onchange="check_option_show(this.value);">'. $options_string .'</select></td></tr>';
  }
  echo '<tr><td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0" id="show_id">';
  $select_value = $type_start == 'option' ? $select_value : '';
  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_12 .'</td><td><input type="text" name="option_comment[]" value="'. $select_value .'"><input type="radio" name="option_value" value="0" checked></td></tr>';
  $option_num = 0;
  unset($option_list[array_search($select_value,$option_list)]);
  $option_list = array_values($option_list);
  foreach($option_list as $key=>$value){
    $key++;
    if($value != ''){
      $option_num++;
      echo '<tr id="o'.$option_num.'"><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_13 .'</td><td><input type="text" name="option_comment[]" value="'. $value .'"><input type="radio" name="option_value" value="'.$option_num.'"><input type="button" value="'. TABLE_BUTTON_DEL .'" onclick="check_del('.$option_num.');"></td></tr>';
    }
  } 
  $option_num++;
  if(empty($option_list)){
     for($i = 1;$i < 5;$i++){
       echo '<tr id="o'.$i.'"><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_13 .'</td><td><input type="text" name="option_comment[]" value=""    ><input type="radio" name="option_value" value="'.$i.'"><input type="button" value="'. TABLE_BUTTON_DEL .'" onclick="check_del('.$i.');"></td></tr>';
     }
     $option_num = 5;
  }
  echo '</table></td></tr>';
  echo '<tr><td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="0" id="button_add"></table></td></tr>'; 
  echo '<tr><td width="30%">&nbsp;</td><td><input type="hidden" id="num_1" value="'.$option_num.'"><input type="hidden" id="num" value="'.$option_num.'"><input type="button" value="'. TABLE_ADD .'" onclick="check_add();">&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>'; 
  $required_true = $required == 'true' ? 'checked' : '';
  $required_false = $required == 'false' ? 'checked' : '';
  $required_true = $required == '' && $required_true == '' ? 'checked' : 'checked';

  echo '<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;'. TABLE_LIST_10 .'</td><td><input type="radio" name="required" value="true" '. $required_true .'>True&nbsp;<input type="radio" name="required" value="false" '. $required_false .'>False</td></tr>';

 
}

$sort = $id == 0 ? 1000 : $sort;
$checkbox_checked = $show_title == 1 ? ' checked' : '';
$checkbox_checked_false = $show_title == 0 ? ' checked' : '';
?>

<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_LIST_5;?></td><td><input type="text" name="sort" value="<?php echo $sort;?>" style="text-align: right;"></td></tr>

<?php
if($type != 'text'){
?>
<tr><td width="30%" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo TABLE_SHOW_TITLE;?></td><td><input type="radio" name="show_title" value="1" style="text-align: right;"<?php echo $checkbox_checked;?>>True&nbsp;<input type="radio" name="show_title" value="0" style="text-align: right;"<?php echo $checkbox_checked_false;?>>False</td></tr>
<?php
}
?>
<tr><td width="30%">&nbsp;</td><td style="padding-left:10%;"><input type="button" name="new" value="<?php echo TABLE_BUTTON_SUBMIT;?>" onclick="show_text(0,'','text');">&nbsp;<input type="button" name="save" value="<?php echo TABLE_BUTTON_SAVE;?>" onclick="if(check_form()){check('save');}else{return check_form();}">&nbsp;

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
