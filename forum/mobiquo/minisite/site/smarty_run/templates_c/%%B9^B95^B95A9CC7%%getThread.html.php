<?php /* Smarty version 2.6.27, created on 2013-11-07 13:07:38
         compiled from getThread.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('function', 'tplEchoUrl', 'getThread.html', 9, false),array('modifier', 'escape', 'getThread.html', 9, false),array('modifier', 'date_format', 'getThread.html', 31, false),)), $this); ?>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "public_header.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
<body id="vanilla_discussion_index" class="Vanilla Discussion Index ">
  <div id="Frame">
	 <div class="Banner">
		<ul>
		  <li>
		    <a href="<?php echo smarty_function_tplEchoUrl(array('mainName' => 'MainForum.php','cmd' => 'forumList'), $this);?>
" style="display:inline-block;font-size:16px;">All Forums</a><?php if ($this->_tpl_vars['data']['navi']): ?><?php $_from = $this->_tpl_vars['data']['navi']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oNaviMnEtForum']):
?><a href="#" style="display:inline-block;">&gt;</a><a href="<?php echo $this->_tpl_vars['oNaviMnEtForum']->getForumUrl(); ?>
" style="display:inline-block;" title="<?php echo ((is_array($_tmp=$this->_tpl_vars['oNaviMnEtForum']->getForumUrlTitle())) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['oNaviMnEtForum']->forumName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a><?php endforeach; endif; unset($_from); ?><?php endif; ?>
		  </li>
		</ul>
	 </div>
	 <div id="Body">
		<div id="Content">
		  <div class="Tabs HeadingTabs DiscussionTabs FirstPage">
   <div class="SubTab" style="font-size:16px;word-break:break-all;word-wrap:break-word;"><?php echo ((is_array($_tmp=$this->_tpl_vars['data']['topic']->topicTitle->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</div>
</div>
<ul class="DataList MessageList Discussion FirstPage">
<?php $_from = $this->_tpl_vars['data']['posts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oMnEtForumPost']):
?>
<li class="Item Comment" id="Comment_<?php echo $this->_tpl_vars['oMnEtForumPost']->postId->oriValue; ?>
">
   <div class="Comment">
      <div class="Meta">
        <?php if ($this->_tpl_vars['oMnEtForumPost']->oAuthorMnEtUser): ?>
                  <span class="Author">
            <?php if ($this->_tpl_vars['oMnEtForumPost']->oAuthorMnEtUser->iconUrl->hasSetOriValue()): ?>
            <a title="admin" href="#"><img src="<?php echo $this->_tpl_vars['oMnEtForumPost']->oAuthorMnEtUser->iconUrl->oriValue; ?>
" alt="admin" class="ProfilePhotoMedium" /></a>
            <?php endif; ?>
            <a href="#"><?php echo ((is_array($_tmp=$this->_tpl_vars['oMnEtForumPost']->oAuthorMnEtUser->userName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>         </span>
        <?php endif; ?>
         <span class="DateCreated">
            <?php echo ((is_array($_tmp=$this->_tpl_vars['oMnEtForumPost']->postTime->oriValue)) ? $this->_run_mod_handler('date_format', true, $_tmp) : smarty_modifier_date_format($_tmp)); ?>

         </span>
                  <div class="CommentInfo">
                     </div>
               </div>
      <div class="Message" id="mnMsg_<?php echo $this->_tpl_vars['oMnEtForumPost']->postId->oriValue; ?>
">
        <?php echo $this->_tpl_vars['oMnEtForumPost']->postContent->mnDisplayValue; ?>

        <?php if ($this->_tpl_vars['oMnEtForumPost']->objsNotInContentMbqEtAtt): ?>
        <br /><br />
        <div><b>Attachments</b></div>
        <?php $_from = $this->_tpl_vars['oMnEtForumPost']->objsNotInContentMbqEtAtt; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['oNotInContentMbqEtAtt']):
?>
            <?php if ($this->_tpl_vars['oNotInContentMbqEtAtt']->isImage()): ?>
                <a href="<?php echo $this->_tpl_vars['oNotInContentMbqEtAtt']->url->oriValue; ?>
" target="_blank"><img src="<?php echo $this->_tpl_vars['oNotInContentMbqEtAtt']->thumbnailUrl->oriValue; ?>
" style="height:100px;width:100px;" /></a>
            <?php else: ?>
            <div>
                <a href="<?php echo $this->_tpl_vars['oNotInContentMbqEtAtt']->url->oriValue; ?>
" target="_blank"><?php echo ((is_array($_tmp=$this->_tpl_vars['oNotInContentMbqEtAtt']->uploadFileName->oriValue)) ? $this->_run_mod_handler('escape', true, $_tmp, 'html') : smarty_modifier_escape($_tmp, 'html')); ?>
</a>
            </div>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
        <?php endif; ?>
      </div>
   </div>
</li>
<?php endforeach; endif; unset($_from); ?>
</ul>
<?php echo $this->_tpl_vars['data']['oMnDataPage']->echoPage(); ?>

   <div class="Foot">
    <!--
      <a href="http://192.168.0.101/vanilla_2-0-18-8/entry/signin?Target=discussion%2F11%2Ftest-topic%3Fpost%23Form_Body" class="TabLink">Add a Comment</a> 
      -->
   </div>
   
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

<script>
<?php echo '
function exttResizeImage() {
    var tempDivs = document.getElementsByTagName(\'div\');
    for (var i = 0;i < tempDivs.length;i ++) {
        var tempDiv = tempDivs[i];
        if (tempDiv.id.indexOf(\'mnMsg_\') == 0) {
        }
    }
}
'; ?>

</script>

</body>
<?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "public_footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>