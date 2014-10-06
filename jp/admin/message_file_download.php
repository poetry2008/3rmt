<?php
	
	if(empty($_GET['file_id'])){
		$file_id_status = false;
	}else{
		$file_id_status = true;
		$file_id = str_replace(' ','+',$_GET['file_id']);
	}
	if($file_id_status){
		//die(var_dump($file_id_status));	
		if (!file_exists('messages_upload/'.$file_id)){
			header("Content-type: text/html; charset=utf-8");
			echo "File not found!";
			exit; 
                } else {
                        $file_id_str = str_replace('*','/',$file_id);
			$file_name = base64_decode($file_id_str);
			$file_name = explode('|||',$file_name);
			$file_name = $file_name[0];
			//die(var_dump($file_name));	
			$file = fopen('messages_upload/'.$file_id,"r"); 
			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
                        Header("Accept-Length: ".filesize('messages_upload/'.$file_name));
                        Header("Content-Disposition: attachment; filename=\"".$file_name."\"");
                        ob_clean(); 
                        flush(); 
			echo fread($file, filesize('messages_upload/'.$file_id));
			fclose($file);
		}
	}
?>
