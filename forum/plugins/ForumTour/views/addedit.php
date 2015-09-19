<h1><?= $this->Data('Title') ?></h1>
<div class="Info"><?= $this->Data('Description') ?></div>

<?= $this->Form->Open() ?>
<?= $this->Form->Errors() ?>
<div id="ForumTourEditContainer">
  <div class>
    <?= $this->Form->Label('Title', 'Title') ?>
    <?= $this->Form->TextBox('Title') ?>
  </div>
  <div>
    <?= $this->Form->Label('Description', 'Description') ?>
    <?= $this->Form->TextBox('Description', array('Multiline' => true)) ?>
  </div>
  <div>
    <?= $this->Form->Label('Position Method', 'PositionMethod') ?>
    <?= $this->Form->DropDown('PositionMethod', array('vanillaelement' => t('Vanilla element'), 'customelement' => t('Custom Element'), 'dom' => t('DOM')), array('Value' => $this->Form->getValue('PositionMethod'))) ?>
  </div>

  <!-- Vanilla element positioning -->
  <div class="positioning positioning--vanilla">
      <div>
        <?= $this->Form->Label('Choose a vanilla element to highlight', 'VanillaTarget') ?>
        <?= $this->Form->DropDown('VanillaTarget', array('Head' => t('Head'), 'Panel' => t('Panel'), 'Content' => t('Content')), array('Value' => $this->Form->getValue('VanillaTarget'))) ?>
      </div>
  </div>

  <!-- Vanilla element positioning -->
  <div class="positioning positioning--custom">
      <?= $this->Form->Label('Enter the ID of the element you want to highlight', 'CustomElement') ?>
      <?= $this->Form->TextBox('CustomElement') ?>
  </div>

  <!-- DOM positioning -->
  <div class="positioning positioning--dom">
      <div class="positioning--dom__inputGroup">
        <?= $this->Form->Label('X Position', 'XPosition') ?>
        <?= $this->Form->TextBox('XPosition') ?>
        <?= $this->Form->DropDown('XPositionType', array('%' => 'Percentage', 'px' => 'Pixel'), array('Value' => $this->Form->getValue('XPositionType'))) ?>
      </div>
      <div class="positioning--dom__inputGroup">
        <?= $this->Form->Label('Y Position', 'YPosition') ?>
        <?= $this->Form->TextBox('YPosition') ?>
        <?= $this->Form->DropDown('YPositionType', array('%' => t('Percentage'), 'px' => t('Pixel')), array('Value' => $this->Form->getValue('YPositionType'))) ?>
      </div>
  </div>

  <div>
    <?= $this->Form->Label('Tooltip Position', 'TooltipPosition') ?>
    <?= $this->Form->DropDown('TooltipPosition', array('top' => t('Top'), 'right' => t('Right'), 'bottom' => t('Bottom'), 'left' => t('Left')), array('Value' => $this->Form->getValue('TooltipPosition'))) ?>
  </div>
</div>
<?= $this->Form->Close('Save') ?>
