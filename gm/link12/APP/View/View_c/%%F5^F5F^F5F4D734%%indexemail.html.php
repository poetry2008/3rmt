<?php /* Smarty version 2.6.18, created on 2012-05-30 17:55:23
         compiled from Site/indexemail.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'Site/indexemail.html', 14, false),)), $this); ?>
<?php $this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%F5^F5F^F5F4D734%%indexemail.html.inc'] = '5fd1ca37e6c60c3e98f2d21a6463d1eb'; ?><div id="email">
	<ul>
	<?php unset($this->_sections['loopFrequentSpecial2']);
$this->_sections['loopFrequentSpecial2']['name'] = 'loopFrequentSpecial2';
$this->_sections['loopFrequentSpecial2']['loop'] = is_array($_loop=$this->_tpl_vars['frequentSpecial2']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['loopFrequentSpecial2']['show'] = true;
$this->_sections['loopFrequentSpecial2']['max'] = $this->_sections['loopFrequentSpecial2']['loop'];
$this->_sections['loopFrequentSpecial2']['step'] = 1;
$this->_sections['loopFrequentSpecial2']['start'] = $this->_sections['loopFrequentSpecial2']['step'] > 0 ? 0 : $this->_sections['loopFrequentSpecial2']['loop']-1;
if ($this->_sections['loopFrequentSpecial2']['show']) {
    $this->_sections['loopFrequentSpecial2']['total'] = $this->_sections['loopFrequentSpecial2']['loop'];
    if ($this->_sections['loopFrequentSpecial2']['total'] == 0)
        $this->_sections['loopFrequentSpecial2']['show'] = false;
} else
    $this->_sections['loopFrequentSpecial2']['total'] = 0;
if ($this->_sections['loopFrequentSpecial2']['show']):

            for ($this->_sections['loopFrequentSpecial2']['index'] = $this->_sections['loopFrequentSpecial2']['start'], $this->_sections['loopFrequentSpecial2']['iteration'] = 1;
                 $this->_sections['loopFrequentSpecial2']['iteration'] <= $this->_sections['loopFrequentSpecial2']['total'];
                 $this->_sections['loopFrequentSpecial2']['index'] += $this->_sections['loopFrequentSpecial2']['step'], $this->_sections['loopFrequentSpecial2']['iteration']++):
$this->_sections['loopFrequentSpecial2']['rownum'] = $this->_sections['loopFrequentSpecial2']['iteration'];
$this->_sections['loopFrequentSpecial2']['index_prev'] = $this->_sections['loopFrequentSpecial2']['index'] - $this->_sections['loopFrequentSpecial2']['step'];
$this->_sections['loopFrequentSpecial2']['index_next'] = $this->_sections['loopFrequentSpecial2']['index'] + $this->_sections['loopFrequentSpecial2']['step'];
$this->_sections['loopFrequentSpecial2']['first']      = ($this->_sections['loopFrequentSpecial2']['iteration'] == 1);
$this->_sections['loopFrequentSpecial2']['last']       = ($this->_sections['loopFrequentSpecial2']['iteration'] == $this->_sections['loopFrequentSpecial2']['total']);
?>
		<?php $this->assign('num', $this->_sections['loopFrequentSpecial2']['index']); ?>
		<?php $this->assign('sites', $this->_tpl_vars['frequentSpecial2'][$this->_sections['loopFrequentSpecial2']['index']]['sites']); ?>
		<?php $this->assign('show', $this->_tpl_vars['frequentSpecial2'][$this->_sections['loopFrequentSpecial2']['index']]['show']); ?>
		<?php if ($this->_tpl_vars['frequentSpecial2'][$this->_sections['loopFrequentSpecial2']['index']]['class']['name']): ?>
		<li class="m4"><strong><?php echo $this->_tpl_vars['frequentSpecial2'][$this->_sections['loopFrequentSpecial2']['index']]['class']['name']; ?>
:</strong></li>
		<li class="m5">
			<?php unset($this->_sections['loopFrequentSpecial2Site']);
$this->_sections['loopFrequentSpecial2Site']['name'] = 'loopFrequentSpecial2Site';
$this->_sections['loopFrequentSpecial2Site']['loop'] = is_array($_loop=$this->_tpl_vars['sites']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['loopFrequentSpecial2Site']['max'] = (int)$this->_tpl_vars['show'];
$this->_sections['loopFrequentSpecial2Site']['show'] = true;
if ($this->_sections['loopFrequentSpecial2Site']['max'] < 0)
    $this->_sections['loopFrequentSpecial2Site']['max'] = $this->_sections['loopFrequentSpecial2Site']['loop'];
$this->_sections['loopFrequentSpecial2Site']['step'] = 1;
$this->_sections['loopFrequentSpecial2Site']['start'] = $this->_sections['loopFrequentSpecial2Site']['step'] > 0 ? 0 : $this->_sections['loopFrequentSpecial2Site']['loop']-1;
if ($this->_sections['loopFrequentSpecial2Site']['show']) {
    $this->_sections['loopFrequentSpecial2Site']['total'] = min(ceil(($this->_sections['loopFrequentSpecial2Site']['step'] > 0 ? $this->_sections['loopFrequentSpecial2Site']['loop'] - $this->_sections['loopFrequentSpecial2Site']['start'] : $this->_sections['loopFrequentSpecial2Site']['start']+1)/abs($this->_sections['loopFrequentSpecial2Site']['step'])), $this->_sections['loopFrequentSpecial2Site']['max']);
    if ($this->_sections['loopFrequentSpecial2Site']['total'] == 0)
        $this->_sections['loopFrequentSpecial2Site']['show'] = false;
} else
    $this->_sections['loopFrequentSpecial2Site']['total'] = 0;
if ($this->_sections['loopFrequentSpecial2Site']['show']):

            for ($this->_sections['loopFrequentSpecial2Site']['index'] = $this->_sections['loopFrequentSpecial2Site']['start'], $this->_sections['loopFrequentSpecial2Site']['iteration'] = 1;
                 $this->_sections['loopFrequentSpecial2Site']['iteration'] <= $this->_sections['loopFrequentSpecial2Site']['total'];
                 $this->_sections['loopFrequentSpecial2Site']['index'] += $this->_sections['loopFrequentSpecial2Site']['step'], $this->_sections['loopFrequentSpecial2Site']['iteration']++):
$this->_sections['loopFrequentSpecial2Site']['rownum'] = $this->_sections['loopFrequentSpecial2Site']['iteration'];
$this->_sections['loopFrequentSpecial2Site']['index_prev'] = $this->_sections['loopFrequentSpecial2Site']['index'] - $this->_sections['loopFrequentSpecial2Site']['step'];
$this->_sections['loopFrequentSpecial2Site']['index_next'] = $this->_sections['loopFrequentSpecial2Site']['index'] + $this->_sections['loopFrequentSpecial2Site']['step'];
$this->_sections['loopFrequentSpecial2Site']['first']      = ($this->_sections['loopFrequentSpecial2Site']['iteration'] == 1);
$this->_sections['loopFrequentSpecial2Site']['last']       = ($this->_sections['loopFrequentSpecial2Site']['iteration'] == $this->_sections['loopFrequentSpecial2Site']['total']);
?>
			<a href='<?php echo $this->_tpl_vars['sites'][$this->_sections['loopFrequentSpecial2Site']['index']]['url']; ?>
' target='_blank'><?php echo $this->_tpl_vars['sites'][$this->_sections['loopFrequentSpecial2Site']['index']]['name']; ?>
</a>
			<?php endfor; endif; ?>
		</li>
		<li class="m6"><a href='<?php if ($this->caching && !$this->_cache_including): echo '{nocache:5fd1ca37e6c60c3e98f2d21a6463d1eb#0}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'class','id' => $this->_tpl_vars['frequentSpecial2'][$this->_sections['loopFrequentSpecial2']['index']]['class']['class_id']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:5fd1ca37e6c60c3e98f2d21a6463d1eb#0}'; endif;?>
'>一覧</a></li>
		<?php endif; ?>
	<?php endfor; endif; ?>
        <li>&nbsp;</li>
	</ul>
</div>