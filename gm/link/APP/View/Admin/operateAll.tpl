<!--{php}-->
	//dump($this->_tpl_vars['config']->operate);
<!--{/php}-->

<!--{if $config->operate|@count == 1}-->
	<input type="hidden" name="operate" value="<!--{$config->operate[0].action}-->" />
	<input type="submit" tabindex="7" accesskey="s" value="<!--{$config->operate[0].title}-->" />
<!--{else}-->
	<!--{foreach from=$config->operate item=item key=key name=operateLoop}-->
	<!--{if $smarty.foreach.operateLoop.first}-->
		<!--{_t key=table_select_operate}-->
		<select tabindex="2" id="operate" name="operate">
	<!--{/if}-->
		
		<option value="<!--{$item.action}-->" >
			<!--{$item.title}-->
		</option>
	
	<!--{if $smarty.foreach.operateLoop.last}-->
		</select>
		<input type="submit" accesskey="s" name="submit" id="submit" value='<!--{_t key=submit}-->' />
	<!--{/if}-->
	<!--{/foreach}-->
<!--{/if}-->
