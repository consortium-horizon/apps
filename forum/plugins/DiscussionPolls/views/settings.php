<?php if(!defined("APPLICATION")) exit();
/* Copyright 2013 Zachary Doll */
echo Wrap(T($this->Data['Title']), 'h1');

echo $this->Form->Open();
echo $this->Form->Errors();

echo Wrap(
        Wrap(
                $this->Form->Label(T('Afficher les résultats'), 'Plugins.DiscussionPolls.EnableShowResults') .
                Wrap($this->Form->CheckBox('Plugins.DiscussionPolls.EnableShowResults') .
                        T('Autoriser les utilisateurs à voir les résultats sans avoir voté'), 'div', array('class' => 'Info'), 'li') .
                $this->Form->Label(T('Titre du sondage'), 'Plugins.DiscussionPolls.DisablePollTitle') .
                Wrap($this->Form->CheckBox('Plugins.DiscussionPolls.DisablePollTitle') .
                        T('Désactiver les titres de sondage'), 'div', array('class' => 'Info')), 'li'), 'ul');

echo $this->Form->Close("Enregistrer");
?>
<div class="Footer">
<?php
echo Wrap(T('Feedback'), 'h3');
?>
  <div class="Aside Box">
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
      <input type="hidden" name="cmd" value="_s-xclick">
      <input type="hidden" name="hosted_button_id" value="SVD3SRMHPSCZ2">
      <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
      <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>

  </div>
<?php
echo Wrap('Find this plugin helpful? Want to support a freelance developer?<br/>Click the donate button to buy me a beer. :D', 'div', array('class' => 'Info'));
echo Wrap('Confused by something? Check out the feedback thread on the official <a href="http://vanillaforums.org/discussion/24670/feedback-for-discussion-polls" target="_blank">Vanilla forums</a>.', 'div', array('class' => 'Info'));
?>
</div>
