<?php if (!defined('APPLICATION')) exit();


?>
<div class="LoggerPlugin">
	<div class="Content">
		<fieldset>
			<legend>
				<h3><?php echo T('Logger for Vanilla'); ?></h3>
				<p>
					<?php
					echo Wrap(sprintf(T('To modify the logger please can edit file <i>config.xml</i>, '.
															'located in <i>%s</i>.'),
														PATH_PLUGINS . '/Logger/'),
										'p');
					echo Wrap(T('You can find more information on how to write a configuration file ' .
											'on <a href="http://logging.apache.org/log4php/quickstart.html" ' .
											'title="Log4php - Quick start">Log4php website</a>.'),
										'p');
					?>
				</p>
			</legend>
		</fieldset>
		<?php
			 echo $this->Form->Close();
		?>
	</div>
</div>
