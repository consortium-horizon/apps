<?php /* Smarty version 2.6.27, created on 2013-11-03 17:00:05
         compiled from public_error.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'public_error.html', 10, false),)), $this); ?>
<html>
<head>
</head>
<body>
<?php if ($this->_tpl_vars['hasErr']): ?>    <div style="border:1px #ff0000 solid;padding:4px">
    <?php $_from = $this->_tpl_vars['errInfoList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oErrorInfo']):
?>
    <?php if (! $this->_tpl_vars['oErrorInfo']->isErrInfo()): ?>
    <div><?php echo ((is_array($_tmp=$this->_tpl_vars['oErrorInfo']->errValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
    <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
</div>
<br>
<?php else: ?>      <?php if ($this->_tpl_vars['errInfoList']): ?>
    <div style="border:1px #00ff00 solid;padding:4px">
        <?php $_from = $this->_tpl_vars['errInfoList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oErrorInfo']):
?>
        <div><?php echo ((is_array($_tmp=$this->_tpl_vars['oErrorInfo']->errValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
        <?php endforeach; endif; unset($_from); ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>