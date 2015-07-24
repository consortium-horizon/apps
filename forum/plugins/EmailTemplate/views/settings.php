<?php if (!defined('APPLICATION')) exit();
/*  Copyright 2014 Guillermo Fernández
*	This program is free software: you can redistribute it and/or modify
*	it under the terms of the GNU General Public License as published by
*	the Free Software Foundation, either version 3 of the License, or
*	(at your option) any later version.
*
*	This program is distributed in the hope that it will be useful,
*	but WITHOUT ANY WARRANTY; without even the implied warranty of
*	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*	GNU General Public License for more details.
*
*	You should have received a copy of the GNU General Public License
*	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>


<div class="Header">
	<?php echo Wrap(T($this->Data['Title']), 'h1'); ?>
 
	<!-- TODO -->

	<?php echo Wrap('Preview', 'h3'); ?>

	<div style="margin: 50px;">

	<?php echo 
			$this->Data['header'].
			"<p>TEST MESSAGE</p>".
			$this->Data['footer']; 
	?>

	</div>

	<?php echo Wrap('Editor', 'h3'); ?>

	<form action="" method="post">

		<?php echo Wrap('Header', 'h4'); ?>
		<textarea style="margin-left:20px;width:90%;min-height:200px" name="header"><?php echo $this->Data['header'] ?></textarea>
		<?php echo Wrap('Footer', 'h4'); ?>
		<textarea style="margin-left:20px;width:90%;min-height:200px" name="footer"><?php echo $this->Data['footer'] ?></textarea>
		
		<br/>
	<input type="submit" class="EnableAddon Button"/>
	</form>



</div>
<?php
