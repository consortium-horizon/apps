<?php if (!defined('APPLICATION')) exit();

// This is the module used by the ForumTour plugin
class ForumTourMenuModule extends Gdn_Module {

  // The tutorial link will be rendered in the sidebar
  public function AssetTarget() {
    return 'Panel';
  }

  public function ToString() {
    // This will help us render all tutorial steps
    $counter = 1;
    $ForumtourData = c('ForumTour', array());

    // Turn on buffer
    ob_start();
    // We have to check if there is a first step (title + description + position), or the tutorial won't be displayed
    if ($ForumtourData) {
      // First, we render the button
      include 'views/homeTutorialButton.php';
      // Then we create the list ...
      echo "<ul class='cd-tour-wrapper'>";
      foreach ($ForumtourData as $index => $step) {
        // Set step title & description ...
        $stepTitle = $step['Title'];
        $PositionMethod = $step['PositionMethod'];
        $VanillaTarget = $step['VanillaTarget'];
        $CustomElement = $step['CustomElement'];
        // get the display method selected by User
        $stepDescription = $step['Description'];
        // ... its relative position to the step indicator ...
        $TooltipPosition= $step['TooltipPosition'];
        // ... then the X coordinate ...
        $Xposition = $step['XPosition'];
        // ... do you prefer px or % ? ...
        $XpositionType = $step['XPositionType'];
        // ... then the Y coordinate ...
        $Yposition = $step['YPosition'];
        // ... do you prefer px or % ? ...
        $YpositionType = $step['YPositionType'];
        // We render the step using the template file
        include 'views/homeTutorial.php';
      }
      // Close the list
      echo "</ul>";
    }
    // Ouput the result
		$String = ob_get_contents();
		@ob_end_clean();
		return $String;
  }
}
