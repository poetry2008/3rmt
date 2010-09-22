<?php $this->partial('_header');?>
<div class="box_right">
<h2>INSERT FILTER</h2>
<form action="index.php?action=insert&controller=filter" method="post">
<table border=1 width="100%">
  <tr>
    <td>SITE_URL</td>
    <td>
    <input type="text" name="record_siteurl" >
    </td>
  </tr>
  <tr>
    <td>SITE_STATE</td>
    <td>
      <input type="radio" name="state" value="1" checked>yes
      <input type="radio" name="state" value="0" >no
    </td>
  </tr>
  <tr>
  <td colspan="2">
    <input type="submit" name="submit" value="insert">
  </tr>
</table>
</form>
	</div>
<?php $this->partial('_footer');?>
