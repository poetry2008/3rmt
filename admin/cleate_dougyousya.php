<?php
	require('includes/application_top.php');

  require(DIR_WS_CLASSES . 'currencies.php');
  $currencies = new currencies();
 $cPath=$_POST['cpath'];
  switch ($HTTP_GET_VARS['action']){
  	case 'set_oroshi':
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
	break;
	
	case 'delete':
		$dougyousya_id=$_GET['id'];
		$cPath=$_GET['cpath'];
		tep_db_query("delete from  set_dougyousya_names  where dougyousya_id = '".$dougyousya_id. "'");
	break;  
  }
?>
<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
	<script type="text/javascript">
	var html = new Array();
	var i=0;
		function input_add(){
			
			html[i] = "追加:<input type='text' name='set_oroshi[]'><br />"; 
			document.getElementById("o_input").innerHTML=html;
			i++;
		}

		
		function w_close(){
		
			//	window.close();	
		}
		
		function del_oroshi(id,path){
			
			var flg=confirm('削除しますか？');
			if(flg){
				location.href="cleate_dougyousya.php?action=delete&id="+id+"&cpath="+path;
			}else{
			
			}
		}
	</script>
	</head>
	<body>
		<form method="post" action="cleate_dougyousya.php?action=set_oroshi" onSubmit="w_close()">
			<input type="button" value="入力フォーム追加"　name='b1' onClick="input_add()"><br />
			<input type="hidden" value="<?php echo $cPath ?>" name="cpath">
			<?php 
			$res=tep_db_query("select * from set_dougyousya_names where parent_id='".$cPath."' ORDER BY dougyousya_id ASC");
			
			while($col=tep_db_fetch_array($res)){
					echo "同業者：<input type='text' name='up_oroshi[]' value='".$col['dougyousya_name']."'><input type='button' value='削除' name='b[]' onclick='del_oroshi(".$col['dougyousya_id'].",".$cPath.")'><br>";
			}
			
			?><div id="o_input"></div>
			<input type="submit" value="同業者登録">
		</form>
	</body>
</html>