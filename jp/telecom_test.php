 <?php
/*
  $Id$

*/

  require('includes/application_top.php');
echo "click next to create this order, copy the follow url for test telecom .";
echo "<br>";
echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no';
/*
$w_clientip=$_GET['clientip'];
$w_telno=$_GET['telno'];
$w_email=$_GET['email'];
$w_sendid=$_GET['sendid'];
$w_username=$_GET['username'];
$w_money=$_GET['money'];
$w_cont=$_GET['cont'];
$w_option=$_GET['option'];
*/



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
echo "<pre>";
print_r($_GET);
print_r($_POST);
print_r($_SESSION);