<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo T($this->Data['Title']); ?></h1>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<ul>
	<li>
	<?php echo $this->Form->Label(T('Display in Side Panel'), 'Plugins.DiscussionEvent.DisplayInSidepanel'); ?>
	<?php echo $this->Form->CheckBox('Plugins.DiscussionEvent.DisplayInSidepanel', T('Show upcoming events in side panel?')); ?>
	<p><small><strong>Note:</strong> To manually insert the list of upcoming events into your site, paste e.g. <i>{module name="DiscussionEventModule" CategoryID=6 Limit=3}</i> into your theme's <i>view.master.tpl</i> file to show three upcoming events from the sixth category.</b></small></p>
	</li>
	<li>
	<?php echo $this->Form->Label(T('Number of Displayed Events'), 'Plugins.DiscussionEvent.MaxDiscussionEvents'); ?>
	<p>The maximum number of upcoming events to be shown.</p>
	<?php echo $this->Form->TextBox('Plugins.DiscussionEvent.MaxDiscussionEvents', array('placeholder' => '10')); ?>
	</li>
</ul>
<?php echo $this->Form->Close('Save'); ?>

<div class="Info">
	<p>Do you have questions or feedback? Please visit the <a href="http://vanillaforums.org/addon/discussionevent-plugin">official plugin site</a>.</p>
	<p>Do you want to support the plugin developer? A small donation is always welcome: <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZMYCC6QNTAVRG" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate" style="vertical-align: middle;"></a></p>
</div>