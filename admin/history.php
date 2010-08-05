<?php 
ob_start();
require('includes/application_top.php');
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();
$cPath=$_GET['cPath'];
$cID=$_GET['cid'];
?>  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; 
charset=<?php echo CHARSET; ?>">
  <link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
  <script language="javascript" src="includes/general.js"></script>
  <script language="javascript" src="includes/javascript/jquery.js"></script>
  <script type="text/javascript" src="includes/javascript/udlr.js"></script>
  <script language="javascript" >
    function goto(){
      var link = document.getElementById('back_link').href;
      location.href=link;
    }
    function delete_one_data(){
      return confirm('削除しますか？');
    }
$(function() {
    $("#saveorder1").bind('click',function(){
      $("#orderstring").val('');
      $("input[name='proid[]']").each(function(i){
      $("#orderstring").val($("#orderstring").val() +','+this.value);
        });
      $("#targetstring").val('');
      $("input[name='TARGET_INPUT[]']").each(function(i){
      $("#targetstring").val($("#targetstring").val() +','+this.value);
        });
      return true;
      //return false;
      });
    $(".udlr").udlr();
    var key_sum=0;
    // 给文本框加个keypress，即键盘按下的时候判断
    $(".input_number").keypress(); 
    $(".input_number").keypress(function(event) {
        if (!$.browser.mozilla) {
            if (event.keyCode && ((event.keyCode < 45 || event.keyCode > 57) && event.keyCode != 47)) {
                // ie6,7,8,opera,chrome管用
                event.preventDefault();
                key_sum++;
            }
        } else {
            if (event.charCode && ((event.charCode < 45 || event.charCode > 57) && event.charCode != 47)) {
                // firefox管用
                event.preventDefault();
                key_sum++;
            }
        }
        if(key_sum>4){
           key_sum=0;
          alert('数字とドット「.」でご入力ください');
        }
    });
});
function ex(id,tr_len){
  //alert(document.getElementsByName['sort_order[]']);
  tr_len = tr_len+1;
  for(exi=1;exi<tr_len;exi++){
    id_tmp1 = 'tr_'+id+'_'+exi;
    id_tmp2 = 'tr_'+(id-1)+'_'+exi;
    var id_val1 = $("#"+id_tmp1).children('input').val();
    var id_val2 = $("#"+id_tmp2).children('input').val();
    tmp = document.getElementById(id_tmp1).innerHTML;
    document.getElementById(id_tmp1).innerHTML =
      document.getElementById(id_tmp2).innerHTML;
    document.getElementById(id_tmp2).innerHTML = tmp;
    $("#"+id_tmp1).children('input').val(id_val2);
    $("#"+id_tmp2).children('input').val(id_val1);
  }
  /*
  $('#tr_'+id+'_1>.sort_order_input').val(id);
  $('#tr_'+(id-1)+'_1>.sort_order_input').val(id-1);
  */
}
  </script>
  <title>履歴表示</title>
  </head>
  <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" >
  <a name="top"></a>
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
                 <td class = "pageHeading">
  <?php
  if ($HTTP_GET_VARS['action'] == 'oroshi') {
    echo '卸業者の履歴登録';
  } else if ($HTTP_GET_VARS['action'] == 'oroshi_c') {
    echo '卸業者の履歴表示';
  } else {
    echo '同業者の履歴登録';
  }
  ?>
  <input type="button" onClick = "goto()" value='戻る'>
                 </td>
              </tr>
              <tr>
                 <td>
                    <table width="100%" cellspacing="0" cellpadding="2" border="0">
                       <tr>
  <?php
  $back_url = 'cleate_dougyousya.php'; 
switch ($HTTP_GET_VARS['action']){
case 'oroshi':
  $back_url = 'cleate_oroshi.php'; 
  $oid = $_GET['o_id'];
  if(isset($_GET['list_id'])){
    $list_id = $_GET['list_id'];
    $res = tep_db_query("select * from set_oroshi_datas where list_id='".$list_id."'");
    if(tep_db_fetch_array($res)) {
      tep_db_query("delete from set_oroshi_datas where list_id='".$list_id."'");
    }
  }
  $res = tep_db_query("select sod.list_id,sod.datas,sod.set_date,cd.categories_name,son.oroshi_name ,son.oroshi_id, cd.categories_id  from set_oroshi_names son,set_oroshi_datas sod ,categories_description cd where sod.oroshi_id = son.oroshi_id and  sod.parent_id = cd.categories_id  and cd.site_id = 0 and  sod.oroshi_id = '".$oid."'");
  while($col =tep_db_fetch_array($res)){
    //    var_dump($col);
    $games[$col['categories_id']][] = $col;
  }
  if($games){
  foreach ($games as $gid=>$game){

      echo "<table width='100%' cellspacing='1' cellpadding='2' border='0'><tr class='dataTableHeadingRow'><td width='160' height='30' class='dataTableHeadingContent'>";
      echo $game[0]['categories_name'];
      echo "</td>";
      echo "<td width='300' class='dataTableHeadingContent'><a href=
        'history.php?action=oroshi_c&cPath=".$gid."&oid=".$oid."'>履歴詳細</a></td>";
      echo "<td class='dataTableHeadingContent'><a href=
        'cleate_list.php?action=prelist&cid=".$game[0]['categories_id']."&oid=".$oid."&src_id=his'
        >データ作成</a></td>";
      echo "</tr>";
    foreach ($game as $key=>$value){
      echo "<tr class='dataTableRow'>";
      echo "<td class='dataTableContent'>".date('Y/m/d H:i:s',strtotime($value['set_date']))."</td>";
      echo "<td class='dataTableContent'>";
      foreach(spliteOroData($value['datas']) as $line){
        echo $line . '<br>';
      }
      echo "</td>";
      echo "<td class='dataTableContent'><a onClick='return delete_one_data()'
        href='history.php?action=oroshi&o_id=".$oid."&list_id=".$value['list_id']."'>削除</a></td>";
      echo "</tr>";
    }
    echo "</table>";

  }
  }
  break;
case 'oroshi_c':
  $back_url = 'history.php';
  $cPath = $_GET['cPath'];
  $oid = $_GET['oid'];
  $back_url_params = 'action=oroshi&o_id='.$oid;
  $res=tep_db_query("select * from set_oroshi_names son,set_oroshi_categories soc  where son.oroshi_id = soc.oroshi_id and soc.categories_id = '".$cPath."' ORDER BY son.oroshi_id ASC");
  $cnt=0;
  while($col=tep_db_fetch_array($res)){
    $o_name[]=$col['oroshi_name'];
    $o_id[]=$col['oroshi_id'];
    $cnt++;
  }
  $res=tep_db_query("select count(*) as cnt_data from set_oroshi_datas where parent_id='".$cPath."' ORDER BY list_id DESC");
  $col=tep_db_fetch_array($res);
  $cnt_data=$col['cnt_data'];
  $a=0;
  for($k=0;$k<20;$k++){//過去20件
    $arr = array();
    for($i=0;$i<$cnt;$i++){
      $res=tep_db_query("select * from set_oroshi_datas where parent_id='".$cPath."' && oroshi_id='".$o_id[$i]."' ORDER BY list_id DESC  limit ".$a.",1 ");
      $col=tep_db_fetch_array($res);
      if ($col) {
        $arr[] = $col;
      }
    }
    if (!$arr) break;
    ?>
    <table border="1">
      <tr>
      <?php 
      for($i=0;$i<$cnt;$i++){
        $res=tep_db_query("select set_date from set_oroshi_datas where parent_id='".$cPath."' && oroshi_id='".$o_id[$i]."' ORDER BY list_id DESC  limit ".$a.",1 ");
        $col=tep_db_fetch_array($res);
        echo "<td align='center'>".date('Y/m/d H:i:s', strtotime($col['set_date']))."</td>";
      }
    ?>
    </tr>
    <tr>
        <?php
        for($i=0;$i<$cnt;$i++){
          $res=tep_db_query("select son.oroshi_name from set_oroshi_names son,set_oroshi_datas sod  where sod.oroshi_id = son.oroshi_id and sod.parent_id='".$cPath."' && sod.oroshi_id='".$o_id[$i]."' ORDER BY list_id DESC");
          $col=tep_db_fetch_array($res);
          echo "<th align='center'>".$col['oroshi_name']."</th>";
        }
    ?>
    </tr>

<?php
    $cr = array("\r\n", "\r");   // 改行コード置換用配
    for($i=0;$i<=$cnt;$i++){
      $res = tep_db_query("select * from set_oroshi_datas where parent_id='".$cPath."' && oroshi_id='".$o_id[$i]."' ORDER BY list_id DESC limit ".$a.",1 ");
      $col[$i] = tep_db_fetch_array($res);
      
      /*
      $col[$i]['datas'] = trim($col[$i]['datas']);         // 文頭文末の空白を削除
      $col[$i]['datas'] = str_replace($cr, "\n",$col[$i]['datas']);  // 改行コードを統一
      $lines[$i] = explode("\n", $col[$i]['datas']);
      */
      $lines[$i] = spliteOroData($col[$i]['datas']);
      $count[$i] = count($lines[$i]);
    }
    
    for($n=0;$n<$cnt;$n++){//取得したデータでどれが一番件数が大きいか
      if($count[0]<=$count[$n]){
        $count[0]=$count[$n];
      }
    }
          
    for($i=0;$i < $count[0];$i++){
      echo "<tr id=color>";
      for($j=0;$j<$cnt;$j++){
        echo "<td>".$lines[$j][$i]."</td>";
      }
      echo "</tr>";
    }
          
    ?>
    </table>
        <br>
        <br>
        <br>
        <?php 
        $a++;
  }
  break;
case 'd_submit':
  $cPath = $_GET['cPath'];
  $cid = $_GET['cid'];
  $did = $_GET['did'];
  $back_url = 'history.php';
  $back_url_params =
    'action=dougyousya_categories'.'&cPath='.$cPath.'&cid='.$cid.'&did='.$did;
  $dougyousya=$_POST['TARGET_INPUT'];//同業者価格
  $proid = $_POST['proid'];
  $dou_id=$_POST['d_id'];//同業者ID
  $submit = $_POST['b1'];
  $str = $_POST['orderstring'];
  $proid_arr = explode(',',$str);
  $str = $_POST['targetstring'];
  $dougyousya = explode(',',$str);
  array_shift($proid_arr);
  array_shift($dougyousya);
     $con = count($proid_arr);
     for($z=0;$z<$con;$z++){
       $sql = 'select order_value from product_dougyousya_order 
               where product_id ="'.$proid_arr[$z].'"';
       $res = tep_db_query($sql);
       if(tep_db_fetch_array($res)){
         $sql = 'update product_dougyousya_order set order_value="'.$z.'" where
         product_id = "'.$proid_arr[$z].'"';
       }else{
         $sql = 'insert into product_dougyousya_order
           values("'.$proid_arr[$z].'","'.$z.'")';
       }
         tep_db_query($sql);
       /*
       tep_db_perform('product_dougyousya_order'
           ,array('order_value' => (int)$key),
           'update', "product_id='".$value."'");
           */
     }
  $d_cnt = count($dou_id);
  $loop_cnt=count($dou_id);
    
  $count_tontye = 0;
  foreach($dou_id as $value)
    {
      if ($value!='')
        $count_tontye++;
    }
  $count_product = count($proid);//一共几行
  $limit = 20;        
  
  // get last history
  /*
  $last_history_arr = array();
  $last_history_date = tep_db_fetch_array(tep_db_query("select * from set_dougyousya_history where categories_id='".$cID."' order by last_date desc"));
  if ($last_history_date) {
    $last_history_date = $last_history_date['last_date'];
    for ($i = 0;$i<$count_product;$i++)
    {
      for ($j=0;$j<$count_tontye;$j++)
      {
        $last_history = tep_db_fetch_array(tep_db_query("
          select * 
          from set_dougyousya_history 
          where categories_id='".$cID."'
            and products_id='".$proid[$i]."'
            and dougyousya_id='".$dou_id[$j]."'
            and last_date='".$last_history_date."'
        "));
        $last_history_arr[] = $last_history['dougyosya_kakaku'];
      }
    }
  }*/
  
  for ($i = 0;$i<$count_tontye;$i++)
    {
      for ($j=0;$j<$count_product;$j++)
        {
          $kankan =  SBC2DBC($dougyousya[$j*$count_tontye+$i]);
          if ($kankan !== ''){
            /*
            $last_history_date = tep_db_fetch_array(tep_db_query("select * from set_dougyousya_history where categories_id='".$cID."' and products_id='".$proid[$j]."' and dougyousya_id='".$dou_id[$i]."' order by last_date desc"));
            if ($last_history_date && $kankan == $last_history_date['dougyosya_kakaku']){
              $last_history_date = $last_history_date['last_date'];
              tep_db_perform("set_dougyousya_history", array('last_date' => 'now()'), 'update', "categories_id='".$cID."' and products_id='".$proid[$j]."' and dougyousya_id='".$dou_id[$i]."' and last_date='".$last_history_date."'");
            } else {*/
              $sql = 'insert into set_dougyousya_history ( `categories_id`,`products_id`,`dougyosya_kakaku`,`dougyousya_id`,`last_date`)';
                    
              $sql.= 'values ('.$cID.','.$proid[$j].',\''.$kankan.'\','.$dou_id[$i].',now())';

              tep_db_query($sql);
              $sql = 'select history_id from  set_dougyousya_history where categories_id='.$cid.' and products_id = '.$proid[$j]. ' and dougyousya_id = '.$dou_id[$i].' order by last_date desc  limit 20,100';
              $res = tep_db_query($sql);
              while($colx = tep_db_fetch_array($res)){
                tep_db_query('delete from set_dougyousya_history where history_id ="'.$colx['history_id'].'"');
              }
            //}
          }
        }
    }
    
          header("Location:history.php?action=dougyousya_categories&cPath=".$cPath."&cid=".$cID."&did=".$did."&fullpath=".$_POST['fullpath']);
  break;
case 'dougyousya':
  //要先把游戏找出来再进行操作

  $did = $_GET['dougyousya_id'];
  $sql = 'select sdc.categories_id,cd.categories_name  from categories_description
    cd,set_dougyousya_categories sdc where cd.site_id = 0 and sdc.categories_id =
    cd.categories_id and  sdc.dougyousya_id ="' .$did.'"';
  $res = tep_db_query($sql);
  while($testcol  = tep_db_fetch_array($res))
    {
      $cate_id= $testcol['categories_id'];
      $cate_name= $testcol['categories_name'];
      $colmunLimit = 2;//分几行
      $colmunLimit_add_1 = $colmunLimit+1;
      echo "<table border=1>";
      echo "<th width='200'></th>";
      //        echo "<td colspan = ".$colmunLimit_add_1 .">";
      echo "<th width='200'>";
      echo $cate_name;
      echo "</th>";
      echo "<th width='200'></th>";
      echo "<tbody>";
      $getSubCategories = 'select cd.categories_name,cd.categories_id from
        categories_description cd, categories c where
        c.categories_id=cd.categories_id and cd.site_id = 0 and c.parent_id ="'.$cate_id.'"';
      $subRes = tep_db_query($getSubCategories);

      $rowCount = $colmunLimit;
      while($subCol = tep_db_fetch_array($subRes)){
        $sub_cate_id = $subCol['categories_id'];
        $sub_cate_name = $subCol['categories_name'];
        if($rowCount == $colmunLimit){
          echo "</tr>\n";
        }
        echo "<td><a href=
          'history.php?action=dougyousya_categories&cid=".$sub_cate_id."&cPath=".$cate_id."&did=".$did."' >".$sub_cate_name.'</a></td>';
        if($rowCount>0) { 
          $rowCount--;
        }else {
          echo "</tr>\n";
          $rowCount =$colmunLimit;
        }
      }
      echo "</tbody></table><br><br>\n";
    }
  break;
case 'deletePoint':
  $cPath = $_GET['cPath'];
  $cid = $_GET['cid'];
  $did = $_GET['did'];
  $back_url_params =
  'action=deletePoint'.'&cPath='.$cPath.'&cid='.$cid.'&pointid='.$_GET ['pointid'];
  tep_db_query('delete from set_dougyousya_history where history_id =
  "'.$_GET['pointid'].'"');
  tep_redirect("history.php?action=dougyousya_categories&cid=".$cid."&cPath=".$cPath."&did=".$did."&fullpath=".$_GET['fullpath']);
  break;
case 'dougyousya_categories':
  $cPath = cpathPart($_GET['cPath']);
  $cid = $_GET['cid'];
  $did = $_GET['did'];
  $back_url = 'history.php'; 
  $back_url_params = 'action=dougyousya'.'&dougyousya_id='.$did;
  if ($_GET['fullpath']) {
    $back_url = 'categories_admin.php'; 
    $back_url_params = 'cPath='.$_GET['fullpath'];
  }
  $a=0;
  $dou_cnt=0;
  $res=tep_db_query("select sdn.*,sdc.categories_id from set_dougyousya_names sdn,set_dougyousya_categories sdc  where sdn.dougyousya_id = sdc.dougyousya_id and sdc.categories_id ='".$cPath."' ORDER BY sdc.dougyousya_id ASC");
  $cnt=0;
  while($col=tep_db_fetch_array($res)){
    $d_name[]=$col['dougyousya_name'];
    $dougyousya_id[]=$col['dougyousya_id'];

    $cnt++;
  }
  $res=tep_db_query("select count(*) as cnt from set_dougyousya_history where categories_id='".$cID."' ORDER BY history_id DESC ");
  $col=tep_db_fetch_array($res);
  $pro_name_cnt=$col['cnt'];
  if ($ocertify->npermission>7) {
    $res=tep_db_query("select * from products p, products_to_categories p2c,
        products_description pd left join product_dougyousya_order pdo
        on pdo.product_id=pd.products_id
        where p.products_id=pd.products_id and 
        p2c.products_id=pd.products_id and 
        p2c.categories_id='".$cID."' and pd.site_id='0' 
        order by pdo.order_value asc");
  } else {
    $res=tep_db_query("select * from products p, products_to_categories p2c,
        products_description pd left join  product_dougyousya_order pdo
        on pdo.product_id=pd.products_id
        where p.products_id=pd.products_id and
        p2c.products_id=pd.products_id and 
        p2c.categories_id='".$cID."' and pd.site_id='0' 
        and p.products_status='1' \
        order by pdo.order_value asc");
  }
  $cnt2=0;
  while($col=tep_db_fetch_array($res)){
    $cid_list[]=$col['products_id'];
    $cnt2++;
  }
  $pro_name_cnt=$cnt*$cnt2;
  echo   ' <form method="post"
  action="history.php?action=d_submit&cPath='.$cPath.'&cid='.$cID.'&did='.$did.'" >';
  echo '<input type="hidden" name="fullpath" value="'.$_GET['fullpath'].'" />';

  // get last history
  $last_history_arr = $last_history_arr2 = array();
  $last_history_date = tep_db_fetch_array(tep_db_query("select * from set_dougyousya_history where categories_id='".$cID."' order by last_date desc"));
  if ($last_history_date) {
    $last_history_date = $last_history_date['last_date'];
    $last_history_query = tep_db_query("select * from set_dougyousya_history where categories_id='".$cID."' and last_date='".$last_history_date."'");
    while($last_history = tep_db_fetch_array($last_history_query)){
      $last_history_arr[$last_history['products_id']][$last_history['dougyousya_id']] = $last_history;
    }
  }
  
  ?>

  <table border="1">
     <tr>
     <td <?php if ($ocertify->npermission>7) {?>colspan ='2'<?php }?>>カテゴリー / 商品</td>
     <?php 
     for($i=0;$i<$cnt;$i++){
       $html .= "<td>".$d_name[$i]."</td>";
     }
  echo $html;

  ?>
  </tr>
      <?php 
      $res=tep_db_query("select count(*) as cnt from set_dougyousya_names sdn
          ,set_dougyousya_categories sdc  where sdn.dougyousya_id =
          sdc.dougyousya_id and sdc.categories_id='".$cPath."'");
  $count=tep_db_fetch_array($res);
  $target_cnt=1;//同業者専用
  $products_count=0;
  //登録フォーム作成
  if($count['cnt'] > 0){
    for($j=0;$j<$count['cnt'];$j++){
      echo "<input type='hidden' name='d_id[]' value='".$dougyousya_id[$j]."'>";//同業者ID
    }
  }
  $x = 0;
  for($i=0;$i<$cnt2;$i++){
    echo "<tr>";
    //$res=tep_db_query("select * from products_description where products_id='".$cid_list[$i]."'");
    $res=tep_db_query("select * from products_description where products_id='".$cid_list[$i]."' order by products_description.products_name asc");

    $col=tep_db_fetch_array($res);  
  if ($ocertify->npermission>7) {
    if($x){
      echo "<td><a href='javascript:void(0);' onclick='ex(".$x.",".($count['cnt']+1).")'>↑</a></td>";
    }else{
      echo "<td></td>";
    }
  }
    echo "<td id='tr_".$x."_1'>";
    echo "<input type='hidden' name='proid[]' value='".$cid_list[$i]."' class='sort_order_input' >";//products_id
    echo "<a href='#".$col['products_name']."'>".$col['products_name']."</a></td>";
    if($count['cnt'] > 0){
      for($j=0;$j<$count['cnt'];$j++){
        //        <input type='text' size='7px' name='TARGET_INPUT[]' onkeydown=ctrl_keydown('TARGET_INPUT',".$i.",".$j.",".$count['cnt'].")></td>";//価格同業者
        $last_history_arr2[$i][$j] = isset($last_history_arr[$cid_list[$i]][$dougyousya_id[$j]])?$last_history_arr[$cid_list[$i]][$dougyousya_id[$j]]['dougyosya_kakaku']:'';
        //        <input type='text' size='7px' name='TARGET_INPUT[]' onkeydown=ctrl_keydown('TARGET_INPUT',".$i.",".$j.",".$count['cnt'].")></td>";//価格同業者
        echo "<td id='tr_".$x."_".($j+2)."' class='dataTableContent' >
        <input value='' pos='".$i."_".$j."' id=\"ti_".$i."_".$j."\" class='udlr input_number col_".$j."'  type='text' size='7px' name='TARGET_INPUT[]' onpaste=\"return !clipboardData.getData('text').match(/\D/)\" ondragenter=\"return false\" style=\"ime-mode:Disabled\"><a href=\"javascript:void(0)\" onclick=\"$('.col_".$j."').val($('#ti_".$i."_".$j."').val())\">統一</a>";//価格同業者
      }
    }else{
      //            echo "<td class='dataTableContent' ><input type='text' size='7px'  name='TARGET_INPUT[]' onkeydown=ctrl_keydown('TARGET_INPUT',".$i.",'0','0')></td>";//価格同業者  
      echo "<td class='dataTableContent' ><input pos='".$i."_".$j."' class='udlr input_number' type='text' size='7px'
        name='TARGET_INPUT[]' ></td>";//価格同業者 
    }
    echo "</tr>";
    $x++;
  }
  ?>
  <tr>
    <td colspan='<?php echo $count['cnt']+1;?>'>
      <input type="submit" name="b1" id = 'saveorder1' value="登録">
      <input type='hidden' id='orderstring' name='orderstring' />
      <input type='hidden' id='targetstring' name='targetstring' />
      <input type="button" onclick="get_last_date()" value="LAST DATA">
      <input type="button" onclick="$('.udlr').val('')" value="RESET">
    </td>
  </tr>
     </form>
     </table>
    <script>
    var last_history = new Array();
    <?php
    foreach($last_history_arr2 as $lkey => $lvalue){
    ?>
    last_history[<?php echo $lkey;?>] = new Array();
      <?php
      foreach($lvalue as $hkey => $hvalue){
    ?>
    last_history[<?php echo $lkey;?>][<?php echo $hkey;?>] = '<?php echo $hvalue;?>';
    <?php
      }
    }
    ?>
    function get_last_date() {
      for(i in last_history){
        for(j in last_history[i]){
          $('#ti_'+i+'_'+j).val(last_history[i][j]);
        }
      }
    }
    </script>
     <!-- data start -->
     <br>
     <br>
     <?php 
  /*$res=tep_db_query("
    select sdh.* ,sdn.dougyousya_name 
    from set_dougyousya_history sdh ,set_dougyousya_names sdn 
    where sdh.dougyousya_id = sdn.dougyousya_id 
      and categories_id='".$cID."' 
    order by sdn.dougyousya_id,pd.products_name,last_date
  " );*/
  if ($ocertify->npermission>7) {
    $res=tep_db_query("
    select sdh.* ,sdn.dougyousya_name 
    from set_dougyousya_history sdh ,set_dougyousya_names sdn, products_description pd,set_dougyousya_categories sdc
    where pd.products_id=sdh.products_id 
      and sdn.dougyousya_id = sdc.dougyousya_id 
      and sdc.categories_id='".$cPath."'
      and sdh.dougyousya_id = sdn.dougyousya_id 
      and sdh.categories_id='".$cID."' 
      and pd.site_id=0
    order by sdn.dougyousya_id,pd.products_name,last_date" );
  } else {
    $res=tep_db_query("
    select sdh.* ,sdn.dougyousya_name 
    from products p,set_dougyousya_history sdh ,set_dougyousya_names sdn, products_description pd,set_dougyousya_categories sdc
    where p.products_id = pd.products_id
      and sdn.dougyousya_id = sdc.dougyousya_id 
      and sdc.categories_id='".$cPath."'
      and pd.products_id=sdh.products_id 
      and sdh.dougyousya_id = sdn.dougyousya_id 
      and sdh.categories_id='".$cID."' 
      and p.products_status = '1'
      and pd.site_id=0
    order by sdn.dougyousya_id,pd.products_name,last_date" );
  }
  
  $products_arr = array();
  while($col_datas=tep_db_fetch_array($res)){
    $products_arr[$col_datas['products_id']][] = $col_datas;
  }

  $color_arr = array('f44040','8fccad','f59a40','35cccc','cccc35','409af5','81cc35','3131f5','35cc35','9331f5');
  ksort($products_arr);
  foreach ($products_arr as $key=>$value)
    {
      $imgstr = '';
      $product_id = $key;
      $res_for_productname = tep_db_query('select products_name from
          products_description where products_id = "'.$value[0]['products_id'].'" and site_id=0');
      
      $productname = tep_db_fetch_array($res_for_productname);
      $productname = $productname['products_name'];
      echo "<a name='".$productname."'></a>";
      //echo $productname;
      $dys_arr = array();
      $time_arr = array();
      $tuli_arr = array();

      foreach ($value as $record)
      {
        $dys_arr[$record['dougyousya_id']][] = $record;
        //echo $record['dougyousya_name'];
        if (!in_array($record['dougyousya_name'], $tuli_arr)){
          $tuli_arr[]  = $record['dougyousya_name'];
        }
      }
      $imgstr = "<img width='720' height='300' alt='".$productname."' onclick='$(\"#history_table_".$value[0]['products_id']."\").toggle()' src = 'chart.php?cht=lxy&chs=720x300&";
      $imgstr.= "chd=t:";
      $style = array();
      $key2count = 0;
      $chco = array();                
      $tuli = array();
      $chls = array();
            
      
      $time_arr = array();
      $kakaku_arr = array();

      
      $len = 1;
      $lenkaku = 1;

      
      foreach($dys_arr as $key2 => $value2){
        foreach($value2 as $key4=>$value4){
          if (!isset($lenmax)){
            $lenmax = $lenmin = strtotime($value4['last_date']);
            $lenkakumax = $lenkakumin = $value4['dougyosya_kakaku'];
          }
          $lenmax = max(strtotime($value4['last_date']),$lenmax);
          $lenmin = min(strtotime($value4['last_date']),$lenmin);
          $lenkakumax = max($value4['dougyosya_kakaku'], $lenkakumax);
          $lenkakumin = min($value4['dougyosya_kakaku'], $lenkakumin);
        }
      }
      
      $len = $lenmax - $lenmin;
      $lenkaku = $lenkakumax - $lenkakumin;
      
      $lenkakumax += $lenkaku*.1;
      $lenkakumin -= $lenkaku*.1;
      $lenkaku *= 1.2;

      $dys_arr_count = 0;
      foreach($dys_arr as $key2=>$value2)
      {
        $tuli[] = $tuli_arr[$dys_arr_count];
        $chco[] = $color_arr[$dys_arr_count];
        
        
        $x = '';
        $y = '';
        foreach ($value2 as $key3=> $value3){
          $x.= round((strtotime($value3['last_date'])-$lenmin)/$len*100);
          if (isset($value2[$key3+1])){
            $x.=',';
          }
          $y.= round(($value3['dougyosya_kakaku']-$lenkakumin)/$lenkaku*100);
          if (isset($value2[$key3+1])){
            $y.=',';
          }
          //$style[]='o,'.$color_arr[$dys_arr_count].','.$key2count.','.$key3.',5';
          $chls[] = 3;
        }
        $style[]='o,'.$color_arr[$dys_arr_count].','.$key2count.',,10';

        $imgstr.=$x.'|'.$y;
        $dys_arr_count ++; 
        if($dys_arr_count!=count($dys_arr))
          {
            $imgstr .='|';
          }
        $key2count++;
        
      }
      $imgstr.='&chf=bg,s,ffffff|c,ls,90,BBBBBB,0.25,999999,0.25,777777,0.25,444444,0.25'
             //. '&chds='.$xmin.','.$xmax.'|'.$min.','.$max
             . '&chm='.join('|',$style)
             //. '&chem='.join('|',$chem)
             . '&chco='.join(',',$chco)
             . '&chdl='.join('|',$tuli)
             . '&chls='.join('|',$chls)
             . '&chg=5,25'
             . '&chdlp=t|l'
             . '&chma=30,30,0,30'
             
               
             . '&chtt='.urlencode($productname)." ".urlencode(date('n月j日 H:i',$lenmin)).'+---+'.urlencode(date('n月j日 H:i',$lenmax))
             . '&chts=000000,14'
               
             . '&chxt='.'x,y'
             . '&chxs=0,000000,12|1,000000,12'
             . '&chxr=1,'.$lenkakumin.','.$lenkakumax
             . '&chxl=0:|'.date('n/j G:i',$lenmin).'|';
      for($leni = 1; $leni<10; $leni++){
        $imgstr .= date('n/j G:i', $lenmin+(($lenmax-$lenmin)/10*$leni))."|";
      }
      $imgstr .= date('n/j G:i',$lenmax);
      $imgstr.="' /><br><div>";
      echo $imgstr;
      unset($lenmax,$lenmin,$lenkakumax,$lenkakumin);
?>
<div id='history_table_<?php echo $value[0]['products_id'];?>' style='display:none;'>
<?php
      foreach ($dys_arr as $did=>$rowRecord)
        {
          echo "<table bgcolor='#999' cellspacing='1' style='float:left;' class='history_img' style='border:1px solid #999;font-size:11px;'>";
          echo "<tr><td colspan=2 style='color:white'>".$rowRecord[0]['dougyousya_name']."</td></tr>";
                  
          for($key8 = count($rowRecord)-1;$key8>=0;$key8--){
            echo "<tr>";
            echo "<td bgcolor='white' width='40' align='right'>"."<a href='history.php?action=deletePoint&cPath=".$cPath."&cid=".$cid."&pointid=".$rowRecord[$key8]['history_id']."&fullpath=".$_GET['fullpath']."'><b>" .$rowRecord[$key8]['dougyosya_kakaku']."</b></a></td>";
            echo "<td bgcolor='white' align='right' style='color:#999'>".date('n/j G:i', strtotime($rowRecord[$key8]['last_date']))."</td>";
            echo "</tr>";
          }
          echo '</table>';                  
        }
?>
</div>
<?php
      echo '</div>
      <div style="clear:both;text-align:right;"><a href="#top">▲</a></div>
      <hr>';
    }
        
  break;
}
?>
</td></tr></table></td></tr><tr><td>
<?php echo '<a style="width:100%;display:none" id = "back_link" href="'. tep_href_link($back_url, $back_url_params, 'NONSSL').'"></a>';
?>
</td></tr></table></td></tr>
</table>
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<br>
</body>
</html>
