<?php if( !defined( 'APPLICATION' ) ) exit(); ?>

<?php
/**
 * Settings
 *
 * Setup settings page HTML
 *
 * @package DiscordWidget
 * @author <a href="http://yaypaul.com">YayPaul (Paul West)</a>
 * @copyright 2017 Paul West.
 * @license MIT
 * @since 1.0
 */
?>

<h1><?php echo T( $this->Data[ 'Title' ] ); ?></h1>

<?php
    echo $this->Form->open();
    echo $this->Form->errors();
?>
<p class="DiscordWidget__info">
    <strong>Your Server ID can be found in your Discord Widget Settings page</strong>
</p>
<p class="DiscordWidget__info">
    If you'd like to enable "Instant Invite", please head to your Discord Server Settings and select an option from the drop down on the Widget Settings page. Your Vanilla widget will also be updated.
</p>
<ul>
    <li><?php 
        echo $this->Form->label( 'What is your server ID?', 'Plugin.DiscordWidget.ServerID' );
        echo $this->Form->input( 'Plugin.DiscordWidget.ServerID' );
    ?></li>
    <li><?php
        echo $this->Form->label( 'Which theme should we use?', 'Plugin.DiscordWidget.Theme' );
        echo $this->Form->dropDown( 'Plugin.DiscordWidget.Theme', array(
            'dark' => 'Dark Theme',
            'light' => 'Light Theme'
        ) );
    ?></li>
    <li><?php 
        echo $this->Form->label( 'How wide do we want it (in pixels)?', 'Plugin.DiscordWidget.Width' );
        echo $this->Form->input( 'Plugin.DiscordWidget.Width' );
    ?></li>
    <li><?php 
        echo $this->Form->label( 'How tall do we want it (in pixels)?', 'Plugin.DiscordWidget.Height' );
        echo $this->Form->input( 'Plugin.DiscordWidget.Height' );
    ?></li>
    <li><?php
        echo $this->Form->label( 'Where should we display the widget in the panel?', 'Plugin.DiscordWidget.ForceTop' );
        echo $this->Form->dropDown( 'Plugin.DiscordWidget.ForceTop', array(
            'top' => 'At the Top',
            'bottom' => 'At the Bottom'
        ) );
        ?>
        <p>
            <?php echo t( '(warning: this might mess with any module sorting plugins)' ); ?>
        </p>
    </li>
</ul>
<br>
<?php
    echo $this->Form->close( 'Save' );
?>

<div class="DiscordWidget__footer">
    <h3><?php echo t( 'Feedback, Suggestions and Bugs' ); ?></h3>
    <div class="DiscordWidget__info">
        <?php echo t( 'Please see the Vanilla Forums Plugin page to give Feedback or Suggestions.' ); ?>
        <br>
        <?php echo Anchor( Gdn_Format::Text( "Discord Widget on Vanilla Forums >>"), 
            Gdn_Format::Url( "https://open.vanillaforums.com/addon/discordwidget-plugin" ) ); ?>
    </div>
    <div class="DiscordWidget__info">
        <?php echo t( 'Please see the Github page for Bug reports, or to Contribute.' ); ?>
        <br>
        <?php echo Anchor( Gdn_Format::Text( "Discord Widget on Github >>"), 
            Gdn_Format::Url( "https://github.com/yaypaul/DiscordWidget" ) ); ?>
    </div>
</div>