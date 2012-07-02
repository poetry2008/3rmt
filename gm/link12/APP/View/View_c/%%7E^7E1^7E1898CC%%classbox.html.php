<?php /* Smarty version 2.6.18, created on 2012-05-30 17:55:23
         compiled from Site/classbox.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'Site/classbox.html', 15, false),array('function', '_t', 'Site/classbox.html', 89, false),array('modifier', 'truncate', 'Site/classbox.html', 73, false),)), $this); ?>
<?php $this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%7E^7E1^7E1898CC%%classbox.html.inc'] = 'e9cc385d4c1ddaf43462531bfa4857d1'; ?><?php if ($this->_tpl_vars['classkind']['children']): ?>
<div id="web-box">
<span class="web-line"><b class="x1">&nbsp;</b><b class="x2">&nbsp;</b><b class="x3">&nbsp;</b><b class="x4">&nbsp;</b><b class="x5">&nbsp;</b><b class="x6">&nbsp;</b><b class="x7">&nbsp;</b></span>
<div class="web-tit">
<img src="images/backpic/tag.gif" alt="title" width="16" height="16" /> <strong><?php echo $this->_tpl_vars['classkind']['name']; ?>
</strong>
</div>
<div class="web-menu">
</div>
<?php unset($this->_sections['loopClass']);
$this->_sections['loopClass']['name'] = 'loopClass';
$this->_sections['loopClass']['loop'] = is_array($_loop=$this->_tpl_vars['classkind']['children']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['loopClass']['show'] = true;
$this->_sections['loopClass']['max'] = $this->_sections['loopClass']['loop'];
$this->_sections['loopClass']['step'] = 1;
$this->_sections['loopClass']['start'] = $this->_sections['loopClass']['step'] > 0 ? 0 : $this->_sections['loopClass']['loop']-1;
if ($this->_sections['loopClass']['show']) {
    $this->_sections['loopClass']['total'] = $this->_sections['loopClass']['loop'];
    if ($this->_sections['loopClass']['total'] == 0)
        $this->_sections['loopClass']['show'] = false;
} else
    $this->_sections['loopClass']['total'] = 0;
if ($this->_sections['loopClass']['show']):

            for ($this->_sections['loopClass']['index'] = $this->_sections['loopClass']['start'], $this->_sections['loopClass']['iteration'] = 1;
                 $this->_sections['loopClass']['iteration'] <= $this->_sections['loopClass']['total'];
                 $this->_sections['loopClass']['index'] += $this->_sections['loopClass']['step'], $this->_sections['loopClass']['iteration']++):
$this->_sections['loopClass']['rownum'] = $this->_sections['loopClass']['iteration'];
$this->_sections['loopClass']['index_prev'] = $this->_sections['loopClass']['index'] - $this->_sections['loopClass']['step'];
$this->_sections['loopClass']['index_next'] = $this->_sections['loopClass']['index'] + $this->_sections['loopClass']['step'];
$this->_sections['loopClass']['first']      = ($this->_sections['loopClass']['iteration'] == 1);
$this->_sections['loopClass']['last']       = ($this->_sections['loopClass']['iteration'] == $this->_sections['loopClass']['total']);
?>
<?php if ($this->_sections['loopClass']['first']): ?>
<table class="web">
<?php endif; ?>
  <tr class="t1">
    <td>
    <span class="web-ico">&nbsp;</span><a class = "web-ico-a" href='<?php if ($this->caching && !$this->_cache_including): echo '{nocache:e9cc385d4c1ddaf43462531bfa4857d1#0}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'class','id' => $this->_tpl_vars['classkind']['children'][$this->_sections['loopClass']['index']]['class_id']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:e9cc385d4c1ddaf43462531bfa4857d1#0}'; endif;?>
'><?php echo $this->_tpl_vars['classkind']['children'][$this->_sections['loopClass']['index']]['name']; ?>

    </a>
    </td>
  </tr>
<?php if ($this->_sections['loopClass']['last']): ?>
</table>
<?php endif; ?>
<?php endfor; endif; ?>
</div>
<?php endif; ?>
<?php if ($this->_tpl_vars['sites']): ?>
<div class="web-box">
<span class="web-line"><b class="x1">&nbsp;</b><b class="x2">&nbsp;</b><b class="x3">&nbsp;</b><b class="x4">&nbsp;</b><b class="x5">&nbsp;</b><b class="x6">&nbsp;</b><b class="x7">&nbsp;</b></span>
<div class="web-tit">
<img src="images/backpic/tag.gif" alt="title" width="16" height="16" /> <strong><?php echo $this->_tpl_vars['class']['name']; ?>
</strong>
</div>
<div class="web-menu">
</div>
</div>
<?php unset($this->_sections['loopSite']);
$this->_sections['loopSite']['name'] = 'loopSite';
$this->_sections['loopSite']['loop'] = is_array($_loop=$this->_tpl_vars['sites']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['loopSite']['show'] = true;
$this->_sections['loopSite']['max'] = $this->_sections['loopSite']['loop'];
$this->_sections['loopSite']['step'] = 1;
$this->_sections['loopSite']['start'] = $this->_sections['loopSite']['step'] > 0 ? 0 : $this->_sections['loopSite']['loop']-1;
if ($this->_sections['loopSite']['show']) {
    $this->_sections['loopSite']['total'] = $this->_sections['loopSite']['loop'];
    if ($this->_sections['loopSite']['total'] == 0)
        $this->_sections['loopSite']['show'] = false;
} else
    $this->_sections['loopSite']['total'] = 0;
if ($this->_sections['loopSite']['show']):

            for ($this->_sections['loopSite']['index'] = $this->_sections['loopSite']['start'], $this->_sections['loopSite']['iteration'] = 1;
                 $this->_sections['loopSite']['iteration'] <= $this->_sections['loopSite']['total'];
                 $this->_sections['loopSite']['index'] += $this->_sections['loopSite']['step'], $this->_sections['loopSite']['iteration']++):
$this->_sections['loopSite']['rownum'] = $this->_sections['loopSite']['iteration'];
$this->_sections['loopSite']['index_prev'] = $this->_sections['loopSite']['index'] - $this->_sections['loopSite']['step'];
$this->_sections['loopSite']['index_next'] = $this->_sections['loopSite']['index'] + $this->_sections['loopSite']['step'];
$this->_sections['loopSite']['first']      = ($this->_sections['loopSite']['iteration'] == 1);
$this->_sections['loopSite']['last']       = ($this->_sections['loopSite']['iteration'] == $this->_sections['loopSite']['total']);
?>
<?php if ($this->_sections['loopSite']['first']): ?>
<div id ='web_page'>
<table class="web_page_table">
<?php endif; ?>
<tr>
<td class="web_table_td1"><a target="_blank" href="<?php echo $this->_tpl_vars['sites'][$this->_sections['loopSite']['index']]['url']; ?>
">
<?php echo $this->_tpl_vars['sites'][$this->_sections['loopSite']['index']]['name']; ?>
</a></td>
<td class="web_table_td2"><?php echo $this->_tpl_vars['sites'][$this->_sections['loopSite']['index']]['comment']; ?>
</td>
</tr>
<?php if ($this->_sections['loopSite']['last']): ?>
</table>      
</div>
<?php endif; ?>
<?php endfor; endif; ?>
<div id='fornt_pager'>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Admin/pager.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>
<?php endif; ?>



<?php if ($this->_tpl_vars['class']['sites']): ?>
<div class="web-box">
<span class="web-line"><b class="x1">&nbsp;</b><b class="x2">&nbsp;</b><b class="x3">&nbsp;</b><b class="x4">&nbsp;</b><b class="x5">&nbsp;</b><b class="x6">&nbsp;</b><b class="x7">&nbsp;</b></span>
<div class="web-tit">
<img src="images/backpic/tag.gif" alt="title" width="16" height="16" /> <strong><?php echo $this->_tpl_vars['class']['name']; ?>
</strong>
</div>
<div class="web-menu">
</div>

<?php unset($this->_sections['loopSite']);
$this->_sections['loopSite']['name'] = 'loopSite';
$this->_sections['loopSite']['loop'] = is_array($_loop=$this->_tpl_vars['class']['sites']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['loopSite']['show'] = true;
$this->_sections['loopSite']['max'] = $this->_sections['loopSite']['loop'];
$this->_sections['loopSite']['step'] = 1;
$this->_sections['loopSite']['start'] = $this->_sections['loopSite']['step'] > 0 ? 0 : $this->_sections['loopSite']['loop']-1;
if ($this->_sections['loopSite']['show']) {
    $this->_sections['loopSite']['total'] = $this->_sections['loopSite']['loop'];
    if ($this->_sections['loopSite']['total'] == 0)
        $this->_sections['loopSite']['show'] = false;
} else
    $this->_sections['loopSite']['total'] = 0;
if ($this->_sections['loopSite']['show']):

            for ($this->_sections['loopSite']['index'] = $this->_sections['loopSite']['start'], $this->_sections['loopSite']['iteration'] = 1;
                 $this->_sections['loopSite']['iteration'] <= $this->_sections['loopSite']['total'];
                 $this->_sections['loopSite']['index'] += $this->_sections['loopSite']['step'], $this->_sections['loopSite']['iteration']++):
$this->_sections['loopSite']['rownum'] = $this->_sections['loopSite']['iteration'];
$this->_sections['loopSite']['index_prev'] = $this->_sections['loopSite']['index'] - $this->_sections['loopSite']['step'];
$this->_sections['loopSite']['index_next'] = $this->_sections['loopSite']['index'] + $this->_sections['loopSite']['step'];
$this->_sections['loopSite']['first']      = ($this->_sections['loopSite']['iteration'] == 1);
$this->_sections['loopSite']['last']       = ($this->_sections['loopSite']['iteration'] == $this->_sections['loopSite']['total']);
?>
<?php if ($this->_sections['loopSite']['first']): ?>
<table class="web">
<?php endif; ?>
	<tr class="t1">
		<td>
		<span class="web-ico">&nbsp;</span><a class = "web-ico-a" href='<?php echo $this->_tpl_vars['class']['sites'][$this->_sections['loopSite']['index']]['url']; ?>
' target='_blank'><?php echo $this->_tpl_vars['class']['sites'][$this->_sections['loopSite']['index']]['name']; ?>
</a>
		<?php if ($this->_tpl_vars['class']['sites'][$this->_sections['loopSite']['index']]['comment']): ?>
		<p><a href='<?php if ($this->caching && !$this->_cache_including): echo '{nocache:e9cc385d4c1ddaf43462531bfa4857d1#1}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'detail','id' => $this->_tpl_vars['class']['sites'][$this->_sections['loopSite']['index']]['id']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:e9cc385d4c1ddaf43462531bfa4857d1#1}'; endif;?>
'><?php echo ((is_array($_tmp=$this->_tpl_vars['class']['sites'][$this->_sections['loopSite']['index']]['comment'])) ? $this->_run_mod_handler('truncate', true, $_tmp, 36, "...") : smarty_modifier_truncate($_tmp, 36, "...")); ?>
</a></p>
		<?php endif; ?>
		</td>
	</tr>
<?php if ($this->_sections['loopSite']['last']): ?>
	<?php unset($this->_sections['loopClassAdd']);
$this->_sections['loopClassAdd']['name'] = 'loopClassAdd';
$this->_sections['loopClassAdd']['loop'] = is_array($_loop=$this->_tpl_vars['sitesAdd']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['loopClassAdd']['show'] = true;
$this->_sections['loopClassAdd']['max'] = $this->_sections['loopClassAdd']['loop'];
$this->_sections['loopClassAdd']['step'] = 1;
$this->_sections['loopClassAdd']['start'] = $this->_sections['loopClassAdd']['step'] > 0 ? 0 : $this->_sections['loopClassAdd']['loop']-1;
if ($this->_sections['loopClassAdd']['show']) {
    $this->_sections['loopClassAdd']['total'] = $this->_sections['loopClassAdd']['loop'];
    if ($this->_sections['loopClassAdd']['total'] == 0)
        $this->_sections['loopClassAdd']['show'] = false;
} else
    $this->_sections['loopClassAdd']['total'] = 0;
if ($this->_sections['loopClassAdd']['show']):

            for ($this->_sections['loopClassAdd']['index'] = $this->_sections['loopClassAdd']['start'], $this->_sections['loopClassAdd']['iteration'] = 1;
                 $this->_sections['loopClassAdd']['iteration'] <= $this->_sections['loopClassAdd']['total'];
                 $this->_sections['loopClassAdd']['index'] += $this->_sections['loopClassAdd']['step'], $this->_sections['loopClassAdd']['iteration']++):
$this->_sections['loopClassAdd']['rownum'] = $this->_sections['loopClassAdd']['iteration'];
$this->_sections['loopClassAdd']['index_prev'] = $this->_sections['loopClassAdd']['index'] - $this->_sections['loopClassAdd']['step'];
$this->_sections['loopClassAdd']['index_next'] = $this->_sections['loopClassAdd']['index'] + $this->_sections['loopClassAdd']['step'];
$this->_sections['loopClassAdd']['first']      = ($this->_sections['loopClassAdd']['iteration'] == 1);
$this->_sections['loopClassAdd']['last']       = ($this->_sections['loopClassAdd']['iteration'] == $this->_sections['loopClassAdd']['total']);
?>
	<td></td>
	<?php endfor; endif; ?>
</tr>
</table>
<?php endif; ?>
<?php endfor; endif; ?>
</div>
<?php endif; ?>
<?php if (! $this->_tpl_vars['classkind']['sites'] && ! $this->_tpl_vars['classkind']['children']): ?>
<div id="no_datas_msg">
「<?php echo $this->_tpl_vars['classkind']['name']; ?>
」<?php if ($this->caching && !$this->_cache_including): echo '{nocache:e9cc385d4c1ddaf43462531bfa4857d1#2}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'no_datas_msg'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:e9cc385d4c1ddaf43462531bfa4857d1#2}'; endif;?>

</div>
<?php endif; ?>