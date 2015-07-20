<?php /* Smarty version 2.6.27, created on 2013-08-24 20:55:16
         compiled from public_delayRedirect.html */ ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "public_header.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<script language="javascript">
    var delaySec = '<?php echo $this->_tpl_vars['dfvDelayRedirectData']['delaySec']; ?>
' * 1000;
    var redirectUrl = '<?php echo $this->_tpl_vars['dfvDelayRedirectData']['redirectUrl']; ?>
';
    var redirectTarget = '<?php echo $this->_tpl_vars['dfvDelayRedirectData']['redirectTarget']; ?>
';
</script>
<body>
    <div style="font-color:<?php if ($this->_tpl_vars['dfvDelayRedirectData']['status'] == @ERR_INFO): ?>green<?php else: ?>red<?php endif; ?>"><?php echo $this->_tpl_vars['dfvDelayRedirectData']['info']; ?>
</div>
</body>
<?php echo '
<script language="javascript">
    window.setTimeout( function() {
        if (redirectTarget == \'_self\') {
            self.location.href = redirectUrl;
        } else if (redirectTarget == \'_top\') {
            top.location.href = redirectUrl;
        } else if (redirectTarget == \'_parent\') {
            parent.location.href = redirectUrl;
        }
    },delaySec);
</script>
'; ?>

<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "public_footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>