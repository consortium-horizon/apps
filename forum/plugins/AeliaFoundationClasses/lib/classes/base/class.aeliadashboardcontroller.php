<?php


namespace Aelia;
if (!defined('APPLICATION')) exit();

/**
 * Base Dashboard Controller. This class extends the standard DashboardController
 * provided by Garden framework, and adds most of the features introduced by
 * Aelia\Controller class.
 *
 * Note: the code for the new features have been copy/pasted
 * because the only way to avoid it would be inheriting from both the original
 * DashboardController AND from Aelia\Controller, which is not possible.
 *
 * @see \DashboardController.
 * @see \Aelia\Controller.
 */
class DashboardController extends Controller {
	/**
	 * This is a good place to include JS, CSS, and modules used by all methods of this controller.
	 *
	 * Always called by dispatcher before controller's requested method.
	 */
	public function Initialize() {
		$this->InitializeForDashboard();

		parent::Initialize();
	}
}
