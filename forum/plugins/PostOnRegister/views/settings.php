<?php if(!defined("APPLICATION")) exit();
/* Copyright 2013 Zachary Doll */
echo Wrap(T($this->Data['Title']), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(
        Wrap(
                $this->Form->Label(T('RegisteredRoleID'), 'Plugins.PostOnRegister.RegisteredRoleID') .
                Wrap($this->Form->TextBox('Plugins.PostOnRegister.RegisteredRoleID') .
                        T(' Role ID pour inscrit pas candidat'), 'div', array('class' => 'Info'), 'li') )
                , 'ul');

echo $this->Form->Close("Enregistrer");
?>