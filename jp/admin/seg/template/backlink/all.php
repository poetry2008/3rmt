<?php $this->partial('_header');?>
<div class="box_right">
      <h2>ALLBACKLINK</h2>
<?php if ($records) {?>
<table border=1 width="100%">
  <tr>
  <!--
    <th>ID</th>
    <th>SESSION_ID</th>
    <th>MISSION_ID</th>
    <th>MISSION_NAME</th>
    <th>KEYWORD</th>
  -->
    <th>TITLE</th>
    <!--
    <th>ORDER_NUMBER</th>
    <th>PAGE_NUMBER</th>
    <th>ORDER_TOTAL_NUMBER</th>
    -->
    <th>SITEURL</th>
    <th>FULLURL</th>
    <th>DESCRIPTION</th>
    <th>CREATED_AT</th>
    <!--
    <th>Opterations</th>
    -->
  </tr>
<?php foreach($records as $record) {?>
  <tr>
    <!--
    <td><?php echo $record->id;?></td>
    <td><?php echo $record->session_id;?></td>
    <td><?php echo $record->mission_id;?></td>
    <td><?php echo $record->mission_name;?></td>
    <td><?php echo $record->keyword;?></td>
    -->
    <td><?php echo $record->title;?></td>
    <!--
    <td><?php echo $record->order_number;?></td>
    <td><?php echo $record->page_number;?></td>
    <td><?php echo $record->order_total_number;?></td>
    -->
    <td><?php echo $record->siteurl;?></td>
    <td><a target='_blank' href="<?php echo $record->fullurl;?>">
    <?php echo $record->fullurl;?></a>
    </td>
    <td><?php echo $record->description;?></td>
    <td><?php echo date('Y/m/d H:i:s', $record->created_at);?></td>
    <!--
    <td>
      DELETE
      <a href="index.php?action=setshow&controller=record&session_id=<?php echo
      $status."&id=".$record->id?>">unshow</a>
    </td>
    -->
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
no records
<?php }?>
</div>
<?php $this->partial('_footer');?>
