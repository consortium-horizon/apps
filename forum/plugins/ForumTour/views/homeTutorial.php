<?php defined('APPLICATION') or die;
?>

<!-- this is the tutorial itself -->
  <!-- and so on .... -->
  <li class="cd-single-step"
      style="left:<?php echo $Xposition.$XpositionType; ?>;
             top:<?php echo $Yposition.$YpositionType; ?>">
    <span></span>

    <div class="cd-more-info <?php echo $TooltipPosition; ?>">
      <h2><?php echo $stepTitle; ?></h2>
      <p><?php echo $stepDescription; ?></p>
    </div>
  </li>
