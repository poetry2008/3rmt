 <?php
/*
  $Id$

*/

  require('includes/application_top.php');
?>
<h1>PAYMENT</h1>
<h2> 方法 1</h2>
<?php 
echo "<a href='".$_POST['redirect_url']
.'?option='.$_POST['option']
.'&username=MAKER'
.'&telno=1387897897'
.'&money='.$_POST['money']
.'&email=makerwang@gmail.com'
.'&clientip='.$_POST['clientip']
.'&option=OPT'
.'&cont=333'
.'&sendid=SENDID'
."'>next</a>";
echo "</br>";
echo "<a target = '_blank'href='";
echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=yes';
echo "'>";

echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=yes';
echo "</a>";


?>
<h2>
<h2> 方法 2</h2>
<?php
echo "<a target= '_blank' href='";
echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=yes';
echo "'>";
echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=yes';
echo "</a>";
echo "<br>";
echo "<a href='".$_POST['redirect_url']
.'?option='.$_POST['option']
.'&username=MAKER'
.'&telno=1387897897'
.'&money='.$_POST['money']
.'&email=makerwang@gmail.com'
.'&clientip='.$_POST['clientip']
.'&option=OPT'
.'&cont=333'
.'&sendid=SENDID'
."'>next</a>";

//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);
//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);