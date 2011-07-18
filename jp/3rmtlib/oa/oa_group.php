<?php
/**
 * @file   oa_group.php
 * @author bobhero <bobhero.chen@gmail.com>
 * @date   Wed May 18 19:03:07 2011
 * 
 * @brief  列表,新建,编辑
 * 
 * 
 */
require_once 'includes/application_top.php';
$action = $_GET['action'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>oa_group</title>
  </head>
  <body>
<?php
switch($action){
case 'list':
  echo 'list';
  break;
case 'new':
  echo 'new';
  oaNew();
  break;
case 'create':
  oaCreate();
  break;
default:
  echo 'default';
  break;
}
function oaCreate()
{
  $type = $_POST['type'];
  unset($_POST['type']);
  $group_id = $_POST['group_id'];
  unset($_POST['group_id']);
  $title = $_POST['title'];
  unset($_POST['title']);
  $comment = $_POST['comment'];
  unset($_POST['comment']);
  $sql = "INSERT INTO `maker_3rmt`.`oa_item` 
          (`id`, `group_id`, `title`, `name`, `comment`, `option`, `type`) 
          VALUES 
          (NULL, '".$group_id."', '".$title."', '".$type."_".$group_id."_".$count."', '".$comment."', '".serialize($_POST)."', '".$type."')";
  echo $sql;
  
}

function oaNew(){
  $type = $_GET['type'];
  $group_id = $_GET['group_id'];
  $type = 'text';
  $itemClass = 'HM_Item_'.ucfirst($type);
  require_once "oa/".$itemClass.".php";
  echo "<form action ='?action=create' method='post' >"  ."</br>\n";
  echo "<input type='hidden' name='group_id' value='".$group_id."'/>"  ."</br>\n";
  echo "<input type='hidden' name='type' value='".$type."'/>"  ."</br>\n";
  echo "Title:<input type='text' name='title' value='"."'/>"  ."</br>\n";
  echo $itemClass::prepareFormWithParent();
  echo "<textarea name='comment'/></textarea>"  ."</br>\n";
  echo "<input type='submit' />"  ."</br>\n";
  echo "</form>";
}

?>
  </body> 
  </html>
