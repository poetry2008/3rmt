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
.'&cont=333'
.'&sendid=SENDID'
."'>next</a>";
echo "</br>";
echo "<a target = '_blank'href='";
echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=yes';
echo "'>";

echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=yes';
echo "</a>";

echo '<br>';
echo '<br>';
echo "<a target = '_blank'href='";
echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=no';
echo "'>";

echo 'http://'.$_SERVER['HTTP_HOST'].'/credit/receive.php?option='.$_POST['option'].'&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=no';
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
.'&cont=333'
.'&sendid=SENDID'
."'>next</a>";

//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);
//print_r($_GET);
//print_r($_POST);
//print_r($_SESSION);
?>
<h2>信用卡支付说明</h2>
<div style="padding-left:50px;">
    <h3>1决算管理页面说明</h3>
    <p>红色条表示，当有登录过骗子用的信用卡出现支付时，提示当前支付为红色登録。设置位置在警告文字列设定功能里。<br />引き当ての未 是订单未创建的意思。决算成功是指已经用信用卡付款成功。<br />决算成功，引当未合起来就是付款成功但未下订单。估计原因是付款结束后，从telecom公司的网站没有跳回到rmt完成下订单的动作。決済成功して、注文できていない<br />決済は失敗　引き当て未就是，付款未成功订单未创建，这个属于正常情况
    </p>
    <img src="../img_telecom/picture_01.gif" />
    <div style="padding-left:30px;">
        <p>1-2警告文字的红色文字设置页面在基本设定的警告文字列设定中。如果匹配已设定的内容，则在订单中如果出现类是内容，会在订单详细和决算履历中显示红色文字警告</p>
        <img src="../img_telecom/picture_02.gif" />
        <p>1-3 前台支付方法信用卡的最终确认页面说明</p>
        <img src="../img_telecom/picture_03.gif" />
        <table>
            <tr>
                <td colspan="2">方法1中</td>
            </tr>
            <tr>
                <td>next</td>
                <td>订单 ，引当 成功</td>
            </tr>
            <tr>
                <td>第一个连接</td>
                <td>传值到telecom unknown 显示为支付成功</td>
            </tr>
            <tr>
                <td>第二个连接</td>
                <td>传值到telecom unknown 显示为支付失败</td>
            </tr>
            <tr>
                <td colspan="2">方法2中跟方法1是相同的内容</td>
            </tr>
    
        </table>
    </div>
    <h3>2订单操作流程</h3>
    	<div style="padding-left:30px;"> 
        	<p>2-1前台点击模拟支付成功连接后，会在后台决算管理中，插入一条模拟的决算成功，引当未的一条数据。</p>
            <table>
            	<tr>
                	<td valign="top">注释：</td>
                    <td>模拟数据除了时间以外其他的都是虚拟相同的内容<br />插入值是实际信用卡网站中发到RMT网站的数据。 订单号，客户号，电话号，邮件地址，顾客名，支付金额，是否支付成功<br />值的内容为 option=20120229-11413722&clientip=76011&telno=1234567&email=makerwang@gmail.com&sendid=123&username=maker&money=10000&cont=no&rel=yes</td>
                </tr>
            </table>
            <img src="../img_telecom/picture_04.gif" />
            <p>2-2当在前台点击next按钮，创建订单按钮时，则订单与决算管理中已经存在的内容进行匹配，匹配成功后，决算管理中的刚才的那条数据就消失了。</p>
            <p>2-3此时查看后台的订单列表中，多了一条订单，订单状态为承认中。承认中的意思是，管理员看到订单后，先打电话确认信用卡顾客是否是本人消费</p>
            <img src="../img_telecom/picture_05.gif" />
            <p>2-4订单详细内容中各部分说明，点击メール　ost发邮件给顾客；クレカ　查询当前订单的信用卡在决算管理中状态。</p>
            <div>注：实际操作时候 由管理员电话确认完成以后填写OA中相关信息确认信息。然后更改状态，继续完成交易</div>
            <img src="../img_telecom/picture_06.gif" />
            <p>2-5 信用卡支付和订单状态的关系</p>
            <table border="1" cellpadding="3" cellspacing="0">
            	<tr>
                	<td width="40"></td>
                    <td width="200">决算管理中效果</td>
                    <td width="150">信用卡支付（決算）</td>
                    <td width="120">订单（引当）</td>
                    <td width="100">后台订单状态</td>
                    <td>注释</td>
                </tr>
                <tr>
                	<td>1</td>
                    <td>支付失败，订单未</td>
                    <td>失敗</td>
                    <td>不成功（未）</td>
                    <td>无订单</td>
                    <td>此时订单消息出现在 http://jp.gamelife.jp/admin/telecom_unknow.php?keywords=&rel_no=1 支付失败这个页面中，这种订单属于普通的订单（由于用户操作错误导致，支付未成功，订单没成</td>
                </tr>
                <tr>
                	<td>2</td>
                    <td>支付成功；订单未成功</td>
                    <td>成功</td>
                    <td>不成功（未）</td>
                    <td>无订单</td>
                    <td>不明付款，此时订单消息出现在http://jp.gamelife.jp/admin/telecom_unknow.php?keywords=&rel_yes=1 支付成功这个页面中，这种订单通常是支付成功了，但在rmt网站操作超时导致订单未完成</td>
                </tr>
                <tr>
                	<td>3</td>
                    <td>订单内容匹配成功，无数据</td>
                    <td>成功</td>
                    <td>成功</td>
                    <td>承认中</td>
                    <td>订单消息从支付成功的页面消失，转成正式订单</td>
                </tr>
                <tr>
                	<td>4</td>
                    <td>无内容</td>
                    <td>失敗</td>
                    <td>成功</td>
                    <td>ーー，</td>
                    <td>直接成为正式订单，但支付状态为信用卡，未支付</td>
                </tr>
            </table>
            <p>1号状态效果</p>
             <img src="../img_telecom/picture_07.gif" />
            <p>2号状态效果</p>
             <img src="../img_telecom/picture_08.gif" />
        </div>

</div>