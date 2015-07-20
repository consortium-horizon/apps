<?php /* Smarty version 2.6.27, created on 2013-11-07 13:05:58
         compiled from threadList.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'tplEchoUrl', 'threadList.html', 9, false),array('modifier', 'escape', 'threadList.html', 9, false),array('modifier', 'date_format', 'threadList.html', 39, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "public_header.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<body id="vanilla_discussions_index" class="Vanilla Discussions index">
<div id="Frame">
    <div class="Banner">
		<ul>
		  <li>
		    <a href="<?php echo smarty_function_tplEchoUrl(array('mainName' => 'MainForum.php','cmd' => 'forumList'), $this);?>
" class="" style="display:inline-block;font-size:16px;">All Forums</a><?php if ($this->_tpl_vars['data']['forum']): ?><a href="#" style="display:inline-block;">&gt;</a><a href="<?php echo $this->_tpl_vars['data']['forum']->getForumUrl(); ?>
" style="display:inline-block;" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['data']['forum']->getForumUrlTitle())) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']['forum']->forumName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><?php endif; ?>
		  </li>
		</ul>
	 </div>
	 <div id="Body">
		<div id="Content">
		  <ul class="DataList Discussions">
		  <?php if ($this->_tpl_vars['data']['forums']): ?>
<li class="Item">
		  <div class="ItemContent Discussion">
		    <b>Sub Forums&nbsp;:&nbsp;</b>
		    <?php $_from = $this->_tpl_vars['data']['forums']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oSubMnEtForum']):
?>
		    <a href="<?php echo $this->_tpl_vars['oSubMnEtForum']->getForumUrl(); ?>
" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['oSubMnEtForum']->getForumUrlTitle())) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['oSubMnEtForum']->forumName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>,&nbsp;
		    <?php endforeach; endif; unset($_from); ?>
		  </div>
</li>
		  <?php endif; ?>
		    <?php $_from = $this->_tpl_vars['data']['topics']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oMnEtForumTopic']):
?>
<li class="Item">
      <?php if ($this->_tpl_vars['oMnEtForumTopic']->oAuthorMnEtUser && $this->_tpl_vars['oMnEtForumTopic']->oAuthorMnEtUser->iconUrl->hasSetOriValue()): ?>
      <a title="<?php echo $this->_tpl_vars['oMnEtForumTopic']->oAuthorMnEtUser->getDisplayName(); ?>
" href="#" class="ProfileLink"><img src="<?php echo $this->_tpl_vars['oMnEtForumTopic']->oAuthorMnEtUser->iconUrl->oriValue; ?>
" alt="<?php echo $this->_tpl_vars['oMnEtForumTopic']->oAuthorMnEtUser->getDisplayName(); ?>
" class="ProfilePhotoMedium" style="overflow:hidden" /></a>
      <?php endif; ?>
      <div class="ItemContent Discussion" style="word-break:break-all;word-wrap:break-word;">
      <a href="<?php echo smarty_function_tplEchoUrl(array('mainName' => 'MainTopic.php','cmd' => 'getThread','vName' => 'tid','vValue' => $this->_tpl_vars['oMnEtForumTopic']->topicId->oriValue), $this);?>
" class="Title" style="font-size:15px;"><?php echo ((is_array($_tmp=$this->_tpl_vars['oMnEtForumTopic']->topicTitle->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>            <div class="Meta">
        <?php if ($this->_tpl_vars['oMnEtForumTopic']->isSticky->oriValue): ?>
        <span class="Announcement">Sticky</span>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['oMnEtForumTopic']->oAuthorMnEtUser): ?>
         <span class="Author"><?php echo ((is_array($_tmp=$this->_tpl_vars['oMnEtForumTopic']->oAuthorMnEtUser->userName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</span>
        <?php endif; ?> 
         <span class="Counts"><?php echo $this->_tpl_vars['oMnEtForumTopic']->totalPostNum->oriValue; ?>
</span><span class="LastCommentDate"><?php echo ((is_array($_tmp=$this->_tpl_vars['oMnEtForumTopic']->postTime->oriValue)) ? $this->_run_mod_handler('date_format', true, $_tmp) : smarty_modifier_date_format($_tmp)); ?>
</span>
      </div>
   </div>
</li>
		    <?php endforeach; endif; unset($_from); ?>
</ul>
		</div>
<?php echo $this->_tpl_vars['data']['oMnDataPage']->echoPage(); ?>

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