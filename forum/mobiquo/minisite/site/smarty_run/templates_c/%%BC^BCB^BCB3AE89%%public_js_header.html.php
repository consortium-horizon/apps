<?php /* Smarty version 2.6.27, created on 2013-08-24 20:53:06
         compiled from public_js_header.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'public_js_header.html', 26, false),)), $this); ?>
    <script language="Javascript" src="<?php echo @MPF_C_JS_PUBLIC_URL; ?>
common.js"></script>
    <script>
        /* 初始化错误信息常量到jCv */
        jCv.ERR_TOP = <?php echo @ERR_TOP; ?>
;
        jCv.ERR_HIGH = <?php echo @ERR_HIGH; ?>
;
        jCv.ERR_APP = <?php echo @ERR_APP; ?>
;
        jCv.ERR_INFO = <?php echo @ERR_INFO; ?>
;
        
        jCv.jsLibUrl = '<?php echo @MPF_C_JS_PUBLIC_URL; ?>
';       /* 公共js目录 */
        jCv.cssLibUrl = '<?php echo @MPF_C_CSS_PUBLIC_URL; ?>
';       /* 公共css目录 */
        jCv.imgLibUrl = '<?php echo @MPF_C_IMG_PUBLIC_URL; ?>
';       /* 公共img目录 */
        jCv.flashLibUrl = '<?php echo @MPF_C_FLASH_PUBLIC_URL; ?>
';       /* 公共flash目录 */
        
        jCv.appJsLibUrl = '<?php echo @MPF_C_APP_JS_URL; ?>
';       /* 模块js目录 */
        jCv.appCssLibUrl = '<?php echo @MPF_C_APP_CSS_URL; ?>
';       /* 模块css目录 */
        jCv.appImgLibUrl = '<?php echo @MPF_C_APP_IMG_URL; ?>
';       /* 模块img目录 */
        jCv.appFlashLibUrl = '<?php echo @MPF_C_APP_FLASH_URL; ?>
';       /* 模块flash目录 */
        
        jCv.rewriteMethod = null;  /* rewrite方式 */
        jCv.mainHomeDomain = '<?php echo @MPF_C_MAIN_HOMEDOMAIN; ?>
';   /* 主站域名 */
        jCv.homeDomain = '<?php echo @MPF_C_HOMEDOMAIN; ?>
';   /* 模块域名 */
        jCv.theme = '<?php echo @MPF_C_THEME; ?>
';  /* 项目皮肤名 */
        
        /* 默认公共信息 */
        jCv.info['cm_program_error'] = '<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['mpfLangCm']['cm_program_error'])) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')))) ? $this->_run_mod_handler('escape', true, $_tmp, 'javascript') : smarty_modifier_escape($_tmp, 'javascript')); ?>
';
    </script>