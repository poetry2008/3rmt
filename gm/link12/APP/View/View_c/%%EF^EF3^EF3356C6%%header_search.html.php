<?php /* Smarty version 2.6.18, created on 2012-05-30 18:14:07
         compiled from Site/header_search.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', '_t', 'Site/header_search.html', 5, false),array('function', 'url', 'Site/header_search.html', 35, false),)), $this); ?>
<?php $this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%EF^EF3^EF3356C6%%header_search.html.inc'] = 'af5b54b12e1b7e8df46b39ea4bd0c400'; ?><div class="top">
<?php echo $this->_tpl_vars['bread']; ?>

</div>
<?php if ($this->_tpl_vars['top_info']): ?>
<h1 class="link_h1"><?php if ($this->caching && !$this->_cache_including): echo '{nocache:af5b54b12e1b7e8df46b39ea4bd0c400#0}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => $this->_tpl_vars['top_info']['h1']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:af5b54b12e1b7e8df46b39ea4bd0c400#0}'; endif;?>
</h1>
<div class="middle">
<div class="text_out">
<?php if ($this->_tpl_vars['top_info']['time']): ?>
<?php echo $this->_tpl_vars['top_info']['time']; ?>

<?php if ($this->caching && !$this->_cache_including): echo '{nocache:af5b54b12e1b7e8df46b39ea4bd0c400#1}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => $this->_tpl_vars['top_info']['text']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:af5b54b12e1b7e8df46b39ea4bd0c400#1}'; endif;?>

<?php else: ?>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:af5b54b12e1b7e8df46b39ea4bd0c400#2}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => $this->_tpl_vars['top_info']['text']), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:af5b54b12e1b7e8df46b39ea4bd0c400#2}'; endif;?>

<?php endif; ?>
</div>
</div>
<?php else: ?>
<h1 class="link_h1"><?php if ($this->caching && !$this->_cache_including): echo '{nocache:af5b54b12e1b7e8df46b39ea4bd0c400#3}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'text_out_h1'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:af5b54b12e1b7e8df46b39ea4bd0c400#3}'; endif;?>
</h1>
<div class="middle">
<div class="text_out">
<p>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:af5b54b12e1b7e8df46b39ea4bd0c400#4}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'text_out_p1'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:af5b54b12e1b7e8df46b39ea4bd0c400#4}'; endif;?>

</p>
<p>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:af5b54b12e1b7e8df46b39ea4bd0c400#5}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'text_out_p2'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:af5b54b12e1b7e8df46b39ea4bd0c400#5}'; endif;?>

</p>
<p>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:af5b54b12e1b7e8df46b39ea4bd0c400#6}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'text_out_p3'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:af5b54b12e1b7e8df46b39ea4bd0c400#6}'; endif;?>

</p>
</div>
</div>
<?php endif; ?>
<div class="bottom"></div>

<div class = 'search_header'>
<form action="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:af5b54b12e1b7e8df46b39ea4bd0c400#7}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitesearch'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:af5b54b12e1b7e8df46b39ea4bd0c400#7}'; endif;?>
" method="post" 
onsubmit="return valsearch()" name="form1" id="form1">
<table><tr><td align="center">
▼ホームページ検索 
<input type="text" id="search_txt" class="sh-txt" name='word' />

  <select name="method">
    <option value="and" selected="selected">AND</option>
    <option value="or">OR</option>
  </select>
   <input type="submit" value=" 検 索 " /> 
</td></tr>
</table>
</form>
</div>
</div>
