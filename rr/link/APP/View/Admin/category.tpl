<!--{foreach name=categoryLoop from=$config->categoryList item=category key=key}-->		
	<!--{if $smarty.foreach.categoryLoop.first}-->
		<!--{_t key=table_category}-->
		<select  tabindex="1" onchange="fnChangeCategory('<!--{$config->name}-->',this.value);">				
	<!--{/if}-->
	
	<!--{if $key eq $config->category}-->
		<option value='<!--{$key}-->' selected="selected">
			<!--{$category}-->
		</option>
	<!--{else}-->
		<option value='<!--{$key}-->'>
			<!--{$category}-->
		</option>
	<!--{/if}-->
	
	<!--{if $smarty.foreach.categoryLoop.last}-->
		</select>
	<!--{/if}-->					
<!--{/foreach}-->	
