<?php
/*
  $Id$
*/
?>
    <td valign="top" id="contents">
    <!-- 切换 -->
    <div class="top_index_image" id="imenu01">
    	<div class="s_link"><a href="javascript:void(0);" onclick="toggle_index_menu(0);">カタカナ</a></div>
        <div class="s_link02"><a href="javascript:void(0);" onclick="toggle_index_menu(1);">英数字</a></div>  
    </div>
    <div class="top_index_warpper" id="icontent01">
         <table class="column_middle_comment" cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td class="column_m_seach" valign="top">
                <div class="column_m_number01">
                        <a href="javascript:void(0);" onclick="search_top_category('ア');">ア</a>
                        <a href="javascript:void(0);" onclick="search_top_category('カ');">カ</a>
                        <a href="javascript:void(0);" onclick="search_top_category('サ');">サ</a>
                        <a href="javascript:void(0);" onclick="search_top_category('タ');">タ</a>
                        <a href="javascript:void(0);" onclick="search_top_category('ナ');">ナ</a>
    <br><br><br>
                        <a href="javascript:void(0);" onclick="search_top_category('ハ');">ハ</a>
                        <a href="javascript:void(0);" onclick="search_top_category('マ');">マ</a>
                        <a href="javascript:void(0);" onclick="search_top_category('ヤ');">ヤ</a>
                        <a href="javascript:void(0);" onclick="search_top_category('ラ');">ラ</a>
                        <a href="javascript:void(0);" onclick="search_top_category('ワ');">ワ</a>
               </div>
    </td>
            <td class="column_m_image"><img src="images/design/search_img_big.gif" alt="seach"></td>
            </tr>
         </table>
    </div>
    <!-- 切换 end -->
    <!-- 切换02 -->
    <div class="top_index_image" id="imenu02" style="display:none;">
    	<div class="s_link_hover"><a href="javascript:void(0);" onclick="toggle_index_menu(0);">カタカナ</a></div>
        <div class="s_link02_hover"><a href="javascript:void(0);" onclick="toggle_index_menu(1);">英数字</a></div>  
    </div>
    <div class="top_index_warpper" id="icontent02" style="display:none;">
         <table class="column_middle_comment" cellpadding="0" cellspacing="0" border="0">
            <tr>
            <td class="column_m_seach" valign="top">
                <div class="column_m_number02">
                        <a href="javascript:void(0);"
                        onclick="search_top_category('a,b,c');">A～C</a>
                        <a href="javascript:void(0);" onclick="search_top_category('d,e,f');">D～F</a>
                        <a href="javascript:void(0);" onclick="search_top_category('g,h,i');">G～I</a>
                        <a href="javascript:void(0);" onclick="search_top_category('j,k,l');">J～L</a>
                        <a href="javascript:void(0);" onclick="search_top_category('m,n,o');">M～O</a>
    <br><br><br>
                        <a href="javascript:void(0);" onclick="search_top_category('p,q,r');">P～R</a>
                        <a href="javascript:void(0);" onclick="search_top_category('s,t,u');">S～U</a>
                        <a href="javascript:void(0);" onclick="search_top_category('v,w');">V～W</a>
                        <a href="javascript:void(0);" onclick="search_top_category('x,y,z');">X～Z</a>
                        <a href="javascript:void(0);" onclick="search_top_category('1,2,3,4,5,6,7,8,9');">1～9</a>
               </div>
    </td>
            <td class="column_m_image"><img src="images/design/search_img_big.gif" alt="seach"></td>
            </tr>
         </table>
    </div>
    <!-- 切换02 end -->
    
<div id="showca" style="display:none">
</div>
<?php 
  // @TODO 改成设置
  #$contents1 = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '10' and site_id = '" . SITE_ID . "'");  //top1
  #$result1   = tep_db_fetch_array($contents1) ;
  #$contents2 = tep_db_query("select * from ".TABLE_INFORMATION_PAGE." where pID = '11' and site_id = '" . SITE_ID . "'");  //top2
  #$result2   = tep_db_fetch_array($contents2) ;
  
  include(DIR_WS_MODULES . FILENAME_LATEST_NEWS);

?>
<?php
  //echo DEFAULT_PAGE_BOTTOM_CONTENTS;
?>
</td>
<td rowspan="2" valign="top" class="right_colum_border">
<?php require(DIR_WS_INCLUDES.'column_right.php')?>
</td>
</tr>
<tr><td colspan="2">
<?php 
include(DIR_WS_MODULES . 'categories_banner_text.php');
?>
</td>
    <!-- body_text_eof //--> 
