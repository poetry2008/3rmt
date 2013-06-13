<HTML>
<HEAD>
<!-- locale-sensitive -->
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8">
<TITLE><?php echo NOTICE_NO_ACCESS_TEXT;?></TITLE>
<script type="text/javascript">
function redirect_back_url(back_url) {
  window.location.href = back_url;
}
window.onload = setTimeout(function () {redirect_back_url('<?php echo $back_url?>')}, 3000);
</script>
</HEAD>
<BODY BGCOLOR="#FFFFFF" >
<BLOCKQUOTE> 

    <P>&nbsp;</P>
        
  <DIV ALIGN="center">
 
<TABLE WIDTH="400" BORDER="0" CELLSPACING="0" CELLPADDING="0">
	<TR BGCOLOR="#999999">
		<TD>
			<TABLE WIDTH="500" BORDER="0" CELLSPACING="1" CELLPADDING="5" ALIGN="center">
				<TR BGCOLOR="#990000">
					<TD COLSPAN="2">
<!-- locale-sensitive -->
						<DIV ALIGN="left">
							<FONT COLOR="#FFFFFF" SIZE="3"><B><?php echo NOTICE_NO_ACCESS_TEXT;?></B> </FONT>
						</DIV>
					</TD>
				</TR>
				<TR>
					<TD BGCOLOR="#FFFFFF" COLSPAN="2" VALIGN="middle">
						<TABLE WIDTH="100%" BORDER="0">
							<TR>
								<TD>
									<IMG SRC="/libImage/warning.gif" WIDTH="40" HEIGHT="40" ALIGN="middle">
									</TD>
									<TD>
										<FONT SIZE="2">
<!-- locale-sensitive -->
								<?php echo NOTICE_NO_ACCESS_READ_TEXT;?><BR>
											</FONT> 
									</TD>
								</TR>
							</TABLE>
						</TD>
					</TR>
				</TABLE>
			</TD>
		</TR>
	</TABLE>
	<?php echo NOTICE_NO_ACCESS_LINK_TEXT;?><BR>
<?php
$sites_info_raw = tep_db_query("select * from sites where id = '1'");
$sites_info_res = tep_db_fetch_array($sites_info_raw);
?>
        <A HREF="<?php echo $sites_info_res['url'];?>"><?php echo $sites_info_res['url'];?></A>
    <br>
    <font size="2"><a href="<?php echo $back_url;?>"><?php echo NOTICE_NO_ACCESS_BACK_TEXT;?></a></font>
  </DIV>
</BLOCKQUOTE>
</HTML>
