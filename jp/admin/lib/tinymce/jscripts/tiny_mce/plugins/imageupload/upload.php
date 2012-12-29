<?php
require_once('../../../../../../includes/configure.php');
ini_set('display_errors', 'Off');
if (isset($_FILES["upload_image"]) && is_uploaded_file($_FILES["upload_image"]["tmp_name"])) {
  //@todo Change base_dir!
  $base_dir = DIR_FS_ADMIN;
  //@todo Change image location and naming (if needed)
  $message_str = ""; 
  $filename = basename($_FILES["upload_image"]['name']);
  $extension_str = split("[/\\.]", strtolower($filename));
  $ext_num = count($extension_str)-1; 
  $extension_str = $extension_str[$ext_num];
  $allow_pic_type = array("jpg", "gif", "png", "jpeg");
  $allow_pic_size = 1024 * 4;
  if (in_array($extension_str, $allow_pic_type) && ($_FILES["upload_image"]["size"] < $allow_pic_size*1024)) {
    $image = 'upload/manuals/' . date("YmdHis").mt_rand(1000,9999).'.'.$extension_str;
    if (move_uploaded_file($_FILES["upload_image"]["tmp_name"], $base_dir . $image)) {
    } else {
      $message_str = "Error: A problem occurred"; 
    }
  } else {
    $message_str = "Error: File type is wrong or size large ".$allow_pic_size."KB"; 
  }
?>
<input type="hidden" id="src" name="src">
<script type="text/javascript" src="../../tiny_mce_popup.js"></script>
<?php
if (!empty($message_str)) {
?>
<script type="text/javascript">
alert("<?php echo $message_str;?>");
tinyMCEPopup.close();
</script>
<?php
} else {
?>
<script type="text/javascript">
  var ImageDialog = {
    init : function(ed) {
      ed.execCommand('mceInsertContent', false, 
        tinyMCEPopup.editor.dom.createHTML('img', {
          src : '<?php echo $image; ?>'
        })
      );
      
      tinyMCEPopup.editor.execCommand('mceRepaint');
      tinyMCEPopup.editor.focus();
      tinyMCEPopup.close();
    }
  };
  tinyMCEPopup.onInit.add(ImageDialog.init, ImageDialog);
</script>
<?php }?>
<?php  } else {?>
<html>
<head><title>UPLOAD PIC</title></head>
<body>
<form name="iform" action="" method="post" enctype="multipart/form-data">
  <input type="file" name="upload_image" onchange="document.forms.iform.submit()">
</form>
</body>
</html>
<?php }?>
