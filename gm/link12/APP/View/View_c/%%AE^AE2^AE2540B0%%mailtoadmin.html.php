<?php /* Smarty version 2.6.18, created on 2012-05-30 15:10:13
         compiled from Site/mailtoadmin.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'url', 'Site/mailtoadmin.html', 5, false),array('function', '_t', 'Site/mailtoadmin.html', 10, false),)), $this); ?>
<?php $this->_cache_serials['/home/hm1002/GM/gm/link/APP/View/View_c/%%AE^AE2^AE2540B0%%mailtoadmin.html.inc'] = '08dab4b5441709ff889d4a79e88750c6'; ?><?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => 'Site/header.html', 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<div id="content">
<div id="custom_mail">
<div class="mail_form">
<form action="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#0}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'mailadminsubmit'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#0}'; endif;?>
"
name="mailtoadmin" method="post"  onsubmit="return checkForm()">
<input type="hidden" value="<?php echo $this->_tpl_vars['id']; ?>
" name="id" />
<table>
<tr><td>
[<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#1}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'event_type'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#1}'; endif;?>
]
</td></tr>
<tr><td>
<input type="checkbox" name="no_link" value="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#2}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'no_link'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#2}'; endif;?>
" />
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#3}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'no_link'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#3}'; endif;?>

</td></tr>
<tr><td>
<input type="checkbox" name="move" value="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#4}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'type_move'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#4}'; endif;?>
" />
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#5}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'type_move'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#5}'; endif;?>

</td></tr>
<tr><td>
<input type="checkbox" name="bana_no_link" value="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#6}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'bana_no_link'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#6}'; endif;?>
" />
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#7}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'bana_no_link'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#7}'; endif;?>

</td></tr>
<tr><td>
<input type="checkbox" name="ill" value="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#8}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'type_ill'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#8}'; endif;?>
" />
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#9}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'type_ill'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#9}'; endif;?>

</td></tr>
<tr><td>
<input type="checkbox" name="other" value="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#10}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'type_other'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#10}'; endif;?>
" />
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#11}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'type_other'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#11}'; endif;?>

</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#12}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'comment_title'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#12}'; endif;?>

</td></tr>
<tr><td>
<textarea name="com" id="com" rows='4' cols='20'></textarea>
</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#13}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'type_fname'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#13}'; endif;?>

</td></tr>
<tr><td>
<input type="text" name="fname" />
</td></tr>
<tr><td>
<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#14}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'type_femail'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#14}'; endif;?>

</td></tr>
<tr><td>
<input type="text" name="femail" id="to_admin_femail"/>
</td></tr>
<tr><td>
<input class="button02" type="submit" value="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#15}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'send_email'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#15}'; endif;?>
" />
</td></tr>
</table>
</form>
<script type="text/javascript">
function checkForm()
{ 
  var error = false;
  var error_message = '';
  //com
  var com = document.getElementById('com');
  if (com.value == '')
  {
    error = error || true;
    error_message += '\n<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#16}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'com_error'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#16}'; endif;?>
';
  }

  //femail
  var femail = document.getElementById('to_admin_femail');
  var email_reg = /[\w\.-]+(\+[\w-]*)?@([\w-]+\.)+[\w-]+/;
  var email_patt = new RegExp(email_reg);

  if (femail.value != '')
  {
    if (femail.value.length>40)
    {
      error = error || true;
      error_message += '\n<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#17}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'femail_error2'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#17}'; endif;?>
';
    }
    else if (!email_patt.exec(femail.value))
    {
      error = error || true;
      error_message += '\n<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#18}'; endif;echo $this->_plugins['function']['_t'][0][0]->_pi_func_t(array('key' => 'femail_error3'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#18}'; endif;?>
';
    }
  }
  if(!error){
    return true;
  }else{
    alert(error_message);
    return false;
  }
}
</script>
</div> <!-- end of .mail_form -->
<a class="send_mail" href="<?php if ($this->caching && !$this->_cache_including): echo '{nocache:08dab4b5441709ff889d4a79e88750c6#19}'; endif;echo $this->_plugins['function']['url'][0][0]->_pi_func_url(array('controller' => 'site','action' => 'sitelist'), $this);if ($this->caching && !$this->_cache_including): echo '{/nocache:08dab4b5441709ff889d4a79e88750c6#19}'; endif;?>
" >
</a>
</div>
</div>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "Site/footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>