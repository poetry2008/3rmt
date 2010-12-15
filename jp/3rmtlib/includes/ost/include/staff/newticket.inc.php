<?php
if(!defined('OSTSCPINC') || !is_object($thisuser) || !$thisuser->isStaff()) die('Access Denied');
$info=($_POST && $errors)?Format::input($_POST):array(); //on error...use the post data
?>
<div width="100%">
    <?if($errors['err']) {?>
        <p align="center" id="errormessage"><?=$errors['err']?></p>
    <?}elseif($msg) {?>
        <p align="center" class="infomessage"><?=$msg?></p>
    <?}elseif($warn) {?>
        <p class="warnmessage"><?=$warn?></p>
    <?}?>
</div>
<table width="80%" border="0" cellspacing=1 cellpadding=2>

<?php 
if (strspn("MSIE",$_SERVER["HTTP_USER_AGENT"])==4){
?>
   <form id='newtform'  action="tickets.php" method="post" enctype="multipart/form-data">
<?php }else { 
?>
   <form id='newtform' onsubmit = 'return checkNg();' action="tickets.php" method="post" enctype="multipart/form-data">
<?php
}
?>
    <input type='hidden' name='a' value='open'>
    <tr><td align="left" colspan=2>【重要】必須項目だけ入力してください。任意項目は初期値から変更しないように.</td></tr>
    <tr>
        <td align="left" nowrap width="20%"><b>メールアドレス:</b></td>
        <td>
            <input type="text" id="email" name="email" size="25" value="<?=$info['email']?>">
            &nbsp;<font class="error"><b>*</b>&nbsp;<?=$errors['email']?></font>
            <? if($cfg->notifyONNewStaffTicket()) {?>
               &nbsp;&nbsp;&nbsp;
               <input type="checkbox" name="alertuser" <?=(!$errors || $info['alertuser'])? 'checked': ''?>>Send alert to user.
            <?}?>
        </td>
    </tr>
    <tr>
        <td align="left" ><b>お名前:</b></td>
        <td>
            <input type="text" id="name" name="name" size="25" value="<?=$info['name']?>">
            &nbsp;<font class="error"><b>*</b>&nbsp;<?=$errors['name']?></font>
        </td>
    </tr>
    <tr>
        <td align="left">電話番号:</td>
        <td><input type="text" name="phone" size="25" value="<?=$info['phone']?>">
            &nbsp;内線番号&nbsp;<input type="text" name="phone_ext" size="6" value="<?=$info['phone_ext']?>">
            <font class="error">&nbsp;<?=$errors['phone']?></font></td>
    </tr>
    <tr height=2px><td align="left" colspan=2 >&nbsp;</td</tr>
    <tr>
        <td align="left"><b>種別:</b></td>
        <td>
            <select name="source">
                <option value="" selected >ソースを選択してください</option>
                <option value="Phone" <?=($info['source']=='Phone')?'selected':''?>>電話</option>
                    <option value="Email" <?=($info['source']=='Email' or !isset($info['source']) )?'selected':''?>>Ｅメール</option>
                <option value="Other" <?=($info['source']=='Other')?'selected':''?>>その他</option>
            </select>
            &nbsp;<font class="error"><b>*</b>&nbsp;<?=$errors['source']?></font>
        </td>
    </tr>
    <tr>
        <td align="left"><b>サイト名:</b></td>
        <td>
            <select name="deptId" id = 'deptId'  onChange="getDeptNg(this.value)">
                <option value="" selected >サイトを選択してください</option>
                <?
                 $services= db_query('SELECT dept_id,dept_name FROM '.DEPT_TABLE.' ORDER BY dept_name');
                 while (list($deptId,$dept) = db_fetch_row($services)){
                    $selected = ($info['deptId']==$deptId)?'selected':''; ?>
                    <option value="<?=$deptId?>"<?=$selected?>><?=$dept?></option>
                <?
                 }?>
            </select>
            &nbsp;<font class="error"><b>*</b>&nbsp;<?=$errors['deptId']?></font>
        </td>
    </tr>
    <tr>
        <td align="left"><b>件名:</b></td>
        <td>
            <input type="text" name="subject" size="35" value="<?=$info['subject']?>" id="subject">
            &nbsp;<font class="error">*&nbsp;<?=$errors['subject']?></font>
        </td>
    </tr>
    <tr>
        <td align="left" valign="top"><b>メール本文:</b></td>
        <td>
            <i>この内容は顧客に通知されます.</i><font class="error"><b>*&nbsp;<?=$errors['issue']?></b></font><br/>
            <?
            $sql='SELECT premade_id,title FROM '.KB_PREMADE_TABLE.' WHERE isenabled=1';
            $canned=db_query($sql);
            if($canned && db_num_rows($canned)) {
            ?>
             テンプレート:&nbsp;
              <select id="canned" name="canned"
                onChange="getCannedResponse(this.options[this.selectedIndex].value,this.form,'issue');this.selectedIndex='0';" >
                <option value="0" selected="selected">　選択してください </option>
                <?while(list($cannedId,$title)=db_fetch_row($canned)) { ?>
                <option value="<?=$cannedId?>" ><?=Format::htmlchars($title)?></option>
                <?}?>
              </select>&nbsp;&nbsp;&nbsp;<label><input type='checkbox' value='1' name=append checked="true" />追加</label>
            <?}?>
            <textarea id="issue" name="issue" cols="85" rows="16" wrap="soft"><?=$info['issue']?></textarea></td>
    </tr>
    <?if($cfg->canUploadFiles()) {
        ?>
    <tr>
        <td>添付ファイル:</td>
        <td>
            <input type="file" name="attachment"><font class="error">&nbsp;<?=$errors['attachment']?></font>
        </td>
    </tr>
    <?}?>
    <tr>
        <td align="left" valign="top">内部メモ:</td>
        <td>
            <i>注意書き（顧客に通知されません）.</i><font class="error"><b>&nbsp;<?=$errors['note']?></b></font><br/>
            <textarea name="note" cols="55" rows="5" wrap="soft"><?=$info['note']?></textarea></td>
    </tr>

    <tr>
        <td align="left" valign="top">期限設定:</td>
        <td>
            <i>右記のタイムゾーンを基準とします(GM <?=$thisuser->getTZoffset()?>)</i>&nbsp;<font class="error">&nbsp;<?=$errors['time']?></font><br>
            <input id="duedate" name="duedate" value="<?=Format::htmlchars($info['duedate'])?>"
                onclick="event.cancelBubble=true;calendar(this);" autocomplete=OFF>
            <a href="#" onclick="event.cancelBubble=true;calendar(getObj('duedate')); return false;"><img src='images/cal.png'border=0 alt=""></a>
            &nbsp;&nbsp;
            <?php
             $min=$hr=null;
             if($info['time'])
                list($hr,$min)=explode(':',$info['time']);
                echo Misc::timeDropdown($hr,$min,'time');
            ?>
            &nbsp;<font class="error">&nbsp;<?=$errors['duedate']?></font>
        </td>
    </tr>
    <?
      $sql='SELECT priority_id,priority_desc FROM '.TICKET_PRIORITY_TABLE.' ORDER BY priority_urgency DESC';
      if(($priorities=db_query($sql)) && db_num_rows($priorities)){ ?>
      <tr>
        <td align="left">重要度:</td>
        <td>
            <select name="pri">
              <?
                $info['pri']=$info['pri']?$info['pri']:$cfg->getDefaultPriorityId();
                while($row=db_fetch_array($priorities)){ ?>
                    <option value="<?=$row['priority_id']?>" <?=$info['pri']==$row['priority_id']?'selected':''?> ><?=$row['priority_desc']?></option>
              <?}?>
            </select>
        </td>
       </tr>
    <? }?>
    <?php
    $services= db_query('SELECT topic_id,topic FROM '.TOPIC_TABLE.' WHERE isactive=1 ORDER BY topic');
    if($services && db_num_rows($services)){ ?>
    <tr>
        <td align="left" valign="top">題目:</td>
        <td>
            <select name="topicId">
                <option value="" selected >選択してください</option>
                <?
                 while (list($topicId,$topic) = db_fetch_row($services)){
                    $selected = ($info['topicId']==$topicId)?'selected':''; ?>
                    <option value="<?=$topicId?>"<?=$selected?>><?=$topic?></option>
                <?
                 }?>
            </select>
            &nbsp;<font class="error">&nbsp;<?=$errors['topicId']?></font>
        </td>
    </tr>
    <?
    }?>
    <tr>
        <td>担当者:</td>
        <td>
            <select id="staffId" name="staffId">
                <option value="0" selected="selected">-選択してください-</option>
                <?
                    //TODO: make sure the user's group is also active....DO a join.
                    $sql=' SELECT staff_id,CONCAT_WS(", ",lastname,firstname) as name FROM '.STAFF_TABLE.' WHERE isactive=1 AND onvacation=0 ';
                    $depts= db_query($sql.' ORDER BY lastname,firstname ');
                    while (list($staffId,$staffName) = db_fetch_row($depts)){
                        $selected = ($info['staffId']==$staffId)?'selected':''; ?>
                        <option value="<?=$staffId?>"<?=$selected?>><?=$staffName?></option>
                    <?
                    }?>
            </select><font class='error'>&nbsp;<?=$errors['staffId']?></font>
                &nbsp;&nbsp;&nbsp;
                <input type="checkbox" name="alertstaff" <?=(!$errors || $info['alertstaff'])? 'checked': ''?>>担当者にメッセージを送る .
        </td>
    </tr>
    <tr>
        <td>署名:</td>
        <td> <?php
            $appendStaffSig=$thisuser->appendMySignature();
            $info['signature']=!$info['signature']?'none':$info['signature']; //change 'none' to 'mine' to default to staff signature.
            ?>
            <div style="margin-top: 2px;">
                <label><input type="radio" name="signature" value="none" checked >　なし</label>
                <?if($appendStaffSig) {?>
                    <label> <input type="radio" name="signature" value="mine" <?=$info['signature']=='mine'?'checked':''?> > 個人の署名</label>
                 <?}?>
                 <label><input type="radio" name="signature" value="dept" <?=$info['signature']=='dept'?'checked':''?> >サイトの署名 (if any)</label>
            </div>
        </td>
    </tr>
    <tr height=2px><td align="left" colspan=2 >&nbsp;</td</tr>
    <tr>
        <td></td>
        <td>
            <input class="button" type="submit" name="submit_x" value="送信">
            <input class="button" type="reset" value="リセット">
            <input class="button" type="button" name="cancel" value="キャンセル" onClick='window.location.href="tickets.php"'>    
        </td>
    </tr>
  </form>
</table>
<script type="text/javascript">
    
    var options = {
        script:"ajax.php?api=tickets&f=searchbyemail&limit=10&",
        varname:"input",
        json: true,
        shownoresults:false,
        maxresults:10,
        callback: function (obj) { document.getElementById('email').value = obj.id; document.getElementById('name').value = obj.info; return false;}
    };
    var autosug = new bsn.AutoSuggest('email', options);

function getDeptNg(id){
var options = {
  url:"ajax.php?api=dept&f=getng&id="+id,
  callback:function(rp){
    //    ngwords = rp.responseText.parseJSON(); 
    tmpobj= eval('('+rp.responseText+')');
    ngwords = tmpobj.ng;
  }
};
Http.get(options);
}

getDeptNg(document.getElementById('deptId').value);


function checkNg(){
ngArr = ngwords.split(',');
var response = document.getElementById('issue');
var response_content = response.value;
var subject  = document.getElementById('subject');
var subject_content = subject.value;
var subject_findkeyword = new Array();
var issue_findkeyword   = new Array();
var keyword;
var result = false;
var linechanger ='\n';
for (keyword in ngArr){
  if(response_content.indexOf(ngArr[keyword])>=0 && ngArr[keyword]!=''){
    issue_findkeyword.push(ngArr[keyword])
  }
  if(subject_content.indexOf(ngArr[keyword])>=0 && ngArr[keyword]!=''){
    subject_findkeyword.push(ngArr[keyword])
  }
}
if(issue_findkeyword.length<=0 && subject_findkeyword.length<=0){
 return true;
}else{
  var keywordString = linechanger;
  for (keyword in issue_findkeyword){
    keywordString+= issue_findkeyword[keyword] + linechanger;
  }
  for (keyword in subject_findkeyword){
    keywordString+= subject_findkeyword[keyword] + linechanger;
  }
  if (issue_findkeyword.length>0 && subject_findkeyword.length>0) {
    return  confirm('NGキーワード '+keywordString+'件名と本文にがあります。このまま返信しますか?');
  } else if (issue_findkeyword.length>0) {
    return  confirm('NGキーワード '+keywordString+'本文にがあります。このまま返信しますか?');
  } else if (subject_findkeyword.length>0) {
    return  confirm('NGキーワード '+keywordString+'件名にがあります。このまま返信しますか?');
  }
  //return  confirm('NGキーワード '+keywordString+'返信内容にNGキーワードが有ります。このまま返信しますか？');
}
}
</script>
<?php 
if (strspn("MSIE",$_SERVER["HTTP_USER_AGENT"])==4){
?>
<script>
var e = document.getElementById("newtform");
e.attachEvent('onsubmit',checkNg);
</script>
<?php } ?>
