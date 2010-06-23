<?php 

  require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();

$cPath = $_GET['cpath'];
$cID   = $_GET['cid'];  

switch ($_GET['action']){
    case update:
      foreach ($_POST['kakaku'] as $key => $kakaku) {
        if (tep_db_num_rows(tep_db_query("select * from set_menu_list where categories_id ='".$cID."' and products_id='".$key."'"))) {
          tep_db_perform('set_menu_list', array(
            'kakuukosuu' => tep_db_prepare_input($_POST['kakuukosuu'][$key]),
            'kakaku' => tep_db_prepare_input($kakaku)
          ), 'update', 'categories_id=\'' . tep_db_prepare_input($cID) . '\' and products_id = \'' . tep_db_prepare_input($key) . '\'');
        } else {
          tep_db_perform('set_menu_list', array(
            'kakuukosuu' => tep_db_prepare_input($_POST['kakuukosuu'][$key]),
            'kakaku' => tep_db_prepare_input($kakaku),
            'categories_id' => tep_db_prepare_input(tep_db_prepare_input($cID)),
            'products_id' => tep_db_prepare_input(tep_db_prepare_input($key)),
          ));
        }
      }
      break;
    case cleate_select:
    $proid=$_POST['select_data'];
    foreach($proid as $val){
      $proid_data[]=$val; 
    }
    
    $kakuu=$_POST['kakuu'];
    foreach($kakuu as $val){
      $kakuu_data[]=$val;
    }
    $kakaku=$_POST['kakaku'];
    foreach($kakaku as $val){
      $kakaku_data[]=$val;
    }
        /*
        *上面三个选好把form传递的值分别存入 $proid_data[] $kakuu_data[] $kakaku_data[]
        */
    $cnt_cnt=count($proid_data);
    $res1=tep_db_query("select * from set_menu_list where categories_id='".$cID."' ORDER BY set_list_id ASC");
    while($col=tep_db_fetch_array($res1)){
      $list_id[]=$col['set_list_id'];
    }
    /*
    *通过cID从set_menu_list查找set_list_id 按照升序存入$list_id[]
    */
    $kakuu_cnt=0;
    $kakaku_cnt=0;
    
    for($i=0;$i<$cnt_cnt;$i++){

          $res_cnt=tep_db_query("select count(*) list_cnt from set_menu_list where categories_id='".$cID."' AND set_list_id='".$list_id[$i]."'");
          $col_cnt=tep_db_fetch_array($res_cnt);
          $cnt_data=$col_cnt['list_cnt'];
          /*
           * 根据cID 和 set_list_id 在set_menu_list查找数据 将查找的结果条数 存入$cnt_data
           */
          if($cnt_data > 0 ){
            //如果有数据
            if(tep_db_prepare_input($proid_data[$i]) !=0){
              //判断表单select值如果不是0 执行修改
              tep_db_query("update set_menu_list set products_id='".$proid_data[$i]."',kakuukosuu='".$kakuu_data[$kakuu_cnt]."',kakaku='".$kakaku_data[$kakaku_cnt]."' where set_list_id=".$list_id[$i]."");
              $kakuu_cnt++;
              $kakaku_cnt++;  
            }
          
          }else{
            //如果 无数据执行insert
            tep_db_query("insert into set_menu_list (categories_id,products_id,kakuukosuu,kakaku) values ('".$cID."','".$proid_data[$i]."','".$kakuu_data[$kakuu_cnt]."','".$kakaku_data[$kakaku_cnt]."')");    
            $kakuu_cnt++;
            $kakaku_cnt++;
          }

    }
    //header("Location:categories.php?cPath=".$cID);
    
    break;
    
}

$res=tep_db_query("select oroshi_name from set_oroshi_names n,set_oroshi_categories oc where n.oroshi_id=oc.oroshi_id and oc.categories_id='".$cPath."' ORDER BY n.oroshi_id ASC");
$products = tep_get_products_by_categories_id($cID);
//echo count($products);

$cnt=0;
while($col=tep_db_fetch_array($res)){
  $o_name[]=$col['oroshi_name'];
  $cnt++;
}
//$cnt・・・・卸業者の数  
//$cnt 通过传递的cPath 查找到的卸業者的总数
//$count[0]・・・・基準データの件数   
//$o_name 通过传递的cPath 查找到的卸業者的名称数组 与cnt对应
$res=tep_db_query("select * from products_to_categories where categories_id='".$cID."'");
$cnt2=0;
while($col=tep_db_fetch_array($res)){
  $cid_list[]=$col['products_id'];
  $cnt2++;
}
/*
 *通过传递的cid 在表products_to_categories中查找对应cid(类别ID)
 *cnt2产品ID的总数(对应该类别ID)
 *cid_list对应cID(类别ID)的 products_id(产品ID)的列表
 */
$cnt3=0;
for($i=0;$i < $cnt2;$i++){
  $res_list =tep_db_query("select * from products_description where products_id='".$cid_list[$i]."'");
  $col_list[]=tep_db_fetch_array($res_list);
  $cnt3++;
}
/*
 *通过cid_list(产品id数组)cnt2(产品id总数)
 *获取col_list(产品信息数组)cnt3(产品信息总数)
 */
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
  <title>リスト表示</title>
  <script type="text/javascript">
  var trader_input_obj=document.getElementsByName("kakaku[]");
var imaginary_input_obj=document.getElementsByName("kakuu[]");
var select_obj=document.getElementsByName("select_data[]");
function ctrl_keydown(evt,id_var,num){ //id_ver=ID　num＝現在の番号
  var n=parseInt(num);
  var n2=parseInt(trader_input_obj.length);//フォームの数
  var a=parseInt(1);
  var id=id_var;
  var e = (evt)?evt:((window.event)?window.event:'');
  var  keychar = e.keyCode?e.keyCode:e.which;
  switch(keychar) { 
  case 13:
    n2 -=a;//フォームの数-１
    if((id == "TRADER")&&(n < n2)){
        
      n +=a;
      trader_input_obj[n].focus();
          
    }else if((id == "IMAGINARY_INPUT")&&(n < n2)){
          
      n += a;
      imaginary_input_obj[n].focus();
          
    }
    break;
      
  case 37:　//キーボードの十字キーの←

      if(id == "TRADER"){
        
        imaginary_input_obj[n].focus();
            
      }
        
    break;
  case 38:　//キーボードの十字キーの↑

      if((id == "TRADER")&&(n != 0)){
        
        n -= a;
        trader_input_obj[n].focus();
          
      }else if((id == "IMAGINARY_INPUT")&&(n != 0)){
          
        n -= a;
        imaginary_input_obj[n].focus();
          
      }
       
    break;
  case 39:　//キーボードの十字キーの→

      if(id == "IMAGINARY_INPUT"){
        
        trader_input_obj[n].focus();
        
      }
    break;
  case 40: 　//キーボードの十字キーの↓
      n2 -=a;//フォームの数-１
    if((id == "TRADER")&&(n < n2)){
        
      n +=a;
          
      trader_input_obj[n].focus();
          
    }else if((id == "IMAGINARY_INPUT")&&(n < n2)){
          
      n += a;
      imaginary_input_obj[n].focus();
          
    }
    break;
  }
      
}

/*function event_onblur(val,cnt){
  if(val=="IMAGINARY_INPUT"){
  opener.imaginary_input_obj[cnt].value=imaginary_input_obj[cnt].value;
  }else if(val =="TRADER"){
  opener.trader_input_obj[cnt].value=trader_input_obj[cnt].value;
  }
  }*/
var hako_list = new Array;
function menuLink(val,num,cnt){
  var index=select_obj[num].selectedIndex;
  
  
  for(var i=0;i<cnt;i++){
    if(index != hako_list[num] && hako_list[num] != null){
      select_obj[i].options[hako_list[num]].disabled=false;
    }
      
    if(num != i && index !=0){
      select_obj[i].options[index].disabled=true;
    }

  }

  trader_input_obj[num].disabled=false;
  imaginary_input_obj[num].disabled=false;
  hako_list[num]=index;
}

function select_reset(cnt,cnt2){
  for(var i=0;i<cnt;i++){
    for(var j=0;j<=cnt2;j++){
      select_obj[i].options[j].disabled=false;
      
    }
    imaginary_input_obj[i].value="";
    select_obj[i].options[0].selected = "selected";
    imaginary_input_obj[i].disabled=true;
    trader_input_obj[i].disabled=true;
  hako_list[i]=0;
  }
}


/*var select_menu_list;
  function ajax_menu_cleate(cid){
  var request = new XMLHttpRequest();
  var send_url="set_ajax.php?action=cleate_menu&cid="+cid;//url=action=hoge&cpath=    まで

  request.open("GET",send_url, true); //非同期通信
  request.onreadystatechange = function() {
  if (request.readyState == 4) {
  var xmlDoc = request.responseXML;
  select_menu_list = xmlDoc.getElementsByTagName("menu");
  }
  }
  request.send(null);
  }
  var index=select_obj[i].selectedIndex;
  if(select_obj[i].options[index].value != "null"){
  var select_datas[] = select_obj[i].options[index].value;
  }
  for(var i=0;i<cnt;i++){
  if(imaginary_input_obj[i].value != ""){
  kakuu[] =imaginary_input_obj[i].value;
  }
  }*/
function list_submit(){
  <!--//判断是否更新flg为弹出的选择框返回的值
  -->
  var flg=confirm("リスト更新");
  if(flg){
    window.document.listform.submit();
      
  }else{
    alert("更新をキャンセルしました");
  }
}

function onload_menu(cnt){
  <!--//使select不起作用 是select 的options的值为0的 kakaku 和 kakuu禁用 
  -->
  for(var i=0;i<cnt;i++){
    var index=select_obj[i].selectedIndex;
    hako_list[i]=index;
    if(select_obj[i].options[index].value == "0"){
      imaginary_input_obj[i].disabled=true;
      trader_input_obj[i].disabled=true;
    }else{
      for(var j=0;j<cnt;j++){
        if(select_obj[i].options[index]!=select_obj[j].options[index]){
          select_obj[j].options[index].disabled=true;
          
        } 
      }
    }
  }
}

function cenge_data(retu,gyou){
  var hako = document.getElementById("list_"+retu+"_"+gyou).innerHTML;
  //alert(hako);

  if(gyou!=0){
    var a=gyou-1;
    document.getElementById("list_"+retu+"_"+gyou).innerHTML=document.getElementById("list_"+retu+"_"+a).innerHTML;
    document.getElementById("list_"+retu+"_"+a).innerHTML=hako;
  }
}


</script>
</head>
<?php 
$cr = array("\r\n", "\r");   // 改行コード置換用配
for($i=0;$i<=$cnt;$i++){
  $res = tep_db_query("select n.*,d.* from set_oroshi_datas d,set_oroshi_names n where n.oroshi_id=d.oroshi_id and d.parent_id='".$cPath."' && n.oroshi_name='".$o_name[$i]."' ORDER BY d.list_id DESC");
  $col[] = tep_db_fetch_array($res);
  
  $col[$i]['datas'] = trim($col[$i]['datas']);         // 文頭文末の空白を削除
  $col[$i]['datas'] = str_replace($cr, "\n",$col[$i]['datas']);  // 改行コードを統一
  $lines[] = explode("\n", $col[$i]['datas']);
  $count[]=count($lines[$i]);
} 
/*
 *根据传递的cpath和查询出来的o_name cnt 查看表set_oroshi_datas
 *把查询结果 分别放在 col[](一行数据) lines[]((二维数组每行的datas(每一个换行为一个lines[][])) count[](每行的长度)
 */
for($n=0;$n<$cnt;$n++){
  if($count[0]<=$count[$n]){
    $count[0]=$count[$n];
  }
}
//把count[]里最大数放到 count[0]
?>

<?php 

$res=tep_db_query("select count(*) list_cnt from set_menu_list where categories_id='".$cID."'");
$col_cnt=tep_db_fetch_array($res);
$list_cnt=$col_cnt['list_cnt'];
//list_cnt 是表set_menu_list中 categories_id = cID 的数据数量(cID是传递过来的值)

$res=tep_db_query("select * from set_menu_list where categories_id='".$cID."' ORDER BY set_list_id ASC");
$i_cnt=0;
while($col=tep_db_fetch_array($res)){
  $menu_datas[$i_cnt][0]=$col['products_id'];
  $menu_datas[$i_cnt][1]=$col['kakuukosuu'];
  $menu_datas[$i_cnt][2]=$col['kakaku'];
  $i_cnt++;
}
//把set_menu_list 的值存入menu_datas(二维数组) i_cnt(查找出来的数据数量)可以用来便利该数组
?>

<body  onload="onload_menu(<?php echo $count[0]; ?>)">
<form name='listform' method='POST' action="list_display.php?action=cleate_select&cid=<?php echo $cID; ?>&cpath=<?php echo $cPath; ?>" onSubmit="return false">
  <table id="table_menu" border="1"　>
  <tr>
  <?php
  for($i=0;$i<$cnt;$i++){
    $res=tep_db_query("select d.set_date from set_oroshi_datas d,set_oroshi_names n where n.oroshi_id=d.oroshi_id and d.parent_id='".$cPath."' && n.oroshi_name='".$o_name[$i]."' ORDER BY d.list_id DESC");
    $col=tep_db_fetch_array($res);
    //根据查询出的o_name 和传递值 cPath 在set_oroshi_datas 表中查找 set_date按照list_id 降序输出
    echo "<td align='center'>".$col['set_date']."</td>";
  }
?>
</tr>

<tr>
<?php
for($i=0;$i<$cnt;$i++){
  $res=tep_db_query("select n.oroshi_name from set_oroshi_datas d, set_oroshi_names n where n.oroshi_id=d.oroshi_id and d.parent_id='".$cPath."' && n.oroshi_name='".$o_name[$i]."' ORDER BY d.list_id DESC");
  $col=tep_db_fetch_array($res);
  //根据查询出的o_name 和传递值 cPath 在set_oroshi_datas 表中查找 oroshi_name按照list_id 降序输出
  echo "<th align='center'>".$col['oroshi_name']."</th>";

} 
echo "<th align='center'>商品選択</th>";
echo "<th align='center'>個数/架空</th>";
echo "<th align='center'>価格/業者</th>";
?>
</tr>
  <?php
  //该表格提交是 会继续把cid和cpath传递
  
  for($i=0;$i < $count[0];$i++){
  echo "<tr id=color_".$i.">";
  for($j=0;$j<$cnt;$j++){
      //$list[$j][$i]=$lines[$j][$i];<input type='button' name='ue[]' value='↑' onClick='cenge_data(".$j.",".$i.")'>
      if($i ==0){
        echo "<td><div id='list_".$j."_".$i."'>".$lines[$j][$i]."</div></td>";
      }else{
        echo "<td><input type='button' name='ue[]'  value='↑' onClick='cenge_data(".$j.",".$i.")'><span id='list_".$j."_".$i."'>".$lines[$j][$i]."</span></td>";
      }
      //循环输出lines(由于conut[0]在count中是最大的 所以会输出 lines[j]数组内的所有数据
      echo "<input type='hidden' value='".$lines[$j][$i]."' name='list[]'>";
    echo "<input type='hidden' value='".$j."' name='gyou_cnt'>";
  }
?>
<td>
<?php
  echo "<select name='select_data[]' onChange='menuLink(this.options[this.selectedIndex].value,".$i.",".$count[0].")'>";
      for($n=0;$n<$cnt3;$n++){
        if($n==0){
          echo "<option value='0'>--選択してください--</option>"; 
          //第一次输出一个 提示下拉框
        }
        if($menu_datas[$i][0] == $col_list[$n]['products_id']){
          echo "<option value=".$col_list[$n]['products_id']." SELECTED>".$col_list[$n]['products_name']."</option>";
        }else{
          echo "<option value=".$col_list[$n]['products_id'].">".$col_list[$n]['products_name']."</option>";
        }
        /*判断本次循环内的menu_datas[$i][0] 是否与$col_list[$n]['products_id']相等 相等则选定
        *menu_datas[*][0]通过cID在set_menu_list表查找到的products_id数组
        *col_list[$n]['products_id']是通过cID表products_to_categories和表products_description查询出的
        */
          
      }
    echo "</select>";?>
</td><td>
<?php
    $flg=0;
    for($n=0;$n<$i_cnt;$n++){
      if($menu_datas[$i][0] == $col_list[$n]['products_id']){
$html = "<input type='text' size='10px' value='".$menu_datas[$i][1]."' name='kakuu[]' id='imaginary_".$i."'  onkeydown=\"ctrl_keydown(event,'IMAGINARY_INPUT','".$i."')\" />";
        $flg=1;
        //onBlur=event_onblur('IMAGINARY_INPUT','".$i."')
      }
    }    
    
    if($flg != 1){
      $html ="<input type='text' size='10px' value='' name='kakuu[]' id='imaginary_".$i."' onkeydown=\"ctrl_keydown(event,'IMAGINARY_INPUT','".$i."')\"　/>";

    }
    //通过变量 flg 确定输出的html 如果判断成立输出文本框的内容为menu_datas[$i][1]（menu_datas[$i]['kakuukosuu']）
    echo $html;
?>
</td><td>
<?php
    $fig_kakaku=0;
    for($n=0;$n<$i_cnt;$n++){
      if($menu_datas[$i][0] == $col_list[$n]['products_id']){
        $html_kakaku = "<input type='text' size='10px' value='".$menu_datas[$i][2]."' name='kakaku[]'  id='trader_".$i."'  onkeydown=\"ctrl_keydown(event,'TRADER','".$i."')\"　/>";
        $fig_kakaku=1;
      }
    }    
    
    if($fig_kakaku != 1){
      $html_kakaku ="<input type='text' size='10px' value='' name='kakaku[]'  id='trader_".$i."'  onkeydown=\"ctrl_keydown(event,'TRADER','".$i."')\"　/>";
    }
    /*
    *通过变量 flg_kakaku 确定输出的html_kakaku
    *如果判断成立输出文本框的内容为menu_datas[$i][1]（menu_datas[$i]['kakaku']）
    */
    echo $html_kakaku;
?>
</td></tr>
  
<?php
    //onBlur=event_onblur('TRADER','".$i."')
}

?>
<tr>
  <td><input type="button" value="登録"　name="b1" onClick="list_submit()" ></td>
  <td><input type="button" value="選択リセット"　name="b2" onClick="select_reset(<?php echo $count[0]; ?>,<?php echo $cnt3; ?>)" ></td>
</tr>
</table>
</form>
  
  
  
<form name='listform' method='POST' action="list_display.php?action=update&cid=<?php echo $cID; ?>&cpath=<?php echo $cPath; ?>">

<table border="1">
  <tr>
    <th>商品選択</th>
    <th>個数/架空</th>
    <th>価格/業者</th>
  </tr>
<?php foreach($products as $p) {?>
  <tr>
    <td><?php echo $p['products_name'];?></td>
    <td><input type="text" name="kakuukosuu[<?php echo $p['products_id'];?>]" value="<?php echo tep_get_kakuukosuu_by_products_id($cID, $p['products_id']);?>" ></td>
    <td><input type="text" name="kakaku[<?php echo $p['products_id'];?>]"     value="<?php echo tep_get_kakaku_by_products_id($cID, $p['products_id']);?>" ></td>
  </tr>
<?php }?>
</table>
<input type="submit">
</form>

</body>
</html>
