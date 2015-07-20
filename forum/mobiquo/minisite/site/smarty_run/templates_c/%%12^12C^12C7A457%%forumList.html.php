<?php /* Smarty version 2.6.27, created on 2013-11-07 13:02:51
         compiled from forumList.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'tplEchoUrl', 'forumList.html', 8, false),array('modifier', 'escape', 'forumList.html', 16, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "public_header.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<body id="vanilla_discussions_index" class="Vanilla Discussions index">
<div id="Frame">
    <div class="Banner">
		<ul>
		  <li><a href="<?php echo smarty_function_tplEchoUrl(array('mainName' => 'MainForum.php','cmd' => 'forumList'), $this);?>
" class="" style="font-size:16px;">All Forums</a></li>
		</ul>
	 </div>
	 <div id="Body">
		<div id="Content">
<ul class="DataList CategoryList">
    <?php $_from = $this->_tpl_vars['objsMnEtForum']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oMnEtForum']):
?>
    <li class="Item Depth1 Unread">
               <div class="ItemContent Category Unread"><a href="<?php echo $this->_tpl_vars['oMnEtForum']->getForumUrl(); ?>
" class="Title" style="font-size:16px;word-break:break-all;word-wrap:break-word;" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['oMnEtForum']->getForumUrlTitle())) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['oMnEtForum']->forumName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><div class="CategoryDescription"><?php echo ((is_array($_tmp=$this->_tpl_vars['oMnEtForum']->description->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div><div class="Meta">
                     <span class="DiscussionCount"><?php echo $this->_tpl_vars['oMnEtForum']->totalTopicNum->oriValue; ?>
 discussions</span>
                     </div>
               </div>
    </li>
               <?php $_from = $this->_tpl_vars['oMnEtForum']->objsSubMnEtForum; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oLv2MnEtForum']):
?>
    <li class="Item Depth2 Read">
               <div class="ItemContent Category Read"><a href="<?php echo $this->_tpl_vars['oLv2MnEtForum']->getForumUrl(); ?>
" class="Title" style="font-size:16px;word-break:break-all;word-wrap:break-word;" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['oLv2MnEtForum']->getForumUrlTitle())) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['oLv2MnEtForum']->forumName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><div class="CategoryDescription"><?php echo ((is_array($_tmp=$this->_tpl_vars['oLv2MnEtForum']->description->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div><div class="Meta">
                     <span class="DiscussionCount"><?php echo $this->_tpl_vars['oLv2MnEtForum']->totalTopicNum->oriValue; ?>
 discussions</span>
                     <?php if ($this->_tpl_vars['oLv2MnEtForum']->objsSubMnEtForum): ?>
                     <span class="ChildCategories"><b>Child Categories:</b> 
                        <?php $_from = $this->_tpl_vars['oLv2MnEtForum']->objsSubMnEtForum; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oLv3MnEtForum']):
?>
                        <a href="<?php echo $this->_tpl_vars['oLv3MnEtForum']->getForumUrl(); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['oLv3MnEtForum']->getForumUrlTitle())) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['oLv3MnEtForum']->forumName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>, 
                            <?php $_from = $this->_tpl_vars['oLv3MnEtForum']->objsSubMnEtForum; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oLv4MnEtForum']):
?>
                        <a href="<?php echo $this->_tpl_vars['oLv4MnEtForum']->getForumUrl(); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['oLv4MnEtForum']->getForumUrlTitle())) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['oLv4MnEtForum']->forumName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>, 
                                <?php $_from = $this->_tpl_vars['oLv4MnEtForum']->objsSubMnEtForum; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oLv5MnEtForum']):
?>
                        <a href="<?php echo $this->_tpl_vars['oLv5MnEtForum']->getForumUrl(); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['oLv5MnEtForum']->getForumUrlTitle())) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['oLv5MnEtForum']->forumName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>, 
                                <?php endforeach; endif; unset($_from); ?>
                            <?php endforeach; endif; unset($_from); ?>
                        <?php endforeach; endif; unset($_from); ?>
                     </span>
                     <?php endif; ?>
                     </div>
               </div>
    </li>
               <?php endforeach; endif; unset($_from); ?>
    <?php endforeach; endif; unset($_from); ?>
</ul>
		</div>
	 </div>
	 <div id="Foot">
		<div class="FootMenu">
        <span><?php if ($this->_tpl_vars['tapatalkPluginApiConfig']['nativeSitePcModeUrl']): ?><a href="<?php echo $this->_tpl_vars['tapatalkPluginApiConfig']['nativeSitePcModeUrl']; ?>
" class="">Full Site</a><?php endif; ?></span>
		</div>
		<div>
		  <a href="http://tapatalk.com/"><span>Powered by Tapatalk</span></a>
		</div>
	 </div>
  </div>


</body>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "public_footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>