<?php /* Smarty version 2.6.18, created on 2012-05-30 15:09:57
         compiled from Admin/pager.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'Admin/pager.html', 11, false),array('function', '_t', 'Admin/pager.html', 33, false),)), $this); ?>
<?php $this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%7D^7D7^7D7E27B4%%pager.html.inc'] = 'f907929a204d2e16e416d7c5907beae7'; ?><div id="pager">
<?php if ($this->_tpl_vars['pagestr']): ?>
<?php echo $this->_tpl_vars['pagestr']; ?>

<?php else: ?>
9 - 16 
<?php endif; ?>
番目を表示 (<?php echo $this->_tpl_vars['pager']['count']; ?>
 ある新着物件のうち)
ページ:
<?php if ($this->_tpl_vars['pagenum'] == $this->_tpl_vars['pager']['firstPageNumber']): ?>
<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#0}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => $this->_tpl_vars['controller'],'action' => $this->_tpl_vars['action'],'parent_id' => $this->_tpl_vars['parent']['class_id'],'id' => $this->_tpl_vars['class']['class_id'],'page' => $this->_tpl_vars['pager']['prevPageNumber']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#0}'; endif;?>
" title='前のページ'>
前のページ 
</a> 
<?php endif; ?>
<?php unset($this->_sections['page']);
$this->_sections['page']['name'] = 'page';
$this->_sections['page']['loop'] = is_array($_loop=$this->_tpl_vars['Navbar']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<?php if ($this->_tpl_vars['Navbar'][$this->_sections['page']['index']]['index'] == $this->_tpl_vars['pager']['currentPage']): ?>
<strong>[<?php echo $this->_tpl_vars['Navbar'][$this->_sections['page']['index']]['number']; ?>
]</strong>
<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#1}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => $this->_tpl_vars['controller'],'action' => $this->_tpl_vars['action'],'parent_id' => $this->_tpl_vars['parent']['class_id'],'id' => $this->_tpl_vars['class']['class_id'],'page' => $this->_tpl_vars['Navbar'][$this->_sections['page']['index']]['index']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#1}'; endif;?>
">
<?php echo $this->_tpl_vars['Navbar'][$this->_sections['page']['index']]['number']; ?>

</a>
<?php endif; ?>
<?php endfor; endif; ?>
<?php if ($this->_tpl_vars['pagenum'] == $this->_tpl_vars['pager']['lastPageNumber']): ?>
<?php else: ?>
<a href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#2}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => $this->_tpl_vars['controller'],'action' => $this->_tpl_vars['action'],'parent_id' => $this->_tpl_vars['parent']['class_id'],'id' => $this->_tpl_vars['class']['class_id'],'page' => $this->_tpl_vars['pager']['nextPageNumber']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#2}'; endif;?>
" title='次のページ'>
次のページ
</a>  
<?php endif; ?>
<?php if ($this->_tpl_vars['pagenum'] == null): ?>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#3}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'total'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#3}'; endif;?>
 <strong><?php echo $this->_tpl_vars['pager']['count']; ?>
</strong> <?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#4}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'one'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#4}'; endif;?>
，
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#5}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'total'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#5}'; endif;?>
 <strong><?php echo $this->_tpl_vars['pager']['pageCount']; ?>
</strong> <?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#6}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'page'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#6}'; endif;?>
，
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#7}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'per_page'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#7}'; endif;?>
 <strong><?php echo $this->_tpl_vars['pager']['pageSize']; ?>
</strong> <?php if ($this->caching && !$this->_cache_including): echo '{nocache:f907929a204d2e16e416d7c5907beae7#8}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'one'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f907929a204d2e16e416d7c5907beae7#8}'; endif;?>

<?php endif; ?>
</div>