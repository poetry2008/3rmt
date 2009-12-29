<table border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td>
	  <a href="" onclick="btchange('t1'); return false;">
	    <img src="images/design/top_tab/on/tab-1.jpg" alt="新着情報" name="t_oimg1" width="129" height="35" border="0" id="t_oimg1" style="display:none" >
	    <img id="t_img1" src="images/design/top_tab/tab-1.jpg" width="129" height="35" border="0" style="display:block" >
	  </a>
	</td>
    <td>
	  <a href="" onclick="btchange('t2'); return false;">
	    <img id="t_oimg2" src="images/design/top_tab/on/tab-2.jpg" width="120" height="35" border="0" style="display:block" >
		<img src="images/design/top_tab/tab-2.jpg" alt="新着商品" width="120" height="35" border="0" id="t_img2" style="display:none" >
	  </a>
	</td>
    <td>
	  <a href="" onclick="btchange('t3'); return false;">
	    <img id="t_oimg3" src="images/design/top_tab/on/tab-3.jpg" width="122" height="35" border="0" style="display:none" >
	    <img src="images/design/top_tab/tab-3.jpg" alt="商品レビュー" width="122" height="35" border="0" id="t_img3" style="display:block" >
	  </a>
	</td>
    <td>
	  <a href="" onclick="btchange('t4'); return false;">
	    <img id="t_oimg4" src="images/design/top_tab/on/tab-4.jpg" width="129" height="35" border="0" style="display:none" >
	    <img src="images/design/top_tab/tab-4.jpg" alt="オススメ商品" width="129" height="35" border="0" id="t_img4" style="display:block" >
	  </a>
	</td>
  </tr>
  <tr>
    <td colspan="4"><img src="images/design/top_tab/tab-header.jpg" width="500" height="8" ></td>
  </tr>
  <tr>
    <td height="150" colspan="4" valign="top" id="tabtable">
	<div align="center" class="main"><img src="images/design/loadingcircle.gif" align="absmiddle"> NowLoading........</div>
	</td>
  </tr>
  <tr>
    <td colspan="4" valign="top"><img src="images/design/top_tab/tab-bottom.jpg" width="500" height="18" ></td>
  </tr>
</table><br>

<?php
  if ($banner = tep_banner_exists('dynamic', 'top_banner')) {
    echo tep_display_banner('static', $banner);
  }
?>