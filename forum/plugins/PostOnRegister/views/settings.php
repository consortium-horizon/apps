<h1><?= $this->Data('Title');
var_dump($this->Data('RegistrationFields'));
 ?></h1>
<div class="Info"><?= $this->Data('Description') ?></div>
<div class="Wrap"><?= Anchor(T('Plugins.PostOnRegister.NewElement', 'New element'), 'settings/postonregisteraddedit', 'SmallButton') ?></div>

<?= $this->Form->Open() ?>
<?= $this->Form->Errors();
$test = array('vanillaelement' => t('Vanilla element'), 'customelement' => t('Custom Element'), 'dom' => t('DOM'));

 ?>
<div id="ForumTourEditContainer">
  <div class>
    <?= $this->Form->Label(T('Plugins.PostOnRegister.EditStepPositionMethod', 'Choose a fied'), 'RegistrationField') ?>
    <?= $this->Form->DropDown('RegistrationField', $this->Data('RegistrationFields') , array('Value' => $this->Form->getValue('PositionMethod'))) ?>
  </div>
</div>

<?= $this->Form->Close('Save') ?>
