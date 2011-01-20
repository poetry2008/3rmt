<!--{if $config->show}-->
<td>
<!--{assign var=url value='View'}-->
<a href="<!--{url controller=$config->controller action=$url id=$d.$idField}-->" title="<!--{$d.$idField}-->">
<!--{_t key=table_show}-->
</a>
</td>
<!--{/if}-->

<!--{if $config->edit}-->
<td>
<!--{assign var=url value='Edit'}-->
<a href="<!--{url controller=$config->controller action=$url id=$d.$idField}-->" title="<!--{$d.$idField}-->">
<!--{_t key=table_edit}-->
</a>
</td>
<!--{/if}-->

<!--{if $config->del}-->
<td>
<!--{assign var=url value='Del'}-->
<a href="<!--{url controller=$config->controller action=$url id=$d.$idField}-->" title="<!--{$d.$idField}-->">
<!--{_t key=table_del}-->
</a>	
</td>
<!--{/if}-->
