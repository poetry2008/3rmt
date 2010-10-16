<?php $this->partial('_header');?>
<div class="box_right">
      <h2>EFF_SESSION_RECORD</h2>
<?php if ($records) {?>
<table border=1 width="100%">
  <tr>
  <!--
    <th>ID</th>
    <th>SESSION_ID</th>
    <th>MISSION_ID</th>
    <th>MISSION_NAME</th>
  -->
    <th>KEYWORD</th>
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
    <th>Opterations</th>
  </tr>
<?php foreach($records as $record) {?>
  <tr>
    <!--
    <td><?php echo $record->id;?></td>
    <td><?php echo $record->session_id;?></td>
    <td><?php echo $record->mission_id;?></td>
    <td><?php echo $record->mission_name;?></td>
    -->
    <td><?php echo $record->keyword;?></td>
    <td><?php echo $record->title;?></td>
    <!--
    <td><?php echo $record->order_number;?></td>
    <td><?php echo $record->page_number;?></td>
    <td><?php echo $record->order_total_number;?></td>
    -->
    <td><?php echo $record->siteurl;?></td>
    <td><?php echo $record->fullurl;?></td>
    <td><?php echo $record->description;?></td>
    <td><?php echo date('Y/m/d H:i:s', $record->created_at);?></td>
    <td>
      DELETE
    </td>
  </tr>
<?php }?>
</table>
<?php } else {?>
no records
<?php }?>
</div>
<?php $this->partial('_footer');?>
