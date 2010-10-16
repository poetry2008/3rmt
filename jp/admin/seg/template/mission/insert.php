<?php $this->partial('_header');?>
<div class="box_right">
<h2>INSERT MISSION<h2>
<form action="index.php?action=insert&controller=mission" method="post">
<table border=1 width="100%">
  <tr>
    <td>MISSION_NAME</td>
    <td align="left">
    <input type="text" name="name" >
    eg: demo
    </td>
  </tr>
  <tr>
    <td>MISSION_KEYWORD</td>
    <td align="left">
    <input type="text" name="keyword" >
    eg: link:http://www.bitem.jp リンク
    </td>
  </tr>
  <tr>
    <td>MISSION_PAGE_LIMIT</td>
    <td align="left">
    <input type="text" name="page_limit" >
    eg: 100  only number
    </td>
  </tr>
  <tr>
    <td>MISSION_RESULT_LIMIT</td>
    <td align="left">
    <input type="text" name="result_limit" >
    eg: 100  only number
    </td>
  </tr>
  <tr>
    <td>MISSION_ENABLED</td>
    <td align="left">
      <input type="radio" name="enabled" value="1" checked>yes
      <input type="radio" name="enabled" value="0" >no
      please check one
    </td>
  </tr>
  <tr>
  <tr>
    <td>MISSION_ENGINE</td>
    <td align="left">
    <?php echo $engineSelect; ?>
    please select one defaule is yahoo
    </td>
  </tr>
  <td colspan='2'>
    <input type="submit" name="submit" value="add">
    <input type="reset" value="reset">
  </tr>
</table>
</form>
</div>
<?php $this->partial('_footer');?>
