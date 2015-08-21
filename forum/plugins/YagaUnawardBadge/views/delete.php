<?php if (!defined('APPLICATION')) exit(); 

echo Wrap($this->Data('Title'), 'h1');
echo $this->Form->Open();
echo $this->Form->Errors();
echo '<div class="P">'.sprintf(
    T('Are you sure you want to take away <em style="font-style:italic;">%s</em> from %s? If the the conditions for receiving this badge are still met, it will be awarded again.'),
    $this->Data('Badgename'),
    $this->Data('Username')
).'</div>';
echo '<div class="Buttons Buttons-Confirm">';
echo $this->Form->Button('OK', array('class' => 'Button Primary'));
echo $this->Form->Button('Cancel', array('type' => 'button', 'class' => 'Button Close'));
echo '</div>';
echo $this->Form->Close();
