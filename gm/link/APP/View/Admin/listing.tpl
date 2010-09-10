<script type="text/javascript">
gConfirm='<!--{_t key=table_del_confirm}-->';	
//gUrl='<!--{url controller=$config->controller action=$config->action}-->';
gUrl = location.href;
</script>
<script type="text/javascript" src="/Script/listing.js"></script>
<script type="text/javascript" src="/Script/manager.js"></script>
<!--{if $config->title}-->
<div id="title"><h3><!--{$config->title}--></h3></div>
<!--{/if}-->
<div id="menu-top">			
<!--{_t key='ui_c_current_location'}--><a href="<!--{url controller='class' action='index'}-->"><!--{_t key='ui_c_root_dir'}--></a>
<!--{if $parent}-->
<!--{section name=loopPath loop=$path}-->
-&gt; <a href="<!--{url controller='class' action='index' parent_id=$path[loopPath].class_id}-->"><!--{$path[loopPath].name}--></a>
<!--{/section}-->      		
<!--{/if}-->
<!--{capture name= ui_format}-->
<!--{_t key='ui_c_calc_child_count'}-->
<!--{/capture}-->
<!--{$subClassescount|string_format:$smarty.capture.ui_format}-->
</div>
<!--{assign var=idField value=$config->id}-->				

<!--{include file=return_add.tpl}-->

<!--{assign var=ascImgPath value='/images/backpic/asc.gif'}-->
<!--{assign	var=descImgPath value='/Images/backpic/desc.gif'}-->


<!--{include file=category.tpl}-->	

<!--{foreach name=dataList from=$data key=key item=d}-->	
<!--{if $smarty.foreach.dataList.first}-->

<!--{if $config->multi or $config->operate}-->
<!--{assign var=url value=$config->action|cat:'Do'}-->
<form id="form_<!--{$config->name}-->" name="form_<!--{$config->name}-->" action="<!--{url controller=$config->controller action=$url table=$config->name}-->" method="post">
<!--{/if}-->

<!--{include file=paging.tpl}-->

<table id="table-row" summary="3">

<!--{include file=header.tpl}-->
<!--{/if}-->

<tr class="tr2">

<!--{if $config->multi}-->
<td class="td-center">
<input type="checkbox" accesskey="m" tabindex="6" name="id[]" id="<!--{$config->name}-->_<!--{$d.$idField}-->" value="<!--{$d.$idField}-->" />
</td>
<!--{/if}-->

<!--{if $config->no}-->
<td class="td-center">
<!--{$smarty.foreach.dataList.iteration}-->
</td>
<!--{/if}-->

<!--{foreach name=fieldList from=$config->fields key=fkey item=field}-->
<td class="td-center">
<!--{$d[$fkey]}-->
</td>
<!--{/foreach}-->
<td>
<!--{if $d.is_custom eq '1'}-->
<!--{if $d.state eq '1'}-->
<img src="/images/img/success.png" alt="search">
<!--{else}-->
<img src="/images/img/fail.png" alt="search">
<!--{/if}-->
<!--{/if}-->
</td>
<td align="center">
<!--{if $d.is_custom eq '1'}-->
<!--{if $d.show_state eq '1'}-->
<img src="/images/icon_status_green.gif">
<a href="<!--{url controller=seoplink
action=linkcheckupdate id=$d.id show=0 
}-->" >
<img src="/images/icon_status_red_light.gif">
</a>
<!--{else}-->
<a href="<!--{url controller=seoplink
action=linkcheckupdate id=$d.id show=1 
}-->" >
<img src="/images/icon_status_green_light.gif">
</a>
<img src="/images/icon_status_red.gif">
<!--{/if}-->
<!--{/if}-->
</td>

<!--{include file=view_edit_del.tpl}-->
<!--{include file=operateRow.tpl}-->
</tr>

<!--{if $smarty.foreach.dataList.last}-->
<tr>
<td colspan="7" class="td-submit"><!--{include file=operateAll.tpl}--></td>
</tr>			
</table>
<!--{if $config->multi or $config->operate}-->
</form>
<!--{/if}-->
<!--{/if}-->
<!--{/foreach}-->
