<?php /* Smarty version 2.6.18, created on 2012-05-30 18:14:04
         compiled from Site/indexallclass.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'Site/indexallclass.html', 7, false),)), $this); ?>
<?php $this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%F0^F0D^F0DF6F57%%indexallclass.html.inc'] = 'c100e040aa9055d7ac376adc7b37d52e'; ?><?php unset($this->_sections['loopClass']);
$this->_sections['loopClass']['name'] = 'loopClass';
$this->_sections['loopClass']['loop'] = is_array($_loop=$this->_tpl_vars['class']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
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
<table class="web_3">
<tr>
<?php endif; ?>
<td>
<span class="web_title"><a href='<?php if ($this->caching && !$this->_cache_including): echo '{nocache:c100e040aa9055d7ac376adc7b37d52e#0}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'class','id' => $this->_tpl_vars['class'][$this->_sections['loopClass']['index']]['class_id']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:c100e040aa9055d7ac376adc7b37d52e#0}'; endif;?>
'>
<?php echo $this->_tpl_vars['class'][$this->_sections['loopClass']['index']]['name']; ?>

</a></span>
<br />
<?php echo $this->_tpl_vars['str'][$this->_sections['loopClass']['index']]['string']; ?>

</td>
<?php $this->assign('dex', $this->_sections['loopClass']['index']+1); ?>
<?php if ($this->_tpl_vars['dex'] % 3 == 0): ?>
</tr><tr>
<?php endif; ?>
<?php if ($this->_sections['loopClass']['last']): ?>
<td></td><td></td><td></td>
</tr>
</table>
<?php endif; ?>

<?php endfor; endif; ?>

