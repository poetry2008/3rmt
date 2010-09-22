<?php $this->partial('_header');?>
<div class="box_right">
<h2>EDIT FILTER</h2>
<?php if ($row) {?>
<form action="index.php?action=edit&controller=filter" method="post">
<table border=1 width="100%">
  <tr>
    <td>SITE_URL</td>
    <td>
    <input type="text" name="record_siteurl" value="<?php echo $row->record_siteurl;?>">
    </td>
  </tr>
  <tr>
    <td>SITE_STATE</td>
    <td>
    <?php 
    if($row->state){
    ?>
      <input type="radio" name="state" value="1" checked>yes
      <input type="radio" name="state" value="0" >no
    <?php
    }else{
    ?>
      <input type="radio" name="state" value="1" >yes
      <input type="radio" name="state" value="0" checked>no
    <?php
    }
    ?>
    </td>
  </tr>
  <tr>
  <td colspan='2'>
    <input type="hidden" name="id" value="<?php echo $row->id;?>">
    <input type="submit" name="submit" value="update">
  </tr>
</table>
</form>
<?php } else {?>
no site_urls
<?php }?>
	</div>
<?php $this->partial('_footer');?>
