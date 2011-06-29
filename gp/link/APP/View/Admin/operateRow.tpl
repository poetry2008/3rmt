<!--{section name=okey loop=$d.operate}-->
<!--{if $smarty.section.okey.first}-->
<td>
<!--{/if}-->

<!--{assign var=o value=$d.operate[okey]}-->

<!--{if $o.action}-->
<a href="<!--{url controller=$o.controller action=$o.action id=$d.$idField}-->">
<!--{$o.title}-->
</a>			
<!--{else}-->
<!--{$o.title}-->
<!--{/if}-->

<!--{if $smarty.section.okey.last}-->
</td>
<!--{/if}-->
<!--{/section}-->
