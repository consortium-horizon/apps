<?php	if (!defined('APPLICATION')) exit();

use \Aelia\AFC\Definitions;

	// This array will be used to assign specific Classes to each Tab in the page.
	// As of 18/03/2012, it just contains one entry, where they key is the current
	// path and the value is the class "Active".
	// This will be used to highlight the "Active Tab", following the logic
	// "assign class Active to the Tab associated to current path".
	$TabsClasses = array();
	$TabsClasses[$this->Data['CurrentPath']] = 'Active';

	/**
	 * Renders the HTML Markup that will appear on the page as a Tab.
	 *
	 * This function has been introduced to reduce the amount of duplicate HTML
	 * used to render the page.
	 *
	 * @param string Title The label that will be assigned to the Tab.
	 * @param string URL The Title will be transfored into a link, which will
	 * point to this URL.
	 * @param array An associative array of classes to assign to each tab. It's
	 * mainly used to determine which Tab will be appear as "Active".
	 *
	 * @return An HTML string that will be rendered as a Tab via CSS.
	 */
	function RenderTabItem($Label, $URL, array $Classes) {
		$Result = sprintf("<li class=\"%s\">\n" .
											"	<span>%s</span>\n" .
											"</li>\n",
											GetValue($URL, $Classes, ''),
											Anchor($Label, $URL));
		return $Result;
	}
?>
	<div>
		<h1><?php echo T($this->Data['Title']); ?></h1>
	</div>
	<div class="Tabs">
		<ul>
			<?php
				echo RenderTabItem(T('General Settings'), Definitions::URL('settings'), $TabsClasses);
				echo RenderTabItem(T('Overrides'), Definitions::URL('overrides_list'), $TabsClasses);
			?>
		</ul>
	</div>
