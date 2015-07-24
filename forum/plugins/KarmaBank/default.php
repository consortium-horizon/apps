<?php if (!defined('APPLICATION')) exit();
/**
*
* # KarmaBank #
*
* ### About ###
* Using simple forum/plugin meta based rules such as comment count, users can earn karma, which is added to their balance, and can even be traded for goods and privileges.
*
* ### Sponsor ###
* Special thanks to Bigfan (http://manyuses.org) for making this happen.
*/

$PluginInfo['KarmaBank'] = array(
    'Name' => 'Karma Bank',
    'Description' => 'Using simple forum/plugin meta based rules such as comment count, users can earn karma, which is added to their balance, and can even be traded for goods and privileges. <b>You must add a starting balance, and add at least 1 rule to activate</b>.',
    'SettingsUrl' => '/dashboard/settings/karmabank',
    'SettingsPermission' => 'Garden.Settings.Manage',
    'RegisterPermissions' =>array('Plugins.KarmaBank.RewardTax'),
    'RequiredApplications' => array('Vanilla' => '2.1'),
    'Version' => '0.9.7.3b',
    'Author' => "Paul Thomas",
    'AuthorEmail' => 'dt01pqt_pt@yahoo.com'
);

include_once(PATH_PLUGINS.'/KarmaBank/class.karmabankmodel.php');
include_once(PATH_PLUGINS.'/KarmaBank/class.karmarulesmodel.php');

class KarmaBank extends Gdn_Plugin {

    public $Meta;
    public $Operations;
    public $OperationsMap;
    public $OperationsOptions = array();
    public $DontTally=array();
    public $MoreMeta=array();
    static public $KarmaChecked = FALSE;

    /*
     *  The meta spec
     */

    public function MetaMap(){
        $this->Meta=array(
            'CountVisits'=>'Counts every session vist',
            'CountComments'=>'Counts every time a member adds a comment',
            'CountDiscussions'=>'Counts every time a member adds a discussion or question (regardless of type)',
            'QnACountAccept'=>'(Requires Q&A plugin) Counts every time a member accepts an answer to their question',
            'QnACountAcceptance'=>'(Requires Q&A plugin) Counts every time a member has their answer to a question accepted  (excluding their own)'
        );

        $this->Operations=array(
            'Equals'=>'When Meta == Target then add Amount (uses absolute value). Not retrospective',
            'Every'=>'When Meta % Target == 0 then add Amount (uses absolute value). Not retrospective based on absolute Meta value',
            'DiffEquals'=>'Recomended over Every. Works out the relative difference between last transaction on that rule, checks if equals and returns a negative/positive based on down or up'
        );
        
        $this->OperationsMap=array(
            'Equals'=>'KarmaBank::OperationEquals',
            'Every'=>'KarmaBank::OperationEvery',
            'DiffEquals'=>'KarmaBank::OperationDiffEquals'
        );
        //custom mappings
        $this->FireEvent('KarmaBankMetaMap');

    }
    
    /*Extend API*/
    
    public function AddMeta($Name, $Explanation,$DontTally=FALSE){
        $this->Meta[$Name]=$Explanation;
        if($DontTally)
            $this->DontTally[]=$Name;
    }
      
    public function AddOpperation($Name, $Explanation, $Function, $Options=array()){
        $this->Operations[$Name]=$Explanation;
        $this->OperationsMap[$Name]=$Function;
        $this->OperationsOptions[$Name]=$Options;
    }
    
    public function AddMetaValues($UserID,$MetaValues){
        if(!GetValue($UserID,$this->MoreMeta))
            $this->MoreMeta[$UserID]=array();
        $this->MoreMeta[$UserID]=array_merge($this->MoreMeta[$UserID],$MetaValues);
        
    }
    
    /*Extend API*/
    
    /*Default Operations*/
    
    public static function OperationEquals($MetaValue,$Target,$Condition,$User,$LastTrans,$Option){
        return $MetaValue == $Target;
    }
    
    public static function OperationEvery($MetaValue,$Target,$Condition,$User,$LastTrans,$Option){
        return $MetaValue % $Target == 0;
    }
    
    public static function OperationDiffEquals($MetaValue,$Target,$Condition,$User,$LastTrans,$Option){
        $Difference=$MetaValue-($LastTrans ? $LastTrans->LastTally : 0);
        if($Difference && abs($Difference)%$Target!==0)
            return FALSE;
        return abs($Difference)!= $Difference? -1:1;
    }
    
    /*Default Operations*/


    /*
    *   Settings Menu Hack (force under Users reputation/privilege related stuff)
    */
    public function Base_GetAppSettingsMenuItems_Handler($Sender) {
        $Menu = &$Sender->EventArguments['SideMenu'];
        $Menu->AddItem('KarmaBank', T('KarmaBank.KarmaBank','Karma Bank'),FALSE, array('class' => 'Reputation'));
        $Menu->AddLink('KarmaBank', T('KarmaBank.SettingsRules','Settings/Rules'), 'settings/karmabank', 'Garden.Settings.Manage');
        if(!C('Garden.DashboardMenu.Sort')){
            //resort KarmaBank menu bellow Users
            $Items = array_keys($Menu->Items);
            $PK = array_search('KarmaBank',$Items);
            $PA = array_splice($Items,$PK,1);
            $UK = array_search('Users',$Items);
            array_splice($Items,$UK+1,0,$PA);
            $MItems=array();
            foreach ($Items As $Item)
                $MItems[$Item]=$Menu->Items[$Item];
            $Menu->Items=$MItems;
            $Menu->Sort=$Items;
        }
    }

    /*
    *   Dashboard interface for setting up KarmaBank
    */
    public function SettingsController_KarmaBank_Create($Sender) {
        $Sender->Permission('Garden.Settings.Manage');
        $KarmaRules=new KarmaRulesModel();
        $Sender->Form = Gdn::Factory('Form');
        if($Sender->Form->IsPostBack() != False){
            $FormValues = $Sender->Form->FormValues();
            //$FormValues['RuleID']=1;
            if($Sender->Form->GetValue('Task')=='AddRule'){ 
                $KarmaRules->DefineSchema();
                $Validation = &$KarmaRules->Validation;
                $KarmaRules->Save($FormValues);
            }else if($Sender->Form->GetValue('Task')=='DisplayOptions'){
                $Validation = new Gdn_Validation();
                SaveToConfig('Plugins.KarmaBank.CommentShowBalance',$FormValues['CommentShowBalance']);
                
            }else if($Sender->Form->GetValue('Task')=='AddStartingBalance'){
                $Validation = new Gdn_Validation();
                $Validation->ApplyRule('StartingBalance', 'Decimal','Starting Balance invalid amount');
                $Validation->Validate($FormValues);
                if (count($Validation->Results()) == 0) {
                    SaveToConfig('Plugins.KarmaBank.StartingBalance', number_format(GetValue('StartingBalance',$FormValues),2,'.',''));
                }
            }else{
                $Validation = new Gdn_Validation();
                $Validation->ApplyRule('Task','Required');
                $Validation->Validate($FormValues);
            }
            if (count($Validation->Results()) == 0) {
                Redirect('/settings/karmabank');
            }
            $Sender->Form->SetValidationResults($Validation->Results());

        }else if(array_key_exists(0,$Sender->RequestArgs) && strtolower($Sender->RequestArgs[0])=='remove' && ctype_digit($Sender->RequestArgs[1])){
            $KarmaRules->RemoveRule($Sender->RequestArgs[1]);
            Redirect('/settings/karmabank');
        }

        $Rules = $KarmaRules->GetRules();

        if($Rules && is_numeric(C('Plugins.KarmaBank.StartingBalance')) && C('Plugins.KarmaBank.StartingBalance')>=0){
            SaveToConfig('Plugins.KarmaBank.Enabled',TRUE);
        }else{
            SaveToConfig('Plugins.KarmaBank.Enabled',FALSE);
        }


        $Sender->AddSideMenu();
        $Sender->SetData('Title', T('KarmaBank.KarmaBankSettings','Karma Bank Settings'));
        $Sender->SetData('Description', T('KarmaBank.KarmaBankDescription',$this->PluginInfo['Description']));
        $Sender->SetData('Meta',$this->Meta);
        $Sender->SetData('Operations',$this->Operations);

        $Sender->SetData('Rules',$Rules );
        $Sender->SetData('Enabled',$this->IsEnabled());
        $Sender->Form->SetValue('CommentShowBalance',C('Plugins.KarmaBank.CommentShowBalance'));
        $Sender->AddDefinition('KBOperationsOptions',json_encode($this->OperationsOptions));
        
        $Sender->AddJsFile('options.js','plugins/KarmaBank');
        
        $Sender->Render('Settings', '', 'plugins/KarmaBank');
    }
    /*
    *   Users are given a starting balance
    */
    public function StartingBalance($UserID=null){
        if(!$UserID){
            $UserID = Gdn::Session()->UserID;
        }else{
            $User = Gdn::UserModel()->GetID($UserID);

            if(!$User)
                return;
        }

        $KarmaBank = new KarmaBankModel($UserID);
        $Balance = $KarmaBank->GetBalance();
        if(!empty($Balance) || @$GLOBALS['php_errormsg'])
            return;
        $StartingBalance = C('Plugins.KarmaBank.StartingBalance');
        if(!$KarmaBank->CheckForCollissions('Starting Balance',floatval($StartingBalance))){
            $KarmaBank->Transaction('Starting Balance',floatval($StartingBalance));
        }
    }



    /*
    *   Adds a tab to user profiles linking Karma Bank
    *   Shows current Balance as count
    */
    public function ProfileController_AddProfileTabs_Handler($Sender){
        if(!$this->IsEnabled())
            return;
        $KarmaBank = new KarmaBankModel($Sender->User->UserID);
        $Balance = $KarmaBank->GetBalance();
        $Sender->AddProfileTab('KarmaBank','profile/karmabank/'.$Sender->User->UserID.'/'.rawurlencode($Sender->User->Name),
                        'KarmaBank',T('KarmaBank.KarmaBank','Karma Bank').(isset($Balance->Balance) && is_numeric($Balance->Balance) ? '<span class="Count">'.sprintf(T('KarmaBank.NumberFormat',"%01.2f"),$Balance->Balance).'</span>':''));
    }

    /*
    *   Shows the user balance/transactions
    *   Pageable history
    *   Tax/reward system
    */
    public function ProfileController_KarmaBank_Create($Sender,$Args){
        if(!$this->IsEnabled())
            return;
        $Sender->Permission('Garden.Profiles.View');
        if(!ctype_digit($Args[0]))
            throw NotFoundException('User');
        
        $Sender->ThisUser=Gdn::UserModel()->GetID($Args[0]);
        list($Offset, $Limit) = OffsetLimit(array_key_exists(2,$Args)?$Args[2]:0,C('Plugins.KarmaBank.PageLimit',5));
        $Sender->Offset=$Offset;
        $KarmaBank = new KarmaBankModel($Sender->ThisUser->UserID);
        $Balance = $KarmaBank->GetBalance();
        if($Sender->Form->IsPostBack() != False && (Gdn::Session()->CheckPermission('Plugins.KarmaBank.RewardTax') || Gdn::Session()->User->Admin)){
            $FormValues = $Sender->Form->FormValues();
            $Validation = new Gdn_Validation();
            $Validation->ApplyRule('RewardTax', 'Decimal','Reward/Tax invalid amount');
            $Validation->ApplyRule('RewardTaxReason', 'Required','Reward/Tax reason required');
            $Validation->Validate($FormValues);
            if (count($Validation->Results()) == 0) {
                $Value=GetValue('RewardTax',$FormValues);
                $Reason=GetValue('RewardTaxReason',$FormValues);
                $Validation->ApplyRule('RewardTaxReason', 'Required','Reward/Tax reason required');
                $Type=($Value==abs($Value))?Gdn::Session()->User->Name.' Rewards':Gdn::Session()->User->Name.' Taxes';
                $Type.=' ['.urlencode($Reason).']';
                $KarmaBank->Transaction($Type,$Value,$Value);
                Redirect($Sender->Request->RequestURI());
            }
            $Sender->Form->SetValidationResults($Validation->Results());
        }else if($Sender->Form->IsPostBack() != False){
            throw PermissionException();
        }
        $PagerFactory = new Gdn_PagerFactory();
        $Sender->Pager = $PagerFactory->GetPager('Pager', $Sender);
        $Sender->Pager->MoreCode = 'Older';
        $Sender->Pager->LessCode = 'Newer';
        $Sender->Pager->ClientID = 'Pager';
        $Sender->Pager->Configure(
        $Sender->Offset,
        $Limit,
        $Balance->TransCount,
         'profile/karmabank/'.$Sender->ThisUser->UserID.'/'.rawurlencode($Sender->ThisUser->Name).'/{Page}'
        );
        $Sender->SetData('Transactions',$KarmaBank->GetTransactionList($Limit,$Sender->Offset));
        $Sender->SetData('Balance',$Balance?$Balance->Balance:0);
        $Sender->AddCssFile('karma.css','plugins/KarmaBank/');
        $Sender->GetUserInfo($Sender->ThisUser->UserID,$Sender->ThisUser->Name);
        $Sender->SetTabView('KarmaBank', dirname(__FILE__).DS.'views'.DS.'karmabank.php', 'Profile', 'Dashboard');
        $Sender->Render();
    }
    
    /*
    *   Show balance with comment user meta
    */
    
    public function DiscussionController_BeforeDiscussionRender_Handler($Sender) {
        if(!$this->IsEnabled() || !C('Plugins.KarmaBank.CommentShowBalance'))
            return;
        $this->CacheBalances($Sender);
    }
   
    //there is an issue of controller getting mixed up
    //despite being fired from the post controller
    public function Base_BeforeCommentRender_Handler($Sender) {
        if(!$this->IsEnabled() || !C('Plugins.KarmaBank.CommentShowBalance'))
            return;
        $this->CacheBalances($Sender);
    }
    
    protected function CacheBalances($Sender) {
        $Discussion = $Sender->Data('Discussion');
        $Comments = ($Sender->Data('Comments') ? $Sender->Data('Comments') : $Sender->Data('CommentData',0));
        $UserIDList = array();

        if ($Discussion)
         $UserIDList[$Discussion->InsertUserID] = 1;
         
        if ($Comments && $Comments->NumRows()) {
         $Comments->DataSeek(-1);
         while ($Comment = $Comments->NextRow())
            $UserIDList[$Comment->InsertUserID] = 1;
        }

        $UserBalances = array();
        if (sizeof($UserIDList)) {
            $KarmaBankModel = new KarmaBankModel(0);
            $Balances = $KarmaBankModel->GetBalances(array_keys($UserIDList));
            foreach($Balances As $UserBalance)
                $UserBalances[$UserBalance->UserID] = $UserBalance->Balance;
            
        }
        $Sender->SetData('KarmaBalances', $UserBalances);
        
    }
    
    public function DiscussionController_CommentInfo_Handler($Sender) {
        if(!$this->IsEnabled() || !C('Plugins.KarmaBank.CommentShowBalance'))
            return;
        $this->ShowBalance($Sender);

    }
  
    public function DiscussionController_DiscussionInfo_Handler($Sender) {
        if(!$this->IsEnabled() || !C('Plugins.KarmaBank.CommentShowBalance'))
            return;
        $this->ShowBalance($Sender);

    }

    //not actually used in 2.1 due to controller mixup
    //uses DiscussionController_DiscussionInfo_Handler instead
    public function PostController_CommentInfo_Handler($Sender) {
        if(!$this->IsEnabled() || !C('Plugins.KarmaBank.CommentShowBalance'))
            return;
        $this->ShowBalance($Sender);
    }

    protected function ShowBalance($Sender) {
        $Balance = ArrayValue($Sender->EventArguments['Author']->UserID, $Sender->Data['KarmaBalances']);
        echo '<span>'.sprintf(T('KarmaBank.NumberFormatKarma',"%01.2f Karma"),$Balance).'</span>';
    }

    /*
    *   CheckKarma is where the magic happens
    *   Sniffing out user meta, applying those rules
    */

    public function CheckKarma($UserID=NULL){
        if(self::$KarmaChecked)
            return;
        if(!$UserID){
            $User = Gdn::Session()->User;
        }else{
            $User = Gdn::UserModel()->GetID($UserID);
            if(!$User)
                return;
        }
        $UserID=$User->UserID;
        
        $MetaConditions=array();
        $MagicMetaConditions=array();
        foreach(array_keys($this->Meta) As $Condition){
            if(substr($Condition, -1)!='%'){
 
                $MetaConditions[]=$Condition;
            }else{
                $MagicMetaConditions[]=$Condition;
            }
        }
        
    
        
        $UMSQL=Gdn::SQL()
            ->Select('um.*')
            ->From('UserMeta um')
            ->Where('um.UserID',$UserID)
            ->BeginWhereGroup()
            ->WhereIn('um.Name',$MetaConditions);
            
        if(!empty($MagicMetaConditions))
            foreach($MagicMetaConditions As $MagicMeta)
                $UMSQL->OrLike('um.Name',$MagicMeta,'pre');
        $UMSQL->EndWhereGroup();
        
            
        $UserMeta= $UMSQL->Get()->Result();
        $MoreMeta=array();

        if(GetValue($UserID,$this->MoreMeta))
            $MoreMeta=array_merge($MoreMeta,$this->MoreMeta[$UserID]);
        
        foreach($UserMeta As $ExtraMeta){
            $MoreMeta[$ExtraMeta->Name]=$ExtraMeta->Value;
        }
            
        $User->Meta=$MoreMeta;
        
        
        
        $KarmaRules = new KarmaRulesModel();
        $Rules = $KarmaRules->GetRules();
        
        
        
        if(!empty($MagicMetaConditions)){//propagate magic rules.
            
            $RulesData = new Gdn_DataSet($Rules);
            $RulesTemp = $RulesData->ResultArray();
            $Rules=$RulesTemp;
            $RuleStep=0;
            
            foreach($RulesTemp As $RuleI => $Rule){
                if(substr($Rule['Condition'], -1)=='%'){
                    $RulesInject=array();
                    foreach(array_keys($MoreMeta) As $MoreIndex){
                        
                        if(stripos($MoreIndex,substr($Rule['Condition'], 0,-1))===0){
                            $DontTally = in_array($Rule['Condition'],$this->DontTally);
                            $this->DontTally[]=$MoreIndex;
                            $RuleTemp=$Rule;
                            $RuleTemp['Condition'] = $MoreIndex;
                            $RulesInject[]=$RuleTemp;
                            
                        }
                    }
                    if(count($RulesInject)){
                        array_splice($Rules,$RuleI+$RuleStep,1,$RulesInject);//add rule per condition
                        $RuleStep+=count($RulesInject)-1;
                    }
                    
                }
            }
        }
        
        $TallySet = $KarmaRules->GetTally($UserID);
        $KarmaBank = new KarmaBankModel($UserID);
        $TransByRules = $KarmaBank->GetLastTransactionTally();
      
        foreach($Rules As $Rule){
            
            if(is_array($Rule))
                $Rule = (object) $Rule;
                
            
            $Condition= $Rule->Condition;
            $Value=null;
            
           
           
            if(property_exists($User,$Condition)){
                $Value = $User->$Condition;
            }else if(GetValue($Condition,$MoreMeta)){
                $Value = GetValue($Condition,$MoreMeta);
            }

            if($Value==null)
                continue;
                
           
            
            $Tally = null;

            foreach($TallySet As $TallyRow){
                if($TallyRow->RuleID==$Rule->RuleID)
                    $Tally = $TallyRow;
            }
            
            $TallyValue =!in_array($Condition,$this->DontTally) && $Tally && $Tally->Value? $Tally->Value : 0;

            $RuleID=$Rule->RuleID;
            $Target=$Rule->Target;
            $Option = GetValue('Option',$Rule);
            $Type = $Condition.($Option?' '.$Option:'').' '.$Rule->Operation.' '.$Target;
            $Transaction=FALSE;
            if($TallyValue!=$Value){
                if(empty($TransByRules)){
                    $LastTrans=null;
                }else{
                    foreach($TransByRules As $TransByRule) 
                        if($TransByRule->RuleID=$RuleID)
                            $LastTrans=$TransByRule; 
                }
                
                $Transaction = call_user_func($this->OperationsMap[$Rule->Operation],$Value,$Target,$Condition,$User,$LastTrans,$Option);
                
                
                if($Transaction && !is_bool($Transaction) && is_numeric($Transaction)) //transaction factor
                    $Rule->Amount*=$Transaction; 
                
                
                
                if(!$KarmaBank->CheckForCollissions($Type,$Rule->Amount,$Value)){//try to prevent collisions (uses file cache psuedo-lock)
                    if($Transaction){
                        $KarmaBank->Transaction($Type,$Rule->Amount,$Value,$RuleID);
                    }
                    if(!in_array($Condition,$this->DontTally))
                        $KarmaRules->SetTally($UserID,$RuleID,$Value);
                }
            }
        }
        $this->EventArguments=array('User'=>$User); 
        $this->FireEvent('AfterKarmaCheck');
        self::$KarmaChecked=TRUE;
    }

    /*
    *   This is used as a per request cron
    *   A top level pseudo-event check, and meta update for plugins
    *   and where the Karma is checked
    */

    public function Base_BeforeControllerMethod_Handler($Sender) {
        if(!$this->IsEnabled())
            return;
        if(!Gdn::Session()->isValid()) return;
        /* QnA Accepted /  Acceptance Counts */

        if(C('EnabledPlugins.QnA')
            && strtolower($Sender->Controller())=='discussion'
            && strtolower($Sender->ControllerMethod())=='qna'){

            $Comment = Gdn::SQL()->GetWhere('Comment', array('CommentID' => GetValue('commentid',$_GET)))->FirstRow(DATASET_TYPE_ARRAY);
            if (!$Comment)
                throw NotFoundException('Comment');

            $Discussion = Gdn::SQL()->GetWhere('Discussion', array('DiscussionID' => $Comment['DiscussionID']))->FirstRow(DATASET_TYPE_ARRAY);

              // Check for permission (let QnA handle exceptions)
            if ((Gdn::Session()->UserID == GetValue('InsertUserID', $Discussion) /*|| Gdn::Session()->CheckPermission('Garden.Moderation.Manage')*/)){
                if (Gdn::Session()->ValidateTransientKey(GetValue('tkey',$_GET))){

                    $Args=$Sender->ControllerArguments();
                    if($Args[0]=='accept'){

                        if ($Discussion['QnA'] != 'Accepted' && (!$Discussion['QnA'] || in_array($Discussion['QnA'], array('Unanswered', 'Answered', 'Rejected')))){
                          $User = Gdn::UserModel()->GetID($Discussion['InsertUserID']);
                          Gdn::SQL()->Update('User',array('QnACountAccept'=>$User->QnACountAccept+1))->Where(array('UserID'=>$User->UserID))->Put();
                          if(Gdn::Session()->UserID==$User->UserID)
                            Gdn::Session()->User->QnACountAccept+=1;
                        }

                        //You don't get points for accepting your own comments
                        if($Discussion['InsertUserID']!=$Comment['InsertUserID']){
                            if($Comment['QnA'] != 'Accepted'){
                                $User = Gdn::UserModel()->GetID($Comment['InsertUserID']);
                                Gdn::SQL()->Update('User',array('QnACountAcceptance'=>$User->QnACountAcceptance+1))->Where(array('UserID'=>$User->UserID))->Put();
                                if(Gdn::Session()->UserID==$User->UserID)
                                    Gdn::Session()->User->QnACountAcceptance+=1;
                            }
                        }
                    }
                }
            }
        }
        
        /* QnA Accepted /  Acceptance Counts */

        /* check/update Starting Balance */
        if(strtolower($Sender->Controller())=='profile' /*&& ctype_digit($Sender->ControllerMethod())*/){
            $Args=$Sender->ControllerArguments();
            if(ctype_digit(GetValue('0',$Args))){
                $this->StartingBalance($Args[0]);
            }
        }else{
            $this->StartingBalance();
        }
        /* check/update Starting Balance */
        $this->CheckKarma();
    }

    public function Setup() {
        $this->Structure();
    }
    /*
    *   Earlier per request cron, hot version update of structure, and map meta spec
    */
    public function Base_BeforeDispatch_Handler($Sender){
        if(C('Plugins.KarmaBank.Version')!=$this->PluginInfo['Version'])
            $this->Structure();
        /* load meta details */
        $this->MetaMap();
    }
    
    public function Structure() {
        
        Gdn::Structure()
            ->Table('KarmaBankBalance')
            ->Column('UserID', 'int(11)',FALSE, 'primary')
            ->Column('Balance','decimal(20,2)')
            ->Column('TransCount','int(11)',0)
            ->Set();

        Gdn::Structure()
            ->Table('KarmaBankTrans')
            ->PrimaryKey('TransID')
            ->Column('UserID', 'int(11)',FALSE,'index')
            ->Column('Type','varchar(500)')
            ->Column('Date','datetime')
            ->Column('Amount','decimal(20,2)')
            ->Set();
         
        Gdn::Structure()
            ->Table('KarmaBankTransTally')
            ->Column('UserID', 'int(11)',FALSE,array('primary','index'))
            ->Column('RuleID','int(11)',FALSE,'primary')
            ->Column('LastTally','decimal(20,2)')
            ->Set();

        if(stripos(Gdn::Controller()->ResolvedPath,'utility/structure')===FALSE){
            Gdn::Structure()
                ->Table('KarmaRules')
                ->Column('RuleID','int(11)',FALSE,'key')
                ->Column('Condition','varchar(100)')
                ->Column('Operation','varchar(100)')
                ->Column('Option','varchar(100)',null)
                ->Column('Target','decimal(20,2)')
                ->Column('Amount','decimal(20,2)')
                ->Column('Remove','int(4)',0)
                ->Set();
                
                $Schema = Gdn::SQL()->FetchTableSchema('KarmaRules');
                if(!$Schema['RuleID']->PrimaryKey || !$Schema['RuleID']->AutoIncrement){
                    Gdn::Structure()
                        ->Table('KarmaRules')
                        ->PrimaryKey('RuleID')
                        ->Set();
                }

            Gdn::Structure()
                ->Table('KarmaRulesTally')
                ->Column('TallyID','int(11)',FALSE,'key')
                ->Column('RuleID','int(11)')
                ->Column('UserID', 'int(11)')
                ->Column('Value','decimal(20,2)')
                ->Set();
                
            $Schema = Gdn::SQL()->FetchTableSchema('KarmaRulesTally');
            if(!$Schema['TallyID']->PrimaryKey || !$Schema['TallyID']->AutoIncrement){
                Gdn::Structure()
                    ->Table('KarmaRulesTally')
                    ->PrimaryKey('TallyID')
                    ->Set();
            }
        }
        
        Gdn::Structure()
            ->Table('User')
            ->Column('QnACountAccept','int(11)',0)
            ->Column('QnACountAcceptance','int(11)',0)
            ->Set();

        //Save Version for hot update

        SaveToConfig('Plugins.KarmaBank.Version', $this->PluginInfo['Version']);
   }
}
