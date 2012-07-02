<?php /* Smarty version 2.6.18, created on 2012-05-30 15:09:49
         compiled from Site/sitesearchresult.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', '_t', 'Site/sitesearchresult.html', 8, false),array('function', 'url', 'Site/sitesearchresult.html', 11, false),array('modifier', 'date_format', 'Site/sitesearchresult.html', 56, false),)), $this); ?>
<?php $this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%9F^9FC^9FCC73BB%%sitesearchresult.html.inc'] = '935353fa114bfbcd76031791e928528d'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'Site/header.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="content_warp">
<div id="custom">
<div class="site_list">
<?php $this->assign('pager_data', $this->_tpl_vars['pager']->getPagerData()); ?>
<div class="site_page">
<?php if ($this->_tpl_vars['pager_data']['firstPage'] == $this->_tpl_vars['pager_data']['currentPage']): ?>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#0}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'prevPage'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#0}'; endif;?>

<?php else: ?>
<?php if ($this->_tpl_vars['pager_data']['prevPage'] == 1): ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#1}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#1}'; endif;?>
" >
<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#2}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch','page' => $this->_tpl_vars['pager_data']['prevPage']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#2}'; endif;?>
" >
<?php endif; ?>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#3}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'prevPage'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#3}'; endif;?>

</a>
<?php endif; ?>
|
<?php $this->assign('pages', $this->_tpl_vars['pager']->getNavbarIndexs($this->_tpl_vars['pager_data']['current_page'])); ?>
<?php unset($this->_sections['page']);
$this->_sections['page']['name'] = 'page';
$this->_sections['page']['loop'] = is_array($_loop=$this->_tpl_vars['pages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['page']['show'] = true;
$this->_sections['page']['max'] = $this->_sections['page']['loop'];
$this->_sections['page']['step'] = 1;
$this->_sections['page']['start'] = $this->_sections['page']['step'] > 0 ? 0 : $this->_sections['page']['loop']-1;
if ($this->_sections['page']['show']) {
    $this->_sections['page']['total'] = $this->_sections['page']['loop'];
    if ($this->_sections['page']['total'] == 0)
        $this->_sections['page']['show'] = false;
} else
    $this->_sections['page']['total'] = 0;
if ($this->_sections['page']['show']):

            for ($this->_sections['page']['index'] = $this->_sections['page']['start'], $this->_sections['page']['iteration'] = 1;
                 $this->_sections['page']['iteration'] <= $this->_sections['page']['total'];
                 $this->_sections['page']['index'] += $this->_sections['page']['step'], $this->_sections['page']['iteration']++):
$this->_sections['page']['rownum'] = $this->_sections['page']['iteration'];
$this->_sections['page']['index_prev'] = $this->_sections['page']['index'] - $this->_sections['page']['step'];
$this->_sections['page']['index_next'] = $this->_sections['page']['index'] + $this->_sections['page']['step'];
$this->_sections['page']['first']      = ($this->_sections['page']['iteration'] == 1);
$this->_sections['page']['last']       = ($this->_sections['page']['iteration'] == $this->_sections['page']['total']);
?>
<?php if ($this->_tpl_vars['pager_data']['currentPage'] == $this->_tpl_vars['pages'][$this->_sections['page']['index']]['index']): ?>
<b> <?php echo $this->_tpl_vars['pages'][$this->_sections['page']['index']]['index']; ?>
</b>
<?php else: ?>
<?php if ($this->_tpl_vars['pages'][$this->_sections['page']['index']]['index'] == 1): ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#4}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#4}'; endif;?>
" >
<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#5}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch','page' => $this->_tpl_vars['pages'][$this->_sections['page']['index']]['number']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#5}'; endif;?>
" >
<?php endif; ?>
<?php echo $this->_tpl_vars['pages'][$this->_sections['page']['index']]['index']; ?>

</a>
<?php endif; ?>
|
<?php endfor; endif; ?>
<?php if ($this->_tpl_vars['pager_data']['lastPage'] == $this->_tpl_vars['pager_data']['currentPage']): ?>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#6}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'nextPage'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#6}'; endif;?>

<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#7}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch','page' => $this->_tpl_vars['pager_data']['nextPage']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#7}'; endif;?>
" >
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#8}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'nextPage'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#8}'; endif;?>

</a>
<?php endif; ?>
</div> <!--end of .site_page-->
<?php $this->assign('sites', $this->_tpl_vars['pager']->findAll()); ?>
<?php unset($this->_sections['list']);
$this->_sections['list']['name'] = 'list';
$this->_sections['list']['loop'] = is_array($_loop=$this->_tpl_vars['sites']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['list']['show'] = true;
$this->_sections['list']['max'] = $this->_sections['list']['loop'];
$this->_sections['list']['step'] = 1;
$this->_sections['list']['start'] = $this->_sections['list']['step'] > 0 ? 0 : $this->_sections['list']['loop']-1;
if ($this->_sections['list']['show']) {
    $this->_sections['list']['total'] = $this->_sections['list']['loop'];
    if ($this->_sections['list']['total'] == 0)
        $this->_sections['list']['show'] = false;
} else
    $this->_sections['list']['total'] = 0;
if ($this->_sections['list']['show']):

            for ($this->_sections['list']['index'] = $this->_sections['list']['start'], $this->_sections['list']['iteration'] = 1;
                 $this->_sections['list']['iteration'] <= $this->_sections['list']['total'];
                 $this->_sections['list']['index'] += $this->_sections['list']['step'], $this->_sections['list']['iteration']++):
$this->_sections['list']['rownum'] = $this->_sections['list']['iteration'];
$this->_sections['list']['index_prev'] = $this->_sections['list']['index'] - $this->_sections['list']['step'];
$this->_sections['list']['index_next'] = $this->_sections['list']['index'] + $this->_sections['list']['step'];
$this->_sections['list']['first']      = ($this->_sections['list']['iteration'] == 1);
$this->_sections['list']['last']       = ($this->_sections['list']['iteration'] == $this->_sections['list']['total']);
?>
<div class="site_item">
<img class="site_item_img01" width="55" height="15" border="0" alt="<?php echo $this->_tpl_vars['sites'][$this->_sections['list']['index']]['name']; ?>
" src="http://cabbage-search.jp/pr/?url=<?php echo $this->_tpl_vars['sites'][$this->_sections['list']['index']]['url']; ?>
">
<a href="<?php echo $this->_tpl_vars['sites'][$this->_sections['list']['index']]['url']; ?>
" target="_blank">
<?php echo $this->_tpl_vars['sites'][$this->_sections['list']['index']]['name']; ?>

</a>
<?php if ($this->_tpl_vars['sites'][$this->_sections['list']['index']]['is_recommend'] == '1'): ?>
<img src="images/isrec.gif" alt='recommend '/>
<?php endif; ?>
<?php if ($this->_tpl_vars['sites'][$this->_sections['list']['index']]['is_king'] == '1'): ?>
<img src="images/rec.gif" alt='king'/>
<?php endif; ?>
<br />
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#9}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'update_date'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#9}'; endif;?>
:<?php echo ((is_array($_tmp=$this->_tpl_vars['sites'][$this->_sections['list']['index']]['updated'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%Y-%m-%d %H:%M:%S") : smarty_modifier_date_format($_tmp, "%Y-%m-%d %H:%M:%S")); ?>

<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#10}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'editsite','id' => $this->_tpl_vars['sites'][$this->_sections['list']['index']]['id']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#10}'; endif;?>
" >
[<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#11}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'edit_or_delete'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#11}'; endif;?>
]
</a>
&nbsp;
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#12}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'mailadmin','id' => $this->_tpl_vars['sites'][$this->_sections['list']['index']]['id']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#12}'; endif;?>
" >
[<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#13}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'mail_to_admin'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#13}'; endif;?>
]
</a>
<br />
<?php echo $this->_tpl_vars['sites'][$this->_sections['list']['index']]['comment']; ?>

</div> 
<?php endfor; endif; ?>
<div class="site_page">
<?php if ($this->_tpl_vars['pager_data']['firstPage'] == $this->_tpl_vars['pager_data']['currentPage']): ?>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#14}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'prevPage'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#14}'; endif;?>

<?php else: ?>
<?php if ($this->_tpl_vars['pager_data']['prevPage'] == 1): ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#15}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#15}'; endif;?>
" >
<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#16}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch','page' => $this->_tpl_vars['pager_data']['prevPage']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#16}'; endif;?>
" >
<?php endif; ?>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#17}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'prevPage'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#17}'; endif;?>

</a>
<?php endif; ?>
|
<?php $this->assign('pages', $this->_tpl_vars['pager']->getNavbarIndexs($this->_tpl_vars['pager_data']['current_page'])); ?>
<?php unset($this->_sections['page']);
$this->_sections['page']['name'] = 'page';
$this->_sections['page']['loop'] = is_array($_loop=$this->_tpl_vars['pages']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['page']['show'] = true;
$this->_sections['page']['max'] = $this->_sections['page']['loop'];
$this->_sections['page']['step'] = 1;
$this->_sections['page']['start'] = $this->_sections['page']['step'] > 0 ? 0 : $this->_sections['page']['loop']-1;
if ($this->_sections['page']['show']) {
    $this->_sections['page']['total'] = $this->_sections['page']['loop'];
    if ($this->_sections['page']['total'] == 0)
        $this->_sections['page']['show'] = false;
} else
    $this->_sections['page']['total'] = 0;
if ($this->_sections['page']['show']):

            for ($this->_sections['page']['index'] = $this->_sections['page']['start'], $this->_sections['page']['iteration'] = 1;
                 $this->_sections['page']['iteration'] <= $this->_sections['page']['total'];
                 $this->_sections['page']['index'] += $this->_sections['page']['step'], $this->_sections['page']['iteration']++):
$this->_sections['page']['rownum'] = $this->_sections['page']['iteration'];
$this->_sections['page']['index_prev'] = $this->_sections['page']['index'] - $this->_sections['page']['step'];
$this->_sections['page']['index_next'] = $this->_sections['page']['index'] + $this->_sections['page']['step'];
$this->_sections['page']['first']      = ($this->_sections['page']['iteration'] == 1);
$this->_sections['page']['last']       = ($this->_sections['page']['iteration'] == $this->_sections['page']['total']);
?>
<?php if ($this->_tpl_vars['pager_data']['currentPage'] == $this->_tpl_vars['pages'][$this->_sections['page']['index']]['index']): ?>
<b> <?php echo $this->_tpl_vars['pages'][$this->_sections['page']['index']]['index']; ?>
</b>
<?php else: ?>
<?php if ($this->_tpl_vars['pages'][$this->_sections['page']['index']]['index'] == 1): ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#18}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#18}'; endif;?>
" >
<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#19}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch','page' => $this->_tpl_vars['pages'][$this->_sections['page']['index']]['number']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#19}'; endif;?>
" >
<?php endif; ?>
<?php echo $this->_tpl_vars['pages'][$this->_sections['page']['index']]['index']; ?>

</a>
<?php endif; ?>
|
<?php endfor; endif; ?>

<?php if ($this->_tpl_vars['pager_data']['lastPage'] == $this->_tpl_vars['pager_data']['currentPage']): ?>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#20}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'nextPage'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#20}'; endif;?>

<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#21}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch','page' => $this->_tpl_vars['pager_data']['nextPage']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#21}'; endif;?>
" >
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:935353fa114bfbcd76031791e928528d#22}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'nextPage'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:935353fa114bfbcd76031791e928528d#22}'; endif;?>

</a>
<?php endif; ?>
</div> 
</div> 
</div> 
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Site/footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>