<h1><?= $this->Data('Title') ?></h1>
<div class="Info"><?= $this->Data('Description') ?></div>

<?= $this->Form->Open() ?>
<?= $this->Form->Errors() ?>
<div id="ForumTourEditContainer">
  <div class>
    <?= $this->Form->Label(T('Plugins.ForumTour.EditStepTitle', 'Title'), 'Title') ?>
    <?= $this->Form->TextBox('Title') ?>
  </div>
  <div>
    <?= $this->Form->Label(T('Plugins.ForumTour.EditStepDescription', 'Description'), 'Description') ?>
    <?= $this->Form->TextBox('Description', array('Multiline' => true)) ?>
  </div>
  <div>
    <?= $this->Form->Label(T('Plugins.ForumTour.EditStepPositionMethod', 'Display method'), 'PositionMethod') ?>
    <?= $this->Form->DropDown('PositionMethod', array('vanillaelement' => t('Vanilla element'), 'customelement' => t('Custom Element'), 'dom' => t('DOM')), array('Value' => $this->Form->getValue('PositionMethod'))) ?>
  </div>

  <!-- Vanilla element positioning -->
  <div class="positioning positioning--vanilla">
      <div>
        <?= $this->Form->Label(T('Plugins.ForumTour.EditStepVanillaElement', 'Choose a vanilla element to highlight'), 'VanillaTarget') ?>
        <?= $this->Form->DropDown('VanillaTarget', array('#Head .Row' => t('Head'), '#Panel' => t('Panel'), '#Content' => t('Content')), array('Value' => $this->Form->getValue('VanillaTarget'))) ?>
      </div>
  </div>

  <!-- Custom element positioning -->
  <div class="positioning positioning--custom">
      <?= $this->Form->Label(T('Plugins.ForumTour.EditStepCustomElement', 'Enter the ID of the element you want to highlight'), 'CustomElement') ?>
      <p>
          <?=T('Plugins.ForumTour.EditStepCustomElementDescription', 'You can combine ID with classes (or anything that works with jquey queryselector). <strong>Warning</strong>, custom CSS properties can affect and cause issues with this method.') ?>
      </p>
      <?= $this->Form->TextBox('CustomElement') ?>
  </div>

  <!-- DOM positioning -->
  <div class="positioning positioning--dom">
      <div class="positioning--dom__inputGroup">
        <?= $this->Form->Label(T('Plugins.ForumTour.EditStepXPosition', 'X Position'), 'XPosition') ?>
        <?= $this->Form->TextBox('XPosition') ?>
        <?= $this->Form->DropDown('XPositionType', array('%' => T('Plugins.ForumTour.Percentage', 'Percentage'), 'px' => 'Pixels'), array('Value' => $this->Form->getValue('XPositionType'))) ?>
      </div>
      <div class="positioning--dom__inputGroup">
        <?= $this->Form->Label(T('Plugins.ForumTour.EditStepYPosition', 'Y Position'), 'YPosition') ?>
        <?= $this->Form->TextBox('YPosition') ?>
        <?= $this->Form->DropDown('YPositionType', array('%' => T('Plugins.ForumTour.Percentage', 'Percentage'), 'px' => t('Pixels')), array('Value' => $this->Form->getValue('YPositionType'))) ?>
      </div>
  </div>

  <div>
    <?= $this->Form->Label(T('Plugins.ForumTour.EditStepTooltipPosition', 'Tooltip Position'), 'TooltipPosition') ?>
    <?= $this->Form->DropDown('TooltipPosition', array('top' => T('Plugins.ForumTour.Top', 'top'), 'right' => T('Plugins.ForumTour.Right', 'Right'), 'bottom' => T('Plugins.ForumTour.Bottom', 'Bottom'), 'left' => T('Plugins.ForumTour.Left', 'Left')), array('Value' => $this->Form->getValue('TooltipPosition'))) ?>
  </div>
</div>

<?= $this->Form->Close('Save') ?>
