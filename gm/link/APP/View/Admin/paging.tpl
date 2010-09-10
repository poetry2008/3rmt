<table summary="1" width="100%" class="page">
<tr>	
<td width="70%">
<!--{if $config->pageNo > 0}-->

		<a href="<!--{url controller=$config->controller action=$config->action table=$config->name}-->" onkeypress='return fnChangePage("<!--{$config->name}-->",0);' onclick='return fnChangePage("<!--{$config->name}-->",0);'>
		<!--{_t key=table_first_page}-->
		</a>
	
	<!--{math assign=temp equation=" x -1 " x=$config->pageNo}-->
		<a href="<!--{url controller=$config->controller action=$config->action table=$config->name}-->" onkeypress="return fnChangePage('<!--{$config->name}-->',<!--{$config->pageNo}--> - 1);" onclick="return fnChangePage('<!--{$config->name}-->',<!--{$config->pageNo}--> - 1);">
			<!--{_t key=table_prev_page}-->
		</a>
<!--{/if}-->

	<select tabindex="3" onchange="fnChangePage('<!--{$config->name}-->',this.value)">
		<!--{section name=p loop=$pageCount}-->
		<!--{assign var=i value=$smarty.section.p.iteration}-->				
			<!--{if $i == $config->pageNo+1 }-->
				<option value="<!--{$config->pageNo}-->" selected="selected">
				<!--{_t key=table_the}-->
				<!--{$i}-->
				<!--{_t key=table_page}--> 
				</option>
			<!--{else}-->
				<option value="<!--{math equation='x -1 ' x=$i}-->" >
				<!--{_t key=table_the}-->
				<!--{$i}-->
				<!--{_t key=table_page}--> 
				</option>					
			<!--{/if}-->
		<!--{/section}-->				
	</select>
	/
	<!--{_t key=table_all}-->
	<!--{$pageCount}-->
	<!--{_t key=table_page}-->			

<!--{if $config->pageNo < $pageCount-1}-->
		<a href="<!--{url controller=$config->controller action=$config->action  table=$config->name}-->" onkeypress="return fnChangePage('<!--{$config->name}-->',<!--{$config->pageNo}-->+1)" onclick="return fnChangePage('<!--{$config->name}-->',<!--{$config->pageNo}-->+1)">
			<!--{_t key=table_next_page}-->
		</a>|
		<a href="<!--{url controller=$config->controller action=$config->action  table=$config->name}-->" onkeypress="return fnChangePage('<!--{$config->name}-->',<!--{$config->pageCount}-->-1)" onclick="return fnChangePage('<!--{$config->name}-->',<!--{$config->pageCount}-->-1)">
			<!--{_t key=table_last_page}-->
		</a>

<!--{/if}-->
	</td>
<td width="30%">
	<!--{_t key=table_setup}-->
	<select tabindex="4" onchange="fnChangeSize('<!--{$config->name}-->',this.value);">
		<!--{if $this->pageSize eq 5}-->
			<option value="5" selected="selected">
				5
				<!--{_t key=table_row}-->
			</option>
		<!--{else}-->
			<option value="5">
				5
				<!--{_t key=table_row}-->
			</option>				
		<!--{/if}-->		
		
		<!--{section name=r loop=100 start=10 step=10}-->
			<!--{if $smarty.section.r.index==$config->pageSize}-->
				<option value="<!--{$smarty.section.r.index}-->" selected="selected">
					<!--{$smarty.section.r.index}-->
					<!--{_t key=table_row}-->
				</option>
			<!--{else}-->
				<option value="<!--{$smarty.section.r.index}-->">
					<!--{$smarty.section.r.index}-->
					<!--{_t key=table_row}-->
				</option>
			<!--{/if}-->
		<!--{/section}-->		
	</select>
	/
	<!--{_t key=table_all}-->
	<!--{$dataCount}-->
	<!--{_t key=table_row}-->
</td>

</tr></table>
