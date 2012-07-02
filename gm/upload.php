<meta http-equiv="content-type" content="text/html;charset=utf-8">
<?php
  
if ($_GET['action'] == "save"){
   
  // require('../includes/application_top.php');
  //require(DIR_WS_ACTIONS.'index_top.php');
  
  $conn = mysql_connect('localhost','hm1002','hm123456') or die('服务器连接失败'); 
  mysql_select_db('hm1002_gm_3rmt',$conn) or die('数据库连接错误');
 
  $sql = "select * from `upload` where uploadid";
  $query = mysql_query($sql);
  while($row = mysql_fetch_array($query)){
  echo $row['title']."<br>";
  }
 
  $uploaddir = "upload";//设置文件保存目录 注意包含/
  $type=array("jpg","gif","bmp","jpeg","png");//设置允许上传文件的类型
  $patch="upload/";//程序所在路径

  //获取文件后缀名函数
  function fileext($filename)
  {
    return substr(strrchr($filename, '.'), 1);
  }
  //生成随机文件名函数
  function random($length)
  {
    $hash = 'CR-';
    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $max = strlen($chars) - 1;
    mt_srand((double)microtime() * 1000000);
    for($i = 0; $i < $length; $i++)
    {
      $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
  }

  $a=strtolower(fileext($_FILES['file']['name']));
  //判断文件类型
  if(!in_array(strtolower(fileext($_FILES['file']['name'])),$type))
  {
    $text=implode(",",$type);
    echo "您只能上传以下类型文件: ",$text,"<br>";
  }
   //生成目标文件的文件名
  else{
    $filename=explode(".",$_FILES['file']['name']);
    do
    {
      $filename[0]=random(10); //设置随机数长度
      $name=implode(".",$filename);
      //$name1=$name.".Mcncc";
      $uploadfile=$uploaddir.$name;
    }

    while(file_exists($uploadfile));

    if (move_uploaded_file($_FILES['file']['tmp_name'],$patch.$uploadfile))
    {
      if(is_uploaded_file($_FILES['file']['tmp_name']))
      {

        echo "上传失败!";
      }
      else
      {//输出图片预览
        echo "<center>您的文件已经上传完毕 上传图片预览: </center><br><center><img
          src='$patch$uploadfile'></center>";
        echo "<br><center><a href='upload.php'>继续上传</a></center>";
      }
    }

  }

  $title=$_POST['title'];
  $pic=$uploadfile;
  if($title == "")
    echo"<Script>window.alert('对不起！你输入的信息不完整!');history.back()</Script>";
  $sql="insert into upload(title,pic) values('$title','$pic')";
  $result=mysql_query($sql,$conn);
  //echo"<Script>window.alert('信息添加成功');location.href='upload.php'</Script>";
}
?>
<html>
<head>
<title>文件上传</title>
</head>
<body>
<form method="post" action="?action=save" enctype="multipart/form-data">
<table border=0 cellspacing=0 cellpadding=0 align=center width="100%">
<tr>
<td width=55 height=20 align="center">&nbsp;</TD>
<td height="16">

<table width="48%" height="93" border="0" cellpadding="0" cellspacing="0">
    <tr>
          <td>标题：</td>
       <td><input name="title" type="text" id="title"></td>
     </tr>
     <tr>
       <td>文件： </td>
        <td><label>
      <input name="file" type="file" value="浏览" >
      <input type="hidden" name="MAX_FILE_SIZE"  value="2000000">
        </label></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
           <td><input  type="submit" value="上传" name="upload"></td>
       </tr>
           </table></td>
           </tr>
                </table>
           </form>
           </body>
        </html>
