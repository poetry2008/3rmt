<?php /* Smarty version 2.6.18, created on 2012-05-30 14:59:50
         compiled from Site/siteedit.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', '_t', 'Site/siteedit.html', 5, false),array('function', 'url', 'Site/siteedit.html', 12, false),)), $this); ?>
<?php $this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%AA^AA0^AA0A171D%%siteedit.html.inc'] = 'f83a21819c16bc8f4d4013ba67f05211'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'Site/header.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="content">
<div id="custom" class="custom_siteedit">
<div class="site_update">
[<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#0}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_edit_title'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#0}'; endif;?>
]
<br />
<br />
<br />
<table> 
<tr>
<td valign="top">
<form action="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#1}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'siteupdate'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#1}'; endif;?>
" name="site_update"
method="post">
<table cellpadding="15">
<tr><td colspan="2">
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#2}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_update_title'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#2}'; endif;?>

</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#3}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_id'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#3}'; endif;?>
:
</td><td>
<input type="text" name="id" value="<?php echo $this->_tpl_vars['site']['id']; ?>
" />
</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#4}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_pass'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#4}'; endif;?>
:
</td><td>
<input type="password" name="pass" value="" />
</td></tr>
<tr><td colspan="2" align="right">
<br />
<input type="submit" value="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#5}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_update_submit'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#5}'; endif;?>
" />
</td></tr>
</table>
</form>
</td>
<td valign="top">
<table>
<tr><td>
[<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#6}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_info'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#6}'; endif;?>
]
</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#7}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_title'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#7}'; endif;?>
:
</td></tr>
<tr><td>
<?php echo $this->_tpl_vars['site']['name']; ?>

</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#8}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_url'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#8}'; endif;?>
:
</td></tr>
<tr><td>
<a href="<?php echo $this->_tpl_vars['site']['url']; ?>
" target="_blank" >
<?php echo $this->_tpl_vars['site']['url']; ?>

</a>
</td></tr>
</table>
</td>
</tr>
</table>
</div> 

<hr align="center" />
<div class="site_del">
<table> 
<tr>
<td valign="top">
<form action="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#9}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitedel'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#9}'; endif;?>
" name="site_del"
method="post">
<table cellpadding="15">
<tr><td colspan="2">
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#10}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_del_title'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#10}'; endif;?>

</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#11}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_id'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#11}'; endif;?>
:
</td><td>
<input type="text" name="id" value="<?php echo $this->_tpl_vars['site']['id']; ?>
" />
</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#12}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_pass'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#12}'; endif;?>
:
</td><td>
<input type="password" name="pass" value="" />
</td></tr>
<tr><td align="right" colspan="2">
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#13}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'check_del'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#13}'; endif;?>

<input type="checkbox" name="del_check" value="1" />
</td></tr>
<tr><td align="right" colspan="2">
<br />
<input type="submit" value="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:f83a21819c16bc8f4d4013ba67f05211#14}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'site_del_submit'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:f83a21819c16bc8f4d4013ba67f05211#14}'; endif;?>
" />
</td></tr>
</table>
</form>
</td>
</tr>
</table>
</div>

</div> 
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Site/footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>