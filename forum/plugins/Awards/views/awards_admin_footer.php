<?php	if(!defined('APPLICATION')) exit();

?>
<div class="Footer Credits">
	<?php
		$AuthorLink = Anchor('Diego Zanella',
												 'http://dev.pathtoenlightenment.net');
		echo Wrap(sprintf('Awards Plugin by %s.', $AuthorLink),
							'span',
							array('class' => 'Author'));
	?>
</div>
