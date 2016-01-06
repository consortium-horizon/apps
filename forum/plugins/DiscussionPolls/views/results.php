<?php if(!defined('APPLICATION')) exit();
/* 	Copyright 2013-2014 Zachary Doll */
function DPRenderResults($Poll) {
  $TitleExists = GetValue('Title', $Poll, FALSE);
  $HideTitle = C('Plugins.DiscussionPolls.DisablePollTitle', FALSE);
  echo '<div class="DP_ResultsForm">';

    if($TitleExists || $HideTitle) {
      $TitleS = $Poll->Title;
    }
    else {
      $TitleS = Wrap(T('Plugins.DiscussionPolls.NotFound', 'Sondage non trouvé'));
    }
    echo Wrap($TitleS, 'div', array('class' => 'pollMainTitle'));
    echo '<ol class="DP_ResultQuestions">';
      if(!$TitleExists && !$HideTitle) {
        //do nothing
      }
      else if(!count($Poll->Questions)) {
        echo Wrap(T('Plugins.DiscussionPolls.NoReults', 'Aucun résultat pour ce sondage !'));
      }
      else {
        foreach($Poll->Questions as $Question) {
          RenderQuestion($Question);
        }
      }
    echo '</ol>';
  echo '</div>';
}

function RenderQuestion($Question) {
  echo '<li class="DP_ResultQuestion">';
  echo Wrap($Question->Title .' (' . Plural($Question->CountResponses, '%s vote', '%s votes'). ')', 'div', array('class' => 'pollTitle'));

  // 'randomize' option bar colors
  $k = $Question->QuestionID % 10;
  echo '<ol class="DP_ResultOptions">';
  foreach($Question->Options as $Option) {
    $String = $Option->Title;
    $Percentage = ($Question->CountResponses == 0) ? '0.00' : number_format(($Option->CountVotes / $Question->CountResponses * 100), 2);
    switch (true) {
    case in_array(round($Percentage), range(0,25)):
        $class = 'red';
    break;
    case in_array(round($Percentage), range(26,50)):
        $class = 'yellow';
    break;
    case in_array(round($Percentage), range(51,75)):
        $class = 'blue';
    break;
    case in_array(round($Percentage), range(76,100)):
        $class = 'green';
    break;
    }

    // // let's wrap this up !
    // $bar = Wrap('', 'div', array('class' => 'bar '. $class .''));
    // $fill = Wrap('', 'div', array('class' => 'fill '. $class .''));
    // $slice = Wrap($bar . $fill, 'div', array('class' => 'slice'));
    // $percent = Wrap(round($Percentage) .'%', 'span');
    // $graph = Wrap($percent.$slice, 'div', array('class' => 'c100 p'. round($Percentage) .''));
    // $optionTitle = Wrap($String, 'div', array('class' => 'optionTitle'));
    // echo Wrap($optionTitle.$graph, 'div', array('class' => 'pollResultItem'));


    $barRoof  = Wrap('', 'div', array('class' => 'bar-face face-position roof percentage'));
    $barBack  = Wrap('', 'div', array('class' => 'bar-face face-position back percentage'));
    $barFloor = Wrap('', 'div', array('class' => 'bar-face face-position floor percentage volume-lights'));
    $barLeft  = Wrap('', 'div', array('class' => 'bar-face face-position left'));
    $barRight = Wrap('', 'div', array('class' => 'bar-face face-position right'));
    $barFront = Wrap('', 'div', array('class' => 'bar-face face-position front percentage volume-lights shine'));
    echo '<div class="QuestionTitle">'.$Option->Title.'</div>';
    echo '<div class="progress-bar">';
    echo '<div class="bar has-rotation has-colors cyan heat-gradient" role="progressbar" aria-valuenow="'.round($Percentage).'" aria-valuemin="0" aria-valuemax="100">';
    echo'<div class="tooltip white"></div>';
    echo $barRoof.$barBack.$barFloor.$barLeft.$barRight.$barFront;
    echo '</div></div>';
    // // Put text where it makes sense
    // if($Percentage < 10) {
    //   $String .= '<span class="DP_Bar DP_Bar-' . $k . '" style="width: ' . $Percentage . '%;">&nbsp</span> ' . $Percentage . '%';
    // }
    // else {
    //   $String .= '<span class="DP_Bar DP_Bar-' . $k . '" style="width: ' . $Percentage . '%;">' . $Percentage . '%</span>';
    // }
    // echo Wrap($String, 'li', array('class' => 'DP_ResultOption'));
    // $k = ++$k % 10;
  }
  echo '</ol>';
  echo '</li>';
}
