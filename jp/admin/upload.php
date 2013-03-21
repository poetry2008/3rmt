<?php
include("includes/application_top.php");
header('Content-Type: text/html; charset=UTF-8');
$inputName='filedata';
$attachDir='upload/manuals';
$dirType=4;
$maxAttachSize=2097152;
$upExt='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';
$msgType=2;
$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;

$err = "";
$msg = "''";
$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
$localName='';

if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){
	file_put_contents($tempPath,file_get_contents("php://input"));
	$localName=urldecode($info[2]);
}
else{
	$upfile=@$_FILES[$inputName];
	if(!isset($upfile))$err='文件域的name错误';
	elseif(!empty($upfile['error'])){
		switch($upfile['error'])
		{
			case '1':
				$err = '文件大小超过了php.ini定义的upload_max_filesize值';
				break;
			case '2':
				$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
				break;
			case '3':
				$err = '文件上传不完全';
				break;
			case '4':
				$err = '无文件上传';
				break;
			case '6':
				$err = '缺少临时文件夹';
				break;
			case '7':
				$err = '写文件失败';
				break;
			case '8':
				$err = '上传被其它扩展中断';
				break;
			case '999':
			default:
				$err = '无有效错误代码';
		}
	}
	elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')$err = '无文件上传';
	else{
		move_uploaded_file($upfile['tmp_name'],$tempPath);
		$localName=$upfile['name'];
	}
}

if($err==''){
	$fileInfo=pathinfo($localName);
	$extension=$fileInfo['extension'];
	if(preg_match('/'.str_replace(',','|',$upExt).'/i',$extension))
	{
		$bytes=filesize($tempPath);
		if($bytes > $maxAttachSize)$err='请不要上传大小超过'.formatBytes($maxAttachSize).'的文件';
		else
		{
			switch($dirType)
			{
				case 1: $attachSubDir = 'day_'.date('ymd'); break;
				case 2: $attachSubDir = 'month_'.date('ym'); break;
				case 3: $attachSubDir = 'ext_'.$extension; break;
				case 4: $attachSubDir = ''; break;
			}
#			$attachDir = $attachDir.'/'.$attachSubDir;
			if(!is_dir($attachDir))
			{
				@mkdir($attachDir, 0777);
				@fclose(fopen($attachDir.'/index.htm', 'w'));
			}
			PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
			$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
			$targetPath = $attachDir.'/'.$newFilename;
			
			rename($tempPath,$targetPath);
			@chmod($targetPath,0755);
			$targetPath=jsonString($targetPath);
			if($immediate=='1')$targetPath='!'.$targetPath;
			if($msgType==1)$msg="'$targetPath'";
			else $msg="{'url':'".$targetPath."','localname':'".jsonString($localName)."','id':'1'}";
		}
	}
	else $err='上传文件扩展名必需为：'.$upExt;

	$sql="insert into manual_upload values ('','".$_GET['rand_num']."','".$newFilename."')";
	$res=mysql_query($sql);
	@unlink($tempPath);
}

echo "{'err':'".jsonString($err)."','msg':".$msg."}";


function jsonString($str)
{
	return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
}
function formatBytes($bytes) {
	if($bytes >= 1073741824) {
		$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
	} elseif($bytes >= 1048576) {
		$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
	} elseif($bytes >= 1024) {
		$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
	} else {
		$bytes = $bytes . 'Bytes';
	}
	return $bytes;
}
?>
