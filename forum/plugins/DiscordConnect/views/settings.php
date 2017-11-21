<?php if (!defined('APPLICATION')) exit(); ?>
<h1><?php echo T($this->Data['Title']); ?></h1>
<?php
echo $this->Form->Open();
echo $this->Form->Errors();
?>
<ul>
    <li>
    <?php echo $this->Form->Label(T('ApplicationID'), 'Plugins.DiscordConnect.ApplicationID'); ?>
    <p>ClientID found at https://discordapp.com/developers/applications/me</p>
    <?php echo $this->Form->TextBox('Plugins.DiscordConnect.ApplicationID', array('placeholder' => 'ApplicationID')); ?>
    </li>
    <li>
    <?php echo $this->Form->Label(T('Secret'), 'Plugins.DiscordConnect.Secret'); ?>
    <p>Client Secret found at https://discordapp.com/developers/applications/me</p>
    <?php echo $this->Form->TextBox('Plugins.DiscordConnect.Secret', array('placeholder' => 'Secret')); ?>
    </li>
    <li>
    <?php echo $this->Form->Label(T('BotToken'), 'Plugins.DiscordConnect.BotToken'); ?>
    <p>BotToken found at https://discordapp.com/developers/applications/me (App bot user section)</p>
    <?php echo $this->Form->TextBox('Plugins.DiscordConnect.ApplicationID', array('placeholder' => 'BotToken')); ?>
    </li>
    <li>
    <?php echo $this->Form->Label(T('GuildId'), 'Plugins.DiscordConnect.GuildId'); ?>
    <p>GuildId found by right clicking your server and selecting "copy id"</p>
    <?php echo $this->Form->TextBox('Plugins.DiscordConnect.GuildId', array('placeholder' => 'GuildId')); ?>
    </li>
</ul>
<?php echo $this->Form->Close('Save'); ?>

<div class="Info">
    <p>Do you have questions or feedback? Please visit the <a href="http://vanillaforums.org/addon/discussionevent-plugin">official plugin site</a>.</p>
    <p>Do you want to support the plugin developer? A small donation is always welcome: <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=ZMYCC6QNTAVRG" target="_blank"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" alt="Donate" style="vertical-align: middle;"></a></p>
</div>