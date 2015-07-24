<?php if (!defined('APPLICATION')) exit();

class KarmaRulesModel extends VanillaModel{
    
    static $Rules = NULL;
    
    public function __construct() {
        parent::__construct('KarmaRules');
    }

    public function GetRules(){
        if(self::$Rules){
            return self::$Rules;
        }
        
        self::$Rules =  $this->SQL
        ->Select('kr.*')
        ->From('KarmaRules kr')
        ->Where('kr.Remove <>',1)
        ->Get()
        ->Result();
        
        return self::$Rules;
    }
 
    public function GetTally($UserID,$RuleID=null){
        return $this->SQL
        ->Select('krt.*')
        ->From('KarmaRulesTally krt')
        ->Where(
            ($RuleID) ?
            array(
                'krt.UserID'=>$UserID,
                'krt.RuleID'=>$RuleID
            )
            :
            array(
                'krt.UserID'=>$UserID,
            )
        )
        ->Get()
        ->Result();
    }
 
    public function SetTally($UserID,$RuleID,$Value){
        if($this->GetTally($UserID,$RuleID)){
            $this->SQL
            ->Update('KarmaRulesTally',
                array(
                    'Value'=>$Value,
                )
            )
            ->Where(
                array(
                    'UserID'=>$UserID,
                    'RuleID'=>$RuleID,
                )
            )
            ->Put();
        }else{
            $this->SQL
            ->Insert('KarmaRulesTally',
                array(
                    'UserID'=>$UserID,
                    'RuleID'=>$RuleID,
                    'Value'=>$Value,
                )
            );
        }
    }
 
    public function SetRule($Condition,$Operation,$Target,$Amount){
        self::$Rules=NULL;
        $this->SQL
        ->Insert('KarmaRules',
            array(
                'Condition'=>$Condition,
                'Operation'=>$Operation,
                'Target'=>$Target,
                'Amount'=>$Amount
            )
        );
        
    }
 
    public function RemoveRule($RuleID){
        self::$Rules=NULL;
        $this->SQL
        ->Update('KarmaRules',
            array(
                'Remove'=>1
            )
        )
        ->Where(
            array(
                'RuleID'=>$RuleID
            )
        )
        ->Put();
    }

}
