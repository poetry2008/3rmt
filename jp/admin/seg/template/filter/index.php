<?php $this->partial('_header');?>
<div class="box_right">
<h2>FILTER INDEX</h2>
<?php if ($filters) {?>
<table border=1 width="100%">
  <tr>
    <th>SITE_ID</th>
    <th>filter</th>
    <th>SITE_STATE</th>
    <th>Opterations</th>
  </tr>
<?php foreach($filters as $filter) {?>
  <tr>
    <td><?php echo $filter->id;?></td>
    <td><?php echo $filter->record_siteurl;?></td>
    <td><?php echo $filter->state;?></td>
    <td>
     <a href = "index.php?action=delete&controller=filter&sf_id=<?php echo
     $filter->id;?>">DELETE</a>
     <a href = "index.php?action=edit&controller=filter&sf_id=<?php echo
     $filter->id;?>">EDIT</a>
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
no filters
<?php }?>
<br>
<a href = "index.php?action=insert&controller=filter">
INSERT
</a>
</div>
<?php $this->partial('_footer');?>
