<?php $this->partial('_header');?>
<div class="box_right">
<h2>EDIT MISSION</h2>
<?php if ($row) {?>
<form action="index.php?action=edit&controller=mission" method="post">
<table border=1 width="100%">
  <tr>
    <td>MISSION_NAME</td>
    <td align="left">
    <input type="text" name="name" value="<?php echo $row->name;?>">
    </td>
  </tr>
  <tr>
    <td>MISSION_KEYWORD</td>
    <td align="left">
    <input type="text" name="keyword" value="<?php echo $row->keyword;?>">
    </td>
  </tr>
  <tr>
    <td>MISSION_PAGE_LIMIT</td>
    <td align="left">
    <input type="text" name="page_limit" value="<?php echo $row->page_limit;?>">
    </td>
  </tr>
  <tr>
    <td>MISSION_RESULT_LIMIT</td>
    <td align="left">
    <input type="text" name="result_limit" value="<?php echo $row->result_limit;?>">
    </td>
  </tr>
  <tr>
    <td>MISSION_ENABLED</td>
    <td align="left">
    <?php 
    if($row->enabled){
    ?>
      <input type="radio" name="enabled" value="1" checked>yes
      <input type="radio" name="enabled" value="0" >no
    <?php
    }else{
    ?>
      <input type="radio" name="enabled" value="1" >yes
      <input type="radio" name="enabled" value="0" checked>no
    <?php
    }
    ?>
    </td>
  </tr>
  <tr>
    <td>MISSION_ENGINE</td>
    <td align="left">
    <?php echo $engineSelect; ?>
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
