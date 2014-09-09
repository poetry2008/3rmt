<?php
	
	if(empty($_GET['file_id'])){
		$file_id_status = false;
	}else{
		$file_id_status = true;
	}
	$file_id=$_GET['file_id'];
	$file_id=base64_decode($file_id);
	if($file_id_status){
		//die(var_dump($file_id_status));	
		if (!file_exists('upload/bulletin_board/'.$file_id)){
			header("Content-type: text/html; charset=utf-8");
			echo "File not found!";
			exit; 
                } else {
			$file_name = explode('|||',$file_id);
			$file_name = $file_name[0];
			//die(var_dump($file_name));	
			$file = fopen('upload/bulletin_board/'.$file_id,"r"); 
			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
                        Header("Accept-Length: ".filesize('upload/bulletin_board/'.$file_name));
			Header("Content-Disposition: attachment; filename=\"".$file_name."\"");
			echo fread($file, filesize('upload/bulletin_board/'.$file_id));
			fclose($file);
		}
	}
?>
