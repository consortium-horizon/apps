<h1><?= $this->Data('Title') ?></h1>
<div class="Info"><?= $this->Data('Description') ?></div>

<?= $this->Form->Open() ?>
<?= $this->Form->Errors() ?>
<ul>
  <li>
    <?= $this->Form->Label('Title', 'Title') ?>
    <?= $this->Form->TextBox('Title') ?>
  </li>
  <li>
    <?= $this->Form->Label('Description', 'Description') ?>
    <?= $this->Form->TextBox('Description', array('Multiline' => true)) ?>
  </li>
  <li>
    <?= $this->Form->Label('X Position', 'XPosition') ?>
    <?= $this->Form->TextBox('XPosition') ?>
  </li>
  <li>
    <?= $this->Form->Label('Use X position in pixel or percent', 'XPositionType') ?>
    <?= $this->Form->DropDown('XPositionType', array('%' => 'Percentage', 'px' => 'Pixel'), array('Value' => $this->Form->getValue('XPositionType'))) ?>
  </li>
  <li>
    <?= $this->Form->Label('Y Position', 'YPosition') ?>
    <?= $this->Form->TextBox('YPosition') ?>
  </li>
  <li>
    <?= $this->Form->Label('Use Y position in pixel or percent', 'YPositionType') ?>
    <?= $this->Form->DropDown('YPositionType', array('%' => t('Percentage'), 'px' => t('Pixel')), array('Value' => $this->Form->getValue('YPositionType'))) ?>
  </li>
  <li>
    <?= $this->Form->Label('Tooltip Position', 'TooltipPosition') ?>
    <?= $this->Form->DropDown('TooltipPosition', array('top' => t('Top'), 'right' => t('Right'), 'bottom' => t('Bottom'), 'left' => t('Left')), array('Value' => $this->Form->getValue('TooltipPosition'))) ?>
  </li>
</ul>
<?= $this->Form->Close('Save') ?>
