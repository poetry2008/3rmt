<?php $this->partial('_header');?>

    <div class="box_right">
    <h2>MISSION</h2>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="index.php?action=insert&controller=mission">
    ADD_MISSION
    </a>
<?php if ($missions) {?>
<table border=1 width="100%">
  <tr>
    <th>Name</th>
    <th>Keyword</th>
    <th>Enabled</th>
    <th>Status</th>
    <th>Opterations</th>
  </tr>
<?php foreach($missions as $mission) {?>
  <tr>
    <td><?php echo $mission->name;?></td>
    <td><?php echo $mission->keyword;?></td>
    <td><?php echo $mission->enabled;?></td>
    <td><?php echo $mission->getStatus($mission->id);?></td>
    <td>
      <a target="_blank" href='index.php?action=start&controller=mission&id=<?php echo $mission->id;?>'>start</a>
      <a href="index.php?action=index&controller=session&mission_id=<?php echo $mission->id;?>">sessions</a>
      <a href="index.php?action=effrecord&controller=mission&mission_id=<?php echo
      $mission->id;?>">eff sessions record</a>
      <a href="index.php?action=edit&controller=mission&mission_id=<?php echo
      $mission->id;?>">edit</a>
      <a href="index.php?action=delete&controller=mission&mission_id=<?php echo
      $mission->id;?>">delete</a>
      <?php 
      if ($mission->getStatus($mission->id)){
      ?>
      <a href="index.php?action=stop&controller=mission&mission_id=<?php echo
      $mission->id;?>">stop</a>
      <?php
      }
      ?>
    </td>
  </tr>
<?php }?>
</table>
<?php 
if($pager){
  if($pager->currentPageNumber>$pager->firstPageNumber){
    $prev = $pager->prevPageNumber;
    $url = $pageurl."page=".$prev;
    echo "[ <a href='".$url."'>";
    echo "prev";
    echo "</a> ]";
  }
  for($i=1;$i<=$pager->pageCount;$i++){
    $url = $pageurl."page=".$i;
    echo "[ <a href='".$url."'>";
    echo $i;
    echo "</a> ]";
  }
  if($pager->currentPageNumber<$pager->lastPageNumber){
    $next = $pager->nextPageNumber; 
    $url = $pageurl."page=".$next;
    echo "[ <a href='".$url."'>";
    echo "next";
    echo "</a> ]";
  }
}
} else {?>
no missions
<?php }?>
</div>
<?php $this->partial('_footer');?>
