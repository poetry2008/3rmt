<?php $this->partial('_header');?>
<div class="box_right">
      <h2>SESSION</h2>
<?php if ($allres) {?>
<table border=1 width="100%">
  <tr>
    <th>ID</th>
    <th>MISSION_KEYWORD</th>
    <th>MISSION_NAME</th>
    <th>STATE</th>
    <th>START_AT</th>
    <th>END_AT</th>
    <th>Opterations</th>
  </tr>
<?php foreach($allres as $key => $res) {?>
  <tr>
    <td><?php echo $res->s_id;?></td>
    <td><?php echo $res->keyword;?></td>
    <td><?php echo $res->name;?></td>
    <td><?php
    if($res->forced==0&&$res->end_at==0){
     echo "running";
    }else{
     echo "stoped";
    }
    ?></td>
    <td><?php echo date('Y/m/d H:i:s', $res->start_at);?></td>
    <td><?php echo date('Y/m/d H:i:s', $res->end_at);?></td>
    <td>
      <a href="index.php?action=index&controller=record&filter=all&session_id=<?php echo
      $res->s_id;?>">records_all</a>
      <a href="index.php?action=index&controller=record&session_id=<?php echo $res->s_id;?>">records</a>
      <a href="index.php?action=delete&controller=session&session_id=<?php echo
      $res->s_id."&mission_id=".$res->mission_id;?>">del</a>
      <?php
      if($res->forced==0&&$res->end_at==0){
      ?>
      <a href="index.php?action=stop&controller=session&id=<?php echo
      $res->s_id;?>">stop</a>
      <?php }?>
    </td>
  </tr>
<?php }?>
</table>
<?php 
if($pager){
  if($pager->currentPageNumber>$pager->firstPageNumber){
    $prev = $pager->prevPageNumber;
    $url = $pageurl."&page=".$prev;
    echo "[ <a href='".$url."'>";
    echo "prev";
    echo "</a> ]";
  }
  for($i=1;$i<=$pager->pageCount;$i++){
    $url = $pageurl."&page=".$i;
    echo "[ <a href='".$url."'>";
    echo $i;
    echo "</a> ]";
  }
  if($pager->currentPageNumber<$pager->lastPageNumber){
    $next = $pager->nextPageNumber; 
    $url = $pageurl."&page=".$next;
    echo "[ <a href='".$url."'>";
    echo "next";
    echo "</a> ]";
  }
}
} else {?>
no sessions
<?php }?>
</div>
<?php $this->partial('_footer');?>
