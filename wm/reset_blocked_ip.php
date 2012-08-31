<?php
require('includes/configure.php');
ini_set('display_errors', 'Off');
$to_email = 'bobhero.chen@gmail.com';
$error = false;
$send_success = 0;
function send_mail($sTo, $sTitle, $sMessage, $sFrom = null, $sReply = null, $sName = NULL)
{
  $sTitle = stripslashes($sTitle);
  $sMessage = stripslashes($sMessage);
  if ($sName == NULL) {
    if ($sFrom) {
      $sFromName = "=?UTF-8?B?" . base64_encode($sFrom) . "?=";
    }
  } else {
    $sFromName = "=?UTF-8?B?" . base64_encode($sName) . "?=";
  }
  $sAdditionalheader = "From:" . $sFrom . "\r\n";
  $sAdditionalheader.= "Reply-To:" . $sFromName . " <" . $sReply . ">\r\n";
  $sAdditionalheader.= "Date:" . date("r") . "\r\n";
  $sAdditionalheader.= "MIME-Version: 1.0\r\n";
  $sAdditionalheader.= "Content-Type:text/plain; charset=UTF-8\r\n";
  $sAdditionalheader.= "Content-Transfer-Encoding:7bit";
  $sTitle = "=?UTF-8?B?" . base64_encode($sTitle) . "?=";
  return @mail($sTo, $sTitle, $sMessage, $sAdditionalheader);
}
if (isset($_POST['action'])) {
  if ($_POST['action'] == 'process') {
    if (empty($_POST['title']) || empty($_POST['content'])) {
      $error = true; 
    }
    if (!$error) {
      $send_success = 1;
      send_mail($to_email, $_POST['title'], $_POST['content']); 
    }
  }
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
<?php
if ($error) {
   echo '<font color="#ff0000">必須</font>';
}
if ($send_success) {
   echo 'メール送信完了';
}
?>
<form action="<?php echo HTTP_SERVER.'/reset_blocked_ip.php';?>" method="post">
<input type="hidden" name="action" value="process">
<table border="0">
  <tr>
    <td>タイトル</td>  
    <td><input type="text" name="title" value="<?php echo isset($_POST['title'])?$_POST['title']:'';?>"></td>  
  </tr>
  <tr>
    <td>内容</td>  
    <td><textarea name="content"><?php echo isset($_POST['content'])?$_POST['content']:'';?></textarea></td>  
  </tr>
  <tr>
    <td colspan="2"><input type="submit" value="submit"></td> 
  </tr>
</table>
</form>
</body>
</html>
