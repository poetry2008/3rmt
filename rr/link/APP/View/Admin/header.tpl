<tr class="tr1">

<!--{if $config->multi}-->
<td>
<input name="checkAll" type="checkbox" onclick="fnCheckAll('<!--{$config->name}-->',this.checked)" onkeypress="fnCheckAll('<!--{$config->name}-->',this.checked)"  tabindex="5" accesskey="c" />

</td>
<!--{/if}-->

<!--{if $config->no}-->
<td>No.</td>
<!--{/if}-->


<!--{foreach name=fieldLoop from=$config->fields key=key item=field}-->
<!--{if $config->sortName eq $key}-->
<!--{if $config->sortOrder eq 'asc'}-->
<td onclick='fnTableSort("<!--{$config->name}-->","<!--{$key}-->","desc")' onkeypress='fnTableSort("<!--{$config->name}-->","<!--{$key}-->","desc")'>
<!--{$field}-->
<img src='<!--{$ascImgPath}-->' alt='<!--{_t key=table_asc}-->' height="11" width="11" />
</td>
<!--{else}-->
<td onclick='fnTableSort("<!--{$config->name}-->","<!--{$key}-->","asc")' onkeypress='fnTableSort("<!--{$config->name}-->","<!--{$key}-->","asc")'>
<!--{$field}-->
<img src='<!--{$descImgPath}-->' alt='<!--{_t key=table_desc}-->' height="11" width="11" />
</td>
<!--{/if}-->
<!--{else}-->
<td onclick='fnTableSort("<!--{$config->name}-->","<!--{$key}-->","asc")' onkeypress='fnTableSort("<!--{$config->name}-->","<!--{$key}-->","asc")'>
<!--{$field}-->
</td>
<!--{/if}-->
<!--{/foreach}-->

<td>
<!--{_t key="seoplink_site_linkcheck"}-->
</td>
<td>
<!--{_t key="seoplink_show_state"}-->
</td>
<td>
<!--{_t key="seoplink_is_king"}-->
</td>

<!--{if $config->show}-->
<td>
<!--{_t key=table_show}-->
</td>
<!--{/if}-->
<!--{if $config->edit}-->
<td>
<!--{_t key=table_edit}-->
</td>
<!--{/if}-->
<!--{if $config->del}-->
<td>
<!--{_t key=table_del}-->
</td>
<!--{/if}-->

<!--{if $data[0].operate}-->
<td>
<!--{_t key=table_operate}-->
</td>
<!--{/if}-->	

</tr>
