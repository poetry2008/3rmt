<?php
/*
  $Id$
*/
?>
<!-- information //-->
<div class="box_title"><?php echo BOX_HEADING_INFORMATION;?></div> 
<ul id="box">
<?php
// ccdd
  $contents_page = tep_db_query("
      select * 
      from ".TABLE_INFORMATION_PAGE." 
      where status = 1 
        and site_id = ".SITE_ID." 
      order by sort_id 
  ");
   while($result = tep_db_fetch_array($contents_page)){
             echo '<li><a href="'.tep_href_link(FILENAME_PAGE,'pID='.$result['pID'],NONSSL).'">'.$result['heading_title'].'</a></li>'."\n" ;
  } 
// Extra Pages ADDED END
?>
<li><?php echo '<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">' . BOX_INFORMATION_CONTACT . '</a>';?></li> 
<li><?php echo '<a href="' . tep_href_link('reorder.php') . '">' . '再配達フォーム' . '</a>'; ?></li> 
</ul>
<!-- information_eof //-->
