<?php
if(!defined('OSTCLIENTINC') || !is_object($thisclient) || !$thisclient->isValid()) die('Kwaheri');

$deptIdSql = ' select dept_id from ost_help_topic where topic_id ='. SITE_TOPIC_ID;
$tmpres= db_query($deptIdSql);
$row =  db_fetch_array($tmpres);
define('SITE_DEPT_ID',$row['dept_id']);

//Get ready for some deep shit.
$qstr='&'; //Query string collector
$status=null;
if($_REQUEST['status']) { //Query string status has nothing to do with the real status used below.
    $qstr.='status='.urlencode($_REQUEST['status']);
    //Status we are actually going to use on the query...making sure it is clean!
    switch(strtolower($_REQUEST['status'])) {
     case 'open':
     case 'closed':
        $status=$_REQUEST['status'];
        break;
     case 'all':
     default:
        $status=''; //ignore
    }
}

//Restrict based on email of the user...STRICT!
$qwhere =' WHERE ticket.dept_id= '.SITE_DEPT_ID.' and email='.db_input($thisclient->getEmail());
//STATUS
if($status){
    $qwhere.=' AND status='.db_input($status);    
}
//Admit this crap sucks...but who cares??
$sortOptions=array('date'=>'ticket.created','ID'=>'ticketID','pri'=>'priority_id','dept'=>'dept_name');
$orderWays=array('DESC'=>'DESC','ASC'=>'ASC');

//Sorting options...
if($_REQUEST['sort']) {
        $order_by =$sortOptions[$_REQUEST['sort']];
}
if($_REQUEST['order']) {
    $order=$orderWays[$_REQUEST['order']];
}
if($_GET['limit']){
    $qstr.='&limit='.urlencode($_GET['limit']);
}

$order_by =$order_by?$order_by:'ticket.created';
$order=$order?$order:'DESC';
$pagelimit=$_GET['limit']?$_GET['limit']:PAGE_LIMIT;
$page=($_GET['p'] && is_numeric($_GET['p']))?$_GET['p']:1;

$qselect = 'SELECT ticket.ticket_id,ticket.ticketID,ticket.topic_id,ticket.dept_id,isanswered,ispublic,subject,name,email '.
           ',dept_name,status,source,priority_id ,ticket.created ';
$qfrom=' FROM '.TICKET_TABLE.' ticket LEFT JOIN '.DEPT_TABLE.' dept ON ticket.dept_id=dept.dept_id ';
//Pagenation stuff....wish MYSQL could auto pagenate (something better than limit)
$total=db_count('SELECT count(*) '.$qfrom.' '.$qwhere);
$pageNav=new Pagenate($total,$page,$pagelimit);
$pageNav->setURL(tep_href_link('view.php',$qstr.'&sort='.urlencode($_REQUEST['sort']).'&order='.urlencode($_REQUEST['order']),'SSL'));

//Ok..lets roll...create the actual query
$qselect.=' ,count(attach_id) as attachments ';
$qfrom.=' LEFT JOIN '.TICKET_ATTACHMENT_TABLE.' attach ON  ticket.ticket_id=attach.ticket_id ';
$qgroup=' GROUP BY ticket.ticket_id';
$query="$qselect $qfrom $qwhere $qgroup ORDER BY $order_by $order LIMIT ".$pageNav->getStart().",".$pageNav->getLimit();
//echo $query;
$tickets_res = db_query($query);
$showing=db_num_rows($tickets_res)?$pageNav->showing():"";
$_status = '_'.$status;
$_open = TEXT_OPEN;
$_closed = TEXT_CLOSED;

$results_type=($status)?($$_status).'':TEXT_ALL;
$negorder=$order=='DESC'?'ASC':'DESC'; //Negate the sorting..
$_negorder=$negorder=="DESC"?TEXT_SORT:TEXT_DESC_SORT;
?>
<div>
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="center" id="infomessage"><?=$msg?></p>
    <?}elseif($warn) {?>
        <p id="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<div class="tickets_contents">
 <table border="0" cellspacing="1" cellpadding="0" align="center" class="tickets_lout" width="100%">
    <tr>
        <td width="60%" class="msg"><?=$showing?>&nbsp;&nbsp;</td>
        <td nowrap align="right">
            <a class="log_out" href="<?php echo tep_href_link('logout.php','','SSL');?>"><img src="images/door_out.gif"><?php echo TEXT_LOGOUT?></a>
        </td>
    </tr>
 </table>
<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="product_listing">
        <tr>
        <?php
        if($_GET['status'] == 'all' || !isset($_GET['status'])){
        
          $products_image_all = 'button_large_hover.gif';
        }else{
          $products_image_all = 'button_large.gif';
        } 
        ?>
        <td width="33%"><a class="product_listing_link"
        style="background:url(images/design/box/<?php echo $products_image_all;?>)" href="<?php echo tep_href_link('view.php','status=all','SSL');?>;"><?php echo TEXT_ALL;?></a></td>
        <?php
        if($_GET['status'] == 'open'){
        
          $products_image_one = 'button_large_hover.gif';
        }else{
          $products_image_one = 'button_large.gif';
        } 
        ?>
        <td width="33%"><a class="product_listing_link"
        style="background:url(images/design/box/<?php echo $products_image_one;?>)" href="<?php echo tep_href_link('view.php','status=open','SSL');?>"><?php echo TEXT_OPEN;?></a></td>
        <?php
        if($_GET['status'] == 'closed'){
        
          $products_image = 'button_large_hover.gif';
        }else{
          $products_image = 'button_large.gif';
        } 
        ?>
        <td width="33%" style="border-right:1px solid #666666;"><a id="jingyi"
        class="product_listing_link" style="background: url(images/design/box/<?php echo $products_image;?>)" href="<?php echo tep_href_link('view.php','status=closed','SSL');?>"><?php echo TEXT_CLOSED?></a></td>
    </tr>
</table>
 <table width="100%" border="0" cellspacing=0 cellpadding=2>
    <tr><td>
     <table width="100%" border="0" cellspacing="1" cellpadding="0" class="tickets" align="center">
        <tr>
          <td align="left" width="15%">&nbsp;<a href="<?php echo tep_href_link('view.php','sort=ID&order='.$negorder.$qstr,'SSL');?>" title="<?php echo TEXT_TITLE_NUM_QUERY;?><?=$_negorder?>"><?php echo TEXT_NUM_QUERY;?></a></td>
          <td width="15%" align="left">&nbsp;<a href="<?php echo tep_href_link('view.php','sort=date&order='.$negorder.$qstr,'SSL');?>" title=" <?=TEXT_TITLE_CREAT_DATE.$_negorder?>"><?php echo TEXT_CREAT_DATE?></a></td>
          <td align="left">&nbsp;<?php echo TEXT_SUBJECT;?></td>
        </tr>
        <?
        $class = "row1";
        $total=0;
        if($tickets_res && ($num=db_num_rows($tickets_res))):
            $defaultDept=Dept::getDefaultDeptName();
            while ($row = db_fetch_array($tickets_res)) {

              if($row['dept_id']!=SITE_DEPT_ID){
                continue;
              }
                $dept=$row['ispublic']?$row['dept_name']:$defaultDept; //Don't show hidden/non-public depts.
                $subject=Format::htmlchars(Format::truncate($row['subject'],40));
                $ticketID=$row['ticketID'];
                if($row['isanswered'] && !strcasecmp($row['status'],'open')) {
                    $subject="<b>$subject</b>";
                    $ticketID="<b>$ticketID</b>";
                }
                ?>
        <tr class="<?=$class?> " id="<?=$row['ticketID']?>">
          <td align="left" title="<?=$row['email']?>" nowrap><a class="Icon <?=strtolower($row['source'])?>Ticket" title="<?=$row['email']?>" href="<?php echo tep_href_link('view.php','id='.$row['ticketID'],'SSL');?>"><?=$ticketID?></a>
          </td>
          <td nowrap><?=Format::db_date($row['created'])?></td>
                                    <?php 
                      $_status = '_'.$row['status'];
            $_open = TEXT_OPEN;
            $_closed = TEXT_CLOSED;
                  ?>
          <td><a href="<?php echo tep_href_link('view.php','id='.$row['ticketID'],'SSL');?>"><?=$subject?></a><?=$row['attachments']?"<span class='Icon file'>&nbsp;</span>":''?></td>
        </tr>
            <?
            $class = ($class =='row2') ?'row1':'row2';
            } //end of while.
        else: //not tickets found!! ?> 
            <tr class="<?=$class?>"><td colspan=7><br /><b><?php echo TEXT_CANNOT_FIND;?></b></td></tr>
        <?
        endif; ?>
     </table>
    </td></tr>
    <tr><td>
    <?
    if($num>0 && $pageNav->getNumPages()>1){ //if we actually had any tickets returned?>
     <tr><td style="text-align:left;padding-left:20px; font-size:14px;"><?php echo TEXT_PAGE;?><?=$pageNav->getPageLinks()?>&nbsp;</td></tr>
    <?}?>
 </table>
</div>
<?
