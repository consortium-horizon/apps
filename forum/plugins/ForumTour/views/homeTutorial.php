<?php defined('APPLICATION') or die;
?>

<!-- this is the tutorial itself -->
  <li
        <?php

        echo "class='cd-single-step";
        if (($PositionMethod == 'vanillaelement') || ($PositionMethod == 'customelement')) {
            echo " cd-element-highlight";
        }
        echo "' ";
        echo "data-target = '". ($PositionMethod == 'vanillaelement' ? $VanillaTarget : $CustomElement) . "'";

        if ($PositionMethod == 'dom') {
            echo "style=' left:" . $Xposition . $XpositionType . "; top:" . $Yposition . $YpositionType . ";'";
        }

        echo "data-positiontype='". $TooltipPosition ."'";
        ?>
      >
    <span></span>

    <div class="cd-more-info <?php echo $TooltipPosition; ?>">
      <h2><?php echo $stepTitle; ?></h2>
      <p><?php echo $stepDescription; ?></p>
    </div>
  </li>
