<?php
if(!defined('OSTSCPINC') || !@$thisuser->isStaff()) die('Access Denied');

?>
<script>
function CountAndClose()
{
var e = document.getElementById("ct");
var cTicks = parseInt(e.innerHTML);
var timer = setInterval(function()
{
if( cTicks )
{
e.innerHTML = --cTicks;
}
else
{
clearInterval(timer);
closeme();
}
}, 1000);
}
function closeme(){
var browserName=navigator.appName; 
if (browserName=="Netscape")
{ 
 
        window.open('','_parent',''); 
        window.close();
 } 
else 
{ 
    if (browserName=="Microsoft Internet Explorer") 
    { 
 
            window.opener = "whocares"; 
            window.close(); 
 
    } 
}
} 
</script>
<p style="margin-bottom:0px;" id="infomessage" align="center"><?php echo $msg;?></p>
<p style="padding:5px; margin:5px 0; border:#ccc solid 1px; line-height:24px; font-size:12px;">
<span id="ct" style="color:#ff0000">5</span>  秒の後で自動的に閉じます<br><img style=" vertical-align:middle; margin-right:5px;" onload="CountAndClose()" src="images/cl.png" alt="close" onClick="closeme();"><span style="color:#999;">画像をクリックする、本ページを閉じます</span>
</p>

