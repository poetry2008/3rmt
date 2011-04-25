<!--{if $config->return || $config->add}-->
<table summary="1">
	<tr>
<!--{/if}-->
	<!--{if $config->return}-->	
		<td>
			<a href='<!--{$config->return}-->'>
				<!--{_t key=table_return}-->
			</a>
		</td>
	<!--{/if}-->
	
	<!--{if $config->add}-->	
		<td>
			<a href='<!--{url controller=$config->controller action=$config->action|cat:"Add"  table=$config->name}-->'>
				<!--{_t key=table_add}-->
			</a>
		</td>
	<!--{/if}-->

<!--{if $config->return || $config->add}-->
	</tr>
</table>
<!--{/if}-->
