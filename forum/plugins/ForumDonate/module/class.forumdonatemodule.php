<?php if (!defined('APPLICATION')) exit();

class ForumDonateModule extends Gdn_Module {
   
 


   public function AssetTarget() {
      return 'Panel';
   }

   public function ToString() {  
     
      echo '<div class="Box DonateBox">'; 
      echo Wrap(T('Donation Box'), 'h4');
      echo '<ul class="PanelInfo">';
      echo '<p></p>';
      echo  T("Your Donations will help keep this forum afloat");
      echo '<p></p>';
     
        echo Anchor(Gdn_Format::Text("My donation link"), Gdn_Format::Url("/donations" ));

 // edit the links above to the appropriate paypal or whatever donate links and image  
    echo Wrap(T('Item 1'), 'li');
    echo Wrap(T('Item 2'), 'li');
    echo  "</ul>";
    echo "</div>";  
   }
}
