<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo RESPONSE_CHARSET; ?>" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="content">
  <span><h3><?php echo h(_T('ui_c_title')); ?> [<a href="<?php echo $this->_url('create', isset($parent['class_id']) ? array('parent_id' => $parent['class_id']) : null); ?>"><?php echo h(_T('ui_c_create')); ?></a>]</h3></span>
  <div class="description"><?php echo t(_T('ui_c_description')); ?></div>
  <br />
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="data">
    <tr>
      <th nowrap="nowrap">ID</th>
      <th nowrap="nowrap"><?php echo h(_T('ui_c_class_name')); ?></th>
      <th nowrap="nowrap"><?php echo h(_T('ui_c_child_count')); ?></th>
      <th nowrap="nowrap"><?php echo h(_T('ui_g_operation')); ?></th>
      <th width="100%" nowrap="nowrap">&nbsp;</th>
    </tr>
	<?php if (count($subClasses)): ?>
    <?php for ($i = 0; $i < count($subClasses); $i++): $isChild = $subClasses[$i]['parent_id'] != 0; $classId = $subClasses[$i]['class_id']; ?>
    <tr class="<?php echo $i % 2 ? 'odd' : 'even'; ?>">
      <td nowrap="nowrap">&nbsp;<?php echo $classId; ?>&nbsp;</td>
      <td nowrap="nowrap">&nbsp;<?php echo h($subClasses[$i]['name']); ?>&nbsp;</td>
      <td align="right" nowrap="nowrap"><?php echo $subClasses[$i]['child_count']; ?>&nbsp;</td>
      <td nowrap="nowrap"><a href="<?php echo $this->_url('edit', array('class_id' => $classId)); ?>"><?php echo h(_T('ui_g_operation_edit')); ?></a> | <a href="<?php echo $this->_url('remove', array('class_id' => $classId)); ?>" onclick="return confirm('<?php echo h(_T('ui_c_remove_confirm')); ?>');"><?php echo h(_T('ui_g_operation_remove')); ?></a> | <a href="<?php echo $this->_url(null, array('parent_id' => $classId)); ?>"><?php echo h(_T('ui_c_operation_list_child')); ?></a> | <a href="<?php echo $this->_url('create', array('parent_id' => $classId)); ?>"><?php echo h(_T('ui_c_operation_create_child')); ?></a></td>
      <td nowrap="nowrap">&nbsp;</td>
    </tr>
    <?php endfor; ?>
	<?php else: // if (count($subClasses)) ?>
    <tr class="<?php echo $i % 2 ? 'odd' : 'even'; ?>">
      <td colspan="5" nowrap="nowrap"><?php echo h(_T('ui_c_no_subclasses')); ?></td>
    </tr>
	<?php endif; // if (count($subClasses)) ?>
  </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="32">
      <?php echo h(_T('ui_c_current_location')); ?>
      <a href="<?php echo $this->_url(null); ?>">
      <?php echo h(_T('ui_c_root_dir')); ?></a>
      <?php if ($parent): foreach ($path as $part): ?>
      -&gt; <a href="<?php echo $this->_url(null, array('parent_id' => $part['class_id'])); ?>">
      <?php echo h($part['name']); ?></a><?php endforeach; endif; ?>
      <?php echo h(sprintf(_T('ui_c_calc_child_count'), count($subClasses))); ?></td>
    </tr>
  </table>
</div>
</body>
</html>
